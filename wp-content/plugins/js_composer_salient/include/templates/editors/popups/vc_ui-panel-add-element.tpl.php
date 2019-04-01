<?php
/** @var $box Vc_Add_Element_Box */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel"
	data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-add-element" id="vc_ui-panel-add-element">
	<div class="vc_ui-panel-window-inner">
		<?php vc_include_template( 'editors/popups/vc_ui-header.tpl.php', array(
			'title' => __( 'Add Element', 'js_composer' ),
			'controls' => array( 'close' ),
			'header_css_class' => 'vc_ui-add-element-header-container',
			'content_template' => 'editors/partials/add_element_tabs.tpl.php',
			'search_template' => 'editors/partials/add_element_search.tpl.php',
			'template_variables' => $template_variables,
		) ) ?>
		<div class="vc_ui-panel-content-container">
			<div class="vc_add-element-container">
				<div class="wpb-elements-list vc_filter-all" data-vc-ui-filter="*"
					data-vc-ui-element="panel-add-element-list">
					<ul class="wpb-content-layouts-container">
						<li class="vc_add-element-deprecated-warning">
							<div class="wpb_element_wrapper">
								<?php echo vc_message_warning( __( 'Elements within this list are deprecated and are no longer supported in newer versions of WPBakery Page Builder.', 'js_composer' ) ) ?>
							</div>
						</li>
						<li><?php echo $box->getControls() ?></li>
						<?php if ( $box->isShowEmptyMessage() && true !== $box->getPartState() ) : ?>
							<li class="vc_add-element-access-warning">
								<div class="wpb_element_wrapper">
									<?php echo vc_message_warning( __( 'Your user role have restricted access to content elements. If required, contact your site administrator to change WPBakery Page Builder Role Manager settings for your user role.', 'js_composer' ) ) ?>
								</div>
							</li>
						<?php endif; ?>
					</ul>
					<div class="vc_clearfix"></div>
					<?php if ( vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get() ) : ?>
						<div class="vc_align_center">
							<span class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw" data-vc-manage-elements style="display:none;"><?php _e( 'Manage elements', 'js_composer' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
