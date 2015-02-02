<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Entity\PropertyLookup;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyLookup implements PropertyLookup {

	/**
	 * @var PropertyLookup
	 */
	private $itemLookup;

	/**
	 * @var EntityDocumentCache
	 */
	private $entityCache;

	/**
	 * @param PropertyLookup $itemLookup
	 * @param EntityDocumentCache $entityCache
	 */
	public function __construct( PropertyLookup $itemLookup, EntityDocumentCache $entityCache ) {
		$this->itemLookup = $itemLookup;
		$this->entityCache = $entityCache;
	}

	/**
	 * @see PropertyLookup::getPropertyForId
	 */
	public function getPropertyForId( PropertyId $propertyId ) {
		try {
			return $this->entityCache->fetch( $propertyId );
		} catch( EntityNotFoundException $e ) {
			$property = $this->itemLookup->getPropertyForId( $propertyId );
			$this->entityCache->save( $property );
			return $property;
		}
	}
}
