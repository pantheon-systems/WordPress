<?php

class WPML_TM_Translation_Services_Admin_Section_Resources {

	public function add_hooks() {
		if ( $this->is_active() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'wpml-tm-ts-admin-section',
			WPML_TM_URL . '/res/css/admin-sections/translation-services.css',
			array(),
			WPML_TM_VERSION
		);
	}

	public function enqueue_scripts() {

		wp_enqueue_script(
			'wpml-tm-ts-admin-section',
			WPML_TM_URL . '/res/js/translation-services.js',
			array(),
			WPML_TM_VERSION
		);

		wp_enqueue_script( 'wpml-tm-translation-services',
			WPML_TM_URL . '/dist/js/translationServices/app.js',
			array(),
			WPML_TM_VERSION
		);

		wp_enqueue_script(
			'wpml-tp-api',
			WPML_TM_URL . '/res/js/wpml-tp-api.js',
			array( 'jquery', 'wp-util' ),
			WPML_TM_VERSION
		);
	}

	private function is_active() {
		return isset( $_GET['sm'] ) && 'translation-services' === $_GET['sm'];
	}
}