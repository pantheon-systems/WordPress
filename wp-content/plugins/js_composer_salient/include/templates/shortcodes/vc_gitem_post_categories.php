<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Gitem_Post_Categories
 */

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

?>
{{ post_categories:<?php echo http_build_query( array(
	'atts' => $atts,
) ); ?> }}
