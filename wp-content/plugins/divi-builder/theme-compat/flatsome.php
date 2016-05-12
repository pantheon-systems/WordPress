<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme Compatibility for Flatsome theme
 * @see https://wordpress.org/themes/make/
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Flatsome {
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

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
	}

	/**
	 * Description
	 * @since 1.0
	 * @return void
	 */
	function admin_enqueue_scripts() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$current_screen = get_current_screen();

		// Only load in post-editing screen
		if ( isset( $current_screen->base ) && 'post' === $current_screen->base ) {
			wp_enqueue_script( 'et_pb_theme_flatsome_editor', ET_BUILDER_PLUGIN_URI . '/theme-compat/js/flatsome-editor.js', array( 'et_pb_admin_js', 'jquery' ), ET_BUILDER_VERSION, true );
		}
	}
}
ET_Builder_Theme_Compat_Flatsome::init();