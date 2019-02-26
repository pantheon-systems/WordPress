<?php

/**
 * Class WPML_TF_Backend_Styles
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Styles {

	const HANDLE = 'wpml-tf-backend';

	/**
	 * method enqueue
	 */
	public function enqueue() {
		$style = ICL_PLUGIN_URL . '/res/css/translation-feedback/backend-feedback-list.css';
		wp_register_style( self::HANDLE, $style, array( 'otgs-ico' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_style( self::HANDLE );
	}
}
