<?php
/**
 * Admin Tools Page
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Tools
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/migration.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-recount.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-rest-consumers-table.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/system-info.php';

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-base-exporter.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-csv-exporter.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-base-importer.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-csv-importer.php';

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/import.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/export.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-import.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-affiliates.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-referrals.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-referrals-payout.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-settings.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-import-settings.php';

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function affwp_tools_admin() {

	$active_tab = affwp_get_current_tools_tab();

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php
			affwp_navigation_tabs( affwp_get_tools_tabs(), $active_tab, array(
				'settings-updated' => false,
				'affwp_notice'     => false
			) );
			?>
		</h2>
		<div id="tab_container">
			<?php
			/**
			 * Fires in the Tools screen tab.
			 *
			 * The dynamic portion of the hook name, `$active_tab`, refers to the slug of
			 * the currently active tools tab.
			 */
			do_action( 'affwp_tools_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieve tools tabs
 *
 * @since 1.0
 * @return array $tabs
 */
function affwp_get_tools_tabs() {

	$tabs                  = array();
	$tabs['export_import'] = __( 'Export / Import', 'affiliate-wp' );

	if ( current_user_can( 'manage_consumers' ) ) {
		$tabs['api_keys'] = __( 'API Keys', 'affiliate-wp' );
	}

	$tabs['recount']       = __( 'Recount Stats', 'affiliate-wp' );
	$tabs['migration']     = __( 'Migration Assistant', 'affiliate-wp' );

	if ( current_user_can( 'manage_affiliate_options' ) ) {
		$tabs['system_info'] = __( 'System Info', 'affiliate-wp' );
	}

	if( affiliate_wp()->settings->get( 'debug_mode', false ) ) {
		$tabs['debug'] = __( 'Debug Assistant', 'affiliate-wp' );
	}

	/**
	 * Filters AffiliateWP tools tabs.
	 *
	 * @since 1.0
	 *
	 * @param array $tabs Array of tools tabs.
	 */
	return apply_filters( 'affwp_tools_tabs', $tabs );
}

/**
 * Retrieves the current Tools tab.
 *
 * @since 1.8
 *
 * @return string Current Tools tab if present in the URL, 'export_import' otherwise.
 */
function affwp_get_current_tools_tab() {
	if ( isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], affwp_get_tools_tabs() ) ) {
		$active_tab = sanitize_text_field( $_GET['tab'] );
	} else {
		$active_tab = 'export_import';
	}

	/**
	 * Filter the current Tools tab.
	 *
	 * @since 1.8
	 *
	 * @param string $active_tab Current Tools tab ID.
	 */
	return apply_filters( 'affwp_current_tools_tab', $active_tab );
}

/**
 * Recount Tab
 *
 * @since       1.0
 * @return      void
 */
