<?php

namespace Wikibase\EntityStore\DataModel;

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
	 * @see EntityDocument::getType
	 */
	public function getType() {
		return array_key_exists( 'type', $this->serialization ) ? $this->serialization['type'] : '';
	}

	/**
	 * @return array
	 */
	public function getSerialization() {
		return $this->serialization;
	}
}
