<?php

namespace Wikibase\EntityStore\MongoDB;

use ArrayIterator;
use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
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
	public function testGetEntityIdsForQuery( Query $query, $type = null, $mongoQuery, $skip, $limit, $mongoResult ) {
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
			->with( $this->equalTo( array( '_id' => 'Q1' ) ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->any() )
			->method( 'buildIntegerForType' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( 0 );

		$lookup = new MongoDBEntityIdForQueryLookup( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$lookup->getEntityIdsForQuery( $query, $type )
		);
	}

	public function getEntityIdsForQueryProvider() {
		return array(
			array(
				new Query(
					new AnyValue(),
					array(),
					new QueryOptions( 10, 0 )
				),
				null,
				array(),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
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
				Item::ENTITY_TYPE,
				array(
					'claims.P1' => array(
						'$elemMatch' => array( 'mainsnak.datavalue.value.numeric-id' => 1 )
					)
				),
				10,
				20,
				array( array( '_id' => 'Q1' ) )
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
							new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
						)
					) ),
					array(),
					new QueryOptions( 10, 0 )
				),
				Item::ENTITY_TYPE,
				array(
					'$and' => array(
						array(
							'claims.P42' => array(
								'$elemMatch' => array( 'mainsnak.datavalue.value' => 'foo' )
							)
						),
						array(
							'claims.P1' => array(
								'$elemMatch' => array( 'mainsnak.datavalue.value.numeric-id' => 42 )
							),
						)
					)
				),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
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
							new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
						)
					) ),
					array(),
					new QueryOptions( 10, 0 )
				),
				Item::ENTITY_TYPE,
				array(
					'$or' => array(
						array(
							'claims.P42' => array(
								'$elemMatch' => array( 'mainsnak.datavalue.value' => 'foo' )
							)
						),
						array(
							'claims.P1' => array(
								'$elemMatch' => array( 'mainsnak.datavalue.value.numeric-id' => 42 )
							),
						)
					)
				),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
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
							new ValueDescription( new EntityIdValue( new PropertyId( 'P42' ) ) )
						) )
					),
					array(),
					new QueryOptions( 10, 0 )
				),
				Item::ENTITY_TYPE,
				array(
					'claims.P42' => array(
						'$elemMatch' => array(
							'$and' => array(
								array(
									'$or' => array(
										array( 'mainsnak.datavalue.value' => 'foo' )
									)
								),
								array(),
								array( 'mainsnak.datavalue.value.numeric-id' => 42 )
							)
						)
					)
				),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription(
							new TimeValue( '+00000001952-03-11T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_DAY, '' )
						)
					),
					array(),
					new QueryOptions( 10, 0 )
				),
				Item::ENTITY_TYPE,
				array(
					'claims.P42' => array(
						'$elemMatch' => array(
							'mainsnak.datavalue.value.time' => new MongoRegex( '/^\+00000001952\-03\-11/' )
						)
					)
				),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription(
							new TimeValue( '+00000001952-00-00T00:00:00Z', 0, 0, 0, TimeValue::PRECISION_YEAR, '' )
						)
					),
					array(),
					new QueryOptions( 10, 0 )
				),
				Item::ENTITY_TYPE,
				array(
					'claims.P42' => array(
						'$elemMatch' => array(
							'mainsnak.datavalue.value.time' => new MongoRegex( '/^\+00000001952/' )
						)
					)
				),
				0,
				10,
				array( array( '_id' => 'Q1' ) )
			),
		);
	}

	/**
	 * @dataProvider getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider
	 */
	public function testGetEntityIdsForQueryWithFeatureNotSupportedException( Query $query ) {
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
		$lookup->getEntityIdsForQuery( $query, 'item' );
	}

	public function getEntityIdsForQueryWithFeatureNotSupportedExceptionProvider() {
		return array(
			array(
				new Query(
					$this->getMockForAbstractClass( 'Ask\Language\Description\Description' ),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
			array(
				new Query(
					new SomeProperty(
						new StringValue( 'foo' ),
						new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) )
					),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new SomeProperty(
							new EntityIdValue( new PropertyId( 'P42' ) ),
							new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ) ),
							true
						)
					),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new EntityIdValue( new ItemId( 'Q1' ) ), ValueDescription::COMP_GREATER )
					),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new MonolingualTextValue( 'en', 'Foo' ) )
					),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
			array(
				new Query(
					new SomeProperty(
						new EntityIdValue( new PropertyId( 'P42' ) ),
						new ValueDescription( new EntityIdValue(
							$this->getMockForAbstractClass( 'Wikibase\DataModel\Entity\EntityId' )
						) )
					),
					array(),
					new QueryOptions( 20, 10 )
				)
			),
		);
	}
}
