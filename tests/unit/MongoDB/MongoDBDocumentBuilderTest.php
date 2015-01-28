<?php

namespace Wikibase\EntityStore\MongoDB;

use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBDocumentBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testBuildDocumentForEntity() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entitySerializerMock = $this->getMock( 'Serializers\Serializer' );
		$entitySerializerMock->expects( $this->once() )
			->method( 'serialize' )
			->with( $this->equalTo( $item ) )
			->willReturn( array( 'id' => 'Q1' ) );

		$entityDeserializerMock = $this->getMock( 'Deserializers\Deserializer' );

		$documentBuilder = new MongoDBDocumentBuilder( $entitySerializerMock, $entityDeserializerMock );

		$this->assertEquals(
			array( 'id' => 'Q1' ),
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

		$documentBuilder = new MongoDBDocumentBuilder( $entitySerializerMock, $entityDeserializerMock );

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

		$documentBuilder = new MongoDBDocumentBuilder( $entitySerializerMock, $entityDeserializerMock );

		$this->assertEquals(
			null,
			$documentBuilder->buildEntityForDocument( array( 'i' => 'Q1' ) )
		);
	}
}
