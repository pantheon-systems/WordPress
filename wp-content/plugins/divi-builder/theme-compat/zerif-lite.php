<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Theme Compatibility for Zerif Lite theme
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Zerif_Lite{
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

		// Fixing v1.8.2.3 and below issue
		// @todo once this issue is fixed in future version, run version_compare() to limit the scope of this fix
		add_action( 'wp_footer', array( $this, 'fix_slide_in_animation' ), 100 );
	}

	/**
	 * Remove `scrolled` event handler. Zerif Lite registers event handler for `scroll` (see /zerif-lite/js/zerif.js)
	 * for waypoint-esque functionality for one-pager site scenario: menu items point to particular ID so when user
	 * reaches section with that particular ID, menu items will turn into active state. This behaviour conflicts with
	 * Divi Builder's slide in animation which is powered by jQuery Waypoints. To fix this, Divi Builder deregister
	 * scrolled event handler considering unanimated section is more vital compared to in-page active state
	 * @since 1.0
	 * @return void
	 */
	function fix_slide_in_animation() {
		if ( wp_script_is( 'jquery', 'done' ) && wp_script_is( 'zerif_script', 'done' ) ) {
			?><script type="text/javascript">jQuery(window).off('scroll', scrolled );</script><?php
		}
	}
}
ET_Builder_Theme_Compat_Zerif_Lite::init();