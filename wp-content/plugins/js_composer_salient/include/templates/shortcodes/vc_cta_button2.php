<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $h2
 * @var $h4
 * @var $position
 * @var $el_width
 * @var $style
 * @var $txt_align
 * @var $accent_color
 * @var $link
 * @var $title
 * @var $color
 * @var $size
 * @var $btn_style
 * @var $el_class
 * @var $css_animation
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Cta_button2
 */
$h2 = $h4 = $position = $el_class = $el_width = $size = $txt_align = $accent_color = $link = $title = $color = $size = $btn_style = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_call_to_action wpb_content_element';

$link = ( '||' === $link ) ? '' : $link;

$class .= ( '' !== $position ) ? ' vc_cta_btn_pos_' . $position : '';
$class .= ( '' !== $el_width ) ? ' vc_el_width_' . $el_width : '';
$class .= ( '' !== $color ) ? ' vc_cta_' . $color : '';
$class .= ( '' !== $style ) ? ' vc_cta_' . $style : '';
$class .= ( '' !== $txt_align ) ? ' vc_txt_align_' . $txt_align : '';

$inline_css = ( '' !== $accent_color ) ? ' style="' . vc_get_css_color( 'background-color', $accent_color ) . vc_get_css_color( 'border-color', $accent_color ) . '"' : '';

$class .= $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );
$css_class .= $this->getCSSAnimation( $css_animation );
?>
<div<?php echo $inline_css; ?> class="<?php echo esc_attr( trim( $css_class ) ); ?>">
	<?php if ( '' !== $link && 'bottom' !== $position ) {
		echo do_shortcode( '[vc_button2 align="' . $position . '" link="' . $link . '" title="' . $title . '" color="' . $color . '" size="' . $size . '" style="' . $btn_style . '" el_class="vc_cta_btn"]' );
} ?>
	<?php if ( '' !== $h2 || '' !== $h4 ) :  ?>
		<hgroup>
			<?php if ( '' !== $h2 ) :  ?><h2 class="wpb_heading"><?php echo $h2; ?></h2><?php endif ?>
			<?php if ( '' !== $h4 ) :  ?><h4 class="wpb_heading"><?php echo $h4; ?></h4><?php endif ?>
		</hgroup>
	<?php endif ?>
	<?php echo wpb_js_remove_wpautop( $content, true ); ?>
	<?php if ( '' !== $link && 'bottom' === $position ) {
		echo do_shortcode( '[vc_button2 link="' . $link . '" title="' . $title . '" color="' . $color . '" size="' . $size . '" style="' . $btn_style . '" el_class="vc_cta_btn"]' );
} ?>
</div>
