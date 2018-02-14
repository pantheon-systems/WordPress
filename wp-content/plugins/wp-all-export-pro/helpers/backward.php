<?php
/**
 * Contains function which were introduced in late wordpress versions 
 */

if ( ! function_exists('is_network_admin')):
/**
 * Whether the current request is for a network admin screen /wp-admin/network/
 *
 * Does not inform on whether the user is a network admin! Use capability checks to
 * tell if the user should be accessing a section or not.
 *
 * @since 3.1.0
 *
 * @return bool True if inside WordPress network administration pages.
 */
function is_network_admin() {
	if ( defined( 'WP_NETWORK_ADMIN' ) )
		return WP_NETWORK_ADMIN;
	return false;
}
endif;

if ( ! function_exists('is_user_admin')):
/**
 * Whether the current request is for a user admin screen /wp-admin/user/
 *
 * Does not inform on whether the user is an admin! Use capability checks to
 * tell if the user should be accessing a section or not.
 *
 * @since 3.1.0
 *
 * @return bool True if inside WordPress user administration pages.
 */
function is_user_admin() {
	if ( defined( 'WP_USER_ADMIN' ) )
		return WP_USER_ADMIN;
	return false;
}
endif;