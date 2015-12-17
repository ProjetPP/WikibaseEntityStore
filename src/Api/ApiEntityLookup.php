<?php

namespace Wikibase\EntityStore\Api;

use Deserializers\Deserializer;
use Mediawiki\Api\MediawikiApi;
use Mediawiki\Api\SimpleRequest;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 *
 * TODO: allow to retrieve more than 50 entities
 */
class ApiEntityLookup implements EntityDocumentLookup {

	/**
	 * @var MediawikiApi
	 */
	private $api;

	/**
	 * @var Deserializer
	 */
	private $deserializer;

	/**
	 * @var EntityStoreOptions
	 */
	private $options;
	/**
	 * @param MediawikiApi $api
	 * @param Deserializer $deserializer
	 * @param EntityStoreOptions $options
	 */
	public function __construct( MediawikiApi $api, Deserializer $deserializer, EntityStoreOptions $options ) {
		$this->api = $api;
		$this->deserializer = $deserializer;
		$this->options = $options;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		$entities = $this->getEntityDocumentsForIds( [ $entityId ] );
		return reset( $entities ) ?: null;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		if( empty( $entityIds ) ) {
			return [];
		}

		return $this->parseResponse( $this->api->getRequest( $this->buildRequest( $entityIds ) ) );
	}

	private function buildRequest( array $entityIds ) {
		$params = [
			'ids' => implode( '|', $this->serializeEntityIds( $entityIds ) )
		];

		if( $this->options->getOption( EntityStore::OPTION_LANGUAGE_FALLBACK ) ) {
			$params['languagefallback'] = true;
		}

		$languagesOption = $this->options->getOption( EntityStore::OPTION_LANGUAGES );
		if( $languagesOption !== null ) {
			$params['languages'] = implode( '|', $languagesOption );
		}

		return new SimpleRequest( 'wbgetentities', $params );
	}

	private function serializeEntityIds( array $entityIds ) {
		$serialization = [];

		/** @var EntityId $entityId */
		foreach( $entityIds as $entityId ) {
			$serialization[] = $entityId->getSerialization();
		}

		return $serialization;
	}

	private function parseResponse( array $response ) {
		$entities = [];

		foreach( $response['entities'] as $serializedEntity ) {
			$entities[] = $this->deserializer->deserialize( $serializedEntity );
		}

		return $entities;
	}
}
