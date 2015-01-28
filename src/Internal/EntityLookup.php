<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\ItemLookup;
use Wikibase\DataModel\Entity\ItemNotFoundException;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Entity\PropertyLookup;
use Wikibase\DataModel\Entity\PropertyNotFoundException;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityLookup implements ItemLookup, PropertyLookup, EntityDocumentLookup {

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
		} catch(EntityNotFoundException $e) {
			throw new ItemNotFoundException( $e->getEntityId() );
		}
	}

	/**
	 * @see PropertyLookup::getPropertyForId
	 */
	public function getPropertyForId( PropertyId $propertyId ) {
		try {
			return $this->entityDocumentLookup->getEntityDocumentForId( $propertyId );
		} catch(EntityNotFoundException $e) {
			throw new PropertyNotFoundException( $e->getEntityId() );
		}
	}
}
