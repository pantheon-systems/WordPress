<?php

class WPML_ST_Plugin_Localization_UI_Factory {

	/**
	 * @return WPML_ST_Plugin_Localization_UI
	 */
	public function create() {
		global $wpdb;

		$localization = new WPML_Localization( $wpdb );
		$utils = new WPML_ST_Plugin_Localization_Utils();

		return new WPML_ST_Plugin_Localization_UI( $localization, $utils );
	}
}