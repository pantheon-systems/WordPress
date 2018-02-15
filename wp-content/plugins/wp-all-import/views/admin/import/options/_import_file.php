<script type="text/javascript">
	var plugin_url = '<?php echo WP_ALL_IMPORT_ROOT_URL; ?>';
</script>

<div class="change_file">

	<div class="rad4 first-step-errors error-upload-rejected">
		<div class="wpallimport-notify-wrapper">
			<div class="error-headers exclamation">
				<h3><?php _e('File upload rejected by server', 'wp_all_import_plugin');?></h3>
				<h4><?php _e("Contact your host and have them check your server's error log.", "wp_all_import_plugin"); ?></h4>
			</div>		
		</div>		
		<a class="button button-primary button-hero wpallimport-large-button wpallimport-notify-read-more" href="http://www.wpallimport.com/documentation/troubleshooting/problems-with-import-files/" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
	</div>

	<div class="rad4 first-step-errors error-file-validation" <?php if ( ! empty($upload_validation) ): ?> style="display:block;" <?php endif; ?>>
		<div class="wpallimport-notify-wrapper">
			<div class="error-headers exclamation">
				<h3><?php _e('There\'s a problem with your import file', 'wp_all_import_plugin');?></h3>
				<h4>
					<?php 
					if ( ! empty($upload_validation) ): 										
						$file_type = strtoupper(pmxi_getExtension($post['file']));
						printf(__('Please verify that the file you using is a valid %s file.', 'wp_all_import_plugin'), $file_type); 
					endif;
					?>
				</h4>
			</div>		
		</div>		
		<a class="button button-primary button-hero wpallimport-large-button wpallimport-notify-read-more" href="http://www.wpallimport.com/documentation/troubleshooting/problems-with-import-files/#invalid" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
	</div>	
	
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header" style="padding-left:30px;">
			<h3><?php _e('Import File','wp_all_import_plugin');?></h3>	
		</div>
		<div class="wpallimport-collapsed-content" style="padding-bottom: 40px;">
			<hr>
			<table class="form-table" style="max-width:none;">
				<tr>
					<td colspan="3">

						<div class="wpallimport-import-types">
							<h3><?php _e('Specify the location of the file to use for future runs of this import.', 'wp_all_import_plugin'); ?></h3>
							<a class="wpallimport-import-from wpallimport-upload-type <?php echo 'upload' == $import->type ? 'selected' : '' ?>" rel="upload_type" href="javascript:void(0);">
								<span class="wpallimport-icon"></span>
								<span class="wpallimport-icon-label"><?php _e('Upload a file', 'wp_all_import_plugin'); ?></span>
							</a>
							<a class="wpallimport-import-from wpallimport-url-type <?php echo 'url' == $import->type ? 'selected' : '' ?>" rel="url_type" href="javascript:void(0);">
								<span class="wpallimport-icon"></span>
								<span class="wpallimport-icon-label"><?php _e('Download from URL', 'wp_all_import_plugin'); ?></span>
							</a>
							<a class="wpallimport-import-from wpallimport-file-type <?php echo 'file' == $import->type ? 'selected' : '' ?>" rel="file_type" href="javascript:void(0);">
								<span class="wpallimport-icon"></span>
								<span class="wpallimport-icon-label"><?php _e('Use existing file', 'wp_all_import_plugin'); ?></span>
							</a>
						</div>						
						
						<input type="hidden" value="upload" name="new_type"/>

						<div class="wpallimport-upload-type-container" rel="upload_type">							
							<div id="plupload-ui" class="wpallimport-file-type-options">
					            <div>				                
					                <input type="hidden" name="filepath" value="<?php if ('upload' == $import->type) echo $import->path; ?>" id="filepath"/>
					                <a id="select-files" href="javascript:void(0);"/><?php _e('Click here to select file from your computer...', 'wp_all_import_plugin'); ?></a>
					                <div id="progressbar" class="wpallimport-progressbar">
					                	<?php if ('upload' == $import->type) _e( '<span>Upload Complete</span> - '.basename($import->path).' 100%', 'wp_all_import_plugin'); ?>
					                </div>
					                <div id="progress" class="wpallimport-progress" <?php if ('upload' == $import->type):?>style="display: block;"<?php endif;?>>
					                	<div id="upload_process" class="wpallimport-upload-process"></div>				                	
					                </div>
					            </div>
					        </div>
						</div>
						<div class="wpallimport-upload-type-container" rel="url_type">							
							<div class="wpallimport-file-type-options">
								<span class="wpallimport-url-icon"></span>
								<input type="text" class="regular-text" name="url" value="<?php echo ('url' == $import->type) ? esc_attr($import->path) : 'Enter a web address to download the file from...'; ?>"/> 								
								<div class="wpallimport-free-edition-notice">									
									<a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0&utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=download-from-url" target="_blank" class="upgrade_link"><?php _e('Upgrade to the Pro edition of WP All Import to Download from URL', 'wp_all_import_plugin');?></a>
									<p style="margin-top:16px;"><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_import_plugin'); ?></p>
								</div>
							</div>
							<input type="hidden" name="downloaded"/>
						</div>
						<div class="wpallimport-upload-type-container" rel="file_type">		
							<?php $upload_dir = wp_upload_dir(); ?>					
							<div class="wpallimport-file-type-options">								
								
								<div id="file_selector" class="dd-container" style="width: 600px;">
									<div class="dd-select" style="width: 600px; background: none repeat scroll 0% 0% rgb(238, 238, 238);">
										<input type="hidden" class="dd-selected-value" value="">
										<a class="dd-selected" style="color: rgb(207, 206, 202);">
											<label class="dd-selected-text "><?php _e('Select a previously uploaded file', 'wp_all_import_plugin'); ?></label>
										</a>
										<span class="dd-pointer dd-pointer-down"></span>
									</div>									
								</div>								
								
								<input type="hidden" name="file" value="<?php if ('file' == $import->type) echo esc_attr($import->path); ?>"/>	
								
								<div class="wpallimport-note" style="margin: 0 auto; ">
									<?php printf(__('Files uploaded to <strong>%s</strong> will appear in this list.', 'wp_all_import_plugin'), $upload_dir['basedir'] . '/wpallimport/files'); ?>									
								</div>
								<div class="wpallimport-free-edition-notice">									
									<a href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0&utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=use-existing-file" target="_blank" class="upgrade_link"><?php _e('Upgrade to the Pro edition of WP All Import to Use Existing Files', 'wp_all_import_plugin');?></a>
									<p style="margin-top:16px;"><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_import_plugin'); ?></p>
								</div>
							</div>
						</div>						
					</td>
				</tr>
			</table>
		</div>		
	</div>
</div>