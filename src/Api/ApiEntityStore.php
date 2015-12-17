<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\MediawikiApi;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\FeatureNotSupportedException;
use Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup;
use Wikibase\EntityStore\Internal\DispatchingEntityLookup;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;
use WikidataQueryApi\WikidataQueryApi;
use WikidataQueryApi\WikidataQueryFactory;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityStore extends EntityStore {

	/**
	 * @var DispatchingEntityLookup
	 */
	private $entityLookup;

	/**
	 * @var DispatchingEntityIdForTermLookup
	 */
	private $entityIdsForTermLookup;

	/**
	 * @var WikidataQueryApi|null
	 */
	private $wikidataQueryApi;

	/**
	 * @param MediawikiApi $api
	 * @param WikidataQueryApi|null $wikidataQueryApi
	 * @param EntityStoreOptions|null $options
	 */
	public function __construct(
		MediawikiApi $api,
		WikidataQueryApi $wikidataQueryApi = null,
		EntityStoreOptions $options = null
	) {
		parent::__construct( $options );

		$this->entityLookup = $this->newEntityLookup( $api );
		$this->entityIdsForTermLookup = $this->newEntityForTermLookup( $api );
		$this->wikidataQueryApi = $wikidataQueryApi;
	}

	private function newEntityLookup( MediawikiApi $api ) {
		$serializationFactory = new EntitySerializationFactory();
		return new DispatchingEntityLookup(
			new ApiEntityLookup( $api, $serializationFactory->newEntityDeserializer(), $this->getOptions() )
		);
	}

	private function newEntityForTermLookup( MediawikiApi $api ) {
		return new DispatchingEntityIdForTermLookup( new ApiEntityIdForTermLookup( $api, new BasicEntityIdParser() ) );
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

	/**
	 * @see EntityStore::getItemIdForQueryLookup
	 */
	public function getItemIdForQueryLookup() {
		if( $this->wikidataQueryApi === null ) {
			throw new FeatureNotSupportedException(
				'ItemIdForQueryLookup not supported: you should configure it to support WikidataQuery'
			);
		}

		$factory = new WikidataQueryFactory( $this->wikidataQueryApi );
		return new WikidataQueryItemIdForQueryLookup( $factory->newSimpleQueryService() );
	}
}
