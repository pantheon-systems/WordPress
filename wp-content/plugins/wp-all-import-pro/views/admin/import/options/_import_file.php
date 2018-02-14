
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
						printf(__('This %s file has errors and is not valid.', 'wp_all_import_plugin'), $file_type); 
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
						
						<input type="hidden" value="<?php echo $post['type']; ?>" name="new_type"/>

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
								<!--a href="javascript:void(0);" class="wpallimport-download-from-url"><?php _e('Upload', 'wp_all_import_plugin'); ?></a-->
							</div>
							<input type="hidden" name="downloaded"/>
						</div>
						<div class="wpallimport-upload-type-container" rel="file_type">		
							<?php $upload_dir = wp_upload_dir(); ?>					
							<div class="wpallimport-file-type-options">								
								
								<?php
									$files_directory = DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR;

									$local_files = array_merge(
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.xml', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.gz', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.zip', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.gzip', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.csv', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.dat', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.psv', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.json', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.txt', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.sql', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.xls', PMXI_Helper::GLOB_NODIR),
										PMXI_Helper::safe_glob($upload_dir['basedir'] . $files_directory . '*.xlsx', PMXI_Helper::GLOB_NODIR)
									);
									sort($local_files);
									$sizes = array();
									if ( ! empty($local_files)){
										foreach ($local_files as $file) {
											$sizes[] = filesize($upload_dir['basedir'] . $files_directory . $file);
										}
									}
								?>
								<script type="text/javascript">									
									var existing_file_sizes = <?php echo json_encode($sizes) ?>;
								</script>

								<select name="" id="file_selector">
									<option value=""><?php _e('Select a previously uploaded file', 'wp_all_import_plugin'); ?></option>
									<?php foreach ($local_files as $file) :?>
										<option value="<?php echo $file; ?>" <?php if ( 'file' == $import->type and $file == basename(esc_attr($import->path))):?>selected="selected"<?php endif; ?>><?php echo basename($file); ?></option>
									<?php endforeach; ?>
								</select>
								
								<input type="hidden" name="file" value="<?php if ('file' == $import->type) echo esc_attr($import->path); ?>"/>	

								<script type="text/javascript">									
									var existing_file_sizes = <?php echo json_encode($sizes) ?>;
								</script>
								<div class="wpallimport-note" style="width:60%; margin: 0 auto; ">
									<?php printf(__('Upload files to <strong>%s</strong> and they will appear in this list', 'wp_all_import_plugin'), $upload_dir['basedir'] . $files_directory) ?>
								</div>
							</div>
						</div>						
					</td>
				</tr>
			</table>
		</div>		
	</div>
</div>