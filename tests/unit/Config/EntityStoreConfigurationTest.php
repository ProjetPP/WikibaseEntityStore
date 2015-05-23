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
			$processor->processConfiguration( $configuration, [ $initialConfig ] )
		);
	}

	public function validConfigurationProvider() {
		return [
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					]
				],
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'options' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery-url' => 'http://wdq.wmflabs.org/api'
					]
				],
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery_url' => 'http://wdq.wmflabs.org/api'
					],
					'options' => []
				]
			],
			[
				[
					'backend' => 'mongodb',
					'mongodb' => [
						'server' => ''
					]
				],
				[
					'backend' => 'mongodb',
					'mongodb' => [
						'server' => '',
						'database' => 'wikibase'
					],
					'options' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'memcached' => true,
						'array' => true
					]
				],
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'lifetime' => 0,
						'memcached' => [
							'enabled' => true,
							'host' => 'localhost',
							'port' => 11211
						],
						'array' => [
							'enabled' => true
						]
					],
					'options' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'lifetime' => 30000,
						'memcached' => false,
						'array' => false
					]
				],
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'lifetime' => 30000,
						'memcached' => [
							'enabled' => false,
							'host' => 'localhost',
							'port' => 11211
						],
						'array' => [
							'enabled' => false
						]
					],
					'options' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'options' => [
						'languages' => [ 'en', 'fr' ]
					]
				],
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'options' => [
						'languages' => [ 'en', 'fr' ]
					]
				]
			],
		];
	}

	/**
	 * @dataProvider invalidConfigurationProvider
	 */
	public function testInvalidConfiguration( array $initialConfig ) {
		$processor = new Processor();
		$configuration = new EntityStoreConfiguration();

		$this->setExpectedException( 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException' );
		$processor->processConfiguration( $configuration, [ $initialConfig ] );
	}

	public function invalidConfigurationProvider() {
		return [
			[
				[]
			],
			[
				[
					'backend' => 'foo'
				]
			],
			[
				[
					'backend' => 'api',
					'api' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => ''
					]
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php',
						'wikidataquery-url' => ''
					]
				]
			],
			[
				[
					'backend' => 'mongodb',
					'mongodb' => []
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'memcached' => 'toto'
					]
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'lifetime' => 'tata'
					]
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'array' => 'tata'
					]
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'memcached' => [
							'host' => []
						]
					]
				]
			],
			[
				[
					'backend' => 'api',
					'api' => [
						'url' => 'http://www.wikidata.org/w/api.php'
					],
					'cache' => [
						'memcached' => [
							'port' => 'foo'
						]
					]
				]
			],
		];
	}
}
