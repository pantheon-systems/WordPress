<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WPBakeryShortCode_VC_Tabs extends WPBakeryShortCode {
	static $filter_added = false;
	protected $controls_css_settings = 'out-tc vc_controls-content-widget';
	protected $controls_list = array( 'edit', 'clone', 'delete' );

	public function __construct( $settings ) {
		parent::__construct( $settings );
		if ( ! self::$filter_added ) {
			$this->addFilter( 'vc_inline_template_content', 'setCustomTabId' );
			self::$filter_added = true;
		}
	}

	public function contentAdmin( $atts, $content = null ) {
		$width = $custom_markup = '';
		$shortcode_attributes = array( 'width' => '1/1' );
		foreach ( $this->settings['params'] as $param ) {
			if ( 'content' !== $param['param_name'] ) {
				$shortcode_attributes[ $param['param_name'] ] = isset( $param['value'] ) ? $param['value'] : null;
			} elseif ( 'content' === $param['param_name'] && null === $content ) {
				$content = $param['value'];
			}
		}
		extract( shortcode_atts( $shortcode_attributes, $atts ) );

		// Extract tab titles

		preg_match_all( '/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );

		$output = '';
		$tab_titles = array();

		if ( isset( $matches[0] ) ) {
			$tab_titles = $matches[0];
		}
		$tmp = '';
		if ( count( $tab_titles ) ) {
			$tmp .= '<ul class="clearfix tabs_controls">';
			foreach ( $tab_titles as $tab ) {
				preg_match( '/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
				if ( isset( $tab_matches[1][0] ) ) {
					$tmp .= '<li><a href="#tab-' . ( isset( $tab_matches[3][0] ) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) . '">' . $tab_matches[1][0] . '</a></li>';

				}
			}
			$tmp .= '</ul>' . "\n";
		} else {
			$output .= do_shortcode( $content );
		}

		$elem = $this->getElementHolder( $width );

		$iner = '';
		foreach ( $this->settings['params'] as $param ) {
			$param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
			if ( is_array( $param_value ) ) {
				// Get first element from the array
				reset( $param_value );
				$first_key = key( $param_value );
				$param_value = $param_value[ $first_key ];
			}
			$iner .= $this->singleParamHtmlHolder( $param, $param_value );
		}

		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			if ( '' !== $content ) {
				$custom_markup = str_ireplace( '%content%', $tmp . $content, $this->settings['custom_markup'] );
			} elseif ( '' === $content && isset( $this->settings['default_content_in_template'] ) && '' !== $this->settings['default_content_in_template'] ) {
				$custom_markup = str_ireplace( '%content%', $this->settings['default_content_in_template'], $this->settings['custom_markup'] );
			} else {
				$custom_markup = str_ireplace( '%content%', '', $this->settings['custom_markup'] );
			}
			$iner .= do_shortcode( $custom_markup );
		}
		$elem = str_ireplace( '%wpb_element_content%', $iner, $elem );
		$output = $elem;

		return $output;
	}

	public function getTabTemplate() {
		return '<div class="wpb_template">' . do_shortcode( '[vc_tab title="Tab" tab_id=""][/vc_tab]' ) . '</div>';
	}

	public function setCustomTabId( $content ) {
		return preg_replace( '/tab\_id\=\"([^\"]+)\"/', 'tab_id="$1-' . time() . '"', $content );
	}
}



/*nectar addition*/
require_once vc_path_dir('SHORTCODES_DIR', 'vc-column.php');


class WPBakeryShortCode_Tabbed_Section extends WPBakeryShortCode {

    static $filter_added = false;
    protected $controls_css_settings = 'out-tc vc_controls-content-widget';
    protected $controls_list = array( 'edit', 'clone', 'delete' );

    public function __construct( $settings ) {
        parent::__construct( $settings );
        // WPBakeryVisualComposer::getInstance()->addShortCode( array( 'base' => 'vc_tab' ) );
        if ( ! self::$filter_added ) {
            $this->addFilter( 'vc_inline_template_content', 'setCustomTabId' );
            self::$filter_added = true;
        }
    }

    public function contentAdmin( $atts, $content = null ) {
        $width = $custom_markup = '';
        $shortcode_attributes = array( 'width' => '1/1' );
        foreach ( $this->settings['params'] as $param ) {
            if ( $param['param_name'] != 'content' ) {
                //$shortcode_attributes[$param['param_name']] = $param['value'];
                if ( isset( $param['value'] ) && is_string( $param['value'] ) ) {
                    $shortcode_attributes[ $param['param_name'] ] = __( $param['value'], "js_composer" );
                } elseif ( isset( $param['value'] ) ) {
                    $shortcode_attributes[ $param['param_name'] ] = $param['value'];
                }
            } else if ( $param['param_name'] == 'content' && $content == null ) {
                //$content = $param['value'];
                $content = __( $param['value'], "js_composer" );
            }
        }
        extract( shortcode_atts(
            $shortcode_attributes
            , $atts ) );

        // Extract tab titles

        preg_match_all( '/vc_tab title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $content, $matches, PREG_OFFSET_CAPTURE );

        $output = '';
        $tab_titles = array();

        if ( isset( $matches[0] ) ) {
            $tab_titles = $matches[0];
        }
        $tmp = '';
        if ( count( $tab_titles ) ) {
            $tmp .= '<ul class="clearfix tabs_controls">';
            foreach ( $tab_titles as $tab ) {
                preg_match( '/title="([^\"]+)"(\stab_id\=\"([^\"]+)\"){0,1}/i', $tab[0], $tab_matches, PREG_OFFSET_CAPTURE );
                if ( isset( $tab_matches[1][0] ) ) {
                    $tmp .= '<li><a href="#tab-' . ( isset( $tab_matches[3][0] ) ? $tab_matches[3][0] : sanitize_title( $tab_matches[1][0] ) ) . '">' . $tab_matches[1][0] . '</a></li>';

                }
            }
            $tmp .= '</ul>' . "\n";
        } else {
            $output .= do_shortcode( $content );
        }


        $elem = $this->getElementHolder( $width );

        $iner = '';
        foreach ( $this->settings['params'] as $param ) {
            $param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
            if ( is_array( $param_value ) ) {
                // Get first element from the array
                reset( $param_value );
                $first_key = key( $param_value );
                $param_value = $param_value[ $first_key ];
            }
            $iner .= $this->singleParamHtmlHolder( $param, $param_value );
        }
        //$elem = str_ireplace('%wpb_element_content%', $iner, $elem);

        if ( isset( $this->settings["custom_markup"] ) && $this->settings["custom_markup"] != '' ) {
            if ( $content != '' ) {
                $custom_markup = str_ireplace( "%content%", $tmp . $content, $this->settings["custom_markup"] );
            } else if ( $content == '' && isset( $this->settings["default_content_in_template"] ) && $this->settings["default_content_in_template"] != '' ) {
                $custom_markup = str_ireplace( "%content%", $this->settings["default_content_in_template"], $this->settings["custom_markup"] );
            } else {
                $custom_markup = str_ireplace( "%content%", '', $this->settings["custom_markup"] );
            }
            //$output .= do_shortcode($this->settings["custom_markup"]);
            $iner .= do_shortcode( $custom_markup );
        }
        $elem = str_ireplace( '%wpb_element_content%', $iner, $elem );
        $output = $elem;

        return $output;
    }

    public function getTabTemplate() {
        return '<div class="wpb_template">' . do_shortcode( '[vc_tab title="Tab" tab_id=""][/vc_tab]' ) . '</div>';
    }

    public function setCustomTabId( $content ) {
        return preg_replace( '/tab\_id\=\"([^\"]+)\"/', 'tab_id="$1-' . time() . '"', $content );
    }
    //added to modify the class - needs wpb_vc_accordion to function properly
    public function getElementHolder( $width ) {
        $output = '';
        $column_controls = $this->getColumnControlsModular();
        $css_class = 'wpb_' . $this->settings["base"] . '  wpb_vc_tabs wpb_content_element wpb_sortable' . ( ! empty( $this->settings["class"] ) ? ' ' . $this->settings["class"] : '' );
        $output .= '<div data-element_type="' . $this->settings["base"] . '" class="' . $css_class . '">';
        $output .= str_replace( "%column_size%", wpb_translateColumnWidthToFractional( $width ), $column_controls );
        $output .= $this->getCallbacks( $this->shortcode );
        $output .= '<div class="wpb_element_wrapper ' . $this->settings( "wrapper_class" ) . '">';
        $output .= '%wpb_element_content%';
        $output .= '</div>'; // <!-- end .wpb_element_wrapper -->';
        $output .= '</div>'; // <!-- end #element-'.$this->shortcode.' -->';
        return $output;
    }


}















class WPBakeryShortCode_Item extends WPBakeryShortCode_VC_Column {
   protected $controls_css_settings = 'tc vc_control-container';
    protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
    protected $predefined_atts = array(
        'tab_id' => '',
        'title' => ''
    );
    protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';

    public function __construct( $settings ) {
        parent::__construct( $settings );
    }

    public function customAdminBlockParams() {
        return ' id="tab-' . $this->atts['tab_id'] . '"';
    }

    public function mainHtmlBlockParams( $width, $i ) {
        return 'data-element_type="' . $this->settings["base"] . '" class="wpb_vc_tab wpb_sortable wpb_content_holder"' . $this->customAdminBlockParams();
    }

    public function containerHtmlBlockParams( $width, $i ) {
        return 'class="wpb_column_container vc_container_for_children"';
    }

    public function getColumnControls( $controls, $extended_css = '' ) {
        return $this->getColumnControlsModular( $extended_css );
        /*
        $controls_start = '<div class="vc_controls controls controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';
        $controls_end = '</div>';

        if ( $extended_css == 'bottom-controls' ) $control_title = sprintf( __( 'Append to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
        else $control_title = sprintf( __( 'Prepend to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );

        $controls_add = ' <a class="vc_control column_add" href="#" title="' . $control_title . '"><i class="vc_icon"></i></a>';
        $controls_edit = ' <a class="vc_control column_edit" href="#" title="' . sprintf( __( 'Edit this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';
        $controls_clone = '<a class="vc_control column_clone" href="#" title="' . sprintf( __( 'Clone this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';

        $controls_delete = '<a class="vc_control column_delete" href="#" title="' . sprintf( __( 'Delete this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';
        return $controls_start . $controls_add . $controls_edit . $controls_clone . $controls_delete . $controls_end;
        */
    }
}


class WPBakeryShortCode_Tab extends WPBakeryShortCode_VC_Column {
   protected $controls_css_settings = 'tc vc_control-container';
    protected $controls_list = array( 'add', 'edit', 'clone', 'delete' );
    protected $predefined_atts = array(
        'tab_id' => '',
        'title' => ''
    );
    protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';

    public function __construct( $settings ) {
        parent::__construct( $settings );
    }

    public function customAdminBlockParams() {
        return ' id="tab-' . $this->atts['tab_id'] . '"';
    }

    public function mainHtmlBlockParams( $width, $i ) {
        return 'data-element_type="' . $this->settings["base"] . '" class="wpb_vc_tab wpb_sortable wpb_content_holder"' . $this->customAdminBlockParams();
    }

    public function containerHtmlBlockParams( $width, $i ) {
        return 'class="wpb_column_container vc_container_for_children"';
    }

    public function getColumnControls( $controls, $extended_css = '' ) {
        return $this->getColumnControlsModular( $extended_css );
        /*
        $controls_start = '<div class="vc_controls controls controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';
        $controls_end = '</div>';

        if ( $extended_css == 'bottom-controls' ) $control_title = sprintf( __( 'Append to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
        else $control_title = sprintf( __( 'Prepend to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );

        $controls_add = ' <a class="vc_control column_add" href="#" title="' . $control_title . '"><i class="vc_icon"></i></a>';
        $controls_edit = ' <a class="vc_control column_edit" href="#" title="' . sprintf( __( 'Edit this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';
        $controls_clone = '<a class="vc_control column_clone" href="#" title="' . sprintf( __( 'Clone this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';

        $controls_delete = '<a class="vc_control column_delete" href="#" title="' . sprintf( __( 'Delete this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc_icon"></i></a>';
        return $controls_start . $controls_add . $controls_edit . $controls_clone . $controls_delete . $controls_end;
        */
    }
}

/*nectar addition end*/
