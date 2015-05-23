<?php

namespace Wikibase\EntityStore\Internal;

use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use Deserializers\Deserializer;
use Serializers\DispatchingSerializer;
use Serializers\Serializer;
use Wikibase\DataModel\DeserializerFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\EntityStore\DataModel\Serializers\SerializedEntitySerializer;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntitySerializationFactory {

	/**
	 * @return Serializer
	 */
	public function newEntitySerializer() {
		$factory = new SerializerFactory( new DataValueSerializer() );
		return new DispatchingSerializer( [
			new SerializedEntitySerializer(),
			$factory->newEntitySerializer(),
		] );
	}

	/**
	 * @return Deserializer
	 */
	public function newEntityDeserializer() {
		$factory = new DeserializerFactory( $this->newDataValueDeserializer(), new BasicEntityIdParser() );
		return $factory->newEntityDeserializer();
	}

	private function newDataValueDeserializer() {
		return new DataValueDeserializer( [
			'number' => 'DataValues\NumberValue',
			'string' => 'DataValues\StringValue',
			'globecoordinate' => 'DataValues\GlobeCoordinateValue',
			'monolingualtext' => 'DataValues\MonolingualTextValue',
			'multilingualtext' => 'DataValues\MultilingualTextValue',
			'quantity' => 'DataValues\QuantityValue',
			'time' => 'DataValues\TimeValue',
			'wikibase-entityid' => 'Wikibase\DataModel\Entity\EntityIdValue'
		] );
	}
}
