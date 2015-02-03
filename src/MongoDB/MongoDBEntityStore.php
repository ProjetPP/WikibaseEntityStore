<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Collection;
use Wikibase\EntityStore\EntityDocumentSaver;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityForTermLookup;
use Wikibase\EntityStore\Internal\EntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityStore extends EntityStore {

	/**
	 * @var EntityLookup
	 */
	private $entityLookup;

	/**
	 * @var EntityForTermLookup
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
		$entityCollection = $this->newEntityCollection( $collection );
		$this->entityLookup = new EntityLookup( $entityCollection );
		$this->entityForTermLookup = new EntityForTermLookup( $this->newEntityForTermLookup( $collection ) );
		$this->entitySaver = $entityCollection;
	}

	private function newEntityCollection( Collection $collection ) {
		return new MongoDBEntityCollection( $collection, $this->newDocumentBuilder() );
	}

	private function newEntityForTermLookup( Collection $collection ) {
		return new MongoDBEntityForTermLookup( $collection, $this->newDocumentBuilder() );
	}

	private function newDocumentBuilder() {
		$serialization = new EntitySerializationFactory();
		return new MongoDBDocumentBuilder( $serialization->newEntitySerializer(), $serialization->newEntityDeserializer() );
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
	 * @see EntityStore::getItemForTermLookup
	 */
	public function getItemForTermLookup() {
		return $this->entityForTermLookup;
	}

	/**
	 * @see EntityStore::getPropertyForTermLookup
	 */
	public function getPropertyForTermLookup() {
		return $this->entityForTermLookup;
	}
}
