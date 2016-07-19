INTRODUCTION
============

MongoDB integration for Drupal, version 8.x-2.0-dev. - Experimental branch for
entity/field storage. _Only use for development purposes: it does *not* work_.

[![Build Status](https://travis-ci.org/FGM/mongodb.svg?branch=storage)](https://travis-ci.org/FGM/mongodb) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/FGM/mongodb/badges/quality-score.png?b=storage)](https://scrutinizer-ci.com/g/FGM/mongodb/?branch=storage)

This package is a collection of several modules, allowing Drupal sites to store
various data in MongoDB. Its __only__ currently working sub-modules are the ones
in this table: the other ones are only meant for contributors to work on.

Module                | Information
----------------------|---------------------------------------------------------
mongodb               | Drupal/Drush wrapper around mongodb-php-library.
mongodb_watchdog      | Store logger (watchdog) messages in MongoDB.

As its name implies, this release is currently alpha-level only. Use at your own risk.

Documentation is available on [Github pages]

[Github pages]: https://fgm.github.io/mongodb/


INSTALLATION
============

The MongoDB module and sub-modules need some amount of configuration before they
will properly work. This guide assumes that :

* a [MongoDB][download] 3.0 or later instance is already installed, configured, and available to connect to from the Drupal instance.
* the site will be running [Drupal][drupal] 8.1.x or 8.2.x.
* the [mongodb][mongodb] (not [mongo][mongo]) PHP extension version 1.1.7 or later is installed and configured.

[download]: https://www.mongodb.org/downloads
[drupal]: https://www.drupal.org/project/drupal
[php]: http://php.net/downloads.php
[mongo]: http://php.net/mongo
[mongodb]: http://php.net/mongodb

If MongoDB is installed on localhost, you may view the web admin interface:

    http://localhost:28017/

* Download the module package, as per [Installing contributed modules (Drupal 8)][install]
* Copy the relevant section from the `example.settings.local.php` to your
`settings.local.php` file if you use one, or `settings.php` otherwise, and
adapt it to match your MongoDB settings.
* At the root of your site, add a composer requiment:

        composer require mongodb/mongodb "^1.0.0"

* Enable the `mongodb` module. You now have access to the MongoDB services and Drush commands.

[install]: https://www.drupal.org/documentation/install/modules-themes/modules-8


CONFIGURATION
=============
mongodb_watchdog
----------------

The module uses a separate database, using the `logger` alias in MongoDB
settings. Do NOT point that alias to the same database as `default`, because the
module drops the logger database when uninstalling, which would drop all your
other data with it.

* `mongodb.watchdog.items`: the maximum item limit on the capped collection used
  by the module. If not defined, it defaults to 10000. The actual (size-based)
  limit is derived from this variable, assuming 1 kiB per watchdog entry.
* `mongodb.watchdog.limit`: the maximum severity level (0 to 7, per RFC 5424) to save
  into watchdog. Errors below this level (with a higher numerical level) will be
  ignored by the module. If not defined, all events will saved.
* `mongodb.watchdog.items_per_page`: the maximum number of events displayed on
  the event details page.
* `mongodb_watchdog.request_tracking`: if true, enable the per-request event
   tracking. If not defined, it defaults to false because its cost is not
   entirely negligible.
* `mongodb_watchdog.requests`: if request tracking is enabled, this setting
  defined the maximum requests limit on the capped collection used by the
  module. If not defined, it defaults to 100000. The actual (size-based) limit
  is derived from this variable, assuming 1 kiB per tracker entry.

See [Drupal\Core\Logger\RfcLogLevel][levels] and [Psr\Log\LogLevel][levelnames]
for further information about severity levels.

[levels]: https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Logger%21RfcLogLevel.php/class/RfcLogLevel/8.2.x
[levelnames]: https://api.drupal.org/api/drupal/vendor%21psr%21log%21Psr%21Log%21LogLevel.php/class/LogLevel/8.2.x


DATABASES / COLLECTIONS
=======================

Module                | Database alias | Collection(s)      | Information
----------------------|----------------|--------------------|-------------------------------
`mongodb`             | `default`      | (none)             | Checks alias/client consistency
`mongodb_watchdog`    | `logger`       | `watchdog`         | Event types
&uarr;                | &uarr;         | `watchdog_*`       | Capped collections for events
&uarr;                | &uarr;         | `watchdog_tracker` | Capped collection for requests

Earlier versions used to support a collection aliasing mechanism. With this
version generalizing dedicated databases per module, this is no longer needed
and the associated machinery has been removed.


DEVELOPMENT
===========

To write your own custom modules on top of MongoDB while avoiding compatibility
issues with this suite, you probably want to write on top of the `mongodb`
module, using the `mongodb.client_factory` and/or `mongodb.database_factory`
services instead of accessing the PHP library or MongoDB extension directly.


CONTRIBUTING
============

Starting with 8.x-2.0-alpha1, use the drupal.org issue queue for issue
discussion, but send pull requests on Github rather than drupal.org patches.


LEGAL
=====

Like any Drupal module, this package is licensed under the [General Public
License, version 2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) or later.

* Drupal is a registered trademark of Dries Buytaert.
* Github is a registered trademark of GiHub, Inc.
* Mongo, MongoDB and the MongoDB  leaf logo are registered trademarks of MongoDB, Inc.
