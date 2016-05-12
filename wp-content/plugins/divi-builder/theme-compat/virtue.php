<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme Compatibility for Virtue theme
 * @see https://wordpress.org/themes/virtue/
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Virtue {
	/**
	 * Unique instance of class
	 */
	public static $instance;

	/**
	 * Constructor
	 */
	private function __construct(){
		$this->init_hooks();
	}

	/**
	 * Gets the instance of the class
	 */
	public static function init(){
		if ( null === self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook methods to WordPress
	 * @return void
	 */
	function init_hooks() {
		$theme   = wp_get_theme();
		$version = isset( $theme['Version'] ) ? $theme['Version'] : false;

		// Bail if no theme version found
		if ( ! $version ) {
			return;
		}

		// Fixing Virtue v2.5.6 below compatibility issue
		// @todo once this issue is fixed in future version, run version_compare() to limit the scope of this fix

		// Remove Virtue's template wrapper on preview screen
		if ( is_et_pb_preview() ) {
			remove_filter( 'template_include', array( 'Kadence_Wrapping', 'wrap' ), 101 );
		}
	}
}
ET_Builder_Theme_Compat_Virtue::init();