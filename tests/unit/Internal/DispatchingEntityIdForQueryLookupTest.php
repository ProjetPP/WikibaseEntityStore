<?php

namespace Wikibase\EntityStore\Internal;

use Ask\Language\Description\AnyValue;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @covers Wikibase\EntityStore\Internal\DispatchingEntityIdForQueryLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityIdForQueryLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityIdsForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with( $this->equalTo( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) ) )
			->willReturn( array( new ItemId( 'Q1' ) ) );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$entityIdForQueryLookup->getEntityIdsForQuery( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) )
		);
	}

	public function testGetItemsForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with( $this->equalTo( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) ) )
			->willReturn( array( new ItemId( 'Q1' ) ) );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$entityIdForQueryLookup->getItemIdsForQuery( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) )
		);
	}

	public function testGetPropertiesForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with( $this->equalTo( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) ) )
			->willReturn( array( new PropertyId( 'P1' ) ) );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			array( new PropertyId( 'P1' ) ),
			$entityIdForQueryLookup->getPropertyIdsForQuery( new Query( new AnyValue(), array(), new QueryOptions( 10, 0 ) ) )
		);
	}
}
