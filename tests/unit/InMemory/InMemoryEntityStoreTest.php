<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\EntityStore\EntityStoreTest;

/**
 * @covers Wikibase\EntityStore\InMemory\InMemoryEntityStore
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityStoreTest extends EntityStoreTest {

	public function testGetEntityDocumentLookup() {
		$store = new InMemoryEntityStore( array() );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$store = new InMemoryEntityStore( array() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$store = new InMemoryEntityStore( array() );

		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\PropertyLookup', $store->getPropertyLookup() );
	}
}
