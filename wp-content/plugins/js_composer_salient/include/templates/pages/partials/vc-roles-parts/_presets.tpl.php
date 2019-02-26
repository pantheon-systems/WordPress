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
		array( true, __( 'All', 'js_composer' ) ),
		array( 'add', __( 'Apply presets only', 'js_composer' ) ),
		array( false, __( 'Disabled', 'js_composer' ) ),
	),
	'main_label' => __( 'Element Presets', 'js_composer' ),
	'description' => __( 'Control access rights to element presets in element edit form. Note: "Apply presets only" restricts users from saving new presets and deleting existing.', 'js_composer' ),
) );
