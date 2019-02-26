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

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/visits/screen-options.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/visits/contextual-help.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/visits/class-list-table.php';

function affwp_visits_admin() {


	$visits_table = new AffWP_Visits_Table();
	$visits_table->prepare_items();
	$affiliate_name = ! empty( $_REQUEST['user_name'] ) ? $_REQUEST['user_name'] : '';
	$from   = ! empty( $_REQUEST['filter_from'] )   ? $_REQUEST['filter_from']   : '';
	$to     = ! empty( $_REQUEST['filter_to'] )     ? $_REQUEST['filter_to']     : '';
	$status = ! empty( $_REQUEST['filter_status'] ) ? $_REQUEST['filter_status'] : '';

	if ( ! empty( $_REQUEST['affiliate'] ) ) {
		$affiliate_id = absint( $_REQUEST['affiliate'] );

		if ( affwp_get_affiliate( $affiliate_id ) ) {
			$affiliate_name = affwp_get_affiliate_username( $affiliate_id );
		}
	}
	?>
	<div class="wrap">

		<h1>
			<?php _e( 'Visits', 'affiliate-wp' ); ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-reports', 'tab' => 'visits' ) ) ); ?>" class="page-title-action"><?php _ex( 'Reports', 'visits', 'affiliate-wp' ); ?></a>
		</h1>
		<?php
		/**
		 * Fires at the top of the Visits admin screen (outside the form element).
		 */
		do_action( 'affwp_visits_page_top' );

		?>

		<form id="affwp-visits-filter" method="get" action="<?php echo esc_url( affwp_admin_url() ); ?>">
			<?php $visits_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-affiliates' ); ?>
			<span class="affwp-ajax-search-wrap">
				<input type="text" name="user_name" id="user_name" class="affwp-user-search" value="<?php echo esc_attr( $affiliate_name ); ?>" data-affwp-status="any" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
			</span>
			<input type="hidden" name="page" value="affiliate-wp-visits" />
			<input type="text" class="affwp-datepicker" autocomplete="off" name="filter_from" placeholder="<?php esc_attr_e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>" value="<?php echo esc_attr( $from ); ?>"/>
			<input type="text" class="affwp-datepicker" autocomplete="off" name="filter_to" placeholder="<?php esc_attr_e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>" value="<?php echo esc_attr( $to ); ?>"/>

			<label class="screen-reader-text" for="filter_status"><?php _e( 'Filter by status', 'affiliate-wp' ); ?></label>
			<select id="filter_status" name="filter_status" class="postform" style="margin-top:-1px;">
				<option value=""<?php selected( '', $status ) ?>><?php _e( 'All', 'affiliate-wp' ); ?></option>
				<option value="converted"<?php selected( 'converted', $status ) ?>><?php _e( 'Converted', 'affiliate-wp' ); ?></option>
				<option value="unconverted"<?php selected( 'unconverted', $status ) ?>><?php _e( 'Unconverted', 'affiliate-wp' ); ?></option>
			</select>
			<input type="submit" class="button" value="<?php _e( 'Filter', 'affiliate-wp' ); ?>"/>
			<?php $visits_table->views() ?>
			<?php $visits_table->display() ?>
		</form>
		<?php

		/**
		 * Fires at the bottom of the Visits admin screen.
		 */
		do_action( 'affwp_visits_page_bottom' );

		?>
	</div>
<?php

}
