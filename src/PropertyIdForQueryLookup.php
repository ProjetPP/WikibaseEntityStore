<?php

namespace Wikibase\EntityStore;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface PropertyIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Description $queryDescription
	 * @param QueryOptions|null $queryOptions
	 * @return PropertyId[]
	 */
	public function getPropertyIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null );
}
