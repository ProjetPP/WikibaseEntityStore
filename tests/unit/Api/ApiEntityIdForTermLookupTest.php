<?php

namespace Wikibase\EntityStore\Api;

use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Api\ApiEntityIdForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityIdForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityIdsForTerm() {
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

		$lookup = new ApiEntityIdForTermLookup( $mediawikiApiMock, new BasicEntityIdParser() );

		$this->assertEquals(
			array( new ItemId( 'Q1' ), new ItemId( 'Q2' ) ),
			$lookup->getEntityIdsForTerm( new Term( 'en', 'foo' ), 'item' )
		);
	}
}
