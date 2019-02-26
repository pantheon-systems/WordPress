<?php

class Affiliate_WP_MarketPress extends Affiliate_WP_Base {

	var $is_version_3 = true;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function init() {

		$this->context      = 'marketpress';
		$this->is_version_3 = $this->get_mp_version() == '2.0' ? false : true;

		if( $this->is_version_3 ){
			add_action( 'mp_order/new_order', array( $this, 'add_pending_referral' ) );
			add_action( 'mp_order_order_paid', array( $this, 'mark_referral_complete' ) );
			add_action( 'mp_order_trashed', array( $this, 'revoke_referral_on_delete' ) );
		} else {
			add_action( 'mp_new_order', array( $this, 'add_pending_referral' ) );
			add_action( 'mp_order_paid', array( $this, 'mark_referral_complete' ) );
			add_action( 'trash_mp_order', array( $this, 'revoke_referral_on_delete' ), 10, 2 );
		}

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}

	/**
	 * Get MarketPress version.
	 *
	 * @access  public
	 */
	public function get_mp_version() {

		$mp_version = false;

		if ( defined( 'MP_VERSION' ) ) {
			$mp_version = MP_VERSION;
		} else {
			global $mp_version;
		}

		// Strip out any beta or RC components from version... get base version
		$mp_version = preg_replace( '/\.\D.*/', '', $mp_version );
		$mp_version = version_compare( $mp_version, '3.0', '>=' ) ? '3.0' : '2.0';

		return $mp_version;
	}

	/**
	 * Record a pending referral
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function add_pending_referral( $order = array() ) {

		if ( $this->was_referred() ) {
			$order_post = $order;
			$order_id   = $order->ID;

			if( $this->is_version_3 ) {
				$amount         = $order->get_meta( 'mp_order_total' );
				$cart           = $order->get_meta( 'mp_cart_info' );
				$items          = wp_list_pluck( $cart->get_items_as_objects(), 'ID' );
				$tax_total      = $order->get_meta( 'mp_tax_total', 0 );
				$shipping_total = $order->get_meta( 'mp_shipping_total', 0 );
				$order_post     = get_post( $order->ID );
			} else {
				$amount         = $order->mp_order_total;
				$items          = $order->mp_cart_info;
				$tax_total      = $order->mp_tax_total;
				$shipping_total = $order->mp_shipping_total;
			}

			if( 0 == $order_post->post_author ) {

				if( $this->is_version_3 ) {

					$customer_email = $order->get_meta( 'mp_shipping_info->email', '' );

				} else {

					$customer_email = $order->mp_shipping_info[ 'email' ];

				}

			} else {

				$user_id        = $order_post->post_author;
				$user           = get_userdata( $user_id );
				$customer_email = $user->user_email;

			}

			if ( $this->is_affiliate_email( $customer_email ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

				return; // Customers cannot refer themselves

			}

			$description = array();

		    foreach( $items as $item ) {

			    if ( is_array( $item ) ) {
				    $order_items = $item;

				    foreach( $order_items as $order_item ) {
					    $description[] = $order_item['name'];
				    }
			    } else {
				    $description[] = get_the_title( $item );
			    }

		    }

		    $description = join( ', ', $description );

			if( affiliate_wp()->settings->get( 'exclude_tax' ) ) {

				$amount -= $tax_total;

			}

			if( affiliate_wp()->settings->get( 'exclude_shipping' ) ) {

				$amount -= $shipping_total;

			}

			$referral_total = $this->calculate_referral_amount( $amount, $order_id );

			$this->insert_pending_referral( $referral_total, $order_id, $description );
		}

	}

	/**
	 * Mark a referral as complete when an order is completed
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function mark_referral_complete( $order = array() ) {

		$order_id = $order->ID;

		$referral = affiliate_wp()->referrals->get_by( 'reference', $order_id, $this->context );

		/*
		 * Add pending referral if referral not yet created because mp_order_paid hook is executed before
		 * mp_order_paid, this prevent completed referral being marked as pending
		 */
		if ( empty( $referral ) ) {

			$this->add_pending_referral( $order );

		}

		$this->complete_referral( $order_id );

	}

	/**
	 * Revoke a referral when an order is deleted
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function revoke_referral_on_delete( $order ) {

		$order_id = $order;

		if( $this->is_version_3 ){
			$order_id = $order->ID;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {

			return;

		}

		if( 'mp_order' != get_post_type( $order_id ) ) {

			return;

		}

		$this->reject_referral( $order_id );

	}

	/**
	 * Set up the reference URL from the referral to the order
	 *
	 * @access  public
	 * @since   1.6
	*/
	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'marketpress' != $referral->context ) {

			return $reference;

		}

		$args = array(
			'post'   => absint( $reference ),
			'action' => 'edit'
		);

		$url = add_query_arg( $args, admin_url( 'post.php' ) );

		return '<a href="' . esc_url( $url ) . '">' . esc_html( $reference ) . '</a>';

	}

}

if ( class_exists( 'Marketpress' ) ) {
	new Affiliate_WP_MarketPress;
}
