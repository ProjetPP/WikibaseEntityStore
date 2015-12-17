<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\ItemLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemLookup implements ItemLookup {

	/**
	 * @var ItemLookup
	 */
	private $itemLookup;

	/**
	 * @var EntityDocumentCache
	 */
	private $entityCache;

	/**
	 * @param ItemLookup $itemLookup
	 * @param EntityDocumentCache $entityCache
	 */
	public function __construct( ItemLookup $itemLookup, EntityDocumentCache $entityCache ) {
		$this->itemLookup = $itemLookup;
		$this->entityCache = $entityCache;
	}

	/**
	 * @see ItemLookup::getItemForId
	 */
	public function getItemForId( ItemId $itemId ) {
		$item = $this->entityCache->fetch( $itemId );

		if( $item === null ) {
			$item = $this->itemLookup->getItemForId( $itemId );
			if( $item !== null ) {
				$this->entityCache->save( $item );
			}
		}

		return $item;
	}
}
