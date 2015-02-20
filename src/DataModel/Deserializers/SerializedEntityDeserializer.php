<?php

namespace Wikibase\EntityStore\DataModel\Deserializers;

use Deserializers\DispatchableDeserializer;
use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Deserializers\EntityIdDeserializer;
use Wikibase\EntityStore\DataModel\SerializedEntity;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntityDeserializer implements DispatchableDeserializer {

	/**
	 * @var EntityIdDeserializer
	 */
	private $entityIdDeserializer;

	/**
	 * @param EntityIdDeserializer $entityIdDeserializer
	 */
	public function __construct( EntityIdDeserializer $entityIdDeserializer ) {
		$this->entityIdDeserializer = $entityIdDeserializer;
	}

	/**
	 * @see Deserializer::isDeserializerFor
	 */
	public function isDeserializerFor( $object ) {
		return is_array( $object ) && array_key_exists( 'type', $object );
	}

	/**
	 * @see Deserializer::deserialize
	 */
	public function deserialize( $serialization ) {
		if ( !$this->isDeserializerFor( $serialization ) ) {
			throw new DeserializationException( 'SerializedEntityDeserializer serialization should be an array' );
		}

		return new SerializedEntity(
			$this->getIdFromSerialization( $serialization ),
			$serialization
		);
	}

	private function getIdFromSerialization( array $serialization ) {
		if ( !array_key_exists( 'id', $serialization ) ) {
			return null;
		}

		return $this->entityIdDeserializer->deserialize( $serialization['id'] );
	}
}
