<?php

/**
 * Class WPML_Compatibility_Factory
 */
class WPML_Compatibility_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	/**
	 * Create array of compatibility objects.
	 *
	 * @return array
	 */
	public function create() {
		$hooks = array();

		$hooks['gutenberg'] = new WPML_Compatibility_Gutenberg( new WPML_WP_API() );

		$hooks['jetpack'] = new WPML_Compatibility_Jetpack();

		$hooks['elementor'] = new WPML_PB_Fix_Maintenance_Query();

		return $hooks;
	}
}
