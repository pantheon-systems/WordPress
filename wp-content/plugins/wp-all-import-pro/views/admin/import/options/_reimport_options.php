<?php if ( ! $this->isWizard  or ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' or $this->isWizard and "new" != $post['wizard_type']): ?>
<h4><?php _e('When WP All Import finds new or changed data...', 'wp_all_import_plugin'); ?></h4>
<?php else: ?>
<h4><?php _e('If this import is run again and WP All Import finds new or changed data...', 'wp_all_import_plugin'); ?></h4>
<?php endif; ?>
<div class="input">
	<input type="hidden" name="create_new_records" value="0" />
	<input type="checkbox" id="create_new_records" name="create_new_records" value="1" <?php echo $post['create_new_records'] ? 'checked="checked"' : '' ?> />
	<label for="create_new_records"><?php _e('Create new posts from records newly present in your file', 'wp_all_import_plugin') ?></label>
	<?php if ( ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' ): ?>
	<a href="#help" class="wpallimport-help" title="<?php _e('New posts will only be created when ID column is present and value in ID column is unique.', 'wp_all_import_plugin') ?>" style="top: -1px;">?</a>
	<?php endif; ?>
</div>
<div class="switcher-target-auto_matching">
	<div class="input">
		<input type="hidden" name="is_delete_missing" value="0" />
		<input type="checkbox" id="is_delete_missing" name="is_delete_missing" value="1" <?php echo $post['is_delete_missing'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
		<label for="is_delete_missing" <?php if ( "new" != $post['wizard_type']): ?>style="color:#ccc;"<?php endif; ?>><?php _e('Delete posts that are no longer present in your file', 'wp_all_import_plugin') ?></label>
		<?php if ( "new" != $post['wizard_type']): ?>
		<a href="#help" class="wpallimport-help" title="<?php _e('Records removed from the import file can only be deleted when importing into New Items. This feature cannot be enabled when importing into Existing Items.', 'wp_all_import_plugin') ?>" style="position:relative; top: -1px;">?</a>
		<?php endif; ?>	
	</div>
	<div class="switcher-target-is_delete_missing" style="padding-left:17px;">
		<div class="input">
			<input type="hidden" name="is_keep_attachments" value="0" />
			<input type="checkbox" id="is_keep_attachments" name="is_keep_attachments" value="1" <?php echo $post['is_keep_attachments'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_keep_attachments"><?php _e('Do not remove attachments', 'wp_all_import_plugin') ?></label>			
		</div>
		<div class="input">
			<input type="hidden" name="is_keep_imgs" value="0" />
			<input type="checkbox" id="is_keep_imgs" name="is_keep_imgs" value="1" <?php echo $post['is_keep_imgs'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_keep_imgs"><?php _e('Do not remove images', 'wp_all_import_plugin') ?></label>			
		</div>
		<div class="input">
			<input type="hidden" name="is_update_missing_cf" value="0" />
			<input type="checkbox" id="is_update_missing_cf" name="is_update_missing_cf" value="1" <?php echo $post['is_update_missing_cf'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_update_missing_cf"><?php _e('Instead of deletion, set Custom Field', 'wp_all_import_plugin') ?></label>			
			<div class="switcher-target-is_update_missing_cf" style="padding-left:17px;">
				<div class="input">
					<?php _e('Name', 'wp_all_import_plugin') ?>
					<input type="text" name="update_missing_cf_name" value="<?php echo esc_attr($post['update_missing_cf_name']) ?>" />
					<?php _e('Value', 'wp_all_import_plugin') ?>
					<input type="text" name="update_missing_cf_value" value="<?php echo esc_attr($post['update_missing_cf_value']) ?>" />									
				</div>
			</div>
		</div>
		<div class="input">
			<input type="hidden" name="set_missing_to_draft" value="0" />
			<input type="checkbox" id="set_missing_to_draft" name="set_missing_to_draft" value="1" <?php echo $post['set_missing_to_draft'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="set_missing_to_draft"><?php _e('Instead of deletion, change post status to Draft', 'wp_all_import_plugin') ?></label>					
		</div>
	</div>	
</div>	
<div class="input">
	<input type="hidden" id="is_keep_former_posts" name="is_keep_former_posts" value="yes" />				
	<input type="checkbox" id="is_not_keep_former_posts" name="is_keep_former_posts" value="no" <?php echo "yes" != $post['is_keep_former_posts'] ? 'checked="checked"': '' ?> class="switcher" />
	<label for="is_not_keep_former_posts"><?php _e('Update existing posts with changed data in your file', 'wp_all_import_plugin') ?></label>
	<?php if ( $this->isWizard and "new" == $post['wizard_type'] and empty(PMXI_Plugin::$session->deligate)): ?>
	<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('These options will only be used if you run this import again later. All data is imported the first time you run an import.<br/><br/>Note that WP All Import will only update/remove posts created by this import. If you want to match to posts that already exist on this site, use Existing Items in Step 1.', 'wp_all_import_plugin') ?>">?</a>	
	<?php endif; ?>
	<div class="switcher-target-is_not_keep_former_posts" style="padding-left:17px;">
		<input type="radio" id="update_all_data" class="switcher" name="update_all_data" value="yes" <?php echo 'no' != $post['update_all_data'] ? 'checked="checked"': '' ?>/>
		<label for="update_all_data"><?php _e('Update all data', 'wp_all_import_plugin' )?></label><br>
		
		<input type="radio" id="update_choosen_data" class="switcher" name="update_all_data" value="no" <?php echo 'no' == $post['update_all_data'] ? 'checked="checked"': '' ?>/>
		<label for="update_choosen_data"><?php _e('Choose which data to update', 'wp_all_import_plugin' )?></label><br>
		<div class="switcher-target-update_choosen_data"  style="padding-left:27px;">
			<div class="input">
				<h4 class="wpallimport-trigger-options wpallimport-select-all" rel="<?php _e("Unselect All", "wp_all_import_plugin"); ?>"><?php _e("Select All", "wp_all_import_plugin"); ?></h4>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_status" value="0" />
				<input type="checkbox" id="is_update_status" name="is_update_status" value="1" <?php echo $post['is_update_status'] ? 'checked="checked"': '' ?> />
				<label for="is_update_status"><?php _e('Post status', 'wp_all_import_plugin') ?></label>
				<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('Hint: uncheck this box to keep trashed posts in the trash.', 'wp_all_import_plugin') ?>">?</a>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_title" value="0" />
				<input type="checkbox" id="is_update_title" name="is_update_title" value="1" <?php echo $post['is_update_title'] ? 'checked="checked"': '' ?> />
				<label for="is_update_title"><?php _e('Title', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_author" value="0" />
				<input type="checkbox" id="is_update_author" name="is_update_author" value="1" <?php echo $post['is_update_author'] ? 'checked="checked"': '' ?> />
				<label for="is_update_author"><?php _e('Author', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_slug" value="0" />
				<input type="checkbox" id="is_update_slug" name="is_update_slug" value="1" <?php echo $post['is_update_slug'] ? 'checked="checked"': '' ?> />
				<label for="is_update_slug"><?php _e('Slug', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_content" value="0" />
				<input type="checkbox" id="is_update_content" name="is_update_content" value="1" <?php echo $post['is_update_content'] ? 'checked="checked"': '' ?> />
				<label for="is_update_content"><?php _e('Content', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_excerpt" value="0" />
				<input type="checkbox" id="is_update_excerpt" name="is_update_excerpt" value="1" <?php echo $post['is_update_excerpt'] ? 'checked="checked"': '' ?> />
				<label for="is_update_excerpt"><?php _e('Excerpt/Short Description', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_dates" value="0" />
				<input type="checkbox" id="is_update_dates" name="is_update_dates" value="1" <?php echo $post['is_update_dates'] ? 'checked="checked"': '' ?> />
				<label for="is_update_dates"><?php _e('Dates', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_menu_order" value="0" />
				<input type="checkbox" id="is_update_menu_order" name="is_update_menu_order" value="1" <?php echo $post['is_update_menu_order'] ? 'checked="checked"': '' ?> />
				<label for="is_update_menu_order"><?php _e('Menu order', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_parent" value="0" />
				<input type="checkbox" id="is_update_parent" name="is_update_parent" value="1" <?php echo $post['is_update_parent'] ? 'checked="checked"': '' ?> />
				<label for="is_update_parent"><?php _e('Parent post', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_post_type" value="0" />
				<input type="checkbox" id="is_update_post_type" name="is_update_post_type" value="1" <?php echo $post['is_update_post_type'] ? 'checked="checked"': '' ?> />
				<label for="is_update_post_type"><?php _e('Post type', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_comment_status" value="0" />
				<input type="checkbox" id="is_update_comment_status" name="is_update_comment_status" value="1" <?php echo $post['is_update_comment_status'] ? 'checked="checked"': '' ?> />
				<label for="is_update_comment_status"><?php _e('Comment status', 'wp_all_import_plugin') ?></label>
			</div>	
			<div class="input">
				<input type="hidden" name="is_update_attachments" value="0" />
				<input type="checkbox" id="is_update_attachments" name="is_update_attachments" value="1" <?php echo $post['is_update_attachments'] ? 'checked="checked"': '' ?> />
				<label for="is_update_attachments"><?php _e('Attachments', 'wp_all_import_plugin') ?></label>
			</div>	
			
			<?php 

				// add-ons re-import options
				do_action('pmxi_reimport', $post_type, $post);

			?>						

			<div class="input">
				<input type="hidden" name="is_update_images" value="0" />
				<input type="checkbox" id="is_update_images" name="is_update_images" value="1" <?php echo $post['is_update_images'] ? 'checked="checked"': '' ?> class="switcher" />
				<label for="is_update_images"><?php _e('Images', 'wp_all_import_plugin') ?></label>
				<!--a href="#help" class="wpallimport-help" title="<?php _e('This will keep the featured image if it exists, so you could modify the post image manually, and then do a reimport, and it would not overwrite the manually modified post image.', 'wp_all_import_plugin') ?>">?</a-->
				<div class="switcher-target-is_update_images" style="padding-left:17px;">
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_images_logic_full_update" name="update_images_logic" value="full_update" <?php echo ( "full_update" == $post['update_images_logic'] ) ? 'checked="checked"': '' ?> />
						<label for="update_images_logic_full_update"><?php _e('Update all images', 'wp_all_import_plugin') ?></label>						
					</div>
					<?php $is_show_add_new_images = apply_filters('wp_all_import_is_show_add_new_images', true, $post_type); ?>
					<?php if ($is_show_add_new_images): ?>
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_images_logic_add_new" name="update_images_logic" value="add_new" <?php echo ( "add_new" == $post['update_images_logic'] ) ? 'checked="checked"': '' ?> />
						<label for="update_images_logic_add_new"><?php _e('Don\'t touch existing images, append new images', 'wp_all_import_plugin') ?></label>
					</div>
					<?php endif; ?>
				</div>
			</div>			
			<div class="input">			
				<input type="hidden" name="custom_fields_list" value="0" />			
				<input type="hidden" name="is_update_custom_fields" value="0" />
				<input type="checkbox" id="is_update_custom_fields" name="is_update_custom_fields" value="1" <?php echo $post['is_update_custom_fields'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_custom_fields"><?php _e('Custom Fields', 'wp_all_import_plugin') ?></label>
				<!--a href="#help" class="wpallimport-help" title="<?php _e('If Keep Custom Fields box is checked, it will keep all Custom Fields, and add any new Custom Fields specified in Custom Fields section, as long as they do not overwrite existing fields. If \'Only keep this Custom Fields\' is specified, it will only keep the specified fields.', 'wp_all_import_plugin') ?>">?</a-->
				<div class="switcher-target-is_update_custom_fields" style="padding-left:17px;">
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_full_update" name="update_custom_fields_logic" value="full_update" <?php echo ( "full_update" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_full_update"><?php _e('Update all Custom Fields', 'wp_all_import_plugin') ?></label>								
					</div>					
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_only" name="update_custom_fields_logic" value="only" <?php echo ( "only" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_only"><?php _e('Update only these Custom Fields, leave the rest alone', 'wp_all_import_plugin') ?></label>														
						<div class="switcher-target-update_custom_fields_logic_only pmxi_choosen" style="padding-left:17px;">								
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "only" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_only_list"/>										
						</div>						
					</div>
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_all_except" name="update_custom_fields_logic" value="all_except" <?php echo ( "all_except" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_all_except"><?php _e('Leave these fields alone, update all other Custom Fields', 'wp_all_import_plugin') ?></label>														
						<div class="switcher-target-update_custom_fields_logic_all_except pmxi_choosen" style="padding-left:17px;">						
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "all_except" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_except_list"/>																				
						</div>						
					</div>
				</div>
			</div>	
			<div class="input">
				<input type="hidden" name="taxonomies_list" value="0" />
				<input type="hidden" name="is_update_categories" value="0" />
				<input type="checkbox" id="is_update_categories" name="is_update_categories" value="1" class="switcher" <?php echo $post['is_update_categories'] ? 'checked="checked"': '' ?> />
				<label for="is_update_categories"><?php _e('Taxonomies (incl. Categories and Tags)', 'wp_all_import_plugin') ?></label>
				<div class="switcher-target-is_update_categories" style="padding-left:17px;">
					<?php
					$existing_taxonomies = array();
					$hide_taxonomies = (class_exists('PMWI_Plugin')) ? array('product_type', 'product_visibility') : array();
					$post_taxonomies = array_diff_key(get_taxonomies_by_object_type($post['is_override_post_type'] ? array_keys(get_post_types( '', 'names' )) : array($post_type), 'object'), array_flip($hide_taxonomies));
					if (!empty($post_taxonomies)): 
						foreach ($post_taxonomies as $ctx):  if ("" == $ctx->labels->name or (class_exists('PMWI_Plugin') and $post_type == "product" and strpos($ctx->name, "pa_") === 0)) continue;
							$existing_taxonomies[] = $ctx->name;												
						endforeach;
					endif;
					?>
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_categories_logic_all_except" name="update_categories_logic" value="all_except" <?php echo ( "all_except" == $post['update_categories_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_categories_logic_all_except"><?php _e('Leave these taxonomies alone, update all others', 'wp_all_import_plugin') ?></label>						
						<div class="switcher-target-update_categories_logic_all_except pmxi_choosen" style="padding-left:17px;">					
							<span class="hidden choosen_values"><?php if (!empty($existing_taxonomies)) echo esc_html(implode(',', $existing_taxonomies));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['taxonomies_list']) and "all_except" == $post['update_categories_logic']) echo esc_html(implode(',', $post['taxonomies_list'])); ?>" type="hidden" name="taxonomies_except_list"/>																				
						</div>						
					</div>
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_categories_logic_only" name="update_categories_logic" value="only" <?php echo ( "only" == $post['update_categories_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_categories_logic_only"><?php _e('Update only these taxonomies, leave the rest alone', 'wp_all_import_plugin') ?></label>						
						<div class="switcher-target-update_categories_logic_only pmxi_choosen" style="padding-left:17px;">							
							<span class="hidden choosen_values"><?php if (!empty($existing_taxonomies)) echo esc_html(implode(',', $existing_taxonomies));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['taxonomies_list']) and "only" == $post['update_categories_logic']) echo esc_html(implode(',', $post['taxonomies_list'])); ?>" type="hidden" name="taxonomies_only_list"/>										
						</div>						
					</div>
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_categories_logic_full_update" name="update_categories_logic" value="full_update" <?php echo ( "full_update" == $post['update_categories_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_categories_logic_full_update"><?php _e('Remove existing taxonomies, add new taxonomies', 'wp_all_import_plugin') ?></label>
					</div>
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_categories_logic_add_new" name="update_categories_logic" value="add_new" <?php echo ( "add_new" == $post['update_categories_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_categories_logic_add_new"><?php _e('Only add new', 'wp_all_import_plugin') ?></label>
					</div>
				</div>
			</div>
			<?php
				// add-ons re-import options
				do_action('pmxi_reimport_options_after_taxonomies', $post_type, $post);
			?>
		</div>
	</div>
</div>	