<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\Cache\CachedEntityDocumentLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedEntityDocumentLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentForIdWithCacheHit() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForIdWithCacheMiss() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForIdWithoutDocument() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );

		$this->assertNull( $entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) ) );
	}

	public function testGetEntityDocumentsForIdsWithCacheHit() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			[ $item ],
			$entityLookup->getEntityDocumentsForIds( [ new ItemId( 'Q1' ) ] )
		);
	}

	public function testGetEntityDocumentsForIdsWithCacheMiss() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( [ new ItemId( 'Q1' ) ] ) )
			->willReturn( [ $item ] );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			[ $item ],
			$entityLookup->getEntityDocumentsForIds( [ new ItemId( 'Q1' ) ] )
		);
	}

	public function testGetEntityDocumentsForIdsWithoutReturns() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( [ new ItemId( 'Q1' ) ] ) )
			->willReturn( [] );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			[],
			$entityLookup->getEntityDocumentsForIds( [ new ItemId( 'Q1' ) ] )
		);
	}
}
