<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityDocumentSaver {

	/**
	 * @param EntityDocument $entityDocument
	 * @todo error handling
	 */
	public function saveEntityDocument( EntityDocument $entityDocument );
}
