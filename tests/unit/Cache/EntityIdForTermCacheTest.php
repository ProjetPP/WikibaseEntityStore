<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\EntityIdForTermCache
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityIdForTermCacheTest extends \PHPUnit_Framework_TestCase {

	public function testFetch() {
		$cache = new EntityIdForTermCache( new ArrayCache() );
		$cache->save( new Term( 'en', 'foo' ), 'item', [ new ItemId( 'Q42' ) ] );

		$this->assertEquals( [ new ItemId( 'Q42' ) ], $cache->fetch( new Term( 'en', 'foo' ), 'item' ) );
	}

	public function testFetchWithException() {
		$this->setExpectedException( '\OutOfBoundsException' );

		$cache = new EntityIdForTermCache( new ArrayCache() );
		$cache->fetch( new Term( 'en', 'foo' ), 'item' );
	}

	public function testContainsTrue() {
		$cache = new EntityIdForTermCache( new ArrayCache() );
		$cache->save( new Term( 'en', 'foo' ), 'item', [ new ItemId( 'Q42' ) ] );

		$this->assertTrue( $cache->contains( new Term( 'en', 'foo' ), 'item' ) );
	}

	public function testContainsFalse() {
		$cache = new EntityIdForTermCache( new ArrayCache() );

		$this->assertFalse( $cache->contains( new Term( 'en', 'foo' ), 'item' ) );
	}
}
