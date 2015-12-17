<?php

namespace Wikibase\EntityStore\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Wikibase\EntityStore\EntityStoreTest;

/**
 * @covers Wikibase\EntityStore\Cache\CachedEntityStore
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedEntityStoreTest extends EntityStoreTest {

	public function testGetEntityDocumentLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getEntityDocumentLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\EntityDocumentLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getItemLookup' )
			->willReturn( $this->getMock( 'Wikibase\DataModel\Services\Lookup\ItemLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getPropertyLookup' )
			->willReturn( $this->getMock( 'Wikibase\DataModel\Services\Lookup\PropertyLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\PropertyLookup', $store->getPropertyLookup() );
	}

	public function testGetEntityDocumentSaver() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getEntityDocumentSaver' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\EntityDocumentSaver' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentSaver', $store->getEntityDocumentSaver() );
	}

	public function testGetItemIdForTermLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getItemIdForTermLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\ItemIdForTermLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForTermLookup', $store->getItemIdForTermLookup() );
	}

	public function testGetPropertyIdForTermLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getPropertyIdForTermLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\PropertyIdForTermLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyIdForTermLookup', $store->getPropertyIdForTermLookup() );
	}

	public function testGetItemIdForQueryLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getItemIdForQueryLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\ItemIdForQueryLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForQueryLookup', $store->getItemIdForQueryLookup() );
	}

	public function testGetPropertyIdForQueryLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getPropertyIdForQueryLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\PropertyIdForQueryLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyIdForQueryLookup', $store->getPropertyIdForQueryLookup() );
	}

	public function testSetupStore() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'setupStore' );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$store->setupStore();
	}

	public function testSetupIndexes() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'setupIndexes' );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$store->setupIndexes();
	}
}
