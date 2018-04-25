<?php
/**
 * Sensor: Log In & Log Out
 *
 * Log In & Out sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login/Logout sensor.
 *
 * 1000 User logged in
 * 1001 User logged out
 * 1002 Login failed
 * 1003 Login failed / non existing user
 * 1004 Login blocked
 * 4003 User has changed his or her password
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_LogInOut extends WSAL_AbstractSensor {

	/**
	 * Transient name.
	 * WordPress will prefix the name with "_transient_" or "_transient_timeout_" in the options table.
	 */
	const TRANSIENT_FAILEDLOGINS = 'wsal-failedlogins-known';
	const TRANSIENT_FAILEDLOGINS_UNKNOWN = 'wsal-failedlogins-unknown';

	/**
	 * Current user object
	 *
	 * @var WP_User
	 */
	protected $_current_user = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		add_action( 'wp_login', array( $this, 'EventLogin' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'EventLogout' ) );
		add_action( 'password_reset', array( $this, 'EventPasswordReset' ), 10, 2 );
		add_action( 'wp_login_failed', array( $this, 'EventLoginFailure' ) );
		add_action( 'clear_auth_cookie', array( $this, 'GetCurrentUser' ), 10 );
		add_filter( 'wp_login_blocked', array( $this, 'EventLoginBlocked' ), 10, 1 );

		// Directory for logged in users log files.
		$user_upload_dir    = wp_upload_dir();
		$user_upload_path   = trailingslashit( $user_upload_dir['basedir'] . '/wp-security-audit-log/failed-logins/' );
		if ( ! $this->CheckDirectory( $user_upload_path ) ) {
			wp_mkdir_p( $user_upload_path );
		}
	}

	/**
	 * Sets current user.
	 */
	public function GetCurrentUser() {
		$this->_current_user = wp_get_current_user();
	}

	/**
	 * Event Login.
	 *
	 * @param string $user_login - Username.
	 * @param object $user - WP_User object.
	 */
	public function EventLogin( $user_login, $user = null ) {
		if ( empty( $user ) ) {
			$user = get_user_by( 'login', $user_login );
		}
		$user_roles = $this->plugin->settings->GetCurrentUserRoles( $user->roles );
		if ( $this->plugin->settings->IsLoginSuperAdmin( $user_login ) ) {
			$user_roles[] = 'superadmin';
		}
		$this->plugin->alerts->Trigger(
			1000, array(
				'Username' => $user_login,
				'CurrentUserRoles' => $user_roles,
			), true
		);
	}

	/**
	 * Event Logout.
	 */
	public function EventLogout() {
		if ( 0 != $this->_current_user->ID ) {
			$this->plugin->alerts->Trigger(
				1001, array(
					'CurrentUserID' => $this->_current_user->ID,
					'CurrentUserRoles' => $this->plugin->settings->GetCurrentUserRoles( $this->_current_user->roles ),
				), true
			);
		}
	}

	/**
	 * Login failure limit count.
	 *
	 * @return int
	 */
	protected function GetLoginFailureLogLimit() {
		return $this->plugin->settings->get_failed_login_limit();
	}

	/**
	 * Non-existing Login failure limit count.
	 *
	 * @return int
	 */
	protected function GetVisitorLoginFailureLogLimit() {
		return $this->plugin->settings->get_visitor_failed_login_limit();
	}

	/**
	 * Expiration of the transient saved in the WP database.
	 *
	 * @return integer Time until expiration in seconds from now
	 */
	protected function GetLoginFailureExpiration() {
		return 12 * 60 * 60;
	}

	/**
	 * Check failure limit.
	 *
	 * @param string  $ip - IP address.
	 * @param integer $site_id - Blog ID.
	 * @param WP_User $user - User object.
	 * @return boolean - Passed limit true|false.
	 */
	protected function IsPastLoginFailureLimit( $ip, $site_id, $user ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		if ( $user ) {
			if ( -1 === (int) $this->GetLoginFailureLogLimit() ) {
				return false;
			} else {
				$data_known = $get_fn( self::TRANSIENT_FAILEDLOGINS );
				return ( false !== $data_known ) && isset( $data_known[ $site_id . ':' . $user->ID . ':' . $ip ] ) && ($data_known[ $site_id . ':' . $user->ID . ':' . $ip ] >= $this->GetLoginFailureLogLimit());
			}
		} else {
			if ( -1 === (int) $this->GetVisitorLoginFailureLogLimit() ) {
				return false;
			} else {
				$data_unknown = $get_fn( self::TRANSIENT_FAILEDLOGINS_UNKNOWN );
				return ( false !== $data_unknown ) && isset( $data_unknown[ $site_id . ':' . $ip ] ) && ($data_unknown[ $site_id . ':' . $ip ] >= $this->GetVisitorLoginFailureLogLimit());
			}
		}
	}

	/**
	 * Increment failure limit.
	 *
	 * @param string  $ip - IP address.
	 * @param integer $site_id - Blog ID.
	 * @param WP_User $user - User object.
	 */
	protected function IncrementLoginFailure( $ip, $site_id, $user ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$set_fn = $this->IsMultisite() ? 'set_site_transient' : 'set_transient';
		if ( $user ) {
			$data_known = $get_fn( self::TRANSIENT_FAILEDLOGINS );
			if ( ! $data_known ) {
				$data_known = array();
			}
			if ( ! isset( $data_known[ $site_id . ':' . $user->ID . ':' . $ip ] ) ) {
				$data_known[ $site_id . ':' . $user->ID . ':' . $ip ] = 1;
			}
			$data_known[ $site_id . ':' . $user->ID . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_FAILEDLOGINS, $data_known, $this->GetLoginFailureExpiration() );
		} else {
			$data_unknown = $get_fn( self::TRANSIENT_FAILEDLOGINS_UNKNOWN );
			if ( ! $data_unknown ) {
				$data_unknown = array();
			}
			if ( ! isset( $data_unknown[ $site_id . ':' . $ip ] ) ) {
				$data_unknown[ $site_id . ':' . $ip ] = 1;
			}
			$data_unknown[ $site_id . ':' . $ip ]++;
			$set_fn( self::TRANSIENT_FAILEDLOGINS_UNKNOWN, $data_unknown, $this->GetLoginFailureExpiration() );
		}
	}

	/**
	 * Event Login failure.
	 *
	 * @param string $username Username.
	 */
	public function EventLoginFailure( $username ) {
		list($y, $m, $d) = explode( '-', date( 'Y-m-d' ) );

		$ip = $this->plugin->settings->GetMainClientIP();

		// Filter $_POST global array for security.
		$post_array = filter_input_array( INPUT_POST );

		$username = array_key_exists( 'log', $post_array ) ? $post_array['log'] : $username;
		$username = sanitize_user( $username );
		$new_alert_code = 1003;
		$user = get_user_by( 'login', $username );
		$site_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);
		if ( $user ) {
			$new_alert_code = 1002;
			$user_roles = $this->plugin->settings->GetCurrentUserRoles( $user->roles );
			if ( $this->plugin->settings->IsLoginSuperAdmin( $username ) ) {
				$user_roles[] = 'superadmin';
			}
		}

		// Check if the alert is disabled from the "Enable/Disable Alerts" section.
		if ( ! $this->plugin->alerts->IsEnabled( $new_alert_code ) ) {
			return;
		}

		if ( $this->IsPastLoginFailureLimit( $ip, $site_id, $user ) ) {
			return;
		}

		$obj_occurrence = new WSAL_Models_Occurrence();

		if ( 1002 == $new_alert_code ) {
			if ( ! $this->plugin->alerts->CheckEnableUserRoles( $username, $user_roles ) ) {
				return;
			}
			$occ = $obj_occurrence->CheckKnownUsers(
				array(
					$ip,
					$username,
					1002,
					$site_id,
					mktime( 0, 0, 0, $m, $d, $y ),
					mktime( 0, 0, 0, $m, $d + 1, $y ) - 1,
				)
			);
			$occ = count( $occ ) ? $occ[0] : null;

			if ( ! empty( $occ ) ) {
				// Update existing record exists user.
				$this->IncrementLoginFailure( $ip, $site_id, $user );
				$new = $occ->GetMetaValue( 'Attempts', 0 ) + 1;

				if ( -1 !== (int) $this->GetLoginFailureLogLimit()
					&& $new > $this->GetLoginFailureLogLimit() ) {
					$new = $this->GetLoginFailureLogLimit() . '+';
				}

				$occ->UpdateMetaValue( 'Attempts', $new );
				$occ->UpdateMetaValue( 'Username', $username );

				// $occ->SetMetaValue('CurrentUserRoles', $user_roles);
				$occ->created_on = null;
				$occ->Save();
			} else {
				// Create a new record exists user.
				$this->plugin->alerts->Trigger(
					$new_alert_code, array(
						'Attempts' => 1,
						'Username' => $username,
						'CurrentUserRoles' => $user_roles,
					)
				);
			}
		} else {
			$occ_unknown = $obj_occurrence->CheckUnKnownUsers(
				array(
					$ip,
					1003,
					$site_id,
					mktime( 0, 0, 0, $m, $d, $y ),
					mktime( 0, 0, 0, $m, $d + 1, $y ) - 1,
				)
			);

			$occ_unknown = count( $occ_unknown ) ? $occ_unknown[0] : null;
			if ( ! empty( $occ_unknown ) ) {
				// Update existing record not exists user.
				$this->IncrementLoginFailure( $ip, $site_id, false );
				$new = $occ_unknown->GetMetaValue( 'Attempts', 0 ) + 1;

				if ( 'on' === $this->plugin->GetGlobalOption( 'log-visitor-failed-login' ) ) {
					$link_file = $this->WriteLog( $new, $username );
				}

				if ( -1 !== (int) $this->GetVisitorLoginFailureLogLimit()
					&& $new > $this->GetVisitorLoginFailureLogLimit() ) {
					$new = $this->GetVisitorLoginFailureLogLimit() . '+';
				}

				$occ_unknown->UpdateMetaValue( 'Attempts', $new );
				if ( ! empty( $link_file ) && 'on' === $this->plugin->GetGlobalOption( 'log-visitor-failed-login' ) ) {
					$occ_unknown->UpdateMetaValue( 'LogFileLink', $link_file );
				} else {
					$link_file = site_url() . '/wp-admin/admin.php?page=wsal-togglealerts#tab-users-profiles---activity';
					$occ_unknown->UpdateMetaValue( 'LogFileLink', $link_file );
				}
				$occ_unknown->created_on = null;
				$occ_unknown->Save();
			} else {
				$link_file = site_url() . '/wp-admin/admin.php?page=wsal-togglealerts#tab-users-profiles---activity';
				$log_file_text = ' in a log file';
				if ( 'on' === $this->plugin->GetGlobalOption( 'log-visitor-failed-login' ) ) {
					$link_file = $this->WriteLog( 1, $username );
					$log_file_text = ' with the usernames used during these failed login attempts';
				}

				// Create a new record not exists user.
				$this->plugin->alerts->Trigger(
					$new_alert_code, array(
						'Attempts' => 1,
						'LogFileLink' => $link_file,
						'LogFileText' => $log_file_text,
					)
				);
			}
		}
	}

	/**
	 * Event changed password.
	 *
	 * @param WP_User $user - User object.
	 * @param string  $new_pass - New Password.
	 */
	public function EventPasswordReset( $user, $new_pass ) {
		if ( ! empty( $user ) ) {
			$user_roles = $this->plugin->settings->GetCurrentUserRoles( $user->roles );
			$this->plugin->alerts->Trigger(
				4003, array(
					'Username' => $user->user_login,
					'CurrentUserRoles' => $user_roles,
				), true
			);
		}
	}

	/**
	 * Event login blocked.
	 *
	 * @param string $username - Username.
	 */
	public function EventLoginBlocked( $username ) {
		$user = get_user_by( 'login', $username );
		$user_roles = $this->plugin->settings->GetCurrentUserRoles( $user->roles );

		if ( $this->plugin->settings->IsLoginSuperAdmin( $username ) ) {
			$user_roles[] = 'superadmin';
		}
		$this->plugin->alerts->Trigger(
			1004, array(
				'Username' => $username,
				'CurrentUserRoles' => $user_roles,
			), true
		);
	}

	/**
	 * Write log file.
	 *
	 * @param int    $attempts - Number of attempt.
	 * @param string $username - Username.
	 * @author Ashar Irfan
	 * @since  2.6.9
	 */
	private function WriteLog( $attempts, $username = '' ) {
		$name_file = null;

		// Create/Append to the log file.
		$data = 'Attempts: ' . $attempts . ' — Username: ' . $username;

		$upload_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/failed-logins/';
		$uploads_url = trailingslashit( $upload_dir['baseurl'] ) . 'wp-security-audit-log/failed-logins/';

		// Check directory.
		if ( $this->CheckDirectory( $uploads_dir_path ) ) {
			$filename = 'failed_logins_usernames_' . date( 'Ymd' ) . '.log';
			$fp = $uploads_dir_path . $filename;
			$name_file = $uploads_url . $filename;
			if ( ! $file = fopen( $fp, 'a' ) ) {
				$i = 1;
				$file_opened = false;
				do {
					$fp2 = substr( $fp, 0, -4 ) . '_' . $i . '.log';
					if ( ! file_exists( $fp2 ) ) {
						if ( $file = fopen( $fp2, 'a' ) ) {
							$file_opened = true;
							$name_file = $uploads_url . substr( $name_file, 0, -4 ) . '_' . $i . '.log';
						}
					} else {
						$latest_filename = $this->GetLastModified( $uploads_dir_path, $filename );
						$fp_last = $uploads_dir_path . $latest_filename;
						if ( $file = fopen( $fp_last, 'a' ) ) {
							$file_opened = true;
							$name_file = $uploads_url . $latest_filename;
						}
					}
					$i++;
				} while ( ! $file_opened );
			}
			fwrite( $file, sprintf( "%s\n", $data ) );
			fclose( $file );
		}

		return $name_file;
	}

	/**
	 * Get the latest file modified.
	 *
	 * @param string $uploads_dir_path - Uploads directory path.
	 * @param string $filename - File name.
	 * @return string $latest_filename - File name.
	 */
	private function GetLastModified( $uploads_dir_path, $filename ) {
		$filename = substr( $filename, 0, -4 );
		$latest_mtime = 0;
		$latest_filename = '';
		if ( $handle = opendir( $uploads_dir_path ) ) {
			while ( false !== ($entry = readdir( $handle )) ) {
				if ( '.' != $entry && '..' != $entry ) {
					$entry = strip_tags( $entry ); // Strip HTML Tags.
					$entry = preg_replace( '/[\r\n\t ]+/', ' ', $entry ); // Remove Break/Tabs/Return Carriage.
					$entry = preg_replace( '/[\"\*\/\:\<\>\?\'\|]+/', ' ', $entry ); // Remove Illegal Chars for folder and filename.
					if ( preg_match( '/^' . $filename . '/i', $entry ) > 0 ) {
						if ( filemtime( $uploads_dir_path . $entry ) > $latest_mtime ) {
							$latest_mtime = filemtime( $uploads_dir_path . $entry );
							$latest_filename = $entry;
						}
					}
				}
			}
			closedir( $handle );
		}
		return $latest_filename;
	}
}
