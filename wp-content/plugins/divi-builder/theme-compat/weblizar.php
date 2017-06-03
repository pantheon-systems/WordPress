<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Theme Compatibility for Weblizar theme
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Weblizar {
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
	 * Latest theme version: 3.2
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

		// Modify widget search selector
		add_filter( 'et_pb_widget_search_selector', array( $this, 'widget_search_selector' ) );

		// Fixing styling quirks
		add_action( 'wp_enqueue_scripts',           array( $this, 'add_styling_fix' ), 12 );
	}

	/**
	 * Instead of using default .widget.widget_search structure for its widget search, weblizar uses custom widget
	 * wrapper structure. Hence, this method adjust the widget search selector so divi builder's conditional class
	 * and other UX enhancement for widget search in sidebar module can be implemented
	 * @return string
	 */
	function widget_search_selector( $selector ) {
		return '.sidebar-block .sidebar-content.blog-search';
	}

	/**
	 * Add inline styling for fixing design quirks on weblizar theme
	 * @return void
	 */
	function add_styling_fix() {
		$style = '.et_divi_builder #et_builder_outer_content .et_pb_widget_area .sidebar-block { margin-top: 0; margin-bottom: 30px; color: inherit; }';
		wp_add_inline_style( 'et-builder-modules-style', $style );
	}
}
ET_Builder_Theme_Compat_Weblizar::init();