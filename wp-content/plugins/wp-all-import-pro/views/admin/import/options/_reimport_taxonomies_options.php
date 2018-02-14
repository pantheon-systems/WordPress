<?php if ( ! $this->isWizard  or ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' or $this->isWizard and "new" != $post['wizard_type']): ?>
<?php
	$custom_type = get_taxonomy($post['taxonomy_type']);
	if (empty($custom_type)){
		$custom_type = new stdClass();
		$custom_type->labels = new stdClass();
		$custom_type->labels->name = __('Taxonomy Terms', 'wp_all_import_plugin');
		$custom_type->labels->singular_name = __('Taxonomy Term', 'wp_all_import_plugin');
	}
?>
<h4><?php _e('When WP All Import finds new or changed data...', 'wp_all_import_plugin'); ?></h4>
<?php else: ?>
<h4><?php _e('If this import is run again and WP All Import finds new or changed data...', 'wp_all_import_plugin'); ?></h4>
<?php endif; ?>
<div class="input">
	<input type="hidden" name="create_new_records" value="0" />
	<input type="checkbox" id="create_new_records" name="create_new_records" value="1" <?php echo $post['create_new_records'] ? 'checked="checked"' : '' ?> />
	<label for="create_new_records"><?php printf(__('Create new %s from records newly present in your file', 'wp_all_import_plugin'), $custom_type->labels->name); ?></label>
	<?php if ( ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' ): ?>
	<a href="#help" class="wpallimport-help" title="<?php printf(__('New %s will only be created when ID column is present and value in ID column is unique.', 'wp_all_import_plugin'), $custom_type->labels->name); ?>" style="top: -1px;">?</a>
	<?php endif; ?>
</div>
<div class="switcher-target-auto_matching">
	<div class="input">
		<input type="hidden" name="is_delete_missing" value="0" />
		<input type="checkbox" id="is_delete_missing" name="is_delete_missing" value="1" <?php echo $post['is_delete_missing'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
		<label for="is_delete_missing" <?php if ( "new" != $post['wizard_type']): ?>style="color:#ccc;"<?php endif; ?>><?php printf(__('Delete %s that are no longer present in your file', 'wp_all_import_plugin'), $custom_type->labels->name); ?></label>
		<?php if ( "new" != $post['wizard_type']): ?>
		<a href="#help" class="wpallimport-help" title="<?php _e('Records removed from the import file can only be deleted when importing into New Items. This feature cannot be enabled when importing into Existing Items.', 'wp_all_import_plugin') ?>" style="position:relative; top: -1px;">?</a>
		<?php endif; ?>	
	</div>
	<div class="switcher-target-is_delete_missing" style="padding-left:17px;">
		<div class="input">
			<input type="hidden" name="is_keep_imgs" value="0" />
			<input type="checkbox" id="is_keep_imgs" name="is_keep_imgs" value="1" <?php echo $post['is_keep_imgs'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_keep_imgs"><?php _e('Do not remove images', 'wp_all_import_plugin') ?></label>			
		</div>
		<div class="input">
			<input type="hidden" name="is_update_missing_cf" value="0" />
			<input type="checkbox" id="is_update_missing_cf" name="is_update_missing_cf" value="1" <?php echo $post['is_update_missing_cf'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_update_missing_cf"><?php _e('Instead of deletion, set Term Meta', 'wp_all_import_plugin') ?></label>
			<div class="switcher-target-is_update_missing_cf" style="padding-left:17px;">
				<div class="input">
					<?php _e('Name', 'wp_all_import_plugin') ?>
					<input type="text" name="update_missing_cf_name" value="<?php echo esc_attr($post['update_missing_cf_name']) ?>" />
					<?php _e('Value', 'wp_all_import_plugin') ?>
					<input type="text" name="update_missing_cf_value" value="<?php echo esc_attr($post['update_missing_cf_value']) ?>" />									
				</div>
			</div>
		</div>
	</div>	
</div>	
<div class="input">
	<input type="hidden" id="is_keep_former_posts" name="is_keep_former_posts" value="yes" />				
	<input type="checkbox" id="is_not_keep_former_posts" name="is_keep_former_posts" value="no" <?php echo "yes" != $post['is_keep_former_posts'] ? 'checked="checked"': '' ?> class="switcher" />
	<label for="is_not_keep_former_posts"><?php printf(__('Update existing %s with changed data in your file', 'wp_all_import_plugin'), $custom_type->labels->name); ?></label>
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
				<input type="hidden" name="is_update_title" value="0" />
				<input type="checkbox" id="is_update_title" name="is_update_title" value="1" <?php echo $post['is_update_title'] ? 'checked="checked"': '' ?> />
				<label for="is_update_title"><?php _e('Name', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_slug" value="0" />
				<input type="checkbox" id="is_update_slug" name="is_update_slug" value="1" <?php echo $post['is_update_slug'] ? 'checked="checked"': '' ?> />
				<label for="is_update_slug"><?php _e('Slug', 'wp_all_import_plugin') ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_content" value="0" />
				<input type="checkbox" id="is_update_content" name="is_update_content" value="1" <?php echo $post['is_update_content'] ? 'checked="checked"': '' ?> />
				<label for="is_update_content"><?php _e('Description', 'wp_all_import_plugin') ?></label>
			</div>
<!--			<div class="input">-->
<!--				<input type="hidden" name="is_update_menu_order" value="0" />-->
<!--				<input type="checkbox" id="is_update_menu_order" name="is_update_menu_order" value="1" --><?php //echo $post['is_update_menu_order'] ? 'checked="checked"': '' ?><!-- />-->
<!--				<label for="is_update_menu_order">--><?php //_e('Order', 'wp_all_import_plugin') ?><!--</label>-->
<!--			</div>-->
			<div class="input">
				<input type="hidden" name="is_update_parent" value="0" />
				<input type="checkbox" id="is_update_parent" name="is_update_parent" value="1" <?php echo $post['is_update_parent'] ? 'checked="checked"': '' ?> />
				<label for="is_update_parent"><?php _e('Parent term', 'wp_all_import_plugin') ?></label>
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
				</div>
			</div>			
			<div class="input">			
				<input type="hidden" name="custom_fields_list" value="0" />			
				<input type="hidden" name="is_update_custom_fields" value="0" />
				<input type="checkbox" id="is_update_custom_fields" name="is_update_custom_fields" value="1" <?php echo $post['is_update_custom_fields'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_custom_fields"><?php _e('Term Meta', 'wp_all_import_plugin') ?></label>
				<!--a href="#help" class="wpallimport-help" title="<?php _e('If Keep Term Meta box is checked, it will keep all Term Meta, and add any new Term Meta specified in Term Meta section, as long as they do not overwrite existing fields. If \'Only keep this Term Meta\' is specified, it will only keep the specified fields.', 'wp_all_import_plugin') ?>">?</a-->
				<div class="switcher-target-is_update_custom_fields" style="padding-left:17px;">
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_full_update" name="update_custom_fields_logic" value="full_update" <?php echo ( "full_update" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_full_update"><?php _e('Update all Term Meta', 'wp_all_import_plugin') ?></label>
					</div>					
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_only" name="update_custom_fields_logic" value="only" <?php echo ( "only" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_only"><?php _e('Update only these Term Meta, leave the rest alone', 'wp_all_import_plugin') ?></label>
						<div class="switcher-target-update_custom_fields_logic_only pmxi_choosen" style="padding-left:17px;">								
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "only" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_only_list"/>										
						</div>						
					</div>
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_all_except" name="update_custom_fields_logic" value="all_except" <?php echo ( "all_except" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_all_except"><?php _e('Leave these fields alone, update all other Term Meta', 'wp_all_import_plugin') ?></label>
						<div class="switcher-target-update_custom_fields_logic_all_except pmxi_choosen" style="padding-left:17px;">						
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "all_except" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_except_list"/>																				
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	