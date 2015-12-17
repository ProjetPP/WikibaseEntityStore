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

	public function testGetEntityLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\EntityLookup', $store->getEntityLookup() );
	}

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

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\PropertyLookup', $store->getPropertyLookup() );
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

	public function testGetItemForQueryLookup() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$wikidataQueryApiMock = $this->getMockBuilder( 'WikidataQueryApi\WikidataQueryApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock, $wikidataQueryApiMock );

		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForQueryLookup', $store->getItemIdForQueryLookup() );
	}

	public function testGetItemForQueryLookupWithException() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$store = new ApiEntityStore( $mediawikiApiMock );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$this->assertInstanceOf( 'Wikibase\EntityStore\ItemIdForQueryLookup', $store->getItemIdForQueryLookup() );
	}
}
