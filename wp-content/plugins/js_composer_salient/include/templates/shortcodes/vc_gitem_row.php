<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $css
 * @var $el_class
 * @var $position
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Row
 */
$css = $el_class = $position = '';

extract( shortcode_atts( array(
	'css' => '',
	'el_class' => '',
	'position' => 'top',
), $atts ) );

$css_class = 'vc_gitem_row vc_row'
	. ( strlen( $el_class ) ? ' ' . $el_class : '' )
	. vc_shortcode_custom_css_class( $css, ' ' )
	. ( $position ? ' vc_gitem-row-position-' . $position : '' );
if ( ! vc_gitem_has_content( $content ) ) {
	return;
}
echo '<div class="' . esc_attr( $css_class ) . '">' . do_shortcode( $content ) . '</div>';
