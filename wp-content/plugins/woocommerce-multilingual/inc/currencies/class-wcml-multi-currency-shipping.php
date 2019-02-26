<?php

class WCML_Multi_Currency_Shipping{

    /** @var WCML_Multi_Currency */
    private $multi_currency;
    /** @var Sitepress */
    private $sitepress;
    /** @var wpdb */
    private $wpdb;

    public function __construct( WCML_Multi_Currency $multi_currency, Sitepress $sitepress, wpdb $wpdb ) {

        $this->multi_currency = $multi_currency;
        $this->sitepress      = $sitepress;
        $this->wpdb           = $wpdb;
    }

    public function add_hooks(){

        // shipping method cost settings
        if( $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WC_VERSION' ), '2.6.0', '>=' ) ) {
            $rates = $this->wpdb->get_results( "SELECT * FROM {$this->wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id IN('flat_rate', 'local_pickup', 'free_shipping')" );
            foreach ( $rates as $method ) {
                $option_name = sprintf( 'woocommerce_%s_%d_settings', $method->method_id, $method->instance_id );
                add_filter( 'option_' . $option_name, array($this, 'convert_shipping_method_cost_settings') );
            }
        }else{
            // Before WooCommerce 2.6
            new WCML_Multi_Currency_Shipping_Legacy();
        }

        // Used for table rate shipping compatibility class
        add_filter( 'wcml_shipping_price_amount', array( $this, 'shipping_price_filter' ) ); // WCML filters
        add_filter( 'wcml_shipping_free_min_amount', array( $this, 'shipping_free_min_amount') ); // WCML filters

        add_filter( 'woocommerce_evaluate_shipping_cost_args', array( $this, 'woocommerce_evaluate_shipping_cost_args') );

        add_filter( 'woocommerce_shipping_packages', array( $this, 'convert_shipping_taxes') );

        add_filter( 'woocommerce_package_rates', array($this, 'convert_shipping_costs_in_package_rates') );
    }

    public function convert_shipping_costs_in_package_rates( $rates ) {

        $cache_group = 'converted_shipping_cost';

        $client_currency = $this->multi_currency->get_client_currency();
        foreach ( $rates as $rate_id => $rate ) {

            $cache_key                      = $rate_id;
            $cached_converted_shipping_cost = wp_cache_get( $cache_key, $cache_group );

            if ( $cached_converted_shipping_cost ) {
                $rate->cost = $cached_converted_shipping_cost;
            } elseif ( isset( $rate->cost ) && $rate->cost ) {
                $rate->cost = $this->multi_currency->prices->raw_price_filter( $rate->cost, $client_currency );
                wp_cache_set( $cache_key, $rate->cost, $cache_group );
            }
        }

        return $rates;
    }

    public function convert_shipping_method_cost_settings( $settings ){

        $has_free_shipping_coupon = false;
        if ( WC()->cart && $coupons = WC()->cart->get_coupons() ) {
            foreach ( $coupons as $code => $coupon ) {

                if (
                    $coupon->is_valid() &&
                    (
                        //backward compatibility for WC < 2.7
                        method_exists( $coupon, 'get_free_shipping' ) ?
                            $coupon->get_free_shipping() :
                            $coupon->enable_free_shipping()
                    )
                ) {
                    $has_free_shipping_coupon = true;
                }
            }
        }

        if( !empty( $settings['requires'] ) ){

            if(
                $settings['requires'] == 'min_amount' ||
                $settings['requires'] == 'either' ||
                ( $settings['requires'] == 'both' && $has_free_shipping_coupon )
            ){
                $settings['min_amount'] = apply_filters( 'wcml_shipping_free_min_amount', $settings['min_amount'] );
            }
        }

        return $settings;
    }

	/**
	 * @param array $args
	 *
	 * When using [cost] in the shipping class costs, we need to use the not-converted cart total
	 * It will be converted as part of the total cost
	 *
	 * @return array
	 */
    public function woocommerce_evaluate_shipping_cost_args( $args ){

        $args['cost'] = $this->multi_currency->prices->unconvert_price_amount( $args['cost'] );

        return $args;
    }

    public function convert_shipping_taxes( $packages ){

    	if( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
		    foreach ( $packages as $package_id => $package ) {
			    if ( isset( $package['rates'] ) ) {
				    foreach ( $package['rates'] as $rate_id => $rate ) {
					    if( $rate->get_shipping_tax() > 0 ){
						    $packages[ $package_id ]['rates'][ $rate_id ]->taxes =
							    WC_Tax::calc_shipping_tax( $packages[ $package_id ]['rates'][ $rate_id ]->cost, WC_Tax::get_shipping_tax_rates() );
					    }
				    }
			    }
		    }
	    }

        return $packages;
    }

    public function shipping_price_filter($price) {

        $price = $this->multi_currency->prices->raw_price_filter($price, $this->multi_currency->get_client_currency());

        return $price;

    }

    public function shipping_free_min_amount($price) {

        $price = $this->multi_currency->prices->raw_price_filter( $price, $this->multi_currency->get_client_currency() );

        return $price;

    }


}