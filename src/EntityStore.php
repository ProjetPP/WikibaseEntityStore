<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\ItemLookup;
use Wikibase\DataModel\Entity\PropertyLookup;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
interface EntityStore {

	/**
	 * @return EntityDocumentLookup
	 */
	public function getEntityDocumentLookup();

	/**
	 * @return ItemLookup
	 */
	public function getItemLookup();

	/**
	 * @return PropertyLookup
	 */
	public function getPropertyLookup();

	/**
	 * @return EntityDocumentSaver
	 */
	public function getEntityDocumentSaver();
}
