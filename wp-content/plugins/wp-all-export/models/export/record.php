<?php

class PMXE_Export_Record extends PMXE_Model_Record {
		
	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);		
		$this->setTable(PMXE_Plugin::getInstance()->getTablePrefix() . 'exports');		
	}					

    public function set_html_content_type(){
        return 'text/html';
    }

    public function generate_bundle( $debug = false)
	{
		// do not generate export bundle if not supported
		if ( ! self::is_bundle_supported($this->options) ) return;
		
		$uploads  = wp_upload_dir();

		//generate temporary folder
		$export_dir = wp_all_export_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::UPLOADS_DIRECTORY, $this->id ) . DIRECTORY_SEPARATOR;
		$bundle_dir = $export_dir . 'bundle' . DIRECTORY_SEPARATOR;
		
		// clear tmp dir
		wp_all_export_rrmdir($bundle_dir);

		@mkdir($bundle_dir);		
						
		$friendly_name = sanitize_file_name($this->friendly_name);		

		$template  = "WP All Import Template - " . $friendly_name . ".txt";

		$templates = array();	

		$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

		if ( ! $is_secure_import)
		{
			$filepath = get_attached_file($this->attch_id);
		}
		else
		{
			$filepath = wp_all_export_get_absolute_path($this->options['filepath']);
		}				
		
		@copy( $filepath, $bundle_dir . basename($filepath) );

		if ( ! empty($this->options['tpl_data']))
		{
			$template_data = array($this->options['tpl_data']);						

			$template_data[0]['source_file_name'] = basename($filepath);

			$template_options = maybe_unserialize($template_data[0]['options']);

			$templates[$template_options['custom_type']] = $template_data;			

			$readme = __("The other two files in this zip are the export file containing all of your data and the import template for WP All Import. \n\nTo import this data, create a new import with WP All Import and upload this zip file.", "wp_all_export_plugin");	

			file_put_contents($bundle_dir . 'readme.txt', $readme);
		}			

		// [ Add child exports to the bundle]
		$exportList = new PMXE_Export_List();				

		foreach ($exportList->getBy('parent_id', $this->id)->convertRecords() as $child_export) 
		{
			$is_generate_child_template = true;

			switch ($child_export->export_post_type) 
			{
				case 'product':
					if ( ! $this->options['order_include_poducts'] ) $is_generate_child_template = false;
					break;
				case 'shop_coupon':
					if ( ! $this->options['order_include_coupons'] ) $is_generate_child_template = false;
					break;				
				case 'shop_customer':
					if ( ! $this->options['order_include_customers'] ) $is_generate_child_template = false;
					break;
			}

			if ( ! $is_generate_child_template ) continue; 			

			if ( ! $is_secure_import)
			{
				$filepath = get_attached_file($child_export->attch_id);
			}
			else
			{
				$filepath = wp_all_export_get_absolute_path($child_export->options['filepath']);
			}				

			if ( ! empty($child_export->options['tpl_data']))
			{
				$template_data = array($child_export->options['tpl_data']);				

				$template_data[0]['source_file_name'] = basename($filepath);

				$template_key = ($child_export->export_post_type == 'shop_customer') ? 'import_users' : $child_export->export_post_type;

				$templates[$template_key] = $template_data;							
			}
			
			@copy( $filepath, $bundle_dir . basename($filepath) );
		}	
		// \[ Add child exports to the bundle]	

		file_put_contents($bundle_dir . $template, json_encode($templates));							

		// if ($this->options['creata_a_new_export_file'] && ! empty($this->options['cpt']) and class_exists('WooCommerce') and in_array('shop_order', $this->options['cpt']) and empty($this->parent_id) )
		// {
		// 	$bundle_path = $export_dir . $friendly_name . '-' . ($this->iteration + 1) . '.zip';			
		// }
		// else
		// {
		// 	$bundle_path = $export_dir . $friendly_name . '.zip';			
		// }		

		$bundle_path = $export_dir . $friendly_name . '.zip';			

		if ( @file_exists($bundle_path))
		{
			@unlink($bundle_path);
		}

		PMXE_Zip::zipDir($bundle_dir, $bundle_path);

		// clear tmp dir
		wp_all_export_rrmdir($bundle_dir);

		$exportOptions = $this->options;
		$exportOptions['bundlepath'] = wp_all_export_get_relative_path($bundle_path);
		$this->set(array(
			'options' => $exportOptions
		))->save();

		return $bundle_path;					
	}

	public function fix_template_options()
	{
		// migrate media options since @version 1.2.4		
		if ( empty($this->options['migration']) )
		{
			$options = $this->options;

			$options['migration'] = PMXE_VERSION;

			$is_migrate_media = false;

			foreach ($options['ids'] as $ID => $value) 
			{
				if ( in_array($options['cc_type'][$ID], array('media', 'attachments')))
				{
					$is_migrate_media = true;
					break;
				}
			}

			if ( ! $is_migrate_media ) 
			{
				$this->set(array('options' => $options))->save();
				
				return $this;
			}

			$fields = array();

			foreach ($options['ids'] as $ID => $value) 
			{
				$field = array(
					'cc_label' => empty($options['cc_label'][$ID]) ? '' : $options['cc_label'][$ID],
					'cc_php' => empty($options['cc_php'][$ID]) ? '' : $options['cc_php'][$ID],
					'cc_code' => empty($options['cc_code'][$ID]) ? '' : $options['cc_code'][$ID],
					'cc_sql' => empty($options['cc_sql'][$ID]) ? '' : $options['cc_sql'][$ID],
					'cc_type' => empty($options['cc_type'][$ID]) ? '' : $options['cc_type'][$ID],
					'cc_options' => empty($options['cc_options'][$ID]) ? '' : $options['cc_options'][$ID],
					'cc_value' => empty($options['cc_value'][$ID]) ? '' : $options['cc_value'][$ID],
					'cc_name' => empty($options['cc_name'][$ID]) ? '' : $options['cc_name'][$ID],
					'cc_settings' => empty($options['cc_settings'][$ID]) ? '' : $options['cc_settings'][$ID],
				);

				switch ($field['cc_type']) 
				{
					case 'media':

						switch ($field['cc_options']) 
						{
							case 'urls':
								$field['cc_label'] = 'url';
								$field['cc_value'] = 'url';
								$field['cc_type']  = 'image_url';
								break;	
							case 'filenames':
								$field['cc_label'] = 'filename';
								$field['cc_value'] = 'filename';
								$field['cc_type']  = 'image_filename';
								break;
							case 'filepaths':
								$field['cc_label'] = 'path';
								$field['cc_value'] = 'path';
								$field['cc_type']  = 'image_path';
								break;
							default:
								$field['cc_label'] = 'url';
								$field['cc_value'] = 'url';
								$field['cc_type']  = 'image_url';
								break;
						}

						$field_name = $field['cc_name'];
						$field['cc_name']    .= '_images';
						$field['cc_options'] = '{"is_export_featured":true,"is_export_attached":true,"image_separator":"|"}';											

						$fields[] = $field;

						$new_fields = array('title', 'caption', 'description', 'alt');

						foreach ($new_fields as $value) 
						{
							$new_field = array(
								'cc_label' => $value,
								'cc_php' => empty($options['cc_php'][$ID]) ? '' : $options['cc_php'][$ID],
								'cc_code' => empty($options['cc_code'][$ID]) ? '' : $options['cc_code'][$ID],
								'cc_sql' => empty($options['cc_sql'][$ID]) ? '' : $options['cc_sql'][$ID],
								'cc_type' => 'image_' . $value,
								'cc_options' => '{"is_export_featured":true,"is_export_attached":true,"image_separator":"|"}',
								'cc_value' => $value,
								'cc_name' => $field_name . '_' . $value,
								'cc_settings' => ''
							);

							$fields[] = $new_field;
						}

						break;

					case 'attachments':					
						$field['cc_type']    = 'attachment_url';
						$field['cc_options'] = '';						
						$fields[] = $field;
						break;

					default:
						$fields[] = $field;
						break;
				}				
			}	

			// reset fields settings
			$options['ids'] = array();
			$options['cc_label'] = array();
			$options['cc_php'] = array();
			$options['cc_code'] = array();
			$options['cc_sql'] = array();
			$options['cc_type'] = array();
			$options['cc_options'] = array();
			$options['cc_value'] = array();
			$options['cc_name'] = array();
			$options['cc_settings'] = array();

			// apply new field settings
			foreach ($fields as $ID => $field) {
				$options['ids'][] = 1;
				$options['cc_label'][] = $field['cc_label'];
				$options['cc_php'][] = $field['cc_php'];
				$options['cc_code'][] = $field['cc_code'];
				$options['cc_sql'][] = $field['cc_sql'];
				$options['cc_type'][] = $field['cc_type'];
				$options['cc_options'][] = $field['cc_options'];
				$options['cc_value'][] = $field['cc_value'];
				$options['cc_name'][] = $field['cc_name'];
				$options['cc_settings'][] = $field['cc_settings'];
			}

			$this->set(array('options' => $options))->save();
		}		

		return $this;
	}

    public static function is_bundle_supported( $options )
    {
        $options += PMXE_Plugin::get_default_import_options();

    	// custom XML template do not support import bundle
    	if ( $options['export_to'] == 'xml' && ! empty($options['xml_template_type']) && in_array($options['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ) return false;

        // Export only parent product do not support import bundle
        if ( ! empty($options['cpt']) and in_array($options['cpt'][0], array('product', 'product_variation')) and class_exists('WooCommerce') and $options['export_variations'] != XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION){
            return false;
        }

    	$unsupported_post_types = array('comments');
    	return ( empty($options['cpt']) and ! in_array($options['wp_query_selector'], array('wp_comment_query')) or ! empty($options['cpt']) and ! in_array($options['cpt'][0], $unsupported_post_types) ) ? true : false;
    }

    /**
	 * Clear associations with posts	 
	 * @return PMXE_Import_Record
	 * @chainable
	 */
	public function deletePosts() {
		$post = new PMXE_Post_List();					
		$this->wpdb->query($this->wpdb->prepare('DELETE FROM ' . $post->getTable() . ' WHERE export_id = %s', $this->id));
		return $this;
	}

	/**
	 * Delete associated sub exports
	 * @return PMXE_Export_Record
	 * @chainable
	 */
	public function deleteChildren(){
		$exportList = new PMXE_Export_List();
		foreach ($exportList->getBy('parent_id', $this->id)->convertRecords() as $i) {
			$i->delete();
		}
		return $this;
	}

	/**
	 * @see parent::delete()	 
	 */
	public function delete() {	
		$this->deletePosts()->deleteChildren();
		if ( ! empty($this->options['import_id']) and wp_all_export_is_compatible()){
			$import = new PMXI_Import_Record();
			$import->getById($this->options['import_id']);
			if ( ! $import->isEmpty() and $import->parent_import_id == 99999 ){
				$import->delete();
			}
		}	
		$export_file_path = wp_all_export_get_absolute_path($this->options['filepath']);
		if ( @file_exists($export_file_path) ){ 
			wp_all_export_remove_source($export_file_path);
		}
		if ( ! empty($this->attch_id) ){
			wp_delete_attachment($this->attch_id, true);
		}
		
		$wp_uploads = wp_upload_dir();	

		$file_for_remote_access = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::UPLOADS_DIRECTORY . DIRECTORY_SEPARATOR . md5(PMXE_Plugin::getInstance()->getOption('cron_job_key') . $this->id) . '.' . $this->options['export_to'];
		
		if ( @file_exists($file_for_remote_access)) @unlink($file_for_remote_access);

		return parent::delete();
	}
	
}
