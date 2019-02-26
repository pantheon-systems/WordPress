<?php

class Affiliate_WP_EasyCart extends Affiliate_WP_Base {

	/**
	 * Setup actions and filters
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function init() {

		$this->context = 'wpeasycart';

		add_action( 'wpeasycart_order_inserted', array( $this, 'add_pending_referral' ), 10, 5 );

		// There should be an option to choose which of these is used
		add_action( 'wpeasycart_order_paid', array( $this, 'mark_referral_complete' ), 10 );
		add_action( 'wpeasycart_full_order_refund', array( $this, 'revoke_referral_on_refund' ), 10 );

	}

	/**
	 * Store a pending referral when a new order is created
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function add_pending_referral( $order_id, $cart, $order_totals, $user, $payment_type ){

		if( $this->was_referred() ) {

			if( affwp_get_affiliate_email( $this->affiliate_id ) == $user->email ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return false; // Customers cannot refer themselves
			}

			if( affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context ) ) {
				return false; // Referral already created for this reference
			}

			$cart_shipping = $order_totals->shipping_total;
			$cart_tax      = $order_totals->tax_total;
			$items         = $cart->cart;

			// Calculate the referral amount based on product prices
			$amount = 0.00;
			foreach( $items as $cart_item ) {

				if( $cart_item->has_affiliate_rule ) {
					continue; // Referrals are disabled on this product
				}

				// The order discount has to be divided across the items

				$product_total = $cart_item->total_price;
				$shipping      = 0;

				if( $cart_shipping > 0 && ! affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

					$shipping       = $cart_shipping / count( $items );
					$product_total += $shipping;

				}

				if( $cart_tax > 0 && ! affiliate_wp()->settings->get( 'exclude_tax' ) ) {

					$tax            = $cart_tax / count( $items );
					$product_total += $tax;

				}

				if( $product_total <= 0 ) {
					continue;
				}

				$amount += $this->calculate_referral_amount( $product_total, $order_id, $cart_item->product_id );

			}

			if( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {
			
				$this->log( 'Referral not created due to 0.00 amount.' );

				return false; // Ignore a zero amount referral
			}

			$description = $this->get_referral_description( $cart->cart );
			$visit_id    = affiliate_wp()->tracking->get_visit_id();

			$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
				'amount'       => $amount,
				'reference'    => $order_id,
				'description'  => $description,
				'affiliate_id' => $this->affiliate_id,
				'visit_id'     => $visit_id,
				'context'      => $this->context
			), $amount, $order_id, $description, $this->affiliate_id, $visit_id, array(), $this->context ) );

			$this->log( sprintf( 'Pending Referral #%d created successfully', $referral_id ) );

		}

	}

	/**
	 * Mark referral as complete when payment is completed
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function mark_referral_complete( $order_id = 0 ) {

		$this->complete_referral( $order_id );

	}

	/**
	 * Revoke the referral when the order is refunded
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function revoke_referral_on_refund( $order_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $order_id );

	}

	/**
	 * Retrieves the referral description
	 *
	 * @access  public
	 * @since   1.1
	*/
	public function get_referral_description( $items ) {

		$description = array();

		foreach ( $items as $key => $item ) {

			$description[] = $item->title;

		}

		$description = implode( ', ', $description );

		return $description;

	}
}

if ( function_exists( 'wpeasycart_load_startup' ) ) {
	new Affiliate_WP_EasyCart;
}
