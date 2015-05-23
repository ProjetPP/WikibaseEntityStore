<?php

namespace Wikibase\EntityStore;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use DataValues\StringValue;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Term\Term;
use Wikibase\EntityStore\Config\EntityStoreFromConfigurationBuilder;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ApiTest extends \PHPUnit_Framework_TestCase {

	public function testApiStore() {
		date_default_timezone_set( 'UTC' );
		$store = $this->getEntityStoreFromConfiguration();

		$this->assertEquals(
			new ItemId( 'Q1' ),
			$store->getItemLookup()->getItemForId( new ItemId( 'Q1' ) )->getId()
		);

		$results = $store->getEntityDocumentLookup()->getEntityDocumentsForIds(
			[ new ItemId( 'Q1' ), new PropertyId( 'P18' ) ]
		);
		$this->assertEquals( 2, count( $results ) );

		$this->assertEquals(
			[ new ItemId( 'Q42' ) ],
			$store->getItemIdForTermLookup()->getItemIdsForTerm( new Term( 'en', 'Douglas Noël Adams' ) )
		);

		$this->assertEquals(
			[ new PropertyId( 'P16' ) ],
			$store->getPropertyIdForTermLookup()->getPropertyIdsForTerm( new Term( 'en', 'highway system' ) )
		);

		$this->assertEquals(
			[ new ItemId( 'Q1' ) ],
			$store->getItemIdForQueryLookup()->getItemIdsForQuery( new SomeProperty(
				new EntityIdValue( new PropertyId( 'P18' ) ),
				new ValueDescription( new StringValue( 'Hubble ultra deep field.jpg' ) )
			) )
		);
	}

	private function getEntityStoreFromConfiguration() {
		$configBuilder = new EntityStoreFromConfigurationBuilder();
		return $configBuilder->buildEntityStore( __DIR__ . '/../data/valid-config-api.json' );
	}
}
