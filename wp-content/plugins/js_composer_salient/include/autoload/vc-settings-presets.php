<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'wp_ajax_vc_action_save_settings_preset', 'vc_action_save_settings_preset' );
add_action( 'wp_ajax_vc_action_set_as_default_settings_preset', 'vc_action_set_as_default_settings_preset' );
add_action( 'wp_ajax_vc_action_delete_settings_preset', 'vc_action_delete_settings_preset' );
add_action( 'wp_ajax_vc_action_restore_default_settings_preset', 'vc_action_restore_default_settings_preset' );
add_action( 'wp_ajax_vc_action_get_settings_preset', 'vc_action_get_settings_preset' );
add_action( 'wp_ajax_vc_action_render_settings_preset_popup', 'vc_action_render_settings_preset_popup' );
add_action( 'wp_ajax_vc_action_render_settings_preset_title_prompt', 'vc_action_render_settings_preset_title_prompt' );
add_action( 'wp_ajax_vc_action_render_settings_templates_prompt', 'vc_action_render_settings_templates_prompt' );
add_action( 'vc_restore_default_settings_preset', 'vc_action_set_as_default_settings_preset', 10, 2 );
add_action( 'vc_register_settings_preset', 'vc_register_settings_preset', 10, 4 );
add_filter( 'vc_add_new_elements_to_box', 'vc_add_new_elements_to_box' );
add_filter( 'vc_add_new_category_filter', 'vc_add_new_category_filter' );

function vc_include_settings_preset_class() {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
}

/**
 * @return Vc_Vendor_Preset
 */
function vc_vendor_preset() {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-vendor-presets.php' );

	return Vc_Vendor_Preset::getInstance();
}

/**
 * Save settings preset for specific shortcode
 *
 * Include freshly rendered html in response
 *
 * Required _POST params:
 * - shortcode_name string
 * - title string
 * - data string params in json
 * - is_default
 *
 * @since 4.7
 */
function vc_action_save_settings_preset() {
	vc_include_settings_preset_class();
	vc_user_access()->part( 'presets' )->checkStateAny( true, null )->validateDie(); // user must have permission to save presets

	$id = Vc_Settings_Preset::saveSettingsPreset( vc_post_param( 'shortcode_name' ), vc_post_param( 'title' ), vc_post_param( 'data' ), vc_post_param( 'is_default' ) );

	$response = array(
		'success' => (bool) $id,
		'html' => Vc_Settings_Preset::getRenderedSettingsPresetPopup( vc_post_param( 'shortcode_name' ) ),
		'id' => $id,
	);

	wp_send_json( $response );
}

/**
 * Set existing preset as default
 *
 * Include freshly rendered html in response
 *
 * Required _POST params:
 * - id int
 * - shortcode_name string
 *
 * @since 4.7
 */
function vc_action_set_as_default_settings_preset() {
	vc_include_settings_preset_class();
	vc_user_access()->part( 'presets' )->checkStateAny( true, null )->validateDie(); // user must have permission to set as default presets

	$id = vc_post_param( 'id' );
	$shortcode_name = vc_post_param( 'shortcode_name' );

	$status = Vc_Settings_Preset::setAsDefaultSettingsPreset( $id, $shortcode_name );

	$response = array(
		'success' => $status,
		'html' => Vc_Settings_Preset::getRenderedSettingsPresetPopup( $shortcode_name ),
	);

	wp_send_json( $response );
}

/**
 * Unmark current default preset as default
 *
 * Include freshly rendered html in response
 *
 * Required _POST params:
 * - shortcode_name string
 *
 * @since 4.7
 */
function vc_action_restore_default_settings_preset() {
	vc_include_settings_preset_class();
	vc_user_access()->part( 'presets' )->checkStateAny( true, null )->validateDie(); // user must have permission to restore presets

	$shortcode_name = vc_post_param( 'shortcode_name' );

	$status = Vc_Settings_Preset::setAsDefaultSettingsPreset( null, $shortcode_name );

	$response = array(
		'success' => $status,
		'html' => Vc_Settings_Preset::getRenderedSettingsPresetPopup( $shortcode_name ),
	);

	wp_send_json( $response );
}

/**
 * Delete specific settings preset
 *
 * Include freshly rendered html in response
 *
 * Required _POST params:
 * - shortcode_name string
 * - id int
 *
 * @since 4.7
 */
function vc_action_delete_settings_preset() {
	vc_include_settings_preset_class();
	vc_user_access()->part( 'presets' )->checkStateAny( true, null )->validateDie(); // user must have permission to delete presets

	$default = get_post_meta( vc_post_param( 'id' ), '_vc_default', true );

	$status = Vc_Settings_Preset::deleteSettingsPreset( vc_post_param( 'id' ) );

	$response = array(
		'success' => $status,
		'default' => $default,
		'html' => Vc_Settings_Preset::getRenderedSettingsPresetPopup( vc_post_param( 'shortcode_name' ) ),
	);

	wp_send_json( $response );
}

/**
 * Get data for specific settings preset
 *
 * Required _POST params:
 * - id int
 *
 * @since 4.7
 */
function vc_action_get_settings_preset() {
	vc_include_settings_preset_class();

	$data = Vc_Settings_Preset::getSettingsPreset( vc_post_param( 'id' ), true );

	if ( false !== $data ) {
		$response = array(
			'success' => true,
			'data' => $data,
		);
	} else {
		$response = array(
			'success' => false,
		);
	}

	wp_send_json( $response );
}

/**
 * Respond with rendered popup menu
 *
 * Required _POST params:
 * - shortcode_name string
 *
 * @since 4.7
 */
function vc_action_render_settings_preset_popup() {
	vc_include_settings_preset_class();
	$html = Vc_Settings_Preset::getRenderedSettingsPresetPopup( vc_post_param( 'shortcode_name' ) );

	$response = array(
		'success' => true,
		'html' => $html,
	);

	wp_send_json( $response );
}

/**
 * Return rendered title prompt
 *
 * @since 4.7
 *
 */
function vc_action_render_settings_preset_title_prompt() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'presets' )->can()->validateDie();

	ob_start();
	vc_include_template( apply_filters( 'vc_render_settings_preset_title_prompt', 'editors/partials/prompt-presets.tpl.php' ) );
	$html = ob_get_clean();

	$response = array(
		'success' => true,
		'html' => $html,
	);

	wp_send_json( $response );
}

/**
 * Return rendered template prompt
 */
function vc_action_render_settings_templates_prompt() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->can()->validateDie();

	ob_start();
	vc_include_template( apply_filters( 'vc_render_settings_preset_title_prompt', 'editors/partials/prompt-templates.tpl.php' ) );
	$html = ob_get_clean();

	$response = array(
		'success' => true,
		'html' => $html,
	);

	wp_send_json( $response );
}

/**
 * Register (add) new vendor preset
 *
 * @since 4.8
 *
 * @param string $title
 * @param string $shortcode
 * @param array $params
 * @param bool $default
 */
function vc_register_settings_preset( $title, $shortcode, $params, $default = false ) {
	vc_vendor_preset()->add( $title, $shortcode, $params, $default );
}

function vc_add_new_elements_to_box( $shortcodes ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	return Vc_Settings_Preset::addVcPresetsToShortcodes( $shortcodes );
}

function vc_add_new_category_filter( $cat ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	return Vc_Settings_Preset::addPresetCategory( $cat );
}
