<?php

/**
 * @author OnTheGo Systems
 */
class WPML_XML_Config_Read_Option implements WPML_XML_Config_Read {
	private $option;
	private $transform;
	private $validate;

	function __construct( WPML_WP_Option $option, WPML_XML_Config_Validate $validate, WPML_XML_Transform $transform ) {
		$this->option    = $option;
		$this->validate  = $validate;
		$this->transform = $transform;
	}

	function get() {
		if ( $this->option->get() ) {
			$content = $this->option->get();

			if ( $this->validate->from_string( $content ) ) {
				return $this->transform->get( $content );
			}
		}

		return null;
	}
}
