<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Gitem extends WPBakeryShortCodesContainer {
	public function contentAdmin( $atts, $content = null ) {
		/**
		 * @var string @el_class - comes
		 */
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getControls( $this->settings( 'controls' ) );
		$output .= '<div ' . $this->mainHtmlBlockParams( '12', '' ) . '>';
		$output .= $column_controls;
		$output .= '<div ' . $this->containerHtmlBlockParams( '12', '' ) . '>';
		$output .= $this->itemGrid();
		$output .= do_shortcode( shortcode_unautop( $content ) );
		$output .= '</div>';
		if ( isset( $this->settings['params'] ) ) {
			$inner = '';
			foreach ( $this->settings['params'] as $param ) {
				$param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
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
		$output .= '</div>';

		return $output;
	}

	public function mainHtmlBlockParams( $width, $i ) {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

		return 'data-element_type="' . $this->settings['base'] . '" class="' . $this->settings['base'] . '-shortcode ' . $sortable . ' wpb_content_holder vc_shortcodes_container"' . $this->customAdminBlockParams();
	}

	public function itemGrid() {
		$output = '<div class="vc_row">'
		          . '<div class="vc_col-xs-4 vc_col-xs-offset-4"><div class="vc_gitem-add-c-col" data-vc-gitem="add-c" data-vc-position="top"></div></div>'
		          . '</div>'
		          . '<div class="vc_row">'
		          . '<div class="vc_col-xs-4 vc_gitem-add-c-left"><div class="vc_gitem-add-c-col" data-vc-gitem="add-c" data-vc-position="left"></div></div>'
		          . '<div class="vc_col-xs-4 vc_gitem-ab-zone" data-vc-gitem="add-ab"></div>'
		          . '<div class="vc_col-xs-4 vc_gitem-add-c-right"><div class="vc_gitem-add-c-col" data-vc-gitem="add-c"  data-vc-position="right"></div></div>'
		          . '</div>'
		          . '<div class="vc_row">'
		          . '<div class="vc_col-xs-4 vc_col-xs-offset-4 vc_gitem-add-c-bottom"><div class="vc_gitem-add-c-col"  data-vc-gitem="add-c"  data-vc-position="bottom"></div></div>'
		          . '</div>';

		return $output;
	}

	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="vc_gitem-content"';
	}

	/**
	 * Get rendered controls
	 *
	 * @param array $controls
	 *
	 * @return string
	 */
	public function getControls( $controls ) {
		if ( ! is_array( $controls ) || empty( $controls ) ) {
			return '';
		}

		$buttons = array();
		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		foreach ( $controls as $control ) {
			switch ( $control ) {
				case 'add':
					if ( $allAccess ) {
						$buttons[] = '<a class="vc_control-btn vc_control-btn-add" href="#" title="' . __( 'Add to this grid item', 'js_composer' ) . '" data-vc-control="add"><i class="vc_icon"></i></a>';
					}
					break;

				case 'edit':
					if ( $editAccess ) {
						$buttons[] = '<a class="vc_control-btn vc_control-btn-edit" href="#" title="' . __( 'Edit this grid item', 'js_composer' ) . '" data-vc-control="edit"><i class="vc_icon"></i></a>';
					}
					break;

				case 'delete':
					if ( $allAccess ) {
						$buttons[] = '<a class="vc_control-btn vc_control-btn-delete" href="#" title="' . __( 'Delete this grid item ', 'js_composer' ) . '" data-vc-control="delete"><i class="vc_icon"></i></a>';
					}
					break;
			}
		}

		$html = '<div class="vc_controls vc_controls-dark vc_controls-visible">' . implode( ' ', $buttons ) . '</div>';

		return $html;
	}
}
