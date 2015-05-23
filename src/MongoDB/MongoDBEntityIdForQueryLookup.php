<?php

namespace Wikibase\EntityStore\MongoDB;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Description;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\DataValue;
use DataValues\StringValue;
use DataValues\TimeValue;
use Doctrine\MongoDB\Cursor;
use Doctrine\MongoDB\Database;
use Doctrine\MongoDB\Query\Expr;
use Iterator;
use MongoRegex;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\EntityStore\FeatureNotSupportedException;
use Wikibase\EntityStore\Internal\EntityIdForQueryLookup;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBEntityIdForQueryLookup implements EntityIdForQueryLookup {

	/**
	 * @var Database
	 */
	private $database;

	/**
	 * @var MongoDBDocumentBuilder
	 */
	private $documentBuilder;

	/**
	 * @var int|null
	 */
	private $timeLimit;

	/**
	 * @param Database $database
	 * @param MongoDBDocumentBuilder $documentBuilder
	 * @param int|null $timeLimit
	 */
	public function __construct( Database $database, MongoDBDocumentBuilder $documentBuilder, $timeLimit = null ) {
		$this->database = $database;
		$this->documentBuilder = $documentBuilder;
		$this->timeLimit = $timeLimit;
	}

	/**
	 * @see EntityForQueryLookup::getEntityIdsForQuery
	 */
	public function getEntityIdsForQuery( Description $queryDescription, QueryOptions $queryOptions = null, $entityType ) {
		return $this->formatResults( $this->doQuery( $queryDescription, $queryOptions, $entityType ) );
	}

	private function doQuery( Description $queryDescription, QueryOptions $queryOptions = null, $entityType ) {
		$cursor = $this->database
			->selectCollection( $entityType )
			->find(
				$this->buildQueryForDescription( $queryDescription, new Expr() )->getQuery(),
				$this->buildQueryModifiers()
			);

		if( $queryOptions === null ) {
			return $cursor;
		}

		return $this->applyOptionsToCursor( $cursor, $queryOptions );
	}

	private function buildQueryForDescription( Description $description, Expr $expr, PropertyId $currentProperty = null ) {
		if( $description instanceof AnyValue ) {
			return $expr;
		} elseif( $description instanceof Conjunction ) {
			return $this->buildQueryForConjunction( $description, $expr, $currentProperty );
		} elseif( $description instanceof Disjunction ) {
			return $this->buildQueryForDisjunction( $description, $expr, $currentProperty );
		} elseif( $description instanceof SomeProperty ) {
			return $this->buildQueryForSomeProperty( $description, $expr );
		} elseif( $description instanceof ValueDescription ) {
			return $this->buildQueryForValueDescription( $description, $expr, $currentProperty );
		} else {
			throw new FeatureNotSupportedException( 'Unknown description type: ' . $description->getType() );
		}
	}

	private function buildQueryForConjunction( Conjunction $conjunction, Expr $expr, PropertyId $currentProperty = null ) {
		foreach( $conjunction->getDescriptions() as $description ) {
			$expr->addAnd( $this->buildQueryForDescription( $description, new Expr(), $currentProperty ) );
		}
		return $expr;
	}

	private function buildQueryForDisjunction( Disjunction $disjunction, Expr $expr, PropertyId $currentProperty = null ) {
		foreach( $disjunction->getDescriptions() as $description ) {
			$expr->addOr( $this->buildQueryForDescription( $description, new Expr(), $currentProperty ) );
		}
		return $expr;
	}

	private function buildQueryForSomeProperty( SomeProperty $someProperty, Expr $expr ) {
		if( $someProperty->isSubProperty() ) {
			throw new FeatureNotSupportedException( 'Sub-properties are not supported yet' );
		}

		$propertyIdValue = $someProperty->getPropertyId();
		if( !( $propertyIdValue instanceof EntityIdValue ) ) {
			throw new FeatureNotSupportedException( 'PropertyId should be an EntityIdValue' );
		}

		$propertyId = $propertyIdValue->getEntityId();
		if( !( $propertyId instanceof PropertyId ) ) {
			throw new FeatureNotSupportedException( 'PropertyId should be a PropertyId' );
		}

		return $this->buildQueryForDescription( $someProperty->getSubDescription(), $expr, $propertyId );
	}

	private function buildQueryForValueDescription(
		ValueDescription $valueDescription,
		Expr $expr,
		PropertyId $currentProperty = null
	) {
		$value = $valueDescription->getValue();

		switch( $valueDescription->getComparator() ) {
			case ValueDescription::COMP_EQUAL:
			case ValueDescription::COMP_LIKE:
				$expr->field( 'sclaims.' . $value->getType() )->equals( $this->buildPropertyValueForSearch( $currentProperty, $value ) );
				return $expr;

			default:
				throw new FeatureNotSupportedException( 'Unsupported ValueDescription comparator' );
		}
	}

	private function buildPropertyValueForSearch( PropertyId $propertyId, DataValue $dataValue ) {
		if( $dataValue instanceof EntityIdValue ) {
			return $this->buildEntityIdValueForSearch( $propertyId, $dataValue );
		} elseif( $dataValue instanceof StringValue ) {
			return $this->buildStringValueForSearch( $propertyId, $dataValue );
		} elseif( $dataValue instanceof TimeValue ) {
			return $this->buildTimeValueForSearch( $propertyId, $dataValue );
		} else {
			throw new FeatureNotSupportedException( 'Not supported DataValue type: ' . $dataValue->getType() );
		}
	}

	private function buildEntityIdValueForSearch( PropertyId $propertyId, EntityIdValue $entityIdValue ) {
		$entityId = $entityIdValue->getEntityId();

		if( !( $entityId instanceof ItemId || $entityId instanceof PropertyId ) ) {
			throw new FeatureNotSupportedException( 'Not supported entity type: ' . $entityId->getEntityType() );
		}

		return $propertyId->getSerialization() . '-' . $entityIdValue->getEntityId()->getSerialization();
	}

	private function buildStringValueForSearch( PropertyId $propertyId, StringValue $stringValue ) {
		return $propertyId->getSerialization() . '-' .
			$this->documentBuilder->buildSearchedStringValue( $stringValue->getValue() );
	}

	private function buildTimeValueForSearch( PropertyId $propertyId, TimeValue $timeValue ) {
		$significantTimePart = preg_replace( '/(-00)*T00:00:00Z$/', '', $timeValue->getTime() );

		return new MongoRegex( '/^' . preg_quote( $propertyId->getSerialization() . '-' . $significantTimePart, '/' ) . '/' );
	}

	private function buildQueryModifiers() {
		$modifiers = [ '_id' => 1 ];

		if( $this->timeLimit !== null ) {
			$modifiers['$maxTimeMS'] = $this->timeLimit;
		}

		return $modifiers;
	}

	private function applyOptionsToCursor( Cursor $cursor, QueryOptions $options ) {
		if( $this->timeLimit !== null ) {
			$cursor->timeout( $this->timeLimit );
		}

		return $cursor
			->skip( $options->getOffset() )
			->limit( $options->getLimit() );
	}

	private function formatResults( Iterator $cursor ) {
		$entityIds = [];

		foreach( $cursor as $document ) {
			$entityIds[] = $this->documentBuilder->buildEntityIdForDocument( $document );
		}

		return $entityIds;
	}
}
