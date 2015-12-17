<?php

namespace Wikibase\EntityStore\DataModel;

use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;

/**
 * An entity containing its serialization
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntity implements EntityDocument {

	/**
	 * @var EntityId|null
	 */
	private $entityId;

	/**
	 * @var array
	 */
	private $serialization;

	/**
	 * @param EntityId|null $entityId
	 * @param array $serialization
	 */
	public function __construct( EntityId $entityId = null, array $serialization ) {
		if( !array_key_exists( 'type', $serialization ) ) {
			throw new InvalidArgumentException( 'The entity serialization does not have a type.' );
		}

		$this->entityId = $entityId;
		$this->serialization = $serialization;
	}

	/**
	 * @see EntityDocument::getId
	 */
	public function getId() {
		return $this->entityId;
	}

	/**
	 * @see EntityDocument::setId
	 */
	public function setId( $id ) {
		throw new InvalidArgumentException( 'Read only entity' );
	}

	/**
	 * @see EntityDocument::getType
	 */
	public function getType() {
		return $this->serialization['type'];
	}

	/**
	 * @see EntityDocument::isEmpty
	 */
	public function isEmpty() {
		foreach( array_keys( $this->serialization ) as $key ) {
			if( !in_array( $key, [ 'type', 'id' ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function getSerialization() {
		return $this->serialization;
	}
}
