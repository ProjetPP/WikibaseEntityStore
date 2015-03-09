<?php

namespace Wikibase\EntityStore\Cache;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use OutOfBoundsException;
use Wikibase\EntityStore\ItemIdForQueryLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemIdForQueryLookup implements ItemIdForQueryLookup {

	/**
	 * @var ItemIdForQueryLookup
	 */
	private $itemIdForQueryLookup;

	/**
	 * @var EntityIdForQueryCache
	 */
	private $entityIdForQueryCache;

	/**
	 * @param ItemIdForQueryLookup $itemIdForQueryLookup
	 * @param EntityIdForQueryCache $entityIdForQueryCache
	 */
	public function __construct(
		ItemIdForQueryLookup $itemIdForQueryLookup,
		EntityIdForQueryCache $entityIdForQueryCache
	) {
		$this->itemIdForQueryLookup = $itemIdForQueryLookup;
		$this->entityIdForQueryCache = $entityIdForQueryCache;
	}

	/**
	 * @see ItemIdForQueryLookup::getItemIdsForQuery
	 */
	public function getItemIdsForQuery( Description $description, QueryOptions $queryOptions = null ) {
		try {
			return $this->entityIdForQueryCache->fetch( $description, $queryOptions, 'item' );
		} catch( OutOfBoundsException $e ) {
			$itemIds = $this->itemIdForQueryLookup->getItemIdsForQuery( $description, $queryOptions );
			$this->entityIdForQueryCache->save( $description, $queryOptions, 'item', $itemIds );
			return $itemIds;
		}
	}
}
