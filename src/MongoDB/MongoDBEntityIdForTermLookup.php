<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Collection;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\EntityIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityIdForTermLookup implements EntityIdForTermLookup {

	/**
	 * @var Collection
	 */
	private $collection;

	/**
	 * @var MongoDBDocumentBuilder
	 */
	private $documentBuilder;

	/**
	 * @param Collection $collection
	 * @param MongoDBDocumentBuilder $documentBuilder
	 */
	public function __construct( Collection $collection, MongoDBDocumentBuilder $documentBuilder ) {
		$this->collection = $collection;
		$this->documentBuilder = $documentBuilder;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForTerm
	 */
	public function getEntityIdsForTerm( Term $term, $entityType = null ) {
		$documents = $this->collection->find(
			$this->buildGetEntityIdForTermQuery( $term, $entityType ),
			array( 'id' => 1 )
		);

		$entities = array();

		foreach( $documents as $document ) {
			$entities[] = $this->documentBuilder->buildEntityIdForDocument( $document );
		}

		return $entities;
	}

	private function buildGetEntityIdForTermQuery( Term $term, $entityType = null ) {
		$query = $this->collection->createQueryBuilder()
			->field( 'searchterms' )->equals( $this->documentBuilder->buildTermForSearch( $term ) );

		if( $entityType !== null ) {
			$query->field( 'type' )->equals( $entityType );
		}

		return $query->getQueryArray();
	}
}
