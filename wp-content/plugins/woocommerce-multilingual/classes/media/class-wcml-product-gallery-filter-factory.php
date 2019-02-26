<?php

class WCML_Product_Gallery_Filter_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WCML_Product_Gallery_Filter( new WPML_Translation_Element_Factory( $sitepress ) );
	}

}