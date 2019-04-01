<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var array $atts */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

$class_to_filter = 'vc-zigzag-wrapper';
$class_to_filter .= vc_shortcode_custom_css_class( $atts['css'], ' ' ) . $this->getExtraClass( $atts['el_class'] ) . $this->getCSSAnimation( $atts['css_animation'] );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$wrapper_attributes = array();
if ( ! empty( $atts['align'] ) ) {
	$class_to_filter .= ' vc-zigzag-align-' . esc_attr( $atts['align'] );
}
if ( ! empty( $atts['el_id'] ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $atts['el_id'] ) . '"';
}

$color = '';
if ( 'custom' !== $atts['color'] ) {
	$color = vc_convert_vc_color( $atts['color'] );
} else {
	$color = esc_attr( $atts['custom_color'] );
}
$width = '100%';
if ( ! empty( $atts['el_width'] ) ) {
	$width = esc_attr( $atts['el_width'] ) . '%';
}
$border_width = '10';
if ( ! empty( $atts['el_border_width'] ) ) {
	$border_width = esc_attr( $atts['el_border_width'] );
}
$minheight = 2 + intval( $border_width );
$svg = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg width="' . ( intval( $border_width ) + 2 ) . 'px' . '" height="' . intval( $border_width ) . 'px' . '" viewBox="0 0 18 15" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon id="Combined-Shape" fill="' . esc_attr( $color ) . '" points="8.98762301 0 0 9.12771969 0 14.519983 9 5.40479869 18 14.519983 18 9.12771969"></polygon></svg>';

?>
<div class="<?php echo esc_attr( $class_to_filter ); ?>" <?php echo implode( ' ', $wrapper_attributes ); ?>>
	<div class="vc-zigzag-inner"
			style="<?php echo esc_attr( 'width: ' . esc_attr( $width ) . ';min-height: ' . $minheight . 'px;background: 0 repeat-x url(\'data:image/svg+xml;utf-8,' . rawurlencode( $svg ) . '\');' ); ?>">
	</div>
</div>
