<?php

/**
 * Class WPML_TF_Backend_Options_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Options_Hooks_Factory extends WPML_Current_Screen_Loader_Factory {

	/** @return string */
	protected function get_screen_regex() {
		return '#' . WPML_PLUGIN_FOLDER . '/menu/languages#';
	}

	/** @return null|WPML_TF_Backend_Options_Hooks */
	protected function create_hooks() {
		/** @var SitePress $sitepress */
		global $sitepress;

		if ( $sitepress->is_setup_complete() ) {
			$template_loader = new WPML_Twig_Template_Loader(
				array( WPML_PLUGIN_PATH . WPML_TF_Backend_Options_View::TEMPLATE_FOLDER )
			);

			$settings_read = new WPML_TF_Settings_Read();
			/** @var WPML_TF_Settings $tf_settings */
			$tf_settings   = $settings_read->get( 'WPML_TF_Settings' );

			$options_view = new WPML_TF_Backend_Options_View(
				$template_loader->get_template(),
				$tf_settings,
				$sitepress
			);

			$translation_service = new WPML_TF_Translation_Service(
				class_exists( 'WPML_TP_Client_Factory' ) ? new WPML_TP_Client_Factory() : null
			);

			return new WPML_TF_Backend_Options_Hooks(
				$options_view,
				new WPML_TF_Backend_Options_Scripts(),
				new WPML_TF_Backend_Options_Styles(),
				$translation_service
			);
		}

		return null;
	}
}
