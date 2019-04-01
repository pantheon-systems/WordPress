<?php
/**
 * Admin Plugins
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Plugins
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Plugins row action links
 *
 * @author Tunbosun Ayinla
 * @since 1.0
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function affwp_plugin_action_links( $links, $file ) {
	$affwp_links = affwp_admin_link( 'settings', __( 'General Settings', 'affiliate-wp' ) );
	if ( $file == 'affiliate-wp/affiliate-wp.php' ) {
		array_unshift( $links, $affwp_links );
	}

	return $links;
}
add_filter( 'plugin_action_links', 'affwp_plugin_action_links', 10, 2 );


/**
 * Plugin row meta links
 *
 * @author Tunbosun Ayinla
 * @since 1.0
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function affwp_plugin_row_meta( $input, $file ) {

	if ( $file != 'affiliate-wp/affiliate-wp.php' ) {
		return $input;
	}

	$links = array(
		'<a href="https://affiliatewp.com/changelog">' . esc_html__( 'Changelog', 'affiliate-wp' ) . '</a>'
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'affwp_plugin_row_meta', 10, 2 );