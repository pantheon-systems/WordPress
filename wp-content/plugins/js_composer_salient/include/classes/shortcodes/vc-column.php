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
class WPBakeryShortCode_VC_Column extends WPBakeryShortCode {
	/**
	 * @var array
	 */
	protected $predefined_atts = array(
		'font_color' => '',
		'el_class' => '',
		'el_position' => '',
		'width' => '1/1',
	);

	public $nonDraggableClass = 'vc-non-draggable-column';

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

	/**
	 * @param $controls
	 * @param string $extended_css
	 *
	 * @return string
	 */
	public function getColumnControls( $controls, $extended_css = '' ) {
		$output = '<div class="vc_controls vc_control-column vc_controls-visible' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';
		$controls_end = '</div>';

		if ( ' bottom-controls' === $extended_css ) {
			$control_title = __( 'Append to this column', 'js_composer' );
		} else {
			$control_title = __( 'Prepend to this column', 'js_composer' );
		}
		if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() ) {
			$controls_add = '<a class="vc_control column_add vc_column-add" data-vc-control="add" href="#" title="' . $control_title . '"><i class="vc-composer-icon vc-c-icon-add"></i></a>';
		} else {
			$controls_add = '';
		}
		$controls_edit = '<a class="vc_control column_edit vc_column-edit"  data-vc-control="edit" href="#" title="' . __( 'Edit this column', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></a>';
		$controls_delete = '<a class="vc_control column_delete vc_column-delete" data-vc-control="delete"  href="#" title="' . __( 'Delete this column', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a>';
		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		if ( is_array( $controls ) && ! empty( $controls ) ) {
			foreach ( $controls as $control ) {
				if ( 'add' === $control || ( $editAccess && 'edit' === $control ) || $allAccess ) {
					$method_name = vc_camel_case( 'output-editor-control-' . $control );
					if ( method_exists( $this, $method_name ) ) {
						$output .= $this->$method_name();
					} else {
						$control_var = 'controls_' . $control;
						if ( isset( ${$control_var} ) ) {
							$output .= ${$control_var};
						}
					}
				}
			}

			return $output . $controls_end;
		} elseif ( is_string( $controls ) && 'full' === $controls ) {
			if ( $allAccess ) {
				return $output . $controls_add . $controls_edit . $controls_delete . $controls_end;
			} elseif ( $editAccess ) {
				return $output . $controls_add . $controls_edit . $controls_end;
			}

			return $output . $controls_add . $controls_end;
		} elseif ( is_string( $controls ) ) {
			$control_var = 'controls_' . $controls;
			if ( 'add' === $controls || ( $editAccess && 'edit' == $controls || $allAccess ) && isset( ${$control_var} ) ) {
				return $output . ${$control_var} . $controls_end;
			}

			return $output . $controls_end;
		}
		if ( $allAccess ) {
			return $output . $controls_add . $controls_edit . $controls_delete . $controls_end;
		} elseif ( $editAccess ) {
			return $output . $controls_add . $controls_edit . $controls_end;
		}

		return $output . $controls_add . $controls_end;
	}

	/**
	 * @param $param
	 * @param $value
	 *
	 * @return string
	 */
	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes.
		$old_names = array(
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		);
		$new_names = array(
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		);
		$value = str_ireplace( $old_names, $new_names, $value );
		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( isset( $param['holder'] ) && 'hidden' !== $param['holder'] ) {
			$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
		}

		return $output;
	}

	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function contentAdmin( $atts, $content = null ) {
		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );

		if ( ' column_14' === $width || ' 1/4' === $width ) {
			$width = array( 'vc_col-sm-3' );
		} elseif ( ' column_14===$width-14-14-14' ) {
			$width = array(
				'vc_col-sm-3',
				'vc_col-sm-3',
				'vc_col-sm-3',
				'vc_col-sm-3',
			);
		} elseif ( ' column_13' === $width || ' 1/3' === $width ) {
			$width = array( 'vc_col-sm-4' );
		} elseif ( ' column_13===$width-23' ) {
			$width = array(
				'vc_col-sm-4',
				'vc_col-sm-8',
			);
		} elseif ( ' column_13===$width-13-13' ) {
			$width = array(
				'vc_col-sm-4',
				'vc_col-sm-4',
				'vc_col-sm-4',
			);
		} elseif ( ' column_12' === $width || ' 1/2' === $width ) {
			$width = array( 'vc_col-sm-6' );
		} elseif ( ' column_12===$width-12' ) {
			$width = array(
				'vc_col-sm-6',
				'vc_col-sm-6',
			);
		} elseif ( ' column_23' === $width || ' 2/3' === $width ) {
			$width = array( 'vc_col-sm-8' );
		} elseif ( ' column_34' === $width || ' 3/4' === $width ) {
			$width = array( 'vc_col-sm-9' );
		} elseif ( ' column_16' === $width || ' 1/6' === $width ) {
			$width = array( 'vc_col-sm-2' );
		} elseif ( ' column_56' === $width || ' 5/6' === $width ) {
			$width = array( 'vc_col-sm-10' );
		} else {
			$width = array( '' );
		}
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}

		return $output;
	}

	/**
	 * @return string
	 */
	public function customAdminBlockParams() {
		return '';
	}

	/**
	 * @param $width
	 * @param $i
	 *
	 * @return string
	 */
	public function mainHtmlBlockParams( $width, $i ) {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

		return 'data-element_type="' . $this->settings['base'] . '" data-vc-column-width="' . wpb_vc_get_column_width_indent( $width[ $i ] ) . '" class="wpb_' . $this->settings['base'] . ' ' . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' ) . ' ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
	}

	/**
	 * @param $width
	 * @param $i
	 *
	 * @return string
	 */
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="wpb_column_container vc_container_for_children"';
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function template( $content = '' ) {
		return $this->contentAdmin( $this->atts );
	}

	/**
	 * @return string
	 */
	protected function templateWidth() {
		return '<%= window.vc_convert_column_size(params.width) %>';
	}

	/**
	 * @param string $font_color
	 *
	 * @return string
	 */
	public function buildStyle( $font_color = '' ) {
		$style = '';
		if ( ! empty( $font_color ) ) {
			$style .= vc_get_css_color( 'color', $font_color );
		}

		return empty( $style ) ? $style : ' style="' . esc_attr( $style ) . '"';
	}
}



