# MongoDB suite for Drupal 8

The MongoDB&reg; suite for Drupal&reg; 8 is a set of modules enabling the
storage of various types of data on a Drupal site, in addition to, or as a
replacement for the standard SQL storage used by Drupal.

It comprises several Drupal modules, each implementing a specific functionality.
With the exception of the base "driver" `mongodb` module, upon which all others
depend because it provides the standardized connection service to Drupal, all
the modules are independent of each other except where indicated.

The `mongodb` module is not just the basis for this package, but also is
designed to ease the development of bespoke code for end-user projects,
providing Drupal-integrated Symfony services for Client and Database with a
familiar alias-based selection, like the SQL database drivers do.

## Modules
### Working

Module                  | In a word | Information
------------------------|-----------|-------------------------------------------
[mongodb]               | driver    | MongoDB Client and Database services, [tests] base
[mongodb_watchdog]      | logger    | PSR-3 compliant logger with a built-in UI

[mongodb]: /mongodb
[mongodb_watchdog]: /mongodb_watchdog
[tests]: /tests

### Not yet ported

These modules exist for earlier Drupal versions, but have not been ported to
Drupal 8 yet. Some of them might never be ported because they are no longer
relevant in Drupal 8

Module                  | Information
------------------------|-------------------------------------------------------
`mongodb_block`         | Store Drupal blocks in MongoDB
`mongodb_block_ui`      | Provide a UI to manager blocks stored in MongoDB. Depends on `mongodb_block`.
`mongodb_cache`         | Store cache information in MongoDB
`mongodb_field_storage` | Store entities and their fields in MongoDB
`mongodb_migrate`       | Migrate SQL entities/fields to MongoDB. Depends on `mongodb_field_storage`.
`mongodb_queue`         | Implement a MongoDB backend for Queue API
`mongodb_session`       | Store session information in MongoDB

### Planned

These modules have no direct equivalent in earlier versions, but their
development is being considered.

Module           | Information
-----------------|-------------------------------------------------------
`mongodb_debug`  | Provide low-level debug information. A D7 version exists on [mongodb_logger] but depends on the legacy `mongo` PHP extension. Futures versions will need the 1.4 version of the `mongodb` extension which implements the MongoDB APM specification.

[mongodb_logger]: https://github.com/FGM/mongodb_logger/

## Project layout

    mkdocs.yml    # The configuration file.
    docs/
        index.md  # The documentation homepage.
        ...       # Other markdown pages, images and other files.

## Legal information

* This suite of modules is licensed under the General Public License, version 2 or later (GPL-2.0+).
* MongoDB is a registered trademark of MongoDB Inc.
* Drupal is a registered trademark of Dries Buytaert.
