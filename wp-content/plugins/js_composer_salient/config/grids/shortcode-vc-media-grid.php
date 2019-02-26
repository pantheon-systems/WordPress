<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( 'class-vc-grids-common.php' );
$mediaGridParams = VcGridsCommon::getMediaCommonAtts();

return array(
	'name' => __( 'Media Grid', 'js_composer' ),
	'base' => 'vc_media_grid',
	'icon' => 'vc_icon-vc-media-grid',
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Media grid from Media Library', 'js_composer' ),
	'params' => $mediaGridParams,
);
