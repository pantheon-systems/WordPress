<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'EDITORS_DIR', 'class-vc-edit-form-fields.php' );

/**
 * Class Vc_ParamGroup_Edit_Form_Fields
 * @since 4.4
 */
class Vc_ParamGroup_Edit_Form_Fields extends Vc_Edit_Form_Fields {

	/**
	 * @since 4.4
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		$this->setSettings( $settings );
	}

	/**
	 * @param $param
	 * @param $value
	 *
	 * @since 4.4
	 * @return mixed|void
	 */
	public function renderField( $param, $value ) {
		return parent::renderField( $param, $value );
	}

	/**
	 * Get shortcode attribute value wrapper for params group.
	 *
	 * This function checks if value isn't set then it uses std or value fields in param settings.
	 * @since 5.2.1
	 *
	 * @param $params_settings
	 * @param null $value
	 *
	 * @return mixed;
	 */
	public function getParamGroupAttributeValue( $params_settings, $value = null ) {
		return $this->parseShortcodeAttributeValue( $params_settings, $value );
	}
}

/**
 * Class Vc_ParamGroup
 * @since 4.4
 */
class Vc_ParamGroup {
	/**
	 * @since 4.4
	 * @var
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var array|mixed
	 */
	protected $value;
	/**
	 * @since 4.4
	 * @var
	 */
	protected $map;
	/**
	 * @since 4.4
	 * @var
	 */
	protected $atts;

	/**
	 * @param $settings
	 * @param $value
	 * @param $tag
	 *
	 * @since 4.4
	 */
	public function __construct( $settings, $value, $tag ) {
		$this->settings = $settings;
		$this->settings['base'] = $tag;
		$this->value = vc_param_group_parse_atts( $value );
		$this->unparsed_value = $value;
	}

	/**
	 * @param $param_name
	 * @param $arr
	 *
	 * @since 4.4
	 * @return array
	 */
	public function params_to_arr( $param_name, $arr ) {
		$data = array();
		foreach ( $arr as $param ) {
			$data[ $param_name . '_' . $param['param_name'] ] = $param['type'];
		}

		return $data;
	}

	/**
	 * @since 4.4
	 * @return mixed|string
	 */
	public function render() {
		$output = '';
		$edit_form = new Vc_ParamGroup_Edit_Form_Fields( $this->settings );

		$settings = $this->settings;
		$output .= '<ul class="vc_param_group-list vc_settings" data-settings="' . htmlentities( json_encode( $settings ), ENT_QUOTES, 'utf-8' ) . '">';

		$template = vc_include_template( 'params/param_group/content.tpl.php' );

		// Parsing values
		if ( ! empty( $this->value ) ) {
			foreach ( $this->value as $values ) {
				$output .= $template;
				$value_block = "<div class='vc_param_group-wrapper vc_clearfix'>";
				$data = $values;
				foreach ( $this->settings['params'] as $param ) {
					$param_value = isset( $data[ $param['param_name'] ] ) ? $data[ $param['param_name'] ] : ( isset( $param['value'] ) ? $param['value'] : null );
					$param['param_name'] = $this->settings['param_name'] . '_' . $param['param_name'];
					$value = $edit_form->getParamGroupAttributeValue( $param, $param_value );
					$value_block .= $edit_form->renderField( $param, $value );
				}
				$value_block .= '</div>';
				$output = str_replace( '%content%', $value_block, $output );
			}
		} else {
			$output .= $template;

		}

		// Empty fields wrapper and Add new fields wrapper
		$content = "<div class='vc_param_group-wrapper vc_clearfix'>";
		foreach ( $this->settings['params'] as $param ) {
			$param['param_name'] = $this->settings['param_name'] . '_' . $param['param_name'];
			$value = $edit_form->getParamGroupAttributeValue( $param );
			$content .= $edit_form->renderField( $param, $value );
		}
		$content .= '</div>';
		$output = str_replace( '%content%', $content, $output );

		// And button on bottom
		$output .= '<li class="wpb_column_container vc_container_for_children vc_param_group-add_content vc_empty-container"></li></ul>';

		$add_template = vc_include_template( 'params/param_group/add.tpl.php' );
		$add_template = str_replace( '%content%', $content, $add_template );

		$output .= '<script type="text/html" class="vc_param_group-template">' . json_encode( $add_template ) . '</script>';
		$output .= '<input name="' . $this->settings['param_name'] . '" class="wpb_vc_param_value  ' . $this->settings['param_name'] . ' ' . $this->settings['type'] . '_field" type="hidden" value="' . $this->unparsed_value . '" />';

		return $output;
	}
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 *
 * @param $param_settings
 * @param $param_value
 * @param $tag
 *
 * @since 4.4
 *
 * vc_filter: vc_param_group_render_filter
 *
 * @return mixed|void rendered template for params in edit form
 */
function vc_param_group_form_field( $param_settings, $param_value, $tag ) {
	$param_group = new Vc_ParamGroup( $param_settings, $param_value, $tag );

	return apply_filters( 'vc_param_group_render_filter', $param_group->render() );
}

add_action( 'wp_ajax_vc_param_group_clone', 'vc_param_group_clone' );

/**
 * @since 4.4
 */
function vc_param_group_clone() {
	vc_user_access()
		->checkAdminNonce()
		->validateDie()
		->wpAny( 'edit_posts', 'edit_pages' )
		->validateDie();

	$param = vc_post_param( 'param' );
	$value = vc_post_param( 'value' );
	$tag = vc_post_param( 'shortcode' );
	die( vc_param_group_clone_by_data( $tag, json_decode( urldecode( $param ), true ), json_decode( urldecode( $value ), true ) ) );
}

/**
 * @param $tag
 * @param $params
 * @param $data
 *
 * @since 4.4
 * @return mixed|string
 */
function vc_param_group_clone_by_data( $tag, $params, $data ) {

	$output = '';
	$params['base'] = $tag;
	$edit_form = new Vc_ParamGroup_Edit_Form_Fields( $params );
	$edit_form->loadDefaultParams();

	$template = vc_include_template( 'params/param_group/content.tpl.php' );
	$output .= $template;
	$value_block = "<div class='vc_param_group-wrapper vc_clearfix'>";

	$data = $data[0];
	if ( isset( $params['params'] ) && is_array( $params['params'] ) ) {
		foreach ( $params['params'] as $param ) {
			$param_data = isset( $data[ $param['param_name'] ] ) ? $data[ $param['param_name'] ] : ( isset( $param['value'] ) ? $param['value'] : '' );
			$param['param_name'] = $params['param_name'] . '_' . $param['param_name'];
			$value_block .= $edit_form->renderField( $param, $param_data );
		}
	}
	$value_block .= '</div>';
	$output = str_replace( '%content%', $value_block, $output );

	return $output;
}

/**
 * @param $atts_string
 *
 * @since 4.4
 * @return array|mixed
 */
function vc_param_group_parse_atts( $atts_string ) {
	$array = json_decode( urldecode( $atts_string ), true );

	return $array;
}

add_filter( 'vc_map_get_param_defaults', 'vc_param_group_param_defaults', 10, 2 );
function vc_param_group_param_defaults( $value, $param ) {
	if ( 'param_group' === $param['type'] && isset( $param['params'] ) && empty( $value ) ) {
		$defaults = vc_map_get_params_defaults( $param['params'] );
		$value = urlencode( json_encode( array( $defaults ) ) );
	}

	return $value;
}
