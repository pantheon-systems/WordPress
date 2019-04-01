<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register batch process
 *
 * @since 1.3
 */
function affwp_lc_register_batch_process() {

	if ( true === version_compare( AFFILIATEWP_VERSION, '2.0', '>=' ) ) {

		affiliate_wp()->utils->batch->register_process( 'migrate-lc-meta', array(
			'class' => 'AffWP\Utils\Batch_Process\Migrate_Lifetime_Commissions_Meta',
			'file'  => AFFWP_LC_PLUGIN_DIR . 'includes/class-batch-migrate-meta.php'
		) );

	}
}
add_action( 'admin_init', 'affwp_lc_register_batch_process' );

/**
 * Displays upgrade notices.
 *
 * @since 1.3
 */
function affwp_lc_upgrade_notice() {

	if ( true === version_compare( AFFILIATEWP_VERSION, '2.0', '>=' ) && false === get_option( 'affwp_lc_migrate_meta' ) ) :

		// Enqueue admin JS for the batch processor.
		affwp_enqueue_admin_js();
		?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e( 'Your database needs to be upgraded following the latest AffiliateWP - Lifetime Commissions update. Depending on the size of your database, this upgrade could take some time.', 'affiliate-wp-lifetime-commissions' ); ?></p>
			<form method="post" class="affwp-batch-form" data-batch_id="migrate-lc-meta"
			      data-nonce="<?php echo esc_attr( wp_create_nonce( 'migrate-lc-meta_step_nonce' ) ); ?>">
				<p>
					<?php submit_button( __( 'Upgrade Database', 'affiliate-wp-lifetime-commissions' ), 'secondary', 'v13-migrate-affiliates-lc-meta', false ); ?>
				</p>
			</form>
		</div>
	<?php endif;
}
add_action( 'admin_notices', 'affwp_lc_upgrade_notice' );