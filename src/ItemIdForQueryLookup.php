<?php

namespace Wikibase\EntityStore;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface ItemIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Description $queryDescription
	 * @param QueryOptions|null $queryOptions
	 * @return ItemId[]
	 */
	public function getItemIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null );
}
