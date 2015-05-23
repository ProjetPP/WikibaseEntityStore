<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\Api\SimpleRequest;
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
			->method( 'getRequest' )
			->with( $this->equalTo(
				new SimpleRequest(
					'wbsearchentities',
					[
						'search' => 'foo',
						'language' => 'en',
						'type' => 'item',
						'limit' => 50
					]
				)
			) )
			->will( $this->returnValue( [
				'search' => [
					[
						'id' => 'Q1',
						'label' => 'foo',
						'aliases' => [ 'bar', 'baz' ]
					],
					[
						'id' => 'Q2',
						'label' => 'bar',
						'aliases' => [ 'baz', 'foo' ]
					],
					[
						'id' => 'Q3',
						'label' => 'bar',
						'aliases' => [ 'baz' ]
					]
				]
			] ) );

		$lookup = new ApiEntityIdForTermLookup( $mediawikiApiMock, new BasicEntityIdParser() );

		$this->assertEquals(
			[ new ItemId( 'Q1' ), new ItemId( 'Q2' ) ],
			$lookup->getEntityIdsForTerm( new Term( 'en', 'foo' ), 'item' )
		);
	}
}
