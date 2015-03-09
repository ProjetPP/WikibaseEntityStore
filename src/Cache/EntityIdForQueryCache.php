<?php

namespace Wikibase\EntityStore\Cache;

use Ask\Language\Description\Description;
use Ask\Language\Option\QueryOptions;
use Doctrine\Common\Cache\Cache;
use OutOfBoundsException;
use RuntimeException;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityIdForQueryCache {

	const CACHE_ID_PREFIX = 'wikibase-store-entityforquery-';

	const CACHE_LIFE_TIME = 86400;

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
	 * @param Description $description
	 * @param QueryOptions $queryOptions
	 * @param string $entityType
	 * @return EntityId[]
	 */
	public function fetch( Description $description, QueryOptions $queryOptions = null, $entityType ) {
		$result = $this->cache->fetch( $this->getCacheId( $description, $queryOptions, $entityType ) );

		if( $result === false ) {
			throw new OutOfBoundsException( 'The search is not in the cache.' );
		}

		return $result;
	}

	/**
	 * @param Description $description
	 * @param QueryOptions $queryOptions
	 * @param string $entityType
	 * @return boolean
	 */
	public function contains( Description $description, QueryOptions $queryOptions = null, $entityType ) {
		return $this->cache->contains( $this->getCacheId( $description, $queryOptions, $entityType ) );
	}

	/**
	 * @param Description $description
	 * @param QueryOptions $queryOptions
	 * @param string $entityType
	 * @param EntityId[] $entityIds
	 */
	public function save( Description $description, QueryOptions $queryOptions = null, $entityType, array $entityIds ) {
		if( !$this->cache->save(
			$this->getCacheId( $description, $queryOptions, $entityType ),
			$entityIds,
			$this->lifeTime
		) ) {
			throw new RuntimeException( 'The cache failed to save.' );
		}
	}

	private function getCacheId( Description $description, QueryOptions $queryOptions = null, $entityType ) {
		$key = self::CACHE_ID_PREFIX . WIKIBASE_DATAMODEL_VERSION . '-' .
			$entityType . '-' . $description->getHash();

		if( $queryOptions !== null ) {
			$key .= '-' . $queryOptions->getOffset() . '-' . $queryOptions->getLimit();
		}

		return $key;
	}
}
