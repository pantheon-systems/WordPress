<?php
/**
 * Class: Occurrence Query Class
 *
 * OccurrenceQuery model adds or clears arguments in the Query model.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * OccurrenceQuery model adds or clears arguments in the Query model.
 *
 * @package Wsal
 */
class WSAL_Models_OccurrenceQuery extends WSAL_Models_Query {

	/**
	 * Query Arguments.
	 *
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * Sets arguments.
	 *
	 * @param string $field - Name field.
	 * @param mixed  $value - Value.
	 * @return self
	 */
	public function addArgument( $field, $value ) {
		$this->arguments[ $field ] = $value;
		return $this;
	}

	/**
	 * Resets arguments.
	 *
	 * @return self
	 */
	public function clearArguments() {
		$this->arguments = array();
		return $this;
	}

	/**
	 * Method: Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct();

		// TO DO: Consider if Get Table is the right method to call given that this is mysql specific.
		$this->addFrom( $this->getConnector()->getAdapter( 'Occurrence' )->GetTable() );
	}
}
