<?php
/**
 * Contextual Help
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Referrals
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
//print_r( get_current_screen() );
/**
 * Payments contextual help.
 *
 * @access      private
 * @since       1.4
 * @return      void
 */
function affwp_referrals_contextual_help() {

	$screen = get_current_screen();

	if ( $screen->id != 'affiliates_page_affiliate-wp-referrals' )
		return;

	$sidebar_text = '<p><strong>' . __( 'For more information:', 'affiliate-wp' ) . '</strong></p>';
	$sidebar_text .= '<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the AffiliateWP website.', 'affiliate-wp' ), esc_url( 'https://affiliatewp.com/documentation/' ) ) . '</p>';
	$sidebar_text .= '<p>' . sprintf( __( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>.', 'affiliate-wp' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP/issues' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP' )  ) . '</p>';

	$screen->set_help_sidebar( $sidebar_text );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-referrals-overview',
		'title'	    => __( 'Overview', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( "This screen provides access to your site&#8217;s referral history.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "<strong>Reference</strong>: this refers to the order number (or similar) that created this referral.", 'affiliate-wp' ) . '</p>' .
			'<p>' . __( "Referral statuses:", 'affiliate-wp' ) . '</p>' .
			'<ul>' .
				'<li>' . __( '<strong>Paid</strong> - this is a referral that has been paid to the affiliate', 'affiliate-wp' ) . '</li>' .
				'<li>' . __( '<strong>Unpaid</strong> - this is a referral that has been accepted but not yet paid to the affiliate', 'affiliate-wp' ) . '</li>' .
				'<li>' . __( '<strong>Pending</strong> - this is a referral that is waiting to be accepted', 'affiliate-wp' ) . '</li>' .
				'<li>' . __( '<strong>Rejected</strong> - this is a referral that has been rejected and will not be paid to the affiliate', 'affiliate-wp' ) . '</li>' .
			'</ul>'
	) );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-referrals-search',
		'title'	    => __( 'Searching Referrals', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( 'Referrals can be searched in several different ways:', 'affiliate-wp' ) . '</p>' .
			'<ul>
				<li>' . __( 'You can enter the referral&#8217;s ID number', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the referral reference prefixed by &#8220;ref:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the referral context prefixed by &#8220;context:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the affiliate&#8217;s ID number prefixed by &#8220;affiliate:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a URL campaign prefixed by &#8220;campaign:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter a word or phrase in the referral&#8217;s description prefixed by &#8220;desc:&#8221;', 'affiliate-wp' ) . '</li>
			</ul>'
	) );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-referrals-export',
		'title'	    => __( 'Exporting Referrals', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( 'Referrals can be exported directly to a CSV file in order to make it easier for your own accounting needs and for you to payout your affiliates&#8217;s earnings.', 'affiliate-wp' ) . '</p>' .
			'<p>' . __( 'The CSV file generated is structured properly for PayPal&#8217;s Mass Payment system so you can easily payout all of your affiliates at once via PayPal.', 'affiliate-wp' ) . '</p>'
	) );

	/**
	 * Fires in the contextual help area of the referral admin screen.
	 *
	 * @param string $screen The current screen.
	 */
	do_action( 'affwp_referrals_contextual_help', $screen );
}
add_action( 'load-affiliates_page_affiliate-wp-referrals', 'affwp_referrals_contextual_help' );
