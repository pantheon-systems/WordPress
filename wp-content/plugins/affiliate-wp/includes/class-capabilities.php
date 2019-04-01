<?php
/**
 * Roles and Capabilities
 *
 * @package     AffiliateWP
 * @subpackage  Classes/Roles
 * @copyright   Copyright (c) 2012, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

/**
 * Affiliate_WP_Roles Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 * @since 1.0
 */
class Affiliate_WP_Capabilities {

	/**
	 * Get things going
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'map_meta_cap', array( $this, 'map_meta_caps' ), 10, 4 );
	}

	/**
	 * Add new capabilities
	 *
	 * @access public
	 * @since  1.0
	 * @global obj $wp_roles
	 * @return void
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			$wp_roles->add_cap( 'administrator', 'view_affiliate_reports' );
			$wp_roles->add_cap( 'administrator', 'export_affiliate_data' );
			$wp_roles->add_cap( 'administrator', 'export_referral_data' );
			$wp_roles->add_cap( 'administrator', 'export_customer_data' );
			$wp_roles->add_cap( 'administrator', 'export_visit_data' );
			$wp_roles->add_cap( 'administrator', 'export_payout_data' );
			$wp_roles->add_cap( 'administrator', 'manage_affiliate_options' );
			$wp_roles->add_cap( 'administrator', 'manage_affiliates' );
			$wp_roles->add_cap( 'administrator', 'manage_referrals' );
			$wp_roles->add_cap( 'administrator', 'manage_customers' );
			$wp_roles->add_cap( 'administrator', 'manage_visits' );
			$wp_roles->add_cap( 'administrator', 'manage_creatives' );
			$wp_roles->add_cap( 'administrator', 'manage_payouts' );
			$wp_roles->add_cap( 'administrator', 'manage_consumers' );
		}
	}


	/**
	 * Remove core post type capabilities (called on uninstall)
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function remove_caps() {

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			/** Site Administrator Capabilities */
			$wp_roles->remove_cap( 'administrator', 'view_affiliate_reports' );
			$wp_roles->remove_cap( 'administrator', 'export_affiliate_data' );
			$wp_roles->remove_cap( 'administrator', 'export_referral_data' );
			$wp_roles->remove_cap( 'administrator', 'export_customer_data' );
			$wp_roles->remove_cap( 'administrator', 'export_visit_data' );
			$wp_roles->remove_cap( 'administrator', 'export_payout_data' );
			$wp_roles->remove_cap( 'administrator', 'manage_affiliate_options' );
			$wp_roles->remove_cap( 'administrator', 'manage_affiliates' );
			$wp_roles->remove_cap( 'administrator', 'manage_referrals' );
			$wp_roles->remove_cap( 'administrator', 'manage_customers' );
			$wp_roles->remove_cap( 'administrator', 'manage_visits' );
			$wp_roles->remove_cap( 'administrator', 'manage_creatives' );
			$wp_roles->remove_cap( 'administrator', 'manage_payouts' );
			$wp_roles->remove_cap( 'administrator', 'manage_consumers' );

		}
	}

	/**
	 * Maps meta capabilities to primitive ones.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array  $caps    The user's actual capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 * @return array (Maybe) modified capabilities.
	 */
	public function map_meta_caps( $caps, $cap, $user_id, $args ) {
		switch( $cap ) {
			case 'add_affiliate':
				$caps[] = 'manage_affiliates';
				break;

			case 'edit_affiliate':
			case 'delete_affiliate':
			case 'view_affiliate':
				$affiliate = affwp_get_affiliate( $args[0] );

				$caps[] = $affiliate ? 'manage_affiliates' : 'do_not_allow';
				break;

			case 'add_creative':
				$caps[] = 'manage_creatives';
				break;

			case 'edit_creative':
			case 'delete_creative':
			case 'view_creative':
				$creative = affwp_get_creative( $args[0] );

				$caps[] = $creative ? 'manage_creatives' : 'do_not_allow';
				break;

			case 'edit_customer':
			case 'delete_customer':
			case 'view_customer':
				$customer = affwp_get_customer( $args[0] );

				$caps[] = $customer ? 'manage_customers' : 'do_not_allow';
				break;

			case 'add_payout':
				$caps[] = 'manage_payouts';
				break;

			case 'view_payout':
				$payout = affwp_get_payout( $args[0] );

				$caps[] = $payout ? 'manage_payouts' : 'do_not_allow';
				break;

			case 'add_referral':
				$caps[] = 'manage_referrals';
				break;

			case 'edit_referral':
			case 'delete_referral':
				$referral = affwp_get_referral( $args[0] );

				$caps[] = $referral ? 'manage_referrals' : 'do_not_allow';
				break;

			case 'add_visit':
				$caps[] = 'manage_visits';
				break;

			case 'add_api_key':
				$caps[] = 'manage_consumers';
				break;

			case 'regenerate_api_key':
			case 'revoke_api_key':
				$consumer = affwp_get_rest_consumer( $args[0] );

				$caps[] = $consumer ? 'manage_consumers' : 'do_not_allow';
				break;
		}

		return $caps;
	}

}
