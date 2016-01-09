<?php

namespace Wikibase\EntityStore\MongoDB;

use Deserializers\Exceptions\DeserializationException;
use MongoBinData;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupList;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBDocumentBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testBuildDocumentForEntity() {
		$item = new Item(
			new ItemId( 'Q1' ),
			new Fingerprint(
				new TermList( [ new Term( 'en', 'foo ' ) ] ),
				new TermList( [ new Term( 'en', 'bar' ) ] ),
				new AliasGroupList( [ new AliasGroup( 'fr', [ 'BAZée', 'bat' ] ) ] )
			)
		);

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entitySerializerMock->expects( $this->once() )
			->method( 'serialize' )
			->with( $this->equalTo( $item ) )
			->willReturn( [
				'type' => 'item',
				'id' => 'Q1',
				'labels' => [
					'en' => [ 'language' => 'en', 'value' => 'foo ' ]
				],
				'descriptions' => [
					'en' => [ 'language' => 'en', 'value' => 'bar' ],
				],
				'aliases' => [
					'fr' => [
						[ 'language' => 'fr', 'value' => 'BAZée' ],
						[ 'language' => 'fr', 'value' => 'bat' ]
					]
				],
				'claims' => [
					'P1' => [
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => 'foo',
									'type' => 'string'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => str_repeat( '0123456789', 20 ),
									'type' => 'string'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => [ 'entity-type' => 'item', 'numeric-id' => 1 ],
									'type' => 'wikibase-entityid'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => [ 'entity-type' => 'property', 'numeric-id' => 1 ],
									'type' => 'wikibase-entityid'
								]
							]
						]
					],
					'P2' => [
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => [
									'value' => [ 'time' => '+00000001952-03-11T00:00:00Z' ],
									'type' => 'time'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => [
									'value' => [ 'latitude' => 1, 'longitude' => 1 ],
									'type' => 'globecoordinate'
								]
							]
						]
					]
				]
			] );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->assertEquals(
			[
				'_id' => 'Q1',
				'type' => 'item',
				'id' => 'Q1',
				'labels' => [
					'en' => [ 'language' => 'en', 'value' => 'foo ' ],
				],
				'descriptions' => [
					'en' => [ 'language' => 'en', 'value' => 'bar' ],
				],
				'aliases' => [
					'fr' => [
						[ 'language' => 'fr', 'value' => 'BAZée' ],
						[ 'language' => 'fr', 'value' => 'bat' ]
					]
				],
				'claims' => [
					'P1' => [
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => 'foo',
									'type' => 'string'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => str_repeat( '0123456789', 20 ),
									'type' => 'string'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => [ 'entity-type' => 'item', 'numeric-id' => 1 ],
									'type' => 'wikibase-entityid'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => [
									'value' => [ 'entity-type' => 'property', 'numeric-id' => 1 ],
									'type' => 'wikibase-entityid'
								]
							]
						]
					],
					'P2' => [
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => [
									'value' => [ 'time' => '+00000001952-03-11T00:00:00Z' ],
									'type' => 'time'
								]
							]
						],
						[
							'mainsnak' => [
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => [
									'value' => [ 'latitude' => 1, 'longitude' => 1 ],
									'type' => 'globecoordinate'
								]
							]
						]
					]
				],
				'sterms' => [
					'en' => [ new MongoBinData( md5( 'foo', true ), MongoBinData::GENERIC ) ],
					'fr' => [
						new MongoBinData( md5( 'bazée', true ), MongoBinData::GENERIC ),
						new MongoBinData( md5( 'bat', true ), MongoBinData::GENERIC )
					]
				],
				'sclaims' => [
					'string' => [ 'P1-foo', 'P1-c902a17556796a9f97afa23bad130b04' ],
					'wikibase-entityid' => [ 'P1-Q1', 'P1-P1' ],
					'time' => [ 'P2-+00000001952-03-11T00:00:00Z' ]
				]
			],
			$documentBuilder->buildDocumentForEntity( $item )
		);
	}

	public function testBuildDocumentForEntityWithLanguageOption() {
		$item = new Item(
			new ItemId( 'Q1' ),
			new Fingerprint(
				new TermList( [ new Term( 'en', 'foo' ), new Term( 'de', 'bar' ) ] ),
				new TermList( [ new Term( 'en', 'bar' ) ] ),
				new AliasGroupList( [
					new AliasGroup( 'fr', [ 'baz', 'bat' ] ),
					new AliasGroup( 'it', [ 'foo' ] )
				] )
			)
		);

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entitySerializerMock->expects( $this->once() )
			->method( 'serialize' )
			->with( $this->equalTo( $item ) )
			->willReturn( [
				'type' => 'item',
				'id' => 'Q1',
				'labels' => [
					'en' => [ 'language' => 'en', 'value' => 'foo ' ],
					'de' => [ 'language' => 'en', 'value' => 'bar' ],
				],
				'descriptions' => [
					'en' => [ 'language' => 'en', 'value' => 'bar' ],
				],
				'aliases' => [
					'fr' => [
						[ 'language' => 'fr', 'value' => 'BAzée' ],
						[ 'language' => 'fr', 'value' => 'bat' ]
					],
					'it' => [ [ 'language' => 'it', 'value' => 'goo' ] ]
				]
			] );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => [ 'en', 'fr' ] ] )
		);

		$this->assertEquals(
			[
				'_id' => 'Q1',
				'id' => 'Q1',
				'type' => 'item',
				'labels' => [
					'en' => [ 'language' => 'en', 'value' => 'foo ' ],
				],
				'descriptions' => [
					'en' => [ 'language' => 'en', 'value' => 'bar' ],
				],
				'aliases' => [
					'fr' => [
						[ 'language' => 'fr', 'value' => 'BAzée' ],
						[ 'language' => 'fr', 'value' => 'bat' ]
					]
				],
				'sterms' => [
					'en' => [ new MongoBinData( md5( 'foo', true ), MongoBinData::GENERIC ) ],
					'fr' => [
						new MongoBinData( md5( 'bazée', true ), MongoBinData::GENERIC ),
						new MongoBinData( md5( 'bat', true ), MongoBinData::GENERIC )
					]
				],
				'sclaims' => []
			],
			$documentBuilder->buildDocumentForEntity( $item )
		);
	}

	public function testBuildEntityForDocument() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$entityDeserializerMock->expects( $this->once() )
			->method( 'deserialize' )
			->with( $this->equalTo( [ 'id' => 'Q1' ] ) )
			->willReturn( $item );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->assertEquals(
			$item,
			$documentBuilder->buildEntityForDocument( [ 'id' => 'Q1' ] )
		);
	}

	public function testBuildEntityForDocumentWithException() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$entityDeserializerMock->expects( $this->once() )
			->method( 'deserialize' )
			->with( $this->equalTo( [ 'i' => 'Q1' ] ) )
			->willThrowException( new DeserializationException() );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->assertEquals(
			null,
			$documentBuilder->buildEntityForDocument( [ 'i' => 'Q1' ] )
		);
	}

	/**
	 * @dataProvider cleanTextForSearchProvider
	 */
	public function testBuildTermForSearch( $text, $cleaned ) {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->assertEquals(
			$cleaned,
			$documentBuilder->cleanTextForSearch( $text )
		);
	}

	public function cleanTextForSearchProvider() {
		return [
			[
				'test',
				new MongoBinData( md5( 'test', true ), MongoBinData::GENERIC )
			],
			[
				'TODO',
				new MongoBinData( md5( 'todo', true ), MongoBinData::GENERIC )
			],
			[
				'Être',
				new MongoBinData( md5( 'être', true ), MongoBinData::GENERIC )
			],
		];
	}

	public function testBuildEntityIdForDocument() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->assertEquals(
			new ItemId( 'Q42' ),
			$documentBuilder->buildEntityIdForDocument( [ '_id' => 'Q42' ] )
		);
	}

	public function testBuildEntityIdForDocumentWithException() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions([ EntityStore::OPTION_LANGUAGES => null ] )
		);

		$this->setExpectedException( 'Wikibase\DataModel\Entity\EntityIdParsingException' );
		$documentBuilder->buildEntityIdForDocument( [] );
	}
}
