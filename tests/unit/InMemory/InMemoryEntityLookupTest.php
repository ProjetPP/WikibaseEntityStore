<?php

namespace Wikibase\EntityStore\InMemory;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\ItemContent;

/**
 * @covers Wikibase\EntityStore\InMemory\InMemoryEntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class InMemoryEntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForIds() {
		$item = new Item( new ItemId( 'Q42' ) );

		$lookup = new InMemoryEntityLookup( array( $item ) );

		$this->assertEquals(
			array( $item ),
			$lookup->getEntityDocumentsForIds( array( new ItemId( 'Q42' ), new ItemId( 'Q43' ) ) )
		);
	}

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q42' ) );

		$lookup = new InMemoryEntityLookup( array( $item ) );

		$this->assertEquals( $item, $lookup->getEntityDocumentForId( new ItemId( 'Q42' ) ) );
	}

	public function testGetEntityDocumentWithException() {
		$lookup = new InMemoryEntityLookup( array() );

		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException');
		$lookup->getEntityDocumentForId( new ItemId( 'Q42' ) );
	}
}
