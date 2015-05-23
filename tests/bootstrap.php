<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
	die( 'You need to install this package with Composer before you can run the tests' );
}

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->addClassMap( [
	'Wikibase\EntityStore\EntityStoreTest' => __DIR__ . '/unit/EntityStoreTest.php'
] );
