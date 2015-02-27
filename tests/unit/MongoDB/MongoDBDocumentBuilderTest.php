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
				new TermList( array( new Term( 'en', 'foo' ) ) ),
				new TermList( array( new Term( 'en', 'bar' ) ) ),
				new AliasGroupList( array( new AliasGroup( 'fr', array( 'bÊz', 'bat' ) ) ) )
			)
		);

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entitySerializerMock->expects( $this->once() )
			->method( 'serialize' )
			->with( $this->equalTo( $item ) )
			->willReturn( array(
				'type' => 'item',
				'id' => 'Q1',
				'labels' => array(
					'en' => array( 'language' => 'en', 'value' => 'foo' )
				),
				'descriptions' => array(
					'en' => array( 'language' => 'en', 'value' => 'bar' ),
				),
				'aliases' => array(
					'fr' => array(
						array( 'language' => 'fr', 'value' => 'baz' ),
						array( 'language' => 'fr', 'value' => 'bat' )
					)
				),
				'claims' => array(
					'P1' => array(
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => 'foo',
									'type' => 'string'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => array( 'entity-type' => 'item', 'numeric-id' => 1 ),
									'type' => 'wikibase-entityid'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => array( 'entity-type' => 'property', 'numeric-id' => 1 ),
									'type' => 'wikibase-entityid'
								)
							)
						)
					),
					'P2' => array(
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => array(
									'value' => array( 'time' => '+00000001952-03-11T00:00:00Z' ),
									'type' => 'time'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => array(
									'value' => array( 'latitude' => 1, 'longitude' => 1 ),
									'type' => 'globecoordinate'
								)
							)
						)
					)
				)
			) );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->assertEquals(
			array(
				'_id' => 'Q1',
				'type' => 'item',
				'id' => 'Q1',
				'labels' => array(
					'en' => array( 'language' => 'en', 'value' => 'foo' ),
				),
				'descriptions' => array(
					'en' => array( 'language' => 'en', 'value' => 'bar' ),
				),
				'aliases' => array(
					'fr' => array(
						array( 'language' => 'fr', 'value' => 'baz' ),
						array( 'language' => 'fr', 'value' => 'bat' )
					)
				),
				'claims' => array(
					'P1' => array(
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => 'foo',
									'type' => 'string'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => array( 'entity-type' => 'item', 'numeric-id' => 1 ),
									'type' => 'wikibase-entityid'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P1',
								'datavalue' => array(
									'value' => array( 'entity-type' => 'property', 'numeric-id' => 1 ),
									'type' => 'wikibase-entityid'
								)
							)
						)
					),
					'P2' => array(
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => array(
									'value' => array( 'time' => '+00000001952-03-11T00:00:00Z' ),
									'type' => 'time'
								)
							)
						),
						array(
							'mainsnak' => array(
								'snaktype' => 'value',
								'property' => 'P2',
								'datavalue' => array(
									'value' => array( 'latitude' => 1, 'longitude' => 1 ),
									'type' => 'globecoordinate'
								)
							)
						)
					)
				),
				'sterms' => array(
					'en' => array( new MongoBinData( 'foo', MongoBinData::GENERIC ) ),
					'fr' => array(
						new MongoBinData( 'baz', MongoBinData::GENERIC ),
						new MongoBinData( 'bat', MongoBinData::GENERIC )
					)
				),
				'sclaims' => array(
					'string' => array( 'P1-foo' ),
					'wikibase-entityid' => array( 'P1-Q1', 'P1-P1' ),
					'time' => array( 'P2-+00000001952-03-11T00:00:00Z' )
				)
			),
			$documentBuilder->buildDocumentForEntity( $item )
		);
	}

	public function testBuildDocumentForEntityWithLanguageOption() {
		$item = new Item(
			new ItemId( 'Q1' ),
			new Fingerprint(
				new TermList( array( new Term( 'en', 'foo' ), new Term( 'de', 'bar' ) ) ),
				new TermList( array( new Term( 'en', 'bar' ) ) ),
				new AliasGroupList( array(
					new AliasGroup( 'fr', array( 'baz', 'bat' ) ),
					new AliasGroup( 'it', array( 'foo' ) )
				) )
			)
		);

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entitySerializerMock->expects( $this->once() )
			->method( 'serialize' )
			->with( $this->equalTo( $item ) )
			->willReturn( array(
				'type' => 'item',
				'id' => 'Q1',
				'labels' => array(
					'en' => array( 'language' => 'en', 'value' => 'foo' ),
					'de' => array( 'language' => 'en', 'value' => 'bar' ),
				),
				'descriptions' => array(
					'en' => array( 'language' => 'en', 'value' => 'bar' ),
				),
				'aliases' => array(
					'fr' => array(
						array( 'language' => 'fr', 'value' => 'baz' ),
						array( 'language' => 'fr', 'value' => 'bat' )
					),
					'it' => array( array( 'language' => 'it', 'value' => 'goo' ) )
				)
			) );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => array( 'en', 'fr' ) ) )
		);

		$this->assertEquals(
			array(
				'_id' => 'Q1',
				'id' => 'Q1',
				'type' => 'item',
				'labels' => array(
					'en' => array( 'language' => 'en', 'value' => 'foo' ),
				),
				'descriptions' => array(
					'en' => array( 'language' => 'en', 'value' => 'bar' ),
				),
				'aliases' => array(
					'fr' => array(
						array( 'language' => 'fr', 'value' => 'baz' ),
						array( 'language' => 'fr', 'value' => 'bat' )
					)
				),
				'sterms' => array(
					'en' => array( new MongoBinData( 'foo', MongoBinData::GENERIC ) ),
					'fr' => array(
						new MongoBinData( 'baz', MongoBinData::GENERIC ),
						new MongoBinData( 'bat', MongoBinData::GENERIC )
					)
				),
				'sclaims' => array()
			),
			$documentBuilder->buildDocumentForEntity( $item )
		);
	}

	public function testBuildEntityForDocument() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$entityDeserializerMock->expects( $this->once() )
			->method( 'deserialize' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( $item );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->assertEquals(
			$item,
			$documentBuilder->buildEntityForDocument( array( 'id' => 'Q1' ) )
		);
	}

	public function testBuildEntityForDocumentWithException() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$entityDeserializerMock->expects( $this->once() )
			->method( 'deserialize' )
			->with( $this->equalTo( array( 'i' => 'Q1' ) ) )
			->willThrowException( new DeserializationException() );

		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->assertEquals(
			null,
			$documentBuilder->buildEntityForDocument( array( 'i' => 'Q1' ) )
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
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->assertEquals(
			$cleaned,
			$documentBuilder->cleanTextForSearch( $text )
		);
	}

	public function cleanTextForSearchProvider() {
		return array(
			array(
				'test',
				new MongoBinData( 'test', MongoBinData::GENERIC )
			),
			array(
				'TODO',
				new MongoBinData( 'todo', MongoBinData::GENERIC )
			),
			array(
				'Être',
				new MongoBinData( 'être', MongoBinData::GENERIC )
			),
		);
	}

	public function testBuildEntityIdForDocument() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->assertEquals(
			new ItemId( 'Q42' ),
			$documentBuilder->buildEntityIdForDocument( array( '_id' => 'Q42' ) )
		);
	}

	public function testBuildEntityIdForDocumentWithException() {
		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );
		$documentBuilder = new MongoDBDocumentBuilder(
			$entitySerializerMock,
			$entityDeserializerMock,
			new BasicEntityIdParser(),
			new EntityStoreOptions(array( EntityStore::OPTION_LANGUAGES => null ) )
		);

		$this->setExpectedException( 'Wikibase\DataModel\Entity\EntityIdParsingException' );
		$documentBuilder->buildEntityIdForDocument( array() );
	}
}
