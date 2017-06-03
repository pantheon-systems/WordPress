<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Some themes are conflicting with Divi Builder beyond generalized solution
 * Load theme-based compatibility fix until theme author makes it compatible with Divi Builder
 * @since 1.0
 */
class ET_Builder_Theme_Compat_Loader {
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
	 * Hooking methods into WordPress actions and filters
	 * @return void
	 */
	private function init_hooks() {
		// load after $post is initiated. Cannot load before `init` hook
		if ( is_admin() ) {
			// Adding script for UX enhancement in dashboard needs earlier hook registration
			add_action( 'wp_loaded', array( $this, 'load_theme_compat' ), 1000 );
		} else {
			// Add after $post object has been set up so it can only load theme compat on page
			// which uses Divi Builder only
			add_action( 'wp', array( $this, 'load_theme_compat' ) );
		}
	}

	/**
	 * Get theme name
	 * @return string|bool
	 */
	function get_theme_name() {
		$theme = wp_get_theme();

		if ( isset( $theme['Name'] ) ) {
			return $theme['Name'];
		}

		return false;
	}

	/**
	 * List of themes with available compatibility
	 * @return array
	 */
	function theme_list() {
		return apply_filters( 'et_builder_theme_compat_loader_list', array(
			'Make',
			'Virtue',
			'evolve',
			'raindrops',
			'Weblizar',
			'Zerif Lite',
			'Flatsome',
		) );
	}

	/**
	 * Check whether current page should load theme compatibility file or not
	 * @return bool
	 */
	function has_theme_compat() {
		$post_id = get_the_ID();

		// in dashboard and preview, always load theme-compat file
		$is_using_pagebuilder = is_admin() || is_et_pb_preview() ? true : isset( $post_id ) && et_pb_is_pagebuilder_used( $post_id );

		// Check whether: 1) current page uses Divi builder or 2) current theme has compatibility file
		if ( $is_using_pagebuilder && in_array( $this->get_theme_name(), $this->theme_list() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Load theme compatibility file, if there's any
	 * @return void
	 */
	function load_theme_compat() {
		if ( $this->has_theme_compat() ) {
			// Get theme-compat file at /theme-compat/ directory
			$theme_compat_path = ET_BUILDER_PLUGIN_DIR . 'theme-compat/' . sanitize_title( $this->get_theme_name() ) . '.php';
			require_once apply_filters( 'et_builder_theme_compat_loader_list_path', $theme_compat_path, $this->get_theme_name() );
		}
	}
}

ET_Builder_Theme_Compat_Loader::init();