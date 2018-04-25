<?php

if ( ! class_exists('XmlExportComment') )
{
	final class XmlExportComment
	{			
		private $init_fields = array(			
			array(
				'label' => 'comment_ID',
				'name'  => 'ID',
				'type'  => 'comment_ID'
			),						
			array(
				'label' => 'comment_author_email',
				'name'  => 'Author Email',
				'type'  => 'comment_author_email'
			),			 			
			array(
				'label' => 'comment_content',
				'name'  => 'Content',
				'type'  => 'comment_content'
			)			
		);

		private $default_fields = array(
			array(
				'label' => 'comment_ID',
				'name'  => 'ID',
				'type'  => 'comment_ID'
			),
			array(
				'label' => 'comment_post_ID',
				'name'  => 'Post ID',
				'type'  => 'comment_post_ID'
			),			
			array(
				'label' => 'comment_author',
				'name'  => 'Author',
				'type'  => 'comment_author'
			),
			array(
				'label' => 'comment_author_email',
				'name'  => 'Author Email',
				'type'  => 'comment_author_email'
			),
			array(
				'label' => 'comment_author_url',
				'name'  => 'Author URL',
				'type'  => 'comment_author_url'
			),
			array(
				'label' => 'comment_author_IP',
				'name'  => 'Author IP',
				'type'  => 'comment_author_IP'
			),
			array(
				'label' => 'comment_date',
				'name'  => 'Date',
				'type'  => 'comment_date'
			),
			array(
				'label' => 'comment_content',
				'name'  => 'Content',
				'type'  => 'comment_content'
			),
			array(
				'label' => 'comment_karma',
				'name'  => 'Karma',
				'type'  => 'comment_karma'
			),
			array(
				'label' => 'comment_approved',
				'name'  => 'Approved',
				'type'  => 'comment_approved'
			),
			array(
				'label' => 'comment_agent',
				'name'  => 'Agent',
				'type'  => 'comment_agent'
			),
			array(
				'label' => 'comment_type',
				'name'  => 'Type',
				'type'  => 'comment_type'
			),
			array(
				'label' => 'comment_parent',
				'name'  => 'Comment Parent',
				'type'  => 'comment_parent'
			),
			array(
				'label' => 'user_id',
				'name'  => 'User ID',
				'type'  => 'user_id'
			)

		);		

		private $advanced_fields = array(					
			
		);			

		public static $is_active = true;

		public function __construct()
		{			

			if ( ( XmlExportEngine::$exportOptions['export_type'] == 'specific' and ! in_array('comments', XmlExportEngine::$post_types) ) 
					or ( XmlExportEngine::$exportOptions['export_type'] == 'advanced' and XmlExportEngine::$exportOptions['wp_query_selector'] != 'wp_comment_query' ) ){ 
				self::$is_active = false;
				return;
			}				
			
			add_filter("wp_all_export_available_sections", 	array( &$this, "filter_available_sections" ), 10, 1);
			add_filter("wp_all_export_init_fields", 		array( &$this, "filter_init_fields"), 10, 1);
			add_filter("wp_all_export_default_fields", 		array( &$this, "filter_default_fields"), 10, 1);
			add_filter("wp_all_export_other_fields", 		array( &$this, "filter_other_fields"), 10, 1);			
		}

		// [FILTERS]			

			/**
			*
			* Filter Init Fields
			*
			*/
			public function filter_init_fields($init_fields){
				return $this->init_fields;
			}

			/**
			*
			* Filter Default Fields
			*
			*/
			public function filter_default_fields($default_fields){
				return $this->default_fields;
			}

			/**
			*
			* Filter Other Fields
			*
			*/
			public function filter_other_fields($other_fields){
				return $this->advanced_fields;
			}	

			/**
			*
			* Filter Sections in Available Data
			*
			*/
			public function filter_available_sections($sections){	
										
				unset($sections['cats']);
				unset($sections['media']);
				unset($sections['other']);

				$sections['cf']['title'] = __("Comment meta", "wp_all_export_plugin");				

				return $sections;
			}					

		// [\FILTERS]

		public function init( & $existing_meta_keys = array() )
		{
			if ( ! self::$is_active ) return;

			global $wp_version;							

			if ( ! empty(XmlExportEngine::$exportQuery)){
				if ( version_compare($wp_version, '4.2.0', '>=') ) 
				{
					$comments = XmlExportEngine::$exportQuery->get_comments();
				}
				else
				{
					$comments = XmlExportEngine::$exportQuery;
				}
			}			

			if ( ! empty( $comments ) ) {
				foreach ( $comments as $comment ) {
					$comment_meta = get_comment_meta($comment->comment_ID, '');					
					if ( ! empty($comment_meta)){
						foreach ($comment_meta as $record_meta_key => $record_meta_value) {
							if ( ! in_array($record_meta_key, $existing_meta_keys) ){
								$to_add = true;
								foreach ($this->default_fields as $default_value) {
									if ( $record_meta_key == $default_value['name'] || $record_meta_key == $default_value['type'] ){
										$to_add = false;
										break;
									}
								}
								if ( $to_add ){
									foreach ($this->advanced_fields as $advanced_value) {
										if ( $record_meta_key == $advanced_value['name'] || $record_meta_key == $advanced_value['type']){
											$to_add = false;
											break;
										}
									}
								}
								if ( $to_add ) $existing_meta_keys[] = $record_meta_key;
							}
						}
					}		
				}
			}	
		}

		public static function prepare_data( $comment, $xmlWriter = false, $implode_delimiter, $preview )
		{
			$article = array();					

			// associate exported comment with import
			if ( wp_all_export_is_compatible() and XmlExportEngine::$exportOptions['is_generate_import'] and XmlExportEngine::$exportOptions['import_id'])
			{	
				$postRecord = new PMXI_Post_Record();
				$postRecord->clear();
				$postRecord->getBy(array(
					'post_id' => $comment->comment_ID,
					'import_id' => XmlExportEngine::$exportOptions['import_id'],
				));

				if ($postRecord->isEmpty()){
					$postRecord->set(array(
						'post_id' => $comment->comment_ID,
						'import_id' => XmlExportEngine::$exportOptions['import_id'],
						'unique_key' => $comment->comment_ID						
					))->save();
				}
				unset($postRecord);
			}	

			$is_xml_export = false;

			if ( ! empty($xmlWriter) and XmlExportEngine::$exportOptions['export_to'] == 'xml' and ! in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ){
				$is_xml_export = true;
			}		

			foreach (XmlExportEngine::$exportOptions['ids'] as $ID => $value) 
			{								
				$fieldName    = apply_filters('wp_all_export_field_name', wp_all_export_parse_field_name(XmlExportEngine::$exportOptions['cc_name'][$ID]), XmlExportEngine::$exportID);
				$fieldValue   = XmlExportEngine::$exportOptions['cc_value'][$ID];
				$fieldLabel   = XmlExportEngine::$exportOptions['cc_label'][$ID];
				$fieldSql     = XmlExportEngine::$exportOptions['cc_sql'][$ID];
				$fieldPhp     = XmlExportEngine::$exportOptions['cc_php'][$ID];
				$fieldCode    = XmlExportEngine::$exportOptions['cc_code'][$ID];
				$fieldType    = XmlExportEngine::$exportOptions['cc_type'][$ID];
				$fieldOptions = XmlExportEngine::$exportOptions['cc_options'][$ID];
                $fieldSettings = empty(XmlExportEngine::$exportOptions['cc_settings'][$ID]) ? $fieldOptions : XmlExportEngine::$exportOptions['cc_settings'][$ID];

				if ( empty($fieldName) or empty($fieldType) or ! is_numeric($ID)) continue;
				
				$element_name = ( ! empty($fieldName) ) ? $fieldName : 'untitled_' . $ID;

				$element_name_ns = '';
				
				if ( $is_xml_export )
				{					
					$element_name = ( ! empty($fieldName) ) ? preg_replace('/[^a-z0-9_:-]/i', '', $fieldName) : 'untitled_' . $ID;				

					if (strpos($element_name, ":") !== false)
					{
						$element_name_parts = explode(":", $element_name);
						$element_name_ns = (empty($element_name_parts[0])) ? '' : $element_name_parts[0];
						$element_name = (empty($element_name_parts[1])) ? 'untitled_' . $ID : preg_replace('/[^a-z0-9_-]/i', '', $element_name_parts[1]);							
					}
				} 
				
				$fieldSnipped = ( ! empty($fieldPhp ) and ! empty($fieldCode)) ? $fieldCode : false;

				switch ($fieldType)
				{						
					case 'comment_ID':													
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_id', pmxe_filter($comment->comment_ID, $fieldSnipped), $comment->comment_ID) );
						break;
					case 'comment_post_ID':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_post_id', pmxe_filter($comment->comment_post_ID, $fieldSnipped), $comment->comment_ID) );
						break;
					case 'comment_author':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_author', pmxe_filter($comment->comment_author, $fieldSnipped), $comment->comment_ID) );
						break;							
					case 'comment_author_email':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_author_email', pmxe_filter($comment->comment_author_email, $fieldSnipped), $comment->comment_ID) );
						break;
					case 'comment_author_url':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_author_url', pmxe_filter($comment->comment_author_url, $fieldSnipped), $comment->comment_ID) );
						break;
					case 'comment_author_IP':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_author_ip', pmxe_filter($comment->comment_author_IP, $fieldSnipped), $comment->comment_ID) );
						break;										
					case 'comment_karma':									
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_karma', pmxe_filter($comment->comment_karma, $fieldSnipped), $comment->comment_ID) );
						break;	
					case 'comment_content':
						$val = apply_filters('pmxe_comment_content', pmxe_filter($comment->comment_content, $fieldSnipped), $comment->comment_ID);										
						wp_all_export_write_article( $article, $element_name, ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val );
						break;		
					case 'comment_date':

                        $post_date = prepare_date_field_value($fieldSettings, strtotime($comment->comment_date), "Y-m-d H:i:s");

						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $comment->comment_ID) );
						break;						
					case 'comment_approved':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_approved', pmxe_filter($comment->comment_approved, $fieldSnipped), $comment->comment_ID) );
						break;	
					case 'comment_agent':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_agent', pmxe_filter($comment->comment_agent, $fieldSnipped), $comment->comment_ID) );
						break;	
					case 'comment_type':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_type', pmxe_filter($comment->comment_type, $fieldSnipped), $comment->comment_ID) );
						break;													
					case 'comment_parent':													
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_comment_parent', pmxe_filter($comment->comment_parent, $fieldSnipped), $comment->comment_ID) );
						break;
					case 'user_id':													
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_id', pmxe_filter($comment->user_id, $fieldSnipped), $comment->comment_ID) );
						break;							
					case 'cf':							
						if ( ! empty($fieldValue) ){																		
							$cur_meta_values = get_comment_meta($comment->comment_ID, $fieldValue);
							if (!empty($cur_meta_values) and is_array($cur_meta_values)){									
								$val = "";
								foreach ($cur_meta_values as $key => $cur_meta_value) {										
									if (empty($val)){
										$val = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $comment->comment_ID);																					
									}
									else{
										$val = apply_filters('pmxe_custom_field', pmxe_filter($val . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $comment->comment_ID);
									}
								}
								wp_all_export_write_article( $article, $element_name, $val );
							}		

							if (empty($cur_meta_values)){
								if (empty($article[$element_name])){									
									wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $fieldValue, $comment->comment_ID) );
								}									
							}																																																																
						}	
						break;																	
					case 'sql':							
						if ( ! empty($fieldSql) ) 
						{
							global $wpdb;											
							$val = $wpdb->get_var( $wpdb->prepare( stripcslashes(str_replace("%%ID%%", "%d", $fieldSql)), $comment->comment_ID ));
							if ( ! empty($fieldPhp) and !empty($fieldCode) )
							{
								// if shortcode defined
								if (strpos($fieldCode, '[') === 0)
								{
									$val = do_shortcode(str_replace("%%VALUE%%", $val, $fieldCode));
								}	
								else
								{
									$val = eval('return ' . stripcslashes(str_replace("%%VALUE%%", $val, $fieldCode)) . ';');
								}										
							}							
							wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_sql_field', $val, $element_name, $comment->comment_ID) );
						}
						break;					
					default:
						# code...
						break;
				}	

				if ( $is_xml_export and isset($article[$element_name]) )
				{
					$element_name_in_file = XmlCsvExport::_get_valid_header_name( $element_name );

					$xmlWriter = apply_filters('wp_all_export_add_before_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $comment->comment_ID);

					$xmlWriter->beginElement($element_name_ns, $element_name_in_file, null);
						$xmlWriter->writeData($article[$element_name], $element_name_in_file);
					$xmlWriter->closeElement();

					$xmlWriter = apply_filters('wp_all_export_add_after_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $comment->comment_ID);
				}																					
			}	
			return $article;	
		}

		/**
	     * __get function.
	     *
	     * @access public
	     * @param mixed $key
	     * @return mixed
	     */
	    public function __get( $key ) {
	        return $this->get( $key );
	    }	

	    /**
	     * Get a session variable
	     *
	     * @param string $key
	     * @param  mixed $default used if the session variable isn't set
	     * @return mixed value of session variable
	     */
	    public function get( $key, $default = null ) {        
	        return isset( $this->{$key} ) ? $this->{$key} : $default;
	    }
	}
}