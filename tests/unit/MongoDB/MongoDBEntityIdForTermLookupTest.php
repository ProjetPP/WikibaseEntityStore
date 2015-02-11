<?php

namespace Wikibase\EntityStore\MongoDB;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityIdForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityIdForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityIdsForTermWithoutType() {
		$cursorMock = $this->getMockBuilder( 'Doctrine\MongoDB\Cursor' )
			->disableOriginalConstructor()
			->getMock();
		$cursorMock->expects( $this->once() )
			->method( 'sort' )
			->with( $this->equalTo( array( '_id' => 1 ) ) )
			->willReturn( array( array( '_id' => 'Q1' ) ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( array(
				'sterms.en' => 'foo'
			) ) )
			->willReturn( $cursorMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityIdForDocument' )
			->with( $this->equalTo( array( '_id' => 'Q1' ) ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->once() )
			->method( 'cleanTextForSearch' )
			->with( $this->equalTo( 'Foo' ) )
			->willReturn( 'foo' );

		$lookup = new MongoDBEntityIdForTermLookup( $collectionMock, $documentBuilderMock );

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$lookup->getEntityIdsForTerm( new Term( 'en', 'Foo' ) )
		);
	}

	public function testGetEntityIdsForTermWithType() {
		$cursorMock = $this->getMockBuilder( 'Doctrine\MongoDB\Cursor' )
			->disableOriginalConstructor()
			->getMock();
		$cursorMock->expects( $this->once() )
			->method( 'sort' )
			->with( $this->equalTo( array( '_id' => 1 ) ) )
			->willReturn( array( array( '_id' => 'Q1' ) ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( array(
				'sterms.en' => 'foo',
				'_type' => 0
			) ) )
			->willReturn( $cursorMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityIdForDocument' )
			->with( $this->equalTo( array( '_id' => 'Q1' ) ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->once() )
			->method( 'cleanTextForSearch' )
			->with( $this->equalTo( 'Foo' ) )
			->willReturn( 'foo' );

		$lookup = new MongoDBEntityIdForTermLookup( $collectionMock, $documentBuilderMock );

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$lookup->getEntityIdsForTerm( new Term( 'en', 'Foo' ), 'item' )
		);
	}
}
