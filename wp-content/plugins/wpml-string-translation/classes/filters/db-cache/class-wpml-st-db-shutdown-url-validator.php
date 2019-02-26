<?php

class WPML_ST_DB_Shutdown_Url_Validator {

	/**
	 * @var WP
	 */
	private $wp;

	/**
	 * @param WP $wp
	 */
	public function __construct( WP $wp ) {
		$this->wp = $wp;
	}

	public function is_404() {
		global $wp_query;

		if ( isset( $wp_query ) ) {
			if ( is_404() ) {
				return true;
			}
			if ( ! is_home() ) {
				return false;
			}
		}

		return $this->get_home_url() !== $this->get_current_url();
	}

	public function is_resetting_single_site_action() {
		return $_POST && array_key_exists( 'icl-reset-all', $_POST ) && $_POST['icl-reset-all'] === 'on';
	}

	public function is_resetting_multi_site_action() {
		return $_GET && array_key_exists( 'action', $_GET ) && $_GET['action'] === 'resetwpml';
	}

	/**
	 * @return string
	 */
	private function get_current_url() {
		$current_url = isset( $_SERVER['REQUEST_SCHEME'] ) ? $_SERVER['REQUEST_SCHEME'] : 'http';
		$current_url .= '://';
		$current_url .= isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '';
		$current_url .= '/';
		$current_url .= isset( $_SERVER['REQUEST_URI'] ) ? trim( $_SERVER['REQUEST_URI'], '/' ) : '';

		return $current_url;
	}

	/**
	 * @return string
	 */
	private function get_home_url() {
		$siteurl  = rtrim( get_option( 'siteurl' ), '/' );
		$home_url = rtrim( home_url(), '/' );
		$home_url = str_replace( $siteurl . '?', $siteurl . '/?', $home_url );

		return $home_url;
	}
}
