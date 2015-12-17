<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\DispatchingEntityLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityStore extends EntityStore {

	/**
	 * @var DispatchingEntityLookup
	 */
	private $entityLookup;

	/**
	 * @param EntityDocument[] $entities
	 */
	public function __construct( array $entities ) {
		parent::__construct();

		$this->entityLookup = new DispatchingEntityLookup( new InMemoryEntityLookup( $entities ) );
	}

	/**
	 * @see EntityStore::getEntityLookup
	 */
	public function getEntityLookup() {
		return $this->entityLookup;
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
