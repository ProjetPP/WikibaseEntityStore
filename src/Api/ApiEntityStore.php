<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\WikibaseFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityForTermLookup;
use Wikibase\EntityStore\Internal\EntityLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityStore extends EntityStore {

	/**
	 * @var EntityLookup
	 */
	private $entityLookup;

	/**
	 * @var EntityForTermLookup
	 */
	private $entityForTermLookup;

	/**
	 * @param MediawikiApi $api
	 */
	public function __construct( MediawikiApi $api ) {
		$this->entityLookup = $this->newEntityLookup( $api );
		$this->entityForTermLookup = $this->newEntityForTermLookup( $api );
	}

	private function newEntityLookup( MediawikiApi $api ) {
		$factory = new WikibaseFactory( $api );
		return new EntityLookup( new ApiEntityLookup( $factory->newRevisionsGetter() ) );
	}

	private function newEntityForTermLookup( MediawikiApi $api ) {
		return new EntityForTermLookup( new ApiEntityForTermLookup( $api, new BasicEntityIdParser(), $this->entityLookup ) );
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

	/**
	 * @see EntityStore::getItemForTermLookup
	 */
	public function getItemForTermLookup() {
		return $this->entityForTermLookup;
	}

	/**
	 * @see EntityStore::getPropertyForTermLookup
	 */
	public function getPropertyForTermLookup() {
		return $this->entityForTermLookup;
	}
}
