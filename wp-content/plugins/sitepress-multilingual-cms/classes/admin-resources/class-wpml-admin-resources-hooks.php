<?php

class WPML_Admin_Resources_Hooks {

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_resources' ) );
	}

	public function register_resources() {
		wp_register_style( 'wpml-tooltip', ICL_PLUGIN_URL . '/res/css/tooltip/tooltip.css', array( 'wp-pointer' ), ICL_SITEPRESS_VERSION );
		wp_register_script( 'wpml-tooltip', ICL_PLUGIN_URL . '/res/js/tooltip/tooltip.js', array( 'wp-pointer', 'jquery' ), ICL_SITEPRESS_VERSION );
	}
}