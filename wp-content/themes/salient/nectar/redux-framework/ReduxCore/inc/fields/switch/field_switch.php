<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_switch' ) ) {
    class ReduxFramework_switch {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 0.0.4
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
         * @since ReduxFramework 0.0.4
         */
        function render() {

            $cb_enabled = $cb_disabled = ''; //no errors, please
            //
            /* nectar additon */
            //upgrade proof
            $old_options = get_option('salient');
            global $salient_redux;

            //upgrade proof
            $fixed_ID = $this->field['id'];
            $legacy_switch = (!empty($old_options[$fixed_ID])) ? $old_options[$fixed_ID] : '-';
            $display_val = (isset($salient_redux[$this->field['id']]) || $legacy_switch == '-') ? $this->value : $legacy_switch;
            
            //Get selected
            /*nectar addition*/
            $activated_switch = '';
            /*nectar addition end*/
            if ( (int) $display_val == 1 ) {
                $cb_enabled = ' selected';
                $activated_switch = 'activated';
            } else {
                $cb_disabled = ' selected';
            }
            /* nectar addition end */

            //Label ON
            $this->field['on'] = isset( $this->field['on'] ) ? $this->field['on'] : __( 'On', 'redux-framework' );

            //Label OFF
            $this->field['off'] = isset( $this->field['off'] ) ? $this->field['off'] : __( 'Off', 'redux-framework' );
            
            /*nectar addition*/
            echo '<div class="switch-options salient '.$activated_switch.'">';
            /*nectar addition end*/
            echo '<label class="cb-enable' . $cb_enabled . '" data-id="' . $this->field['id'] . '"><span>' . $this->field['on'] . '</span></label>';
            echo '<label class="cb-disable' . $cb_disabled . '" data-id="' . $this->field['id'] . '"><span>' . $this->field['off'] . '</span></label>';
            echo '<input type="hidden" class="checkbox checkbox-input ' . $this->field['class'] . '" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" value="' . $display_val . '" />';
            echo '</div>';
        } //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 0.0.4
         */
        function enqueue() {
            wp_enqueue_script(
                'redux-field-switch-js',
                ReduxFramework::$_url . 'inc/fields/switch/field_switch' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-switch-css',
                    ReduxFramework::$_url . 'inc/fields/switch/field_switch.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }
    }
}