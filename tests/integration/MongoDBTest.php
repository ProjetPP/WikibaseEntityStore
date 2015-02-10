<?php

namespace Wikibase\EntityStore;

use Ask\Language\Description\AnyValue;
use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use Ask\Language\Query;
use DataValues\StringValue;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\Config\EntityStoreFromConfigurationBuilder;
use Wikibase\EntityStore\Console\CliApplicationFactory;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase {

	public function testMongoDbStore() {
		$this->setupMongoDB();

		$store = $this->getEntityStoreFromConfiguration();

		$this->assertEquals(
			new ItemId( 'Q1' ),
			$store->getItemLookup()->getItemForId( new ItemId( 'Q1' ) )->getId()
		);

		$results = $store->getEntityDocumentLookup()->getEntityDocumentsForIds(
			array( new ItemId( 'Q1' ), new ItemId( 'Q1000' ) )
		);
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( new ItemId( 'Q1' ), $results[0]->getId() );

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$store->getItemIdForTermLookup()->getItemIdsForTerm( new Term( 'en', 'universe' ) )
		);

		$this->assertEquals(
			array(),
			$store->getItemIdForTermLookup()->getItemIdsForTerm( new Term( 'pl', 'Kosmos' ) )
		);

		$this->assertEquals(
			array( new PropertyId( 'P16' ) ),
			$store->getPropertyIdForQueryLookup()->getPropertyIdsForQuery( new Query(
				new AnyValue(),
				array(),
				new QueryOptions( 1, 0 )
			) )
		);

		$this->assertEquals(
			array( new ItemId( 'Q1' ) ),
			$store->getItemIdForQueryLookup()->getItemIdsForQuery( new Query(
				new SomeProperty(
					new EntityIdValue( new PropertyId( 'P18' ) ),
					new ValueDescription( new StringValue( 'Hubble ultra deep field.jpg' ) )
				),
				array(),
				new QueryOptions( 10, 0 )
			) )
		);
	}

	private function setupMongoDB() {
		$applicationFactory = new CliApplicationFactory();
		$importCommand = $applicationFactory->newApplication()->find( 'import-json-dump' );
		$input = new ArrayInput( array(
			'command' => 'import-json-dump',
			'file' => __DIR__ . '/../data/valid.json',
			'configuration' => __DIR__ . '/../data/valid-config.json'
		) );
		$importCommand->run( $input, new NullOutput() );
	}

	private function getEntityStoreFromConfiguration() {
		$configBuilder = new EntityStoreFromConfigurationBuilder();
		return $configBuilder->buildEntityStore( __DIR__ . '/../data/valid-config.json' );
	}
}
