<?php

namespace Wikibase\EntityStore\Config;

use InvalidArgumentException;
use Mediawiki\Api\MediawikiApi;
use RuntimeException;
use Symfony\Component\Config\Definition\Processor;
use Wikibase\EntityStore\Api\ApiEntityStore;
use Wikibase\EntityStore\MongoDB\MongoDBEntityStore;

class EntityStoreFromConfigurationBuilder {

	/**
	 * @var string
	 */
	private $configurationFileName;

	/**
	 * @param string $configurationFileName
	 */
	public function __construct( $configurationFileName ) {
		$this->configurationFileName = $configurationFileName;
	}

	public function buildEntityStore() {
		$config = $this->parseConfiguration();

		switch( $config['backend'] ) {
			case 'api':
				return new ApiEntityStore( new MediawikiApi( $config['api']['url'] ) );
			case 'mongodb':
				return new MongoDBEntityStore( $this->getMongoDbCollection( $config['mongodb'] ) );
			default:
				throw new InvalidArgumentException( 'Unknown backend: ' . $config['backend'] );
		}
	}

	private function getMongoDbCollection( $config ) {
		$connection = new \Doctrine\MongoDB\Connection( $config['server'] );
		if( !$connection->connect() ) {
			throw new RuntimeException( 'Fail to connect to the database' );
		}

		return $connection
			->selectDatabase( $config['database'] )
			->selectCollection( 'entity' );
	}

	private function parseConfiguration() {
		$configValues = json_decode( file_get_contents( $this->configurationFileName ), true );

		$processor = new Processor();
		$configuration = new EntityStoreConfiguration();
		return $processor->processConfiguration(
			$configuration,
			array( $configValues )
		);
	}
}
