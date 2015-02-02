<?php

namespace Wikibase\EntityStore\Api;

use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Api\ApiEntityForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForTerm() {
		$item1 = new Item( new ItemId( 'Q1' ) );
		$item2 = new Item( new ItemId( 'Q2' ) );

		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$mediawikiApiMock->expects( $this->once() )
			->method( 'getAction' )
			->with( $this->equalTo( 'wbsearchentities' ), $this->equalTo( array(
				'search' => 'foo',
				'language' => 'en',
				'type' => 'item',
				'limit' => 50
			) ) )
			->will( $this->returnValue( array(
				'search' => array(
					array(
						'id' => 'Q1',
						'label' => 'foo',
						'aliases' => array( 'bar', 'baz' )
					),
					array(
						'id' => 'Q2',
						'label' => 'bar',
						'aliases' => array( 'baz', 'foo' )
					),
					array(
						'id' => 'Q3',
						'label' => 'bar',
						'aliases' => array( 'baz' )
					)
				)
			) ) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( array( new ItemId( 'Q1' ), new ItemId( 'Q2' ) ) ) )
			->willReturn( array( $item1, $item2 ) );

		$lookup = new ApiEntityForTermLookup( $mediawikiApiMock, new BasicEntityIdParser(), $entityDocumentLookupMock );

		$this->assertEquals( array( $item1, $item2 ), $lookup->getEntityDocumentsForTerm( new Term( 'en', 'foo' ), 'item' ) );
	}

	public function testGetEntityDocumentsForTermWithNoType() {
		$item = new Item( new ItemId( 'Q1' ) );
		$property = new Property( new PropertyId( 'P1' ), null, 'string' );

		$mediawikiApiMock = $this->getMockBuilder( 'Mediawiki\Api\MediawikiApi' )
			->disableOriginalConstructor()
			->getMock();
		$mediawikiApiMock->expects( $this->exactly( 2 ) )
			->method( 'getAction' )
			->with( $this->equalTo( 'wbsearchentities' ) )
			->will( $this->onConsecutiveCalls(
				array(
					'search' => array(
						array(
							'id' => 'Q1',
							'label' => 'foo',
							'aliases' => array( 'bar', 'baz' )
						),
					)
				),
				array(
					'search' => array(
						array(
							'id' => 'P1',
							'label' => 'bar',
							'aliases' => array( 'foo', 'baz' )
						),
					)
				)
			) );

		$entityDocumentLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\EntityDocumentLookup' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentLookupMock->expects( $this->once() )
			->method( 'getEntityDocumentsForIds' )
			->with( $this->equalTo( array( new ItemId( 'Q1' ), new PropertyId( 'P1' ) ) ) )
			->willReturn( array( $item, $property ) );

		$lookup = new ApiEntityForTermLookup( $mediawikiApiMock, new BasicEntityIdParser(), $entityDocumentLookupMock );

		$this->assertEquals( array( $item, $property ), $lookup->getEntityDocumentsForTerm( new Term( 'en', 'foo' ) ) );
	}
}
