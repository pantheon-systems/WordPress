<?php

class WPML_ST_Theme_Plugin_Localization_Resources {

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'wpml-theme-plugin-localization-scan',
			WPML_ST_URL . '/res/js/theme-plugin-localization/theme-plugin-localization.js',
			array( 'jquery-ui-dialog' ),
			WPML_ST_VERSION
		);

		wp_enqueue_script(
			'wpml-enable-fastest-settings',
			WPML_ST_URL . '/res/js/performance/enable-fastest-settings.js',
			array( 'jquery' ),
			WPML_ST_VERSION
		);

		wp_enqueue_style(
			'wpml-theme-plugin-localization-scan',
			WPML_ST_URL . '/res/css/theme-plugin-localization/theme-plugin-localization.css',
			array(),
			WPML_ST_VERSION
		);

		wp_localize_script(
			'wpml-theme-plugin-localization-scan',
			'wpml_groups_to_scan',
			get_option( WPML_ST_Themes_And_Plugins_Updates::WPML_ST_ITEMS_TO_SCAN )
		);

		wp_enqueue_script(
			'wpml-st-tracking-all-strings-as-english-notice',
			WPML_ST_URL . '/res/js/tracking-all-strings-as-english-notice.js',
			array( 'jquery' ),
			WPML_ST_VERSION
		);

		wp_enqueue_script(
			'wpml-enable-fastest-settings',
			WPML_ST_URL . '/res/js/performance/enable-fastest-settings.js',
			array( 'jquery' ),
			WPML_ST_VERSION
		);

		wp_localize_script(
			'wpml-theme-plugin-localization-scan',
			'wpml_active_plugins_themes',
			$this->get_active_items()
		);
	}

	private function get_active_items() {
		$items = array();

		foreach ( get_plugins() as $key => $plugin ) {
			if ( is_plugin_active( $key ) ) {
				$items['plugin'][] = $key;
			}
		}

		$items['theme'][] = wp_get_theme()->get_template();

		return $items;
	}
}