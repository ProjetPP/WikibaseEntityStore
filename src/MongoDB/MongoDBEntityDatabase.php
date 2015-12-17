<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Database;
use Doctrine\MongoDB\Query\Expr;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityDocumentSaver;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityDatabase implements EntityDocumentLookup, EntityDocumentSaver {

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
	 * @see EntityDocumentLookup::getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		$document = $this->database
			->selectCollection( $entityId->getEntityType() )
			->findOne( $this->buildGetEntityForIdQuery( $entityId ) );

		return ( $document === null ) ? null : $this->documentBuilder->buildEntityForDocument( $document );
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$entities = [];

		foreach( $this->splitEntityIdsPerType( $entityIds ) as $type => $entityIdsForType ) {
			$entities = array_merge(
				$entities,
				$this->getEntitiesInCollection( $entityIdsForType, $type )
			);
		}

		return $entities;
	}

	private function splitEntityIdsPerType( array $entityIds ) {
		$entityIdsPerType = [];

		/** @var EntityId $entityId */
		foreach( $entityIds as $entityId ) {
			$entityIdsPerType[$entityId->getEntityType()][] = $entityId;
		}

		return $entityIdsPerType;
	}

	private function getEntitiesInCollection( array $entityIds, $collectionName ) {
		$documents = $this->database
			->selectCollection( $collectionName )
			->find( $this->buildGetEntitiesForIdsQuery( $entityIds ) );

		$entities = [];
		foreach( $documents as $document ) {
			$entities[] = $this->documentBuilder->buildEntityForDocument( $document );
		}
		return $entities;
	}

	/**
	 * @see EntityDocumentSaver::saveEntityDocument
	 */
	public function saveEntityDocument( EntityDocument $entityDocument ) {
		$this->database->selectCollection( $entityDocument->getType() )->upsert(
			$this->buildGetEntityForIdQuery( $entityDocument->getId() ),
			$this->documentBuilder->buildDocumentForEntity( $entityDocument )
		);
	}

	private function buildGetEntityForIdQuery( EntityId $entityId ) {
		$expr = new Expr();
		return $expr->field( '_id' )->equals( $entityId->getSerialization() )->getQuery();
	}

	private function buildGetEntitiesForIdsQuery( array $entityIds ) {
		$expr = new Expr();
		return $expr->field( '_id' )->in( $this->serializeEntityIds( $entityIds ) )->getQuery();
	}

	private function serializeEntityIds( array $entityIds ) {
		$serializations = [];

		/** @var EntityId $entityId */
		foreach( $entityIds as $entityId ) {
			$serializations[] = $entityId->getSerialization();
		}

		return $serializations;
	}
}
