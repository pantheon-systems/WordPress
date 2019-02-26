<?php

namespace DeliciousBrains\WP_Offload_Media\Pro;

use Amazon_S3_And_CloudFront_Pro;

class Sidebar_Presenter {

	/**
	 * @var Sidebar_Presenter
	 */
	protected static $instance;

	/**
	 * @var Amazon_S3_And_CloudFront_Pro
	 */
	private $as3cf;

	/**
	 * Registered tools.
	 *
	 * @var array
	 */
	private $tools = array();

	/**
	 * Make this class a singleton.
	 *
	 * Use this instead of __construct().
	 *
	 * @param Amazon_S3_And_CloudFront_Pro $as3cf
	 *
	 * @return Sidebar_Presenter
	 */
	public static function get_instance( $as3cf ) {
		if ( ! isset( static::$instance ) && ! ( self::$instance instanceof Sidebar_Presenter ) ) {
			static::$instance = new Sidebar_Presenter();
			// Initialize the class
			static::$instance->init( $as3cf );
		}

		return static::$instance;
	}

	/**
	 * Init.
	 *
	 * @param Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	public function init( $as3cf ) {
		$this->as3cf = $as3cf;

		add_action( 'as3cfpro_load_assets', array( $this, 'load_assets' ) );

		// JS data
		add_filter( 'as3cfpro_js_nonces', array( $this, 'add_js_nonces' ) );

		// AJAX
		add_action( 'wp_ajax_as3cfpro_update_sidebar', array( $this, 'ajax_update_sidebar' ) );
		add_action( 'wp_ajax_as3cfpro_get_status', array( $this, 'ajax_get_status' ) );
	}

	/**
	 * Register a tool.
	 *
	 * @param Tool   $tool
	 * @param string $context
	 *
	 * @return bool
	 */
	public function register_tool( Tool $tool, $context = 'modal' ) {
		if ( ! empty( $this->tools[ $context ][ $tool->get_tool_key() ] ) ) {
			return false;
		}

		$this->tools[ $context ][ $tool->get_tool_key() ] = $tool;

		$tool->priority( $this->get_tools_count() )->init();

		return true;
	}

	/**
	 * Get tool.
	 *
	 * @param string $name
	 *
	 * @return bool|Tool
	 */
	public function get_tool( $name ) {
		foreach ( $this->tools as $context ) {
			if ( array_key_exists( $name, $context ) ) {
				return $context[ $name ];
			}
		}

		return false;
	}

	/**
	 * Render the Pro sidebar with tools
	 */
	public function render_sidebar() {
		$this->as3cf->render_view( 'sidebar' );
	}

	/**
	 * Add the nonces to the Javascript
	 *
	 * @param array $js_nonces
	 *
	 * @return array
	 */
	public function add_js_nonces( $js_nonces ) {
		$js_nonces['update_sidebar'] = wp_create_nonce( 'update-sidebar' );
		$js_nonces['get_status']     = wp_create_nonce( 'get-status' );

		return $js_nonces;
	}

	/**
	 * Load assets.
	 */
	public function load_assets() {
		$this->as3cf->enqueue_script( 'as3cf-pro-sidebar', 'assets/js/pro/sidebar', array(
			'jquery',
			'as3cf-pro-script',
		) );

		wp_localize_script( 'as3cf-pro-sidebar', 'as3cfSidebarTools', $this->get_tools_status() );
	}

	/**
	 * Get tool's status.
	 *
	 * @return array
	 */
	protected function get_tools_status() {
		$data = array();

		foreach ( $this->get_all_tools() as $tool ) {
			$data[ $tool->get_tab() ][ $tool->get_tool_key() ] = $tool->get_status();
		}

		return $data;
	}

	/**
	 * AJAX callback for updating the sidebar.
	 */
	public function ajax_update_sidebar() {
		check_ajax_referer( 'update-sidebar', 'nonce' );

		$tools        = array();
		$calling_tool = $this->as3cf->filter_input( 'tool', INPUT_POST, FILTER_SANITIZE_STRING ); // input var ok;

		foreach ( $this->get_tools_by_context( 'modal' ) as $name => $tool ) {
			$tools[ $name ]['block'] = $tool->get_sidebar_block();

			if ( $name === $calling_tool ) {
				// Only refresh notices for current tool
				$tools[ $name ]['notices'] = $tool->get_error_notices();
			}
		}

		wp_send_json_success( $tools );
	}

	/**
	 * Get tools count.
	 *
	 * @return int
	 */
	public function get_tools_count() {
		$count = 0;

		foreach ( $this->tools as $context ) {
			$count += count( $context );
		}

		return $count;
	}

	/**
	 * Get tools by context.
	 *
	 * @param string $context
	 *
	 * @return array
	 */
	public function get_tools_by_context( $context = 'modal' ) {
		if ( ! empty( $this->tools[ $context ] ) ) {
			return $this->tools[ $context ];
		}

		return array();
	}

	/**
	 * Get all tools.
	 *
	 * @return array
	 */
	public function get_all_tools() {
		$tools = array();

		foreach ( $this->tools as $context ) {
			foreach ( $context as $key => $tool ) {
				$tools[ $key ] = $tool;
			}
		}

		return $tools;
	}

	/**
	 * Ajax update status.
	 */
	public function ajax_get_status() {
		check_ajax_referer( 'get-status', 'nonce' );

		$data = array();

		foreach ( $this->get_all_tools() as $tool ) {
			$data[ $tool->get_tool_key() ] = $tool->get_status();
		}

		$this->as3cf->end_ajax( $data );
	}

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * class via the `new` operator from outside of this class.
	 */
	protected function __construct() {}

	/**
	 * As this class is a singleton it should not be clone-able.
	 */
	protected function __clone() {}

	/**
	 * As this class is a singleton it should not be able to be unserialized.
	 */
	protected function __wakeup() {}
}