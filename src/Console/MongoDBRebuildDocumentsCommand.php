<?php

namespace Wikibase\EntityStore\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Deserializers\EntityIdDeserializer;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\Config\EntityStoreFromConfigurationBuilder;
use Wikibase\EntityStore\DataModel\Deserializers\SerializedEntityDeserializer;
use Wikibase\EntityStore\MongoDB\MongoDBDocumentBuilder;
use Wikibase\EntityStore\MongoDB\MongoDBEntityStore;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBRebuildDocumentsCommand extends Command {

	protected function configure() {
		$this->setName( 'rebuild-mongodb' )
			->setDescription( 'Import a JSON dump in an entity store' )
			->addArgument( 'configuration', InputArgument::REQUIRED, 'Configuration file' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$configurationBuilder = new EntityStoreFromConfigurationBuilder();
		/** @var MongoDBEntityStore $store */
		$store = $configurationBuilder->buildEntityStore( $input->getArgument( 'configuration' ) );
		$database = $store->getDatabase();
		$deserializer = new SerializedEntityDeserializer( new EntityIdDeserializer( new BasicEntityIdParser() ) );

		$saver = $store->getEntityDocumentSaver();

		$output->writeln( 'Beggining of rebuild.' );

		$i = 0;
		foreach( MongoDBDocumentBuilder::$SUPPORTED_ENTITY_TYPES as $entityType ) {
			$cursor = $database->selectCollection( $entityType )->getMongoCollection()->find();
			foreach( $cursor as $serialization ) {
				$saver->saveEntityDocument( $deserializer->deserialize( $serialization ) );

				if( $i % 1000 === 0 ) {
					$output->write( '.' );
				}
				$i++;
			}
		}
		$output->writeln( 'End of rebuild.' );

		$output->writeln( 'Index creation.' );
		$store->setupIndexes();
		$output->writeln( 'Index creation done.' );
	}
}
