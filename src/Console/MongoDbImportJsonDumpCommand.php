<?php

namespace Wikibase\EntityStore\Console;

use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;
use Guzzle\Common\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\EntityStore\Internal\EntitySerializationFactory;
use Wikibase\EntityStore\Internal\JsonDumpReader;
use Wikibase\EntityStore\MongoDB\MongoDBEntityStore;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDbImportJsonDumpCommand extends Command {

	protected function configure() {
		$this->setName( 'mongodb:import-json-dump' )
			->setDescription( 'Import JSON dump in a MongoDb entity store' )
			->addArgument( 'file', InputArgument::REQUIRED, 'JSON dump file' )
			->addOption( 'server', 's', InputOption::VALUE_OPTIONAL,
				'MongoDb server name', '' )
			->addOption( 'database', 'db', InputOption::VALUE_OPTIONAL,
				'Name of the database that should store entities', 'wikibase' )
			->addOption( 'collection', 'c', InputOption::VALUE_OPTIONAL,
				'Name of the collection that should store entities', 'entity' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$connection = new Connection( $input->getOption( 'server' ) );
		if( !$connection->connect() ) {
			throw new RuntimeException( 'Fail to connect to the database' );
		}

		$collection = $connection
			->selectDatabase( $input->getOption( 'database' ) )
			->selectCollection( $input->getOption( 'collection' ) );
		$this->setupEntityCollection( $collection );

		$store = new MongoDBEntityStore( $collection );
		$entitySaver = $store->getEntityDocumentSaver();
		$serialization = new EntitySerializationFactory();

		$count = 0;
		foreach( new JsonDumpReader( $input->getArgument( 'file' ), $serialization->newEntityDeserializer() ) as $entity ) {
			$entitySaver->saveEntityDocument( $entity );
			$count++;

			if($count % 1000 === 0 ) {
				$output->write( '.' );
			}
		}

		$connection->close();

		$output->writeln( 'Importation done' );
	}

	protected function setupEntityCollection( Collection $collection ) {
		$collection->ensureIndex( array( 'id' => 1 ), array( 'unique' => true ) );
		$collection->ensureIndex( array( 'searchterms' => 1, 'type' => 1 ) );
	}
}
