<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_grid_item_map_shortcodes();
do_action( 'vc-render-templates-preview-template' );
/** @var $vc_grid_item_editor Vc_Grid_Item_Editor */
global $vc_grid_item_editor;
if ( $vc_grid_item_editor ) {
	$vc_grid_item_editor->registerBackendCss();
	$vc_grid_item_editor->registerBackendJavascript();
	add_filter( 'admin_body_class', array( $vc_grid_item_editor->templatesEditor(), 'addBodyClassTemplatePreview' ) );
	add_action( 'admin_enqueue_scripts', array( &$vc_grid_item_editor, 'enqueueEditorScripts' ) );
	add_action( 'admin_footer', array( &$vc_grid_item_editor, 'renderEditorFooter' ) );
	add_filter( 'vc_wpbakery_shortcode_get_controls_list', array( $vc_grid_item_editor, 'shortcodesControls' ) );
}

add_action( 'admin_enqueue_scripts', array( visual_composer()->templatesPanelEditor(), 'enqueuePreviewScripts' ) );


global $menu, $submenu, $parent_file, $post_ID, $post, $post_type;
$post_ID = $editorPost->ID;
$post_type = $editorPost->post_type;
$post_title = trim( $editorPost->post_title );
$nonce_action = $nonce_action = 'update-post_' . $post_ID;
$user_ID = isset( $current_user ) && isset( $current_user->ID ) ? (int) $current_user->ID : 0;
$form_action = 'editpost';
$menu = array();
remove_action( 'wp_head', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
add_thickbox();
wp_enqueue_media( array( 'post' => $post_ID ) );
visual_composer()->templatesPanelEditor()->registerPreviewScripts();
require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<style type="text/css">
		#screen-meta, #adminmenumain, .notice, #wpfooter, #message, .updated {
			display: none !important;
		}

		#wpcontent {
			margin-left: 0 !important;
			padding-left: 0 !important;
		}

		.vc_not-remove-overlay {
			position: fixed !important;
			width: 100%;
			height: 100%;
			z-index: 199999999;
		}
		html {
			overflow: hidden;
			background: transparent;
		}
	</style>
	<div class="vc_not-remove-overlay"></div>
	<div class="vc_ui-template-preview">
		<textarea id="content" style="display: none;"><?php echo $content ?></textarea>

		<div id="wpb_visual_composer" class="postbox " style="display: block;">
			<div class="inside">
				<div class="metabox-composer-content">
					<div id="visual_composer_content"
					     class="wpb_main_sortable main_wrapper ui-sortable ui-droppable"></div>
					<div id="vc_no-content-helper" class="vc_welcome"></div>
				</div>
				<input type="hidden" name="vc_js_composer_group_access_show_rule"
				       class="vc_js_composer_group_access_show_rule" value="all">
				<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="true">
				<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading" value="Loading, please wait...">
				<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row" value="Crunching...">
				<input type="hidden" name="vc_grid_item_editor" value="true"/>
				<input type="hidden" name="vc_post_custom_css" id="vc_post-custom-css" value="" autocomplete="off">
			</div>
		</div>
		<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading"
		       value="<?php esc_attr_e( 'Loading, please wait...', 'js_composer' ) ?>"/>
		<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row"
		       value="<?php esc_attr_e( 'Crunching...', 'js_composer' ) ?>"/>
	</div>
	<script type="text/javascript">
		/**
		 * Get content of grid item editor of current post. Data is used as models collection of shortcodes.
		 * Data always wrapped with vc_gitem shortcode.
		 * @return {*}
		 */
		var vcDefaultGridItemContent = '' +
			'[vc_gitem]' +
			'[vc_gitem_animated_block]' +
			'[vc_gitem_zone_a]' +
			'[vc_gitem_row position="top"]' +
			'[vc_gitem_col width="1/1"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[vc_gitem_row position="middle"]' +
			'[vc_gitem_col width="1/2"][/vc_gitem_col]' +
			'[vc_gitem_col width="1/2"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[vc_gitem_row position="bottom"]' +
			'[vc_gitem_col width="1/1"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[/vc_gitem_zone_a]' +
			'[vc_gitem_zone_b]' +
			'[vc_gitem_row position="top"]' +
			'[vc_gitem_col width="1/1"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[vc_gitem_row position="middle"]' +
			'[vc_gitem_col width="1/2"][/vc_gitem_col]' +
			'[vc_gitem_col width="1/2"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[vc_gitem_row position="bottom"]' +
			'[vc_gitem_col width="1/1"][/vc_gitem_col]' +
			'[/vc_gitem_row]' +
			'[/vc_gitem_zone_b]' +
			'[/vc_gitem_animated_block]' +
			'[/vc_gitem]';
	</script>
<?php
vc_include_template( 'editors/partials/backend-shortcodes-templates.tpl.php' );
do_action( 'vc_backend_editor_render' );
do_action( 'vc_vc_grid_item_editor_render' );
do_action( 'vc_ui-template-preview' );
// fix bug #59741644518985 in firefox
//wp_dequeue_script( 'isotope' );
require_once( ABSPATH . 'wp-admin/admin-footer.php' );
