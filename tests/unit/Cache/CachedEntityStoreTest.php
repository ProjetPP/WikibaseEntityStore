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
			->willReturn( $this->getMock( 'Wikibase\DataModel\Entity\ItemLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getPropertyLookup' )
			->willReturn( $this->getMock( 'Wikibase\DataModel\Entity\PropertyLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\PropertyLookup', $store->getPropertyLookup() );
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

	public function testGetItemForTermLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getItemForTermLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\ItemForTermLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemForTermLookup', $store->getItemForTermLookup() );
	}

	public function testGetPropertyForTermLookup() {
		$storeMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityStore' )
			->disableOriginalConstructor()
			->getMock();
		$storeMock->expects( $this->once() )
			->method( 'getPropertyForTermLookup' )
			->willReturn( $this->getMock( 'Wikibase\EntityStore\PropertyForTermLookup' ) );
		$store = new CachedEntityStore( $storeMock, new ArrayCache() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyForTermLookup', $store->getPropertyForTermLookup() );
	}
}
