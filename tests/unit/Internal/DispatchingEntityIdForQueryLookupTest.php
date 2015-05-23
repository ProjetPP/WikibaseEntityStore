<?php

namespace Wikibase\EntityStore\Internal;

use Ask\Language\Description\AnyValue;
use Ask\Language\Option\QueryOptions;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @covers Wikibase\EntityStore\Internal\DispatchingEntityIdForQueryLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityIdForQueryLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityIdsForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with( $this->equalTo( new AnyValue() ), $this->equalTo( new QueryOptions( 10, 0 ) ) )
			->willReturn( [ new ItemId( 'Q1' ) ] );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$entityIdForQueryLookup->getEntityIdsForQuery( new AnyValue(), new QueryOptions( 10, 0 ) )
		);
	}

	public function testGetItemsForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with(
				$this->equalTo( new AnyValue() ),
				$this->equalTo( new QueryOptions( 10, 0 ) ),
				$this->equalTo( Item::ENTITY_TYPE )
			)
			->willReturn( [ new ItemId( 'Q1' ) ] );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$entityIdForQueryLookup->getItemIdsForQuery( new AnyValue(), new QueryOptions( 10, 0 ) )
		);
	}

	public function testGetPropertiesForQuery() {
		$entityIdForQueryLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\Internal\EntityIdForQueryLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForQueryLookupMock->expects( $this->once() )
			->method( 'getEntityIdsForQuery' )
			->with(
				$this->equalTo( new AnyValue() ),
				$this->equalTo( new QueryOptions( 10, 0 ) ),
				$this->equalTo( Property::ENTITY_TYPE )
			)
			->willReturn( [ new PropertyId( 'P1' ) ] );

		$entityIdForQueryLookup = new DispatchingEntityIdForQueryLookup( $entityIdForQueryLookupMock );
		$this->assertEquals(
			[ new PropertyId( 'P1' ) ],
			$entityIdForQueryLookup->getPropertyIdsForQuery( new AnyValue(), new QueryOptions( 10, 0 ) )
		);
	}
}
