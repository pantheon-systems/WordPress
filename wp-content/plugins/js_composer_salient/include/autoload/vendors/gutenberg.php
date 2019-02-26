<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
function vcv_disable_gutenberg_for_classic_editor( $post ) {
	return false;
}

/**
 * @param \Vc_Settings $settings
 */
function vc_gutenberg_add_settings( $settings ) {
	global $wp_version;
	if ( function_exists( 'the_gutenberg_project' ) || version_compare( $wp_version, '4.9.8', '>' ) ) {
		$settings->addField( 'general', __( 'Disable Gutenberg Editor', 'js_composer' ), 'gutenberg_disable', 'vc_gutenberg_sanitize_disable_callback', 'vc_gutenberg_disable_render_callback' );
	}
}

/**
 * @param $rules
 *
 * @return mixed
 */
function vc_gutenberg_sanitize_disable_callback( $rules ) {
	return (bool) $rules;
}

/**
 * Not responsive checkbox callback function
 */
function vc_gutenberg_disable_render_callback() {
	$checked = ( $checked = get_option( 'wpb_js_gutenberg_disable' ) ) ? $checked : false;
	?>
	<label>
		<input type="checkbox"<?php echo( $checked ? ' checked' : '' ) ?> value="1"
				name="<?php echo 'wpb_js_gutenberg_disable' ?>">
		<?php _e( 'Disable', 'js_composer' ) ?>
	</label><br/>
	<p
			class="description indicator-hint"><?php _e( 'Disable Gutenberg Editor.', 'js_composer' ); ?></p>
	<?php
}

function vc_gutenberg_check_disabled( $result, $postType ) {
	if ( 'wpb_gutenberg_param' === $postType ) {
		return true;
	}
	if ( ! isset( $_GET['vcv-gutenberg-editor'] ) && ( get_option( 'wpb_js_gutenberg_disable' ) || vc_is_wpb_content() || isset( $_GET['classic-editor'] ) ) ) {
		return false;
	}

	return $result;
}

function vc_is_wpb_content() {
	$post = get_post();
	if ( ! empty( $post ) && isset( $post->post_content ) && preg_match( '/\[vc_row/', $post->post_content ) ) {
		return true;
	}

	return false;
}

function vc_gutenberg_map() {
	global $wp_version;
	if ( function_exists( 'the_gutenberg_project' ) || version_compare( $wp_version, '4.9.8', '>' ) ) {
		vc_lean_map( 'vc_gutenberg', null, dirname( __FILE__ ) . '/shortcode-vc-gutenberg.php' );
	}
}

add_filter( 'use_block_editor_for_post_type', 'vc_gutenberg_check_disabled', 10, 2 );
add_action( 'vc_settings_tab-general', 'vc_gutenberg_add_settings' );
add_action( 'init', 'vc_gutenberg_map' );

/** @see include/params/gutenberg/class-gutenberg-param.php */
require_once vc_path_dir( 'PARAMS_DIR', 'gutenberg/class-gutenberg-param.php' );
new Gutenberg_Param();
