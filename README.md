# WikibaseEntityStore

[![Build Status](https://travis-ci.org/ProjetPP/WikibaseEntityStore.svg?branch=master)](https://travis-ci.org/ProjetPP/WikibaseEntityStore)
[![Code Coverage](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/WikibaseEntityStore/?branch=master)
[![Dependency Status](https://www.versioneye.com/php/ppp:wikibase-entity-store/dev-master/badge.svg)](https://www.versioneye.com/php/ppp:wikibase-entity-store/dev-master)

On [Packagist](https://packagist.org/packages/ppp/wikibase-entity-store):
[![Latest Stable Version](https://poser.pugx.org/ppp/wikibase-entity-store/version.png)](https://packagist.org/packages/ppp/wikibase-entity-store)
[![Download count](https://poser.pugx.org/ppp/wikibase-entity-store/d/total.png)](https://packagist.org/packages/ppp/wikibase-entity-store)


WikibaseEntityStore is a small library that provides an unified interface to interact with Wikibase entities.

It currently has two backends:

- An API based backend, slow but very easy to deploy
- A MongoDB based backend, faster but requires to maintain a local copy of Wikibase content

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

//Do a query on items using the Ask query language: retrieves the first 10 items with P1: Q1
$itemIds = $store->getItemIdForQueryLookup()->getItemIdsForQuery(
    new Query(
        new SomeProperty(
	        new EntityIdValue( new PropertyId( 'P1' ) ),
			new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
		),
		array(),
		new QueryOptions( 10, 0 )
	)
);
```

### Backends

#### API Backend
The API backend is the most easy to use one. It uses the API of a Wikibase instance and WikidataQuery if you use this EntityStore
as a backend for Wikidata data and you want query support.

The configuration file looks like:

```json
{
    "backend": "api",
    "api": {
        "url": "http://www.wikidata.org/w/api.php",
        "wikidataquery-url": "http://wdq.wmflabs.org/api"
    }
}
```

Replace `http://www.wikidata.org/w/api.php` with the URL of your WediaWiki API if you want to use your store with an other Wikibase instance than Wikidata.

The parameter `wikidataquery-url` is optional and may be unset if you don't want query support using Wikidata content.

Without configuration file:

```php
$store = new \Wikibase\EntityStore\Api\ApiEntityStore(
    new \Mediawiki\Api\MediawikiApi( 'http://www.wikidata.org/w/api.php' ),
    new \WikidataQueryApi\WikidataQueryApi( 'http://wdq.wmflabs.org/api' )
);
 ```

#### MongoDB Backend
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

//Gets the database where entities are stored
$database = $connection
    ->selectDatabase( 'wikibase' );

$store = new \Wikibase\EntityStore\MongoDB\MongDBEntityStore( $database );
```

You can fill the MongoDB database from Wikidata JSON dumps using this script:

     php entitystore import-json-dump MY_JSON_DUMP MY_CONFIGURATION_FILE

Or from incremental XML dumps using this script:

	php entitystore import-incremental-xml-dump MY_XML_DUMP MY_CONFIGURATION_FILE

#### InMemory backend
Backend based on an array of EntityDocuments. Useful for tests.

```php
$store = new \Wikibase\EntityStore\InMemory\InMemoryEntityStore( array(
    new Item( new ItemId( 'Q42' ) )
) );
```

### Options

The different backends support a shared set of options. These options are:

- *string[]* `languages`: Allows to filter the set of internationalized values. Default value: `null` (a.k.a. all languages).
- *bool* `languagefallback`: Apply language fallback system to languages defined using languages option. Default value: `false`.

They can be injected in the configuration:

```json
{
  "options": {
    "languages": ["en", "fr"],
    "languagefallback": true
  }
}
```

They can be also passed as last parameter of `EntityStore` constructors:

```php
$options = new \Wikibase\EntityStore\EntityStoreOptions( array(
	EntityStore::OPTION_LANGUAGES => array( 'en', 'fr' ),
	EntityStore::OPTION_LANGUAGE_FALLBACK => true
) );

$store = new \Wikibase\EntityStore\Api\ApiEntityStore(
    new \Mediawiki\Api\MediawikiApi( 'http://www.wikidata.org/w/api.php' ),
    null,
    $options
);
```

### Cache support

It is possible, in order to get far better performances, to add a cache layer on top of EntityStore:

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

$cache = new \Doctrine\Common\Cache\ArrayCache(); //A very simple cache
$cacheLifeTime = 100000; //Life time of cache in seconds

$cachedStore = new \Wikibase\EntityStore\Cache\CachedEntityStore( $store, $cache, $cacheLifeTime );
```
