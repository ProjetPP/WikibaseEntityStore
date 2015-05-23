<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Internal\DispatchingEntityIdForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityIdForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityIdsForTerm() {
		$entityIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( [ new ItemId( 'Q1' ) ] );

		$entityIdForTermLookup = new DispatchingEntityIdForTermLookup( $entityIdForTermLookupMock );
		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$entityIdForTermLookup->getEntityIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetItemsForTerm() {
		$entityIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( [ new ItemId( 'Q1' ) ] );

		$entityIdForTermLookup = new DispatchingEntityIdForTermLookup( $entityIdForTermLookupMock );
		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$entityIdForTermLookup->getItemIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetPropertiesForTerm() {
		$entityIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( [ new PropertyId( 'P1' ) ] );

		$entityIdForTermLookup = new DispatchingEntityIdForTermLookup( $entityIdForTermLookupMock );
		$this->assertEquals(
			[ new PropertyId( 'P1' ) ],
			$entityIdForTermLookup->getPropertyIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
