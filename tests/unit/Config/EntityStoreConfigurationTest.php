<?php

namespace Wikibase\EntityStore\Config;

use Symfony\Component\Config\Definition\Processor;

/**
 * @covers Wikibase\EntityStore\Config\EntityStoreConfiguration
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityStoreConfigurationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider validConfigurationProvider
	 */
	public function testValidConfiguration( array $initialConfig, array $processedConfig ) {
		$processor = new Processor();
		$configuration = new EntityStoreConfiguration();

		$this->assertEquals(
			$processedConfig,
			$processor->processConfiguration( $configuration, array( $initialConfig ) )
		);
	}

	public function validConfigurationProvider() {
		return array(
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					)
				),
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					)
				)
			),
			array(
				array(
					'backend' => 'mongodb',
					'mongodb' => array(
						'server' => ''
					)
				),
				array(
					'backend' => 'mongodb',
					'mongodb' => array(
						'server' => '',
						'database' => 'wikibase'
					)
				)
			),
		);
	}

	/**
	 * @dataProvider invalidConfigurationProvider
	 */
	public function testInvalidConfiguration( array $initialConfig ) {
		$processor = new Processor();
		$configuration = new EntityStoreConfiguration();

		$this->setExpectedException( 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException' );
		$processor->processConfiguration( $configuration, array( $initialConfig ) );
	}

	public function invalidConfigurationProvider() {
		return array(
			array(
				array()
			),
			array(
				array(
					'backend' => 'foo'
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => ''
					)
				)
			),
			array(
				array(
					'backend' => 'mongodb',
					'mongodb' => array()
				)
			)
		);
	}
}
