<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\EntityIdForTermLookup;
use Wikibase\EntityStore\ItemIdForTermLookup;
use Wikibase\EntityStore\PropertyIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityIdForTermLookup implements ItemIdForTermLookup, PropertyIdForTermLookup, EntityIdForTermLookup {

	/**
	 * @var EntityIdForTermLookup
	 */
	private $entityIdForTermLookup;

	/**
	 * @param EntityIdForTermLookup $entityIdForTermLookup
	 */
	public function __construct( EntityIdForTermLookup $entityIdForTermLookup ) {
		$this->entityIdForTermLookup = $entityIdForTermLookup;
	}

	/**
	 * @see EntityDocumentForTermLookup:getEntityDocumentsForTerm
	 */
	public function getEntityIdsForTerm( Term $term, $entityType = null ) {
		return $this->entityIdForTermLookup->getEntityIdsForTerm( $term, $entityType );
	}

	/**
	 * @see ItemForTermLookup::getItemForTerm
	 */
	public function getItemIdsForTerm( Term $term ) {
		return $this->entityIdForTermLookup->getEntityIdsForTerm( $term, 'item' );
	}

	/**
	 * @see PropertyForTermLookup::getPropertyForTerm
	 */
	public function getPropertyIdsForTerm( Term $term ) {
		return $this->entityIdForTermLookup->getEntityIdsForTerm( $term, 'property' );
	}
}
