<?php
/**
 * Sensor: System Activity
 *
 * System activity sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * System Activity sensor.
 *
 * 6000 Events automatically pruned by system
 * 6001 Option Anyone Can Register in WordPress settings changed
 * 6002 New User Default Role changed
 * 6003 WordPress Administrator Notification email changed
 * 6004 WordPress was updated
 * 6005 User changes the WordPress Permalinks
 * 6007 User requests non-existing pages (404 Error Pages)
 * 8009 User changed forum's role
 * 8010 User changed option of a forum
 * 8012 User changed time to disallow post editing
 * 8013 User changed the forum setting posting throttle time
 * 1006 User logged out all other sessions with the same username
 * 6004 WordPress was updated
 * 6008 Enabled/Disabled the option Discourage search engines from indexing this site
 * 6009 Enabled/Disabled comments on all the website
 * 6010 Enabled/Disabled the option Comment author must fill out name and email
 * 6011 Enabled/Disabled the option Users must be logged in and registered to comment
 * 6012 Enabled/Disabled the option to automatically close comments
 * 6013 Changed the value of the option Automatically close comments
 * 6014 Enabled/Disabled the option for comments to be manually approved
 * 6015 Enabled/Disabled the option for an author to have previously approved comments for the comments to appear
 * 6016 Changed the number of links that a comment must have to be held in the queue
 * 6017 Modified the list of keywords for comments moderation
 * 6018 Modified the list of keywords for comments blacklisting
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_System extends WSAL_AbstractSensor {

	/**
	 * Transient name.
	 * WordPress will prefix the name with "_transient_"
	 * or "_transient_timeout_" in the options table.
	 */
	const TRANSIENT_404         = 'wsal-404-attempts';
	const TRANSIENT_VISITOR_404 = 'wsal-visitor-404-attempts';

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {

		add_action( 'wsal_prune', array( $this, 'EventPruneEvents' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'EventAdminInit' ) );

		add_action( 'automatic_updates_complete', array( $this, 'WPUpdate' ), 10, 1 );
		add_filter( 'template_redirect', array( $this, 'Event404' ) );

		$upload_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/404s/';
		if ( ! $this->CheckDirectory( $uploads_dir_path ) ) {
			wp_mkdir_p( $uploads_dir_path );
		}

		// Directory for logged in users log files.
		$user_upload_dir  = wp_upload_dir();
		$user_upload_path = trailingslashit( $user_upload_dir['basedir'] . '/wp-security-audit-log/404s/users/' );
		$this->remove_sub_directories( $user_upload_path ); // Remove it.

		// Directory for visitor log files.
		$visitor_upload_dir  = wp_upload_dir();
		$visitor_upload_path = trailingslashit( $visitor_upload_dir['basedir'] . '/wp-security-audit-log/404s/visitors/' );
		$this->remove_sub_directories( $visitor_upload_path ); // Remove it.

		// Cron Job 404 log files pruning.
		add_action( 'log_files_pruning', array( $this, 'LogFilesPruning' ) );
		if ( ! wp_next_scheduled( 'log_files_pruning' ) ) {
			wp_schedule_event( time(), 'daily', 'log_files_pruning' );
		}
		// whitelist options.
		add_action( 'whitelist_options', array( $this, 'EventOptions' ), 10, 1 );

		// Update admin email alert.
		add_action( 'update_option_admin_email', array( $this, 'admin_email_changed' ), 10, 3 );
	}

	/**
	 * Check if failed login directory exists then delete all
	 * files within this directory and remove the directory itself.
	 *
	 * @param string $sub_dir - Subdirectory.
	 */
	public function remove_sub_directories( $sub_dir ) {
		// Check if subdirectory exists.
		if ( is_dir( $sub_dir ) ) {
			// Get all files inside failed logins folder.
			$files = glob( $sub_dir . '*', GLOB_BRACE );

			if ( ! empty( $files ) ) {
				// Unlink each file.
				foreach ( $files as $file ) {
					// Check if valid file.
					if ( is_file( $file ) ) {
						// Delete the file.
						unlink( $file );
					}
				}
			}
			// Remove the directory.
			rmdir( $sub_dir );
		}
	}

	/**
	 * Alert: Admin email changed.
	 *
	 * @param mixed  $old_value - The old option value.
	 * @param mixed  $new_value - The new option value.
	 * @param string $option    - Option name.
	 * @since 3.0.0
	 */
	public function admin_email_changed( $old_value, $new_value, $option ) {
		// Check if the option is not empty and is admin_email.
		if ( ! empty( $old_value ) && ! empty( $new_value )
			&& ! empty( $option ) && 'admin_email' === $option ) {
			if ( $old_value != $new_value ) {
				$this->plugin->alerts->Trigger(
					6003, array(
						'OldEmail' => $old_value,
						'NewEmail' => $new_value,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}
	}

	/**
	 * Method: Prune events function.
	 *
	 * @param int    $count The number of deleted events.
	 * @param string $query Query that selected events for deletion.
	 */
	public function EventPruneEvents( $count, $query ) {
		$this->plugin->alerts->Trigger(
			6000, array(
				'EventCount' => $count,
				'PruneQuery' => $query,
			)
		);
	}

	/**
	 * 404 limit count.
	 *
	 * @return integer limit
	 */
	protected function Get404LogLimit() {
		return $this->plugin->settings->Get404LogLimit();
	}

	/**
	 * 404 visitor limit count.
	 *
	 * @return integer limit
	 */
	protected function GetVisitor404LogLimit() {
		return $this->plugin->settings->GetVisitor404LogLimit();
	}

	/**
	 * Expiration of the transient saved in the WP database.
	 *
	 * @return integer Time until expiration in seconds from now
	 */
	protected function Get404Expiration() {
		return 24 * 60 * 60;
	}

	/**
	 * Check 404 limit.
	 *
	 * @param integer $site_id - Blog ID.
	 * @param string  $username - Username.
	 * @param string  $ip - IP address.
	 * @return boolean passed limit true|false
	 */
	protected function IsPast404Limit( $site_id, $username, $ip ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$data = $get_fn( self::TRANSIENT_404 );
		return ( false !== $data ) && isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) && ($data[ $site_id . ':' . $username . ':' . $ip ] > $this->Get404LogLimit());
	}

	/**
	 * Check visitor 404 limit.
	 *
	 * @param integer $site_id - Blog ID.
	 * @param string  $username - Username.
	 * @param string  $ip - IP address.
	 * @return boolean passed limit true|false
	 */
	protected function IsPastVisitor404Limit( $site_id, $username, $ip ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$data = $get_fn( self::TRANSIENT_VISITOR_404 );
		return ( false !== $data ) && isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) && ( $data[ $site_id . ':' . $username . ':' . $ip ] > $this->GetVisitor404LogLimit() );
	}

	/**
	 * Increment 404 limit.
	 *
	 * @param integer $site_id - Blog ID.
	 * @param string  $username - Username.
	 * @param string  $ip - IP address.
	 */
	protected function Increment404( $site_id, $username, $ip ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$set_fn = $this->IsMultisite() ? 'set_site_transient' : 'set_transient';

		$data = $get_fn( self::TRANSIENT_404 );
		if ( ! $data ) {
			$data = array();
		}
		if ( ! isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) ) {
			$data[ $site_id . ':' . $username . ':' . $ip ] = 1;
		}
		$data[ $site_id . ':' . $username . ':' . $ip ]++;
		$set_fn( self::TRANSIENT_404, $data, $this->Get404Expiration() );
	}

	/**
	 * Increment visitor 404 limit.
	 *
	 * @param integer $site_id - Blog ID.
	 * @param string  $username - Username.
	 * @param string  $ip - IP address.
	 */
	protected function IncrementVisitor404( $site_id, $username, $ip ) {
		$get_fn = $this->IsMultisite() ? 'get_site_transient' : 'get_transient';
		$set_fn = $this->IsMultisite() ? 'set_site_transient' : 'set_transient';

		$data = $get_fn( self::TRANSIENT_VISITOR_404 );
		if ( ! $data ) {
			$data = array();
		}
		if ( ! isset( $data[ $site_id . ':' . $username . ':' . $ip ] ) ) {
			$data[ $site_id . ':' . $username . ':' . $ip ] = 1;
		}
		$data[ $site_id . ':' . $username . ':' . $ip ]++;
		$set_fn( self::TRANSIENT_VISITOR_404, $data, $this->Get404Expiration() );
	}

	/**
	 * Event 404 Not found.
	 */
	public function Event404() {
		$attempts = 1;

		global $wp_query;
		if ( ! $wp_query->is_404 ) {
			return;
		}
		$msg = 'times';

		list( $y, $m, $d ) = explode( '-', date( 'Y-m-d' ) );

		$site_id = ( function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0 );
		$ip      = $this->plugin->settings->GetMainClientIP();

		if ( ! is_user_logged_in() ) {
			$username = 'Website Visitor';
		} else {
			$username = wp_get_current_user()->user_login;
		}

		if ( 'Website Visitor' !== $username ) {
			// Check if the alert is disabled from the "Enable/Disable Alerts" section.
			if ( ! $this->plugin->alerts->IsEnabled( 6007 ) ) {
				return;
			}

			if ( $this->IsPast404Limit( $site_id, $username, $ip ) ) {
				return;
			}

			$obj_occurrence = new WSAL_Models_Occurrence();

			$occ = $obj_occurrence->CheckAlert404(
				array(
					$ip,
					$username,
					6007,
					$site_id,
					mktime( 0, 0, 0, $m, $d, $y ),
					mktime( 0, 0, 0, $m, $d + 1, $y ) - 1,
				)
			);

			$occ = count( $occ ) ? $occ[0] : null;
			if ( ! empty( $occ ) ) {
				// Update existing record.
				$this->Increment404( $site_id, $username, $ip );
				$new = ( (int) $occ->GetMetaValue( 'Attempts', 0 ) ) + 1;

				if ( $new > $this->Get404LogLimit() ) {
					$new = 'more than ' . $this->Get404LogLimit();
					$msg .= ' This could possible be a scan, therefore keep an eye on the activity from this IP Address';
				}

				$link_file = $this->WriteLog( $new, $ip, $username );

				$occ->UpdateMetaValue( 'Attempts', $new );
				$occ->UpdateMetaValue( 'Username', $username );
				$occ->UpdateMetaValue( 'Msg', $msg );
				if ( ! empty( $link_file ) ) {
					$occ->UpdateMetaValue( 'LinkFile', $link_file );
				}
				$occ->created_on = null;
				$occ->Save();
			} else {
				$link_file = $this->WriteLog( 1, $ip, $username );
				// Create a new record.
				$fields = array(
					'Attempts' => 1,
					'Username' => $username,
					'Msg' => $msg,
				);
				if ( ! empty( $link_file ) ) {
					$fields['LinkFile'] = $link_file;
				}
				$this->plugin->alerts->Trigger( 6007, $fields );
			}
		} else {
			// Check if the alert is disabled from the "Enable/Disable Alerts" section.
			if ( ! $this->plugin->alerts->IsEnabled( 6023 ) ) {
				return;
			}

			if ( $this->IsPastVisitor404Limit( $site_id, $username, $ip ) ) {
				return;
			}

			$obj_occurrence = new WSAL_Models_Occurrence();

			$occ = $obj_occurrence->CheckAlert404(
				array(
					$ip,
					$username,
					6023,
					$site_id,
					mktime( 0, 0, 0, $m, $d, $y ),
					mktime( 0, 0, 0, $m, $d + 1, $y ) - 1,
				)
			);

			$occ = count( $occ ) ? $occ[0] : null;
			if ( ! empty( $occ ) ) {
				// Update existing record.
				$this->IncrementVisitor404( $site_id, $username, $ip );
				$new = ( (int) $occ->GetMetaValue( 'Attempts', 0 ) ) + 1;

				if ( $new > $this->GetVisitor404LogLimit() ) {
					$new = 'more than ' . $this->GetVisitor404LogLimit();
					$msg .= ' This could possible be a scan, therefore keep an eye on the activity from this IP Address';
				}

				$link_file = $this->WriteLog( $new, $ip, $username, false );

				$occ->UpdateMetaValue( 'Attempts', $new );
				$occ->UpdateMetaValue( 'Username', $username );
				$occ->UpdateMetaValue( 'Msg', $msg );
				if ( ! empty( $link_file ) ) {
					$occ->UpdateMetaValue( 'LinkFile', $link_file );
				}
				$occ->created_on = null;
				$occ->Save();
			} else {
				$link_file = $this->WriteLog( 1, $ip, $username, false );
				// Create a new record.
				$fields = array(
					'Attempts'  => 1,
					'Username'  => $username,
					'Msg'       => $msg,
				);
				if ( ! empty( $link_file ) ) {
					$fields['LinkFile'] = $link_file;
				}
				$this->plugin->alerts->Trigger( 6023, $fields );
			}
		}
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Filter global arrays for security.
		$post_array = filter_input_array( INPUT_POST );
		$get_array = filter_input_array( INPUT_GET );
		$server_array = filter_input_array( INPUT_SERVER );

		// Destroy all the session of the same user from user profile page.
		if ( isset( $post_array['action'] ) && ( 'destroy-sessions' == $post_array['action'] ) && isset( $post_array['user_id'] ) ) {
			$this->plugin->alerts->Trigger(
				1006, array(
					'TargetUserID' => $post_array['user_id'],
				)
			);
		}

		// Make sure user can actually modify target options.
		if ( ! current_user_can( 'manage_options' ) && isset( $post_array['_wpnonce'] ) && ! wp_verify_nonce( $post_array['_wpnonce'], 'update' ) ) {
			return;
		}

		$actype = '';
		if ( ! empty( $server_array['SCRIPT_NAME'] ) ) {
			$actype = basename( $server_array['SCRIPT_NAME'], '.php' );
		}

		$is_option_page = 'options' === $actype;
		$is_network_settings = 'settings' === $actype;
		$is_permalink_page = 'options-permalink' === $actype;

		// WordPress URL changed.
		if ( $is_option_page
			&& wp_verify_nonce( $post_array['_wpnonce'], 'general-options' )
			&& ! empty( $post_array['siteurl'] ) ) {
			$old_siteurl = get_option( 'siteurl' );
			$new_siteurl = isset( $post_array['siteurl'] ) ? $post_array['siteurl'] : '';
			if ( $old_siteurl !== $new_siteurl ) {
				$this->plugin->alerts->Trigger(
					6024, array(
						'old_url' => $old_siteurl,
						'new_url' => $new_siteurl,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Site URL changed.
		if ( $is_option_page
			&& wp_verify_nonce( $post_array['_wpnonce'], 'general-options' )
			&& ! empty( $post_array['home'] ) ) {
			$old_url = get_option( 'home' );
			$new_url = isset( $post_array['home'] ) ? $post_array['home'] : '';
			if ( $old_url !== $new_url ) {
				$this->plugin->alerts->Trigger(
					6025, array(
						'old_url' => $old_url,
						'new_url' => $new_url,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Registeration Option.
		if ( $is_option_page
			&& wp_verify_nonce( $post_array['_wpnonce'], 'general-options' )
			&& ( get_option( 'users_can_register' ) xor isset( $post_array['users_can_register'] ) ) ) {
			$old = get_option( 'users_can_register' ) ? 'Enabled' : 'Disabled';
			$new = isset( $post_array['users_can_register'] ) ? 'Enabled' : 'Disabled';
			if ( $old != $new ) {
				$this->plugin->alerts->Trigger(
					6001, array(
						'OldValue' => $old,
						'NewValue' => $new,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Default Role option.
		if ( $is_option_page && wp_verify_nonce( $post_array['_wpnonce'], 'general-options' ) && ! empty( $post_array['default_role'] ) ) {
			$old = get_option( 'default_role' );
			$new = trim( $post_array['default_role'] );
			if ( $old != $new ) {
				$this->plugin->alerts->Trigger(
					6002, array(
						'OldRole' => $old,
						'NewRole' => $new,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Admin Email Option.
		if ( $is_option_page && wp_verify_nonce( $post_array['_wpnonce'], 'general-options' ) && ! empty( $post_array['admin_email'] ) ) {
			$old = get_option( 'admin_email' );
			$new = trim( $post_array['admin_email'] );
			if ( $old != $new ) {
				$this->plugin->alerts->Trigger(
					6003, array(
						'OldEmail' => $old,
						'NewEmail' => $new,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Admin Email of Network.
		if ( $is_network_settings && ! empty( $post_array['admin_email'] ) ) {
			$old = get_site_option( 'admin_email' );
			$new = trim( $post_array['admin_email'] );
			if ( $old != $new ) {
				$this->plugin->alerts->Trigger(
					6003, array(
						'OldEmail' => $old,
						'NewEmail' => $new,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Permalinks changed.
		if ( $is_permalink_page && ! empty( $post_array['permalink_structure'] ) ) {
			$old = get_option( 'permalink_structure' );
			$new = trim( $post_array['permalink_structure'] );
			if ( $old != $new ) {
				$this->plugin->alerts->Trigger(
					6005, array(
						'OldPattern' => $old,
						'NewPattern' => $new,
						'CurrentUserID' => wp_get_current_user()->ID,
					)
				);
			}
		}

		// Core Update.
		if ( isset( $get_array['action'] ) && 'do-core-upgrade' === $get_array['action'] && isset( $post_array['version'] ) ) {
			$old_version = get_bloginfo( 'version' );
			$new_version = $post_array['version'];
			if ( $old_version != $new_version ) {
				$this->plugin->alerts->Trigger(
					6004, array(
						'OldVersion' => $old_version,
						'NewVersion' => $new_version,
					)
				);
			}
		}

		/* BBPress Forum support  Setting */
		if ( isset( $post_array['action'] ) && 'update' === $post_array['action'] && isset( $post_array['_bbp_default_role'] ) ) {
			$old_role = get_option( '_bbp_default_role' );
			$new_role = $post_array['_bbp_default_role'];
			if ( $old_role != $new_role ) {
				$this->plugin->alerts->Trigger(
					8009, array(
						'OldRole' => $old_role,
						'NewRole' => $new_role,
					)
				);
			}
		}

		if ( isset( $post_array['action'] ) && 'update' === $post_array['action'] && isset( $post_array['option_page'] ) && ( 'bbpress' === $post_array['option_page'] ) ) {
			// Anonymous posting.
			$allow_anonymous = get_option( '_bbp_allow_anonymous' );
			$old_status = ! empty( $allow_anonymous ) ? 1 : 0;
			$new_status = ! empty( $post_array['_bbp_allow_anonymous'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$status = ( 1 === $new_status ) ? 'Enabled' : 'Disabled';
				$this->plugin->alerts->Trigger(
					8010, array(
						'Status' => $status,
					)
				);
			}

			// Disallow editing after.
			$bbp_edit_lock = get_option( '_bbp_edit_lock' );
			$old_time = ! empty( $bbp_edit_lock ) ? $bbp_edit_lock : '';
			$new_time = ! empty( $post_array['_bbp_edit_lock'] ) ? $post_array['_bbp_edit_lock'] : '';
			if ( $old_time != $new_time ) {
				$this->plugin->alerts->Trigger(
					8012, array(
						'OldTime' => $old_time,
						'NewTime' => $new_time,
					)
				);
			}

			// Throttle posting every.
			$bbp_throttle_time = get_option( '_bbp_throttle_time' );
			$old_time2 = ! empty( $bbp_throttle_time ) ? $bbp_throttle_time : '';
			$new_time2 = ! empty( $post_array['_bbp_throttle_time'] ) ? $post_array['_bbp_throttle_time'] : '';
			if ( $old_time2 != $new_time2 ) {
				$this->plugin->alerts->Trigger(
					8013, array(
						'OldTime' => $old_time2,
						'NewTime' => $new_time2,
					)
				);
			}
		}
	}

	/**
	 * WordPress auto core update.
	 *
	 * @param array $automatic - Automatic update array.
	 */
	public function WPUpdate( $automatic ) {
		if ( isset( $automatic['core'][0] ) ) {
			$obj = $automatic['core'][0];
			$old_version = get_bloginfo( 'version' );
			$this->plugin->alerts->Trigger(
				6004, array(
					'OldVersion' => $old_version,
					'NewVersion' => $obj->item->version . ' (auto update)',
				)
			);
		}
	}

	/**
	 * Purge log files older than one month.
	 */
	public function LogFilesPruning() {
		if ( $this->plugin->GetGlobalOption( 'purge-404-log', 'off' ) == 'on' ) {
			$upload_dir = wp_upload_dir();
			$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/404s/';
			if ( is_dir( $uploads_dir_path ) ) {
				if ( $handle = opendir( $uploads_dir_path ) ) {
					while ( false !== ($entry = readdir( $handle )) ) {
						if ( '.' != $entry && '..' != $entry ) {
							if ( strpos( $entry, '6007' ) && file_exists( $uploads_dir_path . $entry ) ) {
								$modified = filemtime( $uploads_dir_path . $entry );
								if ( $modified < strtotime( '-4 weeks' ) ) {
									// Delete file.
									unlink( $uploads_dir_path . $entry );
								}
							}
						}
					}
					closedir( $handle );
				}
			}
		}
		if ( 'on' == $this->plugin->GetGlobalOption( 'purge-visitor-404-log', 'off' ) ) {
			$upload_dir = wp_upload_dir();
			$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/404s/';
			if ( is_dir( $uploads_dir_path ) ) {
				if ( $handle = opendir( $uploads_dir_path ) ) {
					while ( false !== ( $entry = readdir( $handle ) ) ) {
						if ( $entry != '.' && $entry != '..' ) {
							if ( strpos( $entry, '6023' ) && file_exists( $uploads_dir_path . $entry ) ) {
								$modified = filemtime( $uploads_dir_path . $entry );
								if ( $modified < strtotime( '-4 weeks' ) ) {
									// Delete file.
									unlink( $uploads_dir_path . $entry );
								}
							}
						}
					}
					closedir( $handle );
				}
			}
		}
	}

	/**
	 * Events from 6008 to 6018.
	 *
	 * @param array $whitelist - White list options.
	 */
	public function EventOptions( $whitelist = null ) {
		// Filter global arrays for security.
		$post_array = filter_input_array( INPUT_POST );
		$get_array = filter_input_array( INPUT_GET );

		if ( isset( $post_array['option_page'] ) && 'reading' === $post_array['option_page'] ) {
			$old_status = (int) get_option( 'blog_public', 1 );
			$new_status = isset( $post_array['blog_public'] ) ? 0 : 1;
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6008, array(
						'Status' => ( 0 === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}
		}

		if ( isset( $post_array['option_page'] ) && 'discussion' === $post_array['option_page'] ) {
			$old_status = get_option( 'default_comment_status', 'closed' );
			$new_status = isset( $post_array['default_comment_status'] ) ? 'open' : 'closed';
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6009, array(
						'Status' => ( 'open' === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}

			$old_status = (int) get_option( 'require_name_email', 0 );
			$new_status = isset( $post_array['require_name_email'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6010, array(
						'Status' => ( 1 === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}

			$old_status = (int) get_option( 'comment_registration', 0 );
			$new_status = isset( $post_array['comment_registration'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6011, array(
						'Status' => ( 1 === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}

			$old_status = (int) get_option( 'close_comments_for_old_posts', 0 );
			$new_status = isset( $post_array['close_comments_for_old_posts'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$value = isset( $post_array['close_comments_days_old'] ) ? $post_array['close_comments_days_old'] : 0;
				$this->plugin->alerts->Trigger(
					6012, array(
						'Status' => ( 1 === $new_status ) ? 'Enabled' : 'Disabled',
						'Value' => $value,
					)
				);
			}

			$old_value = get_option( 'close_comments_days_old', 0 );
			$new_value = isset( $post_array['close_comments_days_old'] ) ? $post_array['close_comments_days_old'] : 0;
			if ( $old_value !== $new_value ) {
				$this->plugin->alerts->Trigger(
					6013, array(
						'OldValue' => $old_value,
						'NewValue' => $new_value,
					)
				);
			}

			$old_status = (int) get_option( 'comment_moderation', 0 );
			$new_status = isset( $post_array['comment_moderation'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6014, array(
						'Status' => ( 1 === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}

			$old_status = (int) get_option( 'comment_whitelist', 0 );
			$new_status = isset( $post_array['comment_whitelist'] ) ? 1 : 0;
			if ( $old_status !== $new_status ) {
				$this->plugin->alerts->Trigger(
					6015, array(
						'Status' => ( 1 === $new_status ) ? 'Enabled' : 'Disabled',
					)
				);
			}

			$old_value = get_option( 'comment_max_links', 0 );
			$new_value = isset( $post_array['comment_max_links'] ) ? $post_array['comment_max_links'] : 0;
			if ( $old_value !== $new_value ) {
				$this->plugin->alerts->Trigger(
					6016, array(
						'OldValue' => $old_value,
						'NewValue' => $new_value,
					)
				);
			}

			$old_value = get_option( 'moderation_keys', 0 );
			$new_value = isset( $post_array['moderation_keys'] ) ? $post_array['moderation_keys'] : 0;
			if ( $old_value !== $new_value ) {
				$this->plugin->alerts->Trigger( 6017, array() );
			}

			$old_value = get_option( 'blacklist_keys', 0 );
			$new_value = isset( $post_array['blacklist_keys'] ) ? $post_array['blacklist_keys'] : 0;
			if ( $old_value !== $new_value ) {
				$this->plugin->alerts->Trigger( 6018, array() );
			}
		}
		return $whitelist;
	}

	/**
	 * Write Log.
	 *
	 * Write a new line on 404 log file.
	 * Folder: /uploads/wp-security-audit-log/404s/
	 *
	 * @param int    $attempts - Number of attempt.
	 * @param string $ip - IP address.
	 * @param string $username - Username.
	 * @param bool   $logged_in - True if logged in.
	 */
	private function WriteLog( $attempts, $ip, $username = '', $logged_in = true ) {
		$name_file = null;

		if ( $logged_in ) {
			if ( 'on' === $this->plugin->GetGlobalOption( 'log-404', 'off' ) ) {
				// Filter global arrays for security.
				$server_array = filter_input_array( INPUT_SERVER );

				// Request URL.
				$request_uri = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
				$url = home_url() . $request_uri;

				// Get option to log referrer.
				$log_referrer = $this->plugin->GetGlobalOption( 'log-404-referrer' );

				// Check localhost.
				if ( '127.0.0.1' == $ip || '::1' == $ip ) {
					$ip = 'localhost';
				}

				if ( 'on' === $log_referrer ) {
					// Get the referer.
					$referrer = ( isset( $server_array['HTTP_REFERER'] ) ) ? $server_array['HTTP_REFERER'] : false;

					// Data to write.
					$data = '';

					// Append IP if it exists.
					$data = ( $ip ) ? $ip . ',' : '';

					// Create/Append to the log file.
					$data = $data . 'Request URL ' . $url . ',Referer ' . $referrer . ',';
				} else {
					// Data to write.
					$data = '';

					// Append IP if it exists.
					$data = ( $ip ) ? $ip . ',' : '';

					// Create/Append to the log file.
					$data = $data . 'Request URL ' . $url . ',';
				}

				if ( ! is_user_logged_in() ) {
					$username = '';
				} else {
					$username = $username . '_';
				}

				$upload_dir = wp_upload_dir();
				$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/404s/';
				$uploads_url = trailingslashit( $upload_dir['baseurl'] ) . 'wp-security-audit-log/404s/';

				// Check directory.
				if ( $this->CheckDirectory( $uploads_dir_path ) ) {
					$filename = '6007_' . date( 'Ymd' ) . '.log';
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
			}
		} else {
			if ( 'on' === $this->plugin->GetGlobalOption( 'log-visitor-404', 'off' ) ) {
				// Filter global arrays for security.
				$server_array = filter_input_array( INPUT_SERVER );

				// Request URL.
				$request_uri = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
				$url = home_url() . $request_uri;

				// Get option to log referrer.
				$log_referrer = $this->plugin->GetGlobalOption( 'log-visitor-404-referrer' );

				// Check localhost.
				if ( '127.0.0.1' == $ip || '::1' == $ip ) {
					$ip = 'localhost';
				}

				if ( 'on' === $log_referrer ) {
					// Get the referer.
					$referrer = ( isset( $server_array['HTTP_REFERER'] ) ) ? $server_array['HTTP_REFERER'] : false;

					// Data to write.
					$data = '';

					// Append IP if it exists.
					$data = ( $ip ) ? $ip . ',' : '';

					// Create/Append to the log file.
					$data = $data . 'Request URL ' . $url . ',Referer ' . $referrer . ',';
				} else {
					// Data to write.
					$data = '';

					// Append IP if it exists.
					$data = ( $ip ) ? $ip . ',' : '';

					// Create/Append to the log file.
					$data = $data . 'Request URL ' . $url . ',';
				}

				$username = '';
				$upload_dir     = wp_upload_dir();
				$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log/404s/';
				$uploads_url     = trailingslashit( $upload_dir['baseurl'] ) . 'wp-security-audit-log/404s/';

				// Check directory.
				if ( $this->CheckDirectory( $uploads_dir_path ) ) {
					$filename = '6023_' . date( 'Ymd' ) . '.log';
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
			}
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
