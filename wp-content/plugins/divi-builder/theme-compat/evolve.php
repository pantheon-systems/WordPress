<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Theme Compatibility for evolve theme
 * @since 1.0
 */
class ET_Builder_Theme_Compat_evolve {
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

		// Fixing v3.4.4 and below issue
		// @todo once this issue is fixed in future version, run version_compare() to limit the scope of this fix
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styling_fix' ), 12 );
	}

	/**
	 * Add inline styling for fixing design quirks on evolve theme
	 * @return void
	 */
	function add_styling_fix() {
		$style = '.et_builder_outer_content .widget-content{ margin: 0 0px 35px 0px; padding: 10px 0 21px 0; } \n';
		$style .= '.et_builder_outer_content input[type="submit"], .et_builder_outer_content button, .et_builder_outer_content .button, .et_builder_outer_content input#submit { color: inherit !important; }';
		wp_add_inline_style( 'et-builder-modules-style', $style );
	}
}
ET_Builder_Theme_Compat_evolve::init();