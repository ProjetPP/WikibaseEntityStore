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

		$this->assertTrue( $deserializer->isDeserializerFor( [ 'type' => 'item' ] ) );
		$this->assertFalse( $deserializer->isDeserializerFor( [] ) );
		$this->assertFalse( $deserializer->isDeserializerFor( null ) );
	}

	public function testDeserialize() {
		$deserializer = $this->buildDeserializer();

		$this->assertEquals(
			new SerializedEntity( null, [ 'type' => 'foo' ] ),
			$deserializer->deserialize( [ 'type' => 'foo' ] )
		);

		$this->assertEquals(
			new SerializedEntity( new ItemId( 'Q1' ), [ 'id' => 'Q1', 'type' => 'item' ] ),
			$deserializer->deserialize( [ 'id' => 'Q1', 'type' => 'item' ] )
		);
	}

	public function testSerializeThrowExceptionForInvalidInput() {
		$deserializer = $this->buildDeserializer();

		$this->setExpectedException( 'Deserializers\Exceptions\DeserializationException' );
		$deserializer->deserialize( [] );
	}

	public function testSerializeThrowExceptionForInvalidId() {
		$deserializer = $this->buildDeserializer();

		$this->setExpectedException( 'Deserializers\Exceptions\DeserializationException' );
		$deserializer->deserialize( [ 'id' => 'bad' ] );
	}
}
