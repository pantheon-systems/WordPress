<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $key
 * @var $el_class
 * @var $align
 * @var $label
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Post_Meta
 */
$key = $el_class = $align = $label = '';
$label_html = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css_class = 'vc_gitem-post-meta-field-' . $key
	. ( strlen( $el_class ) ? ' ' . $el_class : '' )
	. ( strlen( $align ) ? ' vc_gitem-align-' . $align : '' );
if ( strlen( $label ) ) {
	$label_html = '<span class="vc_gitem-post-meta-label">' . esc_html( $label ) . '</span>';
}
if ( strlen( $key ) ) :  ?>
	<div class="<?php echo esc_attr( $css_class ) ?>"><?php echo $label_html ?> {{ post_meta_value:<?php echo esc_attr( $key ) ?> }}
	</div>
<?php endif ?>
