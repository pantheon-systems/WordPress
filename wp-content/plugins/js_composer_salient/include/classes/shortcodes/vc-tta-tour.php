<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_VC_Tta_Tour extends WPBakeryShortCode_VC_Tta_Tabs {

	public $layout = 'tabs';

	public function getTtaGeneralClasses() {
		$classes = parent::getTtaGeneralClasses();

		if ( isset( $this->atts['controls_size'] ) ) {
			$classes .= ' ' . $this->getTemplateVariable( 'controls_size' );
		}

		return $classes;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamControlsSize( $atts, $content ) {
		if ( isset( $atts['controls_size'] ) && strlen( $atts['controls_size'] ) > 0 ) {
			return 'vc_tta-controls-size-' . $atts['controls_size'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTabsListLeft( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'left' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamTabsList( $atts, $content );
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTabsListRight( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'right' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamTabsList( $atts, $content );
	}

	/**
	 * Never on top
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamPaginationTop( $atts, $content ) {
		return null;
	}

	/**
	 * Always on bottom
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamPaginationBottom( $atts, $content ) {
		return $this->getParamPaginationList( $atts, $content );
	}
}
