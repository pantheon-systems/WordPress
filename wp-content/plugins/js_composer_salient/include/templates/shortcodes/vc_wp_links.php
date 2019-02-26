<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $category
 * @var $orderby
 * @var $options
 * @var $limit
 * @var $el_class
 * @var $el_id
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Wp_Links
 */
$category = $options = $orderby = $limit = $el_class = $el_id = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$options = explode( ',', $options );
if ( in_array( 'images', $options ) ) {
	$atts['images'] = true;
}
if ( in_array( 'name', $options ) ) {
	$atts['name'] = true;
}
if ( in_array( 'description', $options ) ) {
	$atts['description'] = true;
}
if ( in_array( 'rating', $options ) ) {
	$atts['rating'] = true;
}

$el_class = $this->getExtraClass( $el_class );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '<div ' . implode( ' ', $wrapper_attributes ) . ' class="vc_wp_links wpb_content_element' . esc_attr( $el_class ) . '">';
$type = 'WP_Widget_Links';
$args = array();
global $wp_widget_factory;
// to avoid unwanted warnings let's check before using widget
if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
	ob_start();
	the_widget( $type, $atts, $args );
	$output .= ob_get_clean();

	$output .= '</div>';

	echo $output;
}
