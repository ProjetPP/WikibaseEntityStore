<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Internal\EntityForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForTerm() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( array( $item ) );

		$EntityForTermLookup = new EntityForTermLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			array( $item ),
			$EntityForTermLookup->getEntityDocumentsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetItemsForTerm() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( array( $item ) );

		$EntityForTermLookup = new EntityForTermLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			array( $item ),
			$EntityForTermLookup->getItemsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetPropertiesForTerm() {
		$property = new Property( new PropertyId( 'P1' ), null, 'string' );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( array( $property ) );

		$EntityForTermLookup = new EntityForTermLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			array( $property ),
			$EntityForTermLookup->getPropertiesForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
