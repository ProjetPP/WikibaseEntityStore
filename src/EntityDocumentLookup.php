<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityDocumentLookup {

	/**
	 * @param EntityId $entityId
	 * @return EntityDocument
	 * @throws EntityNotFoundException
	 */
	public function getEntityDocumentForId( EntityId $entityId );
}
