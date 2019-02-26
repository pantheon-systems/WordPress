<?php

function affiliate_wp_install() {

	// Create affiliate caps
	$roles = new Affiliate_WP_Capabilities;
	$roles->add_caps();

	$affiliate_wp_install                 = new stdClass();
	$affiliate_wp_install->affiliates     = new Affiliate_WP_DB_Affiliates;
	$affiliate_wp_install->affiliate_meta = new Affiliate_WP_Affiliate_Meta_DB;
	$affiliate_wp_install->referrals      = new Affiliate_WP_Referrals_DB;
	$affiliate_wp_install->visits         = new Affiliate_WP_Visits_DB;
	$affiliate_wp_install->campaigns      = new Affiliate_WP_Campaigns_DB;
	$affiliate_wp_install->creatives      = new Affiliate_WP_Creatives_DB;
	$affiliate_wp_install->settings       = new Affiliate_WP_Settings;
	$affiliate_wp_install->rewrites       = new Affiliate_WP_Rewrites;
	$affiliate_wp_install->REST           = new Affiliate_WP_REST;

	$affiliate_wp_install->affiliates->create_table();
	$affiliate_wp_install->affiliate_meta->create_table();
	$affiliate_wp_install->referrals->create_table();
	$affiliate_wp_install->visits->create_table();
	$affiliate_wp_install->campaigns->create_view();
	$affiliate_wp_install->creatives->create_table();
	$affiliate_wp_install->affiliates->payouts->create_table();
	$affiliate_wp_install->REST->consumers->create_table();

	if ( ! get_option( 'affwp_is_installed' ) ) {
		$affiliate_area = wp_insert_post(
			array(
				'post_title'     => __( 'Affiliate Area', 'affiliate-wp' ),
				'post_content'   => '[affiliate_area]',
				'post_status'    => 'publish',
				'post_author'    => get_current_user_id(),
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		// Update settings.
		$affiliate_wp_install->settings->set( array(
			'affiliates_page'              => $affiliate_area,
			'required_registration_fields' => array(
				'your_name'   => __( 'Your Name', 'affiliate-wp' ),
				'website_url' => __( 'Website URL', 'affiliate-wp' )
			)
		), $save = true );

	}

	// 3 equals unchecked
	update_option( 'affwp_js_works', 3 );
	update_option( 'affwp_is_installed', '1' );
	update_option( 'affwp_version', AFFILIATEWP_VERSION );

	// Clear rewrite rules
	$affiliate_wp_install->rewrites->flush_rewrites();

	$completed_upgrades = array(
		'upgrade_v20_recount_unpaid_earnings'
	);

	// Set past upgrade routines complete for all sites.
	if ( is_multisite() ) {

		if ( true === version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ) {

			$sites = wp_list_pluck( 'blog_id', wp_get_sites() );

		} else {

			$sites = get_sites( array( 'fields' => 'ids' ) );

		}

		foreach ( $sites as $site_id ) {
			switch_to_blog( $site_id );

			update_option( 'affwp_completed_upgrades', $completed_upgrades );

			restore_current_blog();
		}

	} else {

		update_option( 'affwp_completed_upgrades', $completed_upgrades );

	}

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect
	set_transient( '_affwp_activation_redirect', true, MINUTE_IN_SECONDS / 2 );

}
register_activation_hook( AFFILIATEWP_PLUGIN_FILE, 'affiliate_wp_install' );

function affiliate_wp_check_if_installed() {

	// this is mainly for network activated installs
	if( ! get_option( 'affwp_is_installed' ) ) {
		affiliate_wp_install();
	}
}
add_action( 'admin_init', 'affiliate_wp_check_if_installed' );
