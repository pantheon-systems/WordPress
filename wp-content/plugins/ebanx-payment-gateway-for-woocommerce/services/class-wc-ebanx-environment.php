<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enviroment data to be logged
 */
class WC_EBANX_Environment {
	/**
	 * Environment platform
	 *
	 * @var $platform
	 */
	public $platform;

	/**
	 * Interpreter
	 *
	 * @var $interpreter
	 */
	public $interpreter;

	/**
	 * Server software
	 *
	 * @var $web_server
	 */
	public $web_server;

	/**
	 * Database software
	 *
	 * @var $database_server
	 */
	public $database_server;

	/**
	 * Operating system
	 *
	 * @var $operating_system
	 */
	public $operating_system;

	/**
	 * WC_EBANX_Environment constructor
	 */
	public function __construct() {
		global $wp_version;
		$platform       = new stdClass();
		$platform->name = 'WordPress';

		if ( isset( $wp_version ) ) {
			$platform->version = $wp_version;
		} else {
			$platform->version = 'Unknown';
			$platform->error   = 'Unable to detect the version number. Make sure you are calling this inside WordPress.';
		}

		$this->platform                = $platform;
		$interpreter                   = new stdClass();
		$interpreter->name             = 'PHP';
		$interpreter->version          = PHP_VERSION;
		$this->interpreter             = $interpreter;

		if ( PHP_SAPI !== 'cgi-fcgi' && PHP_SAPI !== 'cli' ) {
			$web_server_information_string = filter_input( INPUT_SERVER, 'SERVER_SOFTWARE' );
			$web_server_value_parts_array  = explode( ' ', $web_server_information_string );
			$web_server_parts              = explode( '/', $web_server_value_parts_array[0] );
			$web_server                    = new stdClass();
			$web_server->name              = str_replace( '-', ' ', $web_server_parts[0] );
			$web_server->version           = $web_server_parts[1];
		} else {
			if ( isset( $_SERVER ) && isset( $_SERVER['SERVER_NAME'] ) ) {
				$web_server                    = new stdClass();
				$web_server->name              = sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
				$web_server->version           = PHP_SAPI;
			}
		}

		$this->web_server = $web_server;

		$database_server = new stdClass();
		// @codingStandardsIgnoreStart
		$database        = new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
		if ( ! mysqli_connect_errno() ) {
			// @codingStandardsIgnoreEnd
			if ( strpos( $database->server_info, 'MariaDB' ) !== false ) {
				$database_server->name = 'MariaDB';
			} else {
				$database_server->name = 'MySQL';
			}
			$result                   = $database->query( 'SELECT version() AS version' );
			$row                      = $result->fetch_assoc();
			$database_server->version = $row['version'];
		} else {
			$database_server->name    = 'Unconnected';
			$database_server->version = 'Unknown';
			$database_server->error   = 'Unable to connect to the database. Make sure you are calling this inside WordPress.';
		}

		$this->database_server = $database_server;

		$operating_system          = new stdClass();
		$operating_system->name    = PHP_OS;
		$operating_system->version = $this->extract_version_number_from( php_uname( 'v' ) );
		$this->operating_system    = $operating_system;
	}

	/**
	 * Extracts version number from a string
	 *
	 * @param string $haystack
	 */
	public function extract_version_number_from( $haystack ) {
		preg_match( '/((\d)+(\.|\D))+/', $haystack, $version_candidates_array );

		if ( count( $version_candidates_array ) > 0 && strlen( $version_candidates_array[0] ) > 0 ) {
			$version_candidates_array[0] = str_replace( '.', '_', $version_candidates_array[0] );
			$version_candidates_array[0] = preg_replace( '/[\W]/', '', $version_candidates_array[0] );
			$version_candidates_array[0] = str_replace( '_', '.', $version_candidates_array[0] );
			$version                     = $version_candidates_array[0];
		} else {
			$version = 'Unknown';
		}

		return $version;
	}

	/**
	 * Stringifies this object
	 */
	public function __toString() {
		return json_encode( $this, JSON_PRETTY_PRINT );
	}
}
