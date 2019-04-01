<?php

class WPML_Themes_Plugin_Localization_UI_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Deferred_Action_Loader {

	/** @return WPML_Theme_Plugin_Localization_UI_Hooks */
	public function create() {
		global $sitepress;

		$hooks = null;
		$current_screen = get_current_screen();

		if ( isset( $current_screen->id ) && WPML_PLUGIN_FOLDER . '/menu/theme-localization' === $current_screen->id ) {
			$localization_ui = new WPML_Theme_Plugin_Localization_UI();
			$options_ui = new WPML_Theme_Plugin_Localization_Options_UI( $sitepress );
			$hooks = new WPML_Theme_Plugin_Localization_UI_Hooks( $localization_ui, $options_ui );
		}

		return $hooks;
	}

	/** @return string */
	public function get_load_action() {
		return 'current_screen';
	}
}