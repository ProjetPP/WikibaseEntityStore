<?php

namespace Wikibase\EntityStore\MongoDB;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityDatabase
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityDatabaseTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'findOne' )
			->with( $this->equalTo( array( '_id' => 'Q1' ) ) )
			->willReturn( array( 'id' => 'Q1' ) );

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityForDocument' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( $item );

		$entityStore = new MongoDBEntityDatabase( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			$item,
			$entityStore->getEntityDocumentForId( new ItemId( 'Q1' ) )
		);
	}

	public function testGetEntityDocumentForIdWithException() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'findOne' )
			->with( $this->equalTo( array( '_id' => 'Q1' ) ) )
			->willReturn( null );

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$entityStore = new MongoDBEntityDatabase( $databaseMock, $documentBuilderMock );

		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException' );
		$entityStore->getEntityDocumentForId( new ItemId( 'Q1' ) );
	}

	public function testGetEntityDocumentsForIds() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( array( '_id' => array( '$in' => array( 'Q1', 'Q2' ) ) ) ) )
			->willReturn( array(
				array( 'id' => 'Q1' )
			) );

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityForDocument' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( $item );

		$entityStore = new MongoDBEntityDatabase( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			array( $item ),
			$entityStore->getEntityDocumentsForIds( array( new ItemId( 'Q1' ), new ItemId( 'Q2' ) ) )
		);
	}

	public function testSaveEntityDocument() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'upsert' )
			->with(
				$this->equalTo( array( '_id' => 'Q1' ) ),
				$this->equalTo( array( 'id' => 'Q1' ) )
			);

		$databaseMock = $this->getMockBuilder( 'Doctrine\MongoDB\Database' )
			->disableOriginalConstructor()
			->getMock();
		$databaseMock->expects( $this->once() )
			->method( 'selectCollection' )
			->with( $this->equalTo( 'item' ) )
			->willReturn( $collectionMock );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildDocumentForEntity' )
			->with( $this->equalTo( $item ) )
			->willReturn( array( 'id' => 'Q1' ) );

		$entityStore = new MongoDBEntityDatabase( $databaseMock, $documentBuilderMock );

		$entityStore->saveEntityDocument( $item );
	}
}
