<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityStore extends EntityStore {

	/**
	 * @var EntityLookup
	 */
	private $entityLookup;

	/**
	 * @param EntityDocument[] $entities
	 */
	public function __construct( array $entities ) {
		$this->entityLookup = new EntityLookup( new InMemoryEntityLookup( $entities ) );
	}

	/**
	 * @see EntityStore::getEntityDocumentLookup
	 */
	public function getEntityDocumentLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getItemLookup
	 */
	public function getItemLookup() {
		return $this->entityLookup;
	}

	/**
	 * @see EntityStore::getPropertyLookup
	 */
	public function getPropertyLookup() {
		return $this->entityLookup;
	}
}
