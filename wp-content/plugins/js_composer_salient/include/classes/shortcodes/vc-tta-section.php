<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_VC_Tta_Section extends WPBakeryShortCode_VC_Tta_Accordion {
	protected $controls_css_settings = 'tc vc_control-container';
	protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
	protected $backened_editor_prepend_controls = false;
	/**
	 * @var WPBakeryShortCode_VC_Tta_Accordion
	 */
	public static $tta_base_shortcode;
	public static $self_count = 0;
	public static $section_info = array();

	public function getFileName() {
		if ( isset( self::$tta_base_shortcode ) && 'vc_tta_pageable' === self::$tta_base_shortcode->getShortcode() ) {
			return 'vc_tta_pageable_section';
		} else {
			return 'vc_tta_section';
		}
	}

	public function containerContentClass() {
		return 'wpb_column_container vc_container_for_children vc_clearfix';
	}

	public function getElementClasses() {
		$classes = array();
		$classes[] = 'vc_tta-panel';
		$isActive = ! vc_is_page_editable() && $this->getTemplateVariable( 'section-is-active' );

		if ( $isActive ) {
			$classes[] = $this->activeClass;
		}

		/**
		 * @since 4.6.2
		 */
		if ( isset( $this->atts['el_class'] ) ) {
			$classes[] = $this->atts['el_class'];
		}

		return implode( ' ', array_filter( $classes ) );
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamContent( $atts, $content ) {
		return wpb_js_remove_wpautop( $content );
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTabId( $atts, $content ) {
		if ( isset( $atts['tab_id'] ) && strlen( $atts['tab_id'] ) > 0 ) {
			return $atts['tab_id'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamTitle( $atts, $content ) {
		if ( isset( $atts['title'] ) && strlen( $atts['title'] ) > 0 ) {
			return $atts['title'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamIcon( $atts, $content ) {
		if ( ! empty( $atts['add_icon'] ) && 'true' === $atts['add_icon'] ) {
			$iconClass = '';
			if ( isset( $atts[ 'i_icon_' . $atts['i_type'] ] ) ) {
				$iconClass = $atts[ 'i_icon_' . $atts['i_type'] ];
			}
			vc_icon_element_fonts_enqueue( $atts['i_type'] );

			return '<i class="vc_tta-icon ' . esc_attr( $iconClass ) . '"></i>';
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamIconLeft( $atts, $content ) {
		if ( 'left' === $atts['i_position'] ) {
			return $this->getParamIcon( $atts, $content );
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamIconRight( $atts, $content ) {
		if ( 'right' === $atts['i_position'] ) {
			return $this->getParamIcon( $atts, $content );
		}

		return null;
	}

	/**
	 * Section param active
	 * @param $atts
	 * @param $content
	 * @return bool|null
	 */
	public function getParamSectionIsActive( $atts, $content ) {
		if ( is_object( self::$tta_base_shortcode ) ) {
			if ( isset( self::$tta_base_shortcode->atts['active_section'] ) && strlen( self::$tta_base_shortcode->atts['active_section'] ) > 0 ) {
				$active = (int) self::$tta_base_shortcode->atts['active_section'];
				if ( $active === self::$self_count ) {
					return true;
				}
			}
		}

		return null;
	}

	public function getParamControlIconPosition( $atts, $content ) {
		if ( is_object( self::$tta_base_shortcode ) ) {
			if (
				isset( self::$tta_base_shortcode->atts['c_icon'] ) && strlen( self::$tta_base_shortcode->atts['c_icon'] ) > 0 &&
				isset( self::$tta_base_shortcode->atts['c_position'] ) && strlen( self::$tta_base_shortcode->atts['c_position'] ) > 0
			) {
				$c_position = self::$tta_base_shortcode->atts['c_position'];

				return 'vc_tta-controls-icon-position-' . $c_position;
			}
		}

		return null;
	}

	public function getParamControlIcon( $atts, $content ) {
		if ( is_object( self::$tta_base_shortcode ) ) {
			if ( isset( self::$tta_base_shortcode->atts['c_icon'] ) && strlen( self::$tta_base_shortcode->atts['c_icon'] ) > 0 ) {
				$c_icon = self::$tta_base_shortcode->atts['c_icon'];

				return '<i class="vc_tta-controls-icon vc_tta-controls-icon-' . $c_icon . '"></i>';
			}
		}

		return null;
	}

	public function getParamHeading( $atts, $content ) {
		$isPageEditable = vc_is_page_editable();

		$h4attributes = array();
		$h4classes = array(
			'vc_tta-panel-title',
		);
		if ( $isPageEditable ) {
			$h4attributes[] = 'data-vc-tta-controls-icon-position=""';
		} else {
			$controlIconPosition = $this->getTemplateVariable( 'control-icon-position' );
			if ( $controlIconPosition ) {
				$h4classes[] = $controlIconPosition;
			}
		}
		$h4attributes[] = 'class="' . implode( ' ', $h4classes ) . '"';

		$output = '<h4 ' . implode( ' ', $h4attributes ) . '>'; // close h4

		if ( $isPageEditable ) {
			$output .= '<a href="javascript:;" data-vc-target=""';
			$output .= ' data-vc-tta-controls-icon-wrapper';
			$output .= ' data-vc-use-cache="false"';
		} else {
			$output .= '<a href="#' . esc_attr( $this->getTemplateVariable( 'tab_id' ) ) . '"';
		}

		$output .= ' data-vc-accordion';

		$output .= ' data-vc-container=".vc_tta-container">';
		$output .= $this->getTemplateVariable( 'icon-left' );
		$output .= '<span class="vc_tta-title-text">'
		           . $this->getTemplateVariable( 'title' )
		           . '</span>';
		$output .= $this->getTemplateVariable( 'icon-right' );
		if ( ! $isPageEditable ) {
			$output .= $this->getTemplateVariable( 'control-icon' );
		}

		$output .= '</a>';
		$output .= '</h4>'; // close h4 fix #2229

		return $output;
	}

	/**
	 * Get basic heading
	 *
	 * These are used in Pageable element inside content and are hidden from view
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamBasicHeading( $atts, $content ) {
		$isPageEditable = vc_is_page_editable();

		if ( $isPageEditable ) {
			$attributes = array(
				'href' => 'javascript:;',
				'data-vc-container' => '.vc_tta-container',
				'data-vc-accordion' => '',
				'data-vc-target' => '',
				'data-vc-tta-controls-icon-wrapper' => '',
				'data-vc-use-cache' => 'false',
			);
		} else {
			$attributes = array(
				'data-vc-container' => '.vc_tta-container',
				'data-vc-accordion' => '',
				'data-vc-target' => esc_attr( '#' . $this->getTemplateVariable( 'tab_id' ) ),
			);
		}

		$output = '
			<span class="vc_tta-panel-title">
				<a ' . vc_convert_atts_to_string( $attributes ) . '></a>
			</span>
		';

		return $output;
	}
	/**
	 * Check is allowed to add another element inside current element.
	 *
	 * @since 4.8
	 *
	 * @return bool
	 */
	public function getAddAllowed() {
		return  vc_user_access()
			->part( 'shortcodes' )
			->checkStateAny( true, 'custom', null )->get();
	}
}
