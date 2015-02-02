<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\EntityDocumentForTermLookup;
use Wikibase\EntityStore\ItemForTermLookup;
use Wikibase\EntityStore\PropertyForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityForTermLookup implements ItemForTermLookup, PropertyForTermLookup, EntityDocumentForTermLookup {

	/**
	 * @var EntityDocumentForTermLookup
	 */
	private $entityDocumentForTermLookup;

	/**
	 * @param EntityDocumentForTermLookup $entityDocumentForTermLookup
	 */
	public function __construct( EntityDocumentForTermLookup $entityDocumentForTermLookup ) {
		$this->entityDocumentForTermLookup = $entityDocumentForTermLookup;
	}

	/**
	 * @see EntityDocumentForTermLookup:getEntityDocumentsForTerm
	 */
	public function getEntityDocumentsForTerm( Term $term, $entityType = null ) {
		return $this->entityDocumentForTermLookup->getEntityDocumentsForTerm( $term, $entityType );
	}

	/**
	 * @see ItemForTermLookup::getItemForTerm
	 */
	public function getItemsForTerm( Term $term ) {
		return $this->entityDocumentForTermLookup->getEntityDocumentsForTerm( $term, 'item' );
	}

	/**
	 * @see PropertyForTermLookup::getPropertyForTerm
	 */
	public function getPropertiesForTerm( Term $term ) {
		return $this->entityDocumentForTermLookup->getEntityDocumentsForTerm( $term, 'property' );
	}
}
