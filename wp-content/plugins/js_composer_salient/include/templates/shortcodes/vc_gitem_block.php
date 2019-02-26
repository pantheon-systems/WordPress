<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $background_color
 * @var $float
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem
 */
$el_class = $background_color = $float = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ( ! empty( $background_color ) ) {
	$background_color = ' vc_bg-' . $background_color;
}
echo '<div class="vc_gitem-block' . esc_attr( $background_color )
	. ( strlen( $el_class ) > 0 ? ' ' . esc_attr( $el_class ) : '' )
	. ' vc_gitem-float-' . esc_attr( $float )
	. '">'
	. do_shortcode( $content ) . '</div>';
