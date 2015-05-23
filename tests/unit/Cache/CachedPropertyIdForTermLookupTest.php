<?php

namespace Wikibase\EntityStore\Cache;

use OutOfBoundsException;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\EntityStore\Cache\CachedPropertyIdForTermLookup
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CachedPropertyIdForTermLookupTest extends \PHPUnit_Framework_TestCase {

	public function testGetPropertysForTermWithCacheHit() {
		$propertyIds = [ new PropertyId( 'P1' ) ];

		$propertyForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\PropertyIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();

		$entityIdForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'property' ) )
			->willReturn( $propertyIds );

		$propertyIdForTermLookup = new CachedPropertyIdForTermLookup( $propertyForTermLookupMock, $entityIdForTermCacheMock );
		$this->assertEquals(
			$propertyIds,
			$propertyIdForTermLookup->getPropertyIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}

	public function testGetPropertysForTermWithCacheMiss() {
		$propertyIds = [ new PropertyId( 'P1' ) ];

		$propertyIdForTermLookupMock = $this->getMockBuilder( 'Wikibase\EntityStore\PropertyIdForTermLookup' )
			->disableOriginalConstructor()
			->getMock();
		$propertyIdForTermLookupMock->expects( $this->once() )
			->method( 'getPropertyIdsForTerm' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ) )
			->willReturn( $propertyIds );

		$entityIdForTermCacheMock = $this->getMockBuilder( 'Wikibase\EntityStore\Cache\EntityIdForTermCache' )
			->disableOriginalConstructor()
			->getMock();
		$entityIdForTermCacheMock->expects( $this->once() )
			->method( 'fetch' )
			->with( $this->equalTo( new Term( 'en', 'foo' ) ), $this->equalTo( 'property' ) )
			->willThrowException( new OutOfBoundsException() );

		$propertyIdForTermLookup = new CachedPropertyIdForTermLookup( $propertyIdForTermLookupMock, $entityIdForTermCacheMock );
		$this->assertEquals(
			$propertyIds,
			$propertyIdForTermLookup->getPropertyIdsForTerm( new Term( 'en', 'foo' ) )
		);
	}
}
