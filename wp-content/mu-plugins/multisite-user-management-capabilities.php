<?php

/**
 * Plugin Name: Multisite User Management Capabilities
 * Plugin URI: https://thereforei.am/2011/03/15/how-to-allow-administrators-to-edit-users-in-a-wordpress-network/
 * Description: Removes restrictions on capabilities for editing users. In WordPress multisite only site admins can manage users for security reasons. This plugin removes that restriction enabling capabilities to function as expected.
 * Author: Brent Shepard
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Bypasses do_not_allow capability applied for user management.
 */
function mumc_admin_users_caps( $caps, $cap, $user_id, $args ) {
  if ( ! is_multisite() )
    return $caps;

  foreach( $caps as $key => $capability ) {

    if( $capability != 'do_not_allow' )
      continue;

    switch( $cap ) {
      case 'edit_user':
      case 'edit_users':
        $caps[$key] = 'edit_users';
        break;
      case 'delete_user':
      case 'delete_users':
        $caps[$key] = 'delete_users';
        break;
      case 'create_users':
        $caps[$key] = $cap;
        break;
    }
  }

  return $caps;
}
add_filter( 'map_meta_cap', 'mumc_admin_users_caps', 1, 4 );
remove_all_filters( 'enable_edit_any_user_configuration' );
add_filter( 'enable_edit_any_user_configuration', '__return_true');

/**
 * Adds additional permission checking to ensure that user managers cannot
 * edit site admins regardless of capabilities.
 */
function mumc_edit_permission_check() {
  if ( ! is_multisite() )
    return;

  global $current_user, $profileuser;

  $screen = get_current_screen();

  get_currentuserinfo();

  if( ! is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) { // editing a user profile
    if ( is_super_admin( $profileuser->ID ) ) { // trying to edit a superadmin while less than a superadmin
      wp_die( __( 'You do not have permission to edit this user.' ) );
    } elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) { // editing user and edited user aren't members of the same blog
      wp_die( __( 'You do not have permission to edit this user.' ) );
    }
  }

}
add_filter( 'admin_head', 'mumc_edit_permission_check', 1, 4 );