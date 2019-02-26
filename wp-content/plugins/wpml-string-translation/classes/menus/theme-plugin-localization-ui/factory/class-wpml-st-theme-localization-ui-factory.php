<?php

class WPML_ST_Theme_Localization_UI_Factory {

	const TEMPLATE_PATH = '/templates/theme-plugin-localization/';

	/**
	 * @return WPML_ST_Theme_Localization_UI
	 */
	public function create() {
		global $wpdb;

		$localization = new WPML_Localization( $wpdb );
		$utils = new WPML_ST_Theme_Localization_Utils();

		return new WPML_ST_Theme_Localization_UI( $localization, $utils, self::TEMPLATE_PATH );
	}
}