<?php

namespace Wikibase\EntityStore\Cache;

use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @covers Wikibase\EntityStore\Cache\CachedPropertyLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetPropertyForIdWithCacheHit() {
		$property = new Property( new PropertyId( 'P1' ), null, 'string' );

		$propertyLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\PropertyLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( $property );

		$entityLookup = new CachedPropertyLookup( $propertyLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$property,
			$entityLookup->getPropertyForId( new PropertyId( 'P1' ) )
		);
	}

	public function testGetPropertyForIdWithCacheMiss() {
		$property = new Property( new PropertyId( 'P1' ), null, 'string' );

		$propertyLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\PropertyLookup' )
			->disableOriginalConstructor()
			->getMock();
		$propertyLookupMock->expects( $this->once() )
			->method( 'getPropertyForId' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( $property );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedPropertyLookup( $propertyLookupMock, $entityDocumentCacheMock );
		$this->assertEquals(
			$property,
			$entityLookup->getPropertyForId( new PropertyId( 'P1' ) )
		);
	}

	public function testGetPropertyForIdWithException() {
		$propertyLookupMock = $this->getMockBuilder( 'Wikibase\DataModel\Services\Lookup\PropertyLookup' )
			->disableOriginalConstructor()
			->getMock();
		$propertyLookupMock->expects( $this->once() )
			->method( 'getPropertyForId' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( null );

		$entityDocumentCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityDocumentCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityDocumentCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new PropertyId( 'P1' ) ) )
			->willReturn( null );

		$entityLookup = new CachedPropertyLookup( $propertyLookupMock, $entityDocumentCacheMock );

		$this->assertNull( $entityLookup->getPropertyForId( new PropertyId( 'P1' ) ) );
	}
}
