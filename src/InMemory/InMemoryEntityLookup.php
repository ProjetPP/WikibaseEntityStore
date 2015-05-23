<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityNotFoundException;

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

		if( !array_key_exists( $key, $this->entities ) ) {
			throw new EntityNotFoundException( $entityId );
		}

		return $this->entities[$key];
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$entities = [];

		foreach( $entityIds as $entityId ) {
			try {
				$entities[] = $this->getEntityDocumentForId( $entityId );
			} catch( EntityNotFoundException $e ) {
			}
		}

		return $entities;
	}
}
