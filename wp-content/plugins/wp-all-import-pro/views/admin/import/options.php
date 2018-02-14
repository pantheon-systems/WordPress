<?php	
	$isWizard = $this->isWizard;
	$baseUrl  = $this->baseUrl;	
?>

<input type="hidden" id="selected_post_type" value="<?php echo (!empty($post['custom_type'])) ? $post['custom_type'] : '';?>">
<input type="hidden" id="selected_type" value="<?php echo (!empty($post['type'])) ? $post['type'] : '';?>">

<div class="wpallimport-step-4">
	
	<h2 class="wpallimport-wp-notices"></h2>

	<div class="wpallimport-wrapper">
		<h2 class="wpallimport-wp-notices"></h2>
		<div class="wpallimport-header">
			<div class="wpallimport-logo"></div>
			<div class="wpallimport-title">
				<p><?php _e('WP All Import', 'wp_all_import_plugin'); ?></p>
				<h2><?php _e('Import XML / CSV', 'wp_all_import_plugin'); ?></h2>					
			</div>
			<div class="wpallimport-links">
				<a href="http://www.wpallimport.com/support/" target="_blank"><?php _e('Support', 'wp_all_import_plugin'); ?></a> | <a href="http://www.wpallimport.com/documentation/" target="_blank"><?php _e('Documentation', 'wp_all_import_plugin'); ?></a>
			</div>
		</div>	
		<div class="clear"></div>		
	</div>		

	<?php $visible_sections = apply_filters('pmxi_visible_options_sections', array('reimport', 'settings'), $post['custom_type']); ?>

	<table class="wpallimport-layout">
		<tr>
			<td class="left">		
	
				<?php do_action('pmxi_options_header', $isWizard, $post); ?>
				
				<?php
				$is_valid_root_element = true;		
				$error_codes = $this->warnings->get_error_codes();		
				if ( ! empty($error_codes) and is_array($error_codes) and in_array('root-element-validation', $error_codes))
				{
					$is_valid_root_element = false;
				}				
				?>							

				<div class="ajax-console">					
					<?php if ($this->errors->get_error_codes()): ?>
						<?php $this->error() ?>
					<?php endif ?>
					<?php if ($this->warnings->get_error_codes()): ?>
						<?php $this->warning() ?>
					<?php endif ?>

					<?php 
						wp_all_import_template_notifications( $post );			
					?>						
				</div>											

				<div class="rad4 first-step-errors error-no-root-element" <?php if ($is_valid_root_element === false):?>style="display:block;"<?php endif; ?>>
					<div class="wpallimport-notify-wrapper">
						<div class="error-headers exclamation">
							<?php if ($is_404 and $import->type == 'url'): ?>
							<h3><?php _e('This URL no longer returns an import file', 'wp_all_import_plugin');?></h3>
							<h4 style="font-size:18px;"><?php _e("You must provide a URL that returns a valid import file.", "wp_all_import_plugin"); ?></h4>	
							<?php else: ?>
							<h3><?php _e('There\'s a problem with your import file', 'wp_all_import_plugin');?></h3>
							<h4 style="font-size:18px;"><?php _e("It has changed and is not compatible with this import template.", "wp_all_import_plugin"); ?></h4>
							<?php endif;?>
						</div>		
					</div>		
					<a class="button button-primary button-hero wpallimport-large-button wpallimport-notify-read-more" href="http://www.wpallimport.com/documentation/troubleshooting/problems-with-import-files/#invalid" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
				</div>

				<form class="<?php echo ! $isWizard ? 'edit' : 'options' ?>" method="post" enctype="multipart/form-data" autocomplete="off" <?php echo ! $isWizard ? 'style="overflow:visible;"' : '' ?>>

					<?php $post_type = $post['custom_type']; ?>				

					<?php  if ( ! $this->isWizard): ?>
						
						<?php include( 'options/_import_file.php' ); ?>

					<?php endif; ?>

					<div class="options">
						<?php
													
							if ( in_array('reimport', $visible_sections)){
								if ($post_type == 'taxonomies'){
									include( 'options/_reimport_taxonomies_template.php' );
								}
								else{
									include( 'options/_reimport_template.php' );
								}
							}
							do_action('pmxi_options_tab', $isWizard, $post);
							if ( in_array('settings', $visible_sections)) include( 'options/_settings_template.php' );
							
							include( 'options/_buttons_template.php' );

						?>
					</div>

				</form>					
								
				<a href="http://soflyy.com/" target="_blank" class="wpallimport-created-by"><?php _e('Created by', 'wp_all_import_plugin'); ?> <span></span></a>
					
			</td>
			<td class="right template-sidebar ">
				<div style="position:relative;">
					<?php $this->tag( false ); ?>
				</div>
			</td>	
		</tr>
	</table>

</div>

<div id="record_matching_pointer" style="display:none;">	

	<h3><?php _e("Record Matching", "pmxi_plugin");?></h3>

	<p>
		<b><?php _e("Record Matching is how WP All Import matches records in your file with posts that already exist WordPress.","pmxi_plugin");?></b>
	</p>

	<p>
		<?php _e("Record Matching is most commonly used to tell WP All Import how to match up records in your file with posts WP All Import has already created on your site, so that if your file is updated with new data, WP All Import can update your posts accordingly.","pmxi_plugin");?>
	</p>

	<hr />

	<p><?php _e("AUTOMATIC RECORD MATCHING","pmxi_plugin");?></p>
	
	<p>
		<?php _e("Automatic Record Matching allows WP All Import to update records that were imported or updated during the last run of this same import.","pmxi_plugin");?>
	</p>

	<p>
		<?php _e("Your unique key must be UNIQUE for each record in your feed. Make sure you get it right - you can't change it later. You'll have to re-create your import.","pmxi_plugin");?>
	</p>

	<hr />

	<p><?php _e("MANUAL RECORD MATCHING", "pmxi_plugin");?></p>
	
	<p>
		<?php _e("Manual record matching allows WP All Import to update any records, even records that were not imported with WP All Import, or are part of a different import.","pmxi_plugin");?>
	</p>

</div>