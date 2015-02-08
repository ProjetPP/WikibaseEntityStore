<?php

namespace Wikibase\EntityStore\Console;

use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;
use Guzzle\Common\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\EntityStore\Config\EntityStoreFromConfigurationBuilder;
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
			->addArgument( 'configuration', InputArgument::REQUIRED, 'Configuration file' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$configurationBuilder = new EntityStoreFromConfigurationBuilder();
		$store = $configurationBuilder->buildEntityStore( $input->getArgument( 'configuration' ) );

		$store->setupStore();

		$entitySaver = $store->getEntityDocumentSaver();
		$serialization = new EntitySerializationFactory();

		$dumpReader = new JsonDumpReader(
			$input->getArgument( 'file' ),
			$serialization->newEntityDeserializer(),
			new ConsoleLogger( $output )
		);
		$count = 0;
		foreach( $dumpReader as $entity ) {
			$entitySaver->saveEntityDocument( $entity );
			$count++;

			if($count % 1000 === 0 ) {
				$output->write( '.' );
			}
		}

		$output->writeln( 'Importation done' );
	}
}
