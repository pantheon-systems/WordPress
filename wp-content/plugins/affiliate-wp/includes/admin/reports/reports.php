<?php
/**
 * Reports Admin class.
 *
 * This class renders the Reports screen of AffiliateWP.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @since       1.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-admin.php';

/**
 * Sets up the Reports admin.
 *
 * @since 1.0
 *
 * @see AffWP_Reports_Admin;
 */
function affwp_reports_admin() {
	new AffWP\Admin\Reports;
}
