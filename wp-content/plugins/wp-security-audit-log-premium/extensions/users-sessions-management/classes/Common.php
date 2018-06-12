<?php
/**
 * Class: Utility Class
 *
 * Utility class for common function.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_User_Management_Common
 *
 * Utility class, used for all the common functions used in the plugin.
 *
 * @package user-session-management-wsal
 */
class WSAL_User_Management_Common {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	public $wsal = null;

	/**
	 * Method: Constructor
	 *
	 * @param object $wsal - Instance of WpSecurityAuditLog.
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $wsal ) {
		$this->wsal = $wsal;

		// Update sessions transient with user's latest activity.
		add_action( 'wsal_logged_alert', array( $this, 'update_sessions_transient' ), 10, 3 );
	}

	/**
	 * Method: Update sessions transient after an alert is logged.
	 *
	 * @param WSAL_Models_Occurrence $occ – Occurrence object.
	 * @param int                    $type – Alert code.
	 * @param array                  $data – Alert data.
	 */
	public function update_sessions_transient( $occ, $type, $data ) {
		// Return if user id is not set.
		if ( ! isset( $data['CurrentUserID'] ) || empty( $data['CurrentUserID'] ) ) {
			return;
		}

		// Get user sessions transient.
		$transient = 'wsal-users_sessions';
		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient'; // Check for multisite.
		$user_sessions = $get_fn( $transient );

		// Return if sessions transient don't exist.
		if ( false === $user_sessions ) {
			return;
		}

		// Set user data.
		$user_id    = $data['CurrentUserID'];
		$session_id = isset( $data['SessionID'] ) ? $data['SessionID'] : false;

		// Check if session exists against user id.
		if ( isset( $user_sessions[ $user_id ] ) && ! empty( $user_sessions[ $user_id ] ) ) {
			$sessions = $user_sessions[ $user_id ];

			if ( function_exists( 'array_column' ) ) {
				// Get the token hash columns.
				$token_hash = array_column( $sessions, 'token_hash' );

				// Search the sessions and match sessions token.
				$session_key = array_search( $session_id, $token_hash, true );

				// If the session exists then.
				if ( false !== $session_key ) {
					// Fetch the last alert data.
					if ( isset( $sessions[ $session_key ]['last_alert'] ) ) {
						$last_alert = $sessions[ $session_key ]['last_alert'];
					} else {
						$last_alert['created_on'] = 0;
					}

					// Check if the current alert timestamp is greater than already stored timestamp in sessions.
					if ( $occ->created_on > $last_alert['created_on'] ) {
						// If that's the case, then update last alert data.
						$sessions[ $session_key ]['last_alert'] = array(
							'created_on' => $occ->created_on,
							'message'    => $occ->GetMessage( array( $this, 'meta_formatter' ) ),
							'client_ip'  => $data['ClientIP'],
						);

						// Store sessions to the original sessions array.
						$user_sessions[ $user_id ] = $sessions;

						// Update sessions transient.
						$set_fn = $this->wsal->IsMultisite() ? 'set_site_transient' : 'set_transient';
						$set_fn( $transient, $user_sessions, DAY_IN_SECONDS );
					}
				}
			} else {
				// Fallback for PHP versions less than 5.5.
				foreach ( $sessions as $key => $session ) {
					// If session id exists in user sessions.
					if ( $session_id === $session['token_hash'] ) {
						// Then update last alert data.
						$sessions[ $key ]['last_alert'] = array(
							'created_on' => $occ->created_on,
							'message'    => $occ->GetMessage( array( $this, 'meta_formatter' ) ),
							'client_ip'  => $data['ClientIP'],
						);
					}
				}

				// Store sessions to the original sessions array.
				$user_sessions[ $user_id ] = $sessions;

				// Update sessions transient.
				$set_fn = $this->wsal->IsMultisite() ? 'set_site_transient' : 'set_transient';
				$set_fn( $transient, $user_sessions, DAY_IN_SECONDS );
			}
		}
	}

	/**
	 * Set the option by name with the given value.
	 *
	 * @param string $option - Option name.
	 * @param mixed  $value - Value.
	 */
	public function AddGlobalOption( $option, $value ) {
		$this->wsal->SetGlobalOption( $option, $value );
	}

