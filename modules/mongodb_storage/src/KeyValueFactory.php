<?php

namespace Drupal\mongodb_storage;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\mongodb\DatabaseFactory;

/**
 * Class KeyValueFactory builds KeyValue stores as MongoDB collections.
 */
class KeyValueFactory implements KeyValueFactoryInterface {
  const DATABASE_ALIAS = 'keyvalue';
  const COLLECTION_PREFIX = 'kvp_';

  /**
   * The database in which the stores are created.
   *
   * @var \MongoDB\Database
   */
  protected $database;

  /**
   * A static cache for the stores.
   *
   * @var array<string,\Drupal\mongodb_storage\KeyValueStore>
   */
  protected $stores = [];

  /**
   * KeyValueFactory constructor.
   *
   * @param \Drupal\mongodb\DatabaseFactory $databaseFactory
   *   The mongodb.database_factory service.
   */
  public function __construct(DatabaseFactory $databaseFactory) {
    $this->database = $databaseFactory->get(static::DATABASE_ALIAS);
  }

  /**
   * Constructs a new key/value store for a given collection name.
   *
   * @param string $collection
   *   The name of the collection holding key and value pairs.
   *
   * @return \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   *   A key/value store implementation for the given $collection.
   */
  public function get($collection) {
    $store_collection = $this->database->selectCollection(static::COLLECTION_PREFIX . $collection);
    $store = new KeyValueStore($collection, $store_collection);
    return $store;
  }

}
