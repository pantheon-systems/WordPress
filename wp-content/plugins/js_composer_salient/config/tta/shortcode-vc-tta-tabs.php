<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Tabs', 'js_composer' ),
	'base' => 'vc_tta_tabs',
	'icon' => 'icon-wpb-ui-tab-content',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'category' => __( 'Content', 'js_composer' ),
	'description' => __( 'Tabbed content', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'param_name' => 'title',
			'heading' => __( 'Widget title', 'js_composer' ),
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'style',
			'value' => array(
				__( 'Classic', 'js_composer' ) => 'classic',
				__( 'Modern', 'js_composer' ) => 'modern',
				__( 'Flat', 'js_composer' ) => 'flat',
				__( 'Outline', 'js_composer' ) => 'outline',
			),
			'heading' => __( 'Style', 'js_composer' ),
			'description' => __( 'Select tabs display style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'shape',
			'value' => array(
				__( 'Rounded', 'js_composer' ) => 'rounded',
				__( 'Square', 'js_composer' ) => 'square',
				__( 'Round', 'js_composer' ) => 'round',
			),
			'heading' => __( 'Shape', 'js_composer' ),
			'description' => __( 'Select tabs shape.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'color',
			'heading' => __( 'Color', 'js_composer' ),
			'description' => __( 'Select tabs color.', 'js_composer' ),
			'value' => getVcShared( 'colors-dashed' ),
			'std' => 'grey',
			'param_holder_class' => 'vc_colored-dropdown',
		),
		array(
			'type' => 'checkbox',
			'param_name' => 'no_fill_content_area',
			'heading' => __( 'Do not fill content area?', 'js_composer' ),
			'description' => __( 'Do not fill content area with color.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'spacing',
			'value' => array(
				__( 'None', 'js_composer' ) => '',
				'1px' => '1',
				'2px' => '2',
				'3px' => '3',
				'4px' => '4',
				'5px' => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			),
			'heading' => __( 'Spacing', 'js_composer' ),
			'description' => __( 'Select tabs spacing.', 'js_composer' ),
			'std' => '1',
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'gap',
			'value' => array(
				__( 'None', 'js_composer' ) => '',
				'1px' => '1',
				'2px' => '2',
				'3px' => '3',
				'4px' => '4',
				'5px' => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			),
			'heading' => __( 'Gap', 'js_composer' ),
			'description' => __( 'Select tabs gap.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'tab_position',
			'value' => array(
				__( 'Top', 'js_composer' ) => 'top',
				__( 'Bottom', 'js_composer' ) => 'bottom',
			),
			'heading' => __( 'Position', 'js_composer' ),
			'description' => __( 'Select tabs navigation position.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'alignment',
			'value' => array(
				__( 'Left', 'js_composer' ) => 'left',
				__( 'Right', 'js_composer' ) => 'right',
				__( 'Center', 'js_composer' ) => 'center',
			),
			'heading' => __( 'Alignment', 'js_composer' ),
			'description' => __( 'Select tabs section title alignment.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'autoplay',
			'value' => array(
				__( 'None', 'js_composer' ) => 'none',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'10' => '10',
				'20' => '20',
				'30' => '30',
				'40' => '40',
				'50' => '50',
				'60' => '60',
			),
			'std' => 'none',
			'heading' => __( 'Autoplay', 'js_composer' ),
			'description' => __( 'Select auto rotate for tabs in seconds (Note: disabled by default).', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'param_name' => 'active_section',
			'heading' => __( 'Active section', 'js_composer' ),
			'value' => 1,
			'description' => __( 'Enter active section number (Note: to have all sections closed on initial load enter non-existing number).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'pagination_style',
			'value' => array(
				__( 'None', 'js_composer' ) => '',
				__( 'Square Dots', 'js_composer' ) => 'outline-square',
				__( 'Radio Dots', 'js_composer' ) => 'outline-round',
				__( 'Point Dots', 'js_composer' ) => 'flat-round',
				__( 'Fill Square Dots', 'js_composer' ) => 'flat-square',
				__( 'Rounded Fill Square Dots', 'js_composer' ) => 'flat-rounded',
			),
			'heading' => __( 'Pagination style', 'js_composer' ),
			'description' => __( 'Select pagination style.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'param_name' => 'pagination_color',
			'value' => getVcShared( 'colors-dashed' ),
			'heading' => __( 'Pagination color', 'js_composer' ),
			'description' => __( 'Select pagination color.', 'js_composer' ),
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'grey',
			'dependency' => array(
				'element' => 'pagination_style',
				'not_empty' => true,
			),
		),
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
			'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
	),
	'js_view' => 'VcBackendTtaTabsView',
	'custom_markup' => '
<div class="vc_tta-container" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container">'
	                   . '<ul class="vc_tta-tabs-list">'
	                   . '<li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>'
	                   . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_section title="' . sprintf( '%s %d', __( 'Tab', 'js_composer' ), 1 ) . '"][/vc_tta_section]
[vc_tta_section title="' . sprintf( '%s %d', __( 'Tab', 'js_composer' ), 2 ) . '"][/vc_tta_section]
	',
	/* nectar addition */ 
	/*
	'admin_enqueue_js' => array(
		vc_asset_url( 'lib/vc_tabs/vc-tabs.min.js' ),
	), */
	/* nectar addition end */ 
);
