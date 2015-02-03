<?php

namespace Wikibase\EntityStore\Api;

use Wikibase\EntityStore\EntityStoreTest;

/**
 * @covers Wikibase\EntityStore\Api\ApiEntityStore
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityStoreTest extends EntityStoreTest {

	public function testGetEntityDocumentLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\PropertyLookup', $store->getPropertyLookup() );
	}

	public function testGetItemForTermLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForTermLookup', $store->getItemIdForTermLookup() );
	}

	public function testGetPropertyForTermLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\PropertyIdForTermLookup', $store->getPropertyIdForTermLookup() );
	}
}
