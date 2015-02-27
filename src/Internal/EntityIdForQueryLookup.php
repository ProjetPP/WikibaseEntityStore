<?php

namespace Wikibase\EntityStore\Internal;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Description $queryDescription
	 * @param QueryOptions|null $queryOptions
	 * @param string $entityType
	 * @return EntityId[]
	 */
	public function getEntityIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null, $entityType );
}
