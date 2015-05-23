<?php

namespace Wikibase\EntityStore;

/**
 * @covers Wikibase\EntityStore\EntityStoreOptions
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class EntityStoreOptionsTest extends \PHPUnit_Framework_TestCase {

	public function testGetOption() {
		$options = new EntityStoreOptions( [ 'foo' => 'bar' ] );
		$this->assertEquals(
			'bar',
			$options->getOption( 'foo' )
		);
	}

	public function testGetOptionWithException() {
		$options = new EntityStoreOptions();

		$this->setExpectedException( 'InvalidArgumentException' );
		$options->getOption( 'foo' );
	}

	public function testSetOption() {
		$options = new EntityStoreOptions();
		$options->setOption( 'foo', 'bar' );

		$this->assertEquals(
			'bar',
			$options->getOption( 'foo' )
		);
	}

	public function testHasOption() {
		$options = new EntityStoreOptions( [ 'foo' => 'bar' ] );

		$this->assertTrue( $options->hasOption( 'foo' ) );
		$this->assertFalse( $options->hasOption( 'bar' ) );
	}

	public function testDefaultOptionWithoutValue() {
		$options = new EntityStoreOptions();
		$options->defaultOption( 'foo', 'bar' );

		$this->assertEquals(
			'bar',
			$options->getOption( 'foo' )
		);
	}

	public function testDefaultOptionWithValue() {
		$options = new EntityStoreOptions( [ 'foo' => 'bar'] );
		$options->defaultOption( 'foo', 'baz' );

		$this->assertEquals(
			'bar',
			$options->getOption( 'foo' )
		);
	}
}
