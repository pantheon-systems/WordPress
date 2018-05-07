<?php
/**
 * Extension: Users Sessions Management
 *
 * User sessions management extension for wsal.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WSAL_User_Management_Plugin
 *
 * @package Wsal
 */
class WSAL_User_Management_Plugin {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $wsal = null;

	/**
	 * Method: Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Function to hook at `wsal_init`.
		add_action( 'wsal_init', array( $this, 'wsal_init' ) );

		// Listen to authenticate multiple logins.
		add_filter( 'authenticate', array( $this, 'prevent_concurrent_logins' ), 100, 3 );
		add_action( 'wp_login', array( $this, 'notify_multi_sessions' ), 10, 2 );
	}

	/**
	 * Triggered when the main plugin is loaded.
	 *
	 * @see WpSecurityAuditLog::load()
	 * @param WpSecurityAuditLog $wsal - Instance of WpSecurityAuditLog.
	 */
	public function wsal_init( WpSecurityAuditLog $wsal ) {
		// Autoload files from /classes.
		$wsal->autoloader->Register( 'WSAL_User_Management_', dirname( __FILE__ ) . '/classes' );

		$wsal->usermanagement = new stdClass();
		$wsal->usermanagement->common = new WSAL_User_Management_Common( $wsal );
		$wsal->views->AddFromClass( 'WSAL_User_Management_Views' );
		$this->wsal = $wsal;

		// Cron job to destroy expired sessions.
		add_action( 'destroy_expired', array( $this, 'destroy_sessions_expired' ) );
		if ( ! wp_next_scheduled( 'destroy_expired' ) ) {
			wp_schedule_event( time(), 'hourly', 'destroy_expired' );
		}

		// Cron job to automatically destroy sessions.
		add_action( 'wsal_auto_destroy_sessions', array( $this, 'auto_destroy_sessions' ) );
		if ( ! wp_next_scheduled( 'wsal_auto_destroy_sessions' ) ) {
			wp_schedule_event( time(), 'hourly', 'wsal_auto_destroy_sessions' );
		}
	}

