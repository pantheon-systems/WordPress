<?php

/**
 *  Prevents OptimizeMember from intefering with our ajax user search
 *
 *  @since 1.6.2
 *  @return void
 */
function affwp_optimize_member_user_query( $search_term = '' ) {

	remove_action( 'pre_user_query', 'c_ws_plugin__optimizemember_users_list::users_list_query', 10 );

}
add_action( 'affwp_pre_search_users', 'affwp_optimize_member_user_query' );

/**
 *  Prevents OptimizeMember from redirecting affiliates to the
 *  "Members Home Page/Login Welcome Page" when they log in
 *
 *  @since 1.7.16
 *  @return boolean
 */
function affwp_optimize_member_prevent_affiliate_redirect( $return, $vars ) {

	if ( doing_action( 'affwp_user_login' ) || doing_action( 'affwp_affiliate_register' ) ) {
		$return = false;
	}

	return $return;

}
add_filter( 'ws_plugin__optimizemember_login_redirect', 'affwp_optimize_member_prevent_affiliate_redirect', 10, 2 );

/**
 *  Fixes affiliate redirects when "Allow WishList Member To Handle Login Redirect"
 *  and "Allow WishList Member To Handle Logout Redirect" are enabled in WishList Member
 *
 *  @since 1.7.13
 *  @return boolean
 */
function affwp_wishlist_member_redirects( $return ) {

    $user    = wp_get_current_user();
    $user_id = $user->ID;

    if ( affwp_is_affiliate( $user_id ) ) {
        $return = true;
    }

    return $return;

}
add_filter( 'wishlistmember_login_redirect_override', 'affwp_wishlist_member_redirects' );
add_filter( 'wishlistmember_logout_redirect_override', 'affwp_wishlist_member_redirects' );

/**
 * Disables the mandrill_nl2br filter while sending AffiliateWP emails
 *
 * @since 1.7.17
 * @return void
 */
function affwp_disable_mandrill_nl2br() {
	add_filter( 'mandrill_nl2br', '__return_false' );
}
add_action( 'affwp_email_send_before', 'affwp_disable_mandrill_nl2br');

/**
 * Remove sptRemoveVariationsFromLoop() from pre_get_posts when query var is present.
 *
 * See https://github.com/AffiliateWP/AffiliateWP/issues/1586
 *
 * @since 1.9
 * @return void
 */
function affwp_simple_page_test_compat() {

	if( ! defined( 'SPT_PLUGIN_DIR' ) ) {
		return;
	}

	$tracking = affiliate_wp()->tracking;

	if( empty( $tracking ) ) {
		return;
	}

	if( $tracking->was_referred() ) {

		remove_action( 'pre_get_posts', 'sptRemoveVariationsFromLoop', 10 );

	}

}
add_action( 'pre_get_posts', 'affwp_simple_page_test_compat', -9999 );

/**
 * Removes content filtering originating from Encyclopedia Pro in the affiliate area Creatives tab.
 *
 * @since 2.0.2
 *
 * @param int|false $affiliate_id ID for the current affiliate.
 * @param string    $active_tab   Slug for the currently-active tab.
 */
function affwp_encyclopedia_pro_creatives_affiliate_area_compat( $affiliate_id, $active_tab = '' ) {
	if ( 'creatives' === $active_tab ) {
		add_filter( 'encyclopedia_link_terms_in_post', '__return_false' );
	}
}
add_action( 'affwp_affiliate_dashboard_top', 'affwp_encyclopedia_pro_creatives_affiliate_area_compat', 10, 2 );

/**
 * Removes the RCP Prevent Account Sharing check when logging in or registering thhrough Affiliate Area
 *
 * @since 2.1
 *
 */
function affwp_remove_rcp_can_be_logged_in_check() {
	remove_action( 'init', 'rcp_can_user_be_logged_in', 10 );
}
add_action( 'affwp_pre_process_login_form', 'affwp_remove_rcp_can_be_logged_in_check' );
add_action( 'affwp_pre_process_register_form', 'affwp_remove_rcp_can_be_logged_in_check' );