	/**
	 * Delete the option by name.
	 *
	 * @param string $option - Option name.
	 * @return boolean - Result
	 */
	public function DeleteGlobalOption( $option ) {
		return $this->wsal->DeleteByName( $option );
	}

	/**
	 * Get the option by name.
	 *
	 * @param string $option - Option name.
	 * @return mixed - Value
	 */
	public function GetOptionByName( $option, $default = 0 ) {
		return $this->wsal->GetGlobalOption( $option, $default );
	}

	public function SetMultiSessions( $status, $emails = null ) {
		$opt_name = 'user-management-multi-sessions-notify';
		$this->SaveNotify( $opt_name, $status, $emails );
	}

	public function SetBlocked( $status, $emails = null ) {
		$opt_name = 'user-management-blocked-notify';
		$this->SaveNotify( $opt_name, $status, $emails );
	}

	public function GetMultiSessions() {
		$opt_name = 'user-management-multi-sessions-notify';
		$result = $this->GetOptionByName( $opt_name );
		return $result;
	}

	public function GetBlocked() {
		$opt_name = 'user-management-blocked-notify';
		$result = $this->GetOptionByName( $opt_name );
		return $result;
	}

	/**
	 * Get Auto Terminate Sessions Option.
	 *
	 * @return stdClass
	 */
	public function get_auto_sessions_terminate() {
		$opt_name = 'user-management-auto-terminate-sessions';
		$result = $this->GetOptionByName( $opt_name );
		return $result;
	}

	/**
	 * Set Auto Terminate Sessions Option.
	 *
	 * @param int $status – Enabled/Disabled status.
	 * @param int $hours – Number of hours.
	 */
	public function set_auto_sessions_terminate( $status, $hours = null ) {
		$opt_name = 'user-management-auto-terminate-sessions';
		if ( 1 === $status ) {
			$data = new stdClass();
			$data->status = $status;
			$data->hours  = $hours;
			$result = $this->AddGlobalOption( $opt_name, $data );
		} else {
			$this->DeleteGlobalOption( 'wsal-' . $opt_name );
		}
	}

	/**
	 * Get user roles of the user.
	 *
	 * @param int    $user_id - User ID.
	 * @param string $blog_role - User role.
	 * @return string - Comma separeted roles
	 */
	public function GetUserRoles( $user_id, $blog_role, $userblog_id ) {
		$userRoles = array();

		if ( is_multisite() && is_super_admin( $user_id ) ) {
			$userRoles[] = 'Superadmin';
		}

		if ( ! empty( $blog_role ) ) {
			$userRoles[] = ucwords( $blog_role );
		} else {
			$theuser = new WP_User( $user_id, $userblog_id );
			if ( ! empty( $theuser->roles ) && is_array( $theuser->roles ) ) {
				foreach ( $theuser->roles as $role ) {
					$userRoles[] = ucwords( $role );
				}
			}
		}

		if ( empty( $userRoles ) ) {
			$userRoles[] = '<i>N/A</i>';
		}
		return implode( ', ', array_unique( $userRoles ) );
	}

	/**
	 * Alerts Timestamp
	 * Server's timezone or WordPress' timezone
	 *
	 * @return int $gmt_offset_sec
	 */
	public function GetGmtOffset() {
		$timezone = $this->wsal->settings->GetTimezone();
		$gmt_offset = 0;
		if ( $timezone ) {
			$gmt_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		} else {
			$gmt_offset = date( 'Z' );
		}
		return $gmt_offset;
	}

	/**
	 * Datetime used in the Alerts.
	 */
	public function GetDatetimeFormat() {
		return $this->wsal->settings->GetDatetimeFormat();
	}

	/**
	 * Date Format from WordPress General Settings.
	 */
	public function GetDateFormat() {
		return $this->wsal->settings->GetDateFormat();
	}

	/**
	 * Time Format from WordPress General Settings.
	 */
	public function GetTimeFormat() {
		return $this->wsal->settings->GetTimeFormat();
	}

