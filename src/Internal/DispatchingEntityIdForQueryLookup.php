<?php

namespace Wikibase\EntityStore\Internal;

use Ask\Language\Query;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\EntityStore\ItemIdForQueryLookup;
use Wikibase\EntityStore\PropertyIdForQueryLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityIdForQueryLookup implements ItemIdForQueryLookup, PropertyIdForQueryLookup, EntityIdForQueryLookup {

	/**
	 * @var EntityIdForQueryLookup
	 */
	private $entityIdForQueryLookup;

	/**
	 * @param EntityIdForQueryLookup $entityIdForQueryLookup
	 */
	public function __construct( EntityIdForQueryLookup $entityIdForQueryLookup ) {
		$this->entityIdForQueryLookup = $entityIdForQueryLookup;
	}

	/**
	 * @see EntityIdsForQueryLookup:getEntityDocumentsForQuery
	 */
	public function getEntityIdsForQuery( Query $term, $entityType = null ) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $term, $entityType );
	}

	/**
	 * @see ItemIdsForQueryLookup::getItemForQuery
	 */
	public function getItemIdsForQuery( Query $term ) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $term, Item::ENTITY_TYPE );
	}

	/**
	 * @see PropertyIdsForQueryLookup::getPropertyForQuery
	 */
	public function getPropertyIdsForQuery( Query $term ) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $term, Property::ENTITY_TYPE );
	}
}
