<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\CachedItemForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedItemForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetItemsForTermWithCacheHit() {
		$items = array( new Item( new ItemId( 'Q1' ) ) );

		$itemForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemForTermLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'item' ) )
			->willReturn( $items );

		$itemForTermLookup = new CachedItemForTermLookup( $itemForTermLookupMock, $entityDocumentForTermCacheMock );
		$this->assertEquals(
			$items,
			$itemForTermLookup->getItemsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetItemsForTermWithCacheMiss() {
		$items = array( new Item( new ItemId( 'Q1' ) ) );

		$itemForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\ItemForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$itemForTermLookupMock->expects( $this->once() )
			->method( 'getItemsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( $items );

		$entityDocumentForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'item' ) )
			->willThrowException( new OutOfBoundsException() );

		$itemForTermLookup = new CachedItemForTermLookup( $itemForTermLookupMock, $entityDocumentForTermCacheMock );
		$this->assertEquals(
			$items,
			$itemForTermLookup->getItemsForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
