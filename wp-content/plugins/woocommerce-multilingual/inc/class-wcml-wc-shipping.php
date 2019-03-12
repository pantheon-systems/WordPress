<?php

class WCML_WC_Shipping{

    private $current_language;
    private $sitepress;

    function __construct( &$sitepress ){

        $this->sitepress = $sitepress;

        $this->current_language = $this->sitepress->get_current_language();
        if( $this->current_language == 'all' ){
            $this->current_language = $this->sitepress->get_default_language();
        }

    }

    function add_hooks(){

        add_action('woocommerce_tax_rate_added', array($this, 'register_tax_rate_label_string'), 10, 2 );
        add_action('wp_ajax_woocommerce_shipping_zone_methods_save_settings', array( $this, 'save_shipping_zone_method_from_ajax'), 9 );
        add_action( 'icl_save_term_translation', array( $this, 'sync_class_costs_for_new_shipping_classes' ), 100, 2 );
        add_action( 'wp_ajax_woocommerce_shipping_zone_methods_save_settings', array( $this, 'update_woocommerce_shipping_settings_for_class_costs_from_ajax'), 9);

        add_filter('woocommerce_package_rates', array($this, 'translate_shipping_methods_in_package'));
        add_filter('woocommerce_rate_label',array($this,'translate_woocommerce_rate_label'));
        add_filter( 'pre_update_option_woocommerce_flat_rate_settings', array( $this, 'update_woocommerce_shipping_settings_for_class_costs' ) );
        add_filter( 'pre_update_option_woocommerce_international_delivery_settings', array( $this, 'update_woocommerce_shipping_settings_for_class_costs' ) );

        $this->shipping_methods_filters();
    }

    function shipping_methods_filters(){

        $shipping_methods = WC()->shipping->get_shipping_methods();

        foreach ( $shipping_methods as $shipping_method ) {

            if( isset( $shipping_method->id ) ){
                $shipping_method_id = $shipping_method->id;
            }else{
                continue;
            }

            if( ( defined('WC_VERSION') && version_compare( WC_VERSION , '2.6', '<' ) ) ){
                add_filter( 'woocommerce_settings_api_sanitized_fields_'.$shipping_method_id, array( $this, 'register_shipping_strings' ) );
            }else{
                add_filter( 'woocommerce_shipping_' . $shipping_method_id . '_instance_settings_values', array( $this, 'register_zone_shipping_strings' ),9,2 );
            }

            add_filter( 'option_woocommerce_'.$shipping_method_id.'_settings', array( $this, 'translate_shipping_strings' ), 9, 2 );
        }
    }

    function save_shipping_zone_method_from_ajax(){
        foreach( $_POST['data'] as $key => $value ){
            if( strstr( $key, '_title' ) ){
                $shipping_id = str_replace( 'woocommerce_', '', $key );
                $shipping_id = str_replace( '_title', '', $shipping_id );
                $this->register_shipping_title( $shipping_id.$_POST['instance_id'], $value );
                break;
  	        }
  	    }
  	}

  	function register_zone_shipping_strings( $instance_settings, $object ){
        if( !empty( $instance_settings['title'] ) ){
            $this->register_shipping_title( $object->id.$object->instance_id, $instance_settings['title'] );

            $instance_settings = $this->sync_flat_rate_class_cost( $object->get_post_data(), $instance_settings );
        }

        return $instance_settings;
    }

    function register_shipping_strings( $fields ){
        $shipping = WC_Shipping::instance();

        foreach( $shipping->get_shipping_methods() as $shipping_method ){
            if( isset( $_POST['woocommerce_'.$shipping_method->id.'_enabled'] ) ){
                $shipping_method_id = $shipping_method->id;
                break;
            }
        }

        if( isset( $shipping_method_id ) ){
            $this->register_shipping_title( $shipping_method_id, $fields['title'] );
        }

        return $fields;
    }

    function register_shipping_title( $shipping_method_id, $title ){
        do_action( 'wpml_register_single_string', 'woocommerce', $shipping_method_id .'_shipping_method_title', $title );
    }

    function translate_shipping_strings( $value, $option = false ){

        if( $option && isset( $value['enabled']) && $value['enabled'] == 'no' ){
            return $value;
        }

        $shipping_id = str_replace( 'woocommerce_', '', $option );
        $shipping_id = str_replace( '_settings', '', $shipping_id );

        if( isset( $value['title'] ) ){
            $value['title'] = $this->translate_shipping_method_title( $value['title'], $shipping_id );
        }

        return $value;
    }

    function translate_shipping_methods_in_package( $available_methods ){
        foreach($available_methods as $key => $method){
	        $shipping_id = $method->method_id . $method->instance_id;
            $available_methods[$key]->label =  $this->translate_shipping_method_title( $method->label, $shipping_id );
        }

        return $available_methods;
    }

