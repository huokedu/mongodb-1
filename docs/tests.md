The newly ported modules have some test coverage, which can be checked with
PHPUnit.

# PHPUnit
## Writing custom tests

The `mongodb` module provides a `Drupal\mongodb\Tests\MongoDbTestBase` base
class on which to build custom kernel tests for [bespoke] modules, as
it provides a per-test database created during test <code>setUp()</code>, and
dropped during <code>tearDown()</code>.

[bespoke]: /bespoke


## Running tests

With the Simpletest UI apparently going away in Drupal 8.2 (cf [#2566767],
[#2750461]), tests should be run from the command line.

[#2566767]: https://www.drupal.org/node/2566767
[#2750461]: https://www.drupal.org/node/2750461

### Running directly

The typical full command to run tests looks like this (`\` is to avoid too long a line):

    MONGODB_URI=mongodb://somemongohost:27017 \
    SIMPLETEST_DB=mysql://someuser:somepassword@localhost/somedatabase \
    phpunit -c core/phpunit.xml.dist

* Optional: `MONGODB_URI` points to a working MongoDB instance. This variable is optional:
  if it is not provided, the tests will default to `mongodb://localhost:27017`.
* Required: `SIMPLETEST_DB` is the standard Drupal 8 variable needed to run tests that
  can need the database service.

Both variables can be set in the `core/phpunit.xml` custom configuration file.


### Using a `phpunit.xml` configuration file

The test command can also be simplified using a `phpunit.xml` configuration file:

    phpunit -c core/phpunit.xml

Or to generate a coverage report:

    phpunit -c core/phpunit.xml --coverage-html=/some/coverage/path modules/contrib/mongodb

* `core/phpunit.xml` is a local copy of the default `core/phpunit.xml.dist`
  configuration file, tweaked to only test the minimum set of files needed by
  the test suite.
* It can look like this, to obtain a coverage report not including the whole
  Drupal tree, but just the MongoDB package itself:

        <?xml version="1.0" encoding="UTF-8"?>

        <phpunit ...snip...>
          <php>
            <env name="SIMPLETEST_DB" value="mysql://someuser:somepass@somesqlhost/somedb"/>
            <env name="MONGODB_URI" value="mongodb://somemongohost:27017" />
          </php>
          <testsuites ...snip...>...snip...</testsuites>
          <listeners>...snip...</listener>
          </listeners>
          <filter>
            <whitelist>
              <directory>../modules/contrib/mongodb</directory>
              <exclude>
                <directory>../modules/contrib/mongodb/modules/mongodb/src/Tests</directory>
                <directory suffix="Test.php">./</directory>
                <directory suffix="TestBase.php">./</directory>
              </exclude>
             </whitelist>
          </filter>
        </phpunit>
