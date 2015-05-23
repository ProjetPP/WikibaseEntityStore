<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\MediawikiApi;
use Mediawiki\Api\SimpleRequest;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\Internal\EntityIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 * @todo removes limit of 50 results?
 */
class ApiEntityIdForTermLookup implements EntityIdForTermLookup {

	/**
	 * @var MediawikiApi
	 */
	private $api;

	/**
	 * @var EntityIdParser
	 */
	private $entityIdParser;

	/**
	 * @param MediaWikiApi $api
	 * @param EntityIdParser $entityIdParser
	 */
	public function __construct( MediaWikiApi $api, EntityIdParser $entityIdParser ) {
		$this->api = $api;
		$this->entityIdParser = $entityIdParser;
	}

	/**
	 * @see EntityIdsForTermLookup::getEntityIdsForTerm
	 */
	public function getEntityIdsForTerm( Term $term, $entityType ) {
		return $this->parseResult( $this->api->getRequest( $this->buildRequest( $term, $entityType ) ), $term->getText() );
	}

	protected function buildRequest( Term $term, $entityType ) {
		return new SimpleRequest(
			'wbsearchentities',
			[
				'search' => $term->getText(),
				'language' => $term->getLanguageCode(),
				'type' => $entityType,
				'limit' => 50
			]
		);
	}

	private function parseResult( array $result, $search ) {
		$search = $this->cleanLabel( $search );

		$results = $this->filterResults( $result['search'], $search );

		$entityIds = [];
		foreach( $results as $entry ) {
			$entityIds[] = $this->entityIdParser->parse( $entry['id'] );
		}

		return $entityIds;
	}

	private function filterResults( array $results, $search ) {
		$filtered = [];
		foreach( $results as $entry ) {
			if( $this->doResultsMatch($entry, $search ) ) {
				$filtered[] = $entry;
			}
		}

		return $filtered;
	}

	private function doResultsMatch( array $entry, $search ) {
		if( array_key_exists( 'aliases', $entry ) ) {
			foreach( $entry['aliases'] as $alias ) {
				if( $this->cleanLabel( $alias ) === $search ) {
					return true;
				}
			}
		}

		return array_key_exists( 'label', $entry ) && $this->cleanLabel($entry['label']) === $search;
	}

	private function cleanLabel($label) {
		$label = mb_strtolower( $label, 'UTF-8' );
		return trim( $label );
	}
}
