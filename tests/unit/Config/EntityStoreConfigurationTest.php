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
					),
					'options' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery-url' => 'http://wdq.wmflabs.org/api'
					)
				),
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery_url' => 'http://wdq.wmflabs.org/api'
					),
					'options' => array()
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
					),
					'options' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'memcached' => true,
						'array' => true
					)
				),
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'lifetime' => 0,
						'memcached' => array(
							'enabled' => true,
							'host' => 'localhost',
							'port' => 11211
						),
						'array' => array(
							'enabled' => true
						)
					),
					'options' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'lifetime' => 30000,
						'memcached' => false,
						'array' => false
					)
				),
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'lifetime' => 30000,
						'memcached' => array(
							'enabled' => false,
							'host' => 'localhost',
							'port' => 11211
						),
						'array' => array(
							'enabled' => false
						)
					),
					'options' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'options' => array(
						'languages' => array( 'en', 'fr' )
					)
				),
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'options' => array(
						'languages' => array( 'en', 'fr' )
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
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery-url' => ''
					)
				)
			),
			array(
				array(
					'backend' => 'mongodb',
					'mongodb' => array()
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'memcached' => 'toto'
					)
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'lifetime' => 'tata'
					)
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'array' => 'tata'
					)
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'memcached' => array(
							'host' => array()
						)
					)
				)
			),
			array(
				array(
					'backend' => 'api',
					'api' => array(
						'url' => 'http://www.wikidata.org/w/api.php'
					),
					'cache' => array(
						'memcached' => array(
							'port' => 'foo'
						)
					)
				)
			),
		);
	}
}
