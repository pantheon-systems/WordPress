<?php

/**
 * Class WPML_TF_Backend_Scripts
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Scripts {

	const HANDLE = 'wpml-tf-backend';

	/**
	 * method enqueue
	 */
	public function enqueue() {
		$script = ICL_PLUGIN_URL . '/res/js/translation-feedback/wpml-tf-backend-script.js';
		wp_register_script( self::HANDLE, $script, array( 'jquery', 'wp-util' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_script( self::HANDLE );
	}
}