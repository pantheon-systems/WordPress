<?php
/**
 * Debug Logger
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Strong_Log' ) ) :

class Strong_Log {

	public $name;

	public $filename;

	public $action;

	public function __construct( $name ) {
		$this->set_name( $name );
		$this->set_filename();
		$this->set_log_action();
		$this->add_actions();
	}

	private function set_name( $name = 'main' ) {
		$this->name = $name;
	}

	private function set_filename() {
		$this->filename = apply_filters( "strong_log_{$this->name}_filename",
			str_replace( '_', '-', "strong-{$this->name}-debug.log" ) );
	}

	private function set_log_action() {
		$this->action = "strong_log_{$this->name}";
	}

	public function add_actions() {
		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'shutdown', array( $this, 'on_shutdown' ), 20 );
	}

	public function init() {
		// TODO Make admin check optional.
		if ( $this->is_enabled() && current_user_can( 'administrator' ) ) {
			add_action( $this->action, array( $this, 'debug_log' ), 10, 3 );
		}
	}

	private function is_enabled() {
		$options    = get_option( "wpmtst_{$this->name}_debug" );
		$is_enabled = ( isset( $options['log'] ) && $options['log'] );

		return apply_filters( $this->action, $is_enabled );
	}

	public function get_log_file_path() {
		return $this->get_log_file_base( 'basedir' ) . $this->filename;
	}

	public function get_log_file_url() {
		return $this->get_log_file_base( 'baseurl' ) . $this->filename;
	}

	public function get_log_file_base( $base = 'basedir' ) {
		$upload_dir = wp_upload_dir();

		if ( isset( $upload_dir[ $base ] ) ) {
			$log_file_base = $upload_dir[ $base ];
		} else {
			$log_file_base = $upload_dir['basedir'];
		}

		return trailingslashit( $log_file_base );
	}

	/**
	 * Debug log entries.
	 *
	 * @param $entry
	 * @param string $label
	 * @param string $function
	 */
	public function debug_log( $entry, $label = '', $function = '' ) {
		$this->log( $entry, $label, $function );
	}

	/**
	 * Disable debug logging on shutdown.
	 */
	public function on_shutdown() {
		if ( get_transient( $this->action ) ) {
			do_action( $this->action, str_repeat( '-', 50 ), '', current_filter() );
			delete_transient( $this->action );
		}
	}

	/**
	 * Generic logging function.
	 *
	 * @param array|string $data
	 * @param string $label
	 * @param string $function
	 */
	public function log( $data, $label = '', $function = '' )  {

		$entry = '[' . date('Y-m-d H:i:s') . ']';

		if ( wp_doing_ajax() ) {
			$entry .= ' | DOING_AJAX';
		}

		if ( $function ) {
			$entry .= ' | FN: ' . $function;
		}

		$entry .= ' | ';

		if ( $label ) {
			$entry .= $label . ' = ';
		}

		if ( is_array( $data ) || is_object( $data ) ) {
			$entry .= print_r( $data, true );
		} elseif ( is_bool( $data ) ) {
			$entry .= ( $entry ? 'true' : 'false' ) . PHP_EOL;
		} else {
			$entry .= $data . PHP_EOL;
		}

		//$entry .= PHP_EOL;

		error_log( $entry, 3, $this->get_log_file_path() );

		set_transient( $this->action, true );

	}

}

endif;
