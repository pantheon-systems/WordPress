<?php
/**
 * Contextual Help
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Visits
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Payments contextual help.
 *
 * @access      private
 * @since       1.2
 * @return      void
 */
function affwp_visits_contextual_help() {

	$screen = get_current_screen();

	if ( $screen->id != 'affiliates_page_affiliate-wp-visits' )
		return;

	$sidebar_text = '<p><strong>' . __( 'For more information:', 'affiliate-wp' ) . '</strong></p>';
	$sidebar_text .= '<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the AffiliateWP website.', 'affiliate-wp' ), esc_url( 'https://affiliatewp.com/documentation/' ) ) . '</p>';
	$sidebar_text .= '<p>' . sprintf( __( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>.', 'affiliate-wp' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP/issues' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP' )  ) . '</p>';

	$screen->set_help_sidebar( $sidebar_text );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-visits-overview',
		'title'	    => __( 'Overview', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( "This screen provides access to your site&#8217;s visit history.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Landing Page</strong>: this is the page on your site that the visitor first landed on. It is the URL that the affiliate link pointed to.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Referring URL</strong>: this is the source URL of the affiliate link. It is the web page that the visitor found a link to your site on.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Affiliate</strong>: this the affiliate that the visit was attributed to.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Referral ID</strong>: this is the ID of the referral that was created from this visit, if any.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Context</strong>: this is the context of how the visit was generated, if set.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>IP</strong>: this is the IP address of the visitor.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Converted</strong>: this is a Yes / No status for whether the visit turned into a successful converstion.", 'affiliate-wp' ) . '</p>'
	) );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-visits-search',
		'title'	    => __( 'Searching Visits', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( 'Visit records can be searched in several different ways:', 'affiliate-wp' ) . '</p>' .
			'<ul>
				<li>' . __( 'You can enter an IP address to find all visits from a specific IP', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a complete URL to find all visits that landed on or came from a specific URL', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a partial URL to find all visits that landed on or came from a specific site', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the referral&#8217;s ID number prefixed by &#8220;referral:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a visit context prefixed by &#8220;context:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the affiliate&#8217;s ID number prefixed by &#8220;affiliate:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a URL campaign prefixed by &#8220;campaign:&#8221;', 'affiliate-wp' ) . '</li>
			</ul>'
	) );

	/**
	 * Fires in the contextual-help area of the Visits admin screen.
	 *
	 * @param string $screen The current screen.
	 */
	do_action( 'affwp_visits_contextual_help', $screen );
}
add_action( 'load-affiliates_page_affiliate-wp-visits', 'affwp_visits_contextual_help' );
