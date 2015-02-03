<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\ItemForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemForTermLookup implements ItemForTermLookup {

	/**
	 * @var ItemForTermLookup
	 */
	private $itemForTermLookup;

	/**
	 * @var EntityDocumentForTermCache
	 */
	private $entityForTermCache;

	/**
	 * @param ItemForTermLookup $itemForTermLookup
	 * @param EntityDocumentForTermCache $entityForTermCache
	 */
	public function __construct(
		ItemForTermLookup $itemForTermLookup,
		EntityDocumentForTermCache $entityForTermCache
	) {
		$this->itemForTermLookup = $itemForTermLookup;
		$this->entityForTermCache = $entityForTermCache;
	}

	/**
	 * @see ItemForTermLookup::getItemsForTerm
	 */
	public function getItemsForTerm( Term $term ) {
		try {
			return $this->entityForTermCache->fetch( $term, 'item' );
		} catch( OutOfBoundsException $e ) {
			$items = $this->itemForTermLookup->getItemsForTerm( $term );
			$this->entityForTermCache->save( $term, 'item', $items );
			return $items;
		}
	}
}
