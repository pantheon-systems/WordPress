<?php
/**
 * Plugin Name: Native PHP Sessions for WordPress
 * Version: 0.6.6
 * Description: Offload PHP's native sessions to your database for multi-server compatibility.
 * Author: Pantheon
 * Author URI: https://www.pantheon.io/
 * Plugin URI: https://wordpress.org/plugins/wp-native-php-sessions/
 * Text Domain: pantheon-sessions
 * Domain Path: /languages
 **/

use Pantheon_Sessions\Session;

define( 'PANTHEON_SESSIONS_VERSION', '0.6.6' );

class Pantheon_Sessions {

	private static $instance;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Pantheon_Sessions;
			self::$instance->load();
		}

	}

	/**
	 * Load the plugin
	 */
	private function load() {

		$this->define_constants();
		$this->require_files();

		if ( PANTHEON_SESSIONS_ENABLED ) {

			$this->setup_database();
			$this->set_ini_values();
			$this->initialize_session_override();
			add_action( 'set_logged_in_cookie', array( __CLASS__, 'action_set_logged_in_cookie' ), 10, 4 );
			add_action( 'clear_auth_cookie', array( __CLASS__, 'action_clear_auth_cookie' ) );
		}
	}

	/**
	 * Define our constants
	 */
	private function define_constants() {

		if ( ! defined( 'PANTHEON_SESSIONS_ENABLED' ) ) {
			define( 'PANTHEON_SESSIONS_ENABLED', 1 );
		}

	}

	/**
	 * Load required files
	 */
	private function require_files() {

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once dirname( __FILE__ ) . '/inc/class-cli-command.php';
		}

		require_once dirname( __FILE__ ) . '/callbacks.php';

		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/inc/class-admin.php';
			$this->admin = Pantheon_Sessions\Admin::get_instance();
		}

	}

	/**
	 * Set the PHP ini settings for the session implementation to work properly
	 *
	 * Largely adopted from Drupal 7's implementation
	 */
	private function set_ini_values() {

		// If the user specifies the cookie domain, also use it for session name.
		if ( defined( 'COOKIE_DOMAIN' ) && constant( 'COOKIE_DOMAIN' ) ) {
			$session_name = $cookie_domain = constant( 'COOKIE_DOMAIN' );
		} else {
			$session_name = parse_url( home_url(), PHP_URL_HOST );
			$cookie_domain = ltrim( $session_name, '.' );
			// Strip leading periods, www., and port numbers from cookie domain.
			if ( strpos( $cookie_domain, 'www.' ) === 0 ) {
				$cookie_domain = substr( $cookie_domain, 4 );
			}
			$cookie_domain = explode( ':', $cookie_domain );
			$cookie_domain = '.' . $cookie_domain[0];
		}

		// Per RFC 2109, cookie domains must contain at least one dot other than the
		// first. For hosts such as 'localhost' or IP Addresses we don't set a cookie domain.
		if ( count( explode( '.', $cookie_domain ) ) > 2 && ! is_numeric( str_replace( '.', '', $cookie_domain ) ) ) {
			ini_set( 'session.cookie_domain', $cookie_domain );
		}
		// To prevent session cookies from being hijacked, a user can configure the
		// SSL version of their website to only transfer session cookies via SSL by
		// using PHP's session.cookie_secure setting. The browser will then use two
		// separate session cookies for the HTTPS and HTTP versions of the site. So we
		// must use different session identifiers for HTTPS and HTTP to prevent a
		// cookie collision.
		if ( is_ssl() ) {
			ini_set( 'session.cookie_secure', TRUE );
		}
		$prefix = ini_get( 'session.cookie_secure' ) ? 'SSESS' : 'SESS';

		session_name( $prefix . substr( hash( 'sha256', $session_name ), 0, 32 ) );

		// Use session cookies, not transparent sessions that puts the session id in
		// the query string.
		ini_set( 'session.use_cookies', '1' );
		ini_set( 'session.use_only_cookies', '1' );
		ini_set( 'session.use_trans_sid', '0' );
		// Don't send HTTP headers using PHP's session handler.
		// An empty string is used here to disable the cache limiter.
		ini_set( 'session.cache_limiter', '' );
		// Use httponly session cookies. Limits use by JavaScripts
		ini_set( 'session.cookie_httponly', '1' );
		// Get cookie lifetime from filters so you can put your custom lifetime 
		ini_set( 'session.cookie_lifetime', (int) apply_filters( 'pantheon_session_expiration', 0 ) );

	}

	/**
	 * Override the default sessions implementation with our own
	 *
	 * Largely adopted from Drupal 7's implementation
	 */
	private function initialize_session_override() {
		session_set_save_handler( '_pantheon_session_open', '_pantheon_session_close', '_pantheon_session_read', '_pantheon_session_write', '_pantheon_session_destroy', '_pantheon_session_garbage_collection' );
		// Close the session before $wpdb destructs itself
		add_action( 'shutdown', 'session_write_close', 999, 0 );
		require_once dirname( __FILE__ ) . '/inc/class-session.php';
	}

	/**
	 * Set up the database
	 */
	private function setup_database() {
		global $wpdb, $table_prefix;

		$table_name = "{$table_prefix}pantheon_sessions";
		$wpdb->pantheon_sessions = $table_name;
		$wpdb->tables[] = 'pantheon_sessions';

		if ( get_option( 'pantheon_session_version' ) ) {
			return;
		}

		$create_statement = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			`user_id` bigint(20) unsigned NOT NULL COMMENT 'The user_id corresponding to a session, or 0 for anonymous user.',
			`session_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'A session ID. The value is generated by plugin''s session handlers.',
			`secure_session_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Secure session ID. The value is generated by plugin''s session handlers.',
			`ip_address` varchar(128) NOT NULL DEFAULT '' COMMENT 'The IP address that last used this session ID.',
			`datetime` datetime DEFAULT NULL COMMENT 'The datetime value when this session last requested a page. Old records are purged by PHP automatically.',
			`data` blob COMMENT 'The serialized contents of \$_SESSION, an array of name/value pairs that persists across page requests by this session ID. Plugin loads \$_SESSION from here at the start of each request and saves it at the end.',
			KEY `session_id` (`session_id`),
			KEY `secure_session_id` (`secure_session_id`)
		)";
		$wpdb->query( $create_statement );
		update_option( 'pantheon_session_version', PANTHEON_SESSIONS_VERSION );

	}

	public static function action_set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id ) {
		$session_id = session_id();
		if ( $session_id && $session = Session::get_by_sid( $session_id ) ) {
			$session->set_user_id( $user_id );
		}
	}

	public static function action_clear_auth_cookie() {
		$session_id = session_id();
		if ( $session_id && $session = Session::get_by_sid( $session_id ) ) {
			$session->set_user_id( 0 );
		}
	}

	/**
	 * Force the plugin to be the first loaded
	 *
	 */
	static public function force_first_load()
	{

		$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );
				update_option( 'active_plugins', $plugins );
			}
		}

		return;
	}

}

/**
 * Release the kraken!
 */
function Pantheon_Sessions() {
	return Pantheon_Sessions::get_instance();
}

add_action( 'activated_plugin', 'Pantheon_Sessions::force_first_load');

Pantheon_Sessions();
