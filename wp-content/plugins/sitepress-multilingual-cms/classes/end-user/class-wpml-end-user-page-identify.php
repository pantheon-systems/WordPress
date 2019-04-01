<?php

class WPML_End_User_Page_Identify {
	/** @var  WPML_WP_API */
	private $wpml_api;

	/** @var  string */
	private $pagenow;

	/**
	 * @param WPML_WP_API $wpml_api
	 * @param string $pagenow
	 */
	public function __construct( WPML_WP_API $wpml_api, $pagenow ) {
		$this->wpml_api = $wpml_api;
		$this->pagenow  = $pagenow;
	}

	/**
	 * @return bool
	 */
	public function is_tm_dashboard() {
		return $this->wpml_api->is_tm_page( 'dashboard' );
	}

	/**
	 * @return bool
	 */
	public function is_page_list() {
		if ( 'edit.php' !== $this->pagenow ) {
			return false;
		}

		if ( ! array_key_exists( 'post_type', $_GET ) ) {
			return false;
		}

		return 'page' === $_GET['post_type'];
	}
}
