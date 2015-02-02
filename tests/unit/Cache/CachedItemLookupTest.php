<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\ItemNotFoundException;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * @covers Wikibase\EntityStore\Cache\CachedItemLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetItemForIdWithCacheHit() {
		$item = new Item( new ItemId( 'Q1' ) );

		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Entity\ItemLookup' )
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

		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Entity\ItemLookup' )
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
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedItemLookup( $itemLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getItemForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetItemForIdWithException() {
		$itemLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Entity\ItemLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemLookupMock->expects( $this->once() )
			->method( 'getItemForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new ItemNotFoundException( new ItemId( 'Q1' ) ) );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedItemLookup( $itemLookupMock, $entityDocumentCacheMock );
		$this->setExpectedException( 'Wikibase\DataModel\Entity\ItemNotFoundException' );
		$entityLookup->getItemForId( new ItemId( 'Q1' ) );
	}
}
