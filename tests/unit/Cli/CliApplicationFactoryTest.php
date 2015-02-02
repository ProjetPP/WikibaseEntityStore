<?php

namespace Wikibase\EntityStore\Console;

/**
 * @covers Wikibase\EntityStore\Console\CliApplicationFactory
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CliApplicationFactoryTest extends \PHPUnit_Framework_TestCase {

	public function testNewApplication() {
		$serialization = new CliApplicationFactory();
		$this->assertInstanceOf( 'Symfony\Component\Console\Application', $serialization->newApplication() );
	}
}
