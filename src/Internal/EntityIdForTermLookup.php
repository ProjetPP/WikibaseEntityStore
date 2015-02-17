<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityIdForTermLookup {

	/**
	 * Provides ids of entities of which the label or an alias is the given term.
	 * Does a case insensitive comparison.
	 *
	 * @param Term $term
	 * @param string|null $entityType
	 * @return EntityId[]
	 */
	public function getEntityIdsForTerm( Term $term, $entityType );
}
