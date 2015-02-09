<?php

namespace Wikibase\EntityStore\MongoDB;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Serializers\Serializer;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;

/**
 * Internal class
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBDocumentBuilder {

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
	 * @param Serializer $entitySerializer
	 * @param Deserializer $entityDeserializer
	 * @param EntityIdParser $entityIdParser
	 */
	public function __construct(
		Serializer $entitySerializer,
		Deserializer $entityDeserializer,
		EntityIdParser $entityIdParser
	) {
		$this->entitySerializer = $entitySerializer;
		$this->entityDeserializer = $entityDeserializer;
		$this->entityIdParser = $entityIdParser;
	}

	/**
	 * @param EntityDocument $entityDocument
	 * @return array
	 */
	public function buildDocumentForEntity( EntityDocument $entityDocument ) {
		return $this->addIndexedDataToSerialization(
			$this->entitySerializer->serialize( $entityDocument )
		);
	}

	private function addIndexedDataToSerialization( array $serialization ) {
		$serialization['searchterms'] = $this->buildSearchTermsForEntity( $serialization );

		return $serialization;
	}

	private function buildSearchTermsForEntity( array $serialization ) {
		$searchTerms = array();

		if( array_key_exists( 'labels', $serialization ) ) {
			foreach( $serialization['labels'] as $label ) {
				$searchTerms[] = $this->buildTermForSearch( $label['language'], $label['value'] );
			}
		}

		if( array_key_exists( 'aliases', $serialization ) ) {
			foreach( $serialization['aliases'] as $aliasGroup ) {
				foreach( $aliasGroup as $alias ) {
					$searchTerms[] = $this->buildTermForSearch( $alias['language'], $alias['value'] );
				}
			}
		}

		return $searchTerms;
	}

	/**
	 * @param string $languageCode
	 * @param string $text
	 * @return array
	 */
	public function buildTermForSearch( $languageCode, $text ) {
		$text = mb_strtolower( $text, 'UTF-8' ); //TODO: said to be very slow
		$text = str_replace( //TODO useful? + tests
			array( '\'', '-' ),
			array( ' ', ' ' ),
			$text
		);

		return array(
			'language' => $languageCode,
			'value' => trim( $text )
		);
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
		if( !array_key_exists( 'id', $document ) ) {
			throw new EntityIdParsingException();
		}

		return $this->entityIdParser->parse( $document['id'] );
	}
}
