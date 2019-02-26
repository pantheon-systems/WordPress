<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css
 * @var $position
 * @var $bgimage
 * @var $height
 * @var $link
 * @var $url
 * @var $height_mode
 * @var $featured_image
 * @var $img_size
 * @var $render
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Zone
 */
$el_class = $css = $position = $bgimage = $height = $link = $url = $height_mode = $featured_image = $img_size = $render = $rel = '';

$css_style = $css_style_mini = '';
$image_block = $image = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ( 'no' === $render ) {
	echo '';

	return;
}
// Here we check content. If it doesn't contain any useful content, not to render all this staff.
if ( 'yes' !== $featured_image && empty( $css ) && empty( $el_class ) && empty( $height ) && ! vc_gitem_has_content( $content ) ) {
	return;
}
$css_class = 'vc_gitem-zone'
	. ( strlen( $this->zone_name ) ? ' vc_gitem-zone-' . $this->zone_name : '' );
$css_class_mini = 'vc_gitem-zone-mini';
// Autoheight Mode
// http://jsfiddle.net/tL2pgtyb/4/ {{
// Set css classes for shortcode main html element wrapper and background block
$css_class .= vc_shortcode_custom_css_class( $css, ' ' )
	. ( strlen( $el_class ) ? ' ' . $el_class : '' );
preg_match( '/background(\-image)?\s*\:\s*[^\s]*?\s*url\(\'?([^\)]+)\'?\)/', $css, $img_matches );
$background_image_css_editor = isset( $img_matches[2] ) ? $img_matches[2] : false;
if ( 'custom' === $height_mode ) {
	if ( strlen( $height ) > 0 ) {
		if ( preg_match( '/^\d+$/', $height ) ) {
			$height .= 'px';
		}
		$css_style .= 'height: ' . $height . ';';
	}
} elseif ( 'original' !== $height_mode ) {
	$css_class .= ' vc-gitem-zone-height-mode-auto'
		. ( strlen( $height_mode ) > 0 ? ' vc-gitem-zone-height-mode-auto-' . $height_mode : '' );
}
if ( 'yes' === $featured_image ) {
	$css_style .= '{{ post_image_background_image_css' . ':' . $img_size . ' }}';
	$image = '<img src="{{ post_image_url' . ( false !== $background_image_css_editor ? ':' . rawurlencode( $background_image_css_editor ) . '' : ':' ) . ':' . $img_size . ' }}" class="vc_gitem-zone-img" alt="{{ post_image_alt }}">';
} elseif ( false !== $background_image_css_editor ) {
	$image = '<img src="' . esc_attr( $background_image_css_editor ) . '" class="vc_gitem-zone-img" alt="{{ post_image_alt }}">';
}
if ( strlen( $link ) > 0 && 'none' !== $link ) {
	$css_class .= ' vc_gitem-is-link';
	if ( 'custom' === $link && ! empty( $url ) ) {
		$link_s = vc_build_link( $url );
		/*
		$attr = ' data-vc-link="' . esc_attr( $link_s['url'] ) . '"'
		           . ' data-vc-target="' . esc_attr( trim($link_s['target']) ) . '"'
		           . ' title="' . esc_attr( $link_s['title'] ) . '"';
		*/
		$rel = '';
		if ( ! empty( $link_s['rel'] ) ) {
			$rel = ' rel="' . esc_attr( trim( $link_s['rel'] ) ) . '"';
		}
		$image_block = '<a href="' . esc_attr( $link_s['url'] ) . '" title="'
			. esc_attr( $link_s['title'] ) . '" target="' . esc_attr( trim( $link_s['target'] ) )
			. '" class="vc_gitem-link vc-zone-link"' . $rel . '></a>';
	} elseif ( 'post_link' === $link ) {
		$image_block = '<a href="{{ post_link_url }}" title="{{ post_title }}" class="vc_gitem-link vc-zone-link"></a>';
	} elseif ( 'post_author' === $link ) {
		$image_block = '<a href="{{ post_author_href }}" title="{{ post_author }}" class="vc_gitem-link vc-zone-link"></a>';
	} elseif ( 'image' === $link ) {
		$image_block = '<a href="{{ post_image_url }}" title="{{ post_title }}" class="vc_gitem-link vc-zone-link"></a>';
	} elseif ( 'image_lightbox' === $link ) {
		if ( ! isset( $this->prettyphoto_rel ) ) {
			$this->prettyphoto_rel = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']"';
		}
		$image_block .= '<a href="{{ post_image_url }}" title="{{ post_title }}" ' . $this->prettyphoto_rel . ' data-vc-gitem-zone="prettyphotoLink" class="vc_gitem-link prettyphoto vc-zone-link vc-prettyphoto-link"></a>';
	}
	$image_block = apply_filters( 'vc_gitem_zone_image_block_link', $image_block, $link, 'vc_gitem-link vc-zone-link' );
}
?>
<div class="<?php echo esc_attr( $css_class ) ?>"<?php
echo( empty( $css_style ) ? '' : ' style="' . esc_attr( $css_style ) . '"' )
?>>
	<?php echo $image_block ?>
	<?php echo $image ?>
	<div class="<?php echo esc_attr( $css_class_mini ) ?>"<?php echo( empty( $css_style_mini ) ? '' : ' style="' . esc_attr( $css_style_mini ) . '"' ) ?>>
		<?php echo do_shortcode( $content ) ?>
	</div>
</div>
