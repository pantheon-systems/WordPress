<?php

class WPML_TM_Page {

	private static function is_tm_page( $page = null ) {
		return is_admin() && isset( $_GET['page'] ) && $_GET['page'] === WPML_TM_FOLDER . $page;
	}

	public static function is_tm_translators() {
		return self::is_tm_page( WPML_Translation_Management::PAGE_SLUG_MANAGEMENT )
		       && isset( $_GET['sm'] ) && $_GET['sm'] === 'translators';
	}

	public static function is_settings() {
		return self::is_tm_page( WPML_Translation_Management::PAGE_SLUG_SETTINGS )
		       && ( ! isset( $_GET['sm'] ) || $_GET['sm'] === 'mcsetup' );
	}

	public static function is_translation_queue() {
		return self::is_tm_page( WPML_Translation_Management::PAGE_SLUG_QUEUE );
	}

	public static function get_translators_url( $params = array() ) {
		$url = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php' );
		$params['sm'] = 'translators';
		return add_query_arg( $params, $url );
	}

}