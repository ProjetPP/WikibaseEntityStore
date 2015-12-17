<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;

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
		$entity = $this->entityCache->fetch( $entityId );

		if( $entity === null ) {
			$entity = $this->entityLookup->getEntityDocumentForId( $entityId );
			if( $entity !== null ) {
				$this->entityCache->save( $entity );
			}
		}

		return $entity;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$entities = [];
		$entityIdsToRetrieve = [];

		foreach( $entityIds as $entityId ) {
			$entity = $this->entityCache->fetch( $entityId );

			if( $entity === null ) {
				$entityIdsToRetrieve[] = $entityId;
			} else {
				$entities[] = $entity;
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
