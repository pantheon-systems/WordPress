<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-gitem.php' );

class WPBakeryShortCode_VC_Gitem_Animated_Block extends WPBakeryShortCode_VC_Gitem {
	protected static $animations = array();

	public function itemGrid() {
		$output = '';
		$output .= '<div class="vc_gitem-animated-block-content-controls">'
		           . '<ul class="vc_gitem-tabs vc_clearfix" data-vc-gitem-animated-block="tabs">'
		           . '</ul>'
		           . '</div>';
		$output .= ''
		           . '<div class="vc_gitem-zone-tab vc_clearfix" data-vc-gitem-animated-block="add-a"></div>'
		           . '<div class="vc_gitem-zone-tab vc_clearfix" data-vc-gitem-animated-block="add-b"></div>';

		return $output;
	}

	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="vc_gitem-animated-block-content"';
	}

	public static function animations() {
		return array(
			__( 'Single block (no animation)', 'js_composer' ) => '',
			__( 'Double block (no animation)', 'js_composer' ) => 'none',
			__( 'Fade in', 'js_composer' ) => 'fadeIn',
			__( 'Scale in', 'js_composer' ) => 'scaleIn',
			__( 'Scale in with rotation', 'js_composer' ) => 'scaleRotateIn',
			__( 'Blur out', 'js_composer' ) => 'blurOut',
			__( 'Blur scale out', 'js_composer' ) => 'blurScaleOut',
			__( 'Slide in from left', 'js_composer' ) => 'slideInRight',
			__( 'Slide in from right', 'js_composer' ) => 'slideInLeft',
			__( 'Slide bottom', 'js_composer' ) => 'slideBottom',
			__( 'Slide top', 'js_composer' ) => 'slideTop',
			__( 'Vertical flip in with fade', 'js_composer' ) => 'flipFadeIn',
			__( 'Horizontal flip in with fade', 'js_composer' ) => 'flipHorizontalFadeIn',
			__( 'Go top', 'js_composer' ) => 'goTop20',
			__( 'Go bottom', 'js_composer' ) => 'goBottom20',
		);
	}
}