function affwp_recount_tab() {
?>
	<div id="affwp-dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Recount Affiliate Stats', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Use this tool to recount affiliate statistics.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-form" data-batch_id="recount-affiliate-stats" data-nonce="<?php echo esc_attr( wp_create_nonce( 'recount-affiliate-stats_step_nonce' ) ); ?>">
						<p>
							<span class="affwp-ajax-search-wrap">
								<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php esc_html_e( 'Affiliate name', 'affiliate-wp' ); ?>"/>
							</span>
							<select name="recount_type">
								<option value="earnings"><?php esc_html_e( 'Paid Earnings', 'affiliate-wp' ); ?></option>
								<option value="unpaid-earnings"><?php esc_html_e( 'Unpaid Earnings', 'affiliate-wp' ); ?></option>
								<option value="referrals"><?php esc_html_e( 'Referrals', 'affiliate-wp' ); ?></option>
								<option value="visits"><?php esc_html_e( 'Visits', 'affiliate-wp' ); ?></option>
							</select>
							<div class="description"><?php esc_html_e( 'Enter the name of the affiliate or begin typing to perform a search based on the affiliate&#8217;s name.', 'affiliate-wp' ); ?></div>
						</p>
						<p>
							<input type="hidden" name="affwp_action" value="recount_stats"/>
							<?php submit_button( __( 'Recount', 'affiliate-wp' ), 'secondary', 'recount-stats-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
		</div><!-- .metabox-holder -->
	</div><!-- #affwp-dashboard-widgets-wrap -->
<?php
}
add_action( 'affwp_tools_tab_recount', 'affwp_recount_tab' );

/**
 * Migration assistant tab
 *
 * @since 1.0
 *
 * @global string $wp_version WordPress version
 *
 * @return void
 */
function affwp_migration_tab() {
	global $wp_version;
	$tool_is_compatible = version_compare( $wp_version, '4.4', '>=' );

	$user_counts = count_users();

	$_roles = new WP_Roles();
	$roles  = array();

	foreach ( $_roles->get_names() as $role => $label ) {
		$roles[ $role ]['label'] = translate_user_role( $label );
		$roles[ $role ]['count'] = isset( $user_counts['avail_roles'][ $role ] ) ? $user_counts['avail_roles'][ $role ] : 0;
	}
?>
	<div id="affwp-dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div class="postbox">
				<div class="inside">
					<p><?php esc_html_e( 'These tools assist in migrating affiliate and referral data from existing platforms.', 'affiliate-wp' ); ?></p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'User Accounts', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<?php if ( $tool_is_compatible ) : ?>
						<p><?php esc_html_e( 'Use this tool to create affiliate accounts for each of your existing WordPress user accounts that belong to the selected roles below.', 'affiliate-wp' ); ?></p>
						<p><?php esc_html_e( '<strong>NOTE:</strong> Users that already have affiliate accounts will be skipped. Duplicate accounts will not be created.', 'affiliate-wp' ); ?></p>
						<form method="post" id="affiliate-wp-migrate-user-accounts" class="affwp-batch-form" data-batch_id="migrate-users" data-nonce="<?php echo esc_attr( wp_create_nonce( 'migrate-users_step_nonce' ) ); ?>">
							<h4><span><?php esc_html_e( 'Select User Roles', 'affiliate-wp' ); ?></span></h4>
							<?php foreach ( $roles as $role => $data ) : ?>
								<?php $has_users = ! empty( $data['count'] ); ?>
								<label>
									<input type="checkbox" name="roles[]" value="<?php echo esc_attr( $role ); ?>" <?php checked( $has_users ); disabled( ! $has_users ) ?>>
									<span class="<?php echo ( ! $has_users ) ? 'muted' : ''; ?>"><?php echo esc_html( $data['label'] ); ?> (<?php echo absint( $data['count'] ); ?>)</span>
								</label>
								<br>
							<?php endforeach; ?>
							<p>
								<input type="submit" value="<?php esc_html_e( 'Create Affiliate Accounts for Users', 'affiliate-wp' ); ?>" class="button" />
							</p>
						</form>
					<?php else : ?>
						<?php if ( current_user_can( 'update_core' ) ) : ?>
							<p><?php printf( __( '<strong>NOTE:</strong> WordPress 4.5 or newer is required to use the User Accounts migration tool. <a href="%s" aria-label="Update WordPress now">Update WordPress now</a>.', 'affiliate-wp' ), network_admin_url( 'update-core' ) ); ?></p>
						<?php else : ?>
							<p><?php _e( '<strong>NOTE:</strong> WordPress 4.5 or newer is required to use the User Accounts migration tool.', 'affiliate-wp' ); ?></p>
						<?php endif; // 'update_core' ?>
					<?php endif; // $tool_is_compatible ?>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span>Affiliates Pro</span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Use this tool to migrate existing affiliate / referral data from Affiliates Pro to AffiliateWP.', 'affiliate-wp' ); ?></p>
					<p><?php esc_html_e( '<strong>NOTE:</strong> This tool should only ever be used on a fresh install. If you have already collected affiliate or referral data, do not use this tool.', 'affiliate-wp' ); ?></p>
					<form method="get">
						<input type="hidden" name="type" value="affiliates-pro"/>
						<input type="hidden" name="part" value="affiliates"/>
						<input type="hidden" name="page" value="affiliate-wp-migrate"/>
						<p>
							<input type="submit" value="<?php esc_html_e( 'Migrate Data from Affiliates Pro', 'affiliate-wp' ); ?>" class="button"/>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span>WP Affiliate</span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Use this tool to migrate existing affiliate accounts from WP Affiliate to AffiliateWP.', 'affiliate-wp' ); ?></p>
					<form method="get" class="affwp-batch-form" data-batch_id="migrate-wp-affiliate" data-nonce="<?php echo esc_attr( wp_create_nonce( 'migrate-wp-affiliate_step_nonce' ) ); ?>">
						<p>
							<input type="submit" value="<?php esc_html_e( 'Migrate Data from WP Affiliate', 'affiliate-wp' ); ?>" class="button"/>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

		</div><!-- .metabox-holder -->
	</div><!-- #affwp-dashboard-widgets-wrap -->
<?php
}
add_action( 'affwp_tools_tab_migration', 'affwp_migration_tab' );

/**
 * Export / Import tab
 *
 * @since       1.0
 * @return      void
 */
function affwp_export_import_tab() {
?>
	<div id="affwp-dashboard-widgets-wrap">
		<div class="metabox-holder">

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Affiliates', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export affiliates to a CSV file.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-form" data-batch_id="export-affiliates" data-nonce="<?php echo esc_attr( wp_create_nonce( 'export-affiliates_step_nonce' ) ); ?>">
						<p>
							<select name="status" id="status">
								<option value="0"><?php esc_html_e( 'All Statuses', 'affiliate-wp' ); ?></option>
								<option value="active"><?php esc_html_e( 'Active', 'affiliate-wp' ); ?></option>
								<option value="pending"><?php esc_html_e( 'Pending', 'affiliate-wp' ); ?></option>
								<option value="rejected"><?php esc_html_e( 'Rejected', 'affiliate-wp' ); ?></option>
							</select>
						</p>
						<p>
							<?php submit_button( __( 'Export', 'affiliate-wp' ), 'secondary', 'export-affiliates-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Referrals', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export referrals to a CSV file.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-form" data-batch_id="export-referrals" data-nonce="<?php echo esc_attr( wp_create_nonce( 'export-referrals_step_nonce' ) ); ?>">
						<p>
							<span class="affwp-ajax-search-wrap">
								<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php esc_html_e( 'Affiliate name', 'affiliate-wp' ); ?>" />
							</span>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="start_date" placeholder="<?php esc_html_e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="end_date" placeholder="<?php esc_html_e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<select name="status" id="status">
								<option value="0"><?php esc_html_e( 'All Statuses', 'affiliate-wp' ); ?></option>
								<option value="paid"><?php esc_html_e( 'Paid', 'affiliate-wp' ); ?></option>
								<option value="unpaid"><?php esc_html_e( 'Unpaid', 'affiliate-wp' ); ?></option>
								<option value="pending"><?php esc_html_e( 'Pending', 'affiliate-wp' ); ?></option>
								<option value="rejected"><?php esc_html_e( 'Rejected', 'affiliate-wp' ); ?></option>
							</select>
							<div class="description"><?php esc_html_e( 'To search for an affiliate, enter the affiliate&#8217;s login name, first name, or last name. Leave blank to export referrals for all affiliates.', 'affiliate-wp' ); ?></div>
						</p>
						<p>
							<?php submit_button( __( 'Export', 'affiliate-wp' ), 'secondary', 'export-referrals-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Payouts', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export payouts to a CSV file.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-form" data-batch_id="export-payouts" data-nonce="<?php echo esc_attr( wp_create_nonce( 'export-payouts_step_nonce' ) ); ?>">
						<p>
							<span class="affwp-ajax-search-wrap">
								<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php esc_html_e( 'Affiliate name', 'affiliate-wp' ); ?>" />
							</span>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="start_date" placeholder="<?php esc_html_e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="end_date" placeholder="<?php esc_html_e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<select name="status" id="status">
								<option value="paid"><?php esc_html_e( 'Paid', 'affiliate-wp' ); ?></option>
								<option value="unpaid"><?php esc_html_e( 'Failed', 'affiliate-wp' ); ?></option>
							</select>
							<div class="description"><?php esc_html_e( 'To search for an affiliate, enter the affiliate&#8217;s login name, first name, or last name. Leave blank to export payouts for all affiliates.', 'affiliate-wp' ); ?></div>
						</p>
						<p>
							<?php submit_button( __( 'Export', 'affiliate-wp' ), 'secondary', 'export-payouts-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Visits', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export visits to a CSV file.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-form" data-batch_id="export-visits" data-nonce="<?php echo esc_attr( wp_create_nonce( 'export-visits_step_nonce' ) ); ?>">
						<p>
							<span class="affwp-ajax-search-wrap">
								<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="any" autocomplete="off" placeholder="<?php esc_html_e( 'Affiliate name', 'affiliate-wp' ); ?>" />
							</span>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="start_date" placeholder="<?php esc_html_e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<input type="text" class="affwp-datepicker" autocomplete="off" name="end_date" placeholder="<?php esc_html_e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
							<select name="referral_status" id="referral_status">
								<option value=""><?php esc_html_e( 'All', 'affiliate-wp' ); ?></option>
								<option value="converted"><?php esc_html_e( 'Converted', 'affiliate-wp' ); ?></option>
								<option value="unconverted"><?php esc_html_e( 'Unconverted', 'affiliate-wp' ); ?></option>
							</select>
							<div class="description"><?php esc_html_e( 'To search for an affiliate, enter the affiliate&#8217;s login name, first name, or last name. Leave blank to export visits for all affiliates.', 'affiliate-wp' ); ?></div>
						</p>
						<p>
							<?php submit_button( __( 'Export', 'affiliate-wp' ), 'secondary', 'export-visits-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Settings', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export the AffiliateWP settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'affiliate-wp' ); ?></p>
					<form method="post" action="<?php echo esc_url( affwp_admin_url( 'tools', array( 'tab' => 'export_import' ) ) ); ?>">
						<p><input type="hidden" name="affwp_action" value="export_settings" /></p>
						<p>
							<?php wp_nonce_field( 'affwp_export_nonce', 'affwp_export_nonce' ); ?>
							<?php submit_button( __( 'Export', 'affiliate-wp' ), 'secondary', 'export-settings-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Import Settings', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Import the AffiliateWP settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( affwp_admin_url( 'tools', array( 'tab' => 'export_import' ) ) ); ?>">
						<p>
							<input type="file" name="import_file"/>
						</p>
						<p>
							<input type="hidden" name="affwp_action" value="import_settings" />
							<?php wp_nonce_field( 'affwp_import_nonce', 'affwp_import_nonce' ); ?>
							<?php submit_button( __( 'Import', 'affiliate-wp' ), 'secondary', 'import-settings-submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Import Affiliates', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Import a CSV of affiliate records.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-import-form" data-batch_id="import-affiliates" data-required="email" data-nonce=""<?php echo esc_attr( wp_create_nonce( 'import-affiliates_step_nonce' ) ); ?>">
						<div class="affwp-import-file-wrap">
							<p>
								<input name="affwp-import-file" id="affwp-import-affiliates-file" type="file" />
							</p>
							<p>
								<?php wp_nonce_field( 'affwp_import_nonce', 'affwp_import_nonce' ); ?>
								<?php submit_button( __( 'Import CSV', 'affiliate-wp' ), 'secondary', 'import-affiliates-submit', false ); ?>
							</p>
						</div>

						<div class="affwp-import-options" id="affwp-import-affiliates-options" style="display:none;">

							<p>
								<?php
								printf(
									__( 'Each column loaded from the CSV may be mapped to an affiliate field. Select the column that should be mapped to each field below. Any columns not needed can be ignored. See <a href="%s" target="_blank">this guide</a> for assistance with importing affiliate records.', 'affiliate-wp' ),
									esc_url( 'http://docs.affiliatewp.com/article/1893-importing-affiliates-from-csv' )
								);
								?>
							</p>

							<table class="widefat affwp_repeatable_table striped" width="100%" cellpadding="0" cellspacing="0">
								<thead>
								<tr>
									<th><strong><?php esc_html_e( 'Affiliate Field', 'affiliate-wp' ); ?></strong></th>
									<th><strong><?php esc_html_e( 'CSV Column', 'affiliate-wp' ); ?></strong></th>
									<th><strong><?php esc_html_e( 'Data Preview', 'affiliate-wp' ); ?></strong></th>
								</tr>
								</thead>
								<tbody>
									<?php affwp_do_import_fields( 'affiliates' ); ?>
								</tbody>
							</table>
							<p class="submit">
								<button class="affwp-import-proceed button-primary"><?php esc_html_e( 'Process Import', 'affiliate-wp' ); ?></button>
							</p>
						</div>

					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php esc_html_e( 'Import Referrals', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Import a CSV of referral records.', 'affiliate-wp' ); ?></p>
					<form method="post" enctype="multipart/form-data" class="affwp-batch-import-form" data-batch_id="import-referrals" data-required="affiliate,amount" data-nonce=""<?php echo esc_attr( wp_create_nonce( 'import-referrals_step_nonce' ) ); ?>">
						<div class="affwp-import-file-wrap">
							<p>
								<input name="affwp-import-file" id="affwp-import-referrals-file" type="file" />
							</p>
							<p>
								<?php wp_nonce_field( 'affwp_import_nonce', 'affwp_import_nonce' ); ?>
								<?php submit_button( __( 'Import CSV', 'affiliate-wp' ), 'secondary', 'import-referrals-submit', false ); ?>
							</p>
						</div>

						<div class="affwp-import-options" id="affwp-import-referrals-options" style="display:none;">

							<p>
								<?php
								printf(
									__( 'Each column loaded from the CSV may be mapped to a referral field. Select the column that should be mapped to each field below. Any columns not needed can be ignored. Any affiliates that don&#8217;t exist will be created. See <a href="%s" target="_blank">this guide</a> for assistance with importing referral records.', 'affiliate-wp' ),
									esc_url( 'http://docs.affiliatewp.com/article/1896-importing-referrals-from-csv' )
								);
								?>
							</p>

							<table class="widefat affwp_repeatable_table striped" width="100%" cellpadding="0" cellspacing="0">
								<thead>
								<tr>
									<th><strong><?php esc_html_e( 'Referral Field', 'affiliate-wp' ); ?></strong></th>
									<th><strong><?php esc_html_e( 'CSV Column', 'affiliate-wp' ); ?></strong></th>
									<th><strong><?php esc_html_e( 'Data Preview', 'affiliate-wp' ); ?></strong></th>
								</tr>
								</thead>
								<tbody>
									<?php affwp_do_import_fields( 'referrals' ); ?>
								</tbody>
							</table>
							<p class="submit">
								<button class="affwp-import-proceed button-primary"><?php esc_html_e( 'Process Import', 'affiliate-wp' ); ?></button>
							</p>
						</div>

					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
		</div><!-- .metabox-holder -->
	</div><!-- #affwp-dashboard-widgets-wrap -->
<?php
}
add_action( 'affwp_tools_tab_export_import', 'affwp_export_import_tab' );

/**
 * System Info tab.
 *
 * @since 1.8.7
 */
function affwp_system_info_tab() {
	if ( ! current_user_can( 'manage_affiliate_options' ) ) {
		return;
	}

	$action_url = affwp_admin_url( 'tools', array( 'tab' => 'system_info' ) );
	?>
	<form action="<?php echo esc_url( $action_url ); ?>" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus(); this.select()" id="affwp-system-info-textarea" name="affwp-sysinfo" title="<?php esc_attr_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'affiliate-wp' ); ?>">
			<?php echo affwp_tools_system_info_report(); ?>
		</textarea>
		<p class="submit">
			<input type="hidden" name="affwp_action" value="download_sysinfo" />
			<?php submit_button( 'Download System Info File', 'primary', 'affwp-download-sysinfo', false ); ?>
		</p>
	</form>
	<?php
}

add_action( 'affwp_tools_tab_system_info', 'affwp_system_info_tab' );

/**
 * Listens for system info download requests and delivers the file.
 *
 * @since 1.8.7
 */
function affwp_tools_sysinfo_download() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( ! current_user_can( 'manage_affiliate_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['affwp-download-sysinfo'] ) ) {
		return;
	}

	nocache_headers();

	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="affwp-system-info.txt"' );

	echo wp_strip_all_tags( $_POST['affwp-sysinfo'] );
	exit;
}
add_action( 'admin_init', 'affwp_tools_sysinfo_download' );

/**
 * Debug Tab
 *
 * @since       1.7.15
 * @return      void
 */
function affwp_debug_tab() {
	?>
	<div id="affwp-dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Debug Log', 'affiliate-wp' ); ?></span></h3>
				<div class="inside">
					<form id="affwp-debug-log" method="post">
						<p><?php esc_html_e( 'Use this tool to help debug referral tracking.', 'affiliate-wp' ); ?></p>
						<textarea readonly="readonly" onclick="this.focus(); this.select()" class="large-text" rows="15" name="affwp-debug-log-contents"><?php echo esc_textarea( affiliate_wp()->utils->logs->get_log() ); ?></textarea>
						<p class="submit">
							<input type="hidden" name="affwp_action" value="submit_debug_log" />
							<?php
							submit_button( __( 'Download Debug Log File', 'affiliate-wp' ), 'primary', 'affwp-download-debug-log', false );
							submit_button( __( 'Clear Log', 'affiliate-wp' ), 'secondary affwp-inline-button', 'affwp-clear-debug-log', false  );
							?>
						</p>
						<?php wp_nonce_field( 'affwp-debug-log-action' ); ?>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
		</div><!-- .metabox-holder -->
	</div><!-- #affwp-dashboard-widgets-wrap -->
<?php
}
add_action( 'affwp_tools_tab_debug', 'affwp_debug_tab' );

/**
 * Handles submit actions for the debug log.
 *
 * @since 2.1
 */
function affwp_submit_debug_log() {
	if ( ! current_user_can( 'manage_affiliate_options' ) ) {
		return;
	}

	check_admin_referer( 'affwp-debug-log-action' );

	if ( isset( $_REQUEST['affwp-download-debug-log'] ) ) {
		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="affwp-debug-log.txt"' );

		echo wp_strip_all_tags( $_REQUEST['affwp-debug-log-contents'] );
		exit;

	} elseif ( isset( $_REQUEST['affwp-clear-debug-log'] ) ) {

		// Clear the debug log.
		affiliate_wp()->utils->logs->clear_log();

		wp_safe_redirect( affwp_admin_url( 'tools', array( 'tab' => 'debug' ) ) );
		exit;

	}
}
add_action( 'affwp_submit_debug_log', 'affwp_submit_debug_log' );

/**
 * Clear the debug log
 *
 * @since       1.7.15
 * @deprecated  2.1 See affwp_submit_debug_log
 * @see         affwp_submit_debug_log()
 */
function affwp_clear_debug_log() {
	_deprecated_function( __FUNCTION__, '2.1', 'affwp_submit_debug_log' );

	affwp_submit_debug_log();
}

/**
 * Renders the API Keys tools tab.
 *
 * @since 1.9
 */
function affwp_rest_api_keys_tab() {
	if ( ! current_user_can( 'manage_consumers' ) ) {
		return;
	}

	$keys_table = new \AffWP\REST\Admin\Consumers_Table;
	$keys_table->prepare_items();

	$keys_table->views();
	$keys_table->display();
}
add_action( 'affwp_tools_tab_api_keys', 'affwp_rest_api_keys_tab' );

/**
 * Processes a batch export download request.
 *
 * @since 2.0
 */
function affwp_process_batch_export_download() {
	if( ! wp_verify_nonce( $_REQUEST['nonce'], 'affwp-batch-export' ) ) {
		wp_die(
			__( 'Nonce verification failed', 'affiliate-wp' ),
			__( 'Error', 'affiliate-wp' ),
			array( 'response' => 403 )
		);
	}

	if ( empty( $_REQUEST['batch_id'] ) || false === $batch = affiliate_wp()->utils->batch->get( $_REQUEST['batch_id'] ) ) {
		wp_die(
			__( 'Invalid batch ID.', 'affiliate-wp' ),
			__( 'Error', 'affiliate-wp' ),
			array( 'response' => 403 )
		);
	}

	require_once $batch['file'];

	if ( empty( $batch['class'] ) || ( ! empty( $batch['class'] ) && ! class_exists( $batch['class'] ) ) ) {
		wp_die(
			__( 'Invalid batch export class.', 'affiliate-wp' ),
			__( 'Error', 'affiliate-wp' ),
			array( 'response' => 403 )
		);
	}

	$export = new $batch['class']( $step = 0 );
	$export->export();

}
add_action( 'affwp_download_batch_export', 'affwp_process_batch_export_download' );
