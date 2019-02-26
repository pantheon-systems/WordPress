<?php

class WPML_Installer_Domain_URL_Factory implements IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	public function create() {
		global $sitepress;

		if ( WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $sitepress->get_setting( 'language_negotiation_type' ) ) {
			return new WPML_Installer_Domain_URL( $sitepress->convert_url( get_home_url(), $sitepress->get_default_language() ) );
		}

		return null;
	}
}