<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\PropertyIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyIdForTermLookup implements PropertyIdForTermLookup {

	/**
	 * @var PropertyIdForTermLookup
	 */
	private $propertyIdForTermLookup;

	/**
	 * @var EntityIdForTermCache
	 */
	private $entityIdForTermCache;

	/**
	 * @param PropertyIdForTermLookup $propertyIdForTermLookup
	 * @param EntityIdForTermCache $entityIdForTermCache
	 */
	public function __construct(
		PropertyIdForTermLookup $propertyIdForTermLookup,
		EntityIdForTermCache $entityIdForTermCache
	) {
		$this->propertyIdForTermLookup = $propertyIdForTermLookup;
		$this->entityIdForTermCache = $entityIdForTermCache;
	}

	/**
	 * @see PropertyIdForTermLookup::getPropertysIdForTerm
	 */
	public function getPropertyIdsForTerm( Term $term ) {
		try {
			return $this->entityIdForTermCache->fetch( $term, 'property' );
		} catch( OutOfBoundsException $e ) {
			$propertyIds = $this->propertyIdForTermLookup->getPropertyIdsForTerm( $term );
			$this->entityIdForTermCache->save( $term, 'property', $propertyIds );
			return $propertyIds;
		}
	}
}
