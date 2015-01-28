<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Collection;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityStore extends EntityStore {

	/**
	 * @var MongoDBEntityCollection
	 */
	private $entityCollection;

	/**
	 * @param Collection $collection
	 */
	public function __construct( Collection $collection ) {
		$this->entityCollection = $this->newEntityCollection( $collection );
	}

	private function newEntityCollection( Collection $collection ) {
		return new MongoDBEntityCollection( $collection, $this->newDocumentBuilder() );
	}

	private function newDocumentBuilder() {
		$serialization = new EntitySerializationFactory();
		return new MongoDBDocumentBuilder( $serialization->newEntitySerializer(), $serialization->newEntityDeserializer() );
	}

	/**
	 * @see EntityStore::getEntityDocumentLookup
	 */
	public function getEntityDocumentLookup() {
		return $this->entityCollection;
	}

	/**
	 * @see EntityStore::getItemLookup
	 */
	public function getItemLookup() {
		return new EntityLookup( $this->entityCollection );
	}

	/**
	 * @see EntityStore::getPropertyLookup
	 */
	public function getPropertyLookup() {
		return new EntityLookup( $this->entityCollection );
	}

	/**
	 * @see EntityStore::getEntityDocumentSaver
	 */
	public function getEntityDocumentSaver() {
		return $this->entityCollection;
	}
}
