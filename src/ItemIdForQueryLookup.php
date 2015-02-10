<?php

namespace Wikibase\EntityStore;

use Ask\Language\Query;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface ItemIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Query $query
	 * @return ItemId[]
	 */
	public function getItemIdsForQuery( Query $query );
}
