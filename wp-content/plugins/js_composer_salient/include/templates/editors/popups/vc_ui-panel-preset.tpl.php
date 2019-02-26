<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_ui-font-open-sans vc_ui-panel-window vc_preset-panel vc_media-xs vc_ui-panel"
	data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-preset" id="vc_ui-panel-preset">
	<div class="vc_ui-panel-window-inner">
		<?php vc_include_template( 'editors/popups/vc_ui-header.tpl.php', array(
			'title' => __( 'My Elements', 'js_composer' ),
			'controls' => array(
				'minimize',
				'close',
			),
			'header_css_class' => 'vc_ui-preset-panel-header-container',
		) ); ?>
		<!-- param window footer-->
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content vc_properties-list vc_row"
					data-vc-ui-element="panel-content">
				<div class="vc_column vc_col-sm-12">
					<h3><?php _e( 'Manage My Elements', 'js_composer' );?></h3>
					<p class="vc_description"><?php _e( 'Remove existing elements', 'js_composer' );?></p>
				</div>
				<div class="vc_column vc_col-sm-12">
					<div class="vc_ui-template-list vc_ui-list-bar" data-vc-action="collapseAll" data-vc-presets-list-content>
						<div class="vc_ui-template" style="display:none;">
							<div class="vc_ui-list-bar-item">
								<button type="button" class="vc_ui-list-bar-item-trigger" title="" data-vc-ui-element="template-title"></button>
								<div class="vc_ui-list-bar-item-actions">
									<button type="button" class="vc_general vc_ui-control-button" title="<?php _e( 'Add element', 'js_composer' ); ?>" data-template-handler data-vc-ui-add-preset><i class="vc-composer-icon vc-c-icon-add"></i></button>
									<button type="button" class="vc_general vc_ui-control-button" data-vc-ui-delete="preset-title" data-preset="" data-preset-parent="" title="<?php _e( 'Delete element', 'js_composer' ); ?>">
										<i class="vc-composer-icon vc-c-icon-delete_empty"></i></button>
								</div>
							</div>
						</div>
						<?php echo $box->getPresets(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/ temp content -->
