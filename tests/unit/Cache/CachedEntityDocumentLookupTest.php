<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\EntityStore\EntityNotFoundException;

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
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForIdWithException() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException' );
		$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) );
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
			array( $item ),
			$entityLookup->getEntityDocumentsForIds( array( new ItemId( 'Q1' ) ) )
		);
	}

	public function testGetEntityDocumentsForIdsWithCacheMiss() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( array( new ItemId( 'Q1' ) ) ) )
			->willReturn( array( $item ) );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			array( $item ),
			$entityLookup->getEntityDocumentsForIds( array( new ItemId( 'Q1' ) ) )
		);
	}

	public function testGetEntityDocumentsForIdsWithNoRetuls() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( array( new ItemId( 'Q1' ) ) ) )
			->willReturn( array() );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new CachedEntityDocumentLookup( $entityDocumentLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			array(),
			$entityLookup->getEntityDocumentsForIds( array( new ItemId( 'Q1' ) ) )
		);
	}
}
