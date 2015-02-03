<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface PropertyIdForTermLookup {

	/**
	 * Provides ids of properties of which the label or an alias is the given term.
	 * Does a case insensitive comparison.
	 *
	 * @param Term $term
	 * @return PropertyId[]
	 */
	public function getPropertyIdsForTerm( Term $term );
}
