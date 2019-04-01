<?php

class WPML_Ajax {

	/**
	 * @return bool
	 */
	public static function is_frontend_ajax_request() {
		return wpml_is_ajax() && isset( $_SERVER['HTTP_REFERER'] ) && false === strpos( $_SERVER['HTTP_REFERER'], admin_url() );
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	public static function is_admin_ajax_request_called_from_frontend( $url ) {
		if ( false === strpos( $url, 'admin-ajax.php' ) ) {
			return false;
		}

		// is not frontend
		if ( isset( $_SERVER['HTTP_REFERER'] )
			&& ( strpos( $_SERVER['HTTP_REFERER'], 'wp-admin' ) || strpos( $_SERVER['HTTP_REFERER'], 'admin-ajax' ) )
		) {
			return false;
		}

		return true;
	}
}