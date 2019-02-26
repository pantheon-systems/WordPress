<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function affiliatewp_dlt_install() {
	affiliatewp_direct_link_tracking()->direct_links = new Affiliate_WP_Direct_Links_DB;
	affiliatewp_direct_link_tracking()->direct_links->create_table();
}
register_activation_hook( __FILE__, 'affiliatewp_dlt_install' );
