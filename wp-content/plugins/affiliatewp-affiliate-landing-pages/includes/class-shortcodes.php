<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Affiliate_Landing_Pages_Shortcodes {

	public function __construct() {

		// [affiliate_landing_pages] shortcode.
		add_shortcode( 'affiliate_landing_pages', array( $this, 'shortcode' ) );

	}

	/**
	 * [affiliate_landing_pages] shortcode
	 *
	 * @since 1.0
	 */
	public function shortcode( $atts, $content = null ) {

		if ( ! ( affwp_is_affiliate() && affwp_is_active_affiliate() ) ) {
			return;
		}

		ob_start();

		$alp          = affiliatewp_affiliate_landing_pages();
		$affiliate_id = affwp_get_affiliate_id();

		$alp->list_landing_pages( $affiliate_id );

		$content = ob_get_clean();

		return do_shortcode( $content );
	}

}
new AffiliateWP_Affiliate_Landing_Pages_Shortcodes;
