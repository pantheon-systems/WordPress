<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	'custom_value' => 'custom',
	'capabilities' => WPBMap::getSortedAllShortCodes(),
	'ignore_capabilities' => array(
		'vc_gitem',
		'vc_gitem_animated_block',
		'vc_gitem_zone',
		'vc_gitem_zone_a',
		'vc_gitem_zone_b',
		'vc_gitem_zone_c',
		'vc_column',
		'vc_row_inner',
		'vc_column_inner',
		'vc_posts_grid',
	),
	'categories' => WPBMap::getCategories(),
	'cap_types' => array(
		array( 'all', __( 'All', 'js_composer' ) ),
		array( 'edit', __( 'Edit', 'js_composer' ) ),
	),
	'item_header_name' => __( 'Element', 'js_composer' ),
	'options' => array(
		array( true, __( 'All', 'js_composer' ) ),
		array( 'edit', __( 'Edit only', 'js_composer' ) ),
		array( 'custom', __( 'Custom', 'js_composer' ) ),
	),
	'main_label' => __( 'Elements', 'js_composer' ),
	'custom_label' => __( 'Elements', 'js_composer' ),
	'description' => __( 'Control user access to content elements.', 'js_composer' ),
	'use_table' => true,
) );
