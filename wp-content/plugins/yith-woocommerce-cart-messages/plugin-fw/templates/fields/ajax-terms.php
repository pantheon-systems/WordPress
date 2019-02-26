<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 *
 * [Important Note] the stored value is:
 *  - array                     if WooCommerce version >= 3.0.0
 *  - string (comma-separated)  otherwise
 */

!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

yith_plugin_fw_enqueue_enhanced_select();

$default_field = array(
    'id'       => '',
    'name'     => '',
    'class'    => 'yith-term-search',
    'no_value' => false,
    'multiple' => false,
    'data'     => array(),
    'style'    => 'width:400px',
    'value'    => ''
);

foreach ( $default_field as $field_key => $field_value ) {
    if ( empty( $field[ $field_key ] ) ) {
        $field[ $field_key ] = $field_value;
    }
}
unset( $field_key );
unset( $field_value );
extract( $field );
/**
 * @var string       $id
 * @var string       $class
 * @var bool         $no_value
 * @var bool         $multiple
 * @var array        $data
 * @var string       $name
 * @var string       $style
 * @var string|array $value
 */

if ( $no_value )
    $value = array();

$default_data = array(
    'action'      => 'yith_plugin_fw_json_search_terms',
    'placeholder' => __( 'Search Categories', 'yith-plugin-fw' ),
    'allow_clear' => false,
    'taxonomy'    => 'category',
    'term_field'  => 'id'
);
$data         = wp_parse_args( $data, $default_data );
$show_id      = isset( $data[ 'show_id' ] ) && $data[ 'show_id' ];

// separate select2 needed data and other data
$select2_custom_attributes = array();
$select2_data              = array();
$select2_data_keys         = array( 'placeholder', 'allow_clear', 'action' );
foreach ( $data as $d_key => $d_value ) {
    if ( in_array( $d_key, $select2_data_keys ) ) {
        $select2_data[ $d_key ] = $d_value;
    } else {
        $select2_custom_attributes[ 'data-' . $d_key ] = $d_value;
    }
}

$term_field = $data[ 'term_field' ];

// populate data-selected by value
$data_selected = array();
if ( !empty( $value ) ) {
    if ( $multiple ) {
        if ( 'id' === $term_field ) {
            $value = is_array( $value ) ? array_map( 'absint', $value ) : explode( ',', $value );
        } else {
            $value = is_array( $value ) ? $value : explode( ',', $value );
        }
        foreach ( $value as $term_value ) {
            $term = get_term_by( $term_field, $term_value, $data[ 'taxonomy' ] );
            if ( is_object( $term ) ) {
                $title = wp_kses_post( html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ) );
                $title .= ( $show_id ? " (#{$term->term_id})" : '' );
                $data_selected[ $term_value ] = $title;
            } else {
                $data_selected[ $term_value ] = '#' . $term_value;
            }
        }
    } else {
        $term_value = 'id' === $term_field ? absint( $value ) : $value;
        $term       = get_term_by( $term_field, $term_value, $data[ 'taxonomy' ] );
        if ( is_object( $term ) ) {
            $title = wp_kses_post( html_entity_decode( $term->name, ENT_QUOTES, get_bloginfo( 'charset' ) ) );
            $title .= ( $show_id ? " (#{$term->term_id})" : '' );
            $data_selected[ $term_value ] = $title;
        } else {
            $data_selected[ $term_value ] = '#' . $term_value;
        }
    }
}

// parse $value to string to prevent issue with wc2.6
$value = is_array( $value ) ? implode( ',', $value ) : $value;
?>
<div class="yith-plugin-fw-select2-wrapper">
    <?php
    if ( function_exists( 'yit_add_select2_fields' ) ) {
        yit_add_select2_fields( array(
                                    'id'                => $id,
                                    'name'              => $name,
                                    'class'             => $class,
                                    'data-multiple'     => $multiple,
                                    'data-placeholder'  => $select2_data[ 'placeholder' ],
                                    'data-allow_clear'  => $select2_data[ 'allow_clear' ],
                                    'data-action'       => $select2_data[ 'action' ],
                                    'custom-attributes' => $select2_custom_attributes,
                                    'style'             => $style,
                                    'value'             => $value,
                                    'data-selected'     => $data_selected,
                                    'data-term-field'   => $term_field
                                ) );
    }
    ?>
</div>