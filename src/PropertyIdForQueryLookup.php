<?php

namespace Wikibase\EntityStore;

use Ask\Language\Query;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface PropertyIdForQueryLookup {

	/**
	 * Execute a query and returns the matching entities
	 *
	 * @param Query $query
	 * @return PropertyId[]
	 */
	public function getPropertyIdsForQuery( Query $query );
}
