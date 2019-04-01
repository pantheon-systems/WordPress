<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-tab.php' );

class WPBakeryShortCode_VC_Accordion_tab extends WPBakeryShortCode_VC_Tab {
	protected $controls_css_settings = 'tc vc_control-container';
	protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
	protected $predefined_atts = array(
		'el_class' => '',
		'width' => '',
		'title' => '',
	);

	public $nonDraggableClass = 'vc-non-draggable-container';

	public function contentAdmin( $atts, $content = null ) {
		$width = $el_class = $title = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );

		if ( 'column_14' === $width || '1/4' === $width ) {
			$width = array( 'vc_col-sm-3' );
		} elseif ( 'column_14-14-14-14' === $width ) {
			$width = array(
				'vc_col-sm-3',
				'vc_col-sm-3',
				'vc_col-sm-3',
				'vc_col-sm-3',
			);
		} elseif ( 'column_13' === $width || '1/3' === $width ) {
			$width = array( 'vc_col-sm-4' );
		} elseif ( 'column_13-23' === $width ) {
			$width = array( 'vc_col-sm-4', 'vc_col-sm-8' );
		} elseif ( 'column_13-13-13' === $width ) {
			$width = array( 'vc_col-sm-4', 'vc_col-sm-4', 'vc_col-sm-4' );
		} elseif ( 'column_12' === $width || '1/2' === $width ) {
			$width = array( 'vc_col-sm-6' );
		} elseif ( 'column_12-12' === $width ) {
			$width = array( 'vc_col-sm-6', 'vc_col-sm-6' );
		} elseif ( 'column_23' === $width || '2/3' === $width ) {
			$width = array( 'vc_col-sm-8' );
		} elseif ( 'column_34' === $width || '3/4' === $width ) {
			$width = array( 'vc_col-sm-9' );
		} elseif ( 'column_16' === $width || '1/6' === $width ) {
			$width = array( 'vc_col-sm-2' );
		} else {
			$width = array( '' );
		}
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

		for ( $i = 0; $i < count( $width ); $i ++ ) {
			$output .= '<div class="group ' . $sortable . '">';
			$output .= '<h3><span class="tab-label"><%= params.title %></span></h3>';
			$output .= '<div ' . $this->mainHtmlBlockParams( $width, $i ) . '>';
			$output .= str_replace( '%column_size%', wpb_translateColumnWidthToFractional( $width[ $i ] ), $column_controls );
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div ' . $this->containerHtmlBlockParams( $width, $i ) . '>';
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
			$output .= str_replace( '%column_size%', wpb_translateColumnWidthToFractional( $width[ $i ] ), $column_controls_bottom );
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	public function mainHtmlBlockParams( $width, $i ) {
		return 'data-element_type="' . $this->settings['base'] . '" class=" wpb_' . $this->settings['base'] . '"' . $this->customAdminBlockParams();
	}

	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="wpb_column_container vc_container_for_children"';
	}

