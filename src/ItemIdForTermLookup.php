<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface ItemIdForTermLookup {

	/**
	 * Provides ids of items of which the label or an alias is the given term.
	 * Does a case insensitive comparison.
	 *
	 * @param Term $term
	 * @return ItemId[]
	 */
	public function getItemIdsForTerm( Term $term );
}
