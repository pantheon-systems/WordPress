<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => __( 'Old Tabs', 'js_composer' ),
	'base' => 'vc_tabs',
	'show_settings_on_create' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-ui-tab-content',
	'category' => __( 'Content', 'js_composer' ),
	'deprecated' => '4.6',
	'description' => __( 'Tabbed content', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => __( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'description' => __( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Auto rotate', 'js_composer' ),
			'param_name' => 'interval',
			'value' => array(
				__( 'Disable', 'js_composer' ) => 0,
				3,
				5,
				10,
				15,
			),
			'std' => 0,
			'description' => __( 'Auto rotate tabs each X seconds.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
	'custom_markup' => '
<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
<ul class="tabs_controls">
</ul>
%content%
</div>',
	'default_content' => '
[vc_tab title="' . __( 'Tab 1', 'js_composer' ) . '" tab_id=""][/vc_tab]
[vc_tab title="' . __( 'Tab 2', 'js_composer' ) . '" tab_id=""][/vc_tab]
',
	'js_view' => 'VcTabsView',
);
