<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedEntityDocumentLookup implements EntityDocumentLookup {

	/**
	 * @var EntityDocumentLookup
	 */
	private $entityLookup;

	/**
	 * @var EntityDocumentCache
	 */
	private $entityCache;

	/**
	 * @param EntityDocumentLookup $entityLookup
	 * @param EntityDocumentCache $entityCache
	 */
	public function __construct( EntityDocumentLookup $entityLookup, EntityDocumentCache $entityCache ) {
		$this->entityLookup = $entityLookup;
		$this->entityCache = $entityCache;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		try {
			return $this->entityCache->fetch( $entityId );
		} catch( EntityNotFoundException $e ) {
			$entity = $this->entityLookup->getEntityDocumentForId( $entityId );
			$this->entityCache->save( $entity );
			return $entity;
		}
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$entities = [];
		$entityIdsToRetrieve = [];

		foreach( $entityIds as $entityId ) {
			try {
				$entities[] = $this->entityCache->fetch( $entityId );
			} catch( EntityNotFoundException $e ) {
				$entityIdsToRetrieve[] = $entityId;
			}
		}

		$additionalEntities = [];
		if( !empty( $entityIdsToRetrieve ) ) {
			$additionalEntities = $this->entityLookup->getEntityDocumentsForIds( $entityIdsToRetrieve );
		}
		foreach( $additionalEntities as $entity ) {
			$this->entityCache->save( $entity );
		}

		return array_merge( $entities, $additionalEntities );
	}
}
