<?php

namespace Drupal\mongodb\Tests;

use Drupal\Core\Site\Settings;
use Drupal\KernelTests\KernelTestBase;
use Drupal\mongodb\ClientFactory;
use Drupal\mongodb\DatabaseFactory;

/**
 * Class MongoDbTestBase provides basic setUp()/tearDown() for MongoDB.
 *
 * @group MongoDB
 */
abstract class MongoDbTestBase extends KernelTestBase {
  const DEFAULT_URI = 'mongodb://localhost:27017';
  const CLIENT_BAD_ALIAS = 'bad';
  const CLIENT_TEST_ALIAS = 'test';

  const DB_BAD_CLIENT_ALIAS = 'bad';
  const DB_INVALID_ALIAS = 'invalid';
  const DB_DEFAULT_ALIAS = 'default';
  const DB_UNSET_ALIAS = 'unset';

  public static $modules = ['mongodb'];

  /**
   * A test-specific instance of Settings.
   *
   * @var \Drupal\Core\Site\Settings
   */
  protected $settings;

  protected $uri;

  /**
   * Obtain the name of a per-test database.
   *
   * @param string $postfix
   *   The way for the caller to differentiate this database from others.
   *
   * @return string
   *   The name of the per-test database, like 'simpletest1234_foo'.
   */
  public function getTestDatabaseName($postfix) {
    return $this->getDatabasePrefix() . '_' . $postfix;
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    // $_ENV if it comes from phpunit.xml <env>
    // $_SERVER if it comes from the phpunit command line environment.
    $this->uri = $_ENV['MONGODB_URI'] ?? $_SERVER['MONGODB_URI'] ?? static::DEFAULT_URI;

    $this->settings = new Settings([
      'mongodb' => [
        'clients' => [
          static::CLIENT_BAD_ALIAS => [
            'uri' => 'mongodb://localhost:80',
            'uriOptions' => [],
            'driverOptions' => [],
          ],
          static::CLIENT_TEST_ALIAS => [
            'uri' => $this->uri,
            'uriOptions' => [],
            'driverOptions' => [],
          ],
        ],
        'databases' => [
          static::DB_DEFAULT_ALIAS => [static::CLIENT_TEST_ALIAS, $this->getDatabasePrefix()],
          static::DB_INVALID_ALIAS => [static::CLIENT_TEST_ALIAS, ''],
          static::DB_BAD_CLIENT_ALIAS => [static::CLIENT_BAD_ALIAS, $this->getDatabasePrefix()],
        ],
      ],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown() {
    $clientFactory = new ClientFactory($this->settings);
    $databaseFactory = new DatabaseFactory($clientFactory, $this->settings);
    $databaseFactory->get(static::DB_DEFAULT_ALIAS)
      ->drop();

    parent::tearDown();
  }
}
