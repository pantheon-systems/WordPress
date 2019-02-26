<?php

class WPML_ST_Track_Strings_Notice_Hooks_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_ST_Track_Strings_Notice_Hooks
	 */
	public function create() {
		global $wpml_admin_notices, $sitepress;

		return new WPML_ST_Track_Strings_Notice_Hooks(
			new WPML_ST_Track_Strings_Notice( $wpml_admin_notices ),
			new WPML_Save_Themes_Plugins_Localization_Options( $sitepress )
		);
	}
}