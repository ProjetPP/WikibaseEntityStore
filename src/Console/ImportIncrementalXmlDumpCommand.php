<?php

namespace Wikibase\EntityStore\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\DataModel\Deserializers\EntityIdDeserializer;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\EntityStore\Config\EntityStoreFromConfigurationBuilder;
use Wikibase\EntityStore\DataModel\Deserializers\SerializedEntityDeserializer;
use Wikibase\EntityStore\Internal\IncrementalXmlDumpReader;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class ImportIncrementalXmlDumpCommand extends Command {

	protected function configure() {
		$this->setName( 'import-incremental-xml-dump' )
			->setDescription( 'Import an incremental XML dump of Wikidata in an entity store' )
			->addArgument( 'file', InputArgument::REQUIRED, 'incremental XML dump file' )
			->addArgument( 'configuration', InputArgument::REQUIRED, 'Configuration file' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$configurationBuilder = new EntityStoreFromConfigurationBuilder();
		$store = $configurationBuilder->buildEntityStore( $input->getArgument( 'configuration' ) );

		$output->writeln( 'Import data.' );
		$entitySaver = $store->getEntityDocumentSaver();

		$dumpReader = new IncrementalXmlDumpReader(
			$input->getArgument( 'file' ),
			new SerializedEntityDeserializer( new EntityIdDeserializer( new BasicEntityIdParser() ) ),
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
		$output->writeln( 'Importation done.' );
	}
}
