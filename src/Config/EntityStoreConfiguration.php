<?php

namespace Wikibase\EntityStore\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityStoreConfiguration implements ConfigurationInterface {

	/**
	 * @see ConfigurationInterface::getConfigTreeBuilder
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();

		$rootNode = $treeBuilder->root( 'entitystore' );
		$rootNode
			->children()
				->enumNode( 'backend' )
					->values( [ 'api', 'mongodb' ] )
					->info( 'The backend to use' )
					->isRequired()
					->end()
				->arrayNode( 'api' )
					->info( 'API backend configuration' )
					->children()
						->scalarNode( 'url' )
							->info( 'URL of the API endpoint like http://www.wikidata.org/w/api.php' )
							->isRequired()
							->cannotBeEmpty()
							->end()
						->scalarNode( 'wikidataquery_url' )
							->info( 'URL of the WikidataQuery API endpoint like http://wdq.wmflabs.org/api' )
							->cannotBeEmpty()
							->end()
						->end()
					->end()
				->arrayNode( 'mongodb' )
					->info( 'MongoDB backend configuration' )
					->children()
						->scalarNode( 'server' )
							->info( 'MongoDB server to use' )
							->isRequired()
							->end()
						->scalarNode( 'database' )
							->info( 'MongoDB database to use' )
							->defaultValue( 'wikibase' )
							->end()
						->end()
					->end()
				->arrayNode( 'cache' )
					->info( 'Cache support configuration' )
					->children()
						->integerNode( 'lifetime' )
							->info( 'Cache life time' )
							->defaultValue( 0 )
							->end()
						->arrayNode( 'array' )
							->info( 'Use PHP array cache' )
							->canBeEnabled()
							->end()
						->arrayNode( 'memcached' )
							->info( 'Use Memcached' )
							->canBeEnabled()
							->children()
								->scalarNode( 'host' )
									->defaultValue( 'localhost' )
									->end()
								->integerNode( 'port' )
									->defaultValue( 11211 )
									->end()
								->end()
							->end()
						->end()
					->end()
					->variableNode( 'options' )
						->info( 'EntityStore options configuration' )
						->defaultValue( [] )
					->end()
				->end();

		return $treeBuilder;
	}
}
