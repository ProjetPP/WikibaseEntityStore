<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\PropertyLookup;

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
	private $propertyLookup;

	/**
	 * @var EntityDocumentCache
	 */
	private $entityCache;

	/**
	 * @param PropertyLookup $propertyLookup
	 * @param EntityDocumentCache $entityCache
	 */
	public function __construct( PropertyLookup $propertyLookup, EntityDocumentCache $entityCache ) {
		$this->propertyLookup = $propertyLookup;
		$this->entityCache = $entityCache;
	}

	/**
	 * @see PropertyLookup::getPropertyForId
	 */
	public function getPropertyForId( PropertyId $propertyId ) {
		$property = $this->entityCache->fetch( $propertyId );

		if( $property === null ) {
			$property = $this->propertyLookup->getPropertyForId( $propertyId );
			if( $property !== null ) {
				$this->entityCache->save( $property );
			}
		}

		return $property;
	}
}
