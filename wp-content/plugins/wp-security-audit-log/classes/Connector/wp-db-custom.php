<?php
/**
 * Class: Custom DB Class
 *
 * Test the DB connection.
 * It uses wpdb WordPress DB Class.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test the DB connection.
 * It uses wpdb WordPress DB Class.
 *
 * @package Wsal
 */
class wpdbCustom extends wpdb {

	/**
	 * Overwrite wpdb class for set $allow_bail to false
	 * and hide the print of the error
	 *
	 * @global string $wp_version
	 * @param string $dbuser     - MySQL database user.
	 * @param string $dbpassword - MySQL database password.
	 * @param string $dbname     - MySQL database name.
	 * @param string $dbhost     - MySQL database host.
	 */
	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		register_shutdown_function( array( $this, '__destruct' ) );
		if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
			$this->show_errors();
		}
		if ( function_exists( 'mysqli_connect' ) ) {
			if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
				$this->use_mysqli = ! WP_USE_EXT_MYSQL;
			} elseif ( version_compare( phpversion(), '5.5', '>=' ) || ! function_exists( 'mysql_connect' ) ) {
				$this->use_mysqli = true;
			} elseif ( false !== strpos( $GLOBALS['wp_version'], '-' ) ) {
				$this->use_mysqli = true;
			}
		}
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		// wp-config.php creation will manually connect when ready.
		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return;
		}

		$this->db_connect( false );
	}
}
