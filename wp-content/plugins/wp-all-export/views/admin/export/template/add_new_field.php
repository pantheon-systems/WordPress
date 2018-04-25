<form>
	<div class="wp-all-export-field-options">
		<div class="input" style="margin-bottom:10px;">
			<label for="column_value_default" style="padding:4px; display: block;"><?php _e('What field would you like to export?', 'wp_all_export_plugin' )?></label>
			<div class="clear"></div>
			<?php echo $available_fields_view; ?>																													
		</div>					
		
		<div class="input">
			<label style="padding:4px; display: block;"><?php _e('What would you like to name the column/element in your exported file?','wp_all_export_plugin');?></label>
			<div class="clear"></div>
			<input type="text" class="column_name" value="" style="width:50%"/>
		</div>

		<!-- Advanced Field Options -->
		
		<?php include_once 'advanced_field_options.php'; ?>

		<div class="input disabled_fields_upgrade_notice" style="vertical-align:middle; position: relative;">
			<span class="wpallexport-free-edition-notice">									
				<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=118611&edd_options%5Bprice_id%5D=1&utm_source=wordpress.org&utm_medium=wooco+orders&utm_campaign=free+wp+all+export+plugin"><?php _e('Upgrade to the Pro edition of WP All Export to Export Order Data','wp_all_export_plugin');?></a>
				<p><?php _e('If you already own it, remove the free edition and install the Pro edition.','wp_all_export_plugin');?></p>
			</span>														
		</div>

	</div>																		
	<div class="input wp-all-export-edit-column-buttons">			
		<input type="button" class="delete_action" value="<?php _e("Delete", "wp_all_export_plugin"); ?>" style="border: none;"/>									
		<input type="button" class="save_action"   value="<?php _e("Done", "wp_all_export_plugin"); ?>"   style="border: none;"/>	
		<input type="button" class="close_action"  value="<?php _e("Close", "wp_all_export_plugin"); ?>"  style="border: none;"/>
	</div>
</form>