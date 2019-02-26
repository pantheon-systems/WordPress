<?php

class WPML_Cache_Terms_Per_Lang_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress;
		return new WPML_Cache_Terms_Per_Lang( $sitepress );
	}
}