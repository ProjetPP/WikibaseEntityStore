<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\EntityDocumentForTermCache
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityDocumentForTermCacheTest extends \PHPUnit_Framework_TestCase {

	public function testFetch() {
		$items = array( new Item( new ItemId( 'Q42' ) ) );

		$cache = new EntityDocumentForTermCache( new ArrayCache() );
		$cache->save( new Term( 'en', 'foo' ), 'item', $items );

		$this->assertEquals( $items, $cache->fetch( new Term( 'en', 'foo' ), 'item' ) );
	}

	public function testFetchWithException() {
		$this->setExpectedException( '\OutOfBoundsException' );

		$cache = new EntityDocumentForTermCache( new ArrayCache() );
		$cache->fetch( new Term( 'en', 'foo' ), 'item' );
	}

	public function testContainsTrue() {
		$items = array( new Item( new ItemId( 'Q42' ) ) );

		$cache = new EntityDocumentForTermCache( new ArrayCache() );
		$cache->save( new Term( 'en', 'foo' ), 'item', $items );

		$this->assertTrue( $cache->contains( new Term( 'en', 'foo' ), 'item' ) );
	}

	public function testContainsFalse() {
		$cache = new EntityDocumentForTermCache( new ArrayCache() );

		$this->assertFalse( $cache->contains( new Term( 'en', 'foo' ), 'item' ) );
	}
}
