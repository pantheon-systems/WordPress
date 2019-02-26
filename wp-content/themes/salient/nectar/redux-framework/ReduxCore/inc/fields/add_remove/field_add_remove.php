<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

  if ( ! class_exists( 'ReduxFramework_add_remove' ) ) {
        class ReduxFramework_add_remove {

            /**
             * Field Constructor.
             *
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since Redux_Options 1.0.0
            */
            function __construct($field = array(), $value ='', $parent) {
                $this->field = $field;
        		$this->value = $value;
        		$this->args = $parent->args;
            }

            /**
             * Field Render Function.
             *
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since Redux_Options 1.0.0
            */
            function render() {
            	echo '<div class="add-remove-controls">';
        		echo '<input type="hidden" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="'.esc_attr($this->value).'" />';
                echo '<a href="" class="add button button-primary">+</a> ';
        		echo '<a href="" class="remove button button-primary">-</a>';
        		echo '</div>';
            }

            /**
             * Enqueue Function.
             *
             * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
             *
             * @since Redux_Options 1.0.0
            */
            function enqueue() {
                wp_enqueue_script(
                    'redux-opts-field-add_remove-js', 
                   ReduxFramework::$_url . 'inc/fields/add_remove/field_add_remove.js', 
                    array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
                    time(),
                    true
                );
            }
        }
}
