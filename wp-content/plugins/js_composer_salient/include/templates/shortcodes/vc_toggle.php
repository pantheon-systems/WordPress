<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $style
 * @var $color
 * @var $size
 * @var $open
 * @var $css_animation
 * @var $el_id
 * @var $content - shortcode content
 * @var $css
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Toggle
 */
$title = $el_class = $style = $color = $size = $open = $css_animation = $css = $el_id = '';
$output = '';

$inverted = false;
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

// checking is color inverted
$style = str_replace( '_outline', '', $style, $inverted );
/**
 * @since 4.4
 */
$elementClass = array(
	'base' => apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_toggle', $this->settings['base'], $atts ),
	// TODO: check this code, don't know how to get base class names from params
	'style' => 'vc_toggle_' . $style,
	'color' => ( $color ) ? 'vc_toggle_color_' . $color : '',
	'inverted' => ( $inverted ) ? 'vc_toggle_color_inverted' : '',
	'size' => ( $size ) ? 'vc_toggle_size_' . $size : '',
	'open' => ( 'true' === $open ) ? 'vc_toggle_active' : '',
	'extra' => $this->getExtraClass( $el_class ),
	'css_animation' => $this->getCSSAnimation( $css_animation ), // TODO: remove getCssAnimation as function in helpers
);

$class_to_filter = trim( implode( ' ', $elementClass ) );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

?>
<div <?php echo isset( $el_id ) && ! empty( $el_id ) ? 'id="' . esc_attr( $el_id ) . '"' : ''; ?> class="<?php echo esc_attr( $css_class ); ?>">
	<div class="vc_toggle_title"><?php echo apply_filters( 'wpb_toggle_heading', $this->getHeading( $atts ), array(
			'title' => $title,
			'open' => $open,
		) ); ?><i class="vc_toggle_icon"></i></div>
	<div class="vc_toggle_content"><?php echo wpb_js_remove_wpautop( apply_filters( 'the_content', $content ), true ); ?></div>
</div>
