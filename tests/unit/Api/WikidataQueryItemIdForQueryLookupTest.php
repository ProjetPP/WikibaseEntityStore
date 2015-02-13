<?php

namespace Wikibase\EntityStore\Api;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
use DataValues\StringValue;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use WikidataQueryApi\Query\AbstractQuery;
use WikidataQueryApi\Query\AndQuery;
use WikidataQueryApi\Query\ClaimQuery;
use WikidataQueryApi\Query\OrQuery;
use WikidataQueryApi\Query\StringQuery;

/**
 * @covers Wikibase\EntityStore\Api\WikidataQueryItemIdForQueryLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikidataQueryItemIdForQueryLookupTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getEntityIdsForQueryProvider
	 */
	public function testGetEntityIdsForQuery( Query $query, AbstractQuery $wikidataQueryQuery ) {
		$queryServiceMock = $this->getMockBuilder( 'WikidataQueryApi\Services\SimpleQueryService' )
			->disableOriginalConstructor()
			->getMock();
		$queryServiceMock->expects( $this->once() )
			->method( 'doQuery' )
			->with( $this->equalTo( $wikidataQueryQuery ) )
			->willReturn( array( new ItemId( 'Q1' ) ) );
		$lookup = new WikidataQueryItemIdForQueryLookup( $queryServiceMock );

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$lookup->getItemIdsForQuery( $query, 'item' )
		);
	}

	public function getEntityIdsForQueryProvider() {
		return array(
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P1' ) ),
						new AnyValue()
					),
					array(),
					new QueryOptions( 20, 10 )
				),
				new ClaimQuery( new PropertyId( 'P1' ) )
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P1' ) ),
						new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
					),
					array(),
					new QueryOptions( 20, 10 )
				),
				new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q1' ) )
			),
			array(
				new Query(
					new Conjunction( array(
						new SomeProperty(
							new EntityIdValue( new PropertyId( 'P42' ) ),
							new ValueDescription( new StringValue( 'foo' ) )
						),
						new SomeProperty(
							new EntityIdValue( new PropertyId( 'P1' ) ),
							new ValueDescription( new EntityIdValue( new ItemId( 'Q42' ) ) )
						)
					) ),
					array(),
					new QueryOptions( 10, 0 )
				),
				new AndQuery( array(
					new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) ),
					new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q42' ) )
				) )
			),
			array(
				new Query(
					new Disjunction( array(
						new SomeProperty(
							new EntityIdValue( new PropertyId( 'P42' ) ),
							new ValueDescription( new StringValue( 'foo' ) )
						),
						new SomeProperty(
							new EntityIdValue( new PropertyId( 'P1' ) ),
							new ValueDescription( new EntityIdValue( new ItemId( 'Q42' ) ) )
						)
					) ),
					array(),
					new QueryOptions( 10, 0 )
				),
				new OrQuery( array(
					new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) ),
					new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q42' ) )
				) )
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new Conjunction( array(
							new Disjunction( array(
								new ValueDescription( new StringValue( 'foo' ) )
							) ),
							new AnyValue(),
							new ValueDescription( new EntityIdValue( new ItemId( 'Q42' ) ) )
						) )
					),
					array(),
					new QueryOptions( 10, 0 )
				),
				new AndQuery( array(
					new OrQuery( array(
						new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) )
					) ),
					new ClaimQuery( new PropertyId( 'P42' ) ),
					new ClaimQuery( new PropertyId( 'P42' ), new ItemId( 'Q42' ) )
				) )
			),
		);
	}
}
