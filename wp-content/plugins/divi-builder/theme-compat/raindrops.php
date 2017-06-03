<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Theme Compatibility for Raindrops theme
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Raindrops {
	/**
	 * Unique instance of class
	 */
	public static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Gets the instance of the class
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook methods to WordPress
	 * Note: once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 * Latest theme version: 1.326
	 * @return void
	 */
	function init_hooks() {
		$theme   = wp_get_theme();
		$version = isset( $theme['Version'] ) ? $theme['Version'] : false;

		// Bail if no theme version found
		if ( ! $version ) {
			return;
		}

		// Up to: latest theme version

		// Removing prepended featured image on title on blog module
		if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
			remove_filter( 'the_title', 'raindrops_fallback_title', 10 );
		}
	}
}
ET_Builder_Theme_Compat_Raindrops::init();