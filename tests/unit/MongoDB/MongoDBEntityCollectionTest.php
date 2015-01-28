<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Query\Builder;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityCollection
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityCollectionTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'findOne' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( array( 'id' => 'Q1' ) );
		$collectionMock->expects( $this->once() )
			->method( 'createQueryBuilder' )
			->willReturn( new Builder( $collectionMock ) );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildEntityForDocument' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( $item );

		$entityStore = new MongoDBEntityCollection( $collectionMock, $documentBuilderMock );

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
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) )
			->willReturn( null );
		$collectionMock->expects( $this->once() )
			->method( 'createQueryBuilder' )
			->willReturn( new Builder( $collectionMock ) );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$entityStore = new MongoDBEntityCollection( $collectionMock, $documentBuilderMock );

		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException' );
		$entityStore->getEntityDocumentForId( new ItemId( 'Q1' ) );
	}

	public function testSaveEntityDocument() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'upsert' )
			->with( $this->equalTo( array( 'id' => 'Q1' ) ) );
		$collectionMock->expects( $this->once() )
			->method( 'createQueryBuilder' )
			->willReturn( new Builder( $collectionMock ) );

		$documentBuilderMock = $this->getMockBuilder( 'Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder' )
			->disableOriginalConstructor()
			->getMock();
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildDocumentForEntity' )
			->with( $this->equalTo( $item ) )
			->willReturn( array( 'id' => 'Q1' ) );

		$entityStore = new MongoDBEntityCollection( $collectionMock, $documentBuilderMock );

		$entityStore->saveEntityDocument( $item );
	}
}
