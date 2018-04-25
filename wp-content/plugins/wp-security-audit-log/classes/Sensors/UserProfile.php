<?php
/**
 * Sensor: User Profile
 *
 * User profile sensor file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Profiles sensor.
 *
 * 4000 New user was created on WordPress
 * 4001 User created another WordPress user
 * 4002 The role of a user was changed by another WordPress user
 * 4003 User has changed his or her password
 * 4004 User changed another user's password
 * 4005 User changed his or her email address
 * 4006 User changed another user's email address
 * 4007 User was deleted by another user
 * 4008 User granted Super Admin privileges
 * 4009 User revoked from Super Admin privileges
 * 4013 The forum role of a user was changed by another WordPress user
 * 4014 User opened the profile page of another user
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_UserProfile extends WSAL_AbstractSensor {

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
		add_action( 'user_register', array( $this, 'EventUserRegister' ) );
		add_action( 'edit_user_profile_update', array( $this, 'EventUserChanged' ) );
		add_action( 'personal_options_update', array( $this, 'EventUserChanged' ) );
		add_action( 'delete_user', array( $this, 'EventUserDeleted' ) );
		add_action( 'wpmu_delete_user', array( $this, 'EventUserDeleted' ) );
		add_action( 'set_user_role', array( $this, 'EventUserRoleChanged' ), 10, 3 );

		add_action( 'edit_user_profile', array( $this, 'EventOpenProfile' ), 10, 1 );
	}

	/**
	 * List of super admins.
	 *
	 * @var array
	 */
	protected $old_superadmins;

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		if ( $this->IsMultisite() ) {
			$this->old_superadmins = get_super_admins();
		}
	}

	/**
	 * Triggered when a user is registered.
	 *
	 * @param int $user_id - User ID of the registered user.
	 */
	public function EventUserRegister( $user_id ) {
		$user = get_userdata( $user_id );
		$ismu = function_exists( 'is_multisite' ) && is_multisite();
		$event = $ismu ? 4012 : (is_user_logged_in() ? 4001 : 4000);
		$current_user = wp_get_current_user();
		$this->plugin->alerts->Trigger(
			$event, array(
				'NewUserID' => $user_id,
				'UserChanger' => ! empty( $current_user ) ? $current_user->user_login : '',
				'NewUserData' => (object) array(
					'Username' => $user->user_login,
					'FirstName' => $user->user_firstname,
					'LastName' => $user->user_lastname,
					'Email' => $user->user_email,
					'Roles' => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
				),
			), true
		);
	}

	/**
	 * Triggered when a user role is changed.
	 *
	 * @param int    $user_id - User ID of the user.
	 * @param string $role - String of new roles.
	 * @param string $old_roles - String of old roles.
	 */
	public function EventUserRoleChanged( $user_id, $role, $old_roles ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array['changeit'] ) ) {
			if ( isset( $post_array['_wpnonce'] )
				&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-user_' . $user_id ) ) {
				return false;
			}
		} elseif ( isset( $post_array['changeit'] )
			&& 'Change' === $post_array['changeit']
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'bulk-users' ) ) {
			return false;
		}

		$user = get_userdata( $user_id );
		$bbpress_roles = array( 'bbp_spectator', 'bbp_moderator', 'bbp_participant', 'bbp_keymaster', 'bbp_blocked' );
		// Remove any BBPress roles.
		if ( is_array( $old_roles ) ) {
			foreach ( $old_roles as $value ) {
				if ( in_array( $value, $bbpress_roles ) ) {
					if ( isset( $post_array['bbp-forums-role'] ) && $post_array['bbp-forums-role'] != $value ) {
						$current_user = wp_get_current_user();
						$this->plugin->alerts->TriggerIf(
							4013, array(
								'TargetUsername' => $user->user_login,
								'OldRole' => ucfirst( substr( $value, 4 ) ),
								'NewRole' => ( isset( $post_array['bbp-forums-role'] ) ) ? ucfirst( substr( $post_array['bbp-forums-role'], 4 ) ) : false,
								'UserChanger' => $current_user->user_login,
							)
						);
					}
				}
			}
			$old_roles = array_diff( $old_roles, $bbpress_roles );
		}

		// Get roles.
		$old_role = count( $old_roles ) ? implode( ', ', $old_roles ) : '';
		$new_role = $role;

		// If multisite, then get its URL.
		if ( $this->plugin->IsMultisite() ) {
			$site_id = get_current_blog_id();
		}

		// Alert if roles are changed.
		if ( $old_role != $new_role ) {
			$this->plugin->alerts->TriggerIf(
				4002, array(
					'TargetUserID' => $user_id,
					'TargetUsername' => $user->user_login,
					'OldRole' => $old_role,
					'NewRole' => $new_role,
					'multisite_text' => $this->plugin->IsMultisite() ? $site_id : false,
				), array( $this, 'MustNotContainUserChanges' )
			);
		}
	}

	/**
	 * Triggered when a user changes email, password
	 * or user is granted or revoked super admin access.
	 *
	 * @param int $user_id - User ID of the registered user.
	 */
	public function EventUserChanged( $user_id ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		// Get user data.
		$user = get_userdata( $user_id );

		// Password changed.
		if ( ! empty( $post_array['pass1'] ) && ! empty( $post_array['pass2'] ) ) {
			if ( trim( $post_array['pass1'] ) == trim( $post_array['pass2'] ) ) {
				$event = get_current_user_id() == $user_id ? 4003 : 4004;
				$this->plugin->alerts->Trigger(
					$event, array(
						'TargetUserID' => $user_id,
						'TargetUserData' => (object) array(
							'Username' => $user->user_login,
							'Roles' => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
						),
					)
				);
			}
		}

		// Email changed.
		if ( ! empty( $post_array['email'] ) ) {
			$old_email = $user->user_email;
			$new_email = trim( $post_array['email'] );
			if ( $old_email != $new_email ) {
				$event = get_current_user_id() == $user_id ? 4005 : 4006;
				$this->plugin->alerts->Trigger(
					$event, array(
						'TargetUserID' => $user_id,
						'TargetUsername' => $user->user_login,
						'OldEmail' => $old_email,
						'NewEmail' => $new_email,
					)
				);
			}
		}

		if ( $this->IsMultisite() ) {
			$username = $user->user_login;
			$enabled = isset( $post_array['super_admin'] );

			if ( get_current_user_id() != $user_id ) {
				// Super admin enabled.
				if ( $enabled && ! in_array( $username, $this->old_superadmins ) ) {
					$this->plugin->alerts->Trigger(
						4008, array(
							'TargetUserID' => $user_id,
							'TargetUsername' => $user->user_login,
						)
					);
				}

				// Super admin disabled.
				if ( ! $enabled && in_array( $username, $this->old_superadmins ) ) {
					$this->plugin->alerts->Trigger(
						4009, array(
							'TargetUserID' => $user_id,
							'TargetUsername' => $user->user_login,
						)
					);
				}
			}
		}
	}

	/**
	 * Triggered when a user is deleted.
	 *
	 * @param int $user_id - User ID of the registered user.
	 */
	public function EventUserDeleted( $user_id ) {
		$user = get_userdata( $user_id );
		$role = is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles;
		$this->plugin->alerts->TriggerIf(
			4007, array(
				'TargetUserID' => $user_id,
				'TargetUserData' => (object) array(
					'Username' => $user->user_login,
					'FirstName' => $user->user_firstname,
					'LastName' => $user->user_lastname,
					'Email' => $user->user_email,
					'Roles' => $role ? $role : 'none',
				),
			), array( $this, 'MustNotContainCreateUser' )
		);
	}

	/**
	 * Triggered when a user profile is opened.
	 *
	 * @param object $user - Instance of WP_User.
	 */
	public function EventOpenProfile( $user ) {
		if ( ! empty( $user ) ) {
			$current_user = wp_get_current_user();

			// Filter $_GET array for security.
			$get_array = filter_input_array( INPUT_GET );

			$updated = ( isset( $get_array['updated'] ) ) ? true : false;
			if ( ! empty( $current_user ) && ( $user->ID !== $current_user->ID ) && empty( $updated ) ) {
				$this->plugin->alerts->Trigger(
					4014, array(
						'UserChanger' => $current_user->user_login,
						'TargetUsername' => $user->user_login,
					)
				);
			}
		}
	}

	/**
	 * Must Not Contain Create User.
	 *
	 * @param WSAL_AlertManager $mgr - Instance of WSAL_AlertManager.
	 */
	public function MustNotContainCreateUser( WSAL_AlertManager $mgr ) {
		return ! $mgr->WillTrigger( 4012 );
	}

	/**
	 * Must Not Contain User Changes.
	 *
	 * @param WSAL_AlertManager $mgr - Instance of WSAL_AlertManager.
	 */
	public function MustNotContainUserChanges( WSAL_AlertManager $mgr ) {
		return ! (  $mgr->WillOrHasTriggered( 4010 )
				|| $mgr->WillOrHasTriggered( 4011 )
				|| $mgr->WillOrHasTriggered( 4012 )
				|| $mgr->WillOrHasTriggered( 4000 )
				|| $mgr->WillOrHasTriggered( 4001 )
			);
	}
}
