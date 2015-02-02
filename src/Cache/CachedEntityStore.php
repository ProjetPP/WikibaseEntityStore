<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\Cache;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedEntityStore extends EntityStore {

	/**
	 * @var EntityStore
	 */
	private $entityStore;

	/**
	 * @var EntityDocumentCache
	 */
	private $entityCache;

	/**
	 * @param EntityStore $entityStore
	 */
	public function __construct( EntityStore $entityStore, Cache $cache ) {
		$this->entityStore = $entityStore;
		$this->entityCache = new EntityDocumentCache( $cache );
	}

	/**
	 * @see EntityStore::getEntityDocumentLookup
	 */
	public function getEntityDocumentLookup() {
		return new CachedEntityDocumentLookup( $this->entityStore->getEntityDocumentLookup(), $this->entityCache );
	}

	/**
	 * @see EntityStore::getItemLookup
	 */
	public function getItemLookup() {
		return new CachedItemLookup( $this->entityStore->getItemLookup(), $this->entityCache );
	}

	/**
	 * @see EntityStore::getPropertyLookup
	 */
	public function getPropertyLookup() {
		return new CachedPropertyLookup( $this->entityStore->getPropertyLookup(), $this->entityCache );
	}

	/**
	 * @see EntityStore::getEntityDocumentSaver
	 */
	public function getEntityDocumentSaver() {
		return $this->entityStore->getEntityDocumentSaver();
	}
}
