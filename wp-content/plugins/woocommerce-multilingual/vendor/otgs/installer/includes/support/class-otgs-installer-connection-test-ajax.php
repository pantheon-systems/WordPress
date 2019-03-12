<?php

class OTGS_Installer_Connection_Test_Ajax {

	const ACTION = 'otgs_installer_test_connection';

	private $connection_test;

	public function __construct( OTGS_Installer_Connection_Test $connection_test ) {
		$this->connection_test = $connection_test;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'test_connection' ) );
	}

	public function test_connection() {
		if ( $this->is_valid_request() ) {
			$type       = filter_var( $_POST['type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$repository = filter_var( $_POST['repository'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$method     = 'get_' . $type . '_status';

			if ( $this->connection_test->{$method}( $repository ) ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return isset( $_POST['nonce'], $_POST['type'] ) && wp_verify_nonce( $_POST['nonce'], self::ACTION );
	}
}