	public function contentAdmin_old( $atts, $content = null ) {
		$width = $el_class = $title = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';
		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		for ( $i = 0; $i < count( $width ); $i ++ ) {
			$output .= '<div class="group wpb_sortable">';
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div class="vc_row-fluid wpb_row_container">';
			$output .= '<h3><a href="#">' . $title . '</a></h3>';
			$output .= '<div data-element_type="' . $this->settings['base'] . '" class=" wpb_' . $this->settings['base'] . ' wpb_sortable">';
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div class="vc_row-fluid wpb_row_container">';
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
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	protected function outputTitle( $title ) {
		return '';
	}

	public function customAdminBlockParams() {
		return '';
	}
}


/* nectar addition */ 

Class WPBakeryShortCode_Toggle extends WPBakeryShortCode_VC_Tab {
	protected $controls_css_settings = 'tc vc_control-container';
	protected $controls_list = array('add', 'edit', 'clone', 'delete');
	protected $predefined_atts = array(
		'el_class' => '',
		'width' => '',
		'title' => ''
	);

	public function contentAdmin( $atts, $content = null ) {
		$width = $el_class = $title = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );

		if ( $width == 'column_14' || $width == '1/4' ) {
			$width = array( 'vc_col-sm-3' );
		} else if ( $width == 'column_14-14-14-14' ) {
			$width = array( 'vc_col-sm-3', 'vc_col-sm-3', 'vc_col-sm-3', 'vc_col-sm-3' );
		} else if ( $width == 'column_13' || $width == '1/3' ) {
			$width = array( 'vc_col-sm-4' );
		} else if ( $width == 'column_13-23' ) {
			$width = array( 'vc_col-sm-4', 'vc_col-sm-8' );
		} else if ( $width == 'column_13-13-13' ) {
			$width = array( 'vc_col-sm-4', 'vc_col-sm-4', 'vc_col-sm-4' );
		} else if ( $width == 'column_12' || $width == '1/2' ) {
			$width = array( 'vc_col-sm-6' );
		} else if ( $width == 'column_12-12' ) {
			$width = array( 'vc_col-sm-6', 'vc_col-sm-6' );
		} else if ( $width == 'column_23' || $width == '2/3' ) {
			$width = array( 'vc_col-sm-8' );
		} else if ( $width == 'column_34' || $width == '3/4' ) {
			$width = array( 'vc_col-sm-9' );
		} else if ( $width == 'column_16' || $width == '1/6' ) {
			$width = array( 'vc_col-sm-2' );
		} else {
			$width = array( '' );
		}
		for ( $i = 0; $i < count( $width ); $i ++ ) {
			$output .= '<div class="group wpb_sortable">';
			$output .= '<h3><span class="tab-label"><%= params.title %></span></h3>';
			$output .= '<div ' . $this->mainHtmlBlockParams( $width, $i ) . '>';
			$output .= str_replace( "%column_size%", wpb_translateColumnWidthToFractional( $width[$i] ), $column_controls );
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div ' . $this->containerHtmlBlockParams( $width, $i ) . '>';
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
			$output .= str_replace( "%column_size%", wpb_translateColumnWidthToFractional( $width[$i] ), $column_controls_bottom );
			$output .= '</div>';
			$output .= '</div>';
		}
		return $output;
	}

	public function mainHtmlBlockParams( $width, $i ) {
		return 'data-element_type="' . $this->settings["base"] . '" class="wpb_vc_accordion_tab wpb_' . $this->settings['base'] . '"' . $this->customAdminBlockParams();
	}

	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="wpb_column_container vc_container_for_children"';
	}

	public function contentAdmin_old( $atts, $content = null ) {
		$width = $el_class = $title = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';
		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		for ( $i = 0; $i < count( $width ); $i ++ ) {
			$output .= '<div class="group wpb_sortable">';
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div class="vc_row-fluid wpb_row_container">';
			$output .= '<h3><a href="#">' . $title . '</a></h3>';
			$output .= '<div ' . $this->customAdminBockParams() . ' data-element_type="' . $this->settings["base"] . '" class=" wpb_' . $this->settings['base'] . ' wpb_sortable">';
			$output .= '<div class="wpb_element_wrapper">';
			$output .= '<div class="vc_row-fluid wpb_row_container">';
			$output .= do_shortcode( shortcode_unautop( $content ) );
			$output .= '</div>';
			if ( isset( $this->settings['params'] ) ) {
				$inner = '';
				foreach ( $this->settings['params'] as $param ) {
					$param_value = isset( $$param['param_name'] ) ? $$param['param_name'] : '';
					if ( is_array( $param_value ) ) {
						// Get first element from the array
						reset( $param_value );
						$first_key = key( $param_value );
						$param_value = $param_value[$first_key];
					}
					$inner .= $this->singleParamHtmlHolder( $param, $param_value );
				}
				$output .= $inner;
			}
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	protected function outputTitle( $title ) {
		return '';
	}

	public function customAdminBlockParams() {
		return '';
	}

}

/* nectar addition end */ 

