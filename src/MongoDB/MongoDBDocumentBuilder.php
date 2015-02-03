<?php

namespace Wikibase\EntityStore\MongoDB;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Serializers\Serializer;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\FingerprintProvider;
use Wikibase\DataModel\Term\Term;

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
			$entityDocument,
			$this->entitySerializer->serialize( $entityDocument )
		);
	}

	private function addIndexedDataToSerialization( EntityDocument $entityDocument, $serialization ) {
		if( $entityDocument instanceof FingerprintProvider ) {
			$serialization['searchterms'] = $this->buildSearchTermsForFingerprint( $entityDocument->getFingerprint() );
		}

		return $serialization;
	}

	private function buildSearchTermsForFingerprint( Fingerprint $fingerprint ) {
		$searchTerms = array();

		/** @var Term $label */
		foreach( $fingerprint->getLabels() as $label ) {
			$searchTerms[] = $this->buildTermForSearch( $label );
		}

		/** @var AliasGroup $aliasGroup */
		foreach( $fingerprint->getAliasGroups() as $aliasGroup ) {
			foreach( $aliasGroup->getAliases() as $alias ) {
				$searchTerms[] = $this->buildTermForSearch( new Term( $aliasGroup->getLanguageCode(), $alias ) );
			}
		}

		return $searchTerms;
	}

	/**
	 * @param Term $term
	 * @return array
	 */
	public function buildTermForSearch( Term $term ) {
		$text = mb_strtolower( $term->getText(), 'UTF-8' ); //TODO: said to be very slow
		$text = str_replace( //TODO useful? + tests
			array( '\'', '-' ),
			array( ' ', ' ' ),
			$text
		);

		return array(
			'language' => $term->getLanguageCode(),
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
