<?php

class WPML_Endpoints_Support_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader, IWPML_Deferred_Action_Loader {

	public function get_load_action() {
		return 'plugins_loaded';
	}

	/**
	 * @return WPML_Endpoints_Support
	 */
	public function create() {
		global $sitepress, $wpml_post_translations;

		if ( defined( 'WPML_ST_VERSION' ) ) {
			return new WPML_Endpoints_Support( $wpml_post_translations, $sitepress->get_current_language(), $sitepress->get_default_language() );
		}

		return null;
	}
}