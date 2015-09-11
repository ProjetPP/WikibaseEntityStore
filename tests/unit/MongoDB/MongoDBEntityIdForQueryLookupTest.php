<?php

namespace Wikibase\EntityStore\MongoDB;

use ArrayIterator;
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
use MongoRegex;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\FeatureNotSupportedException;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityIdForQueryLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityIdForQueryLookupTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getEntityIdsForQueryProvider
	 */
	public function testGetEntityIdsForQuery(
		Description $queryDescription,
		QueryOptions $queryOptions,
		$type = null,
		$mongoQuery,
		$skip,
		$limit,
		$mongoResult
	) {
		$cursorMock = $this->getMockBuilder( 'Doctrine\MongoDB\Cursor' )
			->disableOriginalConstructor()
			->getMock();
		$cursorMock->expects( $this->once() )
			->method( 'skip' )
			->with( $this->equalTo( $skip ) )
			->willReturn( $cursorMock );
		$cursorMock->expects( $this->once() )
			->method( 'limit' )
			->with( $this->equalTo( $limit ) )
				->willReturn( new ArrayIterator( $mongoResult ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( $mongoQuery ) )
			->willReturn( $cursorMock );

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( $type ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityIdForDocument' )
			->with( $this->equalTo( [ '_id' => 'Q1' ] ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->any() )
			->method( 'buildIntegerForType' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( 0 );
		$documentBuilderMock->expects( $this->any() )
			->method( 'buildSearchedStringValue' )
			->with( $this->equalTo( 'foo' ) )
			->willReturn( 'foo' );

		$lookup = new MongoDBEntityIdForQueryLookup( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$lookup->getEntityIdsForQuery( $queryDescription, $queryOptions, $type )
		);
	}

	public function getEntityIdsForQueryProvider() {
		return [
			[
				new AnyValue(),
				new QueryOptions( 10, 0 ),
				null,
				[],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P1' ) ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
				),
				new QueryOptions( 20, 10 ),
				Item::ENTITY_TYPE,
				[ 'sclaims.wikibase-entityid' => 'P1-Q1' ],
				10,
				20,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new Conjunction( [
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new StringValue( 'foo' ) )
					),
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P1' ) ),
						new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
					)
				] ),
				new QueryOptions( 10, 0 ),
				Item::ENTITY_TYPE,
				[
					'$and' => [
						[ 'sclaims.string' => 'P42-foo' ],
						[ 'sclaims.wikibase-entityid' => 'P1-P42' ]
					]
				],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new Disjunction( [
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new StringValue( 'foo' ) )
					),
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P1' ) ),
						new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
					)
				] ),
				new QueryOptions( 10, 0 ),
				Item::ENTITY_TYPE,
				[
					'$or' => [
						[ 'sclaims.string' => 'P42-foo' ],
						[ 'sclaims.wikibase-entityid' => 'P1-P42' ]
					]
				],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new Conjunction( [
						new Disjunction( [
							new ValueDescription( new StringValue( 'foo' ) )
						] ),
						new AnyValue(),
						new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
					] )
				),
				new QueryOptions( 10, 0 ),
				Item::ENTITY_TYPE,
				[
					'$and' => [
						[
							'$or' => [
								[ 'sclaims.string' => 'P42-foo' ]
							]
						],
						[],
						[ 'sclaims.wikibase-entityid' => 'P42-P42' ]
					]
				],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription(
						new TimeValue( '+1952-03-11T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_DAY, 'foo' )
					)
				),
				new QueryOptions( 10, 0 ),
				Item::ENTITY_TYPE,
				[ 'sclaims.time' => new MongoRegex( '/^P42\-\+1952\-03\-11/' ) ],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription(
						new TimeValue( '+1952-00-00T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_YEAR, 'foo' )
					)
				),
				new QueryOptions( 10, 0 ),
				Item::ENTITY_TYPE,
				[ 'sclaims.time' => new MongoRegex( '/^P42\-\+1952/' ) ],
				0,
				10,
				[ [ '_id' => 'Q1' ] ]
			],
		];
	}

	public function testGetEntityIdsForQueryWithoutLimits() {

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( [] ) )
			->willReturn( new ArrayIterator( [ [ '_id' => 'Q1' ] ] ) );

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( Item::ENTITY_TYPE ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityIdForDocument' )
			->with( $this->equalTo( [ '_id' => 'Q1' ] ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->any() )
			->method( 'buildIntegerForType' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( 0 );

		$lookup = new MongoDBEntityIdForQueryLookup( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$lookup->getEntityIdsForQuery( new AnyValue(), null, Item::ENTITY_TYPE )
		);
	}

	/**
	 * @dataProvider getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider
	 */
	public function testGetEntityIdsForQueryWithFeatureNotSupportedException( Description $queryDescription ) {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->any() )
			->method( 'buildIntegerForType' )
			->with( $this->equalTo( 'foo' ) )
			->willThrowException( new FeatureNotSupportedException() );

		$lookup = new MongoDBEntityIdForQueryLookup( $databaseMock, $documentBuilderMock );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$lookup->getEntityIdsForQuery( $queryDescription, null, 'item' );
	}

	public function getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider() {
		return [
			[
				$this->getMockForAbstractClass( 'Ask\Language\Description\Description' )
			],
			[
				new SomeProperty(
					new StringValue( 'foo' ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
				)
			],
			[
				new SomeProperty(
					new EntityIdValue( new ItemId( 'Q1' ) ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
				)
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) ),
						true
					)
				)
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ), ValueDescription::COMP_GREATER )
				)
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new MonolingualTextValue( 'en', 'Foo' ) )
				)
			],
			[
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P42' ) ),
					new ValueDescription( new EntityIdValue(
						$this->getMockForAbstractClass( 'Wikibase\DataModel\Entity\EntityId' )
					) )
				)
			],
		];
	}
}
