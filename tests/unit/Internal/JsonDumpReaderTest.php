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

		return new JsonDumpReader(__DIR__ . '/../../data/' . $fileName, $serialization->newEntityDeserializer());
	}

	/**
	 * @dataProvider iteratorProvider
	 */
	public function testIterator( $fileName, $expectedEntities, $withWarning = false ) {
		if( $withWarning ) {
			$this->setExpectedException( 'PHPUnit_Framework_Error_Notice' );
		}
		$entityIds = array();

		foreach( $this->getReader( $fileName ) as $entity ) {
			$entityIds[] = $entity->getId()->getSerialization();
		}

		$this->assertEquals( $expectedEntities, $entityIds );
	}

	public function iteratorProvider() {
		return array(
			array(
				'valid.json',
				array( 'Q1', 'P16', 'P22' )
			),
			array(
				'invalid.json',
				array( 'Q1', 'P16', 'P22' ),
				true
			)
		);
	}
}
