<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$tabs = array();
foreach ( vc_settings()->getTabs() as $tab => $title ) {
	$tabs[] = array( $tab . '-tab', $title );
}
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	'custom_value' => 'custom',
	'capabilities' => $tabs,
	'options' => array(
		array( true, __( 'All', 'js_composer' ) ),
		array( 'custom', __( 'Custom', 'js_composer' ) ),
		array( false, __( 'Disabled', 'js_composer' ) ),
	),
	'main_label' => __( 'Settings options', 'js_composer' ),
	'custom_label' => __( 'Settings options', 'js_composer' ),
	'description' => __( 'Control access rights to WPBakery Page Builder admin settings tabs (e.g. General Settings, Shortcode Mapper, ...)', 'js_composer' ),
) );