	/**
	 * Only allow one session per user.
	 *
	 * @param WP_User $current_user - User object.
	 * @param string  $username - User name.
	 * @param string  $password - User password.
	 */
	public function prevent_concurrent_logins( $current_user, $username, $password ) {
		// Check $current_user.
		if ( ! $current_user || $current_user instanceof WP_Error ) {
			return $current_user;
		}

		// Get multiple sessions option.
		$multiple_sessions = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-allow-multi-sessions' );

		// If they are blocked then.
		if ( $multiple_sessions && 'allow-limited' !== $multiple_sessions ) {
			// Check for override blocked sessions option.
			$session_override = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-blocked-sessions-override', 'without_warning' );

			// Override previous session without warning.
			if ( 'without_warning' === $session_override ) {
				$this->override_last_user_session( $current_user->ID ); // Override last user session.
				return $current_user; // Return the current user.
			} elseif ( 'with_warning' === $session_override ) { // Override session with password.
				// Get wsal_override_password field from post array.
				$wsal_override_password = filter_input( INPUT_POST, 'wsal_override_password', FILTER_SANITIZE_STRING );

				// If $_POST override password field is empty then show the field.
				if ( empty( $wsal_override_password ) ) {
					// Display session override password field.
					add_action( 'login_form', array( $this, 'override_session_password_field' ) );

					// Set the error message.
					$message = esc_html__( 'Your session is blocked. You can override it with a password.', 'wp-security-audit-log' );
					return new WP_Error( 'login_denied', $message ); // Return error.
				} else { // Verify the password.
					// Get the password from options.
					$override_password = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-sessions-override-password', '' );

					// Password hasher.
					$wsal_hasher = new PasswordHash( 8, true );

					// If override password matches with the password stored in options then override session.
					if ( $wsal_hasher->CheckPassword( $wsal_override_password, $override_password ) ) {
						$this->override_last_user_session( $current_user->ID ); // Override last user session.
						return $current_user; // Return the current user.
					}

					// Get blocked session error message.
					$msg = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-sessions-error-message' );
					if ( empty( $msg ) ) {
						$msg = __( '<strong>ERROR</strong>: Your session was blocked with the <a href="https://en-gb.wordpress.org/plugins/wp-security-audit-log" target="_blank">WP Security Audit Log plugin</a> because there is already another user logged in with the same username. Please contact the site administrator for more information.' );
					}
					return new WP_Error( 'login_denied', $msg ); // Return error.
				}
			} elseif ( 'override_block' === $session_override ) { // Do not allow override.
				return $this->check_multiple_login( $current_user, $username );
			}
		} elseif ( 'allow-limited' === $multiple_sessions ) { // If limited sessions are allowed then.
			// Get the number of sessions allowed.
			$allowed_sessions = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-allowed-sessions-number', 3 );

			// Get current user sessions.
			$session_tokens = get_user_meta( $current_user->ID, 'session_tokens', true );
			if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
				$session_tokens = maybe_unserialize( $session_tokens );
			}

			// Block if the number of sessions is greater or equal to the set limit.
			if ( count( $session_tokens ) >= $allowed_sessions ) {
				return $this->check_multiple_login( $current_user, $username );
			}
		}
		return $current_user;
	}

	/**
	 * Method: Adds override session password field
	 * to WP Login form.
	 *
	 * @since 3.1.4
	 */
	public function override_session_password_field() {
		// Override session password field.
		?>
		<p>
			<label for="wsal_override_password"><?php esc_html_e( 'Session Override Password' ); ?>
				<br>
				<input type="password" name="wsal_override_password" id="wsal_override_password" class="input">
			</label>
		</p>
		<?php
	}

	/**
	 * Method: Override last user session.
	 *
	 * @param int $user_id - User id.
	 * @since 3.1.4
	 */
	public function override_last_user_session( $user_id ) {
		// Get current user sessions.
		$session_tokens = get_user_meta( $user_id, 'session_tokens', true );
		if ( ! is_array( $session_tokens ) && is_string( $session_tokens ) ) {
			$session_tokens = maybe_unserialize( $session_tokens );
		}

		if ( ! empty( $session_tokens ) ) {
			// Override the latest session key.
			end( $session_tokens ); // Go to the end of the array.
			$target_session_id = key( $session_tokens ); // Get the session key.
			$this->wsal->usermanagement->common->DestroyUserSession( $user_id, $target_session_id ); // Destroy it.
		}
	}

	/**
	 * Method: Block Multiple User Login.
	 *
	 * @param WP_User $current_user - Current User object.
	 * @param string  $username - Current username.
	 * @return string|WP_Error - Username if allowed|WP_Error object if blocked.
	 */
	public function check_multiple_login( $current_user, $username ) {
		$users = $this->wsal->usermanagement->common->GetUsersWithSessions()->get_results();
		foreach ( $users as $key => $user ) {
			if ( ! empty( $current_user->ID ) && $current_user->ID == $user->ID ) {
				$is_user_known = $this->wsal->usermanagement->common->CheckLoggedInCookie( $current_user->user_login );
				if ( ! $is_user_known ) {
					// To send email blocked user.
					$result = $this->wsal->usermanagement->common->GetBlocked();
					if ( ! empty( $result ) ) {
						$this->wsal->usermanagement->common->AlertByEmail( 'blocked', $result, $user->data );
					}

					// Login blocked action hook.
					do_action( 'wp_login_blocked', $username );

					// Get blocked session error message.
					$msg = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-sessions-error-message' );
					if ( empty( $msg ) ) {
						$msg = __( '<strong>ERROR</strong>: Your session was blocked with the <a href="https://en-gb.wordpress.org/plugins/wp-security-audit-log" target="_blank">WP Security Audit Log plugin</a> because there is already another user logged in with the same username. Please contact the site administrator for more information.' );
					}
					return new WP_Error( 'login_denied', $msg );
				} else {
					return $current_user;
				}
			}
		}
		return $current_user;
	}

	/**
	 * Notify Multi Sessions.
	 * Trigger the event 1005 User logged in with existing session(s)
	 *
	 * @param string  $user_login - User login name.
	 * @param WP_User $current_user (Optional) - User object.
	 */
	public function notify_multi_sessions( $user_login, $current_user = null ) {
		$count = 0;
		if ( empty( $current_user ) ) {
			$current_user = get_user_by( 'login', $user_login );
		}

		if ( empty( $this->wsal ) || ! $this->wsal->usermanagement instanceof stdClass ) {
			return false;
		}

		if ( ! empty( $current_user->ID ) ) {
			$this->wsal->usermanagement->common->setCustomCookie( $current_user->ID );
			$count = $this->wsal->usermanagement->common->CountSessionsByUser( $current_user->ID );
		}

		$is_allow_multiple = $this->wsal->usermanagement->common->GetOptionByName( 'user-management-allow-multi-sessions' );
		if ( ! $is_allow_multiple ) {
			$result = $this->wsal->usermanagement->common->GetMultiSessions();
			// To send email multiple sessions.
			if ( ! empty( $result ) ) {
				$users = $this->wsal->usermanagement->common->GetUsersWithSessions()->get_results();
				foreach ( $users as $key => $user ) {
					if ( ! empty( $current_user->ID ) && $current_user->ID == $user->ID ) {
						if ( $count > 1 ) {
							$this->wsal->usermanagement->common->AlertByEmail( 'multiple', $result, $user->data );
						}
					}
				}
			}
		}
		if ( $count > 1 ) {
			// Get global POST array.
			$post_array = filter_input_array( INPUT_POST );

			// Check for Ultimate Member plugin.
			if ( isset( $post_array['_um_account'] )
			&& isset( $post_array['_um_account_tab'] )
			&& 'password' === $post_array['_um_account_tab'] ) {
				return; // Return if the data is coming for UM plugin account change password page.
			}

			$user_roles = $this->wsal->settings->GetCurrentUserRoles( $current_user->roles );

			if ( $this->wsal->settings->IsLoginSuperAdmin( $current_user->user_login ) ) {
				$user_roles[] = 'superadmin';
			}
			$ip_addresses = $this->wsal->usermanagement->common->GetSessionIPs( $current_user->ID );
			$this->wsal->alerts->Trigger(
				1005, array(
					'Username' => $current_user->user_login,
					'CurrentUserRoles' => $user_roles,
					'IPAddress' => $ip_addresses,
				), true
			);
			$delete_fn = $this->wsal->IsMultisite() ? 'delete_site_transient' : 'delete_transient'; // Check for multisite.
			$delete_fn( 'wsal-users_sessions_offset' );
		}
	}

	/**
	 * Destroy expired sessions.
	 */
	public function destroy_sessions_expired() {
		$sessions = $this->wsal->usermanagement->common->GetAllSessions( 0, false, 'activate_plugin' );
		if ( ! empty( $sessions ) ) {
			foreach ( $sessions as $user_session ) {
				foreach ( $user_session as $session ) {
					if ( $session['expiration'] < time() ) {
						$this->wsal->usermanagement->common->DestroyUserSession( $session['user_id'], $session['token_hash'] );
					}
				}
			}
		}
	}

	/**
	 * Destroy expired sessions on activation.
	 */
	public function destroy_on_activation() {
		if ( class_exists( 'WpSecurityAuditLog' ) ) {
			$wsal = WpSecurityAuditLog::GetInstance();
			$wsal->autoloader->Register( 'WSAL_User_Management_', dirname( __FILE__ ) . '/classes' );
			$wsal->usermanagement = new stdClass();
			$wsal->usermanagement->common = new WSAL_User_Management_Common( $wsal );
			$this->wsal = $wsal;
			$this->destroy_sessions_expired();
		}
	}

	/**
	 * Cron job for automatically destroy session option.
	 *
	 * @return void
	 */
	public function auto_destroy_sessions() {
		// Check if the setting is enabled.
		$auto_terminate = $this->wsal->usermanagement->common->get_auto_sessions_terminate();
		if ( empty( $auto_terminate->status ) ) {
			return;
		}

		// Get latest user activity.
		$get_fn = $this->wsal->IsMultisite() ? 'get_site_transient' : 'get_transient'; // Check for multisite.
		$users_activity = $get_fn( 'wsal-users_sessions' );

		// Return if transient does not exists.
		if ( false === $users_activity ) {
			return;
		}

		// Get stored number of hours.
		$auto_terminate_hours = ! empty( $auto_terminate->hours ) ? (int) $auto_terminate->hours : '';

		// Loop through the sessions.
		foreach ( $users_activity as $user_id => $sessions ) {
			foreach ( $sessions as $session ) {
				if ( isset( $session['last_alert'] ) ) {
					// Number of hours passed since last user activity.
					$hrs_diff = $this->wsal->usermanagement->common->get_hours_since_last_alert( $session['last_alert']['created_on'] );

					// If number of hrs passed is greater than auto terminate hrs then destroy the session.
					if ( $hrs_diff > $auto_terminate_hours ) {
						$this->wsal->usermanagement->common->DestroyUserSession( $user_id, $session['token_hash'] );
					}
				}
			}
		}
	}
}
