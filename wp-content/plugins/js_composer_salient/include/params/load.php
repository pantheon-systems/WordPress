<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder shortcode attributes fields loader
 *
 * @package WPBakeryPageBuilder
 *
 */
require_once vc_path_dir( 'PARAMS_DIR', '/default_params.php' );

/**
 * Loads attributes hooks.
 */
require_once vc_path_dir( 'PARAMS_DIR', '/textarea_html/textarea_html.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/colorpicker/colorpicker.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/loop/loop.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/vc_link/vc_link.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/options/options.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/sorted_list/sorted_list.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/css_editor/css_editor.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/tab_id/tab_id.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/href/href.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/font_container/font_container.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/google_fonts/google_fonts.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/column_offset/column_offset.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/autocomplete/autocomplete.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/params_preset/params_preset.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/param_group/param_group.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/custom_markup/custom_markup.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/animation_style/animation_style.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/iconpicker/iconpicker.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/el_id/el_id.php' );
require_once vc_path_dir( 'PARAMS_DIR', '/gutenberg/gutenberg.php' );

global $vc_params_list;
$vc_params_list = array(
	// Default
	'textfield',
	'dropdown',
	'textarea_html',
	'checkbox',
	'posttypes',
	'taxonomies',
	'taxomonies',
	'exploded_textarea',
	'exploded_textarea_safe',
	'textarea_raw_html',
	'textarea_safe',
	'textarea',
	'attach_images',
	'attach_image',
	'widgetised_sidebars',
	// Advanced
	'colorpicker',
	'loop',
	'vc_link',
	'options',
	'sorted_list',
	'css_editor',
	'font_container',
	'google_fonts',
	'autocomplete',
	'tab_id',
	'href',
	'params_preset',
	'param_group',
	'custom_markup',
	'animation_style',
	'iconpicker',
	'el_id',
	'gutenberg',
);
