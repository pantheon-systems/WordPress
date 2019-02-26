<?php

    /*
     * @package     Redux_Framework
     * @subpackage  Fields
     * @access      public
     * @global      $optname
     * @internal    Internal Note string
     * @link        http://reduxframework.com
     * @method      Test
     * @name        $globalvariablename
     * @param       string  $this->field['test']    This is cool.
     * @param       string|boolean  $field[default] Default value for this field.
     * @return      Test
     * @see         ParentClass
     * @since       Redux 3.0.9
     * @todo        Still need to fix this!
     * @var         string cool
     * @var         int notcool
     * @param       string[] $options {
     * @type        boolean $required Whether this element is required
     * @type        string  $label    The display name for this element
     */

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxFramework_textarea' ) ) {
        class ReduxFramework_textarea {

            /**
             * Field Constructor.
             *
             * @param       $value  Constructed by Redux class. Based on the passing in $field['defaults'] value and what is stored in the database.
             * @param       $parent ReduxFramework object is passed for easier pointing.
             *
             * @since ReduxFramework 1.0.0
             * @type string $field  [test] Description. Default <value>. Accepts <value>, <value>.
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
             *
             * @param array $arr (See above)
             *
             * @return Object A new editor object.
             **/
            function render() {

                /* nectar addition */
                //upgrade proof
                $old_options = get_option('salient');
                global $salient_redux;

                //upgrade proof
                $fixed_ID = $this->field['id'];
                $legacy_textarea = (!empty($old_options[$fixed_ID])) ? $old_options[$fixed_ID] : '-';
                $display_val = (isset($salient_redux[$this->field['id']]) || $legacy_textarea == '-') ? $this->value : $legacy_textarea;

                /* nectar addition end */

                $this->field['placeholder'] = isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : "";
                $this->field['rows']        = isset( $this->field['rows'] ) ? $this->field['rows'] : 6;
                $readonly                   = ( isset( $this->field['readonly'] ) && $this->field['readonly']) ? ' readonly="readonly"' : '';
                // The $this->field variables are already escaped in the ReduxFramework Class.
                
                /* nectar addition */
                ?>
                <textarea <?php echo $readonly; ?> name="<?php echo esc_attr($this->field['name'] . $this->field['name_suffix']); ?>" id="<?php echo $this->field['id']; ?>-textarea" placeholder="<?php echo esc_attr( $this->field['placeholder'] ); ?>" class="large-text <?php echo esc_attr($this->field['class']); ?>" rows="<?php echo esc_attr($this->field['rows']); ?>"><?php echo esc_textarea( $display_val ); ?></textarea>
            <?php  /* nectar addition end */
            }

            function sanitize( $field, $value ) {
                if ( ! isset( $value ) || empty( $value ) ) {
                    $value = "";
                } else {
                    $value = esc_textarea( $value );
                }

                return $value;
            }
        }
    }