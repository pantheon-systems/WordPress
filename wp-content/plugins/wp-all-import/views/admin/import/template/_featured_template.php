<div class="wpallimport-collapsed closed wpallimport-section wpallimport-featured-images">
	<div class="wpallimport-content-section" style="padding-bottom: 0;">
		<div class="wpallimport-collapsed-header" style="margin-bottom: 15px;">
			<h3><?php echo $section_title;?></h3>	
		</div>
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner">
				<?php if ($section_is_show_warning and ( $post_type != "product" or ! class_exists('PMWI_Plugin'))):?>
					
					<div class="wpallimport-free-edition-notice" style="text-align:center; margin-top:-15px; margin-bottom: 40px;">
						<a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0?utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=images" target="_blank" class="upgrade_link"><?php _e('Upgrade to the Pro edition of WP All Import to Import Images', 'pmxi_plugin');?></a>
						<p><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_import_plugin'); ?></p>
					</div>
					
				<?php endif; ?>
				<table class="form-table" style="max-width:none;">
					<tr>
						<td colspan="3">
							<div class="input">
								<div class="input">							
									<input type="radio" name="<?php echo $section_slug; ?>download_images" value="yes" class="switcher" id="<?php echo $section_slug; ?>download_images_yes" <?php echo ("yes" == $post[$section_slug . 'download_images']) ? 'checked="checked"' : '';?>/>
									<label for="<?php echo $section_slug; ?>download_images_yes"><?php _e('Download images hosted elsewhere'); ?></label>
									<a href="#help" class="wpallimport-help" title="<?php _e('http:// or https://', 'wp_all_import_plugin') ?>" style="position: relative; top: -2px;">?</a>
								</div>						
								<div class="switcher-target-<?php echo $section_slug; ?>download_images_yes" style="padding-left:27px;">
									<label for="<?php echo $section_slug; ?>download_featured_delim"><?php _e('Enter image URL one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
									<input type="text" class="small" id="<?php echo $section_slug; ?>download_featured_delim" name="<?php echo $section_slug; ?>download_featured_delim" value="<?php echo esc_attr($post[$section_slug . 'download_featured_delim']) ?>" style="width:5%; text-align:center;"/>
									<textarea name="<?php echo $section_slug; ?>download_featured_image" class="newline rad4" style="clear: both; display:block;" placeholder=""><?php echo esc_attr($post[$section_slug . 'download_featured_image']) ?></textarea>			
								</div>
								<div class="input">							
									<input type="radio" name="<?php echo $section_slug; ?>download_images" value="gallery" class="switcher" id="<?php echo $section_slug; ?>download_images_gallery" <?php echo ("gallery" == $post[$section_slug . 'download_images']) ? 'checked="checked"' : '';?>/>
									<label for="<?php echo $section_slug; ?>download_images_gallery"><?php _e('Use images currently in Media Library'); ?></label>
									<!--a href="#help" class="wpallimport-help" title="<?php _e('http:// or https://', 'wp_all_import_plugin') ?>" style="position: relative; top: -2px;">?</a-->
								</div>						
								<div class="switcher-target-<?php echo $section_slug; ?>download_images_gallery" style="padding-left:27px;">
									<label for="<?php echo $section_slug; ?>gallery_featured_delim"><?php _e('Enter image filenames one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
									<input type="text" class="small" id="<?php echo $section_slug; ?>gallery_featured_delim" name="<?php echo $section_slug; ?>gallery_featured_delim" value="<?php echo esc_attr($post[$section_slug . 'gallery_featured_delim']) ?>" style="width:5%; text-align:center;"/>
									<textarea name="<?php echo $section_slug; ?>gallery_featured_image" class="newline rad4" style="clear: both; display:block; "><?php echo esc_attr($post[$section_slug . 'gallery_featured_image']) ?></textarea>			
								</div>
								<div class="input">
									<?php $wp_uploads = wp_upload_dir(); ?>																					
									<input type="radio" name="<?php echo $section_slug; ?>download_images" value="no" class="switcher" id="<?php echo $section_slug; ?>download_images_no" <?php echo ("no" == $post[$section_slug . 'download_images']) ? 'checked="checked"' : '';?>/>
									<label for="<?php echo $section_slug; ?>download_images_no"><?php printf(__('Use images currently uploaded in %s', 'wp_all_import_plugin'), preg_replace('%.*wp-content/%', 'wp-content/', $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR) ); ?></label>
								</div>
								<div class="switcher-target-<?php echo $section_slug; ?>download_images_no" style="padding-left:27px;">
									<label for="<?php echo $section_slug; ?>featured_delim"><?php _e('Enter image filenames one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
									<input type="text" class="small" id="<?php echo $section_slug; ?>featured_delim" name="<?php echo $section_slug; ?>featured_delim" value="<?php echo esc_attr($post[$section_slug . 'featured_delim']) ?>" style="width:5%; text-align:center;"/>
									<textarea name="<?php echo $section_slug; ?>featured_image" class="newline rad4" style="clear: both; display:block; "><?php echo esc_attr($post[$section_slug . 'featured_image']) ?></textarea>			
								</div>																
							</div>
							<h4><?php _e('Image Options', 'wp_all_import_plugin'); ?></h4>
							<div class="search_through_the_media_library">
								<div class="input" style="margin:3px;">
									<input type="hidden" name="<?php echo $section_slug; ?>search_existing_images" value="0" />
									<input type="checkbox" id="<?php echo $section_slug; ?>search_existing_images" name="<?php echo $section_slug; ?>search_existing_images" value="1" <?php echo $post[$section_slug . 'search_existing_images'] ? 'checked="checked"' : '' ?> class="fix_checkbox"/>
									<label for="<?php echo $section_slug; ?>search_existing_images"><?php _e('Search through the Media Library for existing images before importing new images','wp_all_import_plugin');?> </label>						
									<a href="#help" class="wpallimport-help" title="<?php _e('If an image with the same file name is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if your import has different images with the same file name.', 'wp_all_import_plugin') ?>" style="position: relative; top: -2px;">?</a>
								</div>							
								<div class="input" style="margin: 3px;">
									<input type="hidden" name="<?php echo $section_slug; ?>do_not_remove_images" value="0" />
									<input type="checkbox" id="<?php echo $section_slug; ?>do_not_remove_images" name="<?php echo $section_slug; ?>do_not_remove_images" value="1" <?php echo $post[$section_slug . 'do_not_remove_images'] ? 'checked="checked"': '' ?> />
									<label for="<?php echo $section_slug; ?>do_not_remove_images"><?php _e('Keep images currently in Media Library', 'wp_all_import_plugin') ?></label>
									<a href="#help" class="wpallimport-help" title="<?php _e('If disabled, images attached to imported posts will be deleted and then all images will be imported.', 'wp_all_import_plugin') ?>" style="position:relative; top: -2px;">?</a>
								</div>
							</div>
							<?php if ($section_type == 'images'): ?>
							<div class="input">
								<input type="hidden" value="<?php echo $section_slug; ?>" class="wp_all_import_section_slug"/>
								<a class="preview_images" href="javascript:void(0);" rel="preview_images"><?php _e('Preview & Test', 'wp_all_import_plugin'); ?></a>
							</div>																					
							<div class="input" style="margin:3px;">
								<input type="hidden" name="<?php echo $section_slug; ?>is_featured" value="0" />
								<input type="checkbox" id="<?php echo $section_slug; ?>is_featured" name="<?php echo $section_slug; ?>is_featured" value="1" <?php echo $post[$section_slug . 'is_featured'] ? 'checked="checked"' : '' ?> class="fix_checkbox"/>
								<label for="<?php echo $section_slug; ?>is_featured"><?php _e('Set the first image to the Featured Image (_thumbnail_id)','wp_all_import_plugin');?> </label>						
							</div>																					
							<div class="input" style="margin:3px;">
								<input type="hidden" name="<?php echo $section_slug; ?>create_draft" value="no" />
								<input type="checkbox" id="<?php echo $section_slug; ?>create_draft" name="<?php echo $section_slug; ?>create_draft" value="yes" <?php echo 'yes' == $post[$section_slug . 'create_draft'] ? 'checked="checked"' : '' ?> class="fix_checkbox"/>
								<label for="<?php echo $section_slug; ?>create_draft"><?php _e('If no images are downloaded successfully, create entry as Draft.', 'wp_all_import_plugin') ?></label>
							</div>
							<?php endif; ?>																						
						</td>
					</tr>
				</table>
			</div>

			<div class="wpallimport-collapsed closed wpallimport-section">
				<div class="wpallimport-content-section rad0" style="margin:0; border-top:1px solid #ddd; border-bottom: none; border-right: none; border-left: none; background: #f1f2f2;">
					<div class="wpallimport-collapsed-header">
						<h3 style="color:#40acad;"><?php _e('SEO & Advanced Options','wp_all_import_plugin');?></h3>	
					</div>
					<div class="wpallimport-collapsed-content" style="padding: 0;">
						<div class="wpallimport-collapsed-content-inner">
							<hr>						
							<table class="form-table" style="max-width:none;">
								<tr>
									<td colspan="3">
										<h4><?php _e('Meta Data', 'wp_all_import_plugin'); ?></h4>
										<div class="input">
											<input type="hidden" name="<?php echo $section_slug; ?>set_image_meta_title" value="0" />
											<input type="checkbox" id="<?php echo $section_slug; ?>set_image_meta_title" name="<?php echo $section_slug; ?>set_image_meta_title" value="1" <?php echo $post[$section_slug . 'set_image_meta_title'] ? 'checked="checked"' : '' ?> class="switcher fix_checkbox"/>
											<label for="<?php echo $section_slug; ?>set_image_meta_title"><?php _e('Set Title(s)','wp_all_import_plugin');?></label>
											<div class="switcher-target-<?php echo $section_slug; ?>set_image_meta_title" style="padding-left:23px;">							
												<label for="<?php echo $section_slug; ?>image_meta_title_delim"><?php _e('Enter one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
												<input type="text" class="small" id="<?php echo $section_slug; ?>image_meta_title_delim" name="<?php echo $section_slug; ?>image_meta_title_delim" value="<?php echo esc_attr($post[$section_slug . 'image_meta_title_delim']) ?>" style="width:5%; text-align:center;"/>
												<p style="margin-bottom:5px;"><?php _e('The first title will be linked to the first image, the second title will be linked to the second image, ...', 'wp_all_import_plugin');?></p>
												<textarea name="<?php echo $section_slug; ?>image_meta_title" class="newline rad4"><?php echo esc_attr($post[$section_slug . 'image_meta_title']) ?></textarea>																				
											</div>
										</div>
										<div class="input">
											<input type="hidden" name="<?php echo $section_slug; ?>set_image_meta_caption" value="0" />
											<input type="checkbox" id="<?php echo $section_slug; ?>set_image_meta_caption" name="<?php echo $section_slug; ?>set_image_meta_caption" value="1" <?php echo $post[$section_slug . 'set_image_meta_caption'] ? 'checked="checked"' : '' ?> class="switcher fix_checkbox"/>
											<label for="<?php echo $section_slug; ?>set_image_meta_caption"><?php _e('Set Caption(s)','wp_all_import_plugin');?></label>
											<div class="switcher-target-<?php echo $section_slug; ?>set_image_meta_caption" style="padding-left:23px;">							
												<label for="<?php echo $section_slug; ?>image_meta_caption_delim"><?php _e('Enter one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
												<input type="text" class="small" id="<?php echo $section_slug; ?>image_meta_caption_delim" name="<?php echo $section_slug; ?>image_meta_caption_delim" value="<?php echo esc_attr($post[$section_slug . 'image_meta_caption_delim']) ?>" style="width:5%; text-align:center;"/>
												<p style="margin-bottom:5px;"><?php _e('The first caption will be linked to the first image, the second caption will be linked to the second image, ...', 'wp_all_import_plugin');?></p>
												<textarea name="<?php echo $section_slug; ?>image_meta_caption" class="newline rad4"><?php echo esc_attr($post[$section_slug . 'image_meta_caption']) ?></textarea>																				
											</div>
										</div>
										<div class="input">
											<input type="hidden" name="<?php echo $section_slug; ?>set_image_meta_alt" value="0" />
											<input type="checkbox" id="<?php echo $section_slug; ?>set_image_meta_alt" name="<?php echo $section_slug; ?>set_image_meta_alt" value="1" <?php echo $post[$section_slug . 'set_image_meta_alt'] ? 'checked="checked"' : '' ?> class="switcher fix_checkbox"/>
											<label for="<?php echo $section_slug; ?>set_image_meta_alt"><?php _e('Set Alt Text(s)','wp_all_import_plugin');?></label>
											<div class="switcher-target-<?php echo $section_slug; ?>set_image_meta_alt" style="padding-left:23px;">							
												<label for="<?php echo $section_slug; ?>image_meta_alt_delim"><?php _e('Enter one per line, or separate them with a ', 'wp_all_import_plugin');?></label>
												<input type="text" class="small" id="<?php echo $section_slug; ?>image_meta_alt_delim" name="<?php echo $section_slug; ?>image_meta_alt_delim" value="<?php echo esc_attr($post[$section_slug . 'image_meta_alt_delim']) ?>" style="width:5%; text-align:center;"/>
												<p style="margin-bottom:5px;"><?php _e('The first alt text will be linked to the first image, the second alt text will be linked to the second image, ...', 'wp_all_import_plugin');?></p>
												<textarea name="<?php echo $section_slug; ?>image_meta_alt" class="newline rad4"><?php echo esc_attr($post[$section_slug . 'image_meta_alt']) ?></textarea>
											</div>
										</div>
										<div class="input">
											<input type="hidden" name="<?php echo $section_slug; ?>set_image_meta_description" value="0" />
											<input type="checkbox" id="<?php echo $section_slug; ?>set_image_meta_description" name="<?php echo $section_slug; ?>set_image_meta_description" value="1" <?php echo $post[$section_slug . 'set_image_meta_description'] ? 'checked="checked"' : '' ?> class="switcher fix_checkbox"/>
											<label for="<?php echo $section_slug; ?>set_image_meta_description"><?php _e('Set Description(s)','wp_all_import_plugin');?></label>
											<div class="switcher-target-<?php echo $section_slug; ?>set_image_meta_description" style="padding-left:23px;">	
												<div class="input">
													<input id="<?php echo $section_slug; ?>image_meta_description_delim_logic_separate" type="radio" name="<?php echo $section_slug; ?>image_meta_description_delim_logic" value="separate" <?php echo ($post[$section_slug . 'image_meta_description_delim_logic'] == 'separate' and ! empty($post[$section_slug . 'image_meta_description_delim'])) ? 'checked="checked"' : ''; ?>/>
													<label for="<?php echo $section_slug; ?>image_meta_description_delim_logic_separate"><?php _e('Separate them with a', 'wp_all_import_plugin'); ?></label>
													<input type="text" class="small" id="<?php echo $section_slug; ?>image_meta_description_delim" name="<?php echo $section_slug; ?>image_meta_description_delim" value="<?php echo esc_attr($post[$section_slug . 'image_meta_description_delim']) ?>" style="width:5%; text-align:center;"/>													
												</div>
												<div class="input">
													<input id="<?php echo $section_slug; ?>image_meta_description_delim_logic_line" type="radio" name="<?php echo $section_slug; ?>image_meta_description_delim_logic" value="line" <?php echo ($post[$section_slug . 'image_meta_description_delim_logic'] == 'line' or empty($post[$section_slug . 'image_meta_description_delim'])) ? 'checked="checked"' : ''; ?>/>
													<label for="<?php echo $section_slug; ?>image_meta_description_delim_logic_line"><?php _e('Enter them one per line', 'wp_all_import_plugin'); ?></label>
												</div>												
												<p style="margin-bottom:5px;"><?php _e('The first description will be linked to the first image, the second description will be linked to the second image, ...', 'wp_all_import_plugin');?></p>
												<textarea name="<?php echo $section_slug; ?>image_meta_description" class="newline rad4"><?php echo esc_attr($post[$section_slug . 'image_meta_description']) ?></textarea>																				
											</div>
										</div>										
										<h4><?php _e('Files', 'wp_all_import_plugin'); ?></h4>
										<div class="advanced_options_files">
											<p style="font-style:italic; display:none;"><?php _e('These options not available if Use images currently in Media Library is selected above.', 'wp_all_import_plugin'); ?></p>
											<div class="input" style="margin:3px 0px;">
												<input type="hidden" name="<?php echo $section_slug; ?>auto_rename_images" value="0" />
												<input type="checkbox" id="<?php echo $section_slug; ?>auto_rename_images" name="<?php echo $section_slug; ?>auto_rename_images" value="1" <?php echo $post[$section_slug . 'auto_rename_images'] ? 'checked="checked"' : ''; ?> class="switcher fix_checkbox"/>
												<label for="<?php echo $section_slug; ?>auto_rename_images"><?php _e('Change image file names to','wp_all_import_plugin');?> </label>
												<div class="input switcher-target-<?php echo $section_slug; ?>auto_rename_images" style="padding-left:23px;">
													<input type="text" id="<?php echo $section_slug; ?>auto_rename_images_suffix" name="<?php echo $section_slug; ?>auto_rename_images_suffix" value="<?php echo esc_attr($post[$section_slug . 'auto_rename_images_suffix']) ?>" style="width:480px;"/> 
													<p class="note"><?php _e('Multiple image will have numbers appended, i.e. image-name-1.jpg, image-name-2.jpg '); ?></p>
												</div>
											</div>
											<div class="input" style="margin:3px 0px;">
												<input type="hidden" name="<?php echo $section_slug; ?>auto_set_extension" value="0" />
												<input type="checkbox" id="<?php echo $section_slug; ?>auto_set_extension" name="<?php echo $section_slug; ?>auto_set_extension" value="1" <?php echo $post[$section_slug . 'auto_set_extension'] ? 'checked="checked"' : '' ?> class="switcher fix_checkbox"/>
												<label for="<?php echo $section_slug; ?>auto_set_extension"><?php _e('Change image file extensions','wp_all_import_plugin');?> </label>
												<div class="input switcher-target-<?php echo $section_slug; ?>auto_set_extension" style="padding-left:23px;">
													<input type="text" id="<?php echo $section_slug; ?>new_extension" name="<?php echo $section_slug; ?>new_extension" value="<?php echo esc_attr($post[$section_slug . 'new_extension']) ?>" placeholder="jpg" style="width:480px;"/>
												</div>
											</div>											
										</div>										
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>