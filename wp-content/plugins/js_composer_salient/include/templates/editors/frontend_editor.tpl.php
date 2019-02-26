<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var $editor Vc_Frontend_Editor */
global $menu, $submenu, $parent_file, $post_ID, $post, $post_type, $post_type_object;
$post_ID = $editor->post_id;
$post = $editor->post;
$post_type = $post->post_type;
$post_type_object = get_post_type_object( $post_type );
$post_title = trim( $post->post_title );
$nonce_action = $nonce_action = 'update-post_' . $editor->post_id;
$user_ID = isset( $editor->current_user ) && isset( $editor->current_user->ID ) ? (int) $editor->current_user->ID : 0;
$form_action = 'editpost';
$menu = array();
add_thickbox();
wp_enqueue_media( array( 'post' => $editor->post_id ) );
require_once( $editor->adminFile( 'admin-header.php' ) );
// @since 4.8 js logic for user role access manager.
vc_include_template( 'editors/partials/access-manager-js.tpl.php' );

?>
	<div id="vc_preloader">
		<!-- nectar addition -->
			<div class="wpbakery-logo-and-salient"><span><?php include( vc_path_dir( 'ASSETS_DIR', 'images/salient.svg' ) ); ?></span></div>
		<!-- nectar addition end -->
	</div>
	<div id="vc_overlay_spinner" class="vc_ui-wp-spinner vc_ui-wp-spinner-dark vc_ui-wp-spinner-lg" style="display:none;"></div>
	<script type="text/javascript">
		document.getElementById( 'vc_preloader' ).style.height = window.screen.availHeight;
		var vc_mode = '<?php echo vc_mode() ?>',
			vc_iframe_src = '<?php echo esc_attr( $editor->url ); ?>';
		window.wpbGutenbergEditorUrl = '<?php echo set_url_scheme( admin_url( 'post-new.php?post_type=wpb_gutenberg_param' ) ); ?>';
	</script>
	<input type="hidden" name="vc_post_title" id="vc_title-saved" value="<?php echo esc_attr( $post_title ); ?>"/>
	<input type="hidden" name="vc_post_id" id="vc_post-id" value="<?php echo esc_attr( $editor->post_id ); ?>"/>
<?php

// [vc_navbar frontend]
require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar-frontend.php' );
$nav_bar = new Vc_Navbar_Frontend( $post );
$nav_bar->render();
// [/vc_navbar frontend]
?>
	<div id="vc_inline-frame-wrapper"></div>
<?php
// [add element popup/box]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
$add_element_box = new Vc_Add_Element_Box( $editor );
$add_element_box->render();
// [/add element popup/box]

// [shortcodes edit form panel render]
visual_composer()->editForm()->render();
// [/shortcodes edit form panel render]

// [templates panel editor render]
if ( vc_user_access()->part( 'templates' )->can()->get() ) {
	visual_composer()->templatesPanelEditor()->renderUITemplate();
}
// [/templates panel editor render]

// [preset panel editor render]
visual_composer()->presetPanelEditor()->renderUIPreset();
// [/preset panel editor render]

// [post settings panel render]
if ( vc_user_access()->part( 'post_settings' )->can()->get() ) {
	require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings.php' );
	$post_settings = new Vc_Post_Settings( $editor );
	$post_settings->renderUITemplate();
}
// [/post settings panel render]

// [panel edit layout render]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-edit-layout.php' );
$edit_layout = new Vc_Edit_Layout();
$edit_layout->renderUITemplate();
// [/panel edit layout render]

// fe controls
vc_include_template( 'editors/partials/frontend_controls.tpl.php' );

// [shortcodes presets data]
if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_vendor_settings_presets = Vc_Settings_Preset::listDefaultVendorSettingsPresets();
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_vendor_settings_presets = array();
	$vc_all_presets = array();
}
// [/shortcodes presets data]

?>
	<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css"
	       value="<?php echo esc_attr( $editor->post_custom_css ); ?>" autocomplete="off"/>
	<script type="text/javascript">
		var vc_user_mapper = <?php echo json_encode( WPBMap::getUserShortCodes() ) ?>,
			vc_mapper = <?php echo json_encode( WPBMap::getShortCodes() ) ?>,
			vc_vendor_settings_presets = <?php echo json_encode( $vc_vendor_settings_presets ) ?>,
		    vc_all_presets = <?php echo json_encode( $vc_all_presets ) ?>,
			vc_roles = [], // @todo fix_roles BC for roles
			vcAdminNonce = '<?php echo vc_generate_nonce( 'vc-admin-nonce' ); ?>',
			vc_post_id = <?php echo $post_ID; ?>;
	</script>

<?php vc_include_template( 'editors/partials/vc_settings-image-block.tpl.php' ) ?>
<!-- BC for older plugins 5.5 !-->
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post_ID; ?>" />
	<div style="height: 1px; visibility: hidden; overflow: hidden;">
		<?php
		// Disable notice in edit-form-advanced.php
		$is_IE = false;
		wp_editor( '', 'vc-hidden-editor', array(
			'editor_height' => 300,
			'tinymce' => array(
				'resize' => false,
				'wp_autoresize_on' => false,
				'add_unload_trigger' => false,
				'wp_keep_scroll_position' => ! $is_IE,
			),
		) );
		// Fix: WP 4.0
		wp_dequeue_script( 'editor-expand' );
		do_action( 'vc_frontend_editor_render_template' );
		?>
	</div>
<?php

// other admin footer files and actions.
require_once( $editor->adminFile( 'admin-footer.php' ) ); ?>
