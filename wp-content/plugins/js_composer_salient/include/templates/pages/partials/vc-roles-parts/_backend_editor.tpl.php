<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	// 'custom_value' => array(true, 'default'),
		'capabilities' => array(
			array( 'disabled_ce_editor', __( 'Disable Classic editor', 'js_composer' ) ),
		),
		'options' => array(
		array( true, __( 'Enabled', 'js_composer' ) ),
		array( 'default', __( 'Enabled and default', 'js_composer' ) ),
		array( false, __( 'Disabled', 'js_composer' ) ),
		),
		'main_label' => __( 'Backend editor', 'js_composer' ),
		'custom_label' => __( 'Backend editor', 'js_composer' ),
) );
