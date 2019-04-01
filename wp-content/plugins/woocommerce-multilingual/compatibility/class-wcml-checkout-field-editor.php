<?php

class WCML_Checkout_Field_Editor {

    protected $package, $billing, $shipping, $additional;

    function __construct( $package = false ){
        $this->package = $package ? $package : (object) array(
            'kind'      => 'WooCommerce Add-On',
            'kind_slug' => 'woocommerce-add-on',
            'name'      => 'checkout-field-editor',
            'title'     => 'WooCommerce Checkout Field Editor'
        );
    }

    function add_hooks() {
        global $supress_field_modification;
        if ( ! is_admin() && ! $supress_field_modification ) {
            add_filter( 'pre_option_wc_fields_billing', array( $this, 'get_billing' ) );
            add_filter( 'pre_option_wc_fields_shipping', array( $this, 'get_shipping' ) );
            add_filter( 'pre_option_wc_fields_additional', array( $this, 'get_additional' ) );
        }
        add_filter( 'pre_update_option_wc_fields_billing', array( $this, 'register_fields' ) );
        add_filter( 'pre_update_option_wc_fields_shipping', array( $this, 'register_fields' ) );
        add_filter( 'pre_update_option_wc_fields_additional', array( $this, 'register_fields' ) );
    }

    private function get_exclude_fields() {

        $exclude_fields = array(
            'shipping_address_1',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
            'billing_address_1',
            'billing_city',
            'billing_state',
            'billing_postcode'
        );

        return apply_filters( 'wcml_cfe_exclude_fields_to_register', $exclude_fields );

    }

    public function register_fields( $fields ) {
        foreach ( $fields as $string_name => $field ) {

            if ( in_array( $string_name, $this->get_exclude_fields() ) ) {
                continue;
            }

            // Translate label
            if ( !empty( $field['label'] ) ) {
                do_action( 'wpml_register_string',
                        $field['label'],
                        "{$string_name}_label",
                        $this->package,
                        "{$string_name} Label",
                        $this->package->kind
                );
            }
            // Translate placeholder
            if ( !empty( $field['placeholder'] ) ) {
                do_action( 'wpml_register_string',
                        $field['placeholder'],
                        "{$string_name}_placeholder",
                        $this->package,
                        "{$string_name} Placeholder",
                        $this->package->kind
                );
            }
            // Translate options
            if ( !empty( $field['options'] ) ) {
                $i = 1;
                foreach ( $field['options'] as $option ) {
                    do_action( 'wpml_register_string',
                            $option,
                            "{$string_name}_option_{$i}",
                            $this->package,
                            "{$string_name} Option {$i}",
                            $this->package->kind
                    );
                    $i++;
                }
            }
        }
        return $fields;
    }

    public function translate_fields( $fields ) {
        foreach ( $fields as $string_name => &$field ) {

            if ( in_array( $string_name, $this->get_exclude_fields() ) ) {
                continue;
            }

            // Translate label
            if ( !empty( $field['label'] ) ) {
                $field['label'] = apply_filters( 'wpml_translate_string',
                        $field['label'],
                        "{$string_name}_label",
                        $this->package
                );
            }
            // Translate placeholder
            if ( !empty( $field['placeholder'] ) ) {
                $field['placeholder'] = apply_filters( 'wpml_translate_string',
                        $field['label'],
                        "{$string_name}_placeholder",
                        $this->package
                );
            }
            // Translate options
            if ( !empty( $field['options'] ) ) {
                $i = 1;
                foreach ( $field['options'] as $k => $option ) {
                    $field['options'][$k] = apply_filters( 'wpml_translate_string',
                            $option,
                            "{$string_name}_option_{$i}",
                            $this->package
                    );
                    $i++;
                }
            }
        }
        return $fields;
    }

    public function get_billing() {
        if ( is_null( $this->billing ) ) {
            remove_filter( 'pre_option_wc_fields_billing', array($this, 'get_billing') );
            $this->billing = $this->translate_fields( get_option( 'wc_fields_billing', array() ) );
            add_filter( 'pre_option_wc_fields_billing', array($this, 'get_billing') );
        }
        return $this->billing;
    }

    public function get_shipping() {
        if ( is_null( $this->shipping ) ) {
            remove_filter( 'pre_option_wc_fields_shipping', array($this, 'get_shipping') );
            $this->shipping = $this->translate_fields( get_option( 'wc_fields_shipping', array() ) );
            add_filter( 'pre_option_wc_fields_shipping', array($this, 'get_shipping') );
        }
        return $this->shipping;
    }

    public function get_additional() {
        if ( is_null( $this->additional ) ) {
            remove_filter( 'pre_option_wc_fields_additional', array($this, 'get_additional') );
            $this->additional = $this->translate_fields( get_option( 'wc_fields_additional', array() ) );
            add_filter( 'pre_option_wc_fields_additional', array($this, 'get_additional') );
        }
        return $this->additional;
    }
}
