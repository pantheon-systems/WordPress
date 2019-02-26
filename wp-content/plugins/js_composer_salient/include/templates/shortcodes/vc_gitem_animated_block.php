<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $css
 * @var $animation
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Animated_Block
 */
$css = $animation = $animation_attr = '';

extract( shortcode_atts( array(
	'css' => '', // unmapped
	'animation' => '',
), $atts ) );

$css_style = '';
$css_class = 'vc_gitem-animated-block ' . vc_shortcode_custom_css_class( $css, ' ' );
if ( ! empty( $animation ) ) {
	$css_class .= ' vc_gitem-animate vc_gitem-animate-' . $animation;
	$animation_attr .= ' data-vc-animation="' . esc_attr( $animation ) . '"';
} elseif ( 'vc_gitem_preview' !== vc_request_param( 'action' ) && vc_verify_admin_nonce() && ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) ) {
	$content = preg_replace( '/(?<=\[)(vc_gitem_zone_b\b)/', '$1 render="no"', $content );
}
?>
<div class="<?php echo esc_attr( $css_class ) ?>"<?php echo $animation_attr ?><?php
echo( empty( $css_style ) ? '' : ' style="' . esc_attr( $css_style ) . '"' )
?>><?php echo do_shortcode( $content ) ?></div>
