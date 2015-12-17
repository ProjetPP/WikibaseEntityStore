<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityLookup implements EntityDocumentLookup {

	/**
	 * @var EntityDocument[]
	 */
	private $entities = [];

	/**
	 * @param EntityDocument[] $entities
	 */
	public function __construct( array $entities ) {
		$this->buildEntitiesArray( $entities );
	}

	private function buildEntitiesArray( array $entities ) {
		/** @var EntityDocument $entity */
		foreach( $entities as $entity ) {
			$this->entities[$entity->getId()->getSerialization()] = $entity;
		}
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		$key = $entityId->getSerialization();
		return array_key_exists( $key, $this->entities ) ? $this->entities[$key] : null;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$entities = [];

		foreach( $entityIds as $entityId ) {
			$entity = $this->getEntityDocumentForId( $entityId );
			if( $entity !== null ) {
				$entities[] = $entity;
			}
		}

		return $entities;
	}
}
