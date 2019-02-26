<?php
/**
 * Referrals Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Referrals
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/screen-options.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/contextual-help.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/class-list-table.php';

function affwp_referrals_admin() {

	if( isset( $_GET['action'] ) && 'add_referral' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/new.php';

	} else if( isset( $_GET['action'] ) && 'edit_referral' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/edit.php';

	} else {

		$referrals_table = new AffWP_Referrals_Table();
		$referrals_table->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Referrals', 'affiliate-wp' ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'action', 'add_referral' ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'affiliate-wp' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-reports', 'tab' => 'referrals' ) ) ); ?>" class="page-title-action"><?php _ex( 'Reports', 'referrals', 'affiliate-wp' ); ?></a>
				<button class="page-title-action affwp-referrals-export-toggle"><?php _e( 'Generate Payout File', 'affiliate-wp' ); ?></button>
				<button class="page-title-action affwp-referrals-export-toggle" style="display:none"><?php _e( 'Close', 'affiliate-wp' ); ?></button>
			</h1>

			<?php
			/**
			 * Fires at the top of the referrals list-table admin screen.
			 */
			do_action( 'affwp_referrals_page_top' );
			?>

			<div id="affwp-referrals-export-wrap">

				<?php
				/**
				 * Fires in the action buttons area of the referrals list-table admin screen.
				 */
				do_action( 'affwp_referrals_page_buttons' );
				?>

				<form id="affwp-referrals-export-form" style="display:none;" class="affwp-batch-form" data-batch_id="generate-payouts" data-nonce="<?php echo esc_attr( wp_create_nonce( 'generate-payouts_step_nonce' ) ); ?>" data-ays="<?php esc_attr_e( 'Are you sure you want to generate the payout file? All included referrals will be marked as Paid.', 'affiliate-wp' ); ?>">
					<h2><?php _e( 'Generate Payout File', 'affiliate-wp' ); ?></h2>
					<p>
						<span class="affwp-ajax-search-wrap">
							<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
						</span>

						<input type="text" class="affwp-datepicker" autocomplete="off" name="from" placeholder="<?php _e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
						<input type="text" class="affwp-datepicker" autocomplete="off" name="to" placeholder="<?php _e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
						<input type="text" class="affwp-text" name="minimum" placeholder="<?php esc_attr_e( 'Minimum amount', 'affiliate-wp' ); ?>"/>
					</p>
					<p><?php printf( __( 'This will mark all unpaid referrals in this timeframe as paid. To export referrals with a status other than <em>unpaid</em>, go to the <a href="%s">Tools &rarr; Export</a> page.', 'affiliate-wp' ), esc_url( affwp_admin_url( 'tools', array( 'tab' => 'export_import' ) ) ) ); ?></p>
					<p><?php _e( 'To generate a payout for a specific affiliate, enter the affiliate&#8217;s login name, first name, or last name. Leave blank to generate a payout for any affiliates.', 'affiliate-wp' ); ?></p>
					<p>
						<?php
						/**
						 * Fires just prior to the Generate CSV File button in the referrals list table
						 * admin screen.
						 */
						do_action( 'affwp_referrals_page_csv_export_form' );

						submit_button( __( 'Generate CSV File', 'affiliate-wp' ), 'secondary', 'generate-payouts-submit', false );
						?>
					</p>
				</form>

			</div>
			<form id="affwp-referrals-filter-form" method="get" action="<?php echo esc_url( affwp_admin_url( 'referrals' ) ); ?>">

				<?php $referrals_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-referrals' ); ?>

				<input type="hidden" name="page" value="affiliate-wp-referrals" />

				<?php $referrals_table->views() ?>
				<?php $referrals_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the referrals list table admin screen.
			 */
			do_action( 'affwp_referrals_page_bottom' );
			?>
		</div>
	<?php
	}

}
