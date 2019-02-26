<?php

class WPML_Rewrite_Rules_Filter {
	/**
	 * @var array
	 */
	private $active_languages;

	/**
	 * @var WPML_URL_Filters
	 */
	private $wpml_url_filters;

	/**
	 * @param array $active_languages
	 * @param WPML_URL_Filters $wpml_url_filters
	 */
	public function __construct( $active_languages, $wpml_url_filters = null ) {
		$this->active_languages = $active_languages;

		if ( ! $wpml_url_filters ) {
			global $wpml_url_filters;
		}
		$this->wpml_url_filters = $wpml_url_filters;
	}


	/**
	 * @param string $htaccess_string Content of the .htaccess file
	 *
	 * @return string .htaccess file contents with adjusted RewriteBase
	 */
	public function rid_of_language_param( $htaccess_string ) {
		if ( $this->wpml_url_filters->frontend_uses_root() || $this->is_permalink_page() || $this->is_shop_page() ) {
			foreach ( $this->active_languages as $lang_code ) {
				foreach ( array( '', 'index.php' ) as $base ) {
					$htaccess_string = str_replace(
						'/' . $lang_code . '/' . $base,
						'/' . $base,
						$htaccess_string
					);
				}
			}
		}

		return $htaccess_string;
	}

	/**
	 * Check if it is permalink page in admin.
	 *
	 * @return bool
	 */
	private function is_permalink_page() {
		return $this->is_admin_screen( 'options-permalink' );
	}

	/**
	 * Check if it is WooCommerce shop page in admin.
	 *
	 * @return bool
	 */
	private function is_shop_page() {
		return $this->is_admin_screen( 'page' ) && get_option( 'woocommerce_shop_page_id' ) === intval( $_GET['post'] );
	}

	/**
	 * Check if it as a certain screen on admin page.
	 *
	 * @param string $screen_id
	 *
	 * @return bool
	 */
	private function is_admin_screen( $screen_id ) {
		return is_admin() && function_exists( 'get_current_screen' ) && get_current_screen() && get_current_screen()->id === $screen_id;
	}
}
