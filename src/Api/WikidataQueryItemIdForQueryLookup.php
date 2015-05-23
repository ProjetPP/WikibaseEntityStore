<?php

namespace Wikibase\EntityStore\Api;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Description;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\StringValue;
use DataValues\TimeValue;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\FeatureNotSupportedException;
use Wikibase\EntityStore\ItemIdForQueryLookup;
use WikidataQueryApi\Query\AndQuery;
use WikidataQueryApi\Query\BetweenQuery;
use WikidataQueryApi\Query\ClaimQuery;
use WikidataQueryApi\Query\OrQuery;
use WikidataQueryApi\Query\StringQuery;
use WikidataQueryApi\Services\SimpleQueryService;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikidataQueryItemIdForQueryLookup implements ItemIdForQueryLookup {

	/**
	 * @var SimpleQueryService
	 */
	private $queryService;

	/**
	 * @param SimpleQueryService $queryService
	 */
	public function __construct( SimpleQueryService $queryService ) {
		$this->queryService = $queryService;
	}

	/**
	 * @see ItemIdForQueryLookup::getItemIdsForQuery
	 */
	public function getItemIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null ) {
		return $this->queryService->doQuery( $this->buildQueryForDescription( $queryDescription ) );
	}

	private function buildQueryForDescription( Description $description, PropertyId $propertyId = null ) {
		if( $description instanceof AnyValue ) {
			return $this->buildQueryForAnyValue( $propertyId );
		} elseif( $description instanceof Conjunction ) {
			return $this->buildQueryForConjunction( $description, $propertyId );
		} elseif( $description instanceof Disjunction ) {
			return $this->buildQueryForDisjunction( $description, $propertyId );
		} elseif( $description instanceof SomeProperty ) {
			return $this->buildQueryForSomeProperty( $description );
		} elseif( $description instanceof ValueDescription ) {
			return $this->buildQueryForValueDescription( $description, $propertyId );
		} else {
			throw new FeatureNotSupportedException( 'Unknown description type: ' . $description->getType() );
		}
	}

	private function buildQueryForAnyValue( PropertyId $propertyId = null ) {
		if( $propertyId === null ) {
			throw new FeatureNotSupportedException( 'Search for all items is not supported' );
		}

		return new ClaimQuery( $propertyId );
	}

	private function buildQueryForConjunction( Conjunction $conjunction, PropertyId $propertyId = null ) {
		$parameters = [];
		foreach( $conjunction->getDescriptions() as $description ) {
			$parameters[] = $this->buildQueryForDescription( $description, $propertyId );
		}
		return new AndQuery( $parameters );
	}

	private function buildQueryForDisjunction( Disjunction $disjunction, PropertyId $propertyId = null ) {
		$parameters = [];
		foreach( $disjunction->getDescriptions() as $description ) {
			$parameters[] = $this->buildQueryForDescription( $description, $propertyId );
		}
		return new OrQuery( $parameters );
	}

	private function buildQueryForSomeProperty( SomeProperty $someProperty ) {
		$propertyIdValue = $someProperty->getPropertyId();
		if( !( $propertyIdValue instanceof EntityIdValue ) ) {
			throw new FeatureNotSupportedException( 'PropertyId should be an EntityIdValue' );
		}
		$propertyId = $propertyIdValue->getEntityId();
		if( !( $propertyId instanceof PropertyId ) ) {
			throw new FeatureNotSupportedException( 'PropertyId should contain a PropertyId' );
		}

		if( $someProperty->isSubProperty() ) {
			throw new FeatureNotSupportedException( 'Sub-properties are not supported yet' );
		} else {
			return $this->buildQueryForDescription( $someProperty->getSubDescription(), $propertyId );
		}
	}

	private function buildQueryForValueDescription( ValueDescription $valueDescription, PropertyId $propertyId = null ) {
		if( $propertyId === null ) {
			throw new FeatureNotSupportedException( 'Search of value on any properties is not supported' );
		}

		if( !in_array(
			$valueDescription->getComparator(),
			[ ValueDescription::COMP_EQUAL, ValueDescription::COMP_LIKE ]
		) ) {
			throw new FeatureNotSupportedException( 'Unsupported ValueDescription comparator' );
		}

		$dataValue = $valueDescription->getValue();
		if( $dataValue instanceof EntityIdValue ) {
			return $this->buildEntityIdValueForSearch( $dataValue, $propertyId );
		} elseif( $dataValue instanceof StringValue ) {
			return new StringQuery( $propertyId, $dataValue );
		} elseif( $dataValue instanceof TimeValue ) {
			return new BetweenQuery( $propertyId, $dataValue, $dataValue );
		} else {
			throw new FeatureNotSupportedException( 'Not supported DataValue type: ' . $dataValue->getType() );
		}
	}

	private function buildEntityIdValueForSearch( EntityIdValue $entityIdValue, PropertyId $propertyId ) {
		$entityId = $entityIdValue->getEntityId();

		if( !( $entityId instanceof ItemId ) ) {
			throw new FeatureNotSupportedException( 'Not supported entity type: ' . $entityId->getEntityType() );
		}

		return new ClaimQuery( $propertyId, $entityId );
	}
}
