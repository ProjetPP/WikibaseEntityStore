<?php

namespace Wikibase\EntityStore\Console;

use Symfony\Component\Console\Application;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CliApplicationFactory {

	public function newApplication() {
		$application = new Application( 'WikibaseEntityStore' );

		$application->add( new ImportJsonDumpCommand() );

		return $application;
	}
}
