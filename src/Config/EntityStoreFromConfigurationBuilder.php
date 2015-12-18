<?php

namespace Wikibase\EntityStore\Config;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ChainCache;
use InvalidArgumentException;
use Mediawiki\Api\MediawikiApi;
use RuntimeException;
use Symfony\Component\Config\Definition\Processor;
use Wikibase\EntityStore\Api\ApiEntityStore;
use Wikibase\EntityStore\Cache\CachedEntityStore;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\MongoDB\MongoDBEntityStore;
use WikidataQueryApi\WikidataQueryApi;

class EntityStoreFromConfigurationBuilder {

	/**
	 * @param string $configurationFileName
	 * @return EntityStore
	 */
	public function buildEntityStore( $configurationFileName ) {
		$config = $this->parseConfiguration( $configurationFileName );

		$store = $this->buildEntityStoreFromConfig( $config );

		if( array_key_exists( 'cache', $config ) ) {
			$cache = $this->buildCacheFromConfig( $config['cache'] );

			if( $cache !== null ) {
				return new CachedEntityStore( $store, $cache, $config['cache']['lifetime'] );
			}
		}

		return $store;
	}

	/**
	 * @param string $configurationFileName
	 * @return Cache
	 */
	public function buildCache( $configurationFileName ) {
		$config = $this->parseConfiguration( $configurationFileName );

		if( !array_key_exists( 'cache', $config ) ) {
			throw new InvalidArgumentException( 'No cache key in configuration' );
		}
		return $this->buildCacheFromConfig( $config['cache'] );
	}

	private function buildEntityStoreFromConfig( $config ) {
		$options = new EntityStoreOptions( $config['options'] );

		switch( $config['backend'] ) {
			case 'api':
				return new ApiEntityStore(
					$this->getWikibaseApi( $config['api'] ),
					$this->getWikidataQueryApi( $config['api'] ),
					$options
				);
			case 'mongodb':
				return new MongoDBEntityStore( $this->getMongoDbDatabase( $config['mongodb'] ), $options );
			default:
				throw new InvalidArgumentException( 'Unknown backend: ' . $config['backend'] );
		}
	}

	private function getWikibaseApi( $config ) {
		return MediawikiApi::newFromApiEndpoint( $config['url'] );
	}

	private function getWikidataQueryApi( $config ) {
		if( !array_key_exists( 'wikidataquery_url', $config ) ) {
			return null;
		}

		return new WikidataQueryApi( $config['wikidataquery_url'] );
	}

	private function getMongoDbDatabase( $config ) {
		$connection = new \Doctrine\MongoDB\Connection( $config['server'] );
		if( !$connection->connect() ) {
			throw new RuntimeException( 'Fail to connect to MongoDb' );
		}

		return $connection
			->selectDatabase( $config['database'] );
	}

	private function buildCacheFromConfig( $config ) {
		$caches = [];

		if( $config['array']['enabled'] ) {
			$caches[] = new ArrayCache();
		}

		if( $config['memcached']['enabled'] ) {
			$memcached = new \Memcached();

			if( !$memcached->addServer( $config['memcached']['host'], $config['memcached']['port'] ) ) {
				throw new RuntimeException( 'Fail to connect to Memcached' );
			}

			$memcachedCache = new \Doctrine\Common\Cache\MemcachedCache();
			$memcachedCache->setMemcached($memcached);
			$caches[] = $memcachedCache;
		}

		switch( count( $caches ) ) {
			case 0:
				return null;
			case 1:
				return reset( $caches );
			default:
				return new ChainCache( $caches );
		}
	}

	private function parseConfiguration( $configurationFileName ) {
		$configValues = json_decode( file_get_contents( $configurationFileName ), true );

		$processor = new Processor();
		$configuration = new EntityStoreConfiguration();
		return $processor->processConfiguration(
			$configuration,
			[ $configValues ]
		);
	}
}
