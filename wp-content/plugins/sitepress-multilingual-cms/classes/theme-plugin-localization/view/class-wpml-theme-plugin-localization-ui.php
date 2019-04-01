<?php

class WPML_Theme_Plugin_Localization_UI {

	const TEMPLATE_PATH = '/templates/theme-plugin-localization/';

	/**
	 * @return IWPML_Template_Service
	 */
	private function get_template_service() {
		$paths = array();
		$paths[] = WPML_PLUGIN_PATH . self::TEMPLATE_PATH;

		if ( defined( 'WPML_ST_PATH' ) ) {
			$paths[] = WPML_ST_PATH . self::TEMPLATE_PATH;
		}

		$template_loader = new WPML_Twig_Template_Loader( $paths );
		return $template_loader->get_template();
	}

	/**
	 * @param IWPML_Theme_Plugin_Localization_UI_Strategy $localization_strategy
	 */
	public function render( IWPML_Theme_Plugin_Localization_UI_Strategy $localization_strategy ) {
		return $this->get_template_service()->show( $localization_strategy->get_model(), $localization_strategy->get_template() );
	}
}