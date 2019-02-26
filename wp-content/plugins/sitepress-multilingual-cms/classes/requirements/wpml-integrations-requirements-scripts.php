<?php

class WPML_Integrations_Requirements_Scripts {

	public function add_translation_editor_notice_hook() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_translation_editor_notice_script' ) );
	}

	public function enqueue_translation_editor_notice_script() {
		wp_enqueue_script( 'wpml-integrations-requirements-scripts', ICL_PLUGIN_URL . '/res/js/requirements/integrations-requirements.js', array( 'jquery' ) );
	}

	public function add_plugins_activation_hook() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_plugin_activation_script' ) );
	}

	public function enqueue_plugin_activation_script() {
		wp_enqueue_script( 'wpml-requirements-plugins-activation', ICL_PLUGIN_URL . '/dist/js/wpml-requirements/app.js', array(), ICL_SITEPRESS_VERSION );
	}
}
