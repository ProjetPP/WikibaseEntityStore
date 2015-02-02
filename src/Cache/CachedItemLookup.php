<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\ItemLookup;
use Wikibase\EntityStore\EntityNotFoundException;

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
		try {
			return $this->entityCache->fetch( $itemId );
		} catch( EntityNotFoundException $e ) {
			$item = $this->itemLookup->getItemForId( $itemId );
			$this->entityCache->save( $item );
			return $item;
		}
	}
}
