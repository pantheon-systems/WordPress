<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Tta_Accordion extends WPBakeryShortCodesContainer {
	protected $controls_css_settings = 'out-tc vc_controls-content-widget';
	protected $controls_list = array(
		'add',
		'edit',
		'clone',
		'delete',
	);
	protected $template_vars = array();

	public $layout = 'accordion';
	protected $content;

	public $activeClass = 'vc_active';
	/**
	 * @var WPBakeryShortCode_VC_Tta_Section
	 */
	protected $sectionClass;

	public $nonDraggableClass = 'vc-non-draggable-container';

	public function getFileName() {
		return 'vc_tta_global';
	}

	public function containerContentClass() {
		return 'vc_container_for_children vc_clearfix';
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 */
	public function resetVariables( $atts, $content ) {
		$this->atts = $atts;
		$this->content = $content;
		$this->template_vars = array();
	}

	public function setGlobalTtaInfo() {
		$sectionClass = visual_composer()->getShortCode( 'vc_tta_section' )->shortcodeClass();
		$this->sectionClass = $sectionClass;

		/** @var $sectionClass WPBakeryShortCode_VC_Tta_Section */
		if ( is_object( $sectionClass ) ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Section' );
			WPBakeryShortCode_VC_Tta_Section::$tta_base_shortcode = $this;
			WPBakeryShortCode_VC_Tta_Section::$self_count = 0;
			WPBakeryShortCode_VC_Tta_Section::$section_info = array();

			return true;
		}

		return false;
	}

	/**
	 * Override default getColumnControls to make it "simple"(blue), as single element has
	 *
	 * @param string $controls
	 * @param string $extended_css
	 *
	 * @return string
	 */
	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		// we don't need containers bottom-controls for tabs
		if ( 'bottom-controls' === $extended_css ) {
			return '';
		}
		$column_controls = $this->getColumnControlsModular();

		return $output = $column_controls;
	}

	public function getTtaContainerClasses() {
		$classes = array();
		$classes[] = 'vc_tta-container';

		return implode( ' ', apply_filters( 'vc_tta_container_classes', array_filter( $classes ), $this->getAtts() ) );
	}

	public function getTtaGeneralClasses() {
		$classes = array();
		$classes[] = 'vc_general';
		$classes[] = 'vc_tta';
		$classes[] = 'vc_tta-' . $this->layout;
		$classes[] = $this->getTemplateVariable( 'color' );
		$classes[] = $this->getTemplateVariable( 'style' );
		$classes[] = $this->getTemplateVariable( 'shape' );
		$classes[] = $this->getTemplateVariable( 'spacing' );
		$classes[] = $this->getTemplateVariable( 'gap' );
		$classes[] = $this->getTemplateVariable( 'c_align' );
		$classes[] = $this->getTemplateVariable( 'no_fill' );
		if ( isset( $this->atts['collapsible_all'] ) && 'true' === $this->atts['collapsible_all'] ) {
			$classes[] = 'vc_tta-o-all-clickable';
		}

		$pagination = isset( $this->atts['pagination_style'] ) ? trim( $this->atts['pagination_style'] ) : false;
		if ( $pagination && 'none' !== $pagination && strlen( $pagination ) > 0 ) {
			$classes[] = 'vc_tta-has-pagination';
		}

		/**
		 * @since 4.6.2
		 */
		if ( isset( $this->atts['el_class'] ) ) {
			$classes[] = $this->getExtraClass( $this->atts['el_class'] );
		}

		return implode( ' ', apply_filters( 'vc_tta_accordion_general_classes', array_filter( $classes ), $this->getAtts() ) );
	}

	public function getTtaPaginationClasses() {
		$classes = array();
		$classes[] = 'vc_general';
		$classes[] = 'vc_pagination';

		if ( isset( $this->atts['pagination_style'] ) && strlen( $this->atts['pagination_style'] ) > 0 ) {
			$chunks = explode( '-', $this->atts['pagination_style'] );
			$classes[] = 'vc_pagination-style-' . $chunks[0];
			$classes[] = 'vc_pagination-shape-' . $chunks[1];
		}

		if ( isset( $this->atts['pagination_color'] ) && strlen( $this->atts['pagination_color'] ) > 0 ) {
			$classes[] = 'vc_pagination-color-' . $this->atts['pagination_color'];
		}

		return implode( ' ', $classes );
	}

	public function getWrapperAttributes() {
		$attributes = array();
		$attributes[] = 'class="' . esc_attr( $this->getTtaContainerClasses() ) . '"';
		$attributes[] = 'data-vc-action="' . ( 'true' === $this->atts['collapsible_all'] ? 'collapseAll' : 'collapse' ) . '"';

		$autoplay = isset( $this->atts['autoplay'] ) ? trim( $this->atts['autoplay'] ) : false;
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

	public function getTemplateVariable( $string ) {
		if ( isset( $this->template_vars[ $string ] ) ) {
			return $this->template_vars[ $string ];
		} elseif ( method_exists( $this, 'getParam' . vc_studly( $string ) ) ) {
			$this->template_vars[ $string ] = $this->{'getParam' . vc_studly( $string )}( $this->atts, $this->content );

			return $this->template_vars[ $string ];
		}

		return '';
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamColor( $atts, $content ) {
		if ( isset( $atts['color'] ) && strlen( $atts['color'] ) > 0 ) {
			return 'vc_tta-color-' . $atts['color'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamStyle( $atts, $content ) {
		if ( isset( $atts['style'] ) && strlen( $atts['style'] ) > 0 ) {
			return 'vc_tta-style-' . $atts['style'];
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
			return '<h2>' . $atts['title'] . '</h2>';
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamContent( $atts, $content ) {
		$panelsContent = wpb_js_remove_wpautop( $content );
		if ( isset( $atts['c_icon'] ) && strlen( $atts['c_icon'] ) > 0 ) {
			$isPageEditable = vc_is_page_editable();
			if ( ! $isPageEditable ) {
				$panelsContent = str_replace( '{{{ control-icon }}}', '<i class="vc_tta-controls-icon vc_tta-controls-icon-' . $atts['c_icon'] . '"></i>', $panelsContent );
			} else {
				$panelsContent = str_replace( '{{{ control-icon }}}', '<i class="vc_tta-controls-icon" data-vc-tta-controls-icon="' . $atts['c_icon'] . '"></i>', $panelsContent );
			}
		} else {
			$panelsContent = str_replace( '{{{ control-icon }}}', '', $panelsContent );
		}

		return $panelsContent;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamShape( $atts, $content ) {
		if ( isset( $atts['shape'] ) && strlen( $atts['shape'] ) > 0 ) {
			return 'vc_tta-shape-' . $atts['shape'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamSpacing( $atts, $content ) {
		if ( isset( $atts['spacing'] ) && strlen( $atts['spacing'] ) > 0 ) {
			return 'vc_tta-spacing-' . $atts['spacing'];
		}

		// In case if no spacing set we need to append extra class
		return 'vc_tta-o-shape-group';
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamGap( $atts, $content ) {
		if ( isset( $atts['gap'] ) && strlen( $atts['gap'] ) > 0 ) {
			return 'vc_tta-gap-' . $atts['gap'];
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamNoFill( $atts, $content ) {
		if ( isset( $atts['no_fill'] ) && 'true' === $atts['no_fill'] ) {
			return 'vc_tta-o-no-fill';
		}

		return null;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string|null
	 */
	public function getParamCAlign( $atts, $content ) {
		if ( isset( $atts['c_align'] ) && strlen( $atts['c_align'] ) > 0 ) {
			return 'vc_tta-controls-align-' . $atts['c_align'];
		}

		return null;
	}

	/**
	 * Accordion doesn't have pagination
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return null
	 */
	public function getParamPaginationTop( $atts, $content ) {
		return null;
	}

	/**
	 * Accordion doesn't have pagination
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return null
	 */
	public function getParamPaginationBottom( $atts, $content ) {
		return null;
	}

	/**
	 * Get currently active section (from $atts)
	 *
	 * @param $atts
	 * @param bool $strict_bounds If true, check for min/max bounds
	 *
	 * @return int nth position (one-based) of active section
	 */
	function getActiveSection( $atts, $strict_bounds = false ) {
		$active_section = intval( $atts['active_section'] );

		if ( $strict_bounds ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Section' );
			if ( $active_section < 1 ) {
				$active_section = 1;
			} elseif ( $active_section > WPBakeryShortCode_VC_Tta_Section::$self_count ) {
				$active_section = WPBakeryShortCode_VC_Tta_Section::$self_count;
			}
		}

		return $active_section;
	}

	/**
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamPaginationList( $atts, $content ) {
		if ( empty( $atts['pagination_style'] ) ) {
			return null;
		}
		$isPageEditabe = vc_is_page_editable();

		$sectionClass = $this->sectionClass;

		$html = array();
		$html[] = '<ul class="' . $this->getTtaPaginationClasses() . '">';

		if ( ! $isPageEditabe ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Section' );
			foreach ( WPBakeryShortCode_VC_Tta_Section::$section_info as $nth => $section ) {
				$active_section = $this->getActiveSection( $atts, false );

				$classes = array( 'vc_pagination-item' );
				if ( ( $nth + 1 ) === $active_section ) {
					$classes[] = $this->activeClass;
				}

				$a_html = '<a href="#' . $section['tab_id'] . '" class="vc_pagination-trigger" data-vc-tabs data-vc-container=".vc_tta"></a>';
				$html[] = '<li class="' . implode( ' ', $classes ) . '" data-vc-tab>' . $a_html . '</li>';
			}
		}

		$html[] = '</ul>';

		return implode( '', $html );
	}

	public function enqueueTtaStyles() {
		wp_register_style( 'vc_tta_style', vc_asset_url( 'css/js_composer_tta.min.css' ), false, WPB_VC_VERSION );
		wp_enqueue_style( 'vc_tta_style' );
	}

	public function enqueueTtaScript() {
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc_accordion/vc-accordion.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_tta_autoplay_script', vc_asset_url( 'lib/vc-tta-autoplay/vc-tta-autoplay.min.js' ), array( 'vc_accordion_script' ), WPB_VC_VERSION, true );

		wp_enqueue_script( 'vc_accordion_script' );
		if ( ! vc_is_page_editable() ) {
			wp_enqueue_script( 'vc_tta_autoplay_script' );
		}
	}

	/**
	 * Override default outputTitle (also Icon). To remove anything, also Icon.
	 *
	 * @param $title - just for strict standards
	 *
	 * @return string
	 */
	protected function outputTitle( $title ) {
		return '';
	}

	/**
	 * Check is allowed to add another element inside current element.
	 *
	 * @since 4.8
	 *
	 * @return bool
	 */
	public function getAddAllowed() {
		return vc_user_access_check_shortcode_all( 'vc_tta_section' );
	}
}
