<?php

namespace Wikibase\EntityStore\Api;

use Wikibase\Api\Service\RevisionsGetter;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\EntityStore\EntityDocumentLookup;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityLookup implements EntityDocumentLookup {

	/**
	 * @var RevisionsGetter
	 */
	private $revisionsGetter;

	/**
	 * @param RevisionsGetter $revisionGetter
	 */
	public function __construct( RevisionsGetter $revisionGetter ) {
		$this->revisionsGetter = $revisionGetter;
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentForId
	 */
	public function getEntityDocumentForId( EntityId $entityId ) {
		$entities = $this->getEntityDocumentsForIds( array( $entityId ) );

		if( empty( $entities ) ) {
			throw new EntityNotFoundException( $entityId );
		}

		return reset( $entities );
	}

	/**
	 * @see EntityDocumentLookup::getEntityDocumentsForIds
	 */
	public function getEntityDocumentsForIds( array $entityIds ) {
		$revisions = $this->revisionsGetter->getRevisions( $entityIds );

		$entities = array();

		foreach( $revisions->toArray() as $revision ) {
			$entities[] = $revision->getContent()->getNativeData();
		}

		return $entities;
	}
}
