<?php
/**
 * Plugin Name: Native PHP Sessions for WordPress
 * Version: 1.4.3
 * Description: Offload PHP's native sessions to your database for multi-server compatibility.
 * Author: Pantheon
 * Author URI: https://www.pantheon.io/
 * Plugin URI: https://wordpress.org/plugins/wp-native-php-sessions/
 * Text Domain: wp-native-php-sessions
 *
 * @package WPNPS
 **/

use Pantheon_Sessions\Session;

define( 'PANTHEON_SESSIONS_VERSION', '1.4.3' );

/**
 * Main controller class for the plugin.
 */
class Pantheon_Sessions {
	private const BAK_PANTHEON_SESSIONS_TABLE = 'bak_pantheon_sessions';
	private const TEMP_PANTHEON_SESSIONS_TABLE = 'temp_pantheon_sessions';
	private const PANTHEON_SESSIONS_TABLE = 'pantheon_sessions';

	/**
	 * Copy of the singleton instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * The admin instance.
	 *
	 * @var \Pantheon_Sessions\Admin
	 */
	private $admin;

	/**
	 * Gets a copy of the singleton instance.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Pantheon_Sessions();
			self::$instance->load();
		}
		return self::$instance;
	}

	/**
	 * Load the plugin.
	 */
	private function load() {

		if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		$this->define_constants();
		$this->require_files();

		if ( PANTHEON_SESSIONS_ENABLED ) {

			$this->setup_database();
			$this->initialize_session_override();
			$this->set_ini_values();
			add_action( 'set_logged_in_cookie', [ __CLASS__, 'action_set_logged_in_cookie' ], 10, 4 );
			add_action( 'clear_auth_cookie', [ __CLASS__, 'action_clear_auth_cookie' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_dismiss_notice', [ $this, 'dismiss_notice' ] );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'notices', plugins_url( '/assets/js/notices.js', __FILE__ ), [ 'jquery' ], PANTHEON_SESSIONS_VERSION, true );
	}

	/**
	 * Dismiss the notice when the button is clicked.
	 */
	public function dismiss_notice() {
		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'notice_dismissed', true );
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
			require_once __DIR__ . '/inc/class-cli-command.php';
		}

		if ( is_admin() ) {
			require_once __DIR__ . '/inc/class-admin.php';
			$this->admin = Pantheon_Sessions\Admin::get_instance();
		}
	}

	/**
	 * Set the PHP ini settings for the session implementation to work properly
	 *
	 * Largely adopted from Drupal 7's implementation
	 */
	private function set_ini_values() {

		if ( headers_sent() ) {
			return;
		}

		// If the user specifies the cookie domain, also use it for session name.
		if ( defined( 'COOKIE_DOMAIN' ) && constant( 'COOKIE_DOMAIN' ) ) {
			$cookie_domain = constant( 'COOKIE_DOMAIN' );
			$session_name  = $cookie_domain;
		} else {
			$session_name  = parse_url( home_url(), PHP_URL_HOST );
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
			ini_set( 'session.cookie_secure', true );
		}
		$prefix = ini_get( 'session.cookie_secure' ) ? 'SSESS' : 'SESS';

		session_name( $prefix . substr( hash( 'sha256', $session_name ), 0, 32 ) );

		// Use session cookies, not transparent sessions that puts the session id in
		// the query string.
		$use_cookies = '1';
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$use_cookies = '0';
		}
		ini_set( 'session.use_cookies', $use_cookies );
		ini_set( 'session.use_only_cookies', '1' );
		ini_set( 'session.use_trans_sid', '0' );
		// Don't send HTTP headers using PHP's session handler.
		// An empty string is used here to disable the cache limiter.
		ini_set( 'session.cache_limiter', '' );
		// Use httponly session cookies. Limits use by JavaScripts.
		ini_set( 'session.cookie_httponly', '1' );
		// Get cookie lifetime from filters so you can put your custom lifetime.
		ini_set( 'session.cookie_lifetime', (int) apply_filters( 'pantheon_session_expiration', 0 ) );
	}

