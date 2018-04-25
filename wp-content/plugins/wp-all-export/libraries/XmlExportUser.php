<?php

if ( ! class_exists('XmlExportUser') ){

	final class XmlExportUser
	{		
		private $init_fields = array(			
			array(
				'label' => 'id',
				'name'  => 'ID',
				'type'  => 'id'
			),						
			array(
				'label' => 'user_email',
				'name'  => 'User Email',
				'type'  => 'user_email'
			),			 			
			array(
				'label' => 'user_login',
				'name'  => 'User Login',
				'type'  => 'user_login'
			)			
		);

		private $default_fields = array(
			array(
				'label' => 'id',
				'name'  => 'ID',
				'type'  => 'id'
			),
			array(
				'label' => 'user_login',
				'name'  => 'User Login',
				'type'  => 'user_login'
			),			
			array(
				'label' => 'user_email',
				'name'  => 'User Email',
				'type'  => 'user_email'
			),
			array(
				'label' => 'first_name',
				'name'  => 'First Name',
				'type'  => 'first_name'
			),
			array(
				'label' => 'last_name',
				'name'  => 'Last Name',
				'type'  => 'last_name'
			),
			array(
				'label' => 'user_registered',
				'name'  => 'User Registered',
				'type'  => 'user_registered'
			),
			array(
				'label' => 'user_nicename',
				'name'  => 'User Nicename',
				'type'  => 'user_nicename'
			),
			array(
				'label' => 'user_url',
				'name'  => 'User URL',
				'type'  => 'user_url'
			),
			array(
				'label' => 'display_name',
				'name'  => 'Display Name',
				'type'  => 'display_name'
			),
			array(
				'label' => 'nickname',
				'name'  => 'Nickname',
				'type'  => 'nickname'
			),
			array(
				'label' => 'description',
				'name'  => 'Description',
				'type'  => 'description'
			)
		);		

		private $advanced_fields = array(								
			array(
				'label' => 'wp_capabilities',
				'name'  => 'User Role',
				'type'  => 'wp_capabilities'
			),			
			array(
				'label' => 'user_pass',
				'name'  => 'User Pass',
				'type'  => 'user_pass'
			),							
			array(
				'label' => 'user_activation_key',
				'name'  => 'User Activation Key',
				'type'  => 'user_activation_key'
			),			
			array(
				'label' => 'user_status',
				'name'  => 'User Status',
				'type'  => 'user_status'
			)			
		);

		private $user_core_fields = array();

		public static $is_active = true;

		public static $is_export_shop_customer = false;

		public function __construct()
		{			

			$this->user_core_fields = array('rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front', 'wp_user_level', 'show_welcome_panel', 'dismissed_wp_pointers', 'session_tokens', 'wp_user-settings', 'wp_user-settings-time', 'wp_dashboard_quick_press_last_post_id', 'metaboxhidden_dashboard', 'closedpostboxes_dashboard', 'wp_nav_menu_recently_edited', 'meta-box-order_dashboard', 'closedpostboxes_product', 'metaboxhidden_product', 'manageedit-shop_ordercolumnshidden', 'aim', 'yim', 'jabber', 'wp_media_library_mode');

			if ( ( XmlExportEngine::$exportOptions['export_type'] == 'specific' and ! in_array('users', XmlExportEngine::$post_types)  and ! in_array('shop_customer', XmlExportEngine::$post_types) ) 
					or ( XmlExportEngine::$exportOptions['export_type'] == 'advanced' and XmlExportEngine::$exportOptions['wp_query_selector'] != 'wp_user_query' ) ){ 
				self::$is_active = false;
				return;
			}

			self::$is_active = true;

			if (in_array('shop_customer', XmlExportEngine::$post_types)) self::$is_export_shop_customer = true;			
			
			add_filter("wp_all_export_available_data", 		array( &$this, "filter_available_data"), 10, 1);
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

				$other_fields = array();

				foreach ( $this->advanced_fields as $key => $field ) {
					$other_fields[] = $field;					
				}

				foreach ( $this->user_core_fields as $field ) {
					$other_fields[] = $this->fix_titles(array(
						'label' => $field,
						'name'  => $field,
						'type'  => 'cf'
					));
				}

				return $other_fields;
			}	

			/**
			*
			* Filter Available Data
			*
			*/	
			public function filter_available_data($available_data){

				if (self::$is_export_shop_customer)
				{
					$available_data['address_fields'] = $this->available_customer_data();
				}
				elseif (self::$is_woo_custom_founded)
				{
					$available_data['customer_fields'] = $this->available_customer_data();
				}

				return $available_data;
			}

			/**
			*
			* Filter Sections in Available Data
			*
			*/
			public function filter_available_sections($available_sections){	
										
				unset($available_sections['cats']);
				unset($available_sections['media']);		

				if (self::$is_export_shop_customer)
				{
					$customer_data = array(
						'address' => array(
							'title'   => __("Address", "wp_all_export_plugin"), 
							'content' => 'address_fields'					
						)
					);

					return array_merge(array_slice($available_sections, 0, 1), $customer_data, array_slice($available_sections, 1));	
				}			
				elseif (self::$is_woo_custom_founded)
				{
					$customer_data = array(
						'customer' => array(
							'title'   => __("Address", "wp_all_export_plugin"), 
							'content' => 'customer_fields'					
						)
					);
					$available_sections = array_merge(array_slice($available_sections, 0, 1), $customer_data, array_slice($available_sections, 1));	
				}	

				self::$is_export_shop_customer or $available_sections['other']['title'] = __("Other", "wp_all_export_plugin");			

				return $available_sections;
			}					

		// [\FILTERS]
			
		public static $meta_keys;		
		public static $is_woo_custom_founded = false;
		public function init( & $existing_meta_keys = array() )
		{
			if ( ! self::$is_active ) return;

			global $wpdb;
			//$table_prefix = $wpdb->prefix;
			self::$meta_keys = $wpdb->get_results("SELECT DISTINCT ".$wpdb->usermeta .".meta_key FROM $wpdb->usermeta, $wpdb->users WHERE ".$wpdb->usermeta.".user_id = ".$wpdb->users.".ID LIMIT 500");

			$user_ids = array();
			if ( ! empty(XmlExportEngine::$exportQuery->results)) {
				foreach ( XmlExportEngine::$exportQuery->results as $user ) :				
					$user_ids[] = $user->ID;
				endforeach;
			}			
			
			if ( ! empty(self::$meta_keys)){

				$address_fields = $this->available_customer_data();

				$customer_users = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value != %s ", '_customer_user', '0'));

				// detect if at least one filtered user is a WooCommerce customer
				if ( ! empty($customer_users) ) {					
					foreach ($customer_users as $customer_user) {
						if ( in_array($customer_user->meta_value, $user_ids) ) 
						{
							self::$is_woo_custom_founded = true;				
							break;
						}
					}
				}

				$exclude_keys = array('_first_variation_attributes', '_is_first_variation_created');
				foreach (self::$meta_keys as $meta_key) {
					if ( ! in_array($meta_key->meta_key, $exclude_keys))
					{
						$to_add = true;
						foreach ($this->default_fields as $default_value) {
							if ( $meta_key->meta_key == $default_value['name'] || $meta_key->meta_key == $default_value['type'] ){
								$to_add = false;
								break;
							}
						}
						if ( $to_add ){
							foreach ($this->advanced_fields as $advanced_value) {
								if ( $meta_key->meta_key == $advanced_value['name'] || $meta_key->meta_key == $advanced_value['type']){
									$to_add = false;
									break;
								}
							}
						}
						if ( $to_add ){
							foreach ($this->user_core_fields as $core_field) {
								if ( $meta_key->meta_key == $core_field ){
									$to_add = false;
									break;
								}
							}
						}
						if ( $to_add && ( self::$is_export_shop_customer || self::$is_woo_custom_founded ) )
						{							
							foreach ($address_fields as $address_value) {										
								if ( $meta_key->meta_key == $address_value['label']){
									$to_add = false;
									break;
								}
							}
						}
						if ( $to_add )
						{									
							$existing_meta_keys[] = $meta_key->meta_key;
						} 
					}						
				}
			}			
		}

		public function available_customer_data()
		{
			
			$main_fields = array(
				array(
					'name' => __('Customer User ID', 'wp_all_export_plugin'),
					'label' => '_customer_user',
					'type' => 'cf'
				)						
			);

			$data = array_merge($main_fields, $this->available_billing_information_data(), $this->available_shipping_information_data());

			return apply_filters('wp_all_export_available_user_data_filter', $data);
		
		}

		public function available_billing_information_data()
		{
			
			$keys = array(
				'billing_first_name',  'billing_last_name', 'billing_company',
				'billing_address_1', 'billing_address_2', 'billing_city',
				'billing_postcode', 'billing_country', 'billing_state', 
				'billing_email', 'billing_phone'
			);

			$data = $this->generate_friendly_titles($keys, 'billing');

			return apply_filters('wp_all_export_available_billing_information_data_filter', $data);
		
		}

		public function available_shipping_information_data()
		{
			
			$keys = array(
				'shipping_first_name', 'shipping_last_name', 'shipping_company', 
				'shipping_address_1', 'shipping_address_2', 'shipping_city', 
				'shipping_postcode', 'shipping_country', 'shipping_state'
			);

			$data = $this->generate_friendly_titles($keys, 'shipping');

			return apply_filters('wp_all_export_available_shipping_information_data_filter', $data);
		
		}

		public function generate_friendly_titles($keys, $keyword = ''){
			$data = array();
			foreach ($keys as $key) {				
									
				$key1 = $this->fix_titles(str_replace('_', ' ', $key));
						$key2 = '';

						if(strpos($key1, $keyword)!== false)
						{
							$key1 = str_replace($keyword, '', $key1);
							$key2 = ' ('.__($keyword, 'wp_all_export_plugin').')';
						}
				
				$data[] = array(
					'name'  => __(trim($key1), 'woocommerce').$key2,
					'label' => $key,
					'type'  => 'cf'
				);
										
			}
			return $data;
		}
			/**
			*
			* Helper method to fix fields title
			*
			*/
			protected function fix_titles($field)
			{
				if (is_array($field))
				{
					$field['name'] = $this->fix_title($field['name']);
				}
				else
				{
					$field = $this->fix_title($field);
				}					
				return $field;
			}
				/**
				*
				* Helper method to fix single title
				*
				*/
				protected function fix_title($title)
				{
					$uc_title = ucwords(trim(str_replace("-", " ", str_replace("_", " ", $title))));

					return stripos($uc_title, "width") === false ? str_ireplace(array('id', 'url', 'sku', 'wp', 'ssl'), array('ID', 'URL', 'SKU', 'WP', 'SSL'), $uc_title) : $uc_title;
				}

		public static function prepare_data( $user, $xmlWriter = false, &$acfs, $implode_delimiter, $preview )
		{
			$article = array();	

			// associate exported user with import
			if ( wp_all_export_is_compatible() and XmlExportEngine::$exportOptions['is_generate_import'] and XmlExportEngine::$exportOptions['import_id'])
			{	
				$postRecord = new PMXI_Post_Record();
				$postRecord->clear();
				$postRecord->getBy(array(
					'post_id' => $user->ID,
					'import_id' => XmlExportEngine::$exportOptions['import_id'],
				));

				if ($postRecord->isEmpty()){
					$postRecord->set(array(
						'post_id' => $user->ID,
						'import_id' => XmlExportEngine::$exportOptions['import_id'],
						'unique_key' => $user->ID						
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

				if ( empty($fieldName) or empty($fieldType) or ! is_numeric($ID) ) continue;
				
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
					case 'id':
                        // For ID columns make first element in lowercase for Excel export
                        if ($element_name == 'ID' && !$ID && isset(XmlExportEngine::$exportOptions['export_to']) && XmlExportEngine::$exportOptions['export_to'] == 'csv' && isset(XmlExportEngine::$exportOptions['export_to_sheet']) && XmlExportEngine::$exportOptions['export_to_sheet'] != 'csv'){
                            $element_name = 'id';
                        }
                        wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_id', pmxe_filter($user->ID, $fieldSnipped), $user->ID) );
						break;
					case 'user_login':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_login', pmxe_filter($user->user_login, $fieldSnipped), $user->ID) );
						break;
					case 'user_pass':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_pass', pmxe_filter($user->user_pass, $fieldSnipped), $user->ID) );
						break;							
					case 'user_email':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_email', pmxe_filter($user->user_email, $fieldSnipped), $user->ID) );
						break;
					case 'user_nicename':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_nicename', pmxe_filter($user->user_nicename, $fieldSnipped), $user->ID) );
						break;
					case 'user_url':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_url', pmxe_filter($user->user_url, $fieldSnipped), $user->ID) );
						break;
					case 'user_activation_key':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_activation_key', pmxe_filter($user->user_activation_key, $fieldSnipped), $user->ID) );
						break;
					case 'user_status':													
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_status', pmxe_filter($user->user_status, $fieldSnipped), $user->ID) );
						break;
					case 'display_name':									
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_display_name', pmxe_filter($user->display_name, $fieldSnipped), $user->ID) );
						break;	
					case 'nickname':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_nickname', pmxe_filter($user->nickname, $fieldSnipped), $user->ID) );
						break;	
					case 'first_name':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_first_name', pmxe_filter($user->first_name, $fieldSnipped), $user->ID) );
						break;	
					case 'last_name':						
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_last_name', pmxe_filter($user->last_name, $fieldSnipped), $user->ID) );
						break;													
					case 'wp_capabilities':													
						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_wp_capabilities', pmxe_filter(implode($implode_delimiter, $user->roles), $fieldSnipped), $user->ID) );
						break;
					case 'description':
						$val = apply_filters('pmxe_user_description', pmxe_filter($user->description, $fieldSnipped), $user->ID);						
						wp_all_export_write_article( $article, $element_name, ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val );
						break;		
					case 'user_registered':

					    $post_date = prepare_date_field_value($fieldSettings, strtotime($user->user_registered));

						wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_user_registered', pmxe_filter($post_date, $fieldSnipped), $user->ID) );

						break;																	
					case 'cf':						

						if ( ! empty($fieldValue) )
						{
							$val = "";

							$cur_meta_values = get_user_meta($user->ID, $fieldValue);

							if ( ! empty($cur_meta_values) and is_array($cur_meta_values) )
							{
								foreach ($cur_meta_values as $key => $cur_meta_value) 
								{									
									if (empty($val))
									{
										$val = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $user->ID);																					
									}
									else
									{
										$val = apply_filters('pmxe_custom_field', pmxe_filter($val . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $user->ID);
									}
								}
								wp_all_export_write_article( $article, $element_name, $val );
							}		

							if ( empty($cur_meta_values) )
							{
								if ( empty($article[$element_name]) )
								{									
									wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $fieldValue, $user->ID) );
								}									
							}																																																																
						}	
						break;
					case 'acf':							

						if ( ! empty($fieldLabel) and class_exists( 'acf' ) )
						{		
							global $acf;

							$field_options = unserialize($fieldOptions);

							if ( ! $is_xml_export )
							{
								switch ($field_options['type']) {
									case 'textarea':
									case 'oembed':
									case 'wysiwyg':
									case 'wp_wysiwyg':
									case 'date_time_picker':
									case 'date_picker':
										
										$field_value = get_field($fieldLabel, 'user_' . $user->ID, false);

										break;
									
									default:
										
										$field_value = get_field($fieldLabel, 'user_' . $user->ID);								

										break;
								}
							}		
							else
							{
								$field_value = get_field($fieldLabel, 'user_' . $user->ID);	
							}
														
							XmlExportACF::export_acf_field(
								$field_value, 
								XmlExportEngine::$exportOptions, 
								$ID, 
								'user_' . $user->ID, 
								$article, 
								$xmlWriter, 
								$acfs, 
								$element_name, 
								$element_name_ns, 
								$fieldSnipped, 
								$field_options['group_id'], 
								$preview
							);
																																																																									
						}				
									
						break;												
					case 'sql':							
						
						if ( ! empty($fieldSql) ) 
						{
							global $wpdb;											
							$val = $wpdb->get_var( $wpdb->prepare( stripcslashes(str_replace("%%ID%%", "%d", $fieldSql)), $user->ID ));
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
							wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_sql_field', $val, $element_name, $user->ID) );
						}
						break;					
					default:
						# code...
						break;
				}	

				if ( $is_xml_export and isset($article[$element_name]) )
				{
					$element_name_in_file = XmlCsvExport::_get_valid_header_name( $element_name );

					$xmlWriter = apply_filters('wp_all_export_add_before_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $user->ID);

					$xmlWriter->beginElement($element_name_ns, $element_name_in_file, null);
						$xmlWriter->writeData($article[$element_name], $element_name_in_file);
					$xmlWriter->closeElement();

					$xmlWriter = apply_filters('wp_all_export_add_after_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $user->ID);
				}
			}
			return $article;
		}

		public static function prepare_import_template( $exportOptions, &$templateOptions, $element_name, $ID)
		{			

			$options = $exportOptions;

			$element_type = $options['cc_type'][$ID];

			$is_xml_template = $options['export_to'] == 'xml';

			$implode_delimiter = XmlExportEngine::$implode;

			switch ($element_type) 
			{
				// Export Users
				case 'user_login':
					$templateOptions['pmui']['login'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_login'] = 1;
					break;				
				case 'user_pass':
					$templateOptions['pmui']['pass'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_password'] = 1;
					break;
				case 'user_nicename':
					$templateOptions['pmui']['nicename'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_nicename'] = 1;
					break;
				case 'user_email':
					$templateOptions['pmui']['email'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_email'] = 1;
					break;
				case 'user_registered':
					$templateOptions['pmui']['registered'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_registered'] = 1;
					break;
				case 'display_name':
					$templateOptions['pmui']['display_name'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_display_name'] = 1;
					break;
				case 'user_url':
					$templateOptions['pmui']['url'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_url'] = 1;
					break;

				case 'first_name':
					$templateOptions['pmui']['first_name'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_first_name'] = 1;
					break;
				case 'last_name':
					$templateOptions['pmui']['last_name'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_last_name'] = 1;
					break;
				case 'wp_capabilities':
					$templateOptions['pmui']['role'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_role'] = 1;
					break;
				case 'nickname':
					$templateOptions['pmui']['nickname'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_nickname'] = 1;
					break;
				case 'description':
					$templateOptions['pmui']['description'] = '{'. $element_name .'[1]}';
					$templateOptions['is_update_description'] = 1;
					break;
			}
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