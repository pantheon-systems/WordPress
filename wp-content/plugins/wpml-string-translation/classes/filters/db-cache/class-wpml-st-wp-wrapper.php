<?php

class WPML_ST_WP_Wrapper {
	/**
	 * @var WP
	 */
	private $wp;

	/** @var array */
	private $preserved_filters = array( 'sanitize_title', 'home_url' );

	/**
	 * @param WP $wp
	 */
	public function __construct( WP $wp ) {
		$this->wp = clone $wp;
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public function parse_request( $path ) {
		global $wp_filter;

		$tmp_wp_filter = $wp_filter;
		$GLOBALS['wp_filter'] = array_intersect_key( $wp_filter, array_fill_keys( $this->preserved_filters, 1 ) );

		$result = $path;

		$this->wp->parse_request();
		if ( $this->wp->matched_rule ) {
			$result = $this->wp->matched_rule;
			$this->wp->matched_rule = null;
		}

		$GLOBALS['wp_filter'] = $tmp_wp_filter;

		return $result;
	}
}