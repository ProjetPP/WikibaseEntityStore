<?php

namespace Wikibase\EntityStore\DataModel;

use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\EntityStore\DataModel\SerializedEntity
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializedEntityTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorWithNoType() {
		$this->setExpectedException( 'InvalidArgumentException' );
		new SerializedEntity( null, [] );
	}

	public function testGetId() {
		$entity = new SerializedEntity( new ItemId( 'Q1' ), [ 'type' => 'item' ] );
		$this->assertEquals( new ItemId( 'Q1' ), $entity->getId() );
	}

	public function testGetNullId() {
		$entity = new SerializedEntity( null, [ 'type' => 'item' ] );
		$this->assertEquals( null, $entity->getId() );
	}

	public function testGetType() {
		$entity = new SerializedEntity( null, [ 'type' => 'foo' ] );
		$this->assertEquals( 'foo', $entity->getType() );
	}

	public function testIsEmptyYes() {
		$entity = new SerializedEntity( new ItemId( 'Q1' ), [ 'type' => 'item' ] );
		$this->assertTrue( $entity->isEmpty() );
	}

	public function testIsEmptyNo() {
		$entity = new SerializedEntity( null, [ 'type' => 'item', 'foo' => 'bar' ] );
		$this->assertFalse( $entity->isEmpty() );
	}

	public function testGetSerialization() {
		$entity = new SerializedEntity( null, [ 'type' => 'foo' ] );
		$this->assertEquals( [ 'type' => 'foo' ], $entity->getSerialization() );
	}
}
