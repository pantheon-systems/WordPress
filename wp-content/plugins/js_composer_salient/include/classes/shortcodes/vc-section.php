<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder section
 *
 * @package WPBakeryPageBuilder
 *
 */
class WPBakeryShortCode_VC_Section extends WPBakeryShortCodesContainer {
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="vc_section_container vc_container_for_children"';
	}

	/**
	 * @param $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );
		$this->shortcodeScripts();
	}

	protected function shortcodeScripts() {
		wp_register_script( 'vc_jquery_skrollr_js', vc_asset_url( 'lib/bower/skrollr/dist/skrollr.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_register_script( 'vc_youtube_iframe_api_js', 'https://www.youtube.com/iframe_api', array(), WPB_VC_VERSION, true );
	}

	public function cssAdminClass() {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable' : ' ' . $this->nonDraggableClass );

		return 'wpb_' . $this->settings['base'] . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
	}

	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		$controls_start = '<div class="vc_controls vc_controls-visible controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';

		$output = '<div class="vc_controls vc_controls-row controls_row vc_clearfix">';
		$controls_end = '</div>';
		//Create columns
		$controls_move = ' <a class="vc_control column_move vc_column-move" href="#" title="' . __( 'Drag row to reorder', 'js_composer' ) . '" data-vc-control="move"><i class="vc-composer-icon vc-c-icon-dragndrop"></i></a>';
		$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();
		if ( ! $moveAccess ) {
			$controls_move = '';
		}
		$controls_add = ' <a class="vc_control column_add vc_column-add" href="#" title="' . __( 'Add column', 'js_composer' ) . '" data-vc-control="add"><i class="vc-composer-icon vc-c-icon-add"></i></a>';
		$controls_delete = '<a class="vc_control column_delete vc_column-delete" href="#" title="' . __( 'Delete this row', 'js_composer' ) . '" data-vc-control="delete"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a>';
		$controls_edit = ' <a class="vc_control column_edit vc_column-edit" href="#" title="' . __( 'Edit this row', 'js_composer' ) . '" data-vc-control="edit"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></a>';
		$controls_clone = ' <a class="vc_control column_clone vc_column-clone" href="#" title="' . __( 'Clone this row', 'js_composer' ) . '" data-vc-control="clone"><i class="vc-composer-icon vc-c-icon-content_copy"></i></a>';
		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		$row_edit_clone_delete = '<span class="vc_row_edit_clone_delete">';

		if ( 'add' === $controls ) {
			return $controls_start . $controls_add . $controls_end;
		}
		if ( $allAccess ) {
			$row_edit_clone_delete .= $controls_delete . $controls_clone . $controls_edit;
		} elseif ( $editAccess ) {
			$row_edit_clone_delete .= $controls_edit;
		}
		$row_edit_clone_delete .= '</span>';

		if ( $allAccess ) {
			$output .= $controls_move . $controls_add . $row_edit_clone_delete . $controls_end;
		} elseif ( $editAccess ) {
			$output .= $row_edit_clone_delete . $controls_end;
		} else {
			$output .= $row_edit_clone_delete . $controls_end;
		}

		return $output;
	}

	public function contentAdmin( $atts, $content = null ) {
		$width = '';
		$atts = shortcode_atts( $this->predefined_atts, $atts );

		$output = '';

		$column_controls = $this->getColumnControls();

		$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $this->cssAdminClass() . '">';
		$output .= str_replace( '%column_size%', 1, $column_controls );
		$output .= '<div class="wpb_element_wrapper">';
		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			$markup = $this->settings['custom_markup'];
			$output .= $this->customMarkup( $markup );
		} else {
			// $output .= $this->outputTitle( $this->settings['name'] );
			$output .= '<div ' . $this->containerHtmlBlockParams( $width, 1 ) . '>';
			$output .= do_shortcode( shortcode_unautop( $content ) );
			$output .= '</div>';
			// $output .= $this->paramsHtmlHolders( $atts );
		}
		if ( isset( $this->settings['params'] ) ) {
			$inner = '';
			foreach ( $this->settings['params'] as $param ) {
				if ( ! isset( $param['param_name'] ) ) {
					continue;
				}
				$param_value = isset( $atts[ $param['param_name'] ] ) ? $atts[ $param['param_name'] ] : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array
					reset( $param_value );
					$first_key = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$inner .= $this->singleParamHtmlHolder( $param, $param_value );
			}
			$output .= $inner;
		}
		$output .= '</div>';
		if ( $this->backened_editor_prepend_controls ) {
			$output .= $this->getColumnControls( 'add', 'vc_section-bottom-controls bottom-controls' );
		}
		$output .= '</div>';

		return $output;
	}
}
