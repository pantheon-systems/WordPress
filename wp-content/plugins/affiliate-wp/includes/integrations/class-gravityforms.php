<?php

class Affiliate_WP_Gravity_Forms extends Affiliate_WP_Base {

	/**
	 * Register hooks for this integration
	 *
	 * @access public
	 */
	public function init() {

		if ( ! class_exists( 'GFFormsModel' ) || ! class_exists( 'GFCommon' ) ) {
			return;
		}

		$this->context = 'gravityforms';

		// Gravity Forms hooks
		add_filter( 'gform_entry_created', array( $this, 'add_pending_referral' ), 10, 2 );
		add_action( 'gform_post_payment_completed', array( $this, 'mark_referral_complete' ), 10, 2 );
		add_action( 'gform_post_payment_refunded', array( $this, 'revoke_referral_on_refund' ), 10, 2 );

		// Internal hooks
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

		// Form settings
		add_filter( 'gform_form_settings', array( $this, 'add_settings' ), 10, 2 );
		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_settings' ) );

		// Coupon settings
		add_filter( 'gform_gravityformscoupons_feed_settings_fields', array( $this, 'coupon_settings' ), 10, 2 );
		add_filter( 'admin_footer', array( $this, 'coupon_scripts' ) );
	}

	/**
	 * Add pending referral
	 *
	 * @access public
	 * @uses GFFormsModel::get_lead()
	 * @uses GFCommon::get_product_fields()
	 * @uses GFCommon::to_number()
	 *
	 * @param array $entry
	 * @param array $form
	 */
	public function add_pending_referral( $entry, $form ) {

		// Block referral if form does not allow them
		if ( ! rgar( $form, 'affwp_allow_referrals' ) ) {
			return;
		}

		// Check if an affiliate coupon was included
		$this->maybe_check_coupons( $form, $entry );

		// Block referral if not referred or affiliate ID is empty
		if ( ! $this->was_referred() && empty( $this->affiliate_id ) ) {
			return;
		}

		// Get all emails from submitted form
		$emails = $this->get_emails( $entry, $form );

		// Block referral if any of the affiliate's emails have been submitted
		if ( $emails ) {
			foreach ( $emails as $customer_email ) {
				if ( $this->is_affiliate_email( $customer_email, $this->affiliate_id ) ) {

					$this->log( 'Referral not created because affiliate\'s own account was used.' );

					return false;

				}
			}
		}

		// Do some craziness to determine the price (this should be easy but is not)

		$desc      = isset( $form['title'] ) ? $form['title'] : '';
		$entry     = GFFormsModel::get_lead( $entry['id'] );
		$products  = GFCommon::get_product_fields( $form, $entry );
		$total     = 0;

		foreach ( $products['products'] as $key => $product ) {

			$price = GFCommon::to_number( $product['price'] );

			if ( is_array( rgar( $product,'options' ) ) ) {

				$count = sizeof( $product['options'] );
				$index = 1;

				foreach ( $product['options'] as $option ) {
					$price += GFCommon::to_number( $option['price'] );
				}

			}

			$subtotal = floatval( $product['quantity'] ) * $price;

			$total += $subtotal;

		}

		// replace description if there are products
		if ( ! empty( $products['products'] ) ) {
			$product_names = wp_list_pluck( $products['products'], 'name' );
			$desc = implode( ', ', $product_names );
		}

		$total += floatval( $products['shipping']['price'] );

		$referral_total = $this->calculate_referral_amount( $total, $entry['id'] );

		$this->insert_pending_referral( $referral_total, $entry['id'], $desc );

		if( empty( $total ) ) {
			$this->mark_referral_complete( $entry, array() );
		}

	}

	/**
	 * Mark referral as complete
	 *
	 * @access public
	 * @uses GFFormsModel::add_note()
	 *
	 * @param array $entry
	 * @param array $action
	 */
	public function mark_referral_complete( $entry, $action ) {

		$this->complete_referral( $entry['id'] );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $entry['id'], $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		GFFormsModel::add_note( $entry["id"], 0, 'AffiliateWP', $note );

	}

	/**
	 * Revoke referral on refund
	 *
	 * @access public
	 * @uses GFFormsModel::add_note()
	 *
	 * @param array $entry
	 * @param array $action
	 */
	public function revoke_referral_on_refund( $entry, $action ) {

		$this->reject_referral( $entry['id'] );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $entry['id'], $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		GFFormsModel::add_note( $entry["id"], 0, 'AffiliateWP', $note );

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access public
	 * @uses GFFormsModel::get_lead()
	 *
	 * @param  int    $reference
	 * @param  object $referral
	 * @return string
	 */
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'gravityforms' != $referral->context ) {
			return $reference;
		}

		$entry = GFFormsModel::get_lead( $reference );

