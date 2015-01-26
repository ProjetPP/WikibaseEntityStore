<?php

namespace Wikibase\EntityStore\Internal;

/**
 * @covers Wikibase\EntityStore\Internal\EntitySerializationFactory
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntitySerializationFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testNewEntitySerializer() {
		$serialization = new EntitySerializationFactory();
		$this->assertInstanceOf( 'Serializers\Serializer', $serialization->newEntitySerializer() );
	}

	public function testNewEntityDeserializer() {
		$serialization = new EntitySerializationFactory();
		$this->assertInstanceOf( 'Deserializers\Deserializer', $serialization->newEntityDeserializer() );
	}
}
