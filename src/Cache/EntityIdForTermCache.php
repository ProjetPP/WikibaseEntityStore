<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\Cache;
use OutOfBoundsException;
use RuntimeException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Term\Term;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityIdForTermCache {

	const CACHE_ID_PREFIX = 'wikibase-store-entityforterm-';

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
	 * @param Term $term
	 * @param string $entityType
	 * @return EntityId[]
	 * @throws OutOfBoundsException
	 */
	public function fetch( Term $term, $entityType ) {
		$result = $this->cache->fetch( $this->getCacheId( $term, $entityType ) );

		if( $result === false ) {
			throw new OutOfBoundsException( 'The search is not in the cache.' );
		}

		return $result;
	}

	/**
	 * @param Term $term
	 * @param string $entityType
	 * @return boolean
	 */
	public function contains( Term $term, $entityType ) {
		return $this->cache->contains( $this->getCacheId( $term, $entityType ) );
	}

	/**
	 * @param Term $term
	 * @param string $entityType
	 * @param EntityId[] $entities
	 */
	public function save( Term $term, $entityType, array $entities ) {
		if( !$this->cache->save(
			$this->getCacheId( $term, $entityType ),
			$entities,
			$this->lifeTime
		) ) {
			throw new RuntimeException( 'The cache failed to save.' );
		}
	}

	private function getCacheId( Term $term, $entityType ) {
		return self::CACHE_ID_PREFIX . WIKIBASE_DATAMODEL_VERSION . '-' .
			$entityType . '-' . $term->getLanguageCode() . '-' . hash( 'md5', $term->getText() );
	}
}
