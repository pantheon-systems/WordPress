<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css
 * @var $render
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Zone
 */
$el_class = $css = $render = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

extract( $atts );

if ( 'no' === $render ) {
	echo '';

	return;
}
$css_class = 'vc_gitem-zone'
	. ( strlen( $this->zone_name ) ? ' vc_gitem-zone-' . $this->zone_name : '' )
	. $this->getExtraClass( $el_class );

$css_class_mini = 'vc_gitem-zone-mini';
$css_class .= vc_shortcode_custom_css_class( $css, ' ' );
?>
<div class="<?php echo esc_attr( $css_class ) ?>">
	<div class="<?php echo esc_attr( $css_class_mini ) ?>">
		<?php echo do_shortcode( $content ) ?>
	</div>
</div>
