<?php

/**
 * Class WPML_TF_Settings_Handler
 *
 * @author OnTheGoSystems
 */
abstract class WPML_TF_Settings_Handler {

	/**
	 * @param string $class_name
	 *
	 * @return string
	 */
	protected function get_option_name( $class_name ) {
		return sanitize_title( $class_name );
	}
}