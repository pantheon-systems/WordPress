<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Single_image' );

$atts = array();
parse_str( $data, $atts );
$el_class = $image = $img_size = $img_link = $img_link_target = $img_link_large = $title = $alignment = $css_animation = $css = '';
$image_string = '';
$img_class = new WPBakeryShortCode_VC_Single_image( array( 'base' => 'vc_single_image' ) );
/** @var $img_class WPBakeryShortCode_VC_Single_image */
$atts = vc_map_get_attributes( $img_class->getShortcode(), $atts );
extract( $atts );
$style = ( '' !== $style ) ? $style : '';
$border_color = ( '' !== $border_color ) ? ' vc_box_border_' . $border_color : '';

$img_id = has_post_thumbnail( $post->ID ) ? get_post_thumbnail_id( $post->ID ) : $post->ID;
$img = wpb_getImageBySize( array(
	'attach_id' => $img_id,
	'thumb_size' => $img_size,
	'class' => 'vc_single_image-img',
) );
$img = apply_filters( 'vc_gitem_attribute_featured_image_img', $img );
if ( null === $img || false === $img ) {
	return '';
}
$el_class = $img_class->getExtraClass( $el_class );
$style = preg_replace( '/_circle_2$/', '_circle', $style );
$wrapperClass = 'vc_single_image-wrapper ' . $style . ' ' . $border_color;
$link = vc_gitem_create_link_real( $atts, $post, $wrapperClass, $title );

$image_string = ! empty( $link ) ? '<' . $link . '>' . $img['thumbnail'] . '</a>' : '<div class="' . $wrapperClass . '">' . $img['thumbnail'] . '</div>';
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_single_image wpb_content_element' . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $img_class->settings( 'base' ), $atts );
$css_class .= $img_class->getCSSAnimation( $css_animation );

$css_class .= ' vc_align_' . $alignment;

$output = '
	<div class="' . $css_class . '">
		<figure class="wpb_wrapper vc_figure">
			' . $image_string . '
		</figure>
	</div>
';

return $output;
