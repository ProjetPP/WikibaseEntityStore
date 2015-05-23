<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Database;
use Doctrine\MongoDB\Query\Expr;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\Internal\EntityIdForTermLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityIdForTermLookup implements EntityIdForTermLookup {

	/**
	 * @var Database
	 */
	private $database;

	/**
	 * @var MongoDBDocumentBuilder
	 */
	private $documentBuilder;

	/**
	 * @param Database $database
	 * @param MongoDBDocumentBuilder $documentBuilder
	 */
	public function __construct( Database $database, MongoDBDocumentBuilder $documentBuilder ) {
		$this->database = $database;
		$this->documentBuilder = $documentBuilder;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForTerm
	 */
	public function getEntityIdsForTerm( Term $term, $entityType ) {
		$documents = $this->database
			->selectCollection( $entityType )
			->find(
				$this->buildGetEntityIdForTermQuery( $term ),
				[ '_id' => 1 ]
			);

		$entities = [];

		foreach( $documents as $document ) {
			$entities[] = $this->documentBuilder->buildEntityIdForDocument( $document );
		}

		return $entities;
	}

	private function buildGetEntityIdForTermQuery( Term $term ) {
		$expr = new Expr();
		$expr->field( 'sterms.' . $term->getLanguageCode() )->equals(
			$this->documentBuilder->cleanTextForSearch( $term->getText() )
		);
		return $expr->getQuery();
	}
}
