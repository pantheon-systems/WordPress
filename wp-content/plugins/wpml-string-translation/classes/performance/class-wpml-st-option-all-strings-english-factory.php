<?php

class WPML_ST_Options_All_Strings_English_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {
	/**
	 * @return WPML_ST_Options_All_Strings_English
	 */
	public function create() {
		global $wpdb, $sitepress;

		return new WPML_ST_Options_All_Strings_English( $wpdb, $sitepress->get_default_language() );
	}
}
