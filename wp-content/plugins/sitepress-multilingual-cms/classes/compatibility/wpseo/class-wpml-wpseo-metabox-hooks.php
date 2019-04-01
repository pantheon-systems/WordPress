<?php

class WPML_WPSEO_Metabox_Hooks {

	/** @var WPML_Debug_BackTrace */
	private $backtrace;

	/** @var WPML_URL_Converter */
	private $url_converter;

	/** @var string */
	private $pagenow;

	/**
	 * @param WPML_Debug_BackTrace $backtrace
	 * @param WPML_URL_Converter   $url_converter
	 * @param string               $pagenow
	 */
	public function __construct( WPML_Debug_BackTrace $backtrace, WPML_URL_Converter $url_converter, $pagenow ) {
		$this->backtrace     = $backtrace;
		$this->url_converter = $url_converter;
		$this->pagenow       = $pagenow;
	}

	public function add_hooks() {
		if ( is_admin() ) {
			add_filter( 'get_sample_permalink', array( $this, 'force_permalink_structure_to_postname' ) );

			if ( 'post-new.php' === $this->pagenow ) {
				add_filter( 'home_url', array( $this, 'convert_home_url_in_metabox_formatter' ) );
			}
		}
	}

	/**
	 * This will follow the way WP SEO deals with sample URL in WPSEO_Post_Metabox_Formatter::base_url_for_js
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4690
	 *
	 * @param array $permalink
	 *
	 * @return array
	 */
	public function force_permalink_structure_to_postname( $permalink ) {
		if ( $this->backtrace->is_class_function_in_call_stack( 'WPSEO_Metabox', 'localize_post_scraper_script' ) ) {
			$permalink[0] = preg_replace( '#%pagename%#', '%postname%', $permalink[0] );
		}

		return $permalink;
	}

	/**
	 * @param $url
	 *
	 * @return bool|mixed|string
	 */
	public function convert_home_url_in_metabox_formatter( $url ) {
		if ( $this->backtrace->is_class_function_in_call_stack( 'WPSEO_Post_Metabox_Formatter', 'base_url_for_js' ) ) {
			$url = $this->url_converter->convert_url( $url );
		}

		return $url;
	}
}
