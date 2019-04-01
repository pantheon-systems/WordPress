<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_ui-font-open-sans vc_ui-panel-window vc_templates-panel vc_media-xs vc_ui-panel"
	data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-templates" id="vc_ui-panel-templates">
	<div class="vc_ui-panel-window-inner">
		<!-- param window header-->
		<?php
		$categories_data = $box->getAllTemplatesSorted();
		$categories = $box->getAllCategoriesNames( $categories_data ); ?>
		<?php vc_include_template( 'editors/popups/vc_ui-header.tpl.php', array(
			'title' => __( 'Templates', 'js_composer' ),
			'controls' => array(
				'minimize',
				'close',
			),
			'header_css_class' => 'vc_ui-template-panel-header-container',
			'content_template' => 'editors/partials/vc_ui-templates-tabs.tpl.php',
			'search_template' => 'editors/partials/templates_search.tpl.php',
			'template_variables' => array(
				'categories_data' => $categories_data,
				'categories' => $categories,
			),
		) ); ?>
		<!-- param window footer-->
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content vc_properties-list"
				data-vc-ui-element="panel-content">
				<div class="vc_panel-tabs">
					<?php
					/**
					 * Preparing tabs content
					 */
					?>
					<?php
					$first = true;
					/*nectar addition*/
					if($categories_data) {
						$categories_data = array_reverse($categories_data);
					}
					/*nectar addition end*/
					foreach ( $categories_data as $key => $category ) :
						echo '<div class="vc_edit-form-tab vc_row vc_ui-flex-row' . ( $first ? ' vc_active' : '' ) . '"' . ' data-vc-ui-element="panel-edit-element-tab"' . ' data-tab="' . esc_attr( $category['category'] ) . '">';
						/*nectar additon - library sidebar*/
						if(esc_attr( $category['category'] ) == 'default_templates') {
							echo '<div class="library_categories">
								<ul>';
									$nectar_library_cats = nectar_vc_library_cat_list();
									foreach($nectar_library_cats as $cat_name => $cat_sort_text) {
										echo '<li data-sort="'.$cat_sort_text.'">'.$cat_name.' <span class="count">0</span></li>';
									}
								echo '</ul>
							</div>';
						}
						/* nectar additon end */
						$templates_block = apply_filters( 'vc_templates_render_category', $category );
						if ( isset( $templates_block['output'] ) && is_string( $templates_block['output'] ) ) {
							echo $templates_block['output'];
						}
						echo '</div>';
						$first = false;
					endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/ temp content -->
