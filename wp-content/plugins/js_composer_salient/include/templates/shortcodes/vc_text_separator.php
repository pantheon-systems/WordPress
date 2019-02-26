<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $title_align
 * @var $el_width
 * @var $style
 * @var $title
 * @var $align
 * @var $color
 * @var $accent_color
 * @var $el_class
 * @var $el_id
 * @var $layout
 * @var $css
 * @var $border_width
 * @var $add_icon
 * Icons:
 * @var $i_type
 * @var $i_icon_fontawesome
 * @var $i_icon_openiconic
 * @var $i_icon_typicons
 * @var $i_icon_entypo
 * @var $i_icon_linecons
 * @var $i_color
 * @var $i_custom_color
 * @var $i_background_style
 * @var $i_background_color
 * @var $i_custom_background_color
 * @var $i_size
 * @var $i_css_animation
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortcode_Vc_Text_Separator
 */

$title_align = $el_width = $style = $title = $align = $color = $accent_color = $el_class = $el_id = $layout = $css = $border_width = $add_icon = $i_type = $i_icon_fontawesome = $i_icon_openiconic = $i_icon_typicons = $i_icon_entypo = $i_icon_linecons = $i_color = $i_custom_color = $i_background_style = $i_background_color = $i_custom_background_color = $i_size = $i_css_animation = $css_animation = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_separator wpb_content_element';

$class .= ( '' !== $title_align ) ? ' vc_' . $title_align : '';
$class .= ( '' !== $el_width ) ? ' vc_sep_width_' . $el_width : ' vc_sep_width_100';
$class .= ( '' !== $style ) ? ' vc_sep_' . $style : '';
$class .= ( '' !== $border_width ) ? ' vc_sep_border_width_' . $border_width : '';
$class .= ( '' !== $align ) ? ' vc_sep_pos_' . $align : '';

$class .= ( 'separator_no_text' === $layout ) ? ' vc_separator_no_text' : '';
if ( '' !== $color && 'custom' !== $color ) {
	$class .= ' vc_sep_color_' . $color;
}
$inline_css = ( 'custom' === $color && '' !== $accent_color ) ? ' style="' . vc_get_css_color( 'border-color', $accent_color ) . '"' : '';

$class_to_filter = $class;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$css_class = esc_attr( trim( $css_class ) );
$icon = '';
if ( 'true' === $add_icon ) {
	vc_icon_element_fonts_enqueue( $i_type );
	$icon = $this->getVcIcon( $atts );
}

$content = '';
if ( $icon ) {
	$content = $icon;
}
if ( '' !== $title && 'separator_no_text' !== $layout ) {
	$css_class .= ' vc_separator-has-text';
	$content .= '<h4>' . $title . '</h4>';
}
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$wrapper_attributes_html = implode( ' ', $wrapper_attributes );
$separatorHtml = <<<TEMPLATE
<div class="$css_class" $wrapper_attributes_html><span class="vc_sep_holder vc_sep_holder_l"><span $inline_css class="vc_sep_line"></span></span>$content<span class="vc_sep_holder vc_sep_holder_r"><span $inline_css class="vc_sep_line"></span></span>
</div>
TEMPLATE;
echo $separatorHtml;
