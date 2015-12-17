<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\Cache;
use RuntimeException;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;

/**
 * Cache of Entity objects.
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityDocumentCache {

	const CACHE_ID_PREFIX = 'wikibase-store-entity-';

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var int
	 */
	private $lifeTime;

	/**
	 * @param Cache $cache
	 * @param int $lifeTime
	 */
	public function __construct( Cache $cache, $lifeTime = 0 ) {
		$this->cache = $cache;
		$this->lifeTime = $lifeTime;
	}

	/**
	 * Returns an Entity from the cache
	 *
	 * @param EntityId $entityId
	 * @return EntityDocument|null
	 */
	public function fetch( EntityId $entityId ) {
		return $this->cache->fetch( $this->getCacheIdFromEntityId( $entityId ) ) ?: null;
	}

	/**
	 * Tests if an Entity exists in the cache.
	 *
	 * @param EntityId $entityId
	 * @return bool
	 */
	public function contains( $entityId ) {
		return $this->cache->contains( $this->getCacheIdFromEntityId( $entityId ) );
	}

	/**
	 * Save an Entity in the cache.
	 *
	 * @param EntityDocument $entity
	 */
	public function save( EntityDocument $entity ) {
		if( !$this->cache->save(
			$this->getCacheIdFromEntityId( $entity->getId() ),
			$entity,
			$this->lifeTime
		) ) {
			throw new RuntimeException( 'The cache failed to save for entity ' . $entity->getId()->getSerialization() );
		}
	}

	private function getCacheIdFromEntityId( EntityId $entityId ) {
		return self::CACHE_ID_PREFIX . WIKIBASE_DATAMODEL_VERSION . '-' . $entityId->getSerialization();
	}
}