/* nectar addition */ 

class WPBakeryShortCode_One_Half extends WPBakeryShortCode_VC_Column {

	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-6 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-6' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
}


class WPBakeryShortCode_One_Half_Last extends WPBakeryShortCode_VC_Column {
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-6 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-6' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
}



class WPBakeryShortCode_One_Third extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-4 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-4' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_One_Third_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-4 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-4' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}




class WPBakeryShortCode_One_Fourth extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-3 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-3' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_One_Fourth_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-3 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-3' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_One_Sixth extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-2 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-2' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}




class WPBakeryShortCode_One_Sixth_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-2 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-2' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_Two_Thirds extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-8 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-8' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}



class WPBakeryShortCode_Two_Thirds_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-8 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-8' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
}


class WPBakeryShortCode_Three_Fourths extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-9 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-9' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_Three_Fourths_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-9 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-9' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}


class WPBakeryShortCode_Five_Sixths extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-10 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-10' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}

class WPBakeryShortCode_Five_Sixths_Last extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-10 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-10' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}

class WPBakeryShortCode_One_Whole extends WPBakeryShortCode_VC_Column {
	
	public function mainHtmlBlockParams($width, $i) {
        return 'data-element_type="vc_column" data-vc-column-width="'.wpb_vc_get_column_width_indent($width[$i]).'" class="wpb_' . $this->settings['base'] . ' wpb_vc_column vc_col-sm-12 wpb_sortable ' . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
    }
    
    public function contentAdmin($atts, $content = null) {
       		$width = $el_class = '';
		extract( shortcode_atts( $this->predefined_atts, $atts ) );
		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );
		$column_controls_bottom = $this->getColumnControls( 'add', 'bottom-controls' );


		$width = array( 'vc_col-sm-12' );
		
		for ( $i = 0; $i < count( $width ); $i ++ ) {
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
		}
		return $output;
	}
	
}




$column_params = array(
	 array(
		"type" => "dropdown",
		"class" => "",
		"heading" => "Animation",
		"param_name" => "animation",
		"value" => array(
			 "None" => "none",
		     "Fade In" => "Fade In",
	  		 "Fade In From Left" => "Fade In From Left",
	  		 "Fade In Right" => "Fade In From Right",
	  		 "Fade In From Bottom" => "Fade In From Bottom",
	  		 "Grow In" => "Grow In"		
		)
	),
    array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Animation Delay",
		"param_name" => "delay",
		"description" => ""
	),

	array(
		"type" => "checkbox",
		"class" => "",
		"heading" => "Boxed Column",
		"value" => array("Boxed Style" => "true" ),
		"param_name" => "boxed",
		"description" => ""
	),

	array(
		"type" => "checkbox",
		"class" => "",
		"heading" => "Centered Content",
		"value" => array("Centered Content Alignment" => "true" ),
		"param_name" => "centered_text",
		"description" => ""
	),

	 array(
		"type" => "textfield",
		"class" => "",
		"heading" => "Extra Class Name",
		"param_name" => "class",
		"value" => ""
	));







vc_map(  array(
  "name" => __("One Half", "js_composer"),
  "base" => "one_half",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Half", "js_composer"),
  "base" => "one_half_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Third", "js_composer"),
  "base" => "one_third",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Third", "js_composer"),
  "base" => "one_third_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Fourth", "js_composer"),
  "base" => "one_fourth",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Fourth", "js_composer"),
  "base" => "one_fourth_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));



vc_map(  array(
  "name" => __("One Sixth", "js_composer"),
  "base" => "one_sixth",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Sixth", "js_composer"),
  "base" => "one_sixth_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));



vc_map(  array(
  "name" => __("Three Fourths", "js_composer"),
  "base" => "three_fourths",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("Three Fourths", "js_composer"),
  "base" => "three_fourths_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("Two Thirds", "js_composer"),
  "base" => "two_thirds",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("Two Thirds", "js_composer"),
  "base" => "two_thirds_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("Five Sixths", "js_composer"),
  "base" => "five_sixths",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("Five Sixths", "js_composer"),
  "base" => "five_sixths_last",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
  "params"=> $column_params,
  "js_view" => 'VcColumnView'
));


vc_map(  array(
  "name" => __("One Whole", "js_composer"),
  "base" => "one_whole",
  "class" => "",
  "icon" => "",
  "wrapper_class" => "",
  "controls"	=> "full",
  "allowed_container_element" => false,
  "content_element" => false,
  "is_container" => true,
   "params"=> $column_params,
  "js_view" => 'VcColumnView'
));

/* nectar addition end */ 
