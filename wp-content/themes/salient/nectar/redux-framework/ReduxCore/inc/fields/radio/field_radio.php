<?php

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxFramework_radio' ) ) {
        class ReduxFramework_radio {

            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since ReduxFramework 1.0.0
             */
            function __construct( $field = array(), $value = '', $parent ) {
                $this->parent = $parent;
                $this->field  = $field;
                $this->value  = $value;
            }

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since ReduxFramework 1.0.0
             */
            function render() {

                /* nectar additon */
                //upgrade proof
                $old_options = get_option('salient');
                global $salient_redux;

                //upgrade proof
                $fixed_ID = $this->field['id'];
                $legacy_radio = (!empty($old_options[$fixed_ID])) ? $old_options[$fixed_ID] : '-';
                $display_val = (isset($salient_redux[$this->field['id']]) || $legacy_radio == '-') ? $this->value : $legacy_radio;

                /* nectar additon end */

                if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
                    if ( empty( $this->field['args'] ) ) {
                        $this->field['args'] = array();
                    }
                    $this->field['options'] = $this->parent->get_wordpress_data( $this->field['data'], $this->field['args'] );
                }

                $this->field['data_class'] = ( isset( $this->field['multi_layout'] ) ) ? 'data-' . $this->field['multi_layout'] : 'data-full';

                if ( ! empty( $this->field['options'] ) ) {
                    echo '<ul class="' . $this->field['data_class'] . '">';

                    foreach ( $this->field['options'] as $k => $v ) {
                        echo '<li>';
                        echo '<label for="' . $this->field['id'] . '_' . array_search( $k, array_keys( $this->field['options'] ) ) . '">';
                        /* nectar additon */
                        echo '<input type="radio" class="radio ' . $this->field['class'] . '" id="' . $this->field['id'] . '_' . array_search( $k, array_keys( $this->field['options'] ) ) . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $k . '" ' . checked( $display_val, $k, false ) . '/>';
                        /* nectar additon end */
                        echo ' <span>' . $v . '</span>';
                        echo '</label>';
                        echo '</li>';
                    }
                    //foreach

                    echo '</ul>';
                }
            } //function
        } //class
    }