    /**
     * @param string $title
     * @param string $shipping_id
     * @param string|bool $language
     *
     * @return string
     */
	public function translate_shipping_method_title( $title, $shipping_id, $language = false ) {

		if ( is_admin() && did_action( 'admin_init' )  && did_action( 'current_screen' ) ) {
			$screen        = get_current_screen();
			$is_edit_order = $screen->id === 'shop_order';
		} else {
			$is_edit_order = false;
		}

		if ( ! is_admin() || $is_edit_order ) {

			$shipping_id      = str_replace( ':', '', $shipping_id );
			$translated_title = apply_filters(
				'wpml_translate_single_string',
				$title,
				'woocommerce',
				$shipping_id . '_shipping_method_title',
				$language ? $language : $this->current_language
			);

			if ( $translated_title ) {
				$title = $translated_title;
			}

		}

		return $title;
	}

    function translate_woocommerce_rate_label( $label ){

        $label = apply_filters( 'wpml_translate_single_string', $label, 'woocommerce taxes', $label );

        return $label;
    }

    function register_tax_rate_label_string( $id, $tax_rate ){

        if( !empty( $tax_rate['tax_rate_name'] ) ){
            do_action('wpml_register_single_string', 'woocommerce taxes', $tax_rate['tax_rate_name'] , $tax_rate['tax_rate_name'] );
        }

    }

    function sync_class_costs_for_new_shipping_classes( $original_tax, $result ){
        //update flat rate options for shipping classes
        if( $original_tax->taxonomy == 'product_shipping_class' ){

            $settings = get_option( 'woocommerce_flat_rate_settings' );
            if( is_array( $settings ) ){
                update_option( 'woocommerce_flat_rate_settings', $this->update_woocommerce_shipping_settings_for_class_costs( $settings ) );
            }

            $settings = get_option( 'woocommerce_international_delivery_settings' );
            if( is_array( $settings ) ){
                update_option( 'woocommerce_international_delivery_settings', $this->update_woocommerce_shipping_settings_for_class_costs( $settings ) );
            }

        }
    }

    public function update_woocommerce_shipping_settings_for_class_costs( $settings ){
        remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );
        foreach( $settings as $setting_key => $value ){

            if(  substr($setting_key, 0, 11) == 'class_cost_' ){

                $shipp_class_key = substr($setting_key, 11 );

                if( is_numeric( $shipp_class_key ) ){
                    $shipp_class = get_term( $shipp_class_key, 'product_shipping_class' );
                }else{
                    $shipp_class = get_term_by( 'slug', $shipp_class_key, 'product_shipping_class' );
                }
                $trid = $this->sitepress->get_element_trid( $shipp_class->term_taxonomy_id, 'tax_product_shipping_class' );

                $translations = $this->sitepress->get_element_translations( $trid, 'tax_product_shipping_class' );

                foreach( $translations as $translation ){

                    $tr_shipp_class = get_term_by( 'term_taxonomy_id', $translation->element_id, 'product_shipping_class' );

                    if( is_numeric( $shipp_class_key ) ){
                        $settings[ 'class_cost_'.$tr_shipp_class->term_id ] = $value;
                    }else{
                        $settings[ 'class_cost_'.$tr_shipp_class->slug ] = $value;
                    }

                }

            }

        }
        add_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );

        return $settings;
    }

    function update_woocommerce_shipping_settings_for_class_costs_from_ajax(){

        if (isset($_POST['data']['woocommerce_flat_rate_type']) && $_POST['data']['woocommerce_flat_rate_type'] == 'class') {

            $flat_rate_setting_id = 'woocommerce_flat_rate_' . $_POST['data']['instance_id'] . '_settings';
            $settings = get_option( $flat_rate_setting_id, true );

            $settings = $this->sync_flat_rate_class_cost( $_POST['data'], $settings );

            update_option($flat_rate_setting_id, $settings);
        }
    }

	/**
	 * @param array $data
	 * @param array $inst_settings
	 *
	 * @return array|mixed
	 */
    public function sync_flat_rate_class_cost( $data, $inst_settings ){

        $settings = array();
        foreach ( $data as $key => $value ) {
            if ( 0 === strpos( $key, 'woocommerce_flat_rate_class_cost_') ) {
            	$limit = strlen( 'woocommerce_flat_rate_' );
                $settings[ substr( $key, $limit ) ] = stripslashes( $value );
            }
        }

        $updated_costs_settings = $this->update_woocommerce_shipping_settings_for_class_costs( $settings );

        $inst_settings = is_array( $inst_settings ) ? array_merge( $inst_settings, $updated_costs_settings ) : $updated_costs_settings;

        return $inst_settings;
    }

}