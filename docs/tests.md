The newly ported modules have some test coverage, which can be checked with
PHPUnit.

# PHPUnit
## Running tests directly

The typical full command to run tests looks like this ('\' is to avoid too long a line):

    MONGODB_URI=mongodb://somemongohost:27017 \
    SIMPLETEST_DB=mysql://someuser:somepassword@localhost/somedatabase \
    phpunit -c core/phpunit.xml.dist

* Optional: `MONGODB_URI` points to a working MongoDB instance. This variable is optional:
  if it is not provided, the tests will default to `mongodb://localhost:27017`.
* Required: `SIMPLETEST_DB` is the standard Drupal 8 variable needed to run tests that
  can need the database service.

Both variables can be set in the `core/phpunit.xml` custom configuration file.


## Using a `phpunit.xml` configuration file

The test command can also be simplified using a `phpunit.xml` configuration file:

    phpunit -c core/phpunit.xml

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
