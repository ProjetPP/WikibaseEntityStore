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
	 * @var EntityDocumentForTermCache
	 */
	private $entityForTermCache;

	/**
	 * @param EntityStore $entityStore
	 * @param Cache $cache
	 * @param int $lifeTime
	 */
	public function __construct( EntityStore $entityStore, Cache $cache, $lifeTime = 0 ) {
		$this->entityStore = $entityStore;
		$this->entityCache = new EntityDocumentCache( $cache, $lifeTime );
		$this->entityForTermCache = new EntityDocumentForTermCache( $cache, $lifeTime );
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
	 * @see EntityStore::getItemForTermLookup
	 */
	public function getItemForTermLookup() {
		return new CachedItemForTermLookup( $this->entityStore->getItemForTermLookup(), $this->entityForTermCache );
	}

	/**
	 * @see EntityStore::getPropertyForTermLookup
	 */
	public function getPropertyForTermLookup() {
		return new CachedPropertyForTermLookup( $this->entityStore->getPropertyForTermLookup(), $this->entityForTermCache );
	}
}
