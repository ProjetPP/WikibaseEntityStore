<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\Cache\CachedItemLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetItemForIdWithCacheHit() {
		$item = new Item( new ItemId( 'Q1' ) );

		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\ItemLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new CachedItemLookup( $itemLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getItemForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetItemForIdWithCacheMiss() {
		$item = new Item( new ItemId( 'Q1' ) );

		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\ItemLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemLookupMock->expects( $this->once() )
			->method( 'getItemForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedItemLookup( $itemLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getItemForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetItemForIdWithoutDocument() {
		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\ItemLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemLookupMock->expects( $this->once() )
			->method( 'getItemForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedItemLookup( $itemLookupMock, $entityDocumentCacheMock );

		$this->assertNull( $entityLookup->getItemForId( new ItemId( 'Q1' ) ) );
	}
}
