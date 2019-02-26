<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $width
 * @var $is_end
 * @var $css
 * @var $c_zone_position
 * @var $bgimage
 * @var $height
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem
 */
$el_class = $width = $is_end = $css = $c_zone_position = $bgimage = $height = '';

extract( shortcode_atts( array(
	'el_class' => '',
	'width' => '12',
	'is_end' => '',
	'css' => '',
	'c_zone_position' => '',
	'bgimage' => '',
	'height' => '',
), $atts ) );

$css_class = 'vc_grid-item vc_clearfix' . ( 'true' === $is_end ? ' vc_grid-last-item' : '' )
	. ( strlen( $el_class ) ? ' ' . $el_class : '' )
	. ' vc_col-sm-'
	. $width
	. ( ! empty( $c_zone_position ) ? ' vc_grid-item-zone-c-' . $c_zone_position : '' );
$css_class_mini = 'vc_grid-item-mini vc_clearfix ' . vc_shortcode_custom_css_class( $css, ' ' );
$css_class .= '{{ filter_terms_css_classes }}';
$css_style = '';

if ( 'featured' === $bgimage ) {
	$css_style = 'background-image: url(\'{{ post_image_url }}\');';
	$css_class .= ' vc_grid-item-background-cover';
}
if ( strlen( $height ) > 0 ) {
	$css_style .= 'height: ' . $height . ';';
}
$output = '<div class="' . esc_attr( $css_class ) . '"'
	. ( empty( $css_style ) ? '' : ' style="' . esc_attr( $css_style ) . '"' )
	. '><div class="' . $css_class_mini . '">' . do_shortcode( $content )
	. '</div><div class="vc_clearfix"></div></div>';
echo $output;
