<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\CachedPropertyForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetPropertysForTermWithCacheHit() {
		$properties = array( new Property( new PropertyId( 'P1' ), null, 'string' ) );

		$propertyForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\PropertyForTermLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'property' ) )
			->willReturn( $properties );

		$propertyForTermLookup = new CachedPropertyForTermLookup( $propertyForTermLookupMock, $entityDocumentForTermCacheMock );
		$this->assertEquals(
			$properties,
			$propertyForTermLookup->getPropertiesForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetPropertysForTermWithCacheMiss() {
		$properties = array( new Property( new PropertyId( 'P1' ), null, 'string' ) );

		$propertyForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\PropertyForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$propertyForTermLookupMock->expects( $this->once() )
			->method( 'getPropertiesForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( $properties );

		$entityDocumentForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'property' ) )
			->willThrowException( new OutOfBoundsException() );

		$propertyForTermLookup = new CachedPropertyForTermLookup( $propertyForTermLookupMock, $entityDocumentForTermCacheMock );
		$this->assertEquals(
			$properties,
			$propertyForTermLookup->getPropertiesForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
