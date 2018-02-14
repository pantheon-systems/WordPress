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
	
	/**
	 * Import all files matched by path
	 * @param callable[optional] $logger Method where progress messages are submmitted
	 * @return PMXI_Import_Record
	 * @chainable
	 */
	public function execute($logger = NULL, $cron = false) {

		$this->fix_template_options();

		$wp_uploads = wp_upload_dir();	

		$this->set(array('processing' => 1))->update(); // lock cron requests			

		wp_reset_postdata();

		$functions = $wp_uploads['basedir'] . DIRECTORY_SEPARATOR . WP_ALL_EXPORT_UPLOADS_BASE_DIRECTORY . DIRECTORY_SEPARATOR . 'functions.php';

		if (@file_exists($functions)) {
			require_once $functions;
		}

		XmlExportEngine::$exportOptions  	 = $this->options;
		XmlExportEngine::$is_user_export 	 = $this->options['is_user_export'];
		XmlExportEngine::$is_comment_export  = $this->options['is_comment_export'];
        XmlExportEngine::$is_taxonomy_export = empty($this->options['is_taxonomy_export']) ? false : $this->options['is_taxonomy_export'];
		XmlExportEngine::$exportID 		 	 = $this->id;
		XmlExportEngine::$exportRecord 		 = $this;
        XmlExportEngine::$post_types         = $this->options['cpt'];

        if ( class_exists('SitePress') && ! empty(XmlExportEngine::$exportOptions['wpml_lang'])){
          do_action( 'wpml_switch_language', XmlExportEngine::$exportOptions['wpml_lang'] );
        }

        if (empty(XmlExportEngine::$exportOptions['export_variations'])){
            XmlExportEngine::$exportOptions['export_variations'] = XmlExportEngine::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION;
        }
        if (empty(XmlExportEngine::$exportOptions['export_variations_title'])){
            XmlExportEngine::$exportOptions['export_variations_title'] = XmlExportEngine::VARIATION_USE_PARENT_TITLE;
        }

		if (empty(XmlExportEngine::$exportOptions['xml_template_type'])) XmlExportEngine::$exportOptions['xml_template_type'] = 'simple';

		$filter_args = array(
			'filter_rules_hierarhy' => $this->options['filter_rules_hierarhy'],
			'product_matching_mode' => $this->options['product_matching_mode'],
            'taxonomy_to_export' => empty($this->options['taxonomy_to_export']) ? '' : $this->options['taxonomy_to_export']
		);

        $filters = \Wpae\Pro\Filtering\FilteringFactory::getFilterEngine();
        $filters->init($filter_args);

		if ('advanced' == $this->options['export_type']) 
		{
			// [ Update where clause]
			$filters->parse();

			XmlExportEngine::$exportOptions['whereclause'] = $filters->get('queryWhere');
			XmlExportEngine::$exportOptions['joinclause']  = $filters->get('queryJoin');

			$this->set(array( 'options' => XmlExportEngine::$exportOptions ))->update();
			// [\ Update where clause]
			
			if (XmlExportEngine::$is_user_export)
			{
				add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
				$exportQuery = eval('return new WP_User_Query(array(' . $this->options['wp_query'] . ', \'offset\' => ' . $this->exported . ', \'number\' => ' . $this->options['records_per_iteration'] . '));');			
				remove_action('pre_user_query', 'wp_all_export_pre_user_query');
			}
			elseif (XmlExportEngine::$is_comment_export)
			{				
				add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
				$exportQuery = eval('return new WP_Comment_Query(array(' . $this->options['wp_query'] . ', \'offset\' => ' . $this->exported . ', \'number\' => ' . $this->options['records_per_iteration'] . '));');			
				remove_action('comments_clauses', 'wp_all_export_comments_clauses');
			}
			else
			{		
				remove_all_actions('parse_query');
				remove_all_actions('pre_get_posts');
				remove_all_filters('posts_clauses');			

				add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
				add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);
				$exportQuery = eval('return new WP_Query(array(' . $this->options['wp_query'] . ', \'offset\' => ' . $this->exported . ', \'posts_per_page\' => ' . $this->options['records_per_iteration'] . '));');			
				remove_filter('posts_join', 'wp_all_export_posts_join');			
				remove_filter('posts_where', 'wp_all_export_posts_where');		
			}			
		}
		else
		{
			// [ Update where clause]
			$filters->parse();

			XmlExportEngine::$exportOptions['whereclause'] = $filters->get('queryWhere');
			XmlExportEngine::$exportOptions['joinclause']  = $filters->get('queryJoin');

			$this->set(array( 'options' => XmlExportEngine::$exportOptions ))->update();
			// [\ Update where clause]

			if ( in_array('users', $this->options['cpt']) or in_array('shop_customer', $this->options['cpt']))
			{				
				add_action('pre_user_query', 'wp_all_export_pre_user_query', 10, 1);
				$exportQuery = new WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC', 'number' => $this->options['records_per_iteration'], 'offset' => $this->exported));
				remove_action('pre_user_query', 'wp_all_export_pre_user_query');
			}
			elseif ( in_array('comments', $this->options['cpt']))
			{				
				add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
				global $wp_version;
					
				if ( version_compare($wp_version, '4.2.0', '>=') ) 
				{
					$exportQuery = new WP_Comment_Query( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => $this->options['records_per_iteration'], 'offset' => $this->exported));
				}
				else
				{
					$exportQuery = get_comments( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => $this->options['records_per_iteration'], 'offset' => $this->exported));
				}
				remove_action('comments_clauses', 'wp_all_export_comments_clauses');
			}
            elseif ( in_array('taxonomies', $this->options['cpt']))
            {
                add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
                $exportQuery = new WP_Term_Query( array( 'taxonomy' => $this->options['taxonomy_to_export'], 'orderby' => 'term_id', 'order' => 'ASC', 'number' => $this->options['records_per_iteration'], 'offset' => $this->exported, 'hide_empty' => false));
                $postCount  = count($exportQuery->get_terms());
                remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
            }
			else
			{				
				remove_all_actions('parse_query');
				remove_all_actions('pre_get_posts');
				remove_all_filters('posts_clauses');			
				
				add_filter('posts_where', 'wp_all_export_posts_where', 10, 1);
				add_filter('posts_join', 'wp_all_export_posts_join', 10, 1);
				
				$exportQuery = new WP_Query( array( 'post_type' => $this->options['cpt'], 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC', 'ignore_sticky_posts' => 1, 'offset' => $this->exported, 'posts_per_page' => $this->options['records_per_iteration'] ));

				remove_filter('posts_join', 'wp_all_export_posts_join');			
				remove_filter('posts_where', 'wp_all_export_posts_where');				
			}
		}

		XmlExportEngine::$exportQuery = $exportQuery;
		$errors = new WP_Error();
		$engine = new XmlExportEngine($this->options, $errors);	

		$file_path = false;

		$is_secure_import = PMXE_Plugin::getInstance()->getOption('secure');

		if ( $this->exported == 0 )
		{
			// create an import for this export			
			if ( $this->options['export_to'] == 'csv' || ! empty($this->options['xml_template_type']) && ! in_array($this->options['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ) PMXE_Wpallimport::create_an_import( $this );

			// unlink previously generated files
			$attachment_list = $this->options['attachment_list'];
			if ( ! empty($attachment_list))
			{
				foreach ($attachment_list as $attachment) {
					if (!is_numeric($attachment))
					{						
						@unlink($attachment);
					}
				}
			}
			$exportOptions = $this->options;
			$exportOptions['attachment_list'] = array();
			$this->set(array(				
				'options' => $exportOptions
			))->save();				 

			// generate export file name
			$file_path = wp_all_export_generate_export_file( $this->id ); 						

			if (  ! $is_secure_import )
			{
				$wp_filetype = wp_check_filetype(basename($file_path), null );
				$attachment_data = array(
				    'guid' => $wp_uploads['baseurl'] . '/' . _wp_relative_upload_path( $file_path ), 
				    'post_mime_type' => $wp_filetype['type'],
				    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_path)),
				    'post_content' => '',
				    'post_status' => 'inherit'
				);		

				if ( empty($this->attch_id) )
				{
					$attach_id = wp_insert_attachment( $attachment_data, $file_path );			
				}					
				elseif($this->options['creata_a_new_export_file']) {
					$attach_id = wp_insert_attachment( $attachment_data, $file_path );			
				}
				else
				{
					$attach_id = $this->attch_id;						
					$attachment = get_post($attach_id);
					if ($attachment)
					{
						update_attached_file( $attach_id, $file_path );
						wp_update_attachment_metadata( $attach_id, $attachment_data );	
					}
					else
					{
						$attach_id = wp_insert_attachment( $attachment_data, $file_path );				
					}						
				}				

				$exportOptions = $this->options;
				if ( ! in_array($attach_id, $exportOptions['attachment_list'])){
					$exportOptions['attachment_list'][] = $attach_id;	
				}
				
				$this->set(array(
					'attch_id' => $attach_id,
					'options' => $exportOptions
				))->save();				

			}	
			else 
			{
				$exportOptions = $this->options;
				$exportOptions['filepath'] = $file_path;
				$this->set(array(
					'options' => $exportOptions
				))->save();
			}

			do_action('pmxe_before_export', $this->id);
			
		}
		else
		{
			if (  ! $is_secure_import )
			{
				$file_path = str_replace($wp_uploads['baseurl'], $wp_uploads['basedir'], wp_get_attachment_url( $this->attch_id )); 
			}
			else
			{
				$file_path = wp_all_export_get_absolute_path($this->options['filepath']);
			}
		}

		// [ get total founded records ]
		if (XmlExportEngine::$is_comment_export)
		{
			global $wp_version;
					
			if ( version_compare($wp_version, '4.2.0', '>=') ) 
			{
				$postCount  = count($exportQuery->get_comments());
				add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
				$result = new WP_Comment_Query( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));
				$foundPosts = $result->get_comments();
				remove_action('comments_clauses', 'wp_all_export_comments_clauses');	
			}
			else
			{
				$postCount  = count($exportQuery);
				add_action('comments_clauses', 'wp_all_export_comments_clauses', 10, 1);
				$foundPosts = get_comments( array( 'orderby' => 'comment_ID', 'order' => 'ASC', 'number' => 10, 'count' => true));			
				remove_action('comments_clauses', 'wp_all_export_comments_clauses');	
			}
		}
		elseif(XmlExportEngine::$is_taxonomy_export){
            add_filter('terms_clauses', 'wp_all_export_terms_clauses', 10, 3);
            $result = new WP_Term_Query( array( 'taxonomy' => $this->options['taxonomy_to_export'], 'orderby' => 'term_id', 'order' => 'ASC', 'count' => true, 'hide_empty' => false));
            $foundPosts = count($result->get_terms());
            remove_filter('terms_clauses', 'wp_all_export_terms_clauses');
        }
		else
		{
			$foundPosts = ( ! XmlExportEngine::$is_user_export ) ? $exportQuery->found_posts : $exportQuery->get_total();
			$postCount  = ( ! XmlExportEngine::$is_user_export ) ? $exportQuery->post_count : count($exportQuery->get_results());
		}
		// [ \get total found records ]

		XmlExportEngine::$exportOptions  = $this->options;
		

        switch ( $this->options['export_to'] ) {

            case XmlExportEngine::EXPORT_TYPE_XML:

                if($this->options['xml_template_type'] == XmlExportEngine::EXPORT_TYPE_GOOLE_MERCHANTS) {
                    $googleMerchantsServiceFactory = new \Wpae\App\Service\ExportGoogleMerchantsFactory();
                    $googleMerchantsService = $googleMerchantsServiceFactory->createService();
                    $googleMerchantsService->export($cron, $file_path, $this->exported);
                } else {
                    XmlCsvExport::export_xml( false, $cron, $file_path, $this->exported );
                }

                break;

            case XmlExportEngine::EXPORT_TYPE_CSV:

                XmlCsvExport::export_csv( false, $cron, $file_path, $this->exported );
                break;

            default:
                # code...
                break;
        }

        $this->set(array(
            'exported' => $this->exported + $postCount,
            'last_activity' => date('Y-m-d H:i:s'),
            'processing' => 0
        ))->save();



		if ( empty($foundPosts) )
		{
			$this->set(array(
				'processing' => 0,
				'triggered' => 0,
				'canceled' => 0,
				'registered_on' => date('Y-m-d H:i:s'),
				'iteration' => ++$this->iteration
			))->update();	

			do_action('pmxe_after_export', $this->id, $this);
		}
		elseif ( ! $postCount or $foundPosts == $this->exported )
		{			
			if ( file_exists($file_path))
			{							
				if ( $this->options['export_to'] == 'xml' ) 
				{
                    switch( XmlExportEngine::$exportOptions['xml_template_type'] ){
                        case 'XmlGoogleMerchants':
                        case 'custom':
                            require_once PMXE_ROOT_DIR . '/classes/XMLWriter.php';
                            file_put_contents($file_path, PMXE_XMLWriter::preprocess_xml(XmlExportEngine::$exportOptions['custom_xml_template_footer']), FILE_APPEND);
                        break;
                    }

				    if ( ! in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) )
					{
						$main_xml_tag = apply_filters('wp_all_export_main_xml_tag', $this->options['main_xml_tag'], $this->id);
					
						file_put_contents($file_path, '</'.$main_xml_tag.'>', FILE_APPEND);

						$xml_footer = apply_filters('wp_all_export_xml_footer', '', $this->id);

						if ( ! empty($xml_footer) ) file_put_contents($file_path, $xml_footer, FILE_APPEND);
					}
				}

				PMXE_Wpallimport::generateImportTemplate( $this, $file_path, $foundPosts );								    

	            if ($this->options['is_scheduled'] and "" != $this->options['scheduled_email']){
	                
	                add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );                

	                $headers = 'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>' . "\r\n";
	                
	                $message = '<p>Export '. $this->options['friendly_name'] .' has been completed. You can find exported file in attachments.</p>';                

	                wp_mail($this->options['scheduled_email'], __("WP All Export", "pmxe_plugin"), $message, $headers, array($file_path));

	                remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
	            }

			}	

			$this->set(array(
				'processing' => 0,
				'triggered' => 0,
				'canceled' => 0,				
				'registered_on' => date('Y-m-d H:i:s'),	
				'iteration' => ++$this->iteration										
			))->update();	

			do_action('pmxe_after_export', $this->id, $this);
		}

		$this->set('registered_on', date('Y-m-d H:i:s'))->save(); // update registered_on to indicated that job has been exectured even if no files are going to be imported by the rest of the method
		
		return $this;
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

				if(isset($options['cc_combine_multiple_fields']) && isset($options['cc_combine_multiple_fields_value'])) {

					$field['cc_combine_multiple_fields'] = empty($options['cc_combine_multiple_fields'][$ID]) ? '' : $options['cc_combine_multiple_fields'][$ID];
					$field['cc_combine_multiple_fields_value'] = empty($options['cc_combine_multiple_fields_value'][$ID]) ? '' : $options['cc_combine_multiple_fields_value'][$ID];
				}

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
			$options['cc_combine_multiple_fields'] = array();
			$options['cc_combine_multiple_fields_value'] = array();

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
				if(isset($field['cc_combine_multiple_fields']) && isset($field['cc_combine_multiple_fields_value'])) {
					$options['cc_combine_multiple_fields'][] = $field['cc_combine_multiple_fields'];
					$options['cc_combine_multiple_fields_value'][] = $field['cc_combine_multiple_fields_value'];
				}

			}

			$this->set(array('options' => $options))->save();
		}		

		return $this;
	}

    public static function is_bundle_supported( $options )
    {	
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
