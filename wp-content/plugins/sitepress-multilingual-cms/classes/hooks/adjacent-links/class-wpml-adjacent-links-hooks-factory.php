<?php

/**
 * Class WPML_Adjacent_Links_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_Adjacent_Links_Hooks_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	/** @return WPML_Adjacent_Links_Hooks */
	public function create() {
		global $sitepress, $wpdb;

		return new WPML_Adjacent_Links_Hooks(
			$sitepress,
			$wpdb,
			new WPML_Language_Where_Clause(
				$sitepress,
				$wpdb,
				new WPML_Display_As_Translated_Posts_Query( $wpdb, 'p' )
			)
		);
	}
}
