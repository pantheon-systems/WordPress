<div class="wp-all-export-advanced-field-options-content">
	<!-- Options for SQL field -->
	<div class="input cc_field sql_field_type">
		<a href="#help" rel="sql" class="help" style="display:none;" title="<?php _e('%%ID%% will be replaced with the ID of the post being exported, example: SELECT meta_value FROM wp_postmeta WHERE post_id=%%ID%% AND meta_key=\'your_meta_key\';', 'wp_all_export_plugin'); ?>">?</a>								
		<textarea style="width:100%;" rows="5" class="column_value"></textarea>										
	</div>
	<!-- Options for ACF Repeater field -->
	<div class="input cc_field repeater_field_type">
		<input type="hidden" name="repeater_field_item_per_line" value="0"/>
		<input type="checkbox" id="repeater_field_item_per_line" class="switcher" name="repeater_field_item_per_line" value="1" style="margin: 2px;"/>
		<label for="repeater_field_item_per_line"><?php _e("Display each repeater row in its own csv line", "wp_all_export_plugin"); ?></label>
		<div class="input switcher-target-repeater_field_item_per_line" style="margin-top: 10px; padding-left: 15px;">
			<input type="hidden" name="repeater_field_fill_empty_columns" value="0"/>
			<input type="checkbox" id="repeater_field_fill_empty_columns" name="repeater_field_fill_empty_columns" value="1"/>
			<label for="repeater_field_fill_empty_columns"><?php _e("Fill in empty columns", "wp_all_export_plugin"); ?></label>
			<a href="#help" class="wpallexport-help" style="position: relative; top: 0px;" title="<?php _e('If enabled, each repeater row will appear as its own csv line with all post info filled in for every column.', 'wp_all_export_plugin'); ?>">?</a>
		</div>
	</div>
	<!-- Options for Image field from Media section -->
	<div class="input cc_field image_field_type">
		<div class="input">
			<input type="hidden" name="image_field_is_export_featured" value="0"/>
			<input type="checkbox" id="is_image_export_featured" name="image_field_is_export_featured" value="1" style="margin: 2px;" checked="checked"/>
			<label for="is_image_export_featured"><?php _e("Export featured image", "wp_all_export_plugin"); ?></label>					
		</div>
		<div class="input">
			<input type="hidden" name="image_field_is_export_attached_images" value="0"/>
			<input type="checkbox" id="is_image_export_attached_images" class="switcher" name="image_field_is_export_attached_images" value="1" style="margin: 2px;" checked="checked"/>
			<label for="is_image_export_attached_images"><?php _e("Export attached images", "wp_all_export_plugin"); ?></label>					
			<div class="switcher-target-is_image_export_attached_images" style="margin: 5px 2px;">
				<label><?php _e("Separator", "wp_all_export_plugin"); ?></label>
				<input type="text" name="image_field_separator" value="|" style="width: 40px; text-align:center;">
			</div>
		</div>	
	</div>			
	<!-- Options for Date field -->
	<div class="input cc_field date_field_type">
		<select class="date_field_export_data" style="width: 100%; height: 30px;">
			<option value="unix"><?php _e("UNIX timestamp - PHP time()", "wp_all_export_plugin");?></option>
			<option value="php"><?php _e("Natural Language PHP date()", "wp_all_export_plugin");?></option>									
		</select>
		<div class="input pmxe_date_format_wrapper">
			<label style="padding:4px; display: block;"><?php _e("date() Format", "wp_all_export_plugin"); ?></label>			
			<input type="text" class="pmxe_date_format" value="" placeholder="Y-m-d" style="width: 100%;"/>
		</div>
	</div>		
	<!-- Options for Up/Cross sells products -->
	<div class="input cc_field linked_field_type">
		<select class="linked_field_export_data" style="width: 100%; height: 30px;">
			<option value="sku"><?php _e("Product SKU", "wp_all_export_plugin");?></option>
			<option value="id"><?php _e("Product ID", "wp_all_export_plugin");?></option>									
			<option value="name"><?php _e("Product Name", "wp_all_export_plugin");?></option>									
		</select>				
	</div>
	<!-- PHP snippet options -->
	<div class="input php_snipped" style="margin-top:10px;">
		<input type="checkbox" id="coperate_php" name="coperate_php" value="1" class="switcher" style="margin: 2px;"/>
		<label for="coperate_php"><?php _e("Export the value returned by a PHP function", "wp_all_export_plugin"); ?></label>								
		<a href="#help" class="wpallexport-help" title="<?php _e('The value of the field chosen for export will be passed to the PHP function.', 'wp_all_export_plugin'); ?>" style="top: 0;">?</a>								
		<div class="switcher-target-coperate_php" style="margin-top:5px;">
			<div class="wpallexport-free-edition-notice" style="margin: 15px 0;">
				<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=custom-php&utm_campaign=free+wp+all+export+plugin"><?php _e('Upgrade to the Pro edition of WP All Export to use Custom PHP Functions','wp_all_export_plugin');?></a>
				<p><?php _e('If you already own it, remove the free edition and install the Pro edition.','wp_all_export_plugin');?></p>
			</div>
			<?php echo "&lt;?php ";?>
			<input type="text" class="php_code" value="" style="width:50%;" placeholder='your_function_name'/> 
			<?php echo "(\$value); ?&gt;"; ?>

			<?php
				$uploads = wp_upload_dir();
				$functions = $uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';				
			?>

			<div class="input" style="margin-top: 10px;">

				<h4><?php _e('Function Editor', 'wp_all_export_plugin');?><a href="#help" class="wpallexport-help" title="<?php printf(__("Add functions here for use during your export. You can access this file at %s", "wp_all_export_plugin"), preg_replace("%.*wp-content%", "wp-content", $functions));?>" style="top: 0;">?</a></h4>
				
			</div>									

			<textarea id="wp_all_export_code" name="wp_all_export_code"><?php echo "<?php\n\n?>";?></textarea>						

			<div class="input" style="margin-top: 10px;">

				<div class="input" style="display:inline-block; margin-right: 20px;">
					<input type="button" class="button-primary wp_all_export_save_functions" disabled="disabled" value="<?php _e("Save Functions", 'wp_all_export_plugin'); ?>"/>							
					<div class="wp_all_export_functions_preloader"></div>
				</div>						
				<div class="input wp_all_export_saving_status" style="display:inline-block;"></div>
			</div>					
		</div>												
	</div>				
</div>