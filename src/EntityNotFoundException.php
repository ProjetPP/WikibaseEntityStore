<?php

namespace Wikibase\EntityStore;

use Exception;
use RuntimeException;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class EntityNotFoundException extends RuntimeException {

	/**
	 * @var EntityId
	 */
	private $entityId;

	/**
	 * @param EntityId $entityId
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct( EntityId $entityId, $message = null, Exception $previous = null ) {
		$this->entityId = $entityId;

		parent::__construct(
			$message ?: 'Entity not found: ' . $entityId,
			0,
			$previous
		);
	}

	/**
	 * @return EntityId
	 */
	public function getEntityId() {
		return $this->entityId;
	}
}