	/**
	 * Count the sessions by user ID.
	 *
	 * @param int $user_id
	 * @return int count
	 */
	public function CountSessionsByUser( $user_id ) {
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
			$session_tokens = maybe_unserialize( $session_tokens );
		}
		return count( $session_tokens );
	}

	/**
	 * Check users sessions limit.
	 *
	 * @param int $blog_id - Blog id or site id.
	 * @param int $limit - Number of results.
	 * @return bool - True if in limit | False if not.
	 * @since 3.0
	 */
	public function is_users_sessions_in_limit( $blog_id = 0, $limit = 101 ) {
		$args = array(
			'number'     => $limit,
			'blog_id'    => $blog_id,
			'meta_query' => array(
				array(
					'key'     => 'session_tokens',
					'compare' => 'EXISTS',
				),
			),
		);
		$users = new WP_User_Query( $args );
		$count = count( $users->get_results() );

		if ( $count < 100 ) {
			return true;
		} elseif ( $count >= 100 ) {
			return false;
		}
		return false;
	}

	/**
	 * Get all users with active sessions
	 *
	 * @param int $blog_id - Sessions of a specific blog id.
	 * @param int $limit - Number of results.
	 * @return WP_User_Query
	 */
	public function GetUsersWithSessions( $blog_id = 0, $limit = 101 ) {
		$args = array(
			'number'     => $limit,
			'blog_id'    => $blog_id,
			'meta_query' => array(
				array(
					'key'     => 'session_tokens',
					'compare' => 'EXISTS',
				),
			),
		);
		$users = new WP_User_Query( $args );
		return $users;
	}

	/**
	 * Get all raw session meta from all users
	 *
	 * @param int $offset - Query sessions offset.
	 * @return array
	 */
	public function GetAllSessionsRaw( $offset = false ) {
		// Call to global db object.
		global $wpdb;

		// Array to store results.
		$results  = array();

		// Query the sessions based on limit.
		if ( false === $offset ) {
			$sessions = $wpdb->get_results( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'session_tokens' LIMIT 0, 2000" );
		} else {
			$sessions = $wpdb->get_results( "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'session_tokens' LIMIT $offset, 10" );
		}

		$sessions = wp_list_pluck( $sessions, 'meta_value' );
		$sessions = array_map( 'maybe_unserialize', $sessions );

		foreach ( $sessions as $session ) {
			if ( ! is_array( $session ) && is_string( $session ) ) {
				$session = maybe_unserialize( $session );
			}
			if ( is_array( $session ) ) {
				$results = array_merge( $results, $session );
			}
		}
		return (array) $results;
	}

	/**
	 * Method: Return count of user sessions.
	 *
	 * @param int $blog_id – Sessions of a specific blog id.
	 * @return int – Users sessions count.
	 * @since 3.1.0
	 */
	public function get_all_sessions_count( $blog_id = 0 ) {
		// Call to global db object.
		global $wpdb;

		// Array to store results.
		$results = array();

		// Query the count.
		$query_result = $wpdb->get_results( "SELECT COUNT( meta_value ) AS sessions_count FROM $wpdb->usermeta WHERE meta_key = 'session_tokens' LIMIT 0, 2000" );

		// Set initial count to zero.
		$sessions_count = 0;

		// Check if the query result is array.
		if ( ! empty( $query_result ) && is_array( $query_result ) ) {
			// Get sessions count.
			$sessions_count = (int) $query_result[0]->sessions_count;
		}

		// Return count.
		return $sessions_count;
	}

	/**
	 * Get all sessions from all users
	 *
	 * @param int   $blog_id - Sessions of a specific blog id.
	 * @param int   $offset - Query sessions offset.
	 * @param mixed $flag - Flag to indicate the stage of the plugin.
	 * @return array
	 */
	public function GetAllSessions( $blog_id = 0, $offset = false, $flag = null ) {
		$results  = array();
		$users    = $this->GetUsersWithSessions( $blog_id )->get_results();
		$sessions = $this->GetAllSessionsRaw( $offset );
		foreach ( $users as $user ) {
			$user_sessions = get_user_meta( $user->ID, 'session_tokens', true );
			if ( ! is_array( $user_sessions ) && is_string( $user_sessions ) ) {
				$user_sessions = maybe_unserialize( $user_sessions );
			}

			foreach ( $sessions as $session ) {
				if ( is_array( $user_sessions ) ) {
					foreach ( $user_sessions as $token_hash => $user_session ) {
						// Loose comparison needed.
						if ( $user_session == $session ) {
							// Set last array.
							$last_alert = array();

							// Check if activate_plugin flag is not set.
							if ( 'activate_plugin' !== $flag ) {
								// Get last user alert.
								$user_last_alert = $this->GetLastUserAlert( $user->user_login, $token_hash, $blog_id );

								// Check for empty alert.
								if ( ! $user_last_alert instanceof stdClass ) {
									$last_alert['created_on'] = $user_last_alert->created_on;
									$last_alert['message']    = $user_last_alert->message;
									$last_alert['client_ip']  = $user_last_alert->GetMetaValue( 'ClientIP' );
								} else {
									$last_alert['created_on'] = false;
									$last_alert['message']    = $user_last_alert->message;
									$last_alert['client_ip']  = false;
								}
							}

							$results[ $user->ID ][] = array(
								'user_id'    => $user->ID,
								'username'   => $user->user_login,
								'name'       => $user->display_name,
								'email'      => $user->user_email,
								'role'       => ! empty( $user->roles[0] ) ? $user->roles[0] : '',
								'blog_id'    => $blog_id,
								'created'    => $user_session['login'],
								'expiration' => $user_session['expiration'],
								'ip'         => $user_session['ip'],
								'user_agent' => $user_session['ua'],
								'token_hash' => $token_hash,
								'last_alert' => array(
									'created_on' => isset( $last_alert['created_on'] ) ? $last_alert['created_on'] : false,
									'message'    => isset( $last_alert['message'] ) ? $last_alert['message'] : false,
									'client_ip'  => isset( $last_alert['client_ip'] ) ? $last_alert['client_ip'] : false,
								),
							);
						}
					}
				}
			}
		}
		return array_unique( $results, SORT_REGULAR );
	}

	/**
	 * Destroy a specfic session for a specfic user
	 *
	 * @param int    $user_id
	 * @param string $token_hash
	 * @return void
	 */
	public function DestroyUserSession( $user_id, $token_hash ) {
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		$target_session_id = '';

		$target_session_tokens = $session_tokens;
		if ( ! empty( $target_session_tokens ) ) {
			end( $target_session_tokens );
			$target_session_id = key( $target_session_tokens );
		}

		if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
			$session_tokens = maybe_unserialize( $session_tokens );
		}
		if ( isset( $session_tokens[ $token_hash ] ) ) {
			unset( $session_tokens[ $token_hash ] );
		}

		if ( empty( $session_tokens ) ) {
			// Deleted all the session of user.
			delete_user_meta( $user_id, 'session_tokens' );

			$user_info  = get_userdata( $user_id );
			$username   = $user_info->user_login;

			$this->wsal->alerts->Trigger(
				1007, array(
					'TargetUserName'    => $username,
					'TargetSessionID'   => $target_session_id,
				), true
			);
		} else {
			update_user_meta( $user_id, 'session_tokens', $session_tokens );

			$user_info  = get_userdata( $user_id );
			$username   = $user_info->user_login;
			$this->wsal->alerts->Trigger(
				1007, array(
					'TargetUserName'    => $username,
					'TargetSessionID'   => $target_session_id,
				), true
			);
		}

		// Delete transient.
		$delete_fn = $this->wsal->IsMultisite() ? 'delete_site_transient' : 'delete_transient'; // Check for multisite.
		$delete_fn( 'wsal-users_sessions' );
	}

	/**
	 * Get last user event.
	 *
	 * @param string $value user login name
	 * @param string $session
	 * @return stdClass $lastAlert
	 */
	public function GetLastUserAlert( $value, $session, $blog_id = 0 ) {
		$lastAlert = null;

		$userId = get_user_by( 'login', $value );
		$userId = $userId ? $userId->ID : -1;
		if ( $userId == -1 ) {
			$userId = get_user_by( 'slug', $value );
			$userId = $userId ? $userId->ID : -1;
		}

		$query = new WSAL_Models_OccurrenceQuery();
		$query->addMetaJoin();
		$query->addCondition(
			'( meta.name = "SessionID" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )',
			json_encode( $session )
		);
		if ( $blog_id ) {
			$query->addCondition( 'site_id = %s ', $blog_id );
		}
		$result = $this->ExcuteQuery( $query );

		if ( empty( $result ) ) {
			$query = new WSAL_Models_OccurrenceQuery();
			$query->addMetaJoin();
			$query->addORCondition(
				array(
					'( meta.name = "Username" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) ) ' => json_encode( $value ),
					'( meta.name = "CurrentUserID" AND TRIM(BOTH "\"" FROM meta.value) = TRIM(BOTH "\"" FROM %s) )' => json_encode( $userId ),
				)
			);
			if ( $blog_id ) {
				$query->addCondition( 'site_id = %s ', $blog_id );
			}
			$result = $this->ExcuteQuery( $query );
		}

		if ( ! empty( $result ) ) {
			$lastAlert = $result[0];
			$lastAlert->message = $lastAlert->GetMessage( array( $this, 'meta_formatter' ) );
		} else {
			$lastAlert = new stdClass();
			$lastAlert->message = esc_html__( 'No activity found for this user', 'wp-security-audit-log' );
		}

		return $lastAlert;
	}

	/**
	 * Get the last one ordered by created_on DESC.
	 */
	private function ExcuteQuery( $query ) {
		$query->addOrderBy( 'created_on', true );
		$query->setLimit( 1 );
		return $query->getAdapter()->Execute( $query );
	}

	/**
	 * Send notify email.
	 *
	 * @param string   $type multiple|blocked
	 * @param stdClass $result
	 * @param WP_User  $user
	 */
	public function AlertByEmail( $type, $result, $user ) {
		error_log( 'WP Users Management Sessions Alert' );
		$url = $this->GetPageUrl();
		$timestamp = time() + $this->GetGmtOffset();
		$current_date = date( $this->GetDateFormat(), $timestamp );
		$current_time = date( $this->GetTimeFormat(), $timestamp );
		$current_ip = $this->wsal->settings->GetMainClientIP();
		$site_url = get_site_url();

		$headers = "MIME-Version: 1.0\r\n";

		switch ( $type ) {
			case 'multiple':
				$subject = sprintf( __( 'Multiple Same Users Sessions Alert on %s', 'wp-security-audit-log' ), get_bloginfo( 'name' ) );
				$content = '<p>Two or more people are logged in to WordPress at ' . $site_url . ' with the username <strong>' . $user->display_name . '</strong>. Here are the session details:</p>';
				$content .= $this->GetSessionsByUserId( $user->ID, $type );
				break;

			case 'blocked':
				$subject = sprintf( __( 'User Login Attempt Blocked on %s', 'wp-security-audit-log' ), get_bloginfo( 'name' ) );
				$content = '<p>Someone tried to login to the WordPress at ' . $site_url . ' with the username <strong>' . $user->display_name . '</strong>. Since there was already an existing session with that user this login was blocked.</p>';
				$content .= $this->GetSessionsByUserId( $user->ID, $type );
				$content .= '<p><strong>Blocked Session:</strong><br>Login attempted on: ' . $current_date . ' ' . $current_time . '<br>Source IP of login attempt: ' . $current_ip . '</p>';
				break;
		}
		$content .= '<p>' . sprintf( __( 'Click <a href="%s">here</a> to login to your WordPress and see all the logged in sessions and terminate any of them.', 'wp-security-audit-log' ), $url ) . '</p>';
		add_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );

		add_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );

		$res = wp_mail( $result->emails, $subject, $content, $headers );

		remove_filter( 'wp_mail_content_type', array( $this, '_set_html_content_type' ) );
		remove_filter( 'wp_mail_from', array( $this, 'custom_wp_mail_from' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'custom_wp_mail_from_name' ) );
		error_log( 'Email success: ' . print_r( $res, true ) );
	}

	/**
	 * Get sessions from Administrator users role
	 *
	 * @param array $user_sessions – Array of current blog sessions.
	 * @return array
	 */
	public function CountAdministratorRole( $user_sessions = array() ) {
		// If user sessions array is empty then return 0.
		if ( empty( $user_sessions ) ) {
			return 0;
		}

		// Admin roles array.
		$arr_roles = array();

		// Check for admin roles in the user sessions array.
		foreach ( $user_sessions as $user_session ) {
			foreach ( $user_session as $session ) {
				if ( 'administrator' === $session['role'] ) {
					$arr_roles[] = $session;
				}
			}
		}

		// Return count of admin roles.
		return count( $arr_roles );
	}

	/**
	 * Check Cookie Login data
	 *
	 * @param string $username
	 * @return bool
	 */
	public function CheckLoggedInCookie( $username ) {
		$site_id = $this->GetCurrentSiteId();
		if ( isset( $_COOKIE['wordpress_known_user_cookie'] ) ) {
			$cookieArr = explode( '|', $_COOKIE['wordpress_known_user_cookie'] );
			$cookie_login = $cookieArr[0];
			$cookie_site_id = $cookieArr[1];
			$cookie_hash = $cookieArr[2];

			if ( $cookie_login == $username && $cookie_site_id == $site_id ) {
				$current_user = get_user_by( 'login', $cookie_login );

				$session_tokens = get_user_meta( $current_user->ID, 'session_tokens', true );
				if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
					$session_tokens = maybe_unserialize( $session_tokens );
				}
				foreach ( $session_tokens as $hash_key => $session_token ) {
					if ( $cookie_hash == $hash_key ) {
						$this->DestroyUserSession( $current_user->ID, $cookie_hash );
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Set custom cookie for recognize known users
	 *
	 * @param int $user_id
	 */
	public function setCustomCookie( $user_id ) {
		$site_id = $this->GetCurrentSiteId();
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
			$session_tokens = maybe_unserialize( $session_tokens );
		}
		$current_user = get_user_by( 'id', $user_id );
		$expiration = 0;
		$token = '';
		$secure = is_ssl();
		foreach ( $session_tokens as $token_hash => $session_token ) {
			if ( $expiration < $session_token['expiration'] ) {
				$expiration = $session_token['expiration'];
				$token = $token_hash;
			}
		}

		$logged_in_cookie = $current_user->user_login . '|' . $site_id . '|' . $token;

		setcookie( 'wordpress_known_user_cookie', $logged_in_cookie, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( COOKIEPATH != SITECOOKIEPATH ) {
			setcookie( 'wordpress_known_user_cookie', $logged_in_cookie, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}
	}

	/**
	 * Save the notification on the DB.
	 */
	private function SaveNotify( $opt_name, $status, $emails ) {
		if ( $status == 1 ) {
			$data = new stdClass();
			$data->status = $status;
			$data->emails = $emails;
			$result = $this->AddGlobalOption( $opt_name, $data );
		} else {
			$this->DeleteGlobalOption( 'wsal-' . $opt_name );
		}
	}

	/**
	 * Get the page URL.
	 */
	private function GetPageUrl() {
		$class = $this->wsal->views->FindByClassName( 'WSAL_User_Management_Views' );
		if ( false === $class ) {
			$class = new WSAL_User_Management_Views( $this->wsal );
		}
		return esc_attr( $class->GetUrl() );
	}

	/**
	 * Get sessions by user ID and by type.
	 *
	 * @param int    $user_id
	 * @param string $type multiple|blocked
	 * @return string $content HTML
	 */
	private function GetSessionsByUserId( $user_id, $type ) {
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		$content = '';
		if ( ! empty( $session_tokens ) ) {
			if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
				$session_tokens = maybe_unserialize( $session_tokens );
			}
			if ( $type == 'multiple' ) {
				$content = '<ul style="padding:0;">';
			} else {
				$content = '<p>';
			}
			foreach ( $session_tokens as $key => $session ) {
				$offset = $this->GetGmtOffset();
				$date = date( $this->GetDateFormat(), $session['login'] + $offset );
				$time = date( $this->GetTimeFormat(), $session['login'] + $offset );
				if ( $type == 'multiple' ) {
					$content .= '<li><p>Session ID: ' . $key . '<br>Date: ' . $date . '<br>Time: ' . $time . '<br>Source IP: ' . $session['ip'] . '</p></li>';
				} else {
					$content .= '<strong>Existing session:</strong><br>Session ID: ' . $key . '<br>Date Created: ' . $date . '<br>Time Created: ' . $time . '<br>Source IP: ' . $session['ip'] . '<br>';
				}
			}
			if ( $type == 'multiple' ) {
				$content .= '</ul>';
			} else {
				$content .= '</p>';
			}
		}
		return $content;
	}

	/**
	 * Get session IPs.
	 *
	 * @param int $user_id user ID
	 * @return string comma separated roles
	 */
	public function GetSessionIPs( $user_id ) {
		$ip_addresses = array();
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		if ( ! empty( $session_tokens ) ) {
			if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
				$session_tokens = maybe_unserialize( $session_tokens );
			}
			foreach ( $session_tokens as $key => $session ) {
				array_push( $ip_addresses, $session['ip'] );
			}
		}
		$ip_addresses = array_unique( $ip_addresses );
		return implode( ', ', $ip_addresses );
	}

	/**
	 * Filter the mail content type.
	 */
	final public function _set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Return if there is a from-email in the setting or the original passed.
	 *
	 * @param string $original_email_from original passed
	 * @return string from email_address
	 */
	final public function custom_wp_mail_from( $original_email_from ) {
		$email_from = $this->GetOptionByName( 'from-email' );
		if ( ! empty( $email_from ) ) {
			return $email_from;
		} else {
			return $original_email_from;
		}
	}

	/**
	 * Return if there is a display-name in the setting or the original passed.
	 *
	 * @param string $original_email_from_name original passed
	 * @return string name
	 */
	final public function custom_wp_mail_from_name( $original_email_from_name ) {
		$email_from_name = $this->GetOptionByName( 'display-name' );
		if ( ! empty( $email_from_name ) ) {
			return $email_from_name;
		} else {
			return $original_email_from_name;
		}
	}

	/**
	 * Formatter for the alert message by name.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function meta_formatter( $name, $value ) {
		switch ( true ) {
			case $name == '%Message%':
				return esc_html( $value );

			case $name == '%RevisionLink%':
				if ( ! empty( $value ) && $value != 'NULL' ) {
					return '<br>Click <a target="_blank" href="' . $value . '">here</a> to see the content changes.';
				} else {
					return '';
				}

			case $name == '%CommentLink%':
			case $name == '%CommentMsg%':
				return $value;

			case $name == '%EditorLinkPost%':
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the post</a>';

			case $name == '%EditorLinkPage%':
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the page</a>';

			case $name == '%CategoryLink%':
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the category</a>';

			case $name == '%EditorLinkForum%':
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the forum</a>';

			case $name == '%EditorLinkTopic%':
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the topic</a>';

				// Meta value
			case in_array( $name, array( '%MetaValue%', '%MetaValueOld%', '%MetaValueNew%' ) ):
				return '<strong>' . (
					strlen( $value ) > 50 ? (esc_html( substr( $value, 0, 50 ) ) . '&hellip;') : esc_html( $value )
				) . '</strong>';

			case $name == '%ClientIP%':
				if ( is_string( $value ) ) {
					return '<strong>' . str_replace( array( '"', '[', ']' ), '', $value ) . '</strong>';
				} else {
					return '<i>unknown</i>';
				}
				// Link
			case strncmp( $value, 'http://', 7 ) === 0:
			case strncmp( $value, 'https://', 7 ) === 0:
				return '<a href="' . esc_html( $value ) . '"' . ' title="' . esc_html( $value ) . '"' . ' target="_blank">' . esc_html( $value ) . '</a>';

			case '%multisite_text%' === $name:
				if ( $this->wsal->IsMultisite() && $value ) {
					$site_info = get_blog_details( $value, true );
					if ( $site_info ) {
						return ' on site <a href="' . esc_url( $site_info->siteurl ) . '">' . esc_html( $site_info->blogname ) . '</a>';
					}
					return;
				}
				return;

			case '%ReportText%' === $name:
				return;

			case '%ChangeText%' === $name:
				return ' ' . __( 'View the changes in data inspector.', 'wp-security-audit-log' );

			default:
				return '<strong>' . esc_html( $value ) . '</strong>';
		}
	}

	/**
	 * Get the current site_id
	 *
	 * @return int $site_id
	 */
	public function GetCurrentSiteId() {
		$site_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);
		return $site_id;
	}

	/**
	 * Method: Get number of hours since last logged alert.
	 *
	 * @param float $created_on – Timestamp of last logged alert.
	 * @return bool|int – False if $created_on is empty | Number of hours otherwise.
	 */
	public function get_hours_since_last_alert( $created_on ) {
		// If $created_on is empty, then return.
		if ( empty( $created_on ) ) {
			return false;
		}

		// Last alert date.
		$created_date = new DateTime( date( 'Y-m-d H:i:s', $created_on ) );

		// Current date.
		$current_date = new DateTime( 'NOW' );

		// Calculate time difference.
		$time_diff = $current_date->diff( $created_date );
		$diff_days = $time_diff->d; // Difference in number of days.
		$diff_hrs  = $time_diff->h; // Difference in number of hours.
		$total_hrs = ( $diff_days * 24 ) + $diff_hrs; // Total number of hours.

		// Return difference in hours.
		return $total_hrs;
	}
}
