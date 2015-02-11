<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Collection;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityDocumentSaver;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup;
use Wikibase\EntityStore\Internal\EntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 *
 * @todo add indexes if all languages are supported
 */
class MongoDBEntityStore extends EntityStore {

	/**
	 * @var Collection
	 */
	private $collection;

	/**
	 * @var EntityLookup
	 */
	private $entityLookup;

	/**
	 * @var DispatchingEntityIdForTermLookup
	 */
	private $entityForTermLookup;

	/**
	 * @var EntityDocumentSaver
	 */
	private $entitySaver;

	/**
	 * @param Collection $collection
	 * @param EntityStoreOptions $options
	 */
	public function __construct( Collection $collection, EntityStoreOptions $options = null ) {
		$this->collection = $collection;
		parent::__construct( $options );

		$entityCollection = $this->newEntityCollection( $collection );
		$this->entityLookup = new EntityLookup( $entityCollection );
		$this->entityForTermLookup = new DispatchingEntityIdForTermLookup( $this->newEntityForTermLookup( $collection ) );
		$this->entitySaver = $entityCollection;
	}

	private function newEntityCollection( Collection $collection ) {
		return new MongoDBEntityCollection( $collection, $this->newDocumentBuilder() );
	}

	private function newEntityForTermLookup( Collection $collection ) {
		return new MongoDBEntityIdForTermLookup( $collection, $this->newDocumentBuilder() );
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
	 * @see EntityStore::setupIndexes
	 */
	public function setupIndexes() {
		$this->setupTermIndexes();
	}

	private function setupTermIndexes() {
		$languagesOption = $this->getOption( EntityStore::OPTION_LANGUAGES );

		if( $languagesOption === null ) {
			return;
		}

		foreach( $languagesOption as $language ) {
			$key = 'sterms.' . $language;
			$this->collection->ensureIndex( array( $key => 1, 'type' => 1 ) );
		}
	}
}
