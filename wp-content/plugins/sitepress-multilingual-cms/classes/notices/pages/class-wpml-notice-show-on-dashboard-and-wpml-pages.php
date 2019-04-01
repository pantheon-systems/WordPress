<?php

class WPML_Notice_Show_On_Dashboard_And_WPML_Pages {

	public static function is_on_page() {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( 'dashboard' === $screen->id ) {
				return true;
			}
		}

		$current_page = array_key_exists( 'page', $_GET ) ? $_GET['page'] : null;

		foreach ( array( 'sitepress-multilingual-cms', 'wpml-translation-management' ) as $page ) {
			if ( strpos( $current_page, $page ) === 0 ) {
				return true;
			}
		}

		return false;
	}

}