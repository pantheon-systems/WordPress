<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Direct_Link_Tracking_Shortcodes {

	public function __construct() {

		// [affiliate_direct_links] shortcode
		add_shortcode( 'affiliate_direct_links', array( $this, 'shortcode' ) );

	}

	/**
	 * [affiliate_direct_links] shortcode
	 *
	 * @since 1.1
	 */
	public function shortcode( $atts, $content = null ) {

		if ( ! ( affwp_is_affiliate() && affwp_is_active_affiliate() ) ) {
			return;
		}

		ob_start();

		affiliate_wp()->templates->get_template_part( 'dashboard-tab', 'direct-links' );

		$content = ob_get_clean();

		return do_shortcode( $content );
	}

}
new AffiliateWP_Direct_Link_Tracking_Shortcodes;
