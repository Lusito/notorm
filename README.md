# NotORM - simple reading data from the database

Dual licensed (one or the other)

[![License](https://img.shields.io/badge/License-Apache%20V2-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0) [![License](https://img.shields.io/badge/License-GPL%20V2-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)

This is a fork of http://www.notorm.com/ (original author Jakub Vrana, copyright 2010)

NotORM is a PHP library for simple working with data in the database. The most interesting feature is, that it's very easy work with table relationships. The overall performance is also very important and NotORM can actually run faster than a native driver.

### About this fork

- The original code has not been maintained in years.
- I needed a PSR-4 version of it
- I refactored the code a bit
- I ported the code to PHP 7.1
- I added a couple of extra features
- Usage has been left mainly untouched

### Requirements
- PHP >= 7.1
- any database supported by PDO (tested with MySQL, SQLite, PostgreSQL, MS SQL, Oracle)

### Installation

Install via composer:

`composer require lusito/notorm`

Include the autoloader in your php script, unless you've done that already:

```php
require __DIR__ . '/vendor/autoload.php';
``` 

### Usage

Setup is a bit different to the original NotORM. At first you need to create a Config object:

```php
use Lusito\NotORM\ConfigBuilder;

$dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;
$config = (new ConfigBuilder($dsn, $username, $password))->build();
```

ConfigBuilder allows for various settings to be made. We'll come to that later.

After that, you can use it in the classic way or the new way I'm proposing.

The classic way: 
```php
use Lusito\NotORM\Database;

$db = new Database($config);

foreach ($db->application()->order("title") as $application) { // get all applications ordered by title
    echo "$application[title]\n"; // print application title
    echo $application->author["name"] . "\n"; // print name of the application author
    foreach ($application->application_tag() as $application_tag) { // get all tags of $application
        echo $application_tag->tag["name"] . "\n"; // print the tag name
    }
}
```

The new way uses a static class:
```php
use Lusito\NotORM\DB;

DB::setConfig($config);

foreach (DB::application()->order("title") as $application) { // get all applications ordered by title
    // ...
}
```

This new approach helps you to avoid passing the database instance to every class where you need it (or even setting it globally).

Use `DB::hasConfig()` to check if the DB has been set up.

### Differences of the new approach

|Action|Classic|New|
|---|---|---|
|Get table|`$db->table_name(...$where)`|`DB::table_name(...$where)`|
|Get table (by name)|`$db->__call('table_name', ...$where)`|`DB::getTable('table_name', ...$where)`|
|Get row by id|`$db->table_name[$id]`|`DB::getRow('table_name', $id)`|
|Set a config value (for example debug, debugTimer, freeze, rowClass or jsonAsArray)|`$db->debug = true;`|`DB::setConfigValue('debug', true);`|
|Begin a transaction|`$db->transaction = 'BEGIN';`|`DB::beginTransaction()`|
|Commit a transaction|`$db->transaction = 'COMMIT';`|`DB::commitTransaction()`|
|Rollback a transaction|`$db->transaction = 'ROLLBACK';`|`DB::rollbackTransaction()`|


### License

The code is dual licensed (one or the other)
- [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
- [GNU General Public License, Version 2](http://www.gnu.org/licenses/gpl-2.0.html)
