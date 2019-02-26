<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder shortcode attributes fields
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Edit form fields builder for shortcode attributes.
 *
 * @since 4.4
 */
class Vc_Edit_Form_Fields implements Vc_Render {
	/**
	 * @since 4.4
	 * @var bool
	 */
	protected $tag = false;
	/**
	 * @since 4.4
	 * @var array
	 */
	protected $atts = array();
	/**
	 * @since 4.4
	 * @var array
	 */
	protected $settings = array();
	/**
	 * @since 4.4
	 * @var bool
	 */
	protected $post_id = false;

	/**
	 * Construct Form fields.
	 *
	 * @since 4.4
	 *
	 * @param $tag - shortcode tag
	 * @param $atts - list of attribute assign to the shortcode.
	 */
	public function __construct( $tag, $atts ) {
		$this->tag = $tag;
		$this->atts = apply_filters( 'vc_edit_form_fields_attributes_' . $this->tag, $atts );
		$this->setSettings( WPBMap::getShortCode( $this->tag ) );
	}

	/**
	 * Get settings
	 * @since 4.4
	 *
	 * @param $key
	 *
	 * @return null
	 */
	public function setting( $key ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
	}

	/**
	 * Set settings data
	 * @since 4.4
	 *
	 * @param array $settings
	 */
	public function setSettings( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Shortcode Post ID getter.
	 * If post id isn't set try to get from get_the_ID function.
	 * @since 4.4
	 * @return int|bool;
	 */
	public function postId() {
		if ( false === $this->post_id ) {
			$this->post_id = get_the_ID();
		}

		return $this->post_id;
	}

	/**
	 * Shortcode Post ID setter.
	 * @since 4.4
	 *
	 * @param $post_id - integer value in post_id
	 */
	public function setPostId( $post_id ) {
		$this->post_id = (int) $post_id;
	}

	/**
	 * Get shortcode attribute value.
	 *
	 * This function checks if value isn't set then it uses std or value fields in param settings.
	 * @since 4.4
	 *
	 * @param $param_settings
	 * @param $value
	 *
	 * @return null
	 */
	protected function parseShortcodeAttributeValue( $param_settings, $value ) {
		if ( is_null( $value ) ) { // If value doesn't exists
			if ( isset( $param_settings['std'] ) ) {
				$value = $param_settings['std'];
			} elseif ( isset( $param_settings['value'] ) && is_array( $param_settings['value'] ) && ! empty( $param_settings['type'] ) && 'checkbox' !== $param_settings['type'] ) {
				$first_key = key( $param_settings['value'] );
				$value = $first_key ? $param_settings['value'][ $first_key ] : '';
			} elseif ( isset( $param_settings['value'] ) && ! is_array( $param_settings['value'] ) ) {
				$value = $param_settings['value'];
			}
		}

		return $value;
	}

	/**
	 * Enqueue js scripts for attributes types.
	 * @since 4.4
	 * @return string
	 */
	public function enqueueScripts() {
		$output = '';
		if ( ! WpbakeryShortcodeParams::isEnqueue() ) {
			$scripts = apply_filters( 'vc_edit_form_enqueue_script', WpbakeryShortcodeParams::getScripts() );
			foreach ( $scripts as $script ) {
				$output .= "\n\n" . '<script type="text/javascript" src="' . $script . '"></script>';
			}
		}

		return $output;
	}

	/**
	 * Render grouped fields.
	 * @since 4.4
	 *
	 * @param $groups
	 * @param $groups_content
	 *
	 * @return string
	 */
	protected function renderGroupedFields( $groups, $groups_content ) {
		$output = '';
		if ( sizeof( $groups ) > 1 || ( sizeof( $groups ) >= 1 && empty( $groups_content['_general'] ) ) ) {
			$output .= '<div class="vc_panel-tabs" id="vc_edit-form-tabs">';
			$output .= '<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls">';
			$key = 0;
			foreach ( $groups as $g ) {
				$output .= '<li class="vc_edit-form-tab-control" data-tab-index="' . $key . '"><button data-vc-ui-element-target="#vc_edit-form-tab-' . $key ++ . '" class="vc_ui-tabs-line-trigger" data-vc-ui-element="panel-tab-control">' . ( '_general' === $g ? __( 'General', 'js_composer' ) : $g ) . '</button></li>';
			}
			$output .= '<li class="vc_ui-tabs-line-dropdown-toggle" data-vc-action="dropdown"
						    data-vc-content=".vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-toggle">
                            <span class="vc_ui-tabs-line-trigger" data-vc-accordion
                                  data-vc-container=".vc_ui-tabs-line-dropdown-toggle"
                                  data-vc-target=".vc_ui-tabs-line-dropdown"> </span>
							<ul class="vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-dropdown">
							</ul>
					</ul>';

			$key = 0;
			foreach ( $groups as $g ) {
				$output .= '<div id="vc_edit-form-tab-' . $key ++ . '" class="vc_edit-form-tab vc_row vc_ui-flex-row" data-vc-ui-element="panel-edit-element-tab">';
				$output .= $groups_content[ $g ];
				$output .= '</div>';
			}
			$output .= '</div>';
		} elseif ( ! empty( $groups_content['_general'] ) ) {
			$output .= '<div class="vc_edit-form-tab vc_row vc_ui-flex-row vc_active" data-vc-ui-element="panel-edit-element-tab">' . $groups_content['_general'] . '</div>';
		}

		return $output;
	}

	/**
	 * Render fields html and output it.
	 * @since 4.4
	 * vc_filter: vc_edit_form_class - filter to override editor_css_classes array
	 */
	public function render() {
		$this->loadDefaultParams();
		$output = $el_position = '';
		$groups_content = $groups = array();
		$params = $this->setting( 'params' );
		$editor_css_classes = apply_filters( 'vc_edit_form_class', array(
			'wpb_edit_form_elements',
			'vc_edit_form_elements',
		), $this->atts, $params );
		$deprecated = $this->setting( 'deprecated' );
		require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
		$list_vendor_presets = Vc_Settings_Preset::listVendorSettingsPresets( $this->tag );
		$list_presets = Vc_Settings_Preset::listSettingsPresets( $this->tag );
		$show_settings = false;

		$saveAsTemplateElements = apply_filters( 'vc_popup_save_as_template_elements', array(
			'vc_row',
			'vc_section',
		) );

		$show_presets = ! in_array( $this->tag, $saveAsTemplateElements ) && vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get();

		if ( in_array( $this->tag, $saveAsTemplateElements ) && vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
			$show_settings = true;
		}

		$output .= sprintf( '<script type="text/javascript">window.vc_presets_show=%s;</script>', $show_presets ? 'true' : 'false' );
		$output .= sprintf( '<script type="text/javascript">window.vc_settings_show=%s;</script>', $show_presets || $show_settings ? 'true' : 'false' );

		if ( ! empty( $deprecated ) ) {
			$output .= '<div class="vc_row vc_ui-flex-row vc_shortcode-edit-form-deprecated-message"><div class="vc_col-sm-12 wpb_element_wrapper">' . vc_message_warning( sprintf( __( 'You are using outdated element, it is deprecated since version %s.', 'js_composer' ), $this->setting( 'deprecated' ) ) ) . '</div></div>';
		}
		$output .= '<div class="' . implode( ' ', $editor_css_classes ) . '" data-title="' . htmlspecialchars( __( 'Edit', 'js_composer' ) . ' ' . __( $this->setting( 'name' ), 'js_composer' ) ) . '">';
		if ( is_array( $params ) ) {
			foreach ( $params as $param ) {
				$name = isset( $param['param_name'] ) ? $param['param_name'] : null;
				if ( ! is_null( $name ) ) {
					$value = isset( $this->atts[ $name ] ) ? $this->atts[ $name ] : null;
					$value = $this->parseShortcodeAttributeValue( $param, $value );
					$group = isset( $param['group'] ) && '' !== $param['group'] ? $param['group'] : '_general';
					if ( ! isset( $groups_content[ $group ] ) ) {
						$groups[] = $group;
						$groups_content[ $group ] = '';
					}
					$groups_content[ $group ] .= $this->renderField( $param, $value );
				}
			}
		}
		$output .= $this->renderGroupedFields( $groups, $groups_content );
		$output .= '</div>';
		$output .= $this->enqueueScripts();
		echo $output;
		do_action( 'vc_edit_form_fields_after_render' );
	}

	/**
	 * Generate html for shortcode attribute.
	 *
	 * Method
	 * @since 4.4
	 *
	 * @param $param
	 * @param $value
	 *
	 * vc_filter: vc_single_param_edit - hook to edit any shortode param
	 * vc_filter: vc_form_fields_render_field_{shortcode_name}_{param_name}_param_value - hook to edit shortcode param
	 *     value vc_filter: vc_form_fields_render_field_{shortcode_name}_{param_name}_param - hook to edit shortcode
	 *     param attributes vc_filter: vc_single_param_edit_holder_output - hook to edit output of this method
	 *
	 * @return mixed|void
	 */
	protected function renderField( $param, $value ) {
		$param['vc_single_param_edit_holder_class'] = array(
			'wpb_el_type_' . $param['type'],
			'vc_wrapper-param-type-' . $param['type'],
			'vc_shortcode-param',
			'vc_column',
		);
		if ( ! empty( $param['param_holder_class'] ) ) {
			$param['vc_single_param_edit_holder_class'][] = $param['param_holder_class'];
		}
		$param = apply_filters( 'vc_single_param_edit', $param, $value );
		$output = '<div class="' . implode( ' ', $param['vc_single_param_edit_holder_class'] ) . '" data-vc-ui-element="panel-shortcode-param" data-vc-shortcode-param-name="' . esc_attr( $param['param_name'] ) . '" data-param_type="' . esc_attr( $param['type'] ) . '" data-param_settings="' . esc_attr( json_encode( $param ) ) . '">';
		$output .= ( isset( $param['heading'] ) ) ? '<div class="wpb_element_label">' . $param['heading'] . '</div>' : '';
		$output .= '<div class="edit_form_line">';
		$value = apply_filters( 'vc_form_fields_render_field_' . $this->setting( 'base' ) . '_' . $param['param_name'] . '_param_value', $value, $param, $this->settings, $this->atts );
		$param = apply_filters( 'vc_form_fields_render_field_' . $this->setting( 'base' ) . '_' . $param['param_name'] . '_param', $param, $value, $this->settings, $this->atts );
		$output = apply_filters( 'vc_edit_form_fields_render_field_' . $param['type'] . '_before', $output );
		$output .= vc_do_shortcode_param_settings_field( $param['type'], $param, $value, $this->setting( 'base' ) );
		$output_after = '';
		if ( isset( $param['description'] ) ) {
			$output_after .= '<span class="vc_description vc_clearfix">' . $param['description'] . '</span>';
		}
		$output_after .= '</div></div>';
		$output .= apply_filters( 'vc_edit_form_fields_render_field_' . $param['type'] . '_after', $output_after );

		return apply_filters( 'vc_single_param_edit_holder_output', $output, $param, $value, $this->settings, $this->atts );
	}

	/**
	 * Create default shortcode params
	 *
	 * List of params stored in global variable $vc_params_list.
	 * Please check include/params/load.php for default params list.
	 * @since 4.4
	 * @return bool
	 */
	public function loadDefaultParams() {
		global $vc_params_list;
		if ( empty( $vc_params_list ) ) {
			return false;
		}
		$script_url = vc_asset_url( 'js/dist/edit-form.min.js' );
		foreach ( $vc_params_list as $param ) {
			vc_add_shortcode_param( $param, 'vc_' . $param . '_form_field', $script_url );
		}
		do_action( 'vc_load_default_params' );

		return true;
	}
}
