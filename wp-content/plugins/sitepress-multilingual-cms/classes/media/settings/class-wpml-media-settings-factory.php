<?php

class WPML_Media_Settings_Factory extends WPML_Current_Screen_Loader_Factory {

	public function create_hooks() {
		global $wpdb;

		return new WPML_Media_Settings( $wpdb );
	}

	public function get_screen_regex() {
		return defined( 'WPML_TM_FOLDER' )
			? '/(' . WPML_PLUGIN_FOLDER . '\/menu\/translation-options|wpml_page_' . WPML_TM_FOLDER . '\/menu\/settings)/'
			: '/' . WPML_PLUGIN_FOLDER . '\/menu\/translation-options/';
	}
}