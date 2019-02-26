<?php

class WPML_Admin_URL {

	public static function multilingual_setup( $section = null ) {
		if ( defined( 'WPML_TM_VERSION' ) ) {
			$url = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_SETTINGS . '&sm=mcsetup' );
		} else {
			$url = admin_url( 'admin.php?page=' . ICL_PLUGIN_FOLDER . '/menu/translation-options.php' );
		}

		if ( $section ) {
			$url .= '#ml-content-setup-sec-' . $section;
		}

		return $url;
	}
}