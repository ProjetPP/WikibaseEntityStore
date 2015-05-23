<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\CachedItemIdForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemIdForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetItemsForTermWithCacheHit() {
		$itemIds = [ new ItemId( 'Q1' ) ];

		$itemIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityIdForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'item' ) )
			->willReturn( $itemIds );

		$itemIdForTermLookup = new CachedItemIdForTermLookup( $itemIdForTermLookupMock, $entityIdForTermCacheMock );
		$this->assertEquals(
			$itemIds,
			$itemIdForTermLookup->getItemIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetItemsForTermWithCacheMiss() {
		$itemIds = [ new ItemId( 'Q1' ) ];

		$itemIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemIdForTermLookupMock->expects( $this->once() )
			->method( 'getItemIdsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( $itemIds );

		$entityIdForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'item' ) )
			->willThrowException( new OutOfBoundsException() );

		$itemIdForTermLookup = new CachedItemIdForTermLookup( $itemIdForTermLookupMock, $entityIdForTermCacheMock );
		$this->assertEquals(
			$itemIds,
			$itemIdForTermLookup->getItemIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
