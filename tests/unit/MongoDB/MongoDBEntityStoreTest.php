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

	public function testGetEntityDocumentLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\PropertyLookup', $store->getPropertyLookup() );
	}

	public function testGetEntityDocumentSaver() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$store = new MongoDBEntityStore( $collectionMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentSaver', $store->getEntityDocumentSaver() );
	}
}
