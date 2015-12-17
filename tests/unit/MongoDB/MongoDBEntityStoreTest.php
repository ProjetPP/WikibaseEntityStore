<?php

namespace Wikibase\EntityStore\MongoDB;

use Wikibase\EntityStore\EntityStoreTest;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityStore
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityStoreTest extends EntityStoreTest {

	public function testGetEntityLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\EntityLookup', $store->getEntityLookup() );
	}

	public function testGetEntityDocumentLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\PropertyLookup', $store->getPropertyLookup() );
	}

	public function testGetEntityDocumentSaver() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentSaver', $store->getEntityDocumentSaver() );
	}

	public function testGetItemIdForTermLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForTermLookup', $store->getItemIdForTermLookup() );
	}

	public function testGetPropertyIdForTermLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyIdForTermLookup', $store->getPropertyIdForTermLookup() );
	}

	public function testGetItemIdForQueryLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForQueryLookup', $store->getItemIdForQueryLookup() );
	}

	public function testGetPropertyIdForQueryLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyIdForQueryLookup', $store->getPropertyIdForQueryLookup() );
	}
}
