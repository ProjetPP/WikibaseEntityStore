<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\Cache;
use Wikibase\EntityStore\EntityStore;

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
	 * @var EntityIdForTermCache
	 */
	private $entityIdForTermCache;

	/**
	 * @var EntityIdForQueryCache
	 */
	private $entityIdForQueryCache;

	/**
	 * @param EntityStore $entityStore
	 * @param Cache $cache
	 * @param int $lifeTime
	 */
	public function __construct( EntityStore $entityStore, Cache $cache, $lifeTime = 0 ) {
		parent::__construct();

		$this->entityStore = $entityStore;
		$this->entityCache = new EntityDocumentCache( $cache, $lifeTime );
		$this->entityIdForTermCache = new EntityIdForTermCache( $cache, $lifeTime );
		$this->entityIdForQueryCache = new EntityIdForQueryCache( $cache, $lifeTime );
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

	/**
	 * @see EntityStore::getItemIdForTermLookup
	 */
	public function getItemIdForTermLookup() {
		return new CachedItemIdForTermLookup( $this->entityStore->getItemIdForTermLookup(), $this->entityIdForTermCache );
	}

	/**
	 * @see EntityStore::getPropertyIdForTermLookup
	 */
	public function getPropertyIdForTermLookup() {
		return new CachedPropertyIdForTermLookup( $this->entityStore->getPropertyIdForTermLookup(), $this->entityIdForTermCache );
	}

	/**
	 * @see EntityStore::getItemIdForQueryLookup
	 */
	public function getItemIdForQueryLookup() {
		return new CachedItemIdForQueryLookup( $this->entityStore->getItemIdForQueryLookup(), $this->entityIdForQueryCache );
	}

	/**
	 * @see EntityStore::getPropertyIdForQueryLookup
	 */
	public function getPropertyIdForQueryLookup() {
		return $this->entityStore->getPropertyIdForQueryLookup();
	}

	/**
	 * @see EntityStore::setupStore
	 */
	public function setupStore() {
		$this->entityStore->setupStore();
	}

	/**
	 * @see EntityStore::setupIndexes
	 */
	public function setupIndexes() {
		$this->entityStore->setupIndexes();
	}
}
