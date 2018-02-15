<?php 
/**
 * Admin Statistics page
 * 
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXE_Admin_Settings extends PMXE_Controller_Admin {
	
	public function index() {
		
		$this->data['post'] = $post = $this->input->post(PMXE_Plugin::getInstance()->getOption());

		if ($this->input->post('is_settings_submitted')) { // save settings form	

			check_admin_referer('edit-settings', '_wpnonce_edit-settings');		
			
			if ( ! $this->errors->get_error_codes()) { // no validation errors detected

				PMXE_Plugin::getInstance()->updateOption($post);

				if (empty($_POST['pmxe_license_activate']) and empty($_POST['pmxe_license_deactivate'])) {
					$post['license_status'] = $this->check_license();
					PMXE_Plugin::getInstance()->updateOption($post);
				}				

				isset( $_POST['pmxe_license_activate'] ) and $this->activate_licenses();
				
				wp_redirect(add_query_arg('pmxe_nt', urlencode(__('Settings saved', 'wp_all_export_plugin')), $this->baseUrl)); die();
			}
		}

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
						$extension = strtolower(pmxe_getExtension($filename));
										
						if (($extension != "txt")) 
						{							
							$this->errors->add('form-validation', __('Unknown File extension. Only txt files are permitted', 'wp_all_export_plugin'));
						}
						else {
							$import_data = @file_get_contents($tmp_name);
							if (!empty($import_data)){
								$templates_data = json_decode($import_data, true);
								
								if (!empty($templates_data)){
                                    $templateOptions = empty($templates_data[0]['options']) ? false : unserialize($templates_data[0]['options']);
                                    if ( empty($templateOptions) ){
                                        $this->errors->add('form-validation', __('The template is invalid. Options are missing.', 'wp_all_export_plugin'));
                                    }
                                    else{
                                        if (!isset($templateOptions['is_user_export'])){
                                            $this->errors->add('form-validation', __('The template you\'ve uploaded is intended to be used with WP All Import plugin.', 'wp_all_export_plugin'));
                                        }
                                        else{
                                            $template = new PMXE_Template_Record();
                                            foreach ($templates_data as $template_data) {
                                                unset($template_data['id']);
                                                $template->clear()->set($template_data)->insert();
                                            }
                                            wp_redirect(add_query_arg('pmxe_nt', urlencode(sprintf(_n('%d template imported', '%d templates imported', count($templates_data), 'wp_all_export_plugin'), count($templates_data))), $this->baseUrl)); die();
                                        }
                                    }
								}
								else $this->errors->add('form-validation', __('Wrong imported data format', 'wp_all_export_plugin'));							
							}
							else $this->errors->add('form-validation', __('File is empty or doesn\'t exests', 'wp_all_export_plugin'));
						}
					}
					else $this->errors->add('form-validation', __('Undefined entry!', 'wp_all_export_plugin'));
				}
				else $this->errors->add('form-validation', __('Please select file.', 'wp_all_export_plugin'));

			}
			else{
				$templates_ids = $this->input->post('templates', array());
				if (empty($templates_ids)) {
					$this->errors->add('form-validation', __('Templates must be selected', 'wp_all_export_plugin'));
				}
				
				if ( ! $this->errors->get_error_codes()) { // no validation errors detected
					if ($this->input->post('delete_templates')){
						$template = new PMXE_Template_Record();
						foreach ($templates_ids as $template_id) {
							$template->clear()->set('id', $template_id)->delete();
						}
						wp_redirect(add_query_arg('pmxe_nt', urlencode(sprintf(_n('%d template deleted', '%d templates deleted', count($templates_ids), 'wp_all_export_plugin'), count($templates_ids))), $this->baseUrl)); die();
					}
					if ($this->input->post('export_templates')){
						$export_data = array();
						$template = new PMXE_Template_Record();
						foreach ($templates_ids as $template_id) {
							$export_data[] = $template->clear()->getBy('id', $template_id)->toArray(TRUE);
						}	
						
						$uploads = wp_upload_dir();
						$targetDir = $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::TEMP_DIRECTORY;
						$export_file_name = "templates_".uniqid().".txt";
						file_put_contents($targetDir . DIRECTORY_SEPARATOR . $export_file_name, json_encode($export_data));
						
						PMXE_download::csv($targetDir . DIRECTORY_SEPARATOR . $export_file_name);
						
					}				
				}
			}
		}

		$this->render();

	}
	
	public function dismiss(){

		PMXE_Plugin::getInstance()->updateOption("dismiss", 1);

		exit('OK');
	}	

	/*
	*
	* Activate licenses for main plugin and all premium addons
	*
	*/
	protected function activate_licenses() {

		// listen for our activate button to be clicked
		if( isset( $_POST['pmxe_license_activate'] ) ) {			

			// retrieve the license from the database
			$options = PMXE_Plugin::getInstance()->getOption();
			
			$product_name = PMXE_Plugin::getEddName();

			if ( $product_name !== false ){
				// data to send in our API request
				$api_params = array( 
					'edd_action'=> 'activate_license', 
					'license' 	=> $options['license'], 
					'item_name' => urlencode( $product_name ) // the name of our product in EDD
				);								
				
				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, $options['info_api_url'] ), array( 'timeout' => 15, 'sslverify' => false ) );						

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				
				// $license_data->license will be either "active" or "inactive"

				$options['license_status'] = $license_data->license;
				
				PMXE_Plugin::getInstance()->updateOption($options);	
			}			

		}
	}	

	/*
	*
	* Check plugin's license
	*
	*/
	public static function check_license() {

		global $wp_version;

		$options = PMXE_Plugin::getInstance()->getOption();	

		if (!empty($options['license'])){

			$product_name = PMXE_Plugin::getEddName();

			if ( $product_name !== false ){

				$api_params = array( 
					'edd_action' => 'check_license', 
					'license' => $options['license'], 
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

}