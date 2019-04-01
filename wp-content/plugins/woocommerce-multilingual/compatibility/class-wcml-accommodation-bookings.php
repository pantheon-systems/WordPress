<?php

class WCML_Accommodation_Bookings{

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;

    function __construct( &$woocommerce_wpml ){

	    $this->woocommerce_wpml = $woocommerce_wpml;

        add_action( 'woocommerce_accommodation_bookings_after_booking_base_cost' , array( $this, 'wcml_price_field_after_booking_base_cost' ) );
        add_action( 'woocommerce_accommodation_bookings_after_booking_pricing_override_block_cost' , array( $this, 'wcml_price_field_after_booking_pricing_override_block_cost' ), 10, 2 );
        add_action( 'woocommerce_accommodation_bookings_after_bookings_pricing' , array( $this , 'after_bookings_pricing' ) );

        add_action( 'save_post', array( $this, 'save_custom_costs' ), 110, 2 );
        add_filter( 'get_post_metadata', array( $this, 'product_price_filter'), 9, 4 );

        add_action( 'init', array( $this, 'load_assets' ), 100 );
    }

    function wcml_price_field_after_booking_base_cost( $post_id ){

        $this->echo_wcml_price_field( $post_id, 'wcml_wc_booking_base_cost' );

    }

    function wcml_price_field_after_booking_pricing_override_block_cost( $pricing, $post_id ){

        $this->echo_wcml_price_field( $post_id, 'wcml_wc_booking_pricing_override_block_cost', $pricing );

    }

    function after_bookings_pricing( $post_id ){

        $product_terms = wp_get_post_terms( $post_id, 'product_type', array( "fields" => "names" ) );

	    if(
		    in_array( 'accommodation-booking', $product_terms )&&
		    $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT &&
		    $this->woocommerce_wpml->products->is_original_product( $post_id )
	    ){

            $custom_costs_status = get_post_meta( $post_id, '_wcml_custom_costs_status', true );

            $checked = !$custom_costs_status ? 'checked="checked"' : ' ';

            echo '<div class="wcml_custom_costs">';

            echo '<input type="radio" name="_wcml_custom_costs" id="wcml_custom_costs_auto" value="0" class="wcml_custom_costs_input" '. $checked .' />';
            echo '<label for="wcml_custom_costs_auto">'. __('Calculate costs in other currencies automatically', 'woocommerce-multilingual') .'</label>';

            $checked = $custom_costs_status == 1 ? 'checked="checked"' : ' ';

            echo '<input type="radio" name="_wcml_custom_costs" value="1" id="wcml_custom_costs_manually" class="wcml_custom_costs_input" '. $checked .' />';
            echo '<label for="wcml_custom_costs_manually">'. __('Set costs in other currencies manually', 'woocommerce-multilingual') .'</label>';

            wp_nonce_field( 'wcml_save_accommodation_bookings_custom_costs', '_wcml_custom_costs_nonce' );

            echo '</div>';
        }

    }

    function echo_wcml_price_field( $post_id, $field, $pricing = false, $check = true, $resource_id = false ){

	    if(
		    $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT &&
		    ( !$check || $this->woocommerce_wpml->products->is_original_product( $post_id ) )
	    ){

            $currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

            $wc_currencies = get_woocommerce_currencies();

            echo '<div class="wcml_custom_cost_field" >';

            foreach($currencies as $currency_code => $currency){

                switch( $field ){
                    case 'wcml_wc_booking_base_cost':
                        woocommerce_wp_text_input( array( 'id' => 'wcml_wc_booking_base_cost', 'class'=>'wcml_bookings_custom_price', 'name' => 'wcml_wc_accommodation_booking_base_cost['.$currency_code.']', 'label' => get_woocommerce_currency_symbol($currency_code), 'description' => __( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'woocommerce-bookings' ), 'value' => get_post_meta( $post_id, '_wc_booking_base_cost_'.$currency_code, true ), 'type' => 'number', 'desc_tip' => true, 'custom_attributes' => array(
                            'min'   => '',
                            'step' 	=> '0.01'
                        ) ) );
                        break;


                    case 'wcml_wc_booking_pricing_override_block_cost':

                        if( isset( $pricing[ 'override_block_'.$currency_code ] ) ){
                            $value = $pricing[ 'override_block_'.$currency_code ];
                        }else{
                            $value = '';
                        }

                        echo '<div class="wcml_bookings_range_block" >';
                        echo '<label>'. get_woocommerce_currency_symbol($currency_code) .'</label>';
                        echo '<input type="number" step="0.01" name="wcml_wc_accommodation_booking_pricing_override_block_cost['.$currency_code.']" class="wcml_bookings_custom_price" value="'. $value .'" placeholder="0" />';
                        echo '</div>';
                        break;

                    default:
                        break;

                }

            }

            echo '</div>';

        }
    }

    function save_custom_costs( $post_id, $post ){

        $nonce = filter_input( INPUT_POST, '_wcml_custom_costs_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        if( isset( $_POST['_wcml_custom_costs'] ) && isset( $nonce ) && wp_verify_nonce( $nonce, 'wcml_save_accommodation_bookings_custom_costs' ) ){

            update_post_meta( $post_id, '_wcml_custom_costs_status', $_POST['_wcml_custom_costs'] );

            if( $_POST['_wcml_custom_costs'] == 1 ){

                $currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

                foreach( $currencies as $code => $currency ){

                    $wc_booking_base_cost = $_POST[ 'wcml_wc_accommodation_booking_base_cost' ][ $code ];
                    update_post_meta( $post_id, '_wc_booking_base_cost_'.$code, $wc_booking_base_cost  );
                    update_post_meta( $post_id, '_price_'.$code, $wc_booking_base_cost  );

                }

                $updated_meta = array();
                $booking_pricing = get_post_meta( $post_id, '_wc_booking_pricing', true );

                foreach ( maybe_unserialize( $booking_pricing ) as $key => $prices ) {

                    $updated_meta[ $key ] = $prices;

                    foreach ( $currencies as $code => $currency ) {

                        $updated_meta[ $key ][ 'override_block_'.$code ] = $_POST[ 'wcml_wc_accommodation_booking_pricing_override_block_cost' ][ $code ];

                    }

                }

                update_post_meta( $post_id, '_wc_booking_pricing', $updated_meta );

            }
        }

    }

    function product_price_filter( $value, $object_id, $meta_key, $single ){

	    if(
		    $meta_key == '_price' &&
		    $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT &&
		    !is_admin() &&
		    get_post_type( $object_id ) == 'product' &&
		    ( $currency = $this->woocommerce_wpml->multi_currency->get_client_currency() ) != get_option( 'woocommerce_currency' )
	    ) {

            remove_filter( 'get_post_metadata', array( $this, 'product_price_filter' ), 9, 4 );

            $original_product = $this->woocommerce_wpml->products->get_original_product_id( $object_id );

            if ( get_post_meta( $original_product, '_wcml_custom_costs_status' ) ) {

                $price = get_post_meta( $object_id, '_price_' . $currency , true );
            }

            add_filter( 'get_post_metadata', array( $this, 'product_price_filter' ), 9, 4 );
        }

        return isset( $price) ? $price : $value;
    }

    public function load_assets(){

        $this->woocommerce_wpml->compatibility->bookings->load_assets( 'accommodation-booking' );
    }

}