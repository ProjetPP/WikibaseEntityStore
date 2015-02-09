<?php

namespace Wikibase\EntityStore\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\UnsupportedObjectException;
use Wikibase\EntityStore\DataModel\SerializedEntity;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntitySerializer implements DispatchableSerializer {

	/**
	 * @see Serializer::isSerializerFor
	 */
	public function isSerializerFor( $object ) {
		return is_object( $object ) && $object instanceof SerializedEntity;
	}

	/**
	 * @see Serializer::serialize
	 */
	public function serialize( $object ) {
		if ( !$this->isSerializerFor( $object ) ) {
			throw new UnsupportedObjectException(
				$object,
				'SerializedEntitySerializer can only serialize SerializedEntity objects'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( SerializedEntity $entity ) {
		return $entity->getSerialization();
	}
}
