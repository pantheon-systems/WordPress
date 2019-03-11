<?php

class WCML_Multi_Currency_Coupons{

	private $wc_27_coupons;

    public function __construct() {

	    // WooCommerce 2.7 + compatibility
    	$this->wc_27_coupons = method_exists( 'WC_Coupon', 'get_amount' );

        add_action('woocommerce_coupon_loaded', array($this, 'filter_coupon_data'));

    }

    public function filter_coupon_data($coupon){

        // Alias compatibility
	    if( !$this->wc_27_coupons ) {
		    if ( isset( $coupon->amount ) && ! isset( $coupon->coupon_amount ) ) {
			    $coupon->coupon_amount = $coupon->amount;
		    }
		    if ( isset( $coupon->type ) && ! isset( $coupon->discount_type ) ) {
			    $coupon->discount_type = $coupon->type;
		    }
	    }
        //

	    $discount_type = $this->wc_27_coupons ? $coupon->get_discount_type() : $coupon->discount_type;

        if( $discount_type == 'fixed_cart' || $discount_type == 'fixed_product' ){

	        if( $this->wc_27_coupons ) {
		        $coupon->set_amount( apply_filters( 'wcml_raw_price_amount', $coupon->get_amount() ) );
	        }else{ // backward compatibility
		        $coupon->coupon_amount = apply_filters( 'wcml_raw_price_amount', $coupon->coupon_amount );
	        }

        }

	    if( $this->wc_27_coupons ){

	        $coupon->set_minimum_amount( apply_filters('wcml_raw_price_amount', $coupon->get_minimum_amount() ) );
	        $coupon->set_maximum_amount( apply_filters('wcml_raw_price_amount', $coupon->get_maximum_amount() ) );

        } else { // backward compatibility
	        $coupon->minimum_amount = apply_filters('wcml_raw_price_amount', $coupon->minimum_amount);
	        $coupon->maximum_amount = apply_filters('wcml_raw_price_amount', $coupon->maximum_amount);
        }

    }

}