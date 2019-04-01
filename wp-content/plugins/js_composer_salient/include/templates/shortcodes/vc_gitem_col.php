<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $width
 * @var $align
 * @var $css
 * @var $el_class
 * @var $featured_image
 * @var $img_size
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Col
 */
$width = $align = $css = $el_class = $featured_image = $img_size = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

extract( $atts );
// TODO: Note that vc_map_get_attributes doesnt return align so it should be checked in next bug fix

$style = '';
$width = wpb_translateColumnWidthToSpan( $width );
$css_class = $width
	. ( strlen( $el_class ) ? ' ' . $el_class : '' )
	. ' vc_gitem-col vc_gitem-col-align-' . $align
	. vc_shortcode_custom_css_class( $css, ' ' );

if ( 'yes' === $featured_image ) {
	$style = '{{ post_image_background_image_css' . ':' . $img_size . ' }}';
}
echo '<div class="' . $css_class . '"'
	. ( strlen( $style ) > 0 ? ' style="' . $style . '"' : '' )
	. '>'
	. do_shortcode( $content )
	. '</div>';