	/**
	 * Override the default sessions implementation with our own
	 *
	 * Largely adopted from Drupal 7's implementation
	 */
	private function initialize_session_override() {
		require_once __DIR__ . '/inc/class-session.php';
		require_once __DIR__ . '/inc/class-session-handler.php';
		$session_handler = new Pantheon_Sessions\Session_Handler();
		if ( PHP_SESSION_ACTIVE !== session_status() ) {
			// Check if headers have already been sent.
			if ( headers_sent( $file, $line ) ) {
				// Output a friendly error message if headers are already sent.
				trigger_error(
					sprintf(
						/* translators: %1s: File path, %2d: Line number */
						__( "Oops! The wp-native-php-sessions plugin couldn't start the session because output has already been sent. This might be caused by PHP throwing errors. Please check the code in %1s on line %2d.", 'wp-native-php-sessions' ),
						esc_html( $file ),
						esc_html( $line )
					),
					E_USER_WARNING
				);
			} else {
				session_set_save_handler( $session_handler, false );
			}
		}
		// Close the session before $wpdb destructs itself.
		add_action( 'shutdown', 'session_write_close', 999, 0 );
	}

	/**
	 * Set up the database
	 */
	private function setup_database() {
		global $wpdb, $table_prefix;

		$table_name              = "{$table_prefix}pantheon_sessions";
		$wpdb->pantheon_sessions = $table_name;
		$wpdb->tables[]          = self::PANTHEON_SESSIONS_TABLE;

		if ( get_option( 'pantheon_session_version' ) ) {
			return;
		}

		$create_statement = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'An auto-incrementing id to serve as an index.',
			`user_id` bigint(20) unsigned NOT NULL COMMENT 'The user_id corresponding to a session, or 0 for anonymous user.',
			`session_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'A session ID. The value is generated by plugin''s session handlers.',
			`secure_session_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Secure session ID. The value is generated by plugin''s session handlers.',
			`ip_address` varchar(128) NOT NULL DEFAULT '' COMMENT 'The IP address that last used this session ID.',
			`datetime` datetime DEFAULT NULL COMMENT 'The datetime value when this session last requested a page. Old records are purged by PHP automatically.',
			`data` mediumblob COMMENT 'The serialized contents of \$_SESSION, an array of name/value pairs that persists across page requests by this session ID. Plugin loads \$_SESSION from here at the start of each request and saves it at the end.',
			KEY `session_id` (`session_id`),
			KEY `secure_session_id` (`secure_session_id`)
		)";
		// phpcs:ignore
		$wpdb->query( $create_statement );
		update_option( 'pantheon_session_version', PANTHEON_SESSIONS_VERSION );
	}

	/**
	 * Sets the user id value to the session when the user logs in.
	 *
	 * @param string  $logged_in_cookie Cooke name.
	 * @param integer $expire           When the cookie is set to expire.
	 * @param integer $expiration       When the cookie is set to expire.
	 * @param integer $user_id          Id for the logged-in user.
	 */
	public static function action_set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id ) {
		$session = Session::get_by_sid( session_id() );
		if ( $session ) {
			$session->set_user_id( $user_id );
		}
	}

	/**
	 * Clears the user id value from the session when the user logs out.
	 */
	public static function action_clear_auth_cookie() {
		$session = Session::get_by_sid( session_id() );
		if ( $session ) {
			$session->set_user_id( 0 );
		}
	}

	/**
	 * Force the plugin to be the first loaded
	 */
	public static function force_first_load() {
		$path    = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
		$plugins = get_option( 'active_plugins' );
		if ( $plugins ) {
			$key = array_search( $path, $plugins, true );
			if ( $key ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );
				update_option( 'active_plugins', $plugins );
			}
		}
	}

	/**
	 * Checks whether primary keys were set and notifies users if not.
	 */
	public static function check_native_primary_keys() {
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . self::PANTHEON_SESSIONS_TABLE;
		$old_table = $wpdb->get_blog_prefix() . self::BAK_PANTHEON_SESSIONS_TABLE;
		$query = "SHOW KEYS FROM {$table_name} WHERE key_name = 'PRIMARY';";
		$is_pantheon = isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ? true : false;
		$wp_cli_cmd = $is_pantheon ? 'terminus wp &lt;site&gt;.&lt;env&gt; -- ' : 'wp ';
		$cli_add_index = $wp_cli_cmd . 'pantheon session add-index';
		$key_existence = $wpdb->get_results( $query );
		$user_id = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'notice_dismissed', true );

		if ( empty( $key_existence ) && ! $dismissed ) {
			// If the key doesn't exist, recommend remediation.
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					echo esc_html__( 'Your PHP Native Sessions table is missing a primary key. This can cause performance issues for high-traffic sites.', 'wp-native-php-sessions' );
					?>
				</p>
				<p>
					<?php
					// TODO: Integrate the notice into the Health Check page. See https://getpantheon.atlassian.net/browse/CMSP-745.
					// Translators: %s is the add-index command.
					echo wp_kses_post( sprintf( __( 'If you\'d like to resolve this, please use this WP CLI command: %s and verify that the process completes successfully. Otherwise, you may dismiss this notice.', 'wp-native-php-sessions' ), "<code>$cli_add_index</code>" ) );
					?>
				</p>
			</div>
			<?php
		}

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s',
		$wpdb->esc_like( $old_table ) );

		// Check for table existence and delete if present.
		if ( $wpdb->get_var( $query ) == $old_table ) {
			$cli_key_finalize = $wp_cli_cmd . 'pantheon session primary-key-finalize';
			$cli_key_revert = $wp_cli_cmd . 'pantheon session primary-key-revert';

			// If an old table exists but has not been removed, suggest doing so.
			?>
			<div class="notice notice-error">
				<p>
					<?php
					// Translators: 1: the primary-key-finalize command, 2: the primary-key-revert command.
					echo wp_kses_post( sprintf( __( 'An old version of the PHP Native Sessions table is detected. When testing is complete, run %1$s to clean up old data, or run %2$s if there were issues.', 'wp-native-php-sessions' ), "<code>$cli_key_finalize</code>", "<code>$cli_key_revert</code>" ) );
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Set id as primary key in the Native PHP Sessions plugin table.
	 *
	 * @param int $start_position Site index to begin from.
	 */
	public function add_index( $start_position ) {
		global $wpdb;
		if ( ! is_multisite() ) {
			$this->safe_output( __( 'Single site detected. Beginning processing... \n', 'wp-native-php-sessions' ), 'log' );

			$this->add_single_index( $wpdb->prefix );

			$this->safe_output( __( 'Operation complete, please verify that your site is working as expected. When ready, run wp pantheon session primary-key-finalize to clean up old data, or run wp pantheon session primary-key-revert if there were issues.', 'wp-native-php-sessions' ), 'log' );

			return;
		}

		$this->safe_output( __( 'Multisite installation detected. Processing Sites individually...', 'wp-native-php-sessions' ), 'log' );

		$output = [
			'no_session_table' => 0,
			'id_column_exists' => 0,
		];
		$site_list = $this->get_all_sites( $start_position );
		$site_count = count( $site_list );

		for ( $i = $start_position; $i < $site_count; $i++ ) {
			// translators: %s is the current blog, and then the array index of the current site.
			$this->safe_output( __( 'Processing site %d. In the event of a timeout or error, resume execution starting from this point via "wp pantheon session add-index --start_point=%d". To skip this site if it does not need processing, run "wp pantheon session add-index --start_point=%d".', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ], $i, $i + 1 ] );

			$blog_prefix = $wpdb->get_blog_prefix( $site_list[ $i ] );
			$output = $this->add_single_index( $blog_prefix, $output, true );

			// translators: %s is the current site id.
			$this->safe_output( __( 'Processing for site %d complete.', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ] ] );
		}

		if ( $output['no_session_table'] != 0 ) {
			// translators: %d is the number of sites which did not have a session table.
			$this->safe_output( __( '%d site(s) did not have a session table and were skipped. If this was unexpected, please review output.', 'wp-native-php-sessions' ), 'log', [ $output['no_session_table'] ] );
		}

		if ( $output['id_column_exists'] != 0 ) {
			// translators: %d is the number of sites which had an ID column already.
			$this->safe_output( __( '%d site(s) already had an ID column and were skipped. If this was unexpected, please review output.', 'wp-native-php-sessions' ), 'log', [ $output['id_column_exists'] ] );
		}

		$this->safe_output( __( 'Operation complete, please verify that your site is working as expected. When ready, run wp pantheon session primary-key-finalize to clean up old data, or run wp pantheon session primary-key-revert if there were issues.', 'wp-native-php-sessions' ), 'log' );
	}

	/**
	 * Retrieves all sites with query batches.
	 *
	 * @param int $start_position Starting position of query.
	 *
	 * @return array
	 */
	public function get_all_sites( $start_position ) {
		$num_sites = 2;
		$sites = ( $start_position == 0 ) ? [] : array_fill( 0, $start_position, null );
		$page = 0;

		while ( true ) {
			$offset = ( $page * $num_sites ) + $start_position;
			$next_page = get_sites( [
				'number' => $num_sites,
				'offset' => $offset,
				'fields' => 'ids',
			] );

			if ( empty( $next_page ) || empty( array_diff( $next_page, $sites ) ) ) {
				break;
			}
			$sites = array_merge( $sites, $next_page );

			++$page;
		}

		if ( $start_position == 0 ) {
			unset( $sites[ $start_position - 1 ] );
		}
		return $sites;
	}

	/**
	 * Finalizes the creation of a primary key by deleting the old data.
	 *
	 * @param int $start_position Site index to begin from.
	 */
	public function primary_key_finalize( $start_position ) {
		global $wpdb;

		if ( ! is_multisite() ) {
			$this->safe_output( __( 'Single site detected. Beginning finalization.', 'wp-native-php-sessions' ), 'log' );

			$this->primary_key_finalize_single( $wpdb->prefix );

			$this->safe_output( __( 'Operation complete.', 'wp-native-php-sessions' ), 'log' );

			return;
		}

		$this->safe_output( __( 'Multisite installation detected. Processing sites individually.', 'wp-native-php-sessions' ), 'log' );

		$site_list = $this->get_all_sites( $start_position );
		$output = [ 'no_old_table' => 0 ];
		$site_count = count( $site_list );

		for ( $i = $start_position; $i < $site_count; $i++ ) {
			// translators: $s is the array index of the current site.
			$this->safe_output( __( 'Finalizing site %d. In the event of a timeout or error, resume execution starting from this point via "wp pantheon session primary-key-finalize --start_point=%d". To skip this site if it does not need processing, run "wp pantheon session primary-key-finalize --start_point=%d".', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ], $i, $i + 1 ] );

			$blog_prefix = $wpdb->get_blog_prefix( $site_list[ $i ] );
			$output = $this->primary_key_finalize_single( $blog_prefix, $output, true );

			// translators: %s is the current site id.
			$this->safe_output( __( 'Finalization of site %d complete.', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ] ] );
		}

		if ( $output['no_old_table'] != 0 ) {
			// translators: %d is the number of sites which did not have an old table.
			$this->safe_output( __( '%d site(s) did not have an old table to delete and were skipped. If this is not expected, please review output.', 'wp-native-php-sessions' ), 'log', [ $output['no_old_table'] ] );
		}

		$this->safe_output( __( 'Finalization of all sites complete.', 'wp-native-php-sessions' ), 'log' );
	}

	/**
	 * Finalizes the creation of a primary key by deleting the old data.
	 *
	 * @param int $start_position Site index to begin from.
	 */
	public function primary_key_revert( $start_position ) {
		global $wpdb;

		if ( ! is_multisite() ) {
			$this->safe_output( __( 'Single site detected. Beginning revert.', 'wp-native-php-sessions' ), 'log' );

			$this->primary_key_revert_single( $wpdb->prefix );

			$this->safe_output( __( 'Operation complete.', 'wp-native-php-sessions' ), 'log' );

			return;
		}

		$this->safe_output( __( 'Multisite installation detected. Processing Sites individually.', 'wp-native-php-sessions' ), 'log' );

		$site_list = $this->get_all_sites( $start_position );
		$output = [ 'no_rollback_table' => 0 ];
		$site_count = count( $site_list );

		for ( $i = $start_position; $i < $site_count; $i++ ) {
			// translators: $s is the array index of the current site.
			$this->safe_output( __( 'Processing site %d. In the event of a timeout or error, resume execution starting from this point via "wp pantheon session primary-key-finalize --start_point=%d". To skip this site if it does not need processing, run "wp pantheon session primary-key-finalize --start_point=%d".', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ], $i, $i + 1 ] );

			$blog_prefix = $wpdb->get_blog_prefix( $site_list[ $i ] );
			$output = $this->primary_key_revert_single( $blog_prefix, $output, true );

			// translators: %d is the current site id.
			$this->safe_output( __( 'Revert of site %d complete.', 'wp-native-php-sessions' ), 'log', [ $site_list[ $i ] ] );
		}

		if ( $output['no_rollback_table'] != 0 ) {
			// translators: %d is the number of sites which did not have a rollback table.
			$this->safe_output( __( '%d site(s) did not have a table to roll back to and were skipped. If this is not expected, please review output.', 'wp-native-php-sessions' ), 'log', [ $output['no_rollback_table'] ] );
		}

		$this->safe_output( __( 'Revert operation complete.', 'wp-native-php-sessions' ), 'log' );
	}

	/**
	 * Perform add_index functionality for a single site.
	 *
	 * @param string $prefix Database prefix for the blog.
	 * @param array $output An array of logs/errors which may occur.
	 * @param bool $multisite Whether the site is a multisite.
	 */
	public function add_single_index( $prefix, $output = [], $multisite = false ) {
		global $wpdb;
		$unprefixed_table = self::PANTHEON_SESSIONS_TABLE;
		$table            = esc_sql( $prefix . $unprefixed_table );
		$temp_clone_table = esc_sql( $prefix . 'sessions_temp_clone' );

		// If the command has been run multiple times and there is already a
		// temp_clone table, drop it.
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $temp_clone_table ) );

		if ( $wpdb->get_var( $query ) == $temp_clone_table ) {
			$query = "DROP TABLE {$temp_clone_table};";
			$wpdb->query( $query );
		}

		if ( ! PANTHEON_SESSIONS_ENABLED ) {
			$this->safe_output( __( 'Pantheon Sessions is currently disabled.', 'wp-native-php-sessions' ), 'error' );
		}

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );
		if ( ! $wpdb->get_var( $query ) == $table ) {
			$this->safe_output( __( 'This site does not have a pantheon_sessions table, and is being skipped.', 'wp-native-php-sessions' ), 'log' );
			$output['no_session_table'] = isset( $output['no_session_table'] ) ? $output['no_session_table'] + 1 : 1;

			return $output;
		}

		// Verify that the ID column/primary key does not already exist.
		$query         = "SHOW KEYS FROM {$table} WHERE key_name = 'PRIMARY';";
		$key_existence = $wpdb->get_results( $query );

		// Avoid errors by not attempting to add a column that already exists.
		if ( ! empty( $key_existence ) ) {
			// If dealing with multisites, it's feasible that some may have the
			// column and some may not, so don't stop execution if all that's
			// wrong is the key exists on one.
			if ( ! $multisite ) {
				$type = 'error';
			} else {
				$type = 'log';
			}

			$output['id_column_exists'] = isset( $output['id_column_exists'] ) ? $output['id_column_exists'] + 1 : 1;
			$this->safe_output( __( 'ID column already exists and does not need to be added to the table.', 'wp-native-php-sessions' ), $type );

			return $output;
		}

		// Alert the user that the action is going to go through.
		$this->safe_output( __( 'Primary Key does not exist, resolution starting.', 'wp-native-php-sessions' ), 'log' );

		$count_query = "SELECT COUNT(*) FROM {$table};";
		$count_total = $wpdb->get_results( $count_query );

		// Avoid errors when object returns an empty object.
		if ( ! empty( $count_total ) ) {
			$count_total = $count_total[0]->{'COUNT(*)'};
		} else {
			$count_total = 0;
		}

		if ( $count_total >= 20000 ) {
			// translators: %s is the total number of rows that exist in the pantheon_sessions table.
			$this->safe_output( __( 'A total of %s rows exist. To avoid service interruptions, this operation will be run in batches. Any sessions created between now and when operation completes may need to be recreated.', 'wp-native-php-sessions' ), 'log', [ $count_total ] );
		}
		// Create temporary table to copy data into in batches.
		$query = "CREATE TABLE {$temp_clone_table} LIKE {$table};";
		$wpdb->query( $query );
		$query = "ALTER TABLE {$temp_clone_table} ADD COLUMN id BIGINT AUTO_INCREMENT PRIMARY KEY FIRST";
		$wpdb->query( $query );

		$batch_size = 20000;
		$loops      = ceil( $count_total / $batch_size );

		for ( $i = 0; $i < $loops; $i++ ) {
			$offset = $i * $batch_size;

			$query           = sprintf( "INSERT INTO {$temp_clone_table}
(user_id, session_id, secure_session_id, ip_address, datetime, data)
SELECT user_id,session_id,secure_session_id,ip_address,datetime,data
FROM %s ORDER BY user_id LIMIT %d OFFSET %d", $table, $batch_size, $offset );
			$results         = $wpdb->query( $query );
			$current_results = $results + ( $batch_size * $i );

			// translators: %1 and %2 are how many rows have been processed out of how many total.
			$this->safe_output( __( 'Updated %1$s / %2$s rows. ', 'wp-native-php-sessions' ), 'log', [ $current_results, $count_total ] );
		}

		// Hot swap the old table and the new table, deleting a previous old
		// table if necessary.
		$old_table = esc_sql( $prefix . 'bak_' . $unprefixed_table );
		$query     = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table ) );

		if ( $wpdb->get_var( $query ) == $old_table ) {
			$query = "DROP TABLE {$old_table};";
			$wpdb->query( $query );
		}

		$query = "ALTER TABLE {$table} RENAME {$old_table};";
		$wpdb->query( $query );
		$query = "ALTER TABLE {$temp_clone_table} RENAME {$table};";
		$wpdb->query( $query );

		return $output;
	}

	/**
	 * Finalizes the creation of a primary key by deleting the old data.
	 *
	 * @param string $prefix Database prefix for the blog.
	 * @param array $output An array of logs/errors which may occur.
	 * @param bool $multisite Whether the site is a multisite.
	 */
	public function primary_key_finalize_single( $prefix = null, $output = [], $multisite = false ) {
		global $wpdb;
		$table = esc_sql( $prefix . self::BAK_PANTHEON_SESSIONS_TABLE );

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );

		// Check for table existence and delete if present.
		if ( ! $wpdb->get_var( $query ) == $table ) {
			// If dealing with multisites, it's feasible that some may have a
			// table and some may not, so don't stop execution if it's not found.
			if ( ! $multisite ) {
				$type = 'error';
			} else {
				$output['no_old_table'] = isset( $output['no_old_table'] ) ? $output['no_old_table'] + 1 : 1;
				$type = 'log';
			}

			$this->safe_output( __( 'Old table does not exist to be removed.', 'wp-native-php-sessions' ), $type );
		} else {
			$query = "DROP TABLE {$table};";
			$wpdb->query( $query );

			if ( ! $multisite ) {
				$this->safe_output( __( 'Old table has been successfully removed.', 'wp-native-php-sessions' ), 'log' );
			} else {
				$this->safe_output( __( 'Old table has been successfully removed for this site.', 'wp-native-php-sessions' ), 'log' );
			}
		}

		return $output;
	}

	/**
	 * Reverts addition of primary key.
	 *
	 * @param string $prefix Database prefix for the blog.
	 * @param array $output An array of logs/errors which may occur.
	 * @param bool $multisite Whether the site is a multisite.
	 */
	public function primary_key_revert_single( $prefix = null, $output = [], $multisite = false ) {
		global $wpdb;
		$old_clone_table  = esc_sql( $prefix . self::BAK_PANTHEON_SESSIONS_TABLE );
		$temp_clone_table = esc_sql( $prefix . self::TEMP_PANTHEON_SESSIONS_TABLE );
		$table            = esc_sql( $prefix . self::PANTHEON_SESSIONS_TABLE );

		// If there is no old table to roll back to, error.
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_clone_table ) );

		// If dealing with multisites, it's feasible that some may have a
		// table and some may not, so don't stop execution if it's not found.
		if ( ! $multisite ) {
			$type = 'error';
		} else {
			$type = 'log';
		}

		if ( ! $wpdb->get_var( $query ) == $old_clone_table ) {
			$this->safe_output( __( 'There is no old table to roll back to.', 'wp-native-php-sessions' ), $type );
			$output['no_rollback_table'] = isset( $output['no_rollback_table'] ) ? $output['no_rollback_table'] + 1 : 1;

			return $output;
		}

		// Swap old table and new one.
		$query = "ALTER TABLE {$table} RENAME {$temp_clone_table};";
		$wpdb->query( $query );
		$query = "ALTER TABLE {$old_clone_table} RENAME {$table};";
		$wpdb->query( $query );
		$this->safe_output( __( 'Rolled back to previous state successfully, dropping corrupt table.', 'wp-native-php-sessions' ), 'log' );

		// Remove table which did not function.
		$query = "DROP TABLE {$temp_clone_table}";
		$wpdb->query( $query );
		$this->safe_output( __( 'Site processing complete.', 'wp-native-php-sessions' ), 'log' );

		return $output;
	}

	/**
	 * Provide output to users, whether it's being run in WP_CLI or not.
	 *
	 * @param string $message Message to be printed.
	 * @param string $type If message is being printed through WP_CLI, what type of message.
	 * @param array $variables If sprintf is needed, an array of values.
	 *
	 * @return void
	 */
	protected function safe_output( $message, $type, array $variables = [] ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::$type( vsprintf( $message, $variables ) );
			return;
		}

		print "\n" . wp_kses_post( vsprintf( $message, $variables ) );

		// Calling WP_CLI::error triggers an exit, but we still need to exist even if we don't have WP_CLI available.
		if ( $type === 'error' ) {
			exit( 1 );
		}
	}
}

/**
 * Release the kraken!
 *
 * @return object
 */
function Pantheon_Sessions() {
	return Pantheon_Sessions::get_instance();
}

add_action( 'activated_plugin', 'Pantheon_Sessions::force_first_load' );
add_action( 'admin_notices', 'Pantheon_Sessions::check_native_primary_keys' );

Pantheon_Sessions();
