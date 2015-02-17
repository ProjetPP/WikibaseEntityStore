<?php

namespace Wikibase\EntityStore\MongoDB;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\Conjunction;
use Ask\Language\Description\Description;
use Ask\Language\Description\Disjunction;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
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
use Wikibase\EntityStore\EntityIdForQueryLookup;
use Wikibase\EntityStore\FeatureNotSupportedException;

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
	 * @param Database $database
	 * @param MongoDBDocumentBuilder $documentBuilder
	 */
	public function __construct( Database $database, MongoDBDocumentBuilder $documentBuilder ) {
		$this->database = $database;
		$this->documentBuilder = $documentBuilder;
	}

	/**
	 * @see EntityForQueryLookup::getEntityIdsForQuery
	 */
	public function getEntityIdsForQuery( Query $query, $entityType = null ) {
		return $this->formatResults( $this->doQuery( $query, $entityType ) );
	}

	private function doQuery( Query $query, $entityType = null ) {
		$cursor = $this->database
			->selectCollection( $entityType )
			->find(
				$this->buildQueryForDescription( $query->getDescription(), $entityType ),
				array( '_id' => 1 )
			);

		return $this->applyOptionsToCursor( $cursor, $query->getOptions() );
	}

	private function buildQueryForDescription( Description $description, $entityType = null ) {
		$expr = $this->buildCoreQueryForDescription( $description, new Expr() );

		if( $entityType !== null ) {
			$expr->field( '_type' )->equals( $this->documentBuilder->buildIntegerForType( $entityType ) );
		}

		return $expr->getQuery();
	}

	private function buildCoreQueryForDescription( Description $description, Expr $expr ) {
		if( $description instanceof AnyValue ) {
			return $expr;
		} elseif( $description instanceof Conjunction ) {
			return $this->buildQueryForConjunction( $description, $expr );
		} elseif( $description instanceof Disjunction ) {
			return $this->buildQueryForDisjunction( $description, $expr );
		} elseif( $description instanceof SomeProperty ) {
			return $this->buildQueryForSomeProperty( $description, $expr );
		} elseif( $description instanceof ValueDescription ) {
			return $this->buildQueryForValueDescription( $description, $expr );
		} else {
			throw new FeatureNotSupportedException( 'Unknown description type: ' . $description->getType() );
		}
	}

	private function buildQueryForConjunction( Conjunction $conjunction, Expr $expr ) {
		foreach( $conjunction->getDescriptions() as $description ) {
			$expr->addAnd( $this->buildCoreQueryForDescription( $description, new Expr() ) );
		}
		return $expr;
	}

	private function buildQueryForDisjunction( Disjunction $disjunction, Expr $expr ) {
		foreach( $disjunction->getDescriptions() as $description ) {
			$expr->addOr( $this->buildCoreQueryForDescription( $description, new Expr() ) );
		}
		return $expr;
	}

	private function buildQueryForSomeProperty( SomeProperty $someProperty, Expr $expr ) {
		$propertyIdValue = $someProperty->getPropertyId();
		if( !( $propertyIdValue instanceof EntityIdValue ) ) {
			throw new FeatureNotSupportedException( 'PropertyId should be an EntityIdValue' );
		}

		$subQuery = $this->buildCoreQueryForDescription( $someProperty->getSubDescription(), new Expr() );

		if( $someProperty->isSubProperty() ) {
			throw new FeatureNotSupportedException( 'Sub-properties are not supported yet' );
		} else {
			return $expr->field( 'claims.' . $propertyIdValue->getEntityId()->getSerialization() )->elemMatch( $subQuery );
		}
	}

	private function buildQueryForValueDescription( ValueDescription $valueDescription, Expr $expr ) {
		$parameters = $this->buildDataValueForSearch( $valueDescription->getValue() );

		switch( $valueDescription->getComparator() ) {
			case ValueDescription::COMP_EQUAL:
			case ValueDescription::COMP_LIKE:
				foreach( $parameters as $key => $value ) {
					$expr->field( 'mainsnak.datavalue.' . $key )->equals( $value );
				}
				return $expr;

			default:
				throw new FeatureNotSupportedException( 'Unsupported ValueDescription comparator' );
		}
	}

	private function buildDataValueForSearch( DataValue $dataValue ) {
		if( $dataValue instanceof EntityIdValue ) {
			return $this->buildEntityIdValueForSearch( $dataValue );
		} elseif( $dataValue instanceof StringValue ) {
			return $this->buildStringValueForSearch( $dataValue );
		} elseif( $dataValue instanceof TimeValue ) {
			return $this->buildTimeValueForSearch( $dataValue );
		} else {
			throw new FeatureNotSupportedException( 'Not supported DataValue type: ' . $dataValue->getType() );
		}
	}

	private function buildEntityIdValueForSearch( EntityIdValue $entityIdValue ) {
		$entityId = $entityIdValue->getEntityId();

		if( !( $entityId instanceof ItemId || $entityId instanceof PropertyId ) ) {
			throw new FeatureNotSupportedException( 'Not supported entity type: ' . $entityId->getEntityType() );
		}
		return array(
			'value.numeric-id' => $entityId->getNumericId() //It assumes that the range of the property is only one entity type
		);
	}

	private function buildStringValueForSearch( StringValue $stringValue ) {
		return array(
			'value' => $stringValue->getValue()
		);
	}

	private function buildTimeValueForSearch( TimeValue $timeValue ) {
		$significantTimePart = preg_replace( '/(-00)*T00:00:00Z$/', '', $timeValue->getTime() );

		return array(
			'value.time' => new MongoRegex( '/^' . preg_quote( $significantTimePart, '/' ) . '/' )
		);
	}

	private function applyOptionsToCursor( Cursor $cursor, QueryOptions $options ) {
		return $cursor
			->skip( $options->getOffset() )
			->limit( $options->getLimit() );
	}

	private function formatResults( Iterator $cursor ) {
		$entityIds = array();

		foreach( $cursor as $document ) {
			$entityIds[] = $this->documentBuilder->buildEntityIdForDocument( $document );
		}

		return $entityIds;
	}
}
