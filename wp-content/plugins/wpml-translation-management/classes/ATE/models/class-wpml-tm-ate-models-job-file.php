<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Models_Job_File {
	public $content;
	public $name;
	public $type;

	/**
	 * WPML_TM_ATE_Models_Job_File constructor.
	 *
	 * @param array $args
	 */
	public function __construct( array $args = array() ) {
		foreach ( $args as $key => $value ) {
			$this->$key = $value;
		}
	}
}