<?php
/**
 * Individual session object.
 *
 * @package WPNPS
 */

namespace Pantheon_Sessions;

/**
 * Individual session object.
 */
class Session {

	/**
	 * Any sessions stored statically.
	 *
	 * @var array
	 */
	private static $sessions = array();

	/**
	 * Any secure sessions stored statically.
	 *
	 * @var array
	 */
	private static $secure_sessions = array();

	/**
	 * Session id.
	 *
	 * @var string
	 */
	private $sid;

	/**
	 * Session data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Session user id.
	 *
	 * @var integer
	 */
	private $user_id;

	/**
	 * Get a session based on its ID.
	 *
	 * @param string $sid Session id.
	 * @return Session|false
	 */
	public static function get_by_sid( $sid ) {
		global $wpdb;

		if ( ! $sid ) {
			return false;
		}

		$wpdb = self::restore_wpdb_if_null( $wpdb );

		$column_name = self::get_session_id_column();
		$table_name  = $wpdb->pantheon_sessions;
		// phpcs:ignore
		$session_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE {$column_name}=%s", $sid ) );
		if ( ! $session_row ) {
			return false;
		}

		return new Session( $session_row->$column_name, $session_row->data, $session_row->user_id );
	}

	/**
	 * Create a database entry for this session
	 *
	 * @param string $sid Session id.
	 * @return Session
	 */
	public static function create_for_sid( $sid ) {
		global $wpdb;

		$wpdb = self::restore_wpdb_if_null( $wpdb );

		$insert_data = array(
			'session_id' => $sid,
			'user_id'    => (int) get_current_user_id(),
		);
		if ( function_exists( 'is_ssl' ) && is_ssl() ) {
			$insert_data['secure_session_id'] = $sid;
		}
		$wpdb->insert( $wpdb->pantheon_sessions, $insert_data );
		return self::get_by_sid( $sid );
	}

	/**
	 * Instantiates a session object.
	 *
	 * @param string  $sid     Session id.
	 * @param mixed   $data    Any session data.
	 * @param integer $user_id User id for the session.
	 */
	private function __construct( $sid, $data, $user_id ) {
		$this->sid     = $sid;
		$this->data    = $data;
		$this->user_id = $user_id;
	}

	/**
	 * Get this session's ID
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->sid;
	}

	/**
	 * Get this session's data
	 *
	 * @return mixed
	 */
	public function get_data() {
		return maybe_unserialize( $this->data );
	}

	/**
	 * Get this session's user id.
	 *
	 * @return integer
	 */
	public function get_user_id() {
		return (int) $this->user_id;
	}

	/**
	 * Set the user id for this session.
	 *
	 * @param integer $user_id User id.
	 */
	public function set_user_id( $user_id ) {
		global $wpdb;

		$wpdb = self::restore_wpdb_if_null( $wpdb );

		$this->user_id = (int) $user_id;
		$wpdb->update(
			$wpdb->pantheon_sessions,
			array(
				'user_id' => $this->user_id,
			),
			array( self::get_session_id_column() => $this->get_id() )
		);
	}

	/**
	 * Set the session's data
	 *
	 * @param mixed $data Session data.
	 */
	public function set_data( $data ) {
		global $wpdb;

		if ( $data === $this->get_data() ) {
			return;
		}

		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}

		$wpdb = self::restore_wpdb_if_null( $wpdb );

		$wpdb->update(
			$wpdb->pantheon_sessions,
			array(
				'user_id'    => (int) get_current_user_id(),
				'datetime'   => gmdate( 'Y-m-d H:i:s' ),
				'ip_address' => self::get_client_ip_server(),
				'data'       => maybe_serialize( $data ),
			),
			array( self::get_session_id_column() => $this->get_id() )
		);

		$this->data = maybe_serialize( $data );
	}

	/**
	 * Get the clients ip address
	 *
	 * @return string
	 */
	public static function get_client_ip_server() {
		// Set default.
		$ip_address = apply_filters( 'pantheon_sessions_client_ip_default', '127.0.0.1' );
		$ip_source  = null;

		$keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		$ip_filter_flags = apply_filters( 'pantheon_sessions_client_ip_filter_flags', FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE );

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $_SERVER )
				&& $_SERVER[ $key ]
			) {
				$_ip_address = $_SERVER[ $key ];

				if ( false !== strpos( $_ip_address, ',' ) ) {
					$_ip_address = trim( strstr( $_ip_address, ',', true ) );
				}

				if ( false === filter_var( $_ip_address, FILTER_VALIDATE_IP, $ip_filter_flags ) ) {
					continue;
				}

				$ip_address = $_ip_address;
				$ip_source  = $key;
				break;
			}
		}

		return apply_filters(
			'pantheon_sessions_client_ip',
			preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip_address ),
			$ip_source
		);
	}

	/**
	 * Destroy this session
	 */
	public function destroy() {
		global $wpdb;

		$wpdb = self::restore_wpdb_if_null( $wpdb );

		$wpdb->delete( $wpdb->pantheon_sessions, array( self::get_session_id_column() => $this->get_id() ) );

		// Reset $_SESSION to prevent a new session from being started.
		$_SESSION = array();

		$this->delete_cookies();

	}

	/**
	 * Restores $wpdb database connection if missing.
	 *
	 * @param mixed $wpdb Existing global.
	 * @return object
	 */
	public static function restore_wpdb_if_null( $wpdb ) {
		if ( $wpdb instanceof \wpdb ) {
			return $wpdb;
		}
		$dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
		$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
		$dbname     = defined( 'DB_NAME' ) ? DB_NAME : '';
		$dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

		return new \wpdb( $dbuser, $dbpassword, $dbname, $dbhost );
	}

	/**
	 * Delete session cookies
	 */
	private function delete_cookies() {

		// Cookies don't exist on CLI.
		if ( self::is_cli() ) {
			return;
		}

		$session_name = session_name();
		$cookies      = array(
			$session_name,
			substr( $session_name, 1 ),
			'S' . $session_name,
		);

		foreach ( $cookies as $cookie_name ) {

			if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
				continue;
			}

			$params = session_get_cookie_params();
			setcookie( $cookie_name, '', $_SERVER['REQUEST_TIME'] - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly'] );
			unset( $_COOKIE[ $cookie_name ] );
		}

	}

	/**
	 * Is this request via CLI?
	 *
	 * @return bool
	 */
	private static function is_cli() {
		return 'cli' === PHP_SAPI;
	}

	/**
	 * Get the session ID column name
	 *
	 * @return string
	 */
	private static function get_session_id_column() {
		if ( is_ssl() ) {
			return 'secure_session_id';
		} else {
			return 'session_id';
		}
	}

}
