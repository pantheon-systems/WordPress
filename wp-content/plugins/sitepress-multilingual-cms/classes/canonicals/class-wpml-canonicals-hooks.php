<?php

/**
 * Class WPML_Canonicals_Hooks
 */
class WPML_Canonicals_Hooks {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var  WPML_URL_Converter $url_converter */
	private $url_converter;

	/** @var callable $is_current_request_root_callback */
	private $is_current_request_root_callback;

	/**
	 * WPML_Canonicals_Hooks constructor.
	 *
	 * @param SitePress          $sitepress
	 * @param WPML_URL_Converter $url_converter
	 * @param callable           $is_current_request_root_callback
	 */
	public function __construct( SitePress $sitepress, WPML_URL_Converter $url_converter, $is_current_request_root_callback ) {
		$this->sitepress     = $sitepress;
		$this->url_converter = $url_converter;
		$this->is_current_request_root_callback = $is_current_request_root_callback;
	}

	public function add_hooks() {
		$urls             = $this->sitepress->get_setting( 'urls' );
		$lang_negotiation = (int) $this->sitepress->get_setting( 'language_negotiation_type' );

		if ( WPML_LANGUAGE_NEGOTIATION_TYPE_DIRECTORY === $lang_negotiation
		     && ! empty( $urls['directory_for_default_language'] )
		) {
			add_action( 'template_redirect', array( $this, 'redirect_pages_from_root_to_default_lang_dir' ) );
		} elseif ( WPML_LANGUAGE_NEGOTIATION_TYPE_PARAMETER === $lang_negotiation ) {
			add_action( 'redirect_canonical', array( $this, 'prevent_redirection_with_translated_paged_content' ) );
		}

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && strpos( strtolower( $_SERVER['SERVER_SOFTWARE'] ), 'nginx' ) !== false ) {
			add_action( 'redirect_canonical', array( $this, 'maybe_fix_nginx_redirection_callback' ) );
		}
	}

	public function redirect_pages_from_root_to_default_lang_dir() {
		if ( is_page() && ! call_user_func( $this->is_current_request_root_callback ) ) {
			$lang = $this->sitepress->get_current_language();
			$current_uri = $_SERVER['REQUEST_URI'];
			$abs_home    = $this->url_converter->get_abs_home();
			$install_subdir = wpml_parse_url( $abs_home, PHP_URL_PATH );
			$actual_uri  = preg_replace( '#^' . $install_subdir . '#', '', $current_uri );
			$actual_uri  = '/' . ltrim( $actual_uri, '/' );

			if ( 0 !== strpos( $actual_uri, '/' . $lang . '/' ) ) {
				$canonical_uri = trailingslashit( $install_subdir ) . $lang . $actual_uri;
				$canonical_uri = user_trailingslashit( $canonical_uri );
				$this->sitepress->get_wp_api()->wp_safe_redirect( $canonical_uri, 301 );
			}
		}
	}

	/**
	 * @param string $redirect
	 *
	 * @return bool|string
	 */
	public function maybe_fix_nginx_redirection_callback( $redirect ) {
		if ( is_front_page() ) {
			$redirect = false;
		}

		return $redirect;
	}

	/**
	 * @param string $redirect_url
	 *
	 * @return string|false
	 */
	public function prevent_redirection_with_translated_paged_content( $redirect_url ) {
		if ( ! is_singular() || ! isset( $_GET['lang'] ) ) {
			return $redirect_url;
		}

		$page = (int) get_query_var( 'page' );

		if ( $page < 2 ) {
			return $redirect_url;
		}

		return false;
	}
}
