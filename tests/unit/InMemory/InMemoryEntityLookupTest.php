<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\InMemory\InMemoryEntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForIds() {
		$item = new Item( new ItemId( 'Q42' ) );

		$lookup = new InMemoryEntityLookup( [ $item ] );

		$this->assertEquals(
			[ $item ],
			$lookup->getEntityDocumentsForIds( [ new ItemId( 'Q42' ), new ItemId( 'Q43' ) ] )
		);
	}

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q42' ) );

		$lookup = new InMemoryEntityLookup( [ $item ] );

		$this->assertEquals( $item, $lookup->getEntityDocumentForId( new ItemId( 'Q42' ) ) );
	}

	public function testGetEntityDocumentWithoutDocument() {
		$lookup = new InMemoryEntityLookup( [] );

		$this->assertNull( $lookup->getEntityDocumentForId( new ItemId( 'Q42' ) ) );
	}
}
