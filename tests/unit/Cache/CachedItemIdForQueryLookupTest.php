<?php

namespace Wikibase\EntityStore\Cache;

use Ask\Language\Description\AnyValue;
use OutOfBoundsException;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\Cache\CachedItemIdForQueryLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemIdForQueryLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetItemsForQueryWithCacheHit() {
		$itemIds = [ new ItemId( 'Q1' ) ];

		$itemIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityIdForQueryCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForQueryCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new AnyValue() ), $this->isNull(), $this->equalTo( 'item' ) )
			->willReturn( $itemIds );

		$itemIdForQueryLookup = new CachedItemIdForQueryLookup( $itemIdForQueryLookupMock, $entityIdForQueryCacheMock );
		$this->assertEquals(
			$itemIds,
			$itemIdForQueryLookup->getItemIdsForQuery( new AnyValue() )
		);
	}

	public function testGetItemsForQueryWithCacheMiss() {
		$itemIds = [ new ItemId( 'Q1' ) ];

		$itemIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemIdForQueryLookupMock->expects( $this->once() )
			->method( 'getItemIdsForQuery' )
			->with( $this->equalTo( new AnyValue() ) )
			->willReturn( $itemIds );

		$entityIdForQueryCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForQueryCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new AnyValue() ), $this->isNull(), $this->equalTo( 'item' ) )
			->willThrowException( new OutOfBoundsException() );

		$itemIdForQueryLookup = new CachedItemIdForQueryLookup( $itemIdForQueryLookupMock, $entityIdForQueryCacheMock );
		$this->assertEquals(
			$itemIds,
			$itemIdForQueryLookup->getItemIdsForQuery( new AnyValue() )
		);
	}
}
