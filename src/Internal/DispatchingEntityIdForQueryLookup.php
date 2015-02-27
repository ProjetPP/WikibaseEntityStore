<?php

namespace Wikibase\EntityStore\Internal;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
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
	public function getEntityIdsForQuery(
		Description $queryDescription,
		QueryOptions $queryOptions = null,
		$entityType = null
	) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $queryDescription, $queryOptions, $entityType );
	}

	/**
	 * @see ItemIdsForQueryLookup::getItemForQuery
	 */
	public function getItemIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null ) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $queryDescription, $queryOptions, Item::ENTITY_TYPE );
	}

	/**
	 * @see PropertyIdsForQueryLookup::getPropertyForQuery
	 */
	public function getPropertyIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null ) {
		return $this->entityIdForQueryLookup->getEntityIdsForQuery( $queryDescription, $queryOptions, Property::ENTITY_TYPE );
	}
}
