<?php
/**
 * Admin Options Page
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @return void
 */
function affwp_settings_admin() {

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php affwp_navigation_tabs( affwp_get_settings_tabs(), $active_tab, array( 'settings-updated' => false ) ); ?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'affwp_settings' );
				do_settings_fields( 'affwp_settings_' . $active_tab, 'affwp_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieves the settings tabs.
 *
 * @since 1.0
 *
 * @return array $tabs Settings tabs.
 */
function affwp_get_settings_tabs() {

	$tabs                 = array();
	$tabs['general']      = __( 'General', 'affiliate-wp' );
	$tabs['integrations'] = __( 'Integrations', 'affiliate-wp' );
	$tabs['emails']       = __( 'Emails', 'affiliate-wp' );
	$tabs['misc']         = __( 'Misc', 'affiliate-wp' );

	/**
	 * Filters the list of settings tabs.
	 *
	 * @param array $tabs Settings tabs.
	 */
	return apply_filters( 'affwp_settings_tabs', $tabs );
}

/**
 * Forces a license key check anytime the General settings tab is loaded
 *
 * @since 2.1.4
 *
 * @return void
 */
function affwp_check_license_before_settings_load() {

	if( empty( $_GET['page'] ) || 'affiliate-wp-settings' !== $_GET['page'] ) {
		return;
	}

	if( empty( $_GET['tab'] ) ) {
		return;
	}

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	if( 'general' === $active_tab && affiliate_wp()->settings->get_license_key() ) {
		affiliate_wp()->settings->check_license( true );
	}

}
add_action( 'admin_init', 'affwp_check_license_before_settings_load', 0 );