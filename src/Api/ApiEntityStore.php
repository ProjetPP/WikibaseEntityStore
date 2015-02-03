<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\WikibaseFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup;
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
	 * @var DispatchingEntityIdForTermLookup
	 */
	private $entityIdsForTermLookup;

	/**
	 * @param MediawikiApi $api
	 */
	public function __construct( MediawikiApi $api ) {
		$this->entityLookup = $this->newEntityLookup( $api );
		$this->entityIdsForTermLookup = $this->newEntityForTermLookup( $api );
	}

	private function newEntityLookup( MediawikiApi $api ) {
		$factory = new WikibaseFactory( $api );
		return new EntityLookup( new ApiEntityLookup( $factory->newRevisionsGetter() ) );
	}

	private function newEntityForTermLookup( MediawikiApi $api ) {
		return new DispatchingEntityIdForTermLookup( new ApiEntityIdForTermLookup( $api, new BasicEntityIdParser() ) );
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
	 * @see EntityStore::getItemIdForTermLookup
	 */
	public function getItemIdForTermLookup() {
		return $this->entityIdsForTermLookup;
	}

	/**
	 * @see EntityStore::getPropertyIdForTermLookup
	 */
	public function getPropertyIdForTermLookup() {
		return $this->entityIdsForTermLookup;
	}
}
