<?php
/**
 * 
 */
class BngMigration
{
	private $prefix;
	

	public function __construct($prefix)
	{
		$this->prefix = $prefix;
		
		/**
		 * HOOKS FOR MANAGE OPTIONS
		 */
		add_action( 'admin_menu', array($this, 'bng_settings_menu') );
		add_action( 'wp_ajax_bng_upload_file', array($this, 'bng_upload_file') );
		add_action( 'wp_ajax_bng_remove_file', array($this, 'bng_remove_file') );
		add_action( 'wp_ajax_bng_process_files', array($this, 'bng_process_files') );

		/**
		 * Button Upload File
		 */
		add_action( 'admin_enqueue_scripts', array($this, 'bng_register_main_script') );
		

		
		
	}


 	
 	/**
 	-------------- MANAGE OPTIONS --------------------------------
 	 */
 	
	public function bng_settings_menu()
	{
	    add_submenu_page(
			'edit.php?post_type=bng_type_location', 
			__('Import Locations', 'bitsandgeeks'), 
			__('Import Locations', 'bitsandgeeks'), 
			'manage_options', 
			'bng_import_locations', 
			array($this, 'bng_settings_page')
		);
	}

	public function bng_settings_page()
	{
	    
	    include_once dirname(dirname(__FILE__)) . '/views/upload_form.php';
	}


	public function bng_upload_file()
	{
		$uploaddir = wp_upload_dir();

		$uploadfile = $uploaddir['basedir'] . DIRECTORY_SEPARATOR .basename($_FILES['file']['name']);
		
		
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
		    $message = $uploadfile;
		    $success = true;
		} else {
		    $message = "";
		    $success = false;
		}

