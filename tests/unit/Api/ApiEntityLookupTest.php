<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\SimpleRequest;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;

/**
 * @covers Wikibase\EntityStore\Api\ApiEntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForIds() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$mediawikiApiMock->expects( $this->once() )
			->method( 'getRequest' )
			->with( $this->equalTo(
				new SimpleRequest(
					'wbgetentities',
					[
						'ids' => 'Q42|P42'
					]
				)
			) )
			->will( $this->returnValue( [
				'entities' => [
					[
						'id' => 'Q42',
						'type' => 'item'
					],
					[
						'id' => 'P42',
						'type' => 'property',
						'datatype' => 'string'
					]
				]
			] ) );

		$serializationFactory = new EntitySerializationFactory();
		$lookup = new ApiEntityLookup(
			$mediawikiApiMock,
			$serializationFactory->newEntityDeserializer(),
			new EntityStoreOptions( [
				EntityStore::OPTION_LANGUAGES => null,
				EntityStore::OPTION_LANGUAGE_FALLBACK => false
			] )
		);

		$this->assertEquals(
			[
				new Item( new ItemId( 'Q42' ) ),
				new Property( new PropertyId( 'P42' ), null, 'string' )
			],
			$lookup->getEntityDocumentsForIds( [ new ItemId( 'Q42' ), new PropertyId( 'P42' ) ] )
		);
	}

	public function testGetEntityDocumentsForIdsWithEmptyInput() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();

		$serializationFactory = new EntitySerializationFactory();
		$lookup = new ApiEntityLookup(
			$mediawikiApiMock,
			$serializationFactory->newEntityDeserializer(),
			new EntityStoreOptions( [
				EntityStore::OPTION_LANGUAGES => null,
				EntityStore::OPTION_LANGUAGE_FALLBACK => false
			] )
		);

		$this->assertEquals( [], $lookup->getEntityDocumentsForIds( [] ) );
	}

	public function testGetEntityDocumentForId() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$mediawikiApiMock->expects( $this->once() )
			->method( 'getRequest' )
			->with( $this->equalTo(
				new SimpleRequest(
					'wbgetentities',
					[
						'ids' => 'Q42',
						'languages' => 'en|fr',
						'languagefallback' => true
					]
				)
			) )
			->will( $this->returnValue( [
				'entities' => [
					[
						'id' => 'Q42',
						'type' => 'item'
					]
				]
			] ) );

		$serializationFactory = new EntitySerializationFactory();
		$lookup = new ApiEntityLookup(
			$mediawikiApiMock,
			$serializationFactory->newEntityDeserializer(),
			new EntityStoreOptions( [
				EntityStore::OPTION_LANGUAGES => [ 'en', 'fr' ],
				EntityStore::OPTION_LANGUAGE_FALLBACK => true
			] )
		);

		$this->assertEquals(
			new Item( new ItemId( 'Q42' ) ),
			$lookup->getEntityDocumentForId( new ItemId( 'Q42' ) )
		);
	}

	public function testGetEntityDocumentWithoutDocument() {
		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$mediawikiApiMock->expects( $this->once() )
			->method( 'getRequest' )
			->with( $this->equalTo(
				new SimpleRequest(
					'wbgetentities',
					[
						'ids' => 'Q42',
						'languages' => 'en|fr'
					]
				)
			) )
			->will( $this->returnValue( [ 'entities' => [] ] ) );

		$serializationFactory = new EntitySerializationFactory();
		$lookup = new ApiEntityLookup(
			$mediawikiApiMock,
			$serializationFactory->newEntityDeserializer(),
			new EntityStoreOptions( [
				EntityStore::OPTION_LANGUAGES => [ 'en', 'fr' ],
				EntityStore::OPTION_LANGUAGE_FALLBACK => false
			] ) );

		$this->assertNull( $lookup->getEntityDocumentForId( new ItemId( 'Q42' ) ) );
	}
}
