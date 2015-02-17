<?php

namespace Wikibase\EntityStore;

use Ask\Language\Query;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Query $query
	 * @param string|null $entityType
	 * @return EntityId[]
	 */
	public function getEntityIdsForQuery( Query $query, $entityType );
}
