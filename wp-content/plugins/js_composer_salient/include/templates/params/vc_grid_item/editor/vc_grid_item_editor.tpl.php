<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/navbar/class-vc-navbar-grid-item.php' );
$nav_bar = new Vc_Navbar_Grid_Item( $post );
$nav_bar->render();

?>
<div class="metabox-composer-content">
	<div id="visual_composer_content" class="wpb_main_sortable main_wrapper"
		data-type="<?php echo esc_attr( get_post_type() ) ?>"></div>
	<div id="vc_gitem-preview" class="main_wrapper vc_gitem-preview" data-vc-grid-item="preview">
	</div>
</div>
<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="true"/>
<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading"
	value="<?php _e( 'Loading, please wait...', 'js_composer' ) ?>"/>
<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row"
	value="<?php _e( 'Crunching...', 'js_composer' ) ?>"/>
<input type="hidden" name="vc_grid_item_editor" value="true"/>
<script type="text/javascript">
	var vc_post_id = <?php echo get_the_ID(); ?>;
	<?php
	$vc_gitem_template = vc_request_param( 'vc_gitem_template' );
	if ( strlen( $vc_gitem_template ) && false !== ( $template = Vc_Grid_Item::predefinedTemplate( $vc_gitem_template ) ) ) {
		echo "var vcDefaultGridItemContent = '" . $template['template'] . "';";
	} else {
	?>
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
	<?php
	}
	?>
</script>
