<?php

namespace Wikibase\EntityStore;

/**
 * @covers Wikibase\EntityStore\EntityStore
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityStoreTest extends \PHPUnit_Framework_TestCase {

	public function testGetEntityLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getEntityLookup();
	}

	public function testGetEntityDocumentLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getEntityDocumentLookup();
	}

	public function testGetItemLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getItemLookup();
	}

	public function testGetPropertyLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getPropertyLookup();
	}

	public function testGetItemIdForTermLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getItemIdForTermLookup();
	}

	public function testGetPropertyIdForTermLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getPropertyIdForTermLookup();
	}

	public function testGetItemIdForQueryLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getItemIdForQueryLookup();
	}

	public function testGetPropertyIdForQueryLookup() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getPropertyIdForQueryLookup();
	}

	public function testGetEntityDocumentSaver() {
		$storeMock = $this->getMockForAbstractClass( 'Wikibase\EntityStore\EntityStore' );

		$this->setExpectedException( 'Wikibase\EntityStore\FeatureNotSupportedException');
		$storeMock->getEntityDocumentSaver();
	}
}
