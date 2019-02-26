<?php
use AffWP\Admin\Overview\Meta_Box;

/**
 * Affiiates Overview
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Overview
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Overview Metaboxes.
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-totals.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-registrations.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-most-valuable.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-recent-referrals.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-recent-referral-visits.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-highest-converting-urls.php';

/**
 * Initializes meta boxes displayed via the Overview screen.
 *
 * @since 1.9.4
 */
function affwp_init_overview_meta_boxes() {
	new Meta_Box\Totals;
	new Meta_Box\Affiliate_Registrations;
	new Meta_Box\Most_Valuable_Affiliates;
	new Meta_Box\Recent_Referrals;
	new Meta_Box\Recent_Referral_Visits;
	new Meta_Box\Highest_Converting_URLs;

	/**
	 * Fires after all core Overview meta boxes have been instantiated.
	 *
	 * @since 1.9.4
	 */
	do_action( 'affwp_init_overview_meta_boxes' );
}

/**
 * Displays the Overview screen.
 *
 * @since 1.0
 */
function affwp_affiliates_dashboard() {
	affwp_init_overview_meta_boxes();
	?>
	<div class="wrap">

		<h2><?php _e( 'Overview', 'affiliate-wp' ); ?></h2>

		<?php
		/**
		 * Fires at the top of the Overview page, in the area used for Overview meta-boxes.
		 */
		do_action( 'affwp_overview_meta_boxes' );
		?>

		<div id="affwp-dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'primary', null ); ?>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'secondary', null ); ?>
				</div>

				<div id="postbox-container-3" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'tertiary', null ); ?>
				</div>

			</div>
		</div>

		<?php
		/**
		 * Fires at the bottom of the Overview admin screen.
		 */
		do_action( 'affwp_overview_bottom' );
		?>

	</div>
<?php }
