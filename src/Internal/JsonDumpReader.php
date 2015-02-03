<?php

namespace Wikibase\EntityStore\Internal;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Iterator;
use Psr\Log\LoggerInterface;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * Internal class

 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class JsonDumpReader implements Iterator {

	/**
	 * @var resource
	 */
	private $fileStream;

	/**
	 * @var Deserializer
	 */
	private $entityDeserializer;

	/**
	 * @var EntityDocument|null
	 */
	private $currentEntity = null;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @param string $fileName
	 * @param Deserializer $entityDeserializer
	 * @param LoggerInterface $logger
	 */
	public function __construct( $fileName, Deserializer $entityDeserializer, LoggerInterface $logger ) {
		$this->fileStream = fopen( $fileName, 'r' );
		$this->entityDeserializer = $entityDeserializer;
		$this->logger = $logger;
	}

	public function __destruct() {
		fclose( $this->fileStream );
	}

	/**
	 * @see Iterator::current
	 */
	public function current() {
		return $this->currentEntity;
	}

	/**
	 * @see Iterator::next
	 */
	public function next() {
		$this->currentEntity = null;

		while( true ) {
			$line = fgets( $this->fileStream );

			if( $line === false ) {
				return;
			}

			$line = trim( $line, ", \n\t\r" );

			if( $line !== '' && $line[0] === '{' ) {
				$json = json_decode( $line, true );
				try {
					$this->currentEntity = $this->entityDeserializer->deserialize( $json );
					return;
				} catch( DeserializationException $e ) {
					$id = array_key_exists( 'id', $json ) ? $json['id'] : '';
					$this->logger->error( 'Deserialization of entity ' . $id . ' failed' );
				}
			}
		}
	}

	/**
	 * @see Iterator::key
	 */
	public function key() {
		if ( $this->currentEntity === null ) {
			return null;
		}

		return $this->currentEntity->getId()->getSerialization();
	}

	/**
	 * @see Iterator::valid
	 */
	public function valid() {
		return $this->currentEntity !== null;
	}

	/**
	 * @see Iterator::rewind
	 */
	public function rewind() {
		fseek( $this->fileStream, 0 );
		$this->next();
	}
}