		$url = admin_url( 'admin.php?page=gf_entries&view=entry&id=' . $entry['form_id'] . '&lid=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

	/**
	 * Checks for submitted coupons and sets affiliate ID to the associated affiliate, if any
	 *
	 * @since 1.9
	 * @access public
	 * @uses GFCoupons::get_submitted_coupon_codes()
	 * @uses GFCoupons::get_coupon_field()
	 * @uses GFCoupons::get_config()
	 *
	 * @param  array  $form
	 * @param  array  $entry
	 * @return void
	 */
	public function maybe_check_coupons( $form, $entry ) {

		if( ! class_exists( 'GFCoupons' ) ) {
			return;
		}

		$gf_coupons   = new GFCoupons;
		$coupons      = $gf_coupons->get_submitted_coupon_codes( $form, $entry );
		$coupon_field = $gf_coupons->get_coupon_field( $form );

		if( empty( $coupons ) ) {
			return;
		}

		if ( ! is_object( $coupon_field ) ) {
			return;
		}

		foreach( $coupons as $coupon ) {

			// Forms can have multiple coupons. If there are multiple affiliate coupons, the last one in the list will be used.

			$config = $gf_coupons->get_config( $form, $coupon );

			if( empty( $config['meta']['affwp_affiliate'] ) ) {
				continue;
			}

			$username  = $config['meta']['affwp_affiliate'];
			$affiliate = affwp_get_affiliate( $username );

			if( $affiliate && affiliate_wp()->tracking->is_valid_affiliate( $affiliate->ID ) ) {

				$this->affiliate_id = $affiliate->ID;

			}

		}

	}

	/**
	 * Get all emails from form
	 *
	 * @since 2.0
	 * @access public
	 * @return array $emails all emails submitted via email fields
	 */
	public function get_emails( $entry, $form ) {

		$email_fields = GFCommon::get_email_fields( $form );

		$emails = array();

		if ( $email_fields ) {
			foreach ( $email_fields as $email_field ) {
				if ( ! empty( $entry[ $email_field->id ] ) ) {
					$emails[] = $entry[ $email_field->id ];
				}
			}
		}

		return $emails;

	}

	/**
	 * Register the form-specific settings
	 *
	 * @since  1.7
	 * @return void
	 */
	public function add_settings( $settings, $form ) {

		$checked = rgar( $form, 'affwp_allow_referrals' );

		$field  = '<input type="checkbox" id="affwp_allow_referrals" name="affwp_allow_referrals" value="1" ' . checked( 1, $checked, false ) . ' />';
		$field .= ' <label for="affwp_allow_referrals">' . __( 'Enable affiliate referral creation for this form', 'affiliate-wp' ) . '</label>';

		$settings['Form Options']['affwp_allow_referrals'] = '
			<tr>
				<th>' . __( 'Allow referrals', 'affiliate-wp' ) . '</th>
				<td>' . $field . '</td>
			</tr>';

		return $settings;

	}

	/**
	 * Save form settings
	 *
	 * @since 1.7
	 */
	public function save_settings( $form ) {

		$form['affwp_allow_referrals'] = rgpost( 'affwp_allow_referrals' );

		return $form;

	}


	/**
	 * Add settings to Coupon edit screens
	 *
	 * @since 1.9
	 */
	public function coupon_settings( $settings, $addon ) {

		$settings[2]['fields'][] = array(
			'name'  => 'affwp_affiliate',
			'label' => __( 'Affiliate Coupon', 'affiliate-wp' ),
			'type'  => 'text',
			'class' => 'affwp_gf_coupon',
			'tooltip' => __( 'To connect this coupon to an affiliate, enter the username of the affiliate. Anytime this coupon is redeemed, the connected affiliate will receive a referral commission.', 'affiliate-wp' )
		);

		return $settings;

	}

	/**
	 * Add inline scripts to Coupon edit screen
	 *
	 * @since 1.9
	 */
	public function coupon_scripts() {

		if( empty( $_GET['page'] ) || 'gravityformscoupons' !== $_GET['page'] ) {
			return;
		}

		if( empty( $_GET['fid'] ) ) {
			return;
		}
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Ajax user search.
			$( '.affwp_gf_coupon' ).each( function() {
				var	$this    = $( this ),
					$action  = 'affwp_search_users',
					$search  = $this.val();

				$this.autocomplete( {
					source: ajaxurl + '?action=' + $action + '&term=' + $search,
					delay: 500,
					minLength: 2,
					position: { offset: '0, -1' },
					select: function( event, data ) {
						$this.val( data.item.user_id );
					},
					open: function() {
						$this.addClass( 'open' );
					},
					close: function() {
						$this.removeClass( 'open' );
					}
				} );

				// Unset the input if the input is cleared.
				$this.on( 'keyup', function() {
					if ( ! this.value ) {
						$this.val( '' );
					}
				} );
			} );
		});
		</script>
<?php
	}

}

if ( class_exists( 'GFCommon' ) ) {
	new Affiliate_WP_Gravity_Forms;
}
