<?php

class WPML_Compatibility_Tiny_Compress_Images_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return \WPML_Compatibility_Tiny_Compress_Images
	 */
	public function create() {
		global $sitepress;

		return new WPML_Compatibility_Tiny_Compress_Images(
			new WPML_Translation_Element_Factory( $sitepress )
		);
	}
}
