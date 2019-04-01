<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_VC_Tta_Tabs extends WPBakeryShortCode_VC_Tta_Accordion {

	public $layout = 'tabs';

	public function enqueueTtaScript() {
		wp_register_script( 'vc_tabs_script', vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ), array( 'vc_accordion_script' ), WPB_VC_VERSION, true );
		parent::enqueueTtaScript();
		wp_enqueue_script( 'vc_tabs_script' );
	}

	public function getWrapperAttributes() {
		$attributes = array();
		$attributes[] = 'class="' . esc_attr( $this->getTtaContainerClasses() ) . '"';
		$attributes[] = 'data-vc-action="collapse"';

		$autoplay = $this->atts['autoplay'];
		if ( $autoplay && 'none' !== $autoplay && intval( $autoplay ) > 0 ) {
			$attributes[] = 'data-vc-tta-autoplay="' . esc_attr( json_encode( array(
					'delay' => intval( $autoplay ) * 1000,
				) ) ) . '"';
		}
		if ( ! empty( $this->atts['el_id'] ) ) {
			$attributes[] = 'id="' . esc_attr( $this->atts['el_id'] ) . '"';
		}

		return implode( ' ', $attributes );
	}

	public function getTtaGeneralClasses() {
		$classes = parent::getTtaGeneralClasses();

		if ( ! empty( $this->atts['no_fill_content_area'] ) ) {
			$classes .= ' vc_tta-o-no-fill';
		}

		if ( isset( $this->atts['tab_position'] ) ) {
			$classes .= ' ' . $this->getTemplateVariable( 'tab_position' );
		}

		$classes .= ' ' . $this->getParamAlignment( $this->atts, $this->content );

		return $classes;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTabPosition( $atts, $content ) {
		if ( isset( $atts['tab_position'] ) && strlen( $atts['tab_position'] ) > 0 ) {
			return 'vc_tta-tabs-position-' . $atts['tab_position'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTabsListTop( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'top' !== $atts['tab_position'] ) {
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
	public function getParamTabsListBottom( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'bottom' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamTabsList( $atts, $content );
	}

	/**
	 * Pagination is on top only if tabs are at bottom
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamPaginationTop( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'bottom' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamPaginationList( $atts, $content );
	}

	/**
	 * Pagination is at bottom only if tabs are on top
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamPaginationBottom( $atts, $content ) {
		if ( empty( $atts['tab_position'] ) || 'top' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamPaginationList( $atts, $content );
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	function constructIcon( $atts ) {
		vc_icon_element_fonts_enqueue( $atts['i_type'] );

		$class = 'vc_tta-icon';

		if ( isset( $atts[ 'i_icon_' . $atts['i_type'] ] ) ) {
			$class .= ' ' . $atts[ 'i_icon_' . $atts['i_type'] ];
		} else {
			$class .= ' fa fa-adjust';
		}

		return '<i class="' . $class . '"></i>';
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamTabsList( $atts, $content ) {
		$isPageEditabe = vc_is_page_editable();
		$html = array();
		$html[] = '<div class="vc_tta-tabs-container">';
		$html[] = '<ul class="vc_tta-tabs-list">';
		if ( ! $isPageEditabe ) {
			$active_section = $this->getActiveSection( $atts, false );

			foreach ( WPBakeryShortCode_VC_Tta_Section::$section_info as $nth => $section ) {
				$classes = array( 'vc_tta-tab' );
				if ( ( $nth + 1 ) === $active_section ) {
					$classes[] = $this->activeClass;
				}

				$title = '<span class="vc_tta-title-text">' . $section['title'] . '</span>';
				if ( 'true' === $section['add_icon'] ) {
					$icon_html = $this->constructIcon( $section );
					if ( 'left' === $section['i_position'] ) {
						$title = $icon_html . $title;
					} else {
						$title = $title . $icon_html;
					}
				}
				$a_html = '<a href="#' . $section['tab_id'] . '" data-vc-tabs data-vc-container=".vc_tta">' . $title . '</a>';
				$html[] = '<li class="' . implode( ' ', $classes ) . '" data-vc-tab>' . $a_html . '</li>';
			}
		}

		$html[] = '</ul>';
		$html[] = '</div>';

		return implode( '', apply_filters( 'vc-tta-get-params-tabs-list', $html, $atts, $content, $this ) );
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamAlignment( $atts, $content ) {
		if ( isset( $atts['alignment'] ) && strlen( $atts['alignment'] ) > 0 ) {
			return 'vc_tta-controls-align-' . $atts['alignment'];
		}

		return null;
	}
}
