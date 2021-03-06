<?php

function mongodb_migrate_drush_command() {
  $items['mongodb-migrate-prepare'] = array(
    'description' => 'Prepare for migrate. Resets existing migration. No data is lost.',
  );
  $items['mongodb-migrate'] = array(
    'description' => 'Migrates fields. Run mongodb-migrate-prepare first.',
    'options' => array(
      'timeout' => 'How many seconds the command should run. 0 for no timeout. Defaults to 900.',
      'count' => 'How many entities should the command process. 0 for all. Defaults to all.',
    ),
  );

  return $items;
}

/**
 * Drush callback; prepares for migration by updating {field_config}.
 */
function drush_mongodb_migrate_prepare() {
  $fields = db_query('SELECT field_name FROM {field_config} WHERE storage_type = :storage_type OR data LIKE :mongodb_migrate', array(':storage_type' => 'field_sql_storage', ':mongodb_migrate' => '%mongodb_migrate%'))->fetchCol();
  foreach ($fields as $key => $field_name) {
    $field = field_read_field($field_name);
    if (!db_table_exists(_field_sql_storage_tablename($field))) {
      unset($fields[$key]);
      continue;
    }
    // Updating storage is forbidden by field_update_field(). Bugger. Write our
    // own, simplified update.
    $field['settings']['mongodb_migrate'] = 1;
    $field['storage_type'] = 'mongodb_field_storage';
    $field['storage_module'] = 'mongodb_field_storage';
    $field['storage']['type'] = 'mongodb_field_storage';
    $field['storage']['module'] = 'mongodb_field_storage';
    $data = $field;
    unset($data['columns'], $data['field_name'], $data['type'], $data['locked'], $data['module'], $data['cardinality'], $data['active'], $data['deleted']);
    $field['data'] = $data;
    drupal_write_record('field_config', $field, array('id'));
  }
  $info = field_info_fields();
  $all_entity_types = array();
  foreach ($fields as $field_name) {
    $field = $info[$field_name];
    db_update(_field_sql_storage_tablename($field))
      ->fields(array('deleted' => 0))
      ->condition('deleted', 2)
      ->execute();
    foreach ($field['bundles'] as $entity_type => $bundles) {
      mongodb_collection("migrate.$entity_type")->drop();
      $all_entity_types[] = $entity_type;
    }
  }
  variable_set('mongodb_migrate_fields', array_flip($fields));
  variable_set('mongodb_migrate_types', array_unique($all_entity_types));
  field_cache_clear();
}

/**
 * Drush callback; migrate all field data into MongoDB.
 */
function drush_mongodb_migrate() {
  $timeout = drush_get_option('timeout', 900);
  $finish_time = FALSE;
  if ($timeout > 0) {
    $finish_time = time() + $timeout;
  }
  $limit = drush_get_option('count', 0);
  foreach (variable_get('mongodb_migrate_types', array()) as $entity_type) {
    $counter = 0;
    $collection = mongodb_collection("migrate.$entity_type");
    $entity_info = entity_get_info($entity_type);
    $id_field = $entity_info['entity keys']['id'];
    do {
      $max = iterator_to_array($collection
        ->find(array(), array('_id' => 1))
        ->sort(array('_id' => -1))
        ->limit(1));
      $max = $max ? key($max) : 0;
      $query = db_select($entity_info['base table'], 'e')
        ->fields('e', array($id_field))
        ->condition($id_field, $max, '>')
        ->orderBy($id_field, 'ASC')
        ->range(0, 1);
      $entity_id = $query->execute()->fetchField();
      if ($entity_id) {
        $collection->db->resetError();
        $collection->insert(
          array(
            '_id' => (int) $entity_id,
            'timestamp' => time(),
          ),
          mongodb_default_write_options()
        );
        $lastError = $collection->db->lastError();
        if (empty($lastError['err'])) {
          $entities = entity_load($entity_type, array($entity_id));
          $entity = reset($entities);
          entity_save($entity_type, $entity);
          drush_print("Migrating $entity_type $entity_id");
        }
        else {
          drush_print("Not migrating $entity_type $entity_id");
        }
      }
      if ($finish_time && time() > $finish_time) {
        return;
      }
    } while ($entity_id && (!$limit || ++$counter < $limit));
  }
}