		echo json_encode(array('success'=>$success, 'message'=>$message));
		exit;
		
	}


	public function bng_remove_file()
	{
		$file = $_POST['file'];
		
		if(file_exists($file)){
			@unlink($file);
		}

		echo 'deleted file : '.$file;
		exit;
	}


	public function bng_process_files()
	{
		$files = $_POST['files'];
		$files = explode(',', $files);

		$return = array('success'=>true, 'message'=>'All complete!');

		if( $files != array() ){
			
			ini_set('max_execution_time', 10800);
			ini_set('memory_limit', '1024M');

			require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
						
			try{

				
				foreach ($files as &$file) {
										
					$excelReader = PHPExcel_IOFactory::createReaderForFile($file);
					
					//$excelReader->setReadFilter($bng_filter);
					
					$excelReader->setReadDataOnly();
					$worksheetList = $excelReader->listWorksheetNames($file);
					$sheetname = $worksheetList[0]; 

					$excelReader->setLoadSheetsOnly($sheetname);

					$excelObj = $excelReader->load($file);
					
					$tmp_data = $excelObj->getActiveSheet()->toArray(null, true,true,true);

					$titles = array_shift($tmp_data); // Delete First Row

					
					/*
					$col_start_group_fields = 'V';
					
					foreach ($titles as $col_name => $value) {
						if($col_start_group_fields == $col_name){
							$col_start_group_fields++;
						}else{
							unset($titles[$col_name]);
						}
					}
					*/

					foreach ($tmp_data as $row => $col) {
						if($col['A'] == null || trim($col['A']) == null)
							continue;

						// check if exists
						$old_locations = get_posts(array(
							'meta_key'         => 'location_code',
							'meta_value'       => sanitize_text_field($col['A']),
							'posts_per_page'   => -1,
							'post_type' 	   => 'bng_type_location',
							'post_status' 	   => 'any'
						));

						if($old_locations){
							// exists
							$post_id = $old_locations[0]->ID;
							unset($old_locations);
						}else{
							// new
							$post_id = wp_insert_post( array(
								'post_type' 	   => 'bng_type_location',
								'post_status' 	   => 'publish',
								'comment_status' 	=> 'closed',
								'ping_status'		=> 'closed',
								'post_title'		=> sanitize_text_field($col['C'])
								) );

							if( is_wp_error($post_id) ){
								continue;
							}
						}

						update_post_meta( $post_id, 'location_code', sanitize_text_field($col['A']) );
						update_post_meta( $post_id, 'brand_assigned_code', sanitize_text_field($col['B']) );
						update_post_meta( $post_id, 'business_license_id', sanitize_text_field($col['D']) );
						update_post_meta( $post_id, 'formal_business_name', sanitize_text_field($col['E']) );
						update_post_meta( $post_id, 'business_dba', sanitize_text_field($col['F']) );
						update_post_meta( $post_id, 'address_1', sanitize_text_field($col['G']) );
						update_post_meta( $post_id, 'address_2', sanitize_text_field($col['H']) );
						update_post_meta( $post_id, 'city', sanitize_text_field($col['I']) );
						update_post_meta( $post_id, 'state', sanitize_text_field($col['J']) );
						update_post_meta( $post_id, 'zip', sanitize_text_field($col['K']) );
						update_post_meta( $post_id, 'primary_phone', sanitize_text_field($col['L']) );
						update_post_meta( $post_id, 'tracking_phone', sanitize_text_field($col['N']) );
						update_post_meta( $post_id, 'fax', sanitize_text_field($col['O']) );
						update_post_meta( $post_id, 'facebook_url', sanitize_text_field($col['P']) );
						update_post_meta( $post_id, 'google_url', sanitize_text_field($col['Q']) );
						update_post_meta( $post_id, 'youtube_url', sanitize_text_field($col['R']) );
						update_post_meta( $post_id, 'pinterest_url', sanitize_text_field($col['S']) );
						update_post_meta( $post_id, 'twitter_url', sanitize_text_field($col['T']) );
						update_post_meta( $post_id, 'instagram_url', sanitize_text_field($col['U']) );
						update_post_meta( $post_id, 'angieslist_url', sanitize_text_field($col['V']) );
						update_post_meta( $post_id, 'location_path', sanitize_text_field($col['W']) );
						update_post_meta( $post_id, 'button_1_url', sanitize_text_field($col['X']) );
						update_post_meta( $post_id, 'button_2_url', sanitize_text_field($col['Y']) );
						update_post_meta( $post_id, 'button_3_url', sanitize_text_field($col['Z']) );
						update_post_meta( $post_id, 'button_4_url', sanitize_text_field($col['AA']) );
						update_post_meta( $post_id, 'button_5_url', sanitize_text_field($col['AB']) );
						update_post_meta( $post_id, 'customvar1', sanitize_text_field($col['AC']) );
						update_post_meta( $post_id, 'customvar2', sanitize_text_field($col['AD']) );
						update_post_meta( $post_id, 'customvar3', sanitize_text_field($col['AE']) );
						update_post_meta( $post_id, 'customvar4', sanitize_text_field($col['AF']) );
						update_post_meta( $post_id, 'customvar5', sanitize_text_field($col['AG']) );
						update_post_meta( $post_id, 'customvar6', sanitize_text_field($col['AH']) );
						update_post_meta( $post_id, 'customvar7', sanitize_text_field($col['AI']) );
						update_post_meta( $post_id, 'customvar8', sanitize_text_field($col['AJ']) );
						update_post_meta( $post_id, 'customvar9', sanitize_text_field($col['AK']) );
						update_post_meta( $post_id, 'customvar10', sanitize_text_field($col['AL']) );

						
						/*
						$group_fields = array();
						foreach ($titles as $col_name => $value) {
							$group_fields[] = array(
								'bng_id_fields'=> sanitize_text_field($value), 
								'bng_value_fields'=> sanitize_text_field($col[$col_name])
							);
						}

						update_post_meta($post_id, 'group_fields', $group_fields);
						*/
						
					}	

					// Delete File
					@unlink($file);
				}
				
				unset($files);

			}catch(Exception $e){
				$return['success']	= false;
				$return['message']	= $e->getMessage();
			}
			
			
		}

		echo json_encode($return); exit;
	}


	
	public function bng_register_main_script()
	{
	    wp_enqueue_script( 'bng-plugin-loader', plugins_url('/assets/jquery-loading-overlay-master/src/loadingoverlay.min.js', __DIR__), array( 'jquery' ));
	    wp_enqueue_script( 'bng-noty-alert', plugins_url('/assets/noty/packaged/jquery.noty.packaged.js', __DIR__), array( 'jquery' ));
	    wp_enqueue_script( 'bng-js-dropzone', plugins_url('/assets/js/dropzone.js', __DIR__), array( 'jquery' ));
	    wp_enqueue_style( 'bng-test-connection', plugins_url('/assets/css/style.css', __DIR__) );
	    wp_enqueue_style( 'bng-css-dropzone', plugins_url('/assets/css/dropzone.css', __DIR__) );
	}


	

}