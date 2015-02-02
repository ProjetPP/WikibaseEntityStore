<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityDocumentForTermLookup {

	/**
	 * Provides entities of which the label or an alias is the given term.
	 * Does a case insensitive comparison.
	 *
	 * @param Term $term
	 * @param string|null $entityType
	 * @return EntityDocument[]
	 */
	public function getEntityDocumentsForTerm( Term $term, $entityType = null );
}
