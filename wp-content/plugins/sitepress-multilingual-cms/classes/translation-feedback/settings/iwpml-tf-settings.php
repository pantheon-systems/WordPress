<?php

/**
 * Interface WPML_Settings_Interface
 *
 * @author OnTheGoSystems
 */
interface IWPML_TF_Settings {

	/**
	 * @return array of name/value pairs
	 *
	 * Each property should have its own setter "set_{$property_name}"
	 */
	public function get_properties();
}
