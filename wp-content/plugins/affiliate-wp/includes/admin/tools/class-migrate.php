<?php

class Affiliate_WP_Migrate {


	public function __construct() {

		add_action( 'affwp_migrate', array( $this, 'process_migration' ) );

	}

	public function process_migration() {

		if ( empty( $_REQUEST['type'] ) ) {
			return false;
		}

		$step  = isset( $_REQUEST['step'] )  ? absint( $_REQUEST['step'] )              : 1;
		$type  = isset( $_REQUEST['type'] )  ? sanitize_text_field( $_REQUEST['type'] ) : false;
		$part  = isset( $_REQUEST['part'] )  ? sanitize_text_field( $_REQUEST['part'] ) : false;
		$roles = isset( $_REQUEST['roles'] ) ? array_map( 'sanitize_key', explode( ',', $_REQUEST['roles'] ) ) : array();

		if ( ! $type ) {

			wp_safe_redirect( admin_url() );

			exit;

		}

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-base.php';

		switch ( $type ) {

			case 'affiliates-pro' :

				require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-affiliates-pro.php';

				$migrate = new Affiliate_WP_Migrate_Affiliates_Pro;

				$migrate->process( $step, $part );

				break;

			case 'wp-affiliate' :

				require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-wp-affiliate.php';

				$migrate = new Affiliate_WP_Migrate_WP_Affiliate;

				$migrate->process( $step, $part );

				break;

			case 'users' :

				require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate-users.php';

				$migrate = new Affiliate_WP_Migrate_Users;

				$migrate->roles = $roles;

				$migrate->process( $step, $part );

				break;

		}

	}

}

new Affiliate_WP_Migrate;
