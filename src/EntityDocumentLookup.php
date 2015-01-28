<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;

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
