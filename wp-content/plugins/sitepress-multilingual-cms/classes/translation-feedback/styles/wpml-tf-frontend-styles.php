<?php

/**
 * Class WPML_TF_Frontend_Styles
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Styles {

	const HANDLE = 'wpml-tf-frontend';

	/**
	 * method enqueue
	 */
	public function enqueue() {
		$style = ICL_PLUGIN_URL . '/res/css/translation-feedback/front-style.css';
		wp_register_style( self::HANDLE, $style, array( 'otgs-dialogs', 'otgs-ico' ), ICL_SITEPRESS_VERSION );
		wp_enqueue_style( self::HANDLE );
	}
}
