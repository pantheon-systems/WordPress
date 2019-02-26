<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$el_class = $image = $img_size = $img_link = $img_link_target = $img_link_large = $title = $alignment = $css_animation = $css = '';
/** @var $this WPBakeryShortCode_VC_Single_image */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$default_src = vc_asset_url( 'vc/no_image.png' );
$style = ( '' !== $style ) ? $style : '';
$border_color = ( '' !== $border_color ) ? ' vc_box_border_' . $border_color : '';

$img_id = preg_replace( '/[^\d]/', '', $image );

switch ( $source ) {
	case 'media_library':
		$img = wpb_getImageBySize( array(
			'attach_id' => $img_id,
			'thumb_size' => $img_size,
			'class' => 'vc_single_image-img',
		) );

		break;

	case 'external_link':
		$dimensions = vcExtractDimensions( $img_size );
		$hwstring = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';

		$custom_src = $custom_src ? esc_attr( $custom_src ) : $default_src;

		$img = array(
			'thumbnail' => '<img class="vc_single_image-img" ' . $hwstring . ' src="' . $custom_src . '" />',
		);
		break;

	default:
		$img = false;
}

if ( ! $img ) {
	$img['thumbnail'] = '<img class="vc_single_image-img" src="' . $default_src . '" />';
}

$wrapperClass = 'vc_single_image-wrapper ' . $style . ' ' . $border_color;
$link = vc_gitem_create_link( $atts, $wrapperClass );

$image_string = ! empty( $link ) ? '<' . $link . '>' . $img['thumbnail'] . '</a>' : '<div class="' . $wrapperClass . '"> ' . $img['thumbnail'] . ' </div>';

$class_to_filter = 'wpb_single_image wpb_content_element vc_align_' . $alignment . ' ' . $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$output = '
	<div class="' . esc_attr( $css_class ) . '">
		' . wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_singleimage_heading' ) ) . '
		<figure class="wpb_wrapper vc_figure">
			' . $image_string . '
		</figure>
	</div>
';

echo $output;
