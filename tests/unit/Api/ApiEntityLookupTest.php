<?php

namespace Wikibase\EntityStore\Api;

use Mediawiki\DataModel\Revision;
use Mediawiki\DataModel\Revisions;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\ItemContent;

/**
 * @covers Wikibase\EntityStore\Api\ApiEntityLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ApiEntityLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityDocumentsForIds() {
		$item = new Item( new ItemId( 'Q42' ) );

		$revisionGetterMock = $this->getMockBuilder( 'Wikibase\Api\Service\RevisionsGetter' )
			->disableOriginalConstructor()
			->getMock();
		$revisionGetterMock->expects( $this->once() )
			->method( 'getRevisions' )
			->with( $this->equalTo( array( new ItemId( 'Q42' ), new ItemId( 'Q43' ) ) ) )
			->will( $this->returnValue( new Revisions( array( new Revision( new ItemContent( $item ) ) ) ) ) );

		$lookup = new ApiEntityLookup( $revisionGetterMock );

		$this->assertEquals(
			array( $item ),
			$lookup->getEntityDocumentsForIds( array( new ItemId( 'Q42' ), new ItemId( 'Q43' ) ) )
		);
	}

	public function testGetEntityDocumentsForIdsWithEmptyInput() {
		$revisionGetterMock = $this->getMockBuilder( 'Wikibase\Api\Service\RevisionsGetter' )
			->disableOriginalConstructor()
			->getMock();

		$lookup = new ApiEntityLookup( $revisionGetterMock );

		$this->assertEquals( array(), $lookup->getEntityDocumentsForIds( array() ) );
	}

	public function testGetEntityDocumentForId() {
		$item = new Item( new ItemId( 'Q42' ) );

		$revisionGetterMock = $this->getMockBuilder( 'Wikibase\Api\Service\RevisionsGetter' )
			->disableOriginalConstructor()
			->getMock();
		$revisionGetterMock->expects( $this->once() )
			->method( 'getRevisions' )
			->with( $this->equalTo( array( new ItemId( 'Q42' ) ) ) )
			->will( $this->returnValue( new Revisions( array( new Revision( new ItemContent( $item ) ) ) ) ) );

		$lookup = new ApiEntityLookup( $revisionGetterMock );

		$this->assertEquals( $item, $lookup->getEntityDocumentForId( new ItemId( 'Q42' ) ) );
	}

	public function testGetEntityDocumentWithException() {
		$revisionGetterMock = $this->getMockBuilder( 'Wikibase\Api\Service\RevisionsGetter' )
			->disableOriginalConstructor()
			->getMock();
		$revisionGetterMock->expects( $this->once() )
			->method( 'getRevisions' )
			->with( $this->equalTo( array( new ItemId( 'Q42' ) ) ) )
			->will( $this->returnValue( new Revisions( array() ) ) );

		$lookup = new ApiEntityLookup( $revisionGetterMock );

		$this->setExpectedException( 'Wikibase\EntityStore\EntityNotFoundException');
		$lookup->getEntityDocumentForId( new ItemId( 'Q42' ) );
	}
}
