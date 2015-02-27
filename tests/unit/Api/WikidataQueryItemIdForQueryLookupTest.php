<?php

namespace Wikibase\EntityStore\Api;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Description;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\MonolingualTextValue;
use DataValues\StringValue;
use DataValues\TimeValue;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use WikidataQueryApi\Query\AbstractQuery;
use WikidataQueryApi\Query\AndQuery;
use WikidataQueryApi\Query\BetweenQuery;
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
	public function testGetEntityIdsForQuery( Description $queryDescription, AbstractQuery $wikidataQueryQuery ) {
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
			$lookup->getItemIdsForQuery( $queryDescription )
		);
	}

	public function getEntityIdsForQueryProvider() {
		return array(
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P1' ) ),
					new AnyValue()
				),
				new ClaimQuery( new PropertyId( 'P1' ) )
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P1' ) ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
				),
				new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q1' ) )
			),
			array(
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
				new AndQuery( array(
					new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) ),
					new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q42' ) )
				) )
			),
			array(
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
				new OrQuery( array(
					new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) ),
					new ClaimQuery( new PropertyId( 'P1' ), new ItemId( 'Q42' ) )
				) )
			),
			array(
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
				new AndQuery( array(
					new OrQuery( array(
						new StringQuery( new PropertyId( 'P42' ), new StringValue( 'foo' ) )
					) ),
					new ClaimQuery( new PropertyId( 'P42' ) ),
					new ClaimQuery( new PropertyId( 'P42' ), new ItemId( 'Q42' ) )
				) )
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription(
						new TimeValue( '+00000001952-03-11T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_DAY, '' )
					)
				),
				new BetweenQuery(
					new PropertyId( 'P42' ),
					new TimeValue( '+00000001952-03-11T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_DAY, '' ),
					new TimeValue( '+00000001952-03-11T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_DAY, '' )
				)
			),
		);
	}


	/**
	 * @dataProvider getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider
	 */
	public function testGetEntityIdsForQueryWithFeatureNotSupportedException( Description $queryDescription ) {
		$queryServiceMock = $this->getMockBuilder( 'WikidataQueryApi\Services\SimpleQueryService' )
			->disableOriginalConstructor()
			->getMock();
		$lookup = new WikidataQueryItemIdForQueryLookup( $queryServiceMock );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$lookup->getItemIdsForQuery( $queryDescription );
	}

	public function getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider() {
		return array(
			array(
				new AnyValue()
			),
			array(
				$this->getMockForAbstractClass( 'Ask\Language\Description\Description' )
			),
			array(
				new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) ),
			),
			array(
				new SomeProperty(
					new StringValue( 'foo' ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
				)
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) ),
						true
					)
				)
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ), ValueDescription::COMP_GREATER )
				)
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new MonolingualTextValue( 'en', 'Foo' ) )
				),
			),
			array(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new EntityIdValue(
						$this->getMockForAbstractClass( 'Wikibase\DataModel\Entity\EntityId' )
					) )
				)
			),
		);
	}
}
