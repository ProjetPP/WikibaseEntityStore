<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\MediawikiApi;
use Wikibase\Api\WikibaseFactory;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\Internal\EntityLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityStore implements EntityStore {

	/**
	 * @var ApiEntityLookup
	 */
	private $entityLookup;

	/**
	 * @param MediawikiApi $api
	 */
	public function __construct( MediawikiApi $api ) {
		$this->entityLookup = $this->newApiEntityLookup( $api );
	}

	private function newAPiEntityLookup( MediawikiApi $api ) {
		$factory = new WikibaseFactory( $api );
		return new ApiEntityLookup( $factory->newRevisionsGetter() );
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
		return new EntityLookup( $this->entityLookup );
	}

	/**
	 * @see EntityStore::getPropertyLookup
	 */
	public function getPropertyLookup() {
		return new EntityLookup( $this->entityLookup );
	}

	/**
	 * @see EntityStore::getEntityDocumentSaver
	 */
	public function getEntityDocumentSaver() {
		//TODO???
	}
}
