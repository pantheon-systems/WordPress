<?php 
/**
 * Admin Settings page
 *
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */
class PMXI_Admin_Settings extends PMXI_Controller_Admin {

	public static $path;

	public static $upload_transient;

	public function __construct(){	

		parent::__construct();

		self::$upload_transient = 'pmxi_uploads_path';		

		$uploads = wp_upload_dir();	

		$is_secure_import = PMXI_Plugin::getInstance()->getOption('secure');
		
		if ( ! $is_secure_import ){

			self::$path = wp_all_import_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY );
			
		}
		else {			

			self::$path = get_transient( self::$upload_transient );

			if ( empty(self::$path) ) {
				self::$path = wp_all_import_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY );
				set_transient( self::$upload_transient, self::$path);
			}
		}
		
		$sleep = apply_filters( 'wp_all_import_shard_delay', 0 );
		usleep($sleep);
	}
	
	public function index() {

		$this->data['post'] = $post = $this->input->post(PMXI_Plugin::getInstance()->getOption());		
		
		if ($this->input->post('is_settings_submitted')) { // save settings form
			check_admin_referer('edit-settings', '_wpnonce_edit-settings');
			
			if ( ! preg_match('%^\d+$%', $post['history_file_count'])) {
				$this->errors->add('form-validation', __('History File Count must be a non-negative integer', 'wp_all_import_plugin'));
			}
			if ( ! preg_match('%^\d+$%', $post['history_file_age'])) {
				$this->errors->add('form-validation', __('History Age must be a non-negative integer', 'wp_all_import_plugin'));
			}
			if (empty($post['html_entities'])) $post['html_entities'] = 0;
			if (empty($post['utf8_decode'])) $post['utf8_decode'] = 0;
			
			if ( ! $this->errors->get_error_codes()) { // no validation errors detected

				PMXI_Plugin::getInstance()->updateOption($post);

				if (empty($_POST['pmxi_license_activate']) and empty($_POST['pmxi_license_deactivate'])) {
					foreach ($this->data['addons'] as $class => $addon) {
						$post['statuses'][$class] = $this->check_license($class);
					}					
					PMXI_Plugin::getInstance()->updateOption($post);
				}				

				isset( $_POST['pmxi_license_activate'] ) and $this->activate_licenses();

				$files = new PMXI_File_List(); $files->sweepHistory(); // adjust file history to new settings specified
				
				wp_redirect(add_query_arg('pmxi_nt', urlencode(__('Settings saved', 'wp_all_import_plugin')), $this->baseUrl)); die();
			}
		}
		/*else{			

			foreach ($this->data['addons'] as $class => $addon) {
				$post['statuses'][$class] = $this->check_license($class);
			}								

			PMXI_Plugin::getInstance()->updateOption($post);	
		}*/

		if ($this->input->post('is_templates_submitted')) { // delete templates form

			check_admin_referer('delete-templates', '_wpnonce_delete-templates');

			if ($this->input->post('import_templates')){

				if (!empty($_FILES)){
					$file_name = $_FILES['template_file']['name'];
					$file_size = $_FILES['template_file']['size'];
					$tmp_name  = $_FILES['template_file']['tmp_name'];										
					
					if(isset($file_name)) 
					{				
						
						$filename  = stripslashes($file_name);
						$extension = strtolower(pmxi_getExtension($filename));
										
						if (($extension != "txt")) 
						{							
							$this->errors->add('form-validation', __('Unknown File extension. Only txt files are permitted', 'wp_all_import_plugin'));
						}
						else {
							$import_data = @file_get_contents($tmp_name);
							if (!empty($import_data)){
								$templates_data = json_decode($import_data, true);
								
								if ( ! empty($templates_data) ){
									if ( ! empty($templates_data[0]['options']) && is_array($templates_data[0]['options'])){
										$templateOptions = $templates_data[0]['options'];
									}
									else{
										$templateOptions = empty($templates_data[0]['options']) ? false : unserialize($templates_data[0]['options']);
									}
									if ( empty($templateOptions) ){
										$this->errors->add('form-validation', __('The template is invalid. Options are missing.', 'wp_all_import_plugin'));
									}
									else{
										if (isset($templateOptions['is_user_export'])){
											$this->errors->add('form-validation', __('The template you\'ve uploaded is intended to be used with WP All Export plugin.', 'wp_all_import_plugin'));
										}
										else{
											$template = new PMXI_Template_Record();
											foreach ($templates_data as $template_data) {
												unset($template_data['id']);
												$template->clear()->set($template_data)->insert();
											}
											wp_redirect(add_query_arg('pmxi_nt', urlencode(sprintf(_n('%d template imported', '%d templates imported', count($templates_data), 'wp_all_import_plugin'), count($templates_data))), $this->baseUrl)); die();
										}
									}
								}
								else $this->errors->add('form-validation', __('Wrong imported data format', 'wp_all_import_plugin'));							
							}
							else $this->errors->add('form-validation', __('File is empty or doesn\'t exests', 'wp_all_import_plugin'));
						}
					}
					else $this->errors->add('form-validation', __('Undefined entry!', 'wp_all_import_plugin'));
				}
				else $this->errors->add('form-validation', __('Please select file.', 'wp_all_import_plugin'));

			}
			else{
				$templates_ids = $this->input->post('templates', array());
				if (empty($templates_ids)) {
					$this->errors->add('form-validation', __('Templates must be selected', 'wp_all_import_plugin'));
				}
				
				if ( ! $this->errors->get_error_codes()) { // no validation errors detected
					if ($this->input->post('delete_templates')){
						$template = new PMXI_Template_Record();
						foreach ($templates_ids as $template_id) {
							$template->clear()->set('id', $template_id)->delete();
						}
						wp_redirect(add_query_arg('pmxi_nt', urlencode(sprintf(_n('%d template deleted', '%d templates deleted', count($templates_ids), 'wp_all_import_plugin'), count($templates_ids))), $this->baseUrl)); die();
					}
					if ($this->input->post('export_templates')){
						$export_data = array();
						$template = new PMXI_Template_Record();
						foreach ($templates_ids as $template_id) {
							$export_data[] = $template->clear()->getBy('id', $template_id)->toArray(TRUE);
						}	
						
						$uploads = wp_upload_dir();
						$targetDir = $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::TEMP_DIRECTORY;
						$export_file_name = "templates_".uniqid().".txt";
						file_put_contents($targetDir . DIRECTORY_SEPARATOR . $export_file_name, json_encode($export_data));
						
						PMXI_download::csv($targetDir . DIRECTORY_SEPARATOR . $export_file_name);
						
					}				
				}
			}
		}
		
		$this->render();
	}

	/*
	*
	* Activate licenses for main plugin and all premium addons
	*
	*/
	protected function activate_licenses() {

		// listen for our activate button to be clicked
		if( isset( $_POST['pmxi_license_activate'] ) ) {			

			// retrieve the license from the database
			$options = PMXI_Plugin::getInstance()->getOption();
			
			foreach ($_POST['pmxi_license_activate'] as $class => $val) {							

				if (!empty($options['licenses'][$class])){

					$product_name = (method_exists($class, 'getEddName')) ? call_user_func(array($class, 'getEddName')) : false;

					if ( $product_name !== false ){
						// data to send in our API request
						$api_params = array( 
							'edd_action'=> 'activate_license', 
							'license' 	=> $options['licenses'][$class], 
							'item_name' => urlencode( $product_name ) // the name of our product in EDD
						);								
						
						// Call the custom API.
						$response = wp_remote_get( add_query_arg( $api_params, $options['info_api_url'] ), array( 'timeout' => 15, 'sslverify' => false ) );						

						// make sure the response came back okay
						if ( is_wp_error( $response ) )
							continue;

						// decode the license data
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						
						// $license_data->license will be either "active" or "inactive"

						$options['statuses'][$class] = $license_data->license;
						
						PMXI_Plugin::getInstance()->updateOption($options);	
					}
				}

			}				

		}
	}	

	/*
	*
	* Check plugin's license
	*
	*/
	public static function check_license($class) {

		global $wp_version;

		$options = PMXI_Plugin::getInstance()->getOption();	

		if (!empty($options['licenses'][$class])){

			$product_name = (method_exists($class, 'getEddName')) ? call_user_func(array($class, 'getEddName')) : false;

			if ( $product_name !== false ){

				$api_params = array( 
					'edd_action' => 'check_license', 
					'license' => $options['licenses'][$class], 
					'item_name' => urlencode( $product_name ) 
				);

				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, $options['info_api_url'] ), array( 'timeout' => 15, 'sslverify' => false ) );

				if ( is_wp_error( $response ) )
					return false;

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				return $license_data->license;
				
			}
		}

		return false;

	}
	
	public function cleanup(){

		$removedFiles = 0;

		$wp_uploads = wp_upload_dir();

		$dir = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::TEMP_DIRECTORY;

		$cacheDir = PMXI_Plugin::ROOT_DIR . '/libraries/cache';

		$files = array_diff(@scandir($dir), array('.','..'));

		$cacheFiles = @array_diff(@scandir($cacheDir), array('.','..'));

		$msg = __('Files not found', 'wp_all_import_plugin');

		if ( count($files) or count($cacheFiles)){

			wp_all_import_clear_directory( $dir );

			wp_all_import_clear_directory( $cacheDir );		

			$msg = __('Clean Up has been successfully completed.', 'wp_all_import_plugin');
		}

		// clean logs files
		$table = PMXI_Plugin::getInstance()->getTablePrefix() . 'history';
		global $wpdb;
		$histories = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);

		if ( ! empty($histories) )
		{
			$importRecord = new PMXI_Import_Record();
			$importRecord->clear();
			foreach ($histories as $history) {
				$importRecord->getById($history['import_id']);
				if ( $importRecord->isEmpty() )
				{
					$historyRecord = new PMXI_History_Record();
					$historyRecord->getById($history['id']);
					if ( ! $historyRecord->isEmpty() ) {
						$historyRecord->delete();
					}
				}
				$importRecord->clear();
			}
		}

		// clean uploads folder
		$table = PMXI_Plugin::getInstance()->getTablePrefix() . 'files';		
		$files = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);

		$required_dirs = array();

		if ( ! empty($files) )
		{
			$importRecord = new PMXI_Import_Record();
			$importRecord->clear();
			foreach ($files as $file) {
				$importRecord->getById($file['import_id']);				
				if ( $importRecord->isEmpty()){
					$fileRecord = new PMXI_File_Record();
					$fileRecord->getById($file['id']);					
					if ( ! $fileRecord->isEmpty() ) {						
						$fileRecord->delete();
					}
				}
				else
				{
					$path_parts = pathinfo(wp_all_import_get_absolute_path($file['path']));
					if ( ! empty($path_parts['dirname'])){
			            $path_all_parts = explode('/', $path_parts['dirname']);
			            $dirname = array_pop($path_all_parts);
			            if ( wp_all_import_isValidMd5($dirname)){    
			            	$required_dirs[] = $path_parts['dirname'];
			            }
			        }					
				}
				$importRecord->clear();
			}			
		}

		$uploads_dir = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY;

		if (($dir = @opendir($uploads_dir . DIRECTORY_SEPARATOR)) !== false or ($dir = @opendir($uploads_dir)) !== false) {				
			while(($file = @readdir($dir)) !== false) {
				$filePath = $uploads_dir . DIRECTORY_SEPARATOR . $file;									
				
				if ( is_dir($filePath) and ! in_array($filePath, $required_dirs) and ( ! in_array($file, array('.', '..'))))
				{
					wp_all_import_rmdir($filePath);								
				}						
			}
		}

		wp_redirect(add_query_arg('pmxi_nt', urlencode($msg), $this->baseUrl)); die();
	}

	public function dismiss(){

		PMXI_Plugin::getInstance()->updateOption("dismiss", 1);

		exit('OK');
	}

	public function dismiss_speed_up(){

		PMXI_Plugin::getInstance()->updateOption("dismiss_speed_up", 1);

		exit('OK');
	}

	public function dismiss_manage_top(){

		PMXI_Plugin::getInstance()->updateOption("dismiss_manage_top", 1);

		exit( json_encode(array('result' => 'OK')) );
	}

	public function dismiss_manage_bottom(){

		PMXI_Plugin::getInstance()->updateOption("dismiss_manage_bottom", 1);

		exit( json_encode(array('result' => 'OK')) );
	}
	
	public function meta_values(){

		if ( ! PMXI_Plugin::getInstance()->getAdminCurrentScreen()->is_ajax) { // call is only valid when send with ajax
			exit('nice try!');
		}				

		if ( ! check_ajax_referer( 'wp_all_import_secure', 'security', false ) ){
			exit( json_encode(array('meta_values' => array())) );
		}

		global $wpdb;

		$meta_key = $_POST['key'];

		$r = $wpdb->get_results("
			SELECT DISTINCT postmeta.meta_value
			FROM ".$wpdb->postmeta." as postmeta
			WHERE postmeta.meta_key='".$meta_key."' LIMIT 0,10
		", ARRAY_A);		

		$meta_values = array();
		
		if ( ! empty($r) ){
			foreach ($r as $key => $value) { if (empty($value['meta_value'])) continue;
				$meta_values[] = esc_html($value['meta_value']);
			}
		}

		exit( json_encode(array('meta_values' => $meta_values)) );
	}

	/**
	 * upload.php
	 *
	 * Copyright 2009, Moxiecode Systems AB
	 * Released under GPL License.
	 *
	 * License: http://www.plupload.com/license
	 * Contributing: http://www.plupload.com/contributing
	 */
	public function upload(){			

		if ( ! check_ajax_referer( 'wp_all_import_secure', '_wpnonce', false )){
			exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 100, "message" => __('Security check', 'wp_all_import_plugin')), "id" => "id")));
		}
		
		// HTTP headers for no cache etc
		// header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		// header("Cache-Control: no-store, no-cache, must-revalidate");
		// header("Cache-Control: post-check=0, pre-check=0", false);
		// header("Pragma: no-cache");

		// Settings
		//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		//$uploads = wp_upload_dir();	

		$targetDir = self::$path;

		if (! is_dir($targetDir) || ! is_writable($targetDir)){
			delete_transient( self::$upload_transient );
			exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 100, "message" => __("Uploads folder is not writable.", "wp_all_import_plugin")), "id" => "id")));
		}

		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);		

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

		if ( ! preg_match('%\W(xml|gzip|zip|csv|gz|json|txt|dat|psv|sql|xls|xlsx)$%i', trim(basename($fileName)))) {	
			exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 100, "message" => __("Uploaded file must be XML, CSV, ZIP, GZIP, GZ, JSON, SQL, TXT, DAT or PSV", "wp_all_import_plugin")), "id" => "id")));
		}

		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);

		// Remove old temp files	
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}

			closedir($dir);
		} else{
			delete_transient( self::$upload_transient );
			exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 100, "message" => __("Failed to open temp directory.", "wp_all_import_plugin")), "id" => "id")));
		}
			

		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];

		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['async-upload']['tmp_name']) && is_uploaded_file($_FILES['async-upload']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['async-upload']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else{
						delete_transient( self::$upload_transient );
						exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 101, "message" => __("Failed to open input stream.", "wp_all_import_plugin")), "id" => "id")));
					}
					fclose($in);
					fclose($out);
					@unlink($_FILES['async-upload']['tmp_name']);
				} else{
					delete_transient( self::$upload_transient );					
					exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 102, "message" => __("Failed to open output stream.", "wp_all_import_plugin")), "id" => "id")));
				}
			} else{
				delete_transient( self::$upload_transient );
				exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 103, "message" => __("Failed to move uploaded file.", "wp_all_import_plugin")), "id" => "id")));
			}
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else{
					delete_transient( self::$upload_transient );
					exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 101, "message" => __("Failed to open input stream.", "wp_all_import_plugin")), "id" => "id")));
				}

				fclose($in);
				fclose($out);
			} else{
				delete_transient( self::$upload_transient );
				exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 102, "message" => __("Failed to open output stream.", "wp_all_import_plugin")), "id" => "id")));
			}
		}
		
		$post_type = false;		

		$notice = false;

		$warning = false;

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			$res = rename("{$filePath}.part", $filePath);
			if (!$res){
				@copy("{$filePath}.part", $filePath);
				@unlink("{$filePath}.part");
			}
			chmod($filePath, 0755);
			delete_transient( self::$upload_transient );

			$errors = new WP_Error;

			$uploader = new PMXI_Upload($filePath, $errors, rtrim(str_replace(basename($filePath), '', $filePath), '/'));			
			
			$upload_result = $uploader->upload();			
			
			if ($upload_result instanceof WP_Error){
				$errors = $upload_result;

				$msgs = $errors->get_error_messages();
				ob_start();
				?>
				<?php foreach ($msgs as $msg): ?>
					<p><?php echo $msg ?></p>
				<?php endforeach ?>
				<?php
				$response = ob_get_clean();

				exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 102, "message" => $response), "id" => "id")));
			}
			else 
			{				
				if ( ! empty($upload_result['post_type'])) 
				{
					$post_type = $upload_result['post_type'];

					if ( ! empty($upload_result['template']) )
					{																	

						$template = json_decode($upload_result['template'], true);

						if ( ! empty($template[0]['options']))
						{
							$is_show_cf_notice = ( ! empty($template[0]['options']['custom_name'])) ? true : false;

							$is_show_images_notice = false;

							if ( $post_type != 'product' && (
								$template[0]['options']['download_featured_image'] != '' || 
								$template[0]['options']['gallery_featured_image'] != '' || 
								$template[0]['options']['featured_image'] != ''))
							{
								$is_show_images_notice = true;
							}

							if ( $is_show_cf_notice && $is_show_images_notice ){
								$warning = __('<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0&utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=custom-fields">Upgrade to the Pro edition of WP All Import to Import Images and Custom Fields</a> <p>If you already own it, remove the free edition and install the Pro edition.</p>', 'wp_all_import_plugin');
							}
							else if ( $is_show_cf_notice ){
								$warning = __('<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0&utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=custom-fields">Upgrade to the Pro edition of WP All Import to Import Custom Fields</a> <p>If you already own it, remove the free edition and install the Pro edition.</p>', 'wp_all_import_plugin');
							}
							else if ( $is_show_images_notice ) {		
								$warning = __('<a class="upgrade_link" target="_blank" href="https://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1748&edd_options%5Bprice_id%5D=0&utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=custom-fields">Upgrade to the Pro edition of WP All Import to Import Images</a> <p>If you already own it, remove the free edition and install the Pro edition.</p>', 'wp_all_import_plugin');
							}
						}						
					}					

					switch ( $post_type ) {
						
						case 'shop_order':
							
							if ( ! class_exists('WooCommerce') ) {
								$notice = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires WooCommerce.</p><a class="upgrade_link" href="https://wordpress.org/plugins/woocommerce/" target="_blank">Get WooCommerce</a>', 'wp_all_import_plugin');							
							}
							else {

								if ( ! defined('PMWI_EDITION') ) {

									$notice = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the Pro version of the WooCommerce Add-On.</p><a href="https://www.wpallimport.com/checkout/?edd_action=purchase_collection&taxonomy=download_category&terms=14&utm_source=free-plugin&utm_medium=dot-org&utm_campaign=woocommerce" class="upgrade_link" target="_blank">Purchase the WooCommerce Add-On</a>', 'wp_all_import_plugin');

								}
								elseif ( PMWI_EDITION != 'paid' ) {

									$notice = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the Pro version of the WooCommerce Add-On, but you have the free version installed.</p><a href="https://www.wpallimport.com/checkout/?edd_action=purchase_collection&taxonomy=download_category&terms=14&utm_source=free-plugin&utm_medium=dot-org&utm_campaign=woocommerce" target="_blank" class="upgrade_link">Purchase the WooCommerce Add-On</a>', 'wp_all_import_plugin');

								}							
							}

							break;

						case 'import_users':

							if ( ! class_exists('PMUI_Plugin') ) {
								$notice = __('<p class="wpallimport-bundle-notice">The import bundle you are using requires the User Import Add-On.</p><a href="http://www.wpallimport.com/checkout/?edd_action=add_to_cart&download_id=1921&edd_options%5Bprice_id%5D=1" target="_blank" class="upgrade_link">Purchase the User Import Add-On</a>', 'wp_all_import_plugin');
							}

							break;
						
						default:
							# code...
							break;
					}					
				}				

				if ( ! empty($upload_result['is_empty_bundle_file']))
				{										
					// Return JSON-RPC response
					exit(json_encode(array("jsonrpc" => "2.0", "error" => null, "result" => null, "id" => "id", "name" => $upload_result['filePath'], "post_type" => $post_type, "notice" => $notice, "template" => $upload_result['template'], "url_bundle" => true)));
				}
				else
				{
					// $root_element = wp_all_import_get_reader_engine( array($upload_result['filePath']), array('root_element' => $upload_result['root_element']) );	

					// if ( ! empty($root_element) and empty($upload_result['root_element']))
					// {
					// 	$upload_result['root_element'] = $root_element;
					// }
					
					// validate XML
					$file = new PMXI_Chunk($upload_result['filePath'], array('element' => $upload_result['root_element']));										    					    					   												

					$is_valid = true;

					if ( ! empty($file->options['element']) ) 						
						$defaultXpath = "/". $file->options['element'];																			    		  
					else
						$is_valid = false;

					if ( $is_valid ){

						while ($xml = $file->read()) {

					    	if ( ! empty($xml) ) { 

					      		//PMXI_Import_Record::preprocessXml($xml);
					      		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . "\n" . $xml;
					    	
						      	$dom = new DOMDocument( '1.0', 'UTF-8' );
								$old = libxml_use_internal_errors(true);
								$dom->loadXML($xml);
								libxml_use_internal_errors($old);
								$xpath = new DOMXPath($dom);									
								if (($elements = $xpath->query($defaultXpath)) and $elements->length){
									break;
								}												
						    }
						    /*else {
						    	$is_valid = false;
						    	break;
						    }*/

						}

						if ( empty($xml) ) $is_valid = false;
					}

					unset($file);

					if ( ! preg_match('%\W(xml)$%i', trim($upload_result['source']['path']))) @unlink($upload_result['filePath']);
					
					if ( ! $is_valid )
					{
						ob_start();					
						
						?>
						
						<div class="error inline"><p><?php _e('Please confirm you are importing a valid feed.<br/> Often, feed providers distribute feeds with invalid data, improperly wrapped HTML, line breaks where they should not be, faulty character encodings, syntax errors in the XML, and other issues.<br/><br/>WP All Import has checks in place to automatically fix some of the most common problems, but we can’t catch every single one.<br/><br/>It is also possible that there is a bug in WP All Import, and the problem is not with the feed.<br/><br/>If you need assistance, please contact support – <a href="mailto:support@wpallimport.com">support@wpallimport.com</a> – with your XML/CSV file. We will identify the problem and release a bug fix if necessary.', 'wp_all_import_plugin'); ?></p></div>
						
						<?php

						$response = ob_get_clean();

						$file_type = strtoupper(pmxi_getExtension($upload_result['source']['path']));

						$error_message = sprintf(__("Please verify that the file you uploading is a valid %s file.", "wp_all_import_plugin"), $file_type);

						exit(json_encode(array("jsonrpc" => "2.0", "error" => array("code" => 102, "message" => $error_message), "is_valid" => false, "id" => "id")));
					
					}
					else
					{
						$wp_uploads = wp_upload_dir();
						$uploads    = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::FILES_DIRECTORY . DIRECTORY_SEPARATOR;

						if ( ! file_exists($uploads . basename($filePath))) @copy($filePath, $uploads . basename($filePath));
					}
				}				
								
			}		

		}			

		// Return JSON-RPC response
		exit(json_encode(array("jsonrpc" => "2.0", "error" => null, "result" => null, "id" => "id", "name" => $filePath, "post_type" => $post_type, "notice" => $notice, "warning" => $warning)));

	}		

}