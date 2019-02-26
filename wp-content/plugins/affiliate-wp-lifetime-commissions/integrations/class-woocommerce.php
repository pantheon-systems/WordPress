<?php

class Affiliate_WP_Lifetime_Commissions_WooCommerce extends Affiliate_WP_Lifetime_Commissions_Base {
    
	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
    public function init() {
        $this->context = 'woocommerce';
    }

    /**
     * Retrieves the user's email or ID.
     *
     * @access public
     * @since 1.1
     *
     * @param string $get       What type of user information to retrieve. Accepts 'email' or 'user_id'.
     * @param int    $reference Payment reference number. Default 0.
     * @param string $context   Context for the commission. In this case 'woocommerce'.
     * @return string|mixed|false The billing email if the order is valid, mixed if $get is 'user_id',
     *                            otherwise false.
     */
    public function get( $get = '', $reference = 0, $context ) {

        if ( ! $get ) {
            return false;
        }

		$woocommerce_is_300 = version_compare( WC()->version, '3.0.0', '>=' );

		if ( true === $woocommerce_is_300 ) {
			try {
				$order = new WC_Order( $reference );
			} catch( Exception $e ) {
				return false;
			}

			$order_id = $order->get_id();
		} else {
			$order = new WC_Order( $reference );

			$order_id = $order->id;
		}

		if ( 'email' === $get && $order_id > 0 ) {

			if ( true === $woocommerce_is_300 ) {
				return $order->get_billing_email();
			} else {
				return $order->billing_email;
			}

		} elseif ( 'user_id' === $get ) {
            return get_post_meta( $reference, '_customer_user', true );
        }

        return false;

    }


}
new Affiliate_WP_Lifetime_Commissions_WooCommerce;
