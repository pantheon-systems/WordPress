<?php

class Affiliate_WP_Admin_Menu {


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
	}

	public function register_menus() {
		add_menu_page( __( 'Affiliates', 'affiliate-wp' ), __( 'Affiliates', 'affiliate-wp' ), 'view_affiliate_reports', 'affiliate-wp', 'affwp_affiliates_dashboard' );

		$overview   = add_submenu_page( 'affiliate-wp', __( 'Overview', 'affiliate-wp' ),    __( 'Overview', 'affiliate-wp' ),              'view_affiliate_reports',   'affiliate-wp',            'affwp_affiliates_dashboard' );
		$affiliates = add_submenu_page( 'affiliate-wp', __( 'Affiliates', 'affiliate-wp' ),  __( 'Affiliates', 'affiliate-wp' ),            'manage_affiliates',        'affiliate-wp-affiliates', 'affwp_affiliates_admin'     );
		$referrals  = add_submenu_page( 'affiliate-wp', __( 'Referrals', 'affiliate-wp' ),   __( 'Referrals', 'affiliate-wp' ),             'manage_referrals',         'affiliate-wp-referrals',  'affwp_referrals_admin'      );
		$payouts    = add_submenu_page( 'affiliate-wp', __( 'Payouts', 'affiliate-wp' ),     __( 'Payouts', 'affiliate-wp' ),               'manage_payouts',           'affiliate-wp-payouts',    'affwp_payouts_admin'        );
		$visits     = add_submenu_page( 'affiliate-wp', __( 'Visits', 'affiliate-wp' ),      __( 'Visits', 'affiliate-wp' ),                'manage_visits',            'affiliate-wp-visits',     'affwp_visits_admin'         );
		$creatives  = add_submenu_page( 'affiliate-wp', __( 'Creatives', 'affiliate-wp' ),   __( 'Creatives', 'affiliate-wp' ),             'manage_creatives',         'affiliate-wp-creatives',  'affwp_creatives_admin'      );
		$reports    = add_submenu_page( 'affiliate-wp', __( 'Reports', 'affiliate-wp' ),     __( 'Reports', 'affiliate-wp' ),               'view_affiliate_reports',   'affiliate-wp-reports',    'affwp_reports_admin'        );
		$tools      = add_submenu_page( 'affiliate-wp', __( 'Tools', 'affiliate-wp' ),       __( 'Tools', 'affiliate-wp' ),                 'manage_affiliate_options', 'affiliate-wp-tools',      'affwp_tools_admin'          );
		$settings   = add_submenu_page( 'affiliate-wp', __( 'Settings', 'affiliate-wp' ),    __( 'Settings', 'affiliate-wp' ),              'manage_affiliate_options', 'affiliate-wp-settings',   'affwp_settings_admin'       );
		$migration  = add_submenu_page( null, __( 'AffiliateWP Migration', 'affiliate-wp' ), __( 'AffiliateWP Migration', 'affiliate-wp' ), 'manage_affiliate_options', 'affiliate-wp-migrate',    'affwp_migrate_admin'        );
		$add_ons    = add_submenu_page( 'affiliate-wp', __( 'Add-ons', 'affiliate-wp' ),     __( 'Add-ons', 'affiliate-wp' ),               'manage_affiliate_options', 'affiliate-wp-add-ons',    'affwp_add_ons_admin'        );

		add_action( 'load-' . $affiliates, 'affwp_affiliates_screen_options' );
		add_action( 'load-' . $referrals, 'affwp_referrals_screen_options' );
		add_action( 'load-' . $payouts, 'affwp_payouts_screen_options' );
		add_action( 'load-' . $visits, 'affwp_visits_screen_options' );
		add_action( 'load-' . $creatives, 'affwp_creatives_screen_options' );

	}

}

$affiliatewp_menu = new Affiliate_WP_Admin_Menu;
