<h2 class="wpallimport-wp-notices"></h2>

<form class="wpallimport-template <?php echo ! $this->isWizard ? 'edit' : '' ?> wpallimport-step-3" method="post">	
	
	<div class="wpallimport-wrapper">
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

	<?php $visible_sections = apply_filters('pmxi_visible_template_sections', array('caption', 'main', 'taxonomies', 'cf', 'featured', 'other', 'nested'), $post['custom_type']); ?>

	<table class="wpallimport-layout">
		<tr>
			<td class="left">
				
				<?php do_action('pmxi_template_header', $this->isWizard, $post); ?>

				<?php if ($this->errors->get_error_codes()): ?>
					<?php $this->error() ?>
				<?php endif ?>
				<?php if ($this->warnings->get_error_codes()): ?>
					<?php $this->warning() ?>
				<?php endif ?>
				
				<?php $post_type = $post['custom_type'];?>

				<?php if ( in_array('caption', $visible_sections) ): ?>								

					<div class="wpallimport-collapsed wpallimport-section">
						<div class="wpallimport-content-section" style="overflow: hidden; padding-bottom: 0;">
							<div class="wpallimport-collapsed-header" style="margin-bottom: 15px;">
								<?php if ( $post_type == 'taxonomies' ): ?>
									<h3><?php _e('Name & Description', 'wp_all_import_plugin'); ?></h3>
								<?php else: ?>
									<h3><?php _e('Title & Content', 'wp_all_import_plugin'); ?></h3>
								<?php endif; ?>
							</div>
							<div class="wpallimport-collapsed-content" style="padding: 0;">				
								
								<div style="padding: 15px 25px 65px;">
									<div id="titlediv" style="margin-bottom:20px;">
										<div id="titlewrap">
											<input id="wpallimport-title" class="widefat" type="text" name="title" value="<?php echo esc_attr($post['title']) ?>" placeholder="<?php _e('Drag & drop any element on the right to set the title.', 'wp_all_import_plugin'); ?>"/>
										</div>
									</div>
									
									<div id="poststuff" style="margin-top:-25px;">
										<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">

											<?php wp_editor($post['content'], 'content', array(
													'teeny' => true,	
													'editor_class' => 'wpallimport-plugin-editor',
													'media_buttons' => false,							
													'editor_height' => 200)); 
											?>
											
										</div>
									</div>						

									<?php if ( post_type_supports( $post_type, 'excerpt' ) ):?>														
									<div class="template_input">
										<input type="text" name="post_excerpt" style="width:100%; line-height: 25px;" value="<?php echo esc_attr($post['post_excerpt']) ?>" placeholder="<?php if ($post_type == 'product' and class_exists('PMWI_Plugin')): _e('WooCommerce Short Description', 'wp_all_import_plugin'); else: _e('Excerpt', 'wp_all_import_plugin'); endif; ?>"/>
									</div>
									<?php endif; ?>

									<a class="preview" href="javascript:void(0);" rel="preview"><?php _e('Preview', 'wp_all_import_plugin'); ?></a>
								</div>

								<div class="wpallimport-collapsed closed wpallimport-section">
									<div class="wpallimport-content-section rad0" style="margin:0; border-top:1px solid #ddd; border-bottom: none; border-right: none; border-left: none; background: #f1f2f2;">
										<div class="wpallimport-collapsed-header">
											<h3 style="color:#40acad;"><?php _e('Advanced Options','wp_all_import_plugin');?></h3>	
										</div>
										<div class="wpallimport-collapsed-content" style="padding: 0;">										
											<div class="wpallimport-collapsed-content-inner">											
												<div class="input pmxi_option">																			
													<input type="hidden" name="is_keep_linebreaks" value="0" />
													<input type="checkbox" id="is_keep_linebreaks" name="is_keep_linebreaks" class="fix_checkbox" value="1" <?php echo $post['is_keep_linebreaks'] ? 'checked="checked"' : '' ?> />
													<label for="is_keep_linebreaks"><?php _e('Keep line breaks from file', 'wp_all_import_plugin') ?></label>								
												</div>				
												<div class="input pmxi_option">																			
													<input type="hidden" name="is_leave_html" value="0" />
													<input type="checkbox" id="is_leave_html" name="is_leave_html" class="fix_checkbox" value="1" <?php echo $post['is_leave_html'] ? 'checked="checked"' : '' ?> style="position:relative;"/>
													<label for="is_leave_html"><?php _e('Decode HTML entities with <b>html_entity_decode</b>', 'wp_all_import_plugin') ?></label>
													<a class="wpallimport-help" href="#help" style="position:relative; top:1px;" original-title="If HTML code is showing up in your posts, use this option. You can also use <br /><br /><i>[html_entity_decode({my/xpath})]</i><br /><br /> or <br /><br /><i>[htmlentities({my/xpath})]</i><br /><br /> or <br /><br /><i>[htmlspecialchars_decode({my/xpath})]</i><br /><br /> to decode or encode HTML in your file.">?</a>								
												</div>	
											</div>				
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				<?php endif; ?>

				<input type="hidden" name="custom_type" value="<?php echo $post['custom_type']; ?>"/>
				<input type="hidden" name="type" value="<?php echo ($post['custom_type'] == 'page') ? 'page' : 'post'; ?>"/>										
					
				<?php									

					if ( in_array('main', $visible_sections) ) do_action('pmxi_extend_options_main', $post_type, $post);

					if ( in_array('featured', $visible_sections) ) {
						$is_images_section_enabled = apply_filters('wp_all_import_is_images_section_enabled', true, $post_type);						
						if ( $is_images_section_enabled ) {
							PMXI_API::add_additional_images_section(__('Images', 'wp_all_import_plugin'), '', $post, $post_type, true, true);
						}
							
						do_action('pmxi_extend_options_featured', $post_type, $post);
					}

					if ( in_array('cf', $visible_sections) ){
						if ( $post_type == 'taxonomies' ){
							include( 'template/_term_meta_template.php' );
						}
						else{
							include( 'template/_custom_fields_template.php' );
						}
						do_action('pmxi_extend_options_custom_fields', $post_type, $post);																								
					}

					if ( in_array('taxonomies', $visible_sections) ) {
						include( 'template/_taxonomies_template.php' );					
						do_action('pmxi_extend_options_taxonomies', $post_type, $post);												
					}									

					if ( in_array('other', $visible_sections) ){
						if ( $post_type == 'taxonomies' ) {
							include('template/_term_other_template.php');
						}
						else{
							include( 'template/_other_template.php' );
						}
						do_action('pmxi_extend_options_other', $post_type, $post);
					}

					/*if ( in_array('nested', $visible_sections) ){ 
						include( 'template/_nested_template.php' );
						do_action('pmxi_extend_options_nested', $post_type);
					}*/

					$uploads = wp_upload_dir();
					$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_IMPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';
				    $functions = apply_filters( 'import_functions_file_path', $functions );
					$functions_content = file_get_contents($functions);

					?>
					<div class="wpallimport-collapsed closed wpallimport-section">
						<div class="wpallimport-content-section">
							<div class="wpallimport-collapsed-header">
								<h3><?php _e('Function Editor', 'wp_all_import_plugin'); ?></h3>	
							</div>
							<div class="wpallimport-collapsed-content" style="padding: 0;">
								<div class="wpallimport-collapsed-content-inner">									

									<textarea id="wp_all_import_code" name="wp_all_import_code"><?php echo (empty($functions_content)) ? "<?php\n\n?>": esc_textarea($functions_content);?></textarea>						

									<div class="input" style="margin-top: 10px;">

										<div class="input" style="display:inline-block; margin-right: 20px;">
											<input type="button" class="button-primary wp_all_import_save_functions" value="<?php _e("Save Functions", 'wp_all_import_plugin'); ?>"/>							
											<a href="#help" class="wpallimport-help" title="<?php printf(__("Add functions here for use during your import. You can access this file at %s", "wp_all_import_plugin"), preg_replace("%.*wp-content%", "wp-content", $functions));?>" style="top: 0;">?</a>
											<div class="wp_all_import_functions_preloader"></div>
										</div>						
										<div class="input wp_all_import_saving_status" style="display:inline-block;">

										</div>

									</div>

								</div>
							</div>
						</div>
					</div>
				
				<hr>
				
				<div class="input wpallimport-section" style="padding-bottom: 8px; padding-left: 8px;">

					<?php 
						wp_all_import_template_notifications( $post, 'notice' );							
					?>					
										
					<p style="margin: 11px; float: left;">
						<input type="hidden" name="save_template_as" value="0" />
						<input type="checkbox" id="save_template_as" name="save_template_as" class="switcher-horizontal fix_checkbox" value="1" <?php echo ( ! empty($post['save_template_as'])) ? 'checked="checked"' : '' ?> /> 
						<label for="save_template_as"><?php _e('Save settings as a template','wp_all_import_plugin');?></label>
					</p>
					<div class="switcher-target-save_template_as" style="float: left; overflow: hidden;">
						<input type="text" name="name" placeholder="<?php _e('Template name...', 'wp_all_import_plugin') ?>" style="vertical-align:middle; line-height: 26px;" value="<?php echo esc_attr($post['name']) ?>" />		
					</div>				
					<?php $templates = new PMXI_Template_List(); ?>
					<div class="load-template">				
						<select name="load_template" id="load_template" style="padding:2px; width: auto; height: 40px;">
							<option value=""><?php _e('Load Template...', 'wp_all_import_plugin') ?></option>
							<?php foreach ($templates->getBy()->convertRecords() as $t): ?>
								<option value="<?php echo $t->id ?>"><?php echo $t->name ?></option>
							<?php endforeach ?>
						</select>
					</div>
					
				</div>

				<hr>

				<div class="wpallimport-submit-buttons">
					
					<div style="text-align:center; width:100%;">
						<?php wp_nonce_field('template', '_wpnonce_template'); ?>

						<input type="hidden" name="is_submitted" value="1" />									
						<input type="hidden" name="security" value="<?php echo wp_create_nonce( "wp_all_import_preview" ); ?>" />									

						<?php if ($this->isWizard):?>
							<a href="<?php echo add_query_arg('action', 'element', $this->baseUrl) ?>" class="back rad3" style="float:none;"><?php _e('Back to Step 2', 'wp_all_import_plugin') ?></a>
						<?php else: ?>
							<a href="<?php echo remove_query_arg('id', remove_query_arg('action', $this->baseUrl)); ?>" class="back rad3" style="float:none;"><?php _e('Back to Manage Imports', 'wp_all_import_plugin') ?></a>
						<?php endif; ?>					
						<input type="submit" class="button button-primary button-hero wpallimport-large-button" value="<?php _e( ($this->isWizard) ? 'Continue to Step 4' : 'Update Template', 'wp_all_import_plugin') ?>" />
					</div>

				</div>

				<a href="http://soflyy.com/" target="_blank" class="wpallimport-created-by"><?php _e('Created by', 'wp_all_import_plugin'); ?> <span></span></a>
				
			</td>
			<?php if ($this->isWizard or $this->isTemplateEdit): ?>
				<td class="right template-sidebar">
					<div style="position:relative;">
					<?php $this->tag( false ); ?>
					</div>
				</td>
			<?php endif ?>
		</tr>
	</table>	

</form>
