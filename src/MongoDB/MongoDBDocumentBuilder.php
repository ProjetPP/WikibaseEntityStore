<?php

namespace Wikibase\EntityStore\MongoDB;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use MongoBinData;
use Serializers\Serializer;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\EntityStore\EntityStore;
use Wikibase\EntityStore\EntityStoreOptions;
use Wikibase\EntityStore\FeatureNotSupportedException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBDocumentBuilder {

	public static $SUPPORTED_ENTITY_TYPES = [
		Item::ENTITY_TYPE,
		Property::ENTITY_TYPE
	];

	public static $SUPPORTED_DATAVALUE_TYPES = [
		'string',
		'time',
		'wikibase-entityid'
	];

	/**
	 * @var Serializer
	 */
	private $entitySerializer;

	/**
	 * @var Deserializer
	 */
	private $entityDeserializer;

	/**
	 * @var EntityIdParser
	 */
	private $entityIdParser;

	/**
	 * @var EntityStoreOptions
	 */
	private $options;

	/**
	 * @param Serializer $entitySerializer
	 * @param Deserializer $entityDeserializer
	 * @param EntityIdParser $entityIdParser
	 * @param EntityStoreOptions $options
	 */
	public function __construct(
		Serializer $entitySerializer,
		Deserializer $entityDeserializer,
		EntityIdParser $entityIdParser,
		EntityStoreOptions $options
	) {
		$this->entitySerializer = $entitySerializer;
		$this->entityDeserializer = $entityDeserializer;
		$this->entityIdParser = $entityIdParser;
		$this->options = $options;
	}

	/**
	 * @param EntityDocument $entityDocument
	 * @return array
	 */
	public function buildDocumentForEntity( EntityDocument $entityDocument ) {
		return $this->addIndexedDataToSerialization(
			$this->filterLanguages( $this->entitySerializer->serialize( $entityDocument ) )
		);
	}

	private function addIndexedDataToSerialization( array $serialization ) {
		$serialization['_id'] = $serialization['id'];
		$serialization['sterms'] = $this->buildSearchTermsForEntity( $serialization );
		$serialization['sclaims'] = $this->buildSearchClaimsForEntity( $serialization );

		return $serialization;
	}

	private function filterLanguages( array $serialization ) {
		$languagesOption = $this->options->getOption( EntityStore::OPTION_LANGUAGES );

		if( $languagesOption === null ) {
			return $serialization;
		}

		$languages = array_flip( $languagesOption );
		if( array_key_exists( 'labels', $serialization ) ) {
			$serialization['labels'] = array_intersect_key( $serialization['labels'], $languages );
		}
		if( array_key_exists( 'descriptions', $serialization ) ) {
			$serialization['descriptions'] = array_intersect_key( $serialization['descriptions'], $languages );
		}
		if( array_key_exists( 'aliases', $serialization ) ) {
			$serialization['aliases'] = array_intersect_key( $serialization['aliases'], $languages );
		}

		return $serialization;
	}

	private function buildSearchTermsForEntity( array $serialization ) {
		$searchTerms = [];

		if( array_key_exists( 'labels', $serialization ) ) {
			foreach( $serialization['labels'] as $label ) {
				$searchTerms[$label['language']][] = $this->cleanTextForSearch( $label['value'] );
			}
		}

		if( array_key_exists( 'aliases', $serialization ) ) {
			foreach( $serialization['aliases'] as $aliasGroup ) {
				foreach( $aliasGroup as $alias ) {
					$searchTerms[$alias['language']][] = $this->cleanTextForSearch( $alias['value'] );
				}
			}
		}

		return $searchTerms;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function cleanTextForSearch( $text ) {
		$text = mb_strtolower( $text, 'UTF-8' ); //TODO: said to be very slow
		$text = trim( $text );

		return new MongoBinData( md5( $text, true ), MongoBinData::GENERIC );
	}

	private function buildSearchClaimsForEntity( array $serialization ) {
		if( !array_key_exists( 'claims', $serialization ) ) {
			return [];
		}

		$searchClaims = [];

		foreach( $serialization['claims'] as $claimBag ) {
			foreach( $claimBag as $claim ) {
				$this->addSnakToSearchClaims( $claim['mainsnak'], $searchClaims );
			}
		}

		return $searchClaims;
	}

	private function addSnakToSearchClaims( array $snak, array &$searchClaims ) {
		if( $snak['snaktype'] !== 'value' ) {
			return;
		}

		$valueType = $snak['datavalue']['type'];
		if( !$this->isSupportedDataValueType( $valueType ) ) {
			return;
		}

		$searchClaims[$valueType][] = $snak['property'] . '-' . $this->buildSearchedDataValue( $snak['datavalue'] );
	}

	private function isSupportedDataValueType( $type ) {
		return in_array( $type, self::$SUPPORTED_DATAVALUE_TYPES );
	}

	private function buildSearchedDataValue( array $dataValue ) {
		$value = $dataValue['value'];

		switch( $dataValue['type'] ) {
			case 'string':
				return $this->buildSearchedStringValue( $value );
			case 'time':
				return $value['time'];
			case 'wikibase-entityid':
				return $this->buildSearchedEntityIdValue( $value );
			default:
				throw new FeatureNotSupportedException( 'Not supported DataValue type: ' . $dataValue['type'] );
		}
	}

	/**
	 * Does an hash if needed to keep string size lower than MongoDB index key limit
	 *
	 * @param string $value
	 * @return string
	 */
	public function buildSearchedStringValue( $value ) {
		if( strlen( $value ) <= 32 ) {
			return $value;
		}

		return md5( $value );
	}

	private function buildSearchedEntityIdValue( array $value ) {
		switch( $value['entity-type'] ) {
			case 'item':
				return 'Q' . $value['numeric-id'];
			case 'property':
				return 'P' . $value['numeric-id'];
			default:
				throw new FeatureNotSupportedException( 'Unknown entity type: ' . $value['entity-type'] );
		}
	}

	/**
	 * @param array $document
	 * @return EntityDocument|null
	 */
	public function buildEntityForDocument( array $document ) {
		try {
			return $this->entityDeserializer->deserialize( $document );
		} catch( DeserializationException $exception ) {
			return null;
		}
	}

	/**
	 * @param array $document
	 * @return EntityId
	 * @throws EntityIdParsingException
	 */
	public function buildEntityIdForDocument( array $document ) {
		if( !array_key_exists( '_id', $document ) ) {
			throw new EntityIdParsingException();
		}

		return $this->entityIdParser->parse( $document['_id'] );
	}
}
