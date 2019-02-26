<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 *
 */
class WPBakeryShortCode_VC_Button2 extends WPBakeryShortCode {
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );

		return '<h4 class="wpb_element_title"><span class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '"></span></h4>';
	}
}
