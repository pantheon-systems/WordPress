<?php

/**
 * Class WPML_TF_Frontend_Scripts
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Scripts {

	const HANDLE = 'wpml-tf-frontend';

	/**
	 * method enqueue
	 */
	public function enqueue() {
		$script = ICL_PLUGIN_URL . '/res/js/translation-feedback/wpml-tf-frontend-script.js';
		wp_register_script( self::HANDLE, $script, array( 'jquery', 'jquery-ui-dialog', 'wp-util' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_script( self::HANDLE );
	}
}