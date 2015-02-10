<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Collection;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityDocumentSaver;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup;
use Wikibase\EntityStore\Internal\EntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
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
	 */
	public function __construct( Collection $collection ) {
		$this->collection = $collection;

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
			new BasicEntityIdParser()
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
	 * @see EntityStore::setupStore
	 */
	public function setupStore() {

		//Create query indexes
		$this->collection->ensureIndex( array( 'searchterms' => 1, 'type' => 1 ) );
	}
}
