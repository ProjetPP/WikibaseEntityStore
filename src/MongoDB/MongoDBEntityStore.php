<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Database;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityDocumentSaver;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForQueryLookup;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup;
use Wikibase\EntityStore\Internal\DispatchingEntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 *
 * @todo add indexes if all languages are supported
 */
class MongoDBEntityStore extends EntityStore {

	/**
	 * Option to set the time limit for query operations in milliseconds.
	 */
	const OPTION_QUERY_TIME_LIMIT = 'mongodb-query-time-limit';

	/**
	 * @var Database
	 */
	private $database;

	/**
	 * @var DispatchingEntityLookup
	 */
	private $entityLookup;

	/**
	 * @var DispatchingEntityIdForTermLookup
	 */
	private $entityForTermLookup;

	/**
	 * @var DispatchingEntityIdForQueryLookup
	 */
	private $entityForQueryLookup;

	/**
	 * @var EntityDocumentSaver
	 */
	private $entitySaver;

	/**
	 * @param Database $database
	 * @param EntityStoreOptions $options
	 */
	public function __construct( Database $database, EntityStoreOptions $options = null ) {
		$this->database = $database;

		parent::__construct( $options );
		$this->defaultOption( self::OPTION_QUERY_TIME_LIMIT, null );

		$entityDatabase = $this->newEntityDatabase( $database );
		$this->entityLookup = new DispatchingEntityLookup( $entityDatabase );
		$this->entityForTermLookup = new DispatchingEntityIdForTermLookup( $this->newEntityIdForTermLookup( $database ) );
		$this->entityForQueryLookup = new DispatchingEntityIdForQueryLookup( $this->newEntityIdForQueryLookup( $database ) );
		$this->entitySaver = $entityDatabase;
	}

	private function newEntityDatabase( Database $database ) {
		return new MongoDBEntityDatabase( $database, $this->newDocumentBuilder() );
	}

	private function newEntityIdForTermLookup( Database $database ) {
		return new MongoDBEntityIdForTermLookup( $database, $this->newDocumentBuilder() );
	}

	private function newEntityIdForQueryLookup( Database $database ) {
		return new MongoDBEntityIdForQueryLookup(
			$database,
			$this->newDocumentBuilder(),
			$this->getOption( self::OPTION_QUERY_TIME_LIMIT )
		);
	}

	private function newDocumentBuilder() {
		$serialization = new EntitySerializationFactory();
		return new MongoDBDocumentBuilder(
			$serialization->newEntitySerializer(),
			$serialization->newEntityDeserializer(),
			new BasicEntityIdParser(),
			$this->getOptions()
		);
	}

	/**
	 * @see EntityStore::getEntityLookup
	 */
	public function getEntityLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getEntityDocumentLookup
	 */
	public function getEntityDocumentLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getItemLookup
	 */
	public function getItemLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getPropertyLookup
	 */
	public function getPropertyLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getEntityDocumentSaver
	 */
	public function getEntityDocumentSaver() {
		return $this->entitySaver;
	}

	/**
	 * @see EntityStore::getItemIdForTermLookup
	 */
	public function getItemIdForTermLookup() {
		return $this->entityForTermLookup;
	}

	/**
	 * @see EntityStore::getPropertyIdForTermLookup
	 */
	public function getPropertyIdForTermLookup() {
		return $this->entityForTermLookup;
	}

	/**
	 * @see EntityStore::getItemIdForQueryLookup
	 */
	public function getItemIdForQueryLookup() {
		return $this->entityForQueryLookup;
	}

	/**
	 * @see EntityStore::getPropertyIdForQueryLookup
	 */
	public function getPropertyIdForQueryLookup() {
		return $this->entityForQueryLookup;
	}

	/**
	 * @see EntityStore::setupStore
	 */
	public function setupStore() {
		foreach( MongoDBDocumentBuilder::$SUPPORTED_ENTITY_TYPES as $type ) {
			$this->database->command( [
				'collMod' => $type,
				'usePowerOf2Sizes' => true
			] );
		}
	}

	/**
	 * @see EntityStore::setupIndexes
	 */
	public function setupIndexes() {
		$this->setupTermIndexes();
		$this->setupClaimsIndexes();
	}

	private function setupTermIndexes() {
		$languagesOption = $this->getOption( EntityStore::OPTION_LANGUAGES );

		if( $languagesOption === null ) {
			return;
		}

		foreach( $languagesOption as $language ) {
			$key = 'sterms.' . $language;

			foreach( MongoDBDocumentBuilder::$SUPPORTED_ENTITY_TYPES as $type ) {
				$this->database->selectCollection( $type )->ensureIndex(
					[ $key => 1 ],
					[ 'sparse' => true, 'socketTimeoutMS' => -1 ]
				);
			}
		}
	}

	private function setupClaimsIndexes() {
		foreach( MongoDBDocumentBuilder::$SUPPORTED_ENTITY_TYPES as $entityType ) {
			$collection = $this->database->selectCollection( $entityType );

			foreach( MongoDBDocumentBuilder::$SUPPORTED_DATAVALUE_TYPES as $dataValueType ) {
				$key = 'sclaims.' . $dataValueType;
				$collection->ensureIndex(
					[ $key => 1 ],
					[ 'sparse' => true, 'socketTimeoutMS' => -1 ]
				);
			}
		}
	}
}
