<?php

namespace Wikibase\EntityStore\MongoDB;

use Doctrine\MongoDB\Query\Builder;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\MongoDB\MongoDBEntityForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForTermWithoutType() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( array(
				'searchterms' => array( 'language' => 'en', 'value' => 'foo' )
			) ) )
			->willReturn( array( array( 'id' => 'Q1' ) ) );
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
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildTermForSearch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( array( 'language' => 'en', 'value' => 'foo' ) );

		$lookup = new MongoDBEntityForTermLookup( $collectionMock, $documentBuilderMock );

		$this->assertEquals(
			array( $item ),
			$lookup->getEntityDocumentsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetEntityDocumentsForTermWithType() {
		$item = new Item( new ItemId( 'Q1' ) );

		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( array(
				'searchterms' => array( 'language' => 'en', 'value' => 'foo' ),
				'type' => 'item'
			) ) )
			->willReturn( array( array( 'id' => 'Q1' ) ) );
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
		$documentBuilderMock->expects( $this->once() )
			->method( 'buildTermForSearch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( array( 'language' => 'en', 'value' => 'foo' ) );

		$lookup = new MongoDBEntityForTermLookup( $collectionMock, $documentBuilderMock );

		$this->assertEquals(
			array( $item ),
			$lookup->getEntityDocumentsForTerm( new Term( 'en', 'foo' ), 'item' )
		);
	}
}
