<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	'options' => array(
		array( true, __( 'Enabled', 'js_composer' ) ),
		array( false, __( 'Disabled', 'js_composer' ) ),
	),
	'main_label' => __( 'Page settings', 'js_composer' ),
	'description' => __( 'Control access to WPBakery Page Builder page settings. Note: Disable page settings to restrict editing of Custom CSS through page.', 'js_composer' ),
) );
