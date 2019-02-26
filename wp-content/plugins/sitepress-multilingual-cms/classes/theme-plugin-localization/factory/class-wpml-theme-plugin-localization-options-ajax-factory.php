<?php

class WPML_Theme_Plugin_Localization_Options_Ajax_Factory implements IWPML_AJAX_Action_Loader {

	public function create() {
		global $sitepress;

		$save_localization_options = new WPML_Save_Themes_Plugins_Localization_Options( $sitepress );

		return new WPML_Theme_Plugin_Localization_Options_Ajax( $save_localization_options );
	}
}