<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel" data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-post-settings" id="vc_ui-panel-post-settings">
	<div class="vc_ui-panel-window-inner">
		<?php vc_include_template('editors/popups/vc_ui-header.tpl.php', array(
			'title' => __( 'Page Settings', 'js_composer' ),
			'controls' => array( 'minimize', 'close' ),
			'header_css_class' => 'vc_ui-post-settings-header-container',
			'content_template' => '',
		)); ?>
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements" data-vc-ui-element="panel-content">
				<div class="vc_row">
					<div class="vc_col-sm-12 vc_column" id="vc_settings-title-container">
						<div class="wpb_element_label"><?php _e( 'Page title', 'js_composer' ) ?></div>
						<div class="edit_form_line">
							<input name="page_title" class="wpb-textinput vc_title_name" type="text" value=""
							       id="vc_page-title-field"
							       placeholder="<?php _e( 'Please enter page title', 'js_composer' ) ?>">
					<span
						class="vc_description"><?php printf( __( 'Change title of the current %s (Note: changes may not be displayed in a preview, but will take effect after saving page).', 'js_composer' ), get_post_type() ); ?></span>
						</div>
					</div>
					<div class="vc_col-sm-12 vc_column">
						<div class="wpb_element_label"><?php _e( 'Custom CSS settings', 'js_composer' ) ?></div>
						<div class="edit_form_line">
							<pre id="wpb_csseditor" class="wpb_content_element custom_css wpb_frontend"></pre>
					<span
						class="vc_description vc_clearfix"><?php _e( 'Enter custom CSS (Note: it will be outputted only on this particular page).', 'js_composer' ) ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- param window footer-->
		<?php vc_include_template('editors/popups/vc_ui-footer.tpl.php', array(
			'controls' => array(
				array(
					'name' => 'close',
					'label' => __( 'Close', 'js_composer' ),
				),
				array(
					'name' => 'save',
					'label' => __( 'Save changes', 'js_composer' ),
					'css_classes' => 'vc_ui-button-fw',
					'style' => 'action',
				),
			),
		)); ?>
	</div>
</div>
