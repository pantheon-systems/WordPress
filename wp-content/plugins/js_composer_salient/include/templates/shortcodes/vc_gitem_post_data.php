<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Post_Data
 */
$output = $text = $google_fonts = $font_container = $el_class = $css = $font_container_data = $link_html = '';
$google_fonts_data = array();
extract( $this->getAttributes( $atts ) );

extract( $this->getStyles( $el_class, $css, $google_fonts_data, $font_container_data, $atts ) );

$data_source = $this->getDataSource( $atts );
if ( isset( $atts['link'] ) && '' !== $atts['link'] && 'none' !== $atts['link'] ) {
	$link_html = vc_gitem_create_link( $atts );
}
$use_custom_fonts = isset( $atts['use_custom_fonts'] ) && 'yes' === $atts['use_custom_fonts'];
$settings = get_option( 'wpb_js_google_fonts_subsets' );
$subsets = '';
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
}
$content = '{{ post_data:' . esc_attr( $data_source ) . ' }}';
if ( ! empty( $link_html ) ) {
	$content = '<' . $link_html . '>' . $content . '</a>';
}
$css_class .= ' vc_gitem-post-data';
if ( $data_source ) {
	$css_class .= ' vc_gitem-post-data-source-' . $data_source;
}
if ( $use_custom_fonts && ! empty( $google_fonts_data ) && isset( $google_fonts_data['values']['font_family'] ) ) {
	wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), 'https://fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
}
$output .= '<div class="' . esc_attr( $css_class ) . '" >';
$style = '';
if ( ! empty( $styles ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
}
$output .= '<' . $font_container_data['values']['tag'] . ' ' . $style . ' >';
$output .= $content;
$output .= '</' . $font_container_data['values']['tag'] . '>';
$output .= '</div>';
echo $output;
