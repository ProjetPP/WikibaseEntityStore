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

	public function testGetEntityIdsForTerm() {
		$collectionMock = $this->getMockBuilder( 'Doctrine\MongoDB\Collection' )
			->disableOriginalConstructor()
			->getMock();
		$collectionMock->expects( $this->once() )
			->method( 'find' )
			->with( $this->equalTo( [
				'sterms.en' => 'foo'
			] ) )
			->willReturn( [ [ '_id' => 'Q1' ] ] );

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
			->method( 'buildEntityIdForDocument' )
			->with( $this->equalTo( [ '_id' => 'Q1' ] ) )
			->willReturn( new ItemId( 'Q1' ) );
		$documentBuilderMock->expects( $this->once() )
			->method( 'cleanTextForSearch' )
			->with( $this->equalTo( 'Foo' ) )
			->willReturn( 'foo' );

		$lookup = new MongoDBEntityIdForTermLookup( $databaseMock, $documentBuilderMock );

		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$lookup->getEntityIdsForTerm( new Term( 'en', 'Foo' ), 'item' )
		);
	}
}
