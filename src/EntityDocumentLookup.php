<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Services\Lookup\EntityLookupException;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityDocumentLookup {

	/**
	 * @param EntityId $entityId
	 * @return EntityDocument|null
	 * @throws EntityLookupException
	 */
	public function getEntityDocumentForId( EntityId $entityId );

	/**
	 * @param EntityId[] $entityIds
	 * @return EntityDocument[]
	 * @throws EntityLookupException
	 */
	public function getEntityDocumentsForIds( array $entityIds );
}
