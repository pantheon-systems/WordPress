<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Post_Author
 */

$atts = $this->getAttributes( $atts );

$styles = $this->getStyles( $atts['el_class'], $atts['css'], $atts['google_fonts_data'], $atts['font_container_data'], $atts );

if ( ! empty( $atts['link'] ) ) {
	$atts['link'] = 'post_author';
	$link_html = vc_gitem_create_link( $atts );
}
$use_custom_fonts = isset( $atts['use_custom_fonts'] ) && 'yes' === $atts['use_custom_fonts'];
$settings = get_option( 'wpb_js_google_fonts_subsets' );
$subsets = '';
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
}
$content = '{{ post_author }}';
if ( ! empty( $link_html ) ) {
	$content = '<' . $link_html . '>' . $content . '</a>';
}
$css_class = array(
	$styles['css_class'],
	'vc_gitem-post-data',
);
$css_class[] = 'vc_gitem-post-data-source-post_author';
if ( $use_custom_fonts && ! empty( $atts['google_fonts_data'] ) && isset( $atts['google_fonts_data']['values']['font_family'] ) ) {
	wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $atts['google_fonts_data']['values']['font_family'] ), 'https://fonts.googleapis.com/css?family=' . $atts['google_fonts_data']['values']['font_family'] . $subsets );
}
$output .= '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '" >';
$style = '';
if ( ! empty( $styles['styles'] ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles['styles'] ) ) . '"';
}
$output .= '<' . $atts['font_container_data']['values']['tag'] . ' ' . $style . ' >';
$output .= $content;
$output .= '</' . $atts['font_container_data']['values']['tag'] . '>';
$output .= '</div>';
echo $output;

