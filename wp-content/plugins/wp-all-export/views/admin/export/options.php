<?php

$l10n = array(
		'confirm_and_run'    => __('Confirm & Run Export', 'wp_all_export_plugin'),
		'save_configuration' => __('Save Export Configuration', 'wp_all_export_plugin')	
	);
?>
<script type="text/javascript">	
	var wp_all_export_L10n = <?php echo json_encode($l10n); ?>;
</script>

<div class="wpallexport-step-4 wpallexport-export-options">
	
	<h2 class="wpallexport-wp-notices"></h2>

	<div class="wpallexport-wrapper">
		<h2 class="wpallexport-wp-notices"></h2>
		<div class="wpallexport-header">
			<div class="wpallexport-logo"></div>
			<div class="wpallexport-title">
				<p><?php _e('WP All Export', 'wp_all_export_plugin'); ?></p>
				<h2><?php _e('Export to XML / CSV', 'wp_all_export_plugin'); ?></h2>					
			</div>
			<div class="wpallexport-links">
				<a href="http://www.wpallimport.com/support/" target="_blank"><?php _e('Support', 'wp_all_export_plugin'); ?></a> | <a href="http://www.wpallimport.com/documentation/" target="_blank"><?php _e('Documentation', 'wp_all_export_plugin'); ?></a>
			</div>
		</div>	
		<div class="clear"></div>		
	</div>			

	<table class="wpallexport-layout">
		<tr>
			<td class="left" style="width: 100%;">		
	
				<?php do_action('pmxe_options_header', $this->isWizard, $post); ?>
				
				<div class="ajax-console">					
					<?php if ($this->errors->get_error_codes()): ?>
						<?php $this->error() ?>
					<?php endif ?>					
				</div>				
										
				<div class="wpallexport-content-section" style="padding: 0 30px 0 0; overflow: hidden; margin-bottom: 0;">

					<div id="filtering_result" class="wpallexport-ready-to-go">																		
						<h3> &nbsp; </h3>
						<div class="wp_all_export_preloader"></div>
					</div>	
					<?php if ($this->isWizard): ?>	
					<form class="confirm <?php echo ! $this->isWizard ? 'edit' : '' ?>" method="post" style="float:right;">							

						<?php wp_nonce_field('options', '_wpnonce_options') ?>
						<input type="hidden" name="is_submitted" value="1" />		

						<input type="submit" class="rad10 wp_all_export_confirm_and_run" value="<?php _e('Confirm & Run Export', 'wp_all_export_plugin') ?>" />											
					</form>	
					<?php endif; ?>
				</div>					

				<div class="clear"></div>

				<form class="<?php echo ! $this->isWizard ? 'edit' : 'options' ?> choose-export-options" method="post" enctype="multipart/form-data" autocomplete="off" <?php echo ! $this->isWizard ? 'style="overflow:visible;"' : '' ?>>

					<input type="hidden" class="hierarhy-output" name="filter_rules_hierarhy" value="<?php echo esc_html($post['filter_rules_hierarhy']);?>"/>
					
					<?php 
					$selected_post_type = '';
					if (XmlExportUser::$is_active): 
						$selected_post_type = empty($post['cpt'][0]) ? 'users' : $post['cpt'][0];
					endif;
					if (XmlExportComment::$is_active):
						$selected_post_type = 'comments';
					endif;
					if (empty($selected_post_type) and ! empty($post['cpt'][0]))
					{
						$selected_post_type = $post['cpt'][0];
					}				
					?>

					<input type="hidden" name="selected_post_type" value="<?php echo $selected_post_type; ?>"/>	
					<input type="hidden" name="export_type" value="<?php echo $post['export_type']; ?>"/>
					<input type="hidden" name="taxonomy_to_export" value="<?php echo $post['taxonomy_to_export'];?>">
					<input type="hidden" name="wpml_lang" value="<?php echo empty(PMXE_Plugin::$session->wpml_lang) ? $post['wpml_lang'] : PMXE_Plugin::$session->wpml_lang;?>" />
					<input type="hidden" id="export_variations" name="export_variations" value="<?php echo XmlExportEngine::getProductVariationMode();?>" />

					<?php //\Wpae\Pro\Filtering\FilteringFactory::render_filtering_block( $engine, $this->isWizard, $post ); ?>

					<?php include_once 'options/settings.php'; ?>			

					<p class="wpallexport-submit-buttons" style="text-align: center;">
						<?php wp_nonce_field('options', '_wpnonce_options') ?>
						<input type="hidden" name="is_submitted" value="1" />						
						
						<?php if ($this->isWizard): ?>

							<a href="<?php echo apply_filters('pmxi_options_back_link', add_query_arg('action', 'template', $this->baseUrl), $this->isWizard); ?>" class="back rad3"><?php _e('Back', 'wp_all_export_plugin') ?></a>							

							<input type="submit" class="button button-primary button-hero wpallexport-large-button" value="<?php _e('Confirm & Run Export', 'wp_all_export_plugin'); ?>" />		

						<?php else: ?>		
							<a href="<?php echo apply_filters('pmxi_options_back_link', remove_query_arg('id', remove_query_arg('action', $this->baseUrl)), $this->isWizard); ?>" class="back rad3"><?php _e('Back to Manage Exports', 'wp_all_export_plugin') ?></a>
							<input type="submit" class="button button-primary button-hero wpallexport-large-button" value="<?php _e('Save Export Configuration', 'wp_all_export_plugin'); ?>" />
						<?php endif ?>
					</p>


				</form>					
								
				<a href="http://soflyy.com/" target="_blank" class="wpallexport-created-by"><?php _e('Created by', 'wp_all_export_plugin'); ?> <span></span></a>
					
			</td>			
		</tr>
	</table>

</div>

<div class="wpallexport-overlay"></div>
