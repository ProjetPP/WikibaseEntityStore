<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\Cache\EntityDocumentCache
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityDocumentCacheTest extends \PHPUnit_Framework_TestCase {

	public function testFetchWithHit() {
		$item = new Item( new ItemId( 'Q42' ) );

		$cache = new EntityDocumentCache( new ArrayCache() );
		$cache->save( $item );

		$this->assertEquals( $item, $cache->fetch( new ItemId( 'Q42' ) ) );
	}

	public function testFetchWithMiss() {
		$cache = new EntityDocumentCache( new ArrayCache() );
		$this->assertNull( $cache->fetch( new ItemId( 'Q42' ) ) );
	}

	public function testContainsTrue() {
		$item = new Item( new ItemId( 'Q42' ) );

		$cache = new EntityDocumentCache( new ArrayCache() );
		$cache->save($item);

		$this->assertTrue( $cache->contains( new ItemId( 'Q42' ) ) );
	}

	public function testContainsFalse() {
		$cache = new EntityDocumentCache( new ArrayCache() );

		$this->assertFalse( $cache->contains( new ItemId( 'Q42' ) ) );
	}
}
