# WikibaseEntityStore

[![Build Status](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/54d0e3fa3ca08473b400010f/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54d0e3fa3ca08473b400010f)

On [Packagist](https://packagist.org/packages/ppp/wikibase-entity-store):
[![Latest Stable Version](https://poser.pugx.org/ppp/wikibase-entity-store/version.png)](https://packagist.org/packages/ppp/wikibase-entity-store)
[![Download count](https://poser.pugx.org/ppp/wikibase-entity-store/d/total.png)](https://packagist.org/packages/ppp/wikibase-entity-store)


WikibaseEntityStore is a small library that provides different ways to store Wikibase entities.

## Installation

Use one of the below methods:

1 - Use composer to install the library and all its dependencies using the master branch:

    composer require "ppp/wikibase-entity-store":dev-master"

2 - Create a composer.json file that just defines a dependency on version 1.0 of this package, and run 'composer install' in the directory:

```json
{
    "require": {
        "ppp/wikibase-entity-store": "~1.0"
    }
}
```


## Usage

### Features

The entity storage system is based on the abstract class EntityStore that provides different services to manipulate entities.

These services are:

```php
$storeBuilder = new EntityStoreFromConfigurationBuilder();
$store = $storeBuilder->buildEntityStore( 'MY_CONFIG_FILE.json' ); //See backend section for examples of configuration file

//Retrieves the item Q1
try {
    $item = $store->getItemLookup()->getItemForId( new ItemId( 'Q1' ) );
} catch( ItemNotFoundException $e ) {
    //Item not found
}

//Retrieves the property P1
try {
    $item = $store->getPropertyLookup()->getPropertyForId( new PropertyId( 'P1' ) );
} catch( PropertyNotFoundException $e ) {
    //Property not found
}

//Retrieves the item Q1 as EntityDocument
try {
    $item = $store->getEntityLookup()->getEntityDocumentForId( new ItemId( 'Q1' ) );
} catch( EntityNotFoundException $e ) {
    //Property not found
}

//Retrieves the item Q1 and the property P1 as EntityDocuments
$entities = $store->getEntityLookup()->getEntityDocumentsForIds( array( new ItemId( 'Q1' ), new PropertyId( 'P1' ) ) );

//Retrieves the ids of the items that have as label or alias the term "Nyan Cat" in English (with a case insensitive compare)
$itemIds = $store->getItemIdForTermLookup()->getItemIdsForTerm( new Term( 'en', 'Nyan Cat' ) );

//Retrieves the ids of the properties that have as label or alias the term "foo" in French (with a case insensitive compare)
$propertyIds = $store->getPropertyIdForTermLookup()->getPropertyIdsForTerm( new Term( 'fr', 'Foo' ) );
```

### Backends

### API Backend
The API backend is the most easy to use one. It uses the API of a Wikibase instance.

The configuration file looks like:

```json
{
    "backend": "api",
    "api": {
        "url": "http://www.wikidata.org/w/api.php"
    }
}
```

Replace `http://www.wikidata.org/w/api.php` with the URL of your WediaWiki API if you want to use your store with an other Wikibase instance than Wikidata.

Without configuration file:

```php
$store = new Wikibase\EntityStore\Api\ApiEntityStore(
    new \Mediawiki\Api\MediawikiApi('http://www.wikidata.org/w/api.php' )
);
 ```


### MongoDB Backend
The MongoDB backend uses a MongoDB database. Requires [doctrine/mongodb](https://packagist.org/packages/doctrine/mongodb).

The configuration file looks like:

```json
{
    "backend": "mongodb",
    "mongodb": {
        "server": SERVER,
        "database": DATABASE
    }
}
```

`server` should be a [MongoDB server connection string](http://docs.mongodb.org/manual/reference/connection-string/) and `database` the name of the database to use.

Without configuration file:

```php
//Connect to MongoDB
$connection = new \Doctrine\MongoDB\Connection( MY_CONNECTION_STRING );
if( !$connection->connect() ) {
    throw new RuntimeException( 'Fail to connect to the database' );
}

//Gets the collection where entities are stored
$collection = $connection
    ->selectDatabase( 'wikibase' )
    ->selectCollection( 'entity' );

$store = new Wikibase\EntityStore\MongoDB\MongDBEntityStore( $collection );
```

You can fill the MongoDB database from Wikidata JSON dumps using this script:

     php entitystore mongodb:import-json-dump MY_JSON_DUMP MY_CONFIGURATION_FILE

Options to configure on which database the script act are available. See

     php entitystore mongodb:import-json-dump --help

### InMemory backend
Backend based on an array of EntityDocuments. Useful for tests.

```php
$store = new Wikibase\EntityStore\InMemory\InMemoryEntityStore( array(
    new Item( new ItemId( 'Q42' ) )
) );
```

### Cache support

IIt is possible, in order to get far better performances, to add a cache layer on top of EntityStore:

Adds to the configuration file a `cache` section.

Example with a two layers cache. The first one is a PHP array and the second one a Memcached instance on `localhost:11211`.

```json
{
    "cache": {
        "array": true,
        "memcached": {
            "host": "localhost",
            "port": 11211
        }
    }
}
```

Without configuration file:

```php
$store = MY_ENTITY_STORE;

$cache = new ArrayCache(); //A very simple cache
$cacheLifeTime = 100000; //Life time of cache in seconds

$cachedStore = new \Wikibase\EntityStore\Cache\CachedEntityStore( $store, $cache, $cacheLifeTime );
```
