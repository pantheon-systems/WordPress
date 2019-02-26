<?php

class WPML_WP_Cron_Check {

	const TRANSIENT_NAME = 'wpml_cron_check';

	/** @var WPML_PHP_Functions $php_functions */
	private $php_functions;

	public function __construct( WPML_PHP_Functions $php_functions ) {
		$this->php_functions = $php_functions;
	}

	/** @return bool */
	public function verify() {
		if ( $this->is_doing_cron() ) {
			return true;
		}

		if ( $this->php_functions->constant( 'DISABLE_WP_CRON' ) ) {
			return false;
		}

		$is_on_from_transient = get_transient( self::TRANSIENT_NAME );

		if ( false !== $is_on_from_transient ) {
			return (bool) $is_on_from_transient;
		}

		$is_on   = 1;
		$request = wp_remote_get( site_url( 'wp-cron.php' ) );

		if ( is_wp_error( $request ) ) {
			$is_on = 0;
		} elseif ( $request['response']['code'] !== 200 ) {
			$is_on = 0;
		}

		set_transient( self::TRANSIENT_NAME, $is_on, 12 * HOUR_IN_SECONDS );
		return (bool) $is_on;
	}

	/** @return bool */
	public function is_doing_cron() {
		return (bool) $this->php_functions->constant( 'DOING_CRON' );
	}
}