<?php
/**
 * Affiliates Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
namespace AffWP\Admin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-tab-registry.php';

include AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-affiliates-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-referrals-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-payouts-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-visits-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-campaigns-reports-tab.php';

class Reports {

	/**
	 * Sets up the Reports admin.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		add_action( 'affwp_reports_tabs_init', array( $this, 'register_core_tabs' ) );

		$this->display();
	}

	/**
	 * Renders the admin area.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display() {
		/**
		 * Initializes Reports tabs.
		 *
		 * @since 1.9
		 */
		do_action( 'affwp_reports_tabs_init' );

		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->get_reports_tabs() ) ? $_GET['tab'] : 'referrals';

		$manage_button_template = '<a href="%1$s" class="page-title-action">%2$s</a>';

		switch( $active_tab ) {
			case 'referrals':
				$manage_button = sprintf( $manage_button_template,
					esc_url( affwp_admin_url( 'referrals' ) ),
					__( 'Manage Referrals', 'affiliate-wp' )
				);
				break;

			case 'affiliates':
				$manage_button = sprintf( $manage_button_template,
					esc_url( affwp_admin_url( 'affiliates' ) ),
					__( 'Manage Affiliates', 'affiliate-wp' )
				);
				break;

			case 'payouts':
				$manage_button = sprintf( $manage_button_template,
					esc_url( affwp_admin_url( 'payouts' ) ),
					__( 'View Payouts', 'affiliate-wp' )
				);
				break;

			case 'visits':
				$manage_button = sprintf( $manage_button_template,
					esc_url( affwp_admin_url( 'visits' ) ),
					__( 'Manage Visits', 'affiliate-wp' )
				);
				break;

			default:
				$manage_button = '';
				break;
		}
		?>
		<div class="wrap">

			<h1>
				<?php _e( 'Reports', 'affiliate-wp' ); ?>
				<?php echo $manage_button; ?>
			</h1>

			<?php
			/**
			 * Fires at the top of the admin reports page screen.
			 */
			do_action( 'affwp_reports_page_top' );
			?>

			<h2 class="nav-tab-wrapper">
				<?php
				affwp_navigation_tabs( $this->get_reports_tabs(), $active_tab, array(
					'settings-updated' => false,
					'affwp_notice'     => false
				) );
				?>
			</h2>

			<?php
			/**
			 * Fires in the middle of the admin reports page screen.
			 */
			do_action( 'affwp_reports_page_middle' );
			?>

			<div id="tab_container">

				<?php
				/**
				 * Fires inside the tab container element of the currently-active admin reports screen tab.
				 *
				 * The dynamic portion of the hook name, `$active_tab`, refers to the active reports tab.
				 */
				do_action( 'affwp_reports_tab_' . $active_tab );
				?>

			</div><!-- #tab_container-->

			<?php
			/**
			 * Fires at the bottom of the admin reports page screen.
			 */
			do_action( 'affwp_reports_page_bottom' );
			?>

		</div>
		<?php
	}

	/**
	 * Retrieves the Reports tabs.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return array $tabs Tabs array.
	 */
	public function get_reports_tabs() {
		/**
		 * Filters the tabs displayed on the Reports screen.
		 *
		 * Tabs are added by extending AffWP\Admin\Reports\Tab.
		 *
		 * @since 1.1
		 *
		 * @see \AffWP\Admin\Reports\Tab
		 *
		 * @param array $tabs Tabs array.
		 */
		return apply_filters( 'affwp_reports_tabs', array() );
	}

	/**
	 * Registers the core Reports tabs.
	 *
	 * Hooked to {@see 'affwp_reports_tabs_init'}.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_core_tabs() {
		new \AffWP\Referral\Admin\Reports\Tab;
		new \AffWP\Affiliate\Admin\Reports\Tab;
		new \AffWP\Affiliate\Payout\Admin\Reports\Tab;
		new \AffWP\Visit\Admin\Reports\Tab;
		new \AffWP\Campaign\Admin\Reports\Tab;
	}
}
