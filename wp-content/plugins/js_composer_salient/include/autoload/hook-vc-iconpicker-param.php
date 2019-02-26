<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/***
 * @since 4.4
 * Hook Vc-Iconpicker-Param.php
 *
 * Adds actions and filters for iconpicker param.
 * Used to:
 *  - register/enqueue icons fonts for admin pages
 *  - register/enqueue js for iconpicker param
 *  - register/enqueue css for iconpicker param
 */

// @see Vc_Base::frontCss, used to append actions when frontCss(frontend editor/and real view mode) method called
// This action registers all styles(fonts) to be enqueue later
add_action( 'vc_base_register_front_css', 'vc_iconpicker_base_register_css' );

// @see Vc_Base::registerAdminCss, used to append action when registerAdminCss(backend editor) method called
// This action registers all styles(fonts) to be enqueue later
add_action( 'vc_base_register_admin_css', 'vc_iconpicker_base_register_css' );

// @see Vc_Base::registerAdminJavascript, used to append action when registerAdminJavascript(backend/frontend editor) method called
// This action will register needed js file, and also you can use it for localizing js.
add_action( 'vc_base_register_admin_js', 'vc_iconpicker_base_register_js' );

// @see Vc_Backend_Editor::printScriptsMessages (wp-content/plugins/js_composer/include/classes/editors/class-vc-backend-editor.php),
// used to enqueue needed js/css files when backend editor is rendering
add_action( 'vc_backend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );
// @see Vc_Frontend_Editor::enqueueAdmin (wp-content/plugins/js_composer/include/classes/editors/class-vc-frontend-editor.php),
// used to enqueue needed js/css files when frontend editor is rendering
add_action( 'vc_frontend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );

/**
 * This action registers all styles(fonts) to be enqueue later
 * @see filter 'vc_base_register_front_css' - preview/frontend-editor
 *      filter 'vc_base_register_admin_css' - backend editor
 *
 * @since 4.4
 */
function vc_iconpicker_base_register_css() {
	// Vc Icon picker fonts:
	/* nectar addition */
	/*
	wp_register_style( 'font-awesome', vc_asset_url( 'lib/bower/font-awesome/css/font-awesome.min.css' ), array(), WPB_VC_VERSION );
	*/
	/* nectar addition end */
	wp_register_style( 'vc_typicons', vc_asset_url( 'css/lib/typicons/src/font/typicons.min.css' ), false, WPB_VC_VERSION );
	wp_register_style( 'vc_openiconic', vc_asset_url( 'css/lib/vc-open-iconic/vc_openiconic.min.css' ), false, WPB_VC_VERSION );
	wp_register_style( 'vc_linecons', vc_asset_url( 'css/lib/vc-linecons/vc_linecons_icons.min.css' ), false, WPB_VC_VERSION );
	wp_register_style( 'vc_entypo', vc_asset_url( 'css/lib/vc-entypo/vc_entypo.min.css' ), false, WPB_VC_VERSION );
	wp_register_style( 'vc_monosocialiconsfont', vc_asset_url( 'css/lib/monosocialiconsfont/monosocialiconsfont.min.css' ), false, WPB_VC_VERSION );
	/* nectar addition */
	/*
	wp_register_style( 'vc_material', vc_asset_url( 'css/lib/vc-material/vc_material.min.css' ), false, WPB_VC_VERSION );
	*/
	/* nectar addition end */
	// Theme
	wp_register_style( 'vc-icon-picker-main-css', vc_asset_url( 'lib/bower/vcIconPicker/css/jquery.fonticonpicker.min.css' ), false, WPB_VC_VERSION );
	wp_register_style( 'vc-icon-picker-main-css-theme', vc_asset_url( 'lib/bower/vcIconPicker/themes/grey-theme/jquery.fonticonpicker.vcgrey.min.css' ), false, WPB_VC_VERSION );
}

/**
 * Register admin js for iconpicker functionality
 *
 * @since 4.4
 */
function vc_iconpicker_base_register_js() {
	wp_register_script( 'vc-icon-picker', vc_asset_url( 'lib/bower/vcIconPicker/jquery.fonticonpicker.min.js' ), array( 'jquery' ), WPB_VC_VERSION );
}

/**
 * Enqueue ALL fonts/styles for Editor(admin) mode. (to allow easy change icons)
 * - To append your icons fonts add action:
 *  vc_backend_editor_enqueue_jscss and vc_frontend_editor_enqueue_jscss
 *
 * @since 4.4
 */
function vc_iconpicker_editor_jscss() {
	// Enqueue js and theme css files
	wp_enqueue_script( 'vc-icon-picker' );
	wp_enqueue_style( 'vc-icon-picker-main-css' );
	wp_enqueue_style( 'vc-icon-picker-main-css-theme' );

	// Fonts
	wp_enqueue_style( 'font-awesome' );
	wp_enqueue_style( 'vc_openiconic' );
	wp_enqueue_style( 'vc_typicons' );
	wp_enqueue_style( 'vc_entypo' );
	wp_enqueue_style( 'vc_linecons' );
	wp_enqueue_style( 'vc_monosocialiconsfont' );
	/* nectar addition */
	/*
	wp_enqueue_style( 'vc_material' );
	*/
	/* nectar addition end */
}
