<?php

namespace Wikibase\EntityStore\DataModel\Serializers;

use Wikibase\DataModel\Entity\Item;
use Wikibase\EntityStore\DataModel\SerializedEntity;

/**
 * @covers Wikibase\EntityStore\DataModel\Serializers\SerializedEntitySerializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntitySerializerTest extends \PHPUnit_Framework_TestCase {

	public function testIsSerializerFor() {
		$serializer = new SerializedEntitySerializer();

		$this->assertTrue( $serializer->isSerializerFor( new SerializedEntity( null, [ 'type' => 'item' ] ) ) );
		$this->assertFalse( $serializer->isSerializerFor( null ) );
		$this->assertFalse( $serializer->isSerializerFor( new Item() ) );
	}

	public function testSerialize() {
		$serializer = new SerializedEntitySerializer();

		$this->assertEquals(
			[ 'type' => 'foo' ],
			$serializer->serialize( new SerializedEntity( null, [ 'type' => 'foo' ]))
		);
	}

	public function testSerializeThrowException() {
		$serializer = new SerializedEntitySerializer();

		$this->setExpectedException( 'Serializers\Exceptions\UnsupportedObjectException' );
		$serializer->serialize( new Item() );
	}
}
