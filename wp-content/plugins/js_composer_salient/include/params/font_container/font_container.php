<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Font_Container
 * @since 4.3
 * vc_map examples:
 *  array(
 *        'type' => 'font_container',
 *        'param_name' => 'font_container',
 *        'value'=>'',
 *        'settings'=>array(
 *            'fields'=>array(
 *                'tag'=>'h2',
 *                'text_align',
 *                'font_size',
 *                'line_height',
 *                'color',
 *
 *                'tag_description' => __('Select element tag.','js_composer'),
 *                'text_align_description' => __('Select text alignment.','js_composer'),
 *                'font_size_description' => __('Enter font size.','js_composer'),
 *                'line_height_description' => __('Enter line height.','js_composer'),
 *                'color_description' => __('Select color for your element.','js_composer'),
 *            ),
 *        ),
 *    ),
 *  Ordering of fields, font_family, tag, text_align and etc. will be Same as ordering in array!
 *  To provide default value to field use 'key' => 'value'
 */
class Vc_Font_Container {

	/**
	 * @param $settings
	 * @param $value
	 *
	 * @return string
	 */
	public function render( $settings, $value ) {
		$fields = array();
		$values = array();
		extract( $this->_vc_font_container_parse_attributes( $settings['settings']['fields'], $value ) );

		$data = array();
		$output = '';
		if ( ! empty( $fields ) ) {
			if ( isset( $fields['tag'] ) ) {
				$data['tag'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Element tag', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-tag-container">
                        <select class="vc_font_container_form_field-tag-select">';
				$tags = $this->_vc_font_container_get_allowed_tags();
				foreach ( $tags as $tag ) {
					$data['tag'] .= '<option value="' . $tag . '" class="' . $tag . '" ' . ( $values['tag'] == $tag ? 'selected' : '' ) . '>' . $tag . '</option>';
				}
				$data['tag'] .= '
                        </select>
                    </div>';
				if ( isset( $fields['tag_description'] ) && strlen( $fields['tag_description'] ) > 0 ) {
					$data['tag'] .= '
                    <span class="vc_description clear">' . $fields['tag_description'] . '</span>
                    ';
				}

				$data['tag'] .= '</div>';
			}
			if ( isset( $fields['font_size'] ) ) {
				$data['font_size'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Font size', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-font_size-container">
                        <input class="vc_font_container_form_field-font_size-input" type="text" value="' . $values['font_size'] . '" />
                    </div>';

				if ( isset( $fields['font_size_description'] ) && strlen( $fields['font_size_description'] ) > 0 ) {
					$data['font_size'] .= '
                    <span class="vc_description clear">' . $fields['font_size_description'] . '</span>
                    ';
				}
				$data['font_size'] .= '</div>';
			}
			if ( isset( $fields['text_align'] ) ) {
				$data['text_align'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Text align', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-text_align-container">
                        <select class="vc_font_container_form_field-text_align-select">
                            <option value="left" class="left" ' . ( 'left' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'left', 'js_composer' ) . '</option>
                            <option value="right" class="right" ' . ( 'right' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'right', 'js_composer' ) . '</option>
                            <option value="center" class="center" ' . ( 'center' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'center', 'js_composer' ) . '</option>
                            <option value="justify" class="justify" ' . ( 'justify' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'justify', 'js_composer' ) . '</option>
                        </select>
                    </div>';
				if ( isset( $fields['text_align_description'] ) && strlen( $fields['text_align_description'] ) > 0 ) {
					$data['text_align'] .= '
                    <span class="vc_description clear">' . $fields['text_align_description'] . '</span>
                    ';
				}
				$data['text_align'] .= '</div>';
			}
			if ( isset( $fields['line_height'] ) ) {
				$data['line_height'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Line height', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-line_height-container">
                        <input class="vc_font_container_form_field-line_height-input"  type="text"  value="' . $values['line_height'] . '" />
                    </div>';
				if ( isset( $fields['line_height_description'] ) && strlen( $fields['line_height_description'] ) > 0 ) {
					$data['line_height'] .= '
                    <span class="vc_description clear">' . $fields['line_height_description'] . '</span>
                    ';
				}
				$data['line_height'] .= '</div>';
			}
			if ( isset( $fields['color'] ) ) {
				$data['color'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Text color', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-color-container">
                        <div class="color-group">
                            <input type="text" value="' . $values['color'] . '" class="vc_font_container_form_field-color-input vc_color-control" />
                        </div>
                    </div>';
				if ( isset( $fields['color_description'] ) && strlen( $fields['color_description'] ) > 0 ) {
					$data['color'] .= '
                    <span class="vc_description clear">' . $fields['color_description'] . '</span>
                    ';
				}
				$data['color'] .= '</div>';
			}
			if ( isset( $fields['font_family'] ) ) {
				$data['font_family'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Font Family', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-font_family-container">
                        <select class="vc_font_container_form_field-font_family-select">';
				$fonts = $this->_vc_font_container_get_web_safe_fonts();
				foreach ( $fonts as $font_name => $font_data ) {
					$data['font_family'] .= '<option value="' . $font_name . '" class="' . vc_build_safe_css_class( $font_name ) . '" ' . ( strtolower( $values['font_family'] ) == strtolower( $font_name ) ? 'selected' : '' ) . ' data[font_family]="' . urlencode( $font_data ) . '">' . $font_name . '</option>';
				}
				$data['font_family'] .= '
                        </select>
                    </div>';
				if ( isset( $fields['font_family_description'] ) && strlen( $fields['font_family_description'] ) > 0 ) {
					$data['font_family'] .= '
                    <span class="vc_description clear">' . $fields['font_family_description'] . '</span>
                    ';
				}
				$data['font_family'] .= '</div>';
			}
			if ( isset( $fields['font_style'] ) ) {
				$data['font_style'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Font style', 'js_composer' ) . '</div>
                    <div class="vc_font_container_form_field-font_style-container">
                        <label>
                            <input type="checkbox" class="vc_font_container_form_field-font_style-checkbox italic" value="italic" ' . ( '1' === $values['font_style_italic'] ? 'checked' : '' ) . '><span class="vc_font_container_form_field-font_style-label italic">' . __( 'italic', 'js_composer' ) . '</span>
                         </label>
                        <br />
                        <label>
                            <input type="checkbox" class="vc_font_container_form_field-font_style-checkbox bold" value="bold" ' . ( '1' === $values['font_style_bold'] ? 'checked' : '' ) . '><span class="vc_font_container_form_field-font_style-label bold">' . __( 'bold', 'js_composer' ) . '</span>
                        </label>
                    </div>';
				if ( isset( $fields['font_style_description'] ) && strlen( $fields['font_style_description'] ) > 0 ) {
					$data['font_style'] .= '
                    <span class="vc_description clear">' . $fields['font_style_description'] . '</span>
                    ';
				}
				$data['font_style'] .= '</div>';
			}
			$data = apply_filters( 'vc_font_container_output_data', $data, $fields, $values, $settings );
			// combine all in output, make sure you follow ordering
			foreach ( $fields as $key => $field ) {
				if ( isset( $data[ $key ] ) ) {
					$output .= $data[ $key ];
				}
			}
		}
		$output .= '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value  ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="' . $value . '" />';

		return $output;
	}

	/**
	 * If field 'font_family' is used this is list of fonts available
	 * To modify this list, you should use add_filter('vc_font_container_get_fonts_filter','your_custom_function');
	 * vc_filter: vc_font_container_get_fonts_filter - to modify list of fonts
	 * @return array list of fonts
	 */
	public function _vc_font_container_get_web_safe_fonts() {
		// this is "Web Safe FONTS" from w3c: http://www.w3schools.com/cssref/css_websafe_fonts.asp
		$web_fonts = array(
			'Georgia' => 'Georgia, serif',
			'Palatino Linotype' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'Book Antiqua' => '"Book Antiqua", Palatino, serif',
			'Palatino' => 'Palatino, serif',
			'Times New Roman' => '"Times New Roman", Times, serif',
			'Arial' => 'Arial, Helvetica, sans-serif',
			'Arial Black' => '"Arial Black", Gadget, sans-serif',
			'Helvetica' => 'Helvetica, sans-serif',
			'Comic Sans MS' => '"Comic Sans MS", cursive, sans-serif',
			'Impact' => 'Impact, Charcoal, sans-serif',
			'Charcoal' => 'Charcoal, sans-serif',
			'Lucida Sans Unicode' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Lucida Grande' => '"Lucida Grande", sans-serif',
			'Tahoma' => 'Tahoma, Geneva, sans-serif',
			'Geneva' => 'Geneva, sans-serif',
			'Trebuchet MS' => '"Trebuchet MS", Helvetica, sans-serif',
			'Verdana' => '"Trebuchet MS", Helvetica, sans-serif',
			'Courier New' => '"Courier New", Courier, monospace',
			'Lucida Console' => '"Lucida Console", Monaco, monospace',
			'Monaco' => 'Monaco, monospace',
		);

		return apply_filters( 'vc_font_container_get_fonts_filter', $web_fonts );
	}

	/**
	 * If 'tag' field used this is list of allowed tags
	 * To modify this list, you should use add_filter('vc_font_container_get_allowed_tags','your_custom_function');
	 * vc_filter: vc_font_container_get_allowed_tags - to modify list of allowed tags by default
	 * @return array list of allowed tags
	 */
	public function _vc_font_container_get_allowed_tags() {
		$allowed_tags = array(
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'div',
		);

		return apply_filters( 'vc_font_container_get_allowed_tags', $allowed_tags );

	}

	/**
	 * @param $attr
	 * @param $value
	 *
	 * @return array
	 */
	public function _vc_font_container_parse_attributes( $attr, $value ) {
		$fields = array();
		if ( isset( $attr ) ) {
			foreach ( $attr as $key => $val ) {
				if ( is_numeric( $key ) ) {
					$fields[ $val ] = '';
				} else {
					$fields[ $key ] = $val;
				}
			}
		}

		$values = vc_parse_multi_attribute( $value, array(
				'tag' => isset( $fields['tag'] ) ? $fields['tag'] : 'h2',
				'font_size' => isset( $fields['font_size'] ) ? $fields['font_size'] : '',
				'font_style_italic' => isset( $fields['font_style_italic'] ) ? $fields['font_style_italic'] : '',
				'font_style_bold' => isset( $fields['font_style_bold'] ) ? $fields['font_style_bold'] : '',
				'font_family' => isset( $fields['font_family'] ) ? $fields['font_family'] : '',
				'color' => isset( $fields['color'] ) ? $fields['color'] : '',
				'line_height' => isset( $fields['line_height'] ) ? $fields['line_height'] : '',
				'text_align' => isset( $fields['text_align'] ) ? $fields['text_align'] : 'left',
				'tag_description' => isset( $fields['tag_description'] ) ? $fields['tag_description'] : '',
				'font_size_description' => isset( $fields['font_size_description'] ) ? $fields['font_size_description'] : '',
				'font_style_description' => isset( $fields['font_style_description'] ) ? $fields['font_style_description'] : '',
				'font_family_description' => isset( $fields['font_family_description'] ) ? $fields['font_family_description'] : '',
				'color_description' => isset( $fields['color_description'] ) ? $fields['color_description'] : 'left',
				'line_height_description' => isset( $fields['line_height_description'] ) ? $fields['line_height_description'] : '',
				'text_align_description' => isset( $fields['text_align_description'] ) ? $fields['text_align_description'] : '',
			)
		);

		return array( 'fields' => $fields, 'values' => $values );
	}
}

/**
 * @param $settings
 * @param $value
 *
 * @return mixed|void
 */
function vc_font_container_form_field( $settings, $value ) {
	$font_container = new Vc_Font_Container();

	return apply_filters( 'vc_font_container_render_filter', $font_container->render( $settings, $value ) );
}
