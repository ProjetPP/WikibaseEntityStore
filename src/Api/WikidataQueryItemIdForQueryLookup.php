<?php

namespace Wikibase\EntityStore\Api;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Description;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Query;
use DataValues\StringValue;
use InvalidArgumentException;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\FeatureNotSupportedException;
use Wikibase\EntityStore\ItemIdForQueryLookup;
use WikidataQueryApi\Query\AndQuery;
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
	public function getItemIdsForQuery( Query $query ) {
		return $this->queryService->doQuery( $this->buildQueryForDescription( $query->getDescription() ) );
	}

	private function buildQueryForDescription( Description $description, PropertyId $propertyId = null ) {
		if( $description instanceof AnyValue ) {
			return new ClaimQuery( $propertyId );
		} elseif( $description instanceof Conjunction ) {
			return $this->buildQueryForConjunction( $description, $propertyId );
		} elseif( $description instanceof Disjunction ) {
			return $this->buildQueryForDisjunction( $description, $propertyId );
		} elseif( $description instanceof SomeProperty ) {
			return $this->buildQueryForSomeProperty( $description );
		} elseif( $description instanceof ValueDescription ) {
			return $this->buildQueryForValueDescription( $description, $propertyId );
		} else {
			throw new InvalidArgumentException( 'Unknown description type: ' . $description->getType() );
		}
	}

	private function buildQueryForConjunction( Conjunction $conjunction, PropertyId $propertyId = null ) {
		$parameters = array();
		foreach( $conjunction->getDescriptions() as $description ) {
			$parameters[] = $this->buildQueryForDescription( $description, $propertyId );
		}
		return new AndQuery( $parameters );
	}

	private function buildQueryForDisjunction( Disjunction $disjunction, PropertyId $propertyId = null ) {
		$parameters = array();
		foreach( $disjunction->getDescriptions() as $description ) {
			$parameters[] = $this->buildQueryForDescription( $description, $propertyId );
		}
		return new OrQuery( $parameters );
	}

	private function buildQueryForSomeProperty( SomeProperty $someProperty ) {
		$propertyIdValue = $someProperty->getPropertyId();
		if( !( $propertyIdValue instanceof EntityIdValue ) ) {
			throw new InvalidArgumentException( 'PropertyId should be an EntityIdValue' );
		}
		$propertyId = $propertyIdValue->getEntityId();
		if( !( $propertyId instanceof PropertyId ) ) {
			throw new InvalidArgumentException( 'PropertyId should contain a PropertyId' );
		}

		if( $someProperty->isSubProperty() ) {
			throw new FeatureNotSupportedException( 'Sub-properties are not supported yet' );
		} else {
			return $this->buildQueryForDescription( $someProperty->getSubDescription(), $propertyId );
		}
	}

	private function buildQueryForValueDescription( ValueDescription $valueDescription, PropertyId $propertyId ) {
		if( !in_array(
			$valueDescription->getComparator(),
			array( ValueDescription::COMP_EQUAL, ValueDescription::COMP_LIKE )
		) ) {
			throw new FeatureNotSupportedException( 'Unsupported ValueDescription comparator' );
		}

		$dataValue = $valueDescription->getValue();
		if( $dataValue instanceof EntityIdValue ) {
			return $this->buildEntityIdValueForSearch( $dataValue, $propertyId );
		} elseif( $dataValue instanceof StringValue ) {
			return new StringQuery( $propertyId, $dataValue );
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
