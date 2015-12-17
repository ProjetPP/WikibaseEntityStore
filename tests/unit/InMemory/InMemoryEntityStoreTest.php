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

	public function testGetEntityLookup() {
		$store = new InMemoryEntityStore( [] );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\EntityLookup', $store->getEntityLookup() );
	}

	public function testGetEntityDocumentLookup() {
		$store = new InMemoryEntityStore( [] );

		$this->assertInstanceOf( 'Wikibase\EntityStore\EntityDocumentLookup', $store->getEntityDocumentLookup() );
	}

	public function testGetItemLookup() {
		$store = new InMemoryEntityStore( [] );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\ItemLookup', $store->getItemLookup() );
	}

	public function testGetPropertyLookup() {
		$store = new InMemoryEntityStore( [] );

		$this->assertInstanceOf( 'Wikibase\DataModel\Services\Lookup\PropertyLookup', $store->getPropertyLookup() );
	}
}
