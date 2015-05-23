<?php

namespace Wikibase\EntityStore\Internal;

/**
 * @covers Wikibase\EntityStore\Internal\JsonDumpReader
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class JsonDumpReaderTest extends \PHPUnit_Framework_TestCase {

	private function getReader( $fileName ) {
		$serialization = new EntitySerializationFactory();
		$logger = $this->getMock( 'Psr\Log\LoggerInterface' );

		return new JsonDumpReader(__DIR__ . '/../../data/' . $fileName, $serialization->newEntityDeserializer(), $logger );
	}

	/**
	 * @dataProvider iteratorProvider
	 */
	public function testIterator( $fileName, $expectedEntities ) {
		$entityIds = [];

		foreach( $this->getReader( $fileName ) as $entity ) {
			$entityIds[] = $entity->getId()->getSerialization();
		}

		$this->assertEquals( $expectedEntities, $entityIds );
	}

	public function iteratorProvider() {
		return [
			[
				'valid.json',
				[ 'Q1', 'P16', 'P22' ]
			],
			[
				'invalid.json',
				[ 'Q1', 'P16', 'P22' ]
			]
		];
	}
}
