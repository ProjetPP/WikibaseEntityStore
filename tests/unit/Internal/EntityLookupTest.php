<?php

namespace Wikibase\EntityStore\Internal;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\EntityNotFoundException;

/**
 * @covers Wikibase\EntityStore\Internal\EntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q1' ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentForId' )
			->with( $this->equalTo( new ItemId( 'Q1' ) ) )
			->willReturn( $item );

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
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
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException' );
		$entityLookup->getEntityDocumentForId( new ItemId( 'Q1' ) );
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

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
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
			->willThrowException( new EntityNotFoundException( new ItemId( 'Q1' ) ) );

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\DataModel\Entity\ItemNotFoundException' );
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

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
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
			->willThrowException( new EntityNotFoundException( new PropertyId( 'P1' ) ) );

		$entityLookup = new EntityLookup( $entityDocumentLookupMock );
		$this->setExpectedException( 'Wikibase\DataModel\Entity\PropertyNotFoundException' );
		$entityLookup->getPropertyForId( new PropertyId( 'P1' ) );
	}
}
