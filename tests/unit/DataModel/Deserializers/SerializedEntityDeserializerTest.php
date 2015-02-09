<?php

namespace Wikibase\EntityStore\DataModel\Deserializers;

use Wikibase\DataModel\Deserializers\EntityIdDeserializer;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\EntityStore\DataModel\SerializedEntity;

/**
 * @covers Wikibase\EntityStore\DataModel\Deserializers\SerializedEntityDeserializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntityDeserializerTest extends \PHPUnit_Framework_TestCase {

	private function buildDeserializer() {
		return new SerializedEntityDeserializer( new EntityIdDeserializer( new BasicEntityIdParser() ) );
	}

	public function testIsDeserializerFor() {
		$deserializer = $this->buildDeserializer();

		$this->assertTrue( $deserializer->isDeserializerFor( array() ) );
		$this->assertFalse( $deserializer->isDeserializerFor( null ) );
	}

	public function testDeserialize() {
		$deserializer = $this->buildDeserializer();

		$this->assertEquals(
			new SerializedEntity( null, array( 'type' => 'foo' ) ),
			$deserializer->deserialize(array( 'type' => 'foo' ) )
		);

		$this->assertEquals(
			new SerializedEntity( new ItemId( 'Q1' ), array( 'id' => 'Q1' ) ),
			$deserializer->deserialize(array( 'id' => 'Q1' ) )
		);
	}

	public function testSerializeThrowExceptionForInvalidInput() {
		$deserializer = $this->buildDeserializer();

		$this->setExpectedException( 'Deserializers\Exceptions\DeserializationException' );
		$deserializer->deserialize( null );
	}

	public function testSerializeThrowExceptionForInvalidId() {
		$deserializer = $this->buildDeserializer();

		$this->setExpectedException( 'Deserializers\Exceptions\DeserializationException' );
		$deserializer->deserialize( array( 'id' => 'bad' ) );
	}
}
