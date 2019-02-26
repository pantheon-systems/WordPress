<?php
/**
 * Affiiates Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/screen-options.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/class-list-table.php';

function affwp_affiliates_admin() {

	$action = null;

	if ( isset( $_GET['action2'] ) && '-1' !== $_GET['action2'] ) {
		$action = $_GET['action2'];
	} elseif ( isset( $_GET['action'] ) && '-1' !== $_GET['action'] ) {
		$action = $_GET['action'];
	}

	$affiliate_id = isset( $_REQUEST['affiliate_id'] ) ? absint( $_REQUEST['affiliate_id'] ) : 0;
	$affiliate    = affwp_get_affiliate( $affiliate_id );

	if ( 'view_affiliate' === $action && $affiliate ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/view.php';

	} elseif ( 'add_affiliate' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/new.php';

	} elseif ( 'edit_affiliate' === $action && $affiliate  ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/edit.php';

	} elseif ( 'review_affiliate' === $action && $affiliate  ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/review.php';

	} elseif ( 'delete' === $action && $affiliate  ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/delete.php';

	} else {

		$affiliates_table = new AffWP_Affiliates_Table();
		$affiliates_table->prepare_items();
?>
		<div class="wrap">
			<h1>
				<?php _e( 'Affiliates', 'affiliate-wp' ); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'add_affiliate' ) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'affiliate-wp' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-reports', 'tab' => 'affiliates' ) ) ); ?>" class="page-title-action"><?php _ex( 'Reports', 'affiliates', 'affiliate-wp' ); ?></a>
			</h1>
			<?php

			/**
			 * Fires at the top of the admin affiliates page.
			 *
			 * Use this hook to add content to this section of AffiliateWP.
			 */
			do_action( 'affwp_affiliates_page_top' );

			?>
			<form id="affwp-affiliates-filter" method="get" action="<?php echo esc_url( affwp_admin_url() ); ?>">
				<?php $affiliates_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-affiliates' ); ?>

				<input type="hidden" name="page" value="affiliate-wp-affiliates" />

				<?php $affiliates_table->views() ?>
				<?php $affiliates_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin affiliates page.
			 *
			 * Use this hook to add content to this section of AffiliateWP.
			 */
			do_action( 'affwp_affiliates_page_bottom' );
			?>
		</div>
<?php

	}

}
