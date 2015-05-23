<?php

namespace Wikibase\EntityStore;

use InvalidArgumentException;

/**
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class EntityStoreOptions {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @param array $options
	 */
	public function __construct( array $options = [] ) {
		$this->options = $options;
	}

	/**
	 * @param string $option
	 * @param mixed $value
	 */
	public function setOption( $option, $value ) {
		$this->options[$option] = $value;
	}

	/**
	 * @param string $option
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function getOption( $option ) {
		if ( !array_key_exists( $option, $this->options ) ) {
			throw new InvalidArgumentException( 'Unknown option ' . $option );
		}

		return $this->options[$option];
	}

	/**
	 * @param string $option
	 * @return boolean
	 */
	public function hasOption( $option ) {
		return array_key_exists( $option, $this->options );
	}

	/**
	 * Sets the default value of an option
	 *
	 * @param string $option
	 * @param mixed $default
	 */
	public function defaultOption( $option, $default ) {
		if ( !$this->hasOption( $option ) ) {
			$this->setOption( $option, $default );
		}
	}
}
