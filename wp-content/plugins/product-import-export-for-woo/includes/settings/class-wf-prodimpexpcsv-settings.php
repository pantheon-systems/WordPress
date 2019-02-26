<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdImpExpCsv_Settings {

	/**
	 * Product Exporter Tool
	 */
	public static function save_settings( ) {
		global $wpdb;

		//update_option( 'woocommerce_'.WF_PROD_IMP_EXP_ID.'_settings', $settings );
		
		//echo ':'.$ftp_server.':'.$ftp_user.':'.$ftp_password.':'.$use_ftps; die();
		//wp_redirect( admin_url( '/admin.php?page='.WF_WOOCOMMERCE_CSV_IM_EX.'&tab=settings' ) );
		//exit;
	}
}
