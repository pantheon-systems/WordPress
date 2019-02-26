<?php

class AffiliateWP_Affiliate_Dashboard_Sharing_Admin {

	public function __construct() {
		// add settings
		add_filter( 'affwp_settings_integrations', array( $this, 'settings_integrations' ) );
	}

	/**
	 * Integration settings
	 * 
	 * @since 1.1
	*/
	public function settings_integrations( $settings = array() ) {

		$settings[ 'dashboard_sharing_header' ] = array(
			'name' => __( 'Affiliate Dashboard Sharing', 'affwp-affiliate-dashboard-sharing' ),
			'type' => 'header'
		);

		$settings[ 'ads_social_networks' ] = array(
			'name' => __( 'Social Networks', 'affwp-affiliate-dashboard-sharing' ),
			'type' => 'multicheck',
			'options' => affwp_affiliate_dashboard_sharing()->social_networks()
		);

		$settings[ 'ads_facebook_share_button' ] = array(
			'name' => __( 'Facebook Share Button', 'affwp-affiliate-dashboard-sharing' ),
			'desc' => __( 'Enables the Facebook share button. Facebook must be enabled above.', 'affwp-affiliate-dashboard-sharing' ),
			'type' => 'checkbox',
		);

		$settings[ 'ads_campaign_tracking' ] = array(
			'name' => __( 'Campaign Tracking', 'affwp-affiliate-dashboard-sharing' ),
			'desc' => __( 'Automatically append the social network as the utm_source. Eg: ?utm_source=twitter', 'affwp-affiliate-dashboard-sharing' ),
			'type' => 'checkbox',
		);

		$settings[ 'ads_twitter_sharing_text' ] = array(
			'name' => __( 'Twitter Text', 'affwp-affiliate-dashboard-sharing' ),
			'desc' => '<p class="description">' . __( 'The default text that will show when an affiliate shares to Twitter. Leave blank to use Site Title.', 'affwp-affiliate-dashboard-sharing' ) . '</p>',
			'type' => 'text',
		);

		$settings[ 'ads_email_subject' ] = array(
			'name' => __( 'Email Sharing Subject', 'affwp-affiliate-dashboard-sharing' ),
			'desc' => '<p class="description">' . __( 'The default text that will show in the email subject line. Leave blank to use Site Title.', 'affwp-affiliate-dashboard-sharing' ) . '</p>',
			'type' => 'text',
		);

		$settings[ 'ads_email_body' ] = array(
			'name' => __( 'Email Sharing Message', 'affwp-affiliate-dashboard-sharing' ),
			'desc' => '<p class="description">' . __( 'The default text that will show in the email message. The affiliate\'s referral URL will be automatically appended to the email.', 'affwp-affiliate-dashboard-sharing' ) . '</p>',
			'type' => 'text',
			'std'  => __( 'I thought you might be interested in this:', 'affwp-affiliate-dashboard-sharing' ),
		);

		return $settings;

	}

}
new AffiliateWP_Affiliate_Dashboard_Sharing_Admin;