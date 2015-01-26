<?php

namespace Wikibase\EntityStore;

use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\EntityNotFoundException
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class EntityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor() {
		$itemId = new ItemId( 'Q42' );
		$exception = new EntityNotFoundException( $itemId );

		$this->assertEquals( $itemId, $exception->getEntityId() );
	}
}
