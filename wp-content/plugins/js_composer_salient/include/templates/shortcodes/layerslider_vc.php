<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $id
 * @var $el_class
 * Shortcode class
 * @var $this WPBakeryShortCode_Layerslider_Vc
 */
$el_class = $title = $id = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class = $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_layerslider_element wpb_content_element' . $el_class, $this->settings['base'], $atts );

$output .= '<div class="' . esc_attr( $css_class ) . '">';
$output .= wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_layerslider_heading' ) );
$output .= apply_filters( 'vc_layerslider_shortcode', do_shortcode( '[layerslider id="' . $id . '"]' ) );
$output .= '</div>';

echo $output;
