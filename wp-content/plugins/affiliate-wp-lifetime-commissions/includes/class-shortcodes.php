<?php

class Affiliate_WP_Lifetime_Commissions_Shortcodes {

	public function __construct() {

		// Force front-end scripts.
		add_filter( 'affwp_force_frontend_scripts', array( $this, 'force_frontend_scripts' ) );

		// [affiliate_lifetime_customers]
		add_shortcode( 'affiliate_lifetime_customers', array( $this, 'affiliate_lifetime_customers' ) );
	}

	/**
	 * Force the frontend scripts to load on pages with the shortcodes.
	 *
	 * @since  1.3
	 */
	public function force_frontend_scripts( $ret ) {
		global $post;

		if ( has_shortcode( $post->post_content, 'affiliate_lifetime_customers' ) ) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Show the lifetime customers for the logged in affiliate.
	 *
	 * [affiliate_lifetime_customers]
	 *
	 * @since  1.3
	 */
	public function affiliate_lifetime_customers( $atts, $content = null ) {

		if ( ! ( affwp_is_affiliate() && affwp_is_active_affiliate() ) ) {
			return;
		}

		if ( ! ( affiliate_wp_lifetime_commissions()->can_access_lifetime_customers() || affiliate_wp_lifetime_commissions()->global_lifetime_customers_access() ) ) {
			return;
		}

		ob_start();

		affiliate_wp()->templates->get_template_part( 'dashboard-tab', 'lifetime-customers' );

		$content = ob_get_clean();

		return do_shortcode( $content );
	}
}
new Affiliate_WP_Lifetime_Commissions_Shortcodes;
