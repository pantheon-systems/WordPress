<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/* Call to action
 * @since 4.5
 */
require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$h2_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'h2_', __( 'Heading', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
	),
), array(
	'element' => 'use_custom_fonts_h2',
	'value' => 'true',
) );

// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h2_custom_heading ) && ! empty( $h2_custom_heading ) ) {
	foreach ( $h2_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h2_custom_heading[ $key ]['value'] = '';
			if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
				$sub_key = array_search( 'tag', $param['settings']['fields'] );
				if ( false !== $sub_key ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields']['tag'] );
				}
				$sub_key = array_search( 'text_align', $param['settings']['fields'] );
				if ( false !== $sub_key ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
					unset( $h2_custom_heading[ $key ]['settings']['fields']['text_align'] );
				}
			}
		}
	}
}
$h4_custom_heading = vc_map_integrate_shortcode( vc_custom_heading_element_params(), 'h4_', __( 'Subheading', 'js_composer' ), array(
	'exclude' => array(
		'source',
		'text',
		'css',
	),
), array(
	'element' => 'use_custom_fonts_h4',
	'value' => 'true',
) );

// This is needed to remove custom heading _tag and _align options.
if ( is_array( $h4_custom_heading ) && ! empty( $h4_custom_heading ) ) {
	foreach ( $h4_custom_heading as $key => $param ) {
		if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
			$h4_custom_heading[ $key ]['value'] = '';
			if ( isset( $param['settings'] ) && is_array( $param['settings'] ) && isset( $param['settings']['fields'] ) ) {
				$sub_key = array_search( 'tag', $param['settings']['fields'] );
				if ( false !== $sub_key ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['tag'] ) ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields']['tag'] );
				}
				$sub_key = array_search( 'text_align', $param['settings']['fields'] );
				if ( false !== $sub_key ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields'][ $sub_key ] );
				} elseif ( isset( $param['settings']['fields']['text_align'] ) ) {
					unset( $h4_custom_heading[ $key ]['settings']['fields']['text_align'] );
				}
			}
		}
	}
}
$params = array_merge( array(
	array(
		'type' => 'textfield',
		'heading' => __( 'Heading', 'js_composer' ),
		'admin_label' => true,
		'param_name' => 'h2',
		'value' => __( 'Hey! I am first heading line feel free to change me', 'js_composer' ),
		'description' => __( 'Enter text for heading line.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => __( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_fonts_h2',
		'description' => __( 'Enable Google fonts.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),

), $h2_custom_heading, array(
	array(
		'type' => 'textfield',
		'heading' => __( 'Subheading', 'js_composer' ),
		'param_name' => 'h4',
		'value' => '',
		'description' => __( 'Enter text for subheading line.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-9',
	),
	array(
		'type' => 'checkbox',
		'heading' => __( 'Use custom font?', 'js_composer' ),
		'param_name' => 'use_custom_fonts_h4',
		'description' => __( 'Enable custom font option.', 'js_composer' ),
		'edit_field_class' => 'vc_col-sm-3',
	),
), $h4_custom_heading, array(
	array(
		'type' => 'dropdown',
		'heading' => __( 'Text alignment', 'js_composer' ),
		'param_name' => 'txt_align',
		'value' => getVcShared( 'text align' ),
		// default left
		'description' => __( 'Select text alignment in "Call to Action" block.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Shape', 'js_composer' ),
		'param_name' => 'shape',
		'std' => 'rounded',
		'value' => array(
			__( 'Square', 'js_composer' ) => 'square',
			__( 'Rounded', 'js_composer' ) => 'rounded',
			__( 'Round', 'js_composer' ) => 'round',
		),
		'description' => __( 'Select call to action shape.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Style', 'js_composer' ),
		'param_name' => 'style',
		'value' => array(
			__( 'Classic', 'js_composer' ) => 'classic',
			__( 'Flat', 'js_composer' ) => 'flat',
			__( 'Outline', 'js_composer' ) => 'outline',
			__( '3d', 'js_composer' ) => '3d',
			__( 'Custom', 'js_composer' ) => 'custom',
		),
		'std' => 'classic',
		'description' => __( 'Select call to action display style.', 'js_composer' ),
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Background color', 'js_composer' ),
		'param_name' => 'custom_background',
		'description' => __( 'Select custom background color.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'custom' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
	),
	array(
		'type' => 'colorpicker',
		'heading' => __( 'Text color', 'js_composer' ),
		'param_name' => 'custom_text',
		'description' => __( 'Select custom text color.', 'js_composer' ),
		'dependency' => array(
			'element' => 'style',
			'value' => array( 'custom' ),
		),
		'edit_field_class' => 'vc_col-sm-6',
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Color', 'js_composer' ),
		'param_name' => 'color',
		'value' => array( __( 'Classic', 'js_composer' ) => 'classic' ) + getVcShared( 'colors-dashed' ),
		'std' => 'classic',
		'description' => __( 'Select color schema.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown vc_cta3-colored-dropdown',
		'dependency' => array(
			'element' => 'style',
			'value_not_equal_to' => array( 'custom' ),
		),
	),
	array(
		'type' => 'textarea_html',
		'heading' => __( 'Text', 'js_composer' ),
		'param_name' => 'content',
		'value' => __( 'I am promo text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Width', 'js_composer' ),
		'param_name' => 'el_width',
		'value' => array(
			'100%' => '',
			'90%' => 'xl',
			'80%' => 'lg',
			'70%' => 'md',
			'60%' => 'sm',
			'50%' => 'xs',
		),
		'description' => __( 'Select call to action width (percentage).', 'js_composer' ),
	),
	array(
		'type' => 'dropdown',
		'heading' => __( 'Add button', 'js_composer' ) . '?',
		'description' => __( 'Add button for call to action.', 'js_composer' ),
		'param_name' => 'add_button',
		'value' => array(
			__( 'No', 'js_composer' ) => '',
			__( 'Top', 'js_composer' ) => 'top',
			__( 'Bottom', 'js_composer' ) => 'bottom',
			__( 'Left', 'js_composer' ) => 'left',
			__( 'Right', 'js_composer' ) => 'right',
		),
	),
), vc_map_integrate_shortcode( 'vc_btn', 'btn_', __( 'Button', 'js_composer' ), array(
	'exclude' => array( 'css' ),
), array(
	'element' => 'add_button',
	'not_empty' => true,
) ), array(
	array(
		'type' => 'dropdown',
		'heading' => __( 'Add icon?', 'js_composer' ),
		'description' => __( 'Add icon for call to action.', 'js_composer' ),
		'param_name' => 'add_icon',
		'value' => array(
			__( 'No', 'js_composer' ) => '',
			__( 'Top', 'js_composer' ) => 'top',
			__( 'Bottom', 'js_composer' ) => 'bottom',
			__( 'Left', 'js_composer' ) => 'left',
			__( 'Right', 'js_composer' ) => 'right',
		),
	),
	array(
		'type' => 'checkbox',
		'param_name' => 'i_on_border',
		'heading' => __( 'Place icon on border?', 'js_composer' ),
		'description' => __( 'Display icon on call to action element border.', 'js_composer' ),
		'group' => __( 'Icon', 'js_composer' ),
		'dependency' => array(
			'element' => 'add_icon',
			'not_empty' => true,
		),
	),
), vc_map_integrate_shortcode( 'vc_icon', 'i_', __( 'Icon', 'js_composer' ), array(
	'exclude' => array(
		'align',
		'css',
	),
), array(
	'element' => 'add_icon',
	'not_empty' => true,
) ), array(
	/// cta3
	vc_map_add_css_animation(),
	array(
		'type' => 'el_id',
		'heading' => __( 'Element ID', 'js_composer' ),
		'param_name' => 'el_id',
		'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'js_composer' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
	),
	array(
		'type' => 'textfield',
		'heading' => __( 'Extra class name', 'js_composer' ),
		'param_name' => 'el_class',
		'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
	),
	array(
		'type' => 'css_editor',
		'heading' => __( 'CSS box', 'js_composer' ),
		'param_name' => 'css',
		'group' => __( 'Design Options', 'js_composer' ),
	),
) );

return array(
	'name' => __( 'Call to Action', 'js_composer' ),
	'base' => 'vc_cta',
	'icon' => 'icon-wpb-call-to-action',
	'category' => array( __( 'Content', 'js_composer' ) ),
	'description' => __( 'Catch visitors attention with CTA block', 'js_composer' ),
	'since' => '4.5',
	'params' => $params,
	'js_view' => 'VcCallToActionView3',
);
