<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\EntityLookupException;

/**
 * @covers Wikibase\EntityStore\Internal\DispatchingEntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class DispatchingEntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntity() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testHasEntity() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			$item,
			$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForIdWithException() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityLookupException( new ItemId( 'Q1' ) ) );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\DataModel\Services\Lookup\EntityLookupException' );
		$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) );
	}

	public function testGetEntityDocumentsForIds() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( [ new ItemId( 'Q1' ) ] ) )
			->willReturn( [ $item ] );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			[ $item ],
			$entityLookup->getEntityDocumentsForIds( [ new ItemId( 'Q1' ) ] )
		);
	}

	public function testGetItemForId() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			$item,
			$entityLookup->getItemForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetItemForIdWithException() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willThrowException( new EntityLookupException( new ItemId( 'Q1' ) ) );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\DataModel\Services\Lookup\ItemLookupException' );
		$entityLookup->getItemForId( new ItemId( 'Q1' ) );
	}

	public function testGetPropertyForId() {
		$property = new Property( new PropertyId( 'P1' ), null, 'string' );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( $property );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->assertEquals(
			$property,
			$entityLookup->getPropertyForId( new PropertyId( 'P1' ) )
		);
	}

	public function testGetPropertyForIdWithException() {
		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willThrowException( new EntityLookupException( new PropertyId( 'P1' ) ) );

		$entityLookup = new DispatchingEntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\DataModel\Services\Lookup\PropertyLookupException' );
		$entityLookup->getPropertyForId( new PropertyId( 'P1' ) );
	}
}
