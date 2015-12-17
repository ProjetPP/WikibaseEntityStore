<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\EntityLookup;
use Wikibase\DataModel\Services\Lookup\EntityLookupException;
use Wikibase\DataModel\Services\Lookup\ItemLookup;
use Wikibase\DataModel\Services\Lookup\ItemLookupException;
use Wikibase\DataModel\Services\Lookup\PropertyLookup;
use Wikibase\DataModel\Services\Lookup\PropertyLookupException;
use Wikibase\EntityStore\EntityDocumentLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityLookup implements ItemLookup, PropertyLookup, EntityLookup, EntityDocumentLookup {

	/**
	 * @var EntityDocumentLookup
	 */
	private $entityDocumentLookup;

	/**
	 * @param EntityDocumentLookup $entityDocumentLookup
	 */
	public function __construct( EntityDocumentLookup $entityDocumentLookup ) {
		$this->entityDocumentLookup = $entityDocumentLookup;
	}

	/**
	 * @see EntityLookup:getEntity
	 */
	public function getEntity( EntityId $entityId ) {
		return $this->entityDocumentLookup->getEntityDocumentForId( $entityId );
	}

	/**
	 * @see EntityLookup:hasEntity
	 */
	public function hasEntity( EntityId $entityId ) {
		return $this->entityDocumentLookup->getEntityDocumentForId( $entityId ) !== null;
	}

	/**
	 * @see EntityDocumentLookup:getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		return $this->entityDocumentLookup->getEntityDocumentForId( $entityId );
	}

	/**
	 * @see EntityDocumentLookup:getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		return $this->entityDocumentLookup->getEntityDocumentsForIds( $entityIds );
	}

	/**
	 * @see ItemLookup::getItemForId
	 */
	public function getItemForId( ItemId $itemId ) {
		try {
			return $this->entityDocumentLookup->getEntityDocumentForId( $itemId );
		} catch(EntityLookupException $e) {
			throw new ItemLookupException( $e->getEntityId(), $e->getMessage(), $e );
		}
	}

	/**
	 * @see PropertyLookup::getPropertyForId
	 */
	public function getPropertyForId( PropertyId $propertyId ) {
		try {
			return $this->entityDocumentLookup->getEntityDocumentForId( $propertyId );
		} catch(EntityLookupException $e) {
			throw new PropertyLookupException( $e->getEntityId(), $e->getMessage(), $e );
		}
	}
}
