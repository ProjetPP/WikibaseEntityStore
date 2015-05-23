<?php

namespace Wikibase\EntityStore\Cache;

use Ask\Language\Description\AnyValue;
use Ask\Language\Option\QueryOptions;
use Doctrine\Common\Cache\ArrayCache;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\Cache\EntityIdForQueryCache
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityIdForQueryCacheTest extends \PHPUnit_Framework_TestCase {

	public function testFetch() {
		$cache = new EntityIdForQueryCache( new ArrayCache() );
		$cache->save( new AnyValue(), null, 'item', [ new ItemId( 'Q42' ) ] );

		$this->assertEquals( [ new ItemId( 'Q42' ) ], $cache->fetch( new AnyValue(), null, 'item' ) );
	}

	public function testFetchWithOptions() {
		$cache = new EntityIdForQueryCache( new ArrayCache() );
		$cache->save( new AnyValue(), new QueryOptions( 10, 0 ), 'item', [ new ItemId( 'Q42' ) ] );

		$this->assertEquals( [ new ItemId( 'Q42' ) ], $cache->fetch( new AnyValue(), new QueryOptions( 10, 0 ), 'item' ) );
	}

	public function testFetchWithException() {
		$this->setExpectedException( '\OutOfBoundsException' );

		$cache = new EntityIdForQueryCache( new ArrayCache() );
		$cache->fetch( new AnyValue(), null, 'item' );
	}

	public function testContainsTrue() {
		$cache = new EntityIdForQueryCache( new ArrayCache() );
		$cache->save(new AnyValue(), null, 'item', [ new ItemId( 'Q42' ) ] );

		$this->assertTrue( $cache->contains( new AnyValue(), null, 'item' ) );
	}

	public function testContainsFalse() {
		$cache = new EntityIdForQueryCache( new ArrayCache() );

		$this->assertFalse( $cache->contains( new AnyValue(), null, 'item' ) );
	}
}
