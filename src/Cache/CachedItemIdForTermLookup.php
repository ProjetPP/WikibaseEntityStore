<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\ItemIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemIdForTermLookup implements ItemIdForTermLookup {

	/**
	 * @var ItemIdForTermLookup
	 */
	private $itemIdForTermLookup;

	/**
	 * @var EntityIdForTermCache
	 */
	private $entityIdForTermCache;

	/**
	 * @param ItemIdForTermLookup $itemIdForTermLookup
	 * @param EntityIdForTermCache $entityIdForTermCache
	 */
	public function __construct(
		ItemIdForTermLookup $itemIdForTermLookup,
		EntityIdForTermCache $entityIdForTermCache
	) {
		$this->itemIdForTermLookup = $itemIdForTermLookup;
		$this->entityIdForTermCache = $entityIdForTermCache;
	}

	/**
	 * @see ItemIdForTermLookup::getItemIdsForTerm
	 */
	public function getItemIdsForTerm( Term $term ) {
		try {
			return $this->entityIdForTermCache->fetch( $term, 'item' );
		} catch( OutOfBoundsException $e ) {
			$itemIds = $this->itemIdForTermLookup->getItemIdsForTerm( $term );
			$this->entityIdForTermCache->save( $term, 'item', $itemIds );
			return $itemIds;
		}
	}
}
