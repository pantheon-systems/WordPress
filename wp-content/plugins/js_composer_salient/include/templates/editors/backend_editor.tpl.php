<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
// [shortcodes presets data]
if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_all_presets = array();
}
// [/shortcodes presets data]
global $wp_version;
?>
<script type="text/javascript">
	var vc_all_presets = <?php echo json_encode( $vc_all_presets ) ?>;
	var vc_post_id = <?php echo get_the_ID(); ?>;
	window.wpbGutenbergEditorUrl = '<?php echo set_url_scheme( admin_url( 'post-new.php?post_type=wpb_gutenberg_param' ) ); ?>';
	window.wpbGutenbergEditorSWitchUrl = '<?php echo set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&vcv-gutenberg-editor' ) ); ?>';
	window.wpbGutenbergEditorClassicSWitchUrl = '<?php echo set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&classic-editor' ) ); ?>';
	window.wpbIsGutenberg = <?php echo version_compare( $wp_version, '4.9.8', '>' ) && ! get_option( 'wpb_js_gutenberg_disable' ) ? 'true' : 'false' ?>;
</script>

<?php

require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar.php' );
/** @var $post WP_Post */
$nav_bar = new Vc_Navbar( $post );
$nav_bar->render();
/** @var $editor Vc_Backend_Editor */
?>
	<style>
		#wpb_visual_composer {
			display: none;
		}
	</style>
	<div class="metabox-composer-content">
		<div id="visual_composer_content" class="wpb_main_sortable main_wrapper"></div>
		<?php require vc_path_dir( 'TEMPLATES_DIR', 'editors/partials/vc_welcome_block.tpl.php' ); ?>

	</div>
<?php

$wpb_vc_status = apply_filters( 'wpb_vc_js_status_filter', vc_get_param( 'wpb_vc_js_status', get_post_meta( $post->ID, '_wpb_vc_js_status', true ) ) );

if ( '' === $wpb_vc_status || ! isset( $wpb_vc_status ) ) {
	$wpb_vc_status = vc_user_access()->part( 'backend_editor' )->checkState( 'default' )->get() ? 'true' : 'false';
}

?>

	<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="<?php echo esc_attr( $wpb_vc_status ); ?>"/>
	<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading"
			value="<?php esc_attr_e( 'Loading, please wait...', 'js_composer' ) ?>"/>
	<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row"
			value="<?php esc_attr_e( 'Crunching...', 'js_composer' ) ?>"/>
	<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css"
			value="<?php echo esc_attr( $editor->post_custom_css ); ?>" autocomplete="off"/>
	<div id="vc_preloader" style="display: none;"></div>
	<div id="vc_overlay_spinner" class="vc_ui-wp-spinner vc_ui-wp-spinner-dark vc_ui-wp-spinner-lg" style="display:none;"></div>
<?php vc_include_template( 'editors/partials/access-manager-js.tpl.php' );
