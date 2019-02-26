<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * An example of how to write WPBakery WPBakery Page Builder custom shortcode
 *
 * To create shortcodes for WPBakery Page Builder you need to complete 2 steps.
 *
 * 1. Create new class which extends WPBakeryShortCode.
 * If you are not familiar with OOP in php, don't worry follow this instruction and we will guide you how to
 * create valid shortcode for WPBakery Page Builder without learning OOP.
 *
 * 2. Need to create configurations by using wpb_map function.
 *
 * Shortcode class.
 * Shortcode class extends WPBakeryShortCode abstract class.
 * Correct name for shortcode class should look like WPBakeryShortCode_YOUR_SHORTCODE_HERE.
 * YOUR_SHORTCODE_HERE must contain only latin letters, numbers and symbol "_".
 */

/**
 * Shortcode class example "Hello World"
 *
 * Lets pretend that we want to create shortcode with this structure: [my_hello_world foo="bar"]Shortcode content
 * here[/my_hello_world]
 */
class WPBakeryShortCode_my_hello_world extends WPBakeryShortCode {

	/*
	 * Thi methods returns HTML code for frontend representation of your shortcode.
	 * You can use your own html markup.
	 *
	 * @param $atts - shortcode attributes
	 * @param @content - shortcode content
	 *
	 * @access protected
	 * vc_filter: VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG vc_shortcodes_css_class - hook to edit element class
	 * @return string
	 */
	protected function content( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'width' => '1/2',
			'el_position' => '',
			'foo' => '',
			'my_dropdown' => '',
		), $atts ) );

		$width_class = '';
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $width_class, $this->settings['base'], $atts );
		$output = '<div class="' . $css_class . '">';
		$output .= '<h3>' . $foo . '</h3>';
		$output .= wpb_js_remove_wpautop( $content, true );
		$output .= '<p> Dropdown: ' . $my_dropdown . '</p>';
		$output .= '</div>';

		return $output;
	}
}

/*
 * Settings array to setup shortcode "Hello world"
 * base param is required.
 *
 * Mapping examples: $PLUGIN_DIR/config/map.php
 *
 * name - used in content elements menu and shortcode edit screen.
 * base - shortcode base. Example my_hello_world
 * class - helper class to target your shortcode in css in WPBakery Page Builder edit mode
 * icon - in order to add icon for your shortcode in dropdown menu, add class name here and style it in
 *          your own css file. Note: bootstrap icons supported.
 * controls - in WPBakery Page Builder mode shortcodes can have different controls (popup_delete, edit_popup_delete, size_delete, popup_delete, full).
 				Default is full.
 * params - array which holds your shortcode params. This params will be editable in shortcode settings page.
 *
 * Available param types:
 *
 * textarea_html (only one html textarea is permitted per shortcode)
 * textfield - simple input field,
 * dropdown - dropdown element with set of available options,
 * attach_image - single image selection,
 * attach_images - multiple images selection,
 * exploded_textarea - textarea, where each line will be imploded with comma (,),
 * posttypes - checkboxes with available post types,
 * widgetised_sidebars - dropdown element with set of available widget regions,
 * textarea - simple textarea,
 * textarea_raw_html - textarea, it's content will be codede into base64 (this allows you to store raw js or raw html code).
 *
 */

vc_map( array(
	'base' => 'my_hello_world',
	'name' => __( 'Hello World', 'js_composer' ),
	'class' => '',
	'icon' => 'icon-heart',
	'params' => array(
		array(
			'type' => 'textfield',
			'holder' => 'h3',
			'class' => '',
			'heading' => __( 'Foo attribute', 'js_composer' ),
			'param_name' => 'foo',
			'value' => __( "I'm foo attribute", 'js_composer' ),
			'description' => __( 'Enter foo value.', 'js_composer' ),
		),
		array(
			'type' => 'textarea_html',
			'holder' => 'div',
			'class' => '',
			'heading' => __( 'Text', 'js_composer' ),
			'param_name' => 'content',
			'value' => __( "I'm hello world", 'js_composer' ),
			'description' => __( 'Enter your content.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Drop down example', 'js_composer' ),
			'param_name' => 'my_dropdown',
			'value' => array( 1, 2, 'three' ),
			'description' => __( 'One, two or three?', 'js_composer' ),
		),
	),
) );

