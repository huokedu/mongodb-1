# Driver: `mongodb`

The `mongodb` module is the main module in the suite, providing a thin Drupal
adapter for the standard MongoDB PHP library, in the form of two factory
services and a base test class.

## Factories

The basic idea of these factory services is to provide a way to create
instances of the standard `Client` and `Database` classes from a simple alias
string, taking all properties initialized during these creations from the
Drupal standard `Settings` object.

This allows code to be unaware of the execution environment, referring to
database or client by a functional alias, while the specifics of accessing
the relevant `mongod`/`mongos` and database are left to per-environment
settings,

* `\Drupal\mongodb\ClientFactory`:
  * `__construct()`: takes a single `Settings` parameter. This is normally
     invoked by the DIC, to which the class is exposed as
     `mongodb.client_factory`.
  * `get(string $alias): MongoDb\Client`; returns a `Client` instance matching
    the value defined in Drupal settings for `$alias`.
