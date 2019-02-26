<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_filter( 'vc_edit_form_fields_optional_params', 'vc_edit_for_fields_add_optional_params' );

if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_action( 'vc_edit_form_fields_after_render', 'vc_output_required_params_to_init' );
	add_filter( 'vc_edit_form_fields_optional_params', 'vc_edit_for_fields_add_optional_params' );
}

function vc_edit_for_fields_add_optional_params( $params ) {
	$arr = array(
		'hidden',
		'textfield',
		'dropdown',
		'checkbox',
		'posttypes',
		'taxonomies',
		'taxomonies',
		'exploded_textarea',
		'textarea_raw_html',
		'textarea_safe',
		'textarea',
		'attach_images',
		'attach_image',
		'widgetised_sidebars',
		'colorpicker',
		'loop',
		'vc_link',
		'sorted_list',
		'tab_id',
		'href',
		'custom_markup',
		'animation_style',
		'iconpicker',
		'el_id',
		'vc_grid_item',
		'google_fonts',
	);
	$params = array_values( array_unique( array_merge( $params, $arr ) ) );

	return $params;
}

function vc_output_required_params_to_init() {
	$params = WpbakeryShortcodeParams::getRequiredInitParams();

	$js_array = array();
	foreach ( $params as $param ) {
		$js_array[] = '"' . $param . '"';
	}

	echo '
		<script type="text/javascript">
			if ( window.vc ) {
				window.vc.required_params_to_init = [' . implode( ',', $js_array ) . '];
			}
		</script>
	';
}
