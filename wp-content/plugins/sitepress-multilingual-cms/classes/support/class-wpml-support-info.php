<?php
/**
 * @author OnTheGo Systems
 */
class WPML_Support_Info {
	/** @var wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}


	public function is_suhosin_active() {
		return extension_loaded( 'suhosin' );
	}

	public function eval_disabled_by_suhosin() {
		return (bool) ini_get( 'suhosin.executor.disable_eval' );
	}

	public function get_max_execution_time() {
		return ini_get( 'max_execution_time' );
	}

	public function get_max_input_vars() {
		return ini_get( 'max_input_vars' );
	}

	public function get_php_memory_limit() {
		return ini_get('memory_limit');
	}

	public function get_memory_usage() {
		return $this->format_size_units( memory_get_usage() );
	}

	public function get_php_version() {
		return PHP_VERSION;
	}

	public function get_wp_memory_limit() {
		return constant('WP_MEMORY_LIMIT');
	}

	public function get_wp_max_memory_limit() {
		return constant('WP_MAX_MEMORY_LIMIT');
	}

	public function get_wp_multisite() {
		return is_multisite();
	}

	public function get_wp_version() {
		return $GLOBALS['wp_version'];
	}

	public function is_memory_less_than( $reference, $memory ) {
		if ( (int) $reference === - 1 ) {
			return false;
		}

		$reference_in_bytes = $this->return_bytes( $reference );
		$memory_in_bytes    = $this->return_bytes( $memory );

		return $memory_in_bytes < $reference_in_bytes;
	}

	public function is_version_less_than( $reference, $version ) {
		return version_compare( $version, $reference, '<' );
	}

	public function is_utf8mb4_charset_supported() {
		return $this->wpdb->has_cap( 'utf8mb4' );
	}

	private function return_bytes( $val ) {
		$val  = trim( $val );

		$exponents = array(
			'k' => 1,
			'm' => 2,
			'g' => 3,
		);

		$last = strtolower( substr( $val, - 1 ) );

		if ( ! is_numeric( $last ) ) {
			$val = (int) substr( $val, 0, - 1 );

			if ( array_key_exists( $last, $exponents ) ) {
				$val *= pow( 1024, $exponents[ $last ] );
			}
		} else {
			$val = (int) $val;
		}

		return $val;
	}

	private function format_size_units( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
		} elseif ( $bytes > 1 ) {
			$bytes .= ' bytes';
		} elseif ( $bytes === 1 ) {
			$bytes .= ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}
}
