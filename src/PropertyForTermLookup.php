<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface PropertyForTermLookup {

	/**
	 * Provides properties of which the label or an alias is the given term.
	 * Does a case insensitive comparison.
	 *
	 * @param Term $term
	 * @return Property[]
	 */
	public function getPropertiesForTerm( Term $term );
}
