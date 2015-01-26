<?php

namespace Wikibase\EntityStore\Console;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class CliApplicationFactory {

	public function newApplication() {
		$application = new Application();
		$application->setName( 'WikibaseEntityStoreMongoDB' );
	}

	public function newCreateCommand() {

	}
}
