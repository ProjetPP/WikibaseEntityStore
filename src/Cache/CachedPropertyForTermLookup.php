<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\PropertyForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyForTermLookup implements PropertyForTermLookup {

	/**
	 * @var PropertyForTermLookup
	 */
	private $propertyForTermLookup;

	/**
	 * @var EntityDocumentForTermCache
	 */
	private $entityForTermCache;

	/**
	 * @param PropertyForTermLookup $propertyForTermLookup
	 * @param EntityDocumentForTermCache $entityForTermCache
	 */
	public function __construct(
		PropertyForTermLookup $propertyForTermLookup,
		EntityDocumentForTermCache $entityForTermCache
	) {
		$this->propertyForTermLookup = $propertyForTermLookup;
		$this->entityForTermCache = $entityForTermCache;
	}

	/**
	 * @see PropertyForTermLookup::getPropertysForTerm
	 */
	public function getPropertiesForTerm( Term $term ) {
		try {
			return $this->entityForTermCache->fetch( $term, 'property' );
		} catch( OutOfBoundsException $e ) {
			$properties = $this->propertyForTermLookup->getPropertiesForTerm( $term );
			$this->entityForTermCache->save( $term, 'property', $properties );
			return $properties;
		}
	}
}
