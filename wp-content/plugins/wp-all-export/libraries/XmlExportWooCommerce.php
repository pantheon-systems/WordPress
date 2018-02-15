<?php

if ( ! class_exists('XmlExportWooCommerce') )
{
	final class XmlExportWooCommerce
	{		
		public static $products_data = null;

		private $init_fields = array(			
			array(
				'name'  => 'SKU',
				'type'  => 'woo',				
				'label' => '_sku'
			),
			array(
				'name'  => 'product_type',
				'type'  => 'cats',				
				'label' => 'product_type',
			),
            array(
                'label' => 'parent',
                'name'  => 'Parent Product ID',
                'type'  => 'parent',
            )
		);
		
		private $_woo_data     = array();
		private $_product_data = array();

		/** @var \Wpae\App\Service\WooCommerceVersion  */
		private $wooCommerceVersion;

		private static $_existing_attributes = array();					

		public static $is_active = true;

		public function __construct()
		{
			$this->_woo_data = array(
				'_visibility', '_stock_status', '_downloadable', '_virtual', '_regular_price', '_sale_price', '_purchase_note', '_featured', '_weight', '_length',
				'_width', '_height', '_sku', '_sale_price_dates_from', '_sale_price_dates_to', '_price', '_sold_individually', '_manage_stock', '_stock', '_upsell_ids', '_crosssell_ids',
				'_downloadable_files', '_download_limit', '_download_expiry', '_download_type', '_product_url', '_button_text', '_backorders', '_tax_status', '_tax_class', '_product_image_gallery', '_default_attributes',
				'total_sales', '_product_attributes', '_product_version', '_variation_description', '_wc_rating_count', '_wc_review_count', '_wc_average_rating'
			);

			$this->wooCommerceVersion = new \Wpae\App\Service\WooCommerceVersion();
			$this->_product_data = array('_sku', '_price', '_regular_price','_sale_price', '_stock_status', '_stock', '_visibility', '_product_url', 'total_sales', 'attributes');

			if ( ! class_exists('WooCommerce') 
				or ( XmlExportEngine::$exportOptions['export_type'] == 'specific' and ! in_array('product', XmlExportEngine::$post_types) ) 
					or ( XmlExportEngine::$exportOptions['export_type'] == 'advanced' and strpos(XmlExportEngine::$exportOptions['wp_query'], 'product') === false ) ) {
				self::$is_active = false;
				return;			
			}			

			self::$is_active = true;

			if ( empty(PMXE_Plugin::$session) ) // if cron execution
			{
				$id = $_GET['export_id'];
				$export = new PMXE_Export_Record();
				$export->getById($id);	
				if ( $export->options['export_to'] == 'csv'){	
					$this->init_additional_data();
				}
			} 
			else
			{
				self::$products_data = PMXE_Plugin::$session->get('products_data');												
				
				if (empty(self::$products_data))
				{
					$this->init_additional_data();
				}
			}

            global $wp_taxonomies;

            $max_input_vars = @ini_get('max_input_vars');
            if (empty($max_input_vars)) $max_input_vars = 100;
            foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

              if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3 ){

                  if ( count($this->init_fields) < $max_input_vars / 10 ){
                      $this->init_fields[] = array(
                          'name'  => $obj->label,
                          'label' => $obj->name,
                          'type'  => 'attr'
                      );
                  }
              }
            }

            if ( ! empty(self::$products_data['attributes']))
            {
              foreach (self::$products_data['attributes'] as $attribute) {
                  if ( count($this->init_fields) < $max_input_vars / 10 ) {
                      $this->init_fields[] = $this->fix_titles(array(
                          'name' => str_replace('attribute_', '', $attribute->meta_key),
                          'label' => $attribute->meta_key,
                          'type' => 'cf'
                      ));
                  }
              }
            }

            $this->init_fields[] = array(
                'name' => 'Product Attributes',
                'label' => '_product_attributes',
                'type' => 'woo'
            );

			add_filter("wp_all_export_init_fields",    		array( &$this, "filter_init_fields"), 10, 1);
			add_filter("wp_all_export_default_fields", 		array( &$this, "filter_default_fields"), 10, 1);
			add_filter("wp_all_export_other_fields", 		array( &$this, "filter_other_fields"), 10, 1);
			add_filter("wp_all_export_available_sections",  array( &$this, "filter_available_sections"), 10, 1);
			add_filter("wp_all_export_available_data", 		array( &$this, "filter_available_data"), 10, 1);			

		}

		// [FILTERS]
			
			/**
			*
			* Filter Init Fields
			*
			*/
			public function filter_init_fields($init_fields){
				foreach ($this->init_fields as $field) {
					$init_fields[] = $field;
				}			
				return array_map(array( &$this, 'fix_titles'), $init_fields);
			}

			/**
			*
			* Filter Default Fields
			*
			*/
			public function filter_default_fields($default_fields){		
				foreach ($default_fields as $key => $field) {
					$default_fields[$key]['auto'] = true;
				}
                $default_fields[] = array(
                    'label' => 'parent',
                    'name'  => 'Parent Product ID',
                    'type'  => 'parent',
                    'auto'  => true
                );
				return array_map(array( &$this, 'fix_titles'), $default_fields);	
			}

			/**
			*
			* Filter Other Fields
			*
			*/
			public function filter_other_fields($other_fields){
				
				$advanced_fields = $this->get_fields_for_product_advanced_section();

				foreach ($advanced_fields as $advanced_field) 
				{
					$other_fields[] = $advanced_field;
				}

				// add needed fields to auto generate list
				foreach ($other_fields as $key => $field) 
				{
					if ( strpos($field['label'], '_min_') === 0 || strpos($field['label'], '_max_') === 0 ) 
						continue;

                    if ($field['type'] == 'parent'){
                        unset($other_fields[$key]);
                        continue;
                    }

					if ( $field['type'] != 'attr' ) $other_fields[$key]['auto'] = true;	

					$other_fields[$key] = $this->fix_titles($other_fields[$key]);					
				}					

				return $other_fields;
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
						$uc_title = ucwords(trim(str_replace("_", " ", $title)));

						return stripos($uc_title, "width") === false ? str_ireplace(array(' id ', ' url ', ' sku ', ' wc '), array('ID', 'URL', 'SKU', 'WC'), $uc_title) : $uc_title;
					}

				// helper method
				protected function get_fields_for_product_advanced_section()
				{
					$advanced_fields = array();

					if ( ! empty($this->_woo_data))
					{
						foreach ($this->_woo_data as $woo_key) 
						{						
							if ( strpos($woo_key, 'attribute_pa_') === 0 ) continue;		

							if ( ! in_array($woo_key, $this->_product_data) )
							{
								switch ($woo_key) 
								{
									case '_upsell_ids':
										$advanced_fields[] = array(
											'name'  => 'Up-Sells',
											'label' => $woo_key,
											'type'  => 'woo'
										);
										break;
									case '_crosssell_ids':
										$advanced_fields[] = array(
											'name'  => 'Cross-Sells',
											'label' => $woo_key,
											'type'  => 'woo'
										);
										break;
									
									default:
										$advanced_fields[] = $this->fix_titles(array(
											'name'  => $woo_key,
											'label' => $woo_key,
											'type'  => 'woo'
										));										
										break;
								}								
							}
						}						
					}
					return $advanced_fields;
				}

				protected function get_fields_for_product_attributes_section()
				{
					$attributes_fields = array();					

					if ( empty(self::$_existing_attributes) )
					{
						global $wp_taxonomies;

						foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

							if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3 and ! in_array($obj->name, self::$_existing_attributes))
								self::$_existing_attributes[] = $obj->name;															
						}
					}
										
					if ( ! empty(self::$_existing_attributes) )
					{
						foreach (self::$_existing_attributes as $key => $tx_name) {
							$tx = get_taxonomy($tx_name);
                            $attributes_fields[] = array(
                                  'name'  => $tx->label,
                                  'label' => $tx_name,
                                  'type'  => 'attr'
                            );
						}
					}

					if ( ! empty(self::$products_data['attributes']))
					{
						foreach (self::$products_data['attributes'] as $attribute) {
							$attributes_fields[] = $this->fix_titles(array(
								'name'  => str_replace('attribute_', '', $attribute->meta_key),
								'label' => $attribute->meta_key,
								'type'  => 'cf'								
							));
						}
					}
					
					return $attributes_fields;
				}

			/**
			*
			* Filter Available Data
			*
			*/	
			public function filter_available_data($available_data){
				$available_data['woo_data'] = $this->_woo_data;
				$available_data['existing_attributes'] = self::$_existing_attributes;				
				$available_data['product_fields'] = $this->get_fields_for_product_data_section();
				// $available_data['product_attributes'] = $this->get_fields_for_product_attributes_section();								

				if ( ! empty($available_data['existing_taxonomies']) )	{
					$existing_taxonomies = $available_data['existing_taxonomies'];
					$available_data['existing_taxonomies'] = array();
					foreach ($existing_taxonomies as $taxonomy) {
						$tx = get_taxonomy($taxonomy['label']);
						if ($taxonomy['label'] == 'product_shipping_class')
						{
							$available_data['product_fields'][] = array(
								'name'  => 'Shipping Class',
								'label' => $taxonomy['label'],
								'type'  => 'cats',
								'auto'  => true
							);
						}
						else
						{							
							$available_data['existing_taxonomies'][] = 	array(
								'name'  => ($taxonomy['label'] == 'product_type') ? 'Product Type' : (empty($tx->label) ? $tx->name : $tx->label),
								'label' => $taxonomy['label'],
								'type'  => 'cats',
								'auto'  => true
							);						
						}						
					}
				}

				return $available_data;
			}		
				// helper method
				protected function get_fields_for_product_data_section()
				{
					$product_fields = array();
					if ( ! empty($this->_product_data) )
					{
						foreach ($this->_product_data as $woo_key) 
						{						
							switch ($woo_key) 
							{
								case '_product_url':
									$product_fields[] = array(
										'name'  => 'External Product URL',
										'label' => $woo_key,
										'type'  => 'woo',
										'auto'  => true
									);
									break;
								
								default:
									$product_fields[] = $this->fix_titles(array(
										'name'  => $woo_key,
										'label' => $woo_key,
										'type'  => 'woo',
										'auto'  => true
									));
									break;
							}						
						}					
					}					
					return $product_fields;
				}		

			/**
			*
			* Filter Sections in Available Data
			*
			*/
			public function filter_available_sections($available_sections){

				$available_sections['other']['title'] = __("Other", "wp_all_export_plugin");

				$product_data = array(
					'product_data' => array(
						'title'    => __("Product Data", "wp_all_export_plugin"), 
						'content'  => 'product_fields',
						'additional' => array(
							'attributes' => array(
								'title' => __("Attributes", "wp_all_export_plugin"), 
								'meta'  => $this->get_fields_for_product_attributes_section()
							)
						)						
					),
					// 'product_attributes' => array(
					// 	'title'    => __("Attributes", "wp_all_export_plugin"), 
					// 	'content'  => 'product_attributes'						
					// ),
				);					
				return array_merge(array_slice($available_sections, 0, 1), $product_data, array_slice($available_sections, 1));	
			}

		// [\FILTERS]

		public function init( & $existing_meta_keys = array() ){

			if ( ! self::$is_active ) return;

			$hide_fields = array('_edit_lock', '_edit_last');		

			$this->filter_meta_keys( $existing_meta_keys );			

			global $wp_taxonomies;	

			foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

				if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3 and ! in_array($obj->name, self::$_existing_attributes))
					self::$_existing_attributes[] = $obj->name;															
			}

		}
			// helper method
			protected function filter_meta_keys( & $meta_keys = array() )
			{
				if ( ! empty($meta_keys) )
				{
					foreach ($meta_keys as $key => $record_meta_key) {
						
						if ( in_array($record_meta_key, $this->_woo_data) ) unset($meta_keys[$key]);

						if ( strpos($record_meta_key, 'attribute_pa_') === 0 || strpos($record_meta_key, '_min_') === 0 || strpos($record_meta_key, '_max_') === 0){
							if ( ! in_array($record_meta_key, $this->_woo_data)){
								$this->_woo_data[] = $record_meta_key;
								unset($meta_keys[$key]);
							}												
						}		
						if ( strpos($record_meta_key, 'attribute_') === 0 && strpos($record_meta_key, 'attribute_pa_') === false )
						{
							unset($meta_keys[$key]);
						}				
					}
				}
			}

		public function init_additional_data()
		{			
			if ( ! self::$is_active ) return;								

			$cs = PMXE_Plugin::getInstance()->getAdminCurrentScreen();

			if ( empty(self::$products_data) or ! empty($cs) and 'PMXE_Admin_Manage' == $cs->base )
			{				

				self::$products_data = array();				

				global $wpdb;

				$table_prefix = $wpdb->prefix;

				self::$products_data['attributes'] = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_key FROM {$table_prefix}postmeta 
						WHERE {$table_prefix}postmeta.meta_key LIKE %s AND {$table_prefix}postmeta.meta_key NOT LIKE %s", 'attribute_%', 'attribute_pa_%'));				

				if ( ! empty(PMXE_Plugin::$session) )
				{ 
					PMXE_Plugin::$session->set('products_data', self::$products_data);
					PMXE_Plugin::$session->save_data();
				}
			}
		}

		// [prepare fields for order items]
		public function get_all_fields_for_order_items()
		{			
			$meta_keys = wp_all_export_get_existing_meta_by_cpt('product');
			
			$this->filter_meta_keys( $meta_keys );

			$default_data   = $this->get_default_fields_for_order_items();

			$product_data   = $this->get_fields_for_product_data_section();

			$taxes_data     = array();
			
			foreach ($product_data as $field) 
			{
				if ( ! in_array($field['label'], array('_sku', 'attributes')))	$default_data[] = $field;
			}

			$existing_taxonomies = wp_all_export_get_existing_taxonomies_by_cpt('product');

			foreach ($existing_taxonomies as $taxonomy)
			{
				$tx = get_taxonomy($taxonomy['label']);
				if ($taxonomy['label'] == 'product_shipping_class')
				{
					$product_data[] = array(
						'name'  => 'Shipping Class',
						'label' => $taxonomy['name'],
						'type'  => 'cats',
						'auto'  => true
					);
				}
				else
				{							
					$taxes_data[] = 	array(
						'name'  => ($taxonomy['label'] == 'product_type') ? 'Product Type' : $tx->label,
						'label' => $taxonomy['label'],
						'type'  => 'cats',
						'auto'  => true
					);						
				}						
			}

			return array(
				'product_data'  => array(
					'title' => __('Product Data', 'wp_all_export_plugin'), 
					'meta'  => $default_data,
					// 'additional' => array(
					// 	'attributes' => array(
					// 		'title' => __("Attributes", "wp_all_export_plugin"),
					// 		'meta' => $this->get_fields_for_product_attributes_section()
					// 	)
					// )
				),
				'taxonomies'    => array(
					'title' => __('Taxonomies', 'wp_all_export_plugin'),
					'meta'  => $taxes_data
				),
				'cf' 			=> array(
					'title' => __('Custom Fields', 'wp_all_export_plugin'),
					'meta'  => $meta_keys
				),
				'attributes'      => array(
					'title' => __('Attributes', 'wp_all_export_plugin'),
					'meta'  => $this->get_fields_for_product_attributes_section()
				),
				'advanced'      => array(
					'title' => __('Advanced', 'wp_all_export_plugin'),
					'meta'  => $this->get_fields_for_product_advanced_section()
				)
			);
		}
			// helper method
			protected function get_default_fields_for_order_items()
			{
				$exclude_default_fields = array('id', 'title', 'permalink');

				$default_fields = array();
				foreach (XmlExportEngine::$default_fields as $field) 
				{
					if ( ! in_array($field['type'], $exclude_default_fields) ) $default_fields[] = $field;
				}
				return $default_fields;
			}
		// [\prepare fields for order items]

		protected function prepare_export_data( $record, $options, $elId )
		{
			$data = array();

			$element_value = str_replace("item_data__", "", $options['cc_value'][$elId]);

			if ( ! empty($element_value) )
			{									
				$implode_delimiter = XmlExportEngine::$implode;

				$element_name = ( ! empty($options['cc_name'][$elId]) ) ? $options['cc_name'][$elId] : 'untitled_' . $elId;				
				$fieldSnipped = ( ! empty($options['cc_php'][$elId]) and ! empty($options['cc_code'][$elId]) ) ? $options['cc_code'][$elId] : false;

				switch ($element_value)
				{
					case 'attributes':
						$_product_attributes = (empty($record->post_parent)) ? get_post_meta($record->ID, '_product_attributes', true) : get_post_meta($record->post_parent, '_product_attributes', true);

						if ( empty(self::$_existing_attributes) )
						{
							global $wp_taxonomies;	

							foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

								if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3 and ! in_array($obj->name, self::$_existing_attributes))
									self::$_existing_attributes[] = $obj->name;															
							}
						}

						// combine taxonomies attributes
						if ( ! empty(self::$_existing_attributes))
						{
							foreach (self::$_existing_attributes as $taxonomy_slug) {

								$taxonomy = get_taxonomy($taxonomy_slug);

                                $taxonomy_slug_xpath = str_replace("-", "_", $taxonomy_slug);
								$data['Attribute Name (' . $taxonomy_slug_xpath . ')'] = $taxonomy->labels->singular_name;
								$data['Attribute In Variations (' . $taxonomy_slug_xpath . ')'] = (!empty($_product_attributes[strtolower(urlencode($taxonomy_slug))]['is_variation'])) ? "yes" : "no";
								$data['Attribute Is Visible (' . $taxonomy_slug_xpath . ')']    = (!empty($_product_attributes[strtolower(urlencode($taxonomy_slug))]['is_visible'])) ? "yes" : "no";
								$data['Attribute Is Taxonomy (' . $taxonomy_slug_xpath . ')']   = (!empty($_product_attributes[strtolower(urlencode($taxonomy_slug))]['is_taxonomy'])) ? "yes" : "no";

								$element_name = 'Attribute Value (' . $taxonomy_slug_xpath . ')';

								if ($record->post_parent == 0)
								{
                                  if (isset($_product_attributes[strtolower(urlencode($taxonomy_slug))]['value'])) {
                                    $txes_list = get_the_terms($record->ID, $taxonomy_slug);
                                    if (!is_wp_error($txes_list) and !empty($txes_list)) {
                                      $attr_new = array();
                                      foreach ($txes_list as $t) {
                                        $attr_new[] = $t->name;
                                      }
                                      $data[$element_name] = apply_filters('pmxe_woo_attribute', pmxe_filter(implode($implode_delimiter, $attr_new), $fieldSnipped), $record->ID, $taxonomy_slug);
                                    }
                                    else {
                                      $data[$element_name] = pmxe_filter('', $fieldSnipped);
                                    }
                                  }
                                  else
                                  {
                                    $data[$element_name] = pmxe_filter('', $fieldSnipped);
                                  }
								}
								else
								{
									$variation_attribute = get_post_meta($record->ID, 'attribute_' . strtolower(urlencode($taxonomy_slug)), true);
                                    $term = get_term_by('slug', $variation_attribute, $taxonomy_slug);
                                    if ($term and !is_wp_error($term)){
                                        $variation_attribute = $term->name;
                                    }
									$data[$element_name] = apply_filters('pmxe_woo_attribute', pmxe_filter($variation_attribute, $fieldSnipped), $record->ID, $taxonomy_slug);
								}
							}
						}

						// combine custom attributes
						if ( ! empty(self::$products_data['attributes']))
						{
							foreach (self::$products_data['attributes'] as $attribute) 
							{
								$attribute_standart_name = str_replace('attribute_', '', $attribute->meta_key);
								$attribute_name = ucfirst($attribute_standart_name);

								$data['Attribute Name (' . $attribute_name . ')']  = $attribute_name;
								$data['Attribute Value (' . $attribute_name . ')'] = get_post_meta($record->ID, $attribute->meta_key, true);

								$data['Attribute In Variations (' . $attribute_name . ')'] = (!empty($_product_attributes[$attribute_standart_name]['is_variation'])) ? "yes" : "no";
								$data['Attribute Is Visible (' . $attribute_name . ')']    = (!empty($_product_attributes[$attribute_standart_name]['is_visible'])) ? "yes" : "no";
								$data['Attribute Is Taxonomy (' . $attribute_name . ')']   = (!empty($_product_attributes[$attribute_standart_name]['is_taxonomy'])) ? "yes" : "no";
							}
						}

						break;
					
					default:

						$cur_meta_values = get_post_meta($record->ID, $element_value);

						if ( ! empty($cur_meta_values) and is_array($cur_meta_values) )
						{
							foreach ($cur_meta_values as $key => $cur_meta_value) 
							{
								$fieldOptions = $options['cc_options'][$elId];
								$fieldSettings = empty($options['cc_settings'][$elId]) ? $fieldOptions : $options['cc_settings'][$elId];

								switch ($element_value)
								{									
									case '_downloadable_files':
										
										$files = maybe_unserialize($cur_meta_value);
										$file_paths = array();
										$file_names = array();

										if ( ! empty($files) ){
											
											foreach ($files as $key => $file) {
												$file_paths[] = $file['file'];
												$file_names[] = $file['name'];
											}											
											
										}
										$data[$element_name . ' Paths'] = pmxe_filter(implode($implode_delimiter, $file_paths), $fieldSnipped);
										$data[$element_name . ' Names'] = pmxe_filter(implode($implode_delimiter, $file_names), $fieldSnipped);

										break;
									case '_crosssell_ids':
									case '_upsell_ids':

										$sell_export_type = empty($options['cc_settings'][$elId]) ? 'sku' : $options['cc_settings'][$elId];

										$_ids = maybe_unserialize($cur_meta_value);
										$_values = array();
										if (!empty($_ids)){

											switch ($sell_export_type) {
												case 'sku':
													foreach ($_ids as $_id) {
														$_values[] = get_post_meta($_id, '_sku', true);
													}
													break;
												case 'id':
													$_values = $_ids;
													break;
												case 'name':
													foreach ($_ids as $_id) {
														$p = get_post($_id);
														if ($p)
														{
															$_values[] = $p->post_name;	
														}														
													}
													break;
												default:
													# code...
													break;
											}																																	
										}
										$data[$element_name] = apply_filters('pmxe_woo_field', pmxe_filter(implode($implode_delimiter, $_values), $fieldSnipped), $element_value, $record->ID);								
										break;

									case '_sale_price_dates_from':
									case '_sale_price_dates_to':

                                        $post_date = $cur_meta_value ? prepare_date_field_value($fieldSettings, $cur_meta_value) : "";

										$data[$element_name] = apply_filters('pmxe_woo_field', pmxe_filter($post_date, $fieldSnipped), $element_value, $record->ID);
										
										// wp_all_export_write_article( $article, $element_name, apply_filters('pmxe_post_date', pmxe_filter($post_date, $fieldSnipped), $record->ID) );
										break;		

                                    case '_tax_class':

                                        if ( $cur_meta_value == '' ){
                                            $tax_status = get_post_meta($record->ID, '_tax_status', true);
                                            if ( 'taxable' == $tax_status ){
                                                $cur_meta_value = 'standard';
                                            }
                                        }
                                        $data[$element_name] = apply_filters('pmxe_woo_field', pmxe_filter($cur_meta_value, $fieldSnipped), $element_value, $record->ID);
                                        break;

									default:

										if ( empty($data[$element_name]) )
										{											
											$data[$element_name] = apply_filters('pmxe_woo_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $element_value, $record->ID);																			
										}
										else
										{
											$data[$element_name . '_' . $key] = apply_filters('pmxe_woo_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $element_value, $record->ID);																														
										}

										break;
								}		
							}
						}

						if($this->wooCommerceVersion->isWooCommerceNewerThan('3.0')) {
							if($element_value == '_featured') {
								if($record->post_type == 'product_variation') {
									$product = new WC_Product($record->post_parent);
								}
								else {
									$product = new WC_Product($record->ID);
								}

								if($product) {
									$value = $product->is_featured() ? 'yes' : 'no';
									$data[$element_name] = apply_filters('pmxe_woo_field',pmxe_filter($value, $fieldSnipped), $element_value, $record->ID);
								}
							}
						}

						if ( empty($cur_meta_values) && $element_value != '_featured' )
						{												
							$data[$element_name] = apply_filters('pmxe_woo_field', pmxe_filter('', $fieldSnipped), $element_value, $record->ID);
						}

						break;
				}
																																																																						
			}


			return $data;
		}

		public function export_csv( & $article, & $titles, $record, $options, $element_key )
		{			
			$data_to_export = $this->prepare_export_data( $record, $options, $element_key );

			foreach ($data_to_export as $key => $data) 
			{
				// $article[$key] = $data;	
				wp_all_export_write_article( $article, $key, $data );
			}
		}

		public function get_element_header( & $headers, $options, $element_key )
		{
			$element_value = str_replace("item_data__", "", $options['cc_value'][$element_key]);

			switch ($element_value) 
			{
				case 'attributes':

					// headers for taxonomy attributes
					if ( ! empty(self::$_existing_attributes))
					{
						foreach (self::$_existing_attributes as $taxonomy_slug) {

							$taxonomy = get_taxonomy($taxonomy_slug);
                            $taxonomy_slug_xpath = str_replace("-", "_", $taxonomy_slug);
                            $headers[] = 'Attribute Name (' . $taxonomy_slug_xpath . ')';
                            $headers[] = 'Attribute Value (' . $taxonomy_slug_xpath . ')';
                            $headers[] = 'Attribute In Variations (' . $taxonomy_slug_xpath . ')';
                            $headers[] = 'Attribute Is Visible (' . $taxonomy_slug_xpath . ')';
                            $headers[] = 'Attribute Is Taxonomy (' . $taxonomy_slug_xpath . ')';
						}
					}

					// headers for custom attributes
					if ( ! empty(self::$products_data['attributes']))
					{
						foreach (self::$products_data['attributes'] as $attribute) 
						{
							$attribute_name = ucfirst(str_replace('attribute_', '', $attribute->meta_key));

                            $headers[] = 'Attribute Name (' . $attribute_name . ')';
                            $headers[] = 'Attribute Value (' . $attribute_name . ')';
                            $headers[] = 'Attribute In Variations (' . $attribute_name . ')';
                            $headers[] = 'Attribute Is Visible (' . $attribute_name . ')';
                            $headers[] = 'Attribute Is Taxonomy (' . $attribute_name . ')';
						}
					}

					break;

				case '_downloadable_files':

					$headers[] = $options['cc_name'][$element_key] . ' Paths';
					$headers[] = $options['cc_name'][$element_key] . ' Names';

					break;

				default:

					if ( ! in_array($options['cc_name'][$element_key], $headers)) 
					{
						$headers[] = $options['cc_name'][$element_key];
					}
					else
					{
						$is_added = false;
						$i = 0;
						do
						{
							$new_element_name = $options['cc_name'][$element_key] . '_' . md5($i);

							if ( ! in_array($new_element_name, $headers) )
							{
								$headers[] = $new_element_name;
								$is_added = true;
							}

							$i++;
						}
						while ( ! $is_added );						
					}					

					break;
			}
		}

		public function export_xml( & $xmlWriter, $record, $options, $elId ){

			if ( ! self::$is_active ) return;	

			$data_to_export = $this->prepare_export_data( $record, $options, $elId );

			foreach ($data_to_export as $key => $data) 
			{			
				$element_name_ns = '';	
				$element_name = str_replace("-", "_", preg_replace('/[^a-z0-9:_]/i', '', $key));
				if (strpos($element_name, ":") !== false)
				{
					$element_name_parts = explode(":", $element_name);
					$element_name_ns = (empty($element_name_parts[0])) ? '' : $element_name_parts[0];
					$element_name = (empty($element_name_parts[1])) ? 'untitled_' . $ID : $element_name_parts[1];							
				}
				
				$xmlWriter = apply_filters('wp_all_export_add_before_element', $xmlWriter, $element_name, XmlExportEngine::$exportID, $record->ID);

				$xmlWriter->beginElement($element_name_ns, $element_name, null);
					$xmlWriter->writeData($data, $element_name);
				$xmlWriter->closeElement();			

				$xmlWriter = apply_filters('wp_all_export_add_after_element', $xmlWriter, $element_name, XmlExportEngine::$exportID, $record->ID);		
			}

		}		

		public static function prepare_import_template( $exportOptions, &$templateOptions, &$cf_list, &$attr_list, $element_name, $element_key )
		{
			if ( ! in_array($element_key, $cf_list)) $cf_list[] = $element_key;

			$is_xml_template = $exportOptions['export_to'] == 'xml';

			$implode_delimiter = XmlExportEngine::$implode;

			switch ($element_key) 
			{												
				case '_visibility':
					$templateOptions['is_product_visibility'] = 'xpath';
					$templateOptions['single_product_visibility'] = '{'. $element_name .'[1]}';
					break;				
				case '_stock_status':
					$templateOptions['product_stock_status'] = 'xpath';
					$templateOptions['single_product_stock_status'] = '{'. $element_name .'[1]}';
					break;
				case '_downloadable':
					$templateOptions['is_product_downloadable'] = 'xpath';
					$templateOptions['single_product_downloadable'] = '{'. $element_name .'[1]}';
					break;
				case '_virtual':
					$templateOptions['is_product_virtual'] = 'xpath';
					$templateOptions['single_product_virtual'] = '{'. $element_name .'[1]}';
					break;
				case '_price':
					$templateOptions['single_product_regular_price'] = '{'. $element_name .'[1]}';
					break;
				case '_regular_price':
					$templateOptions['single_product_regular_price'] = '{'. $element_name .'[1]}';
					if ( ! in_array('_price', $cf_list)) $cf_list[] = '_price';
					break;
				case '_sale_price':													
					$templateOptions['single_product_sale_price'] = '{'. $element_name .'[1]}';
					if ( ! in_array('_price', $cf_list)) $cf_list[] = '_price';
					break;
				case '_purchase_note':													
					$templateOptions['single_product_purchase_note'] = '{'. $element_name .'[1]}';
					break;
				case '_featured':				
					$templateOptions['is_product_featured'] = 'xpath';									
					$templateOptions['single_product_featured'] = '{'. $element_name .'[1]}';
					break;
				case '_weight':																	
					$templateOptions['single_product_weight'] = '{'. $element_name .'[1]}';
					break;
				case '_length':
					$templateOptions['single_product_length'] = '{'. $element_name .'[1]}';
					break;
				case '_width':																	
					$templateOptions['single_product_width'] = '{'. $element_name .'[1]}';
					break;
				case '_height':
					$templateOptions['single_product_height'] = '{'. $element_name .'[1]}';
					break;
				case '_sku':
					$templateOptions['single_product_sku'] = '{'. $element_name .'[1]}';								
					//$templateOptions['single_product_parent_id'] = '{parent_id[1]}';
					break;
				case '_sale_price_dates_from':
					$templateOptions['single_sale_price_dates_from'] = '{'. $element_name .'[1]}';
					break;
				case '_sale_price_dates_to':
					$templateOptions['single_sale_price_dates_to'] = '{'. $element_name .'[1]}';
					break;
				case '_sold_individually':
					$templateOptions['product_sold_individually'] = 'xpath';	
					$templateOptions['single_product_sold_individually'] = '{'. $element_name .'[1]}';
					break;
				case '_manage_stock':
					$templateOptions['is_product_manage_stock'] = 'xpath';	
					$templateOptions['single_product_manage_stock'] = '{'. $element_name .'[1]}';
					break;
				case '_stock':
					$templateOptions['single_product_stock_qty'] = '{'. $element_name .'[1]}';
					break;
				case '_upsell_ids':								

					if ($implode_delimiter == "|")
						$templateOptions['single_product_up_sells'] = '[str_replace("|", ",",{' . $element_name . '[1]})]';
					else
						$templateOptions['single_product_up_sells'] = '{' . $element_name . '[1]}';

					break;
				case '_crosssell_ids':								

					if ($implode_delimiter == "|")
						$templateOptions['single_product_cross_sells'] = '[str_replace("|", ",",{' . $element_name . '[1]})]';
					else
						$templateOptions['single_product_cross_sells'] = '{' . $element_name . '[1]}';

					break;
				case '_downloadable_files':
					if ($is_xml_template)
					{
						$templateOptions['single_product_files'] = '{'. $element_name .'Paths[1]}';
						$templateOptions['single_product_files_names'] = '{'. $element_name .'Names[1]}';												
					}
					else
					{
						$templateOptions['single_product_files'] = '{'. $element_name .'paths[1]}';
						$templateOptions['single_product_files_names'] = '{'. $element_name .'names[1]}';	
					}
					break;
				case '_download_limit':
					$templateOptions['single_product_download_limit'] = '{'. $element_name .'[1]}';
					break;
				case '_download_expiry':
					$templateOptions['single_product_download_expiry'] = '{'. $element_name .'[1]}';
					break;
				case '_download_type':
					$templateOptions['single_product_download_type'] = '{'. $element_name .'[1]}';
					break;
				case '_product_url':
					$templateOptions['single_product_url'] = '{'. $element_name .'[1]}';
					break;
				case '_button_text':
					$templateOptions['single_product_button_text'] = '{'. $element_name .'[1]}';
					break;
				case '_tax_status':
					$templateOptions['is_multiple_product_tax_status'] = 'no';
					$templateOptions['single_product_tax_status'] = '{'. $element_name .'[1]}';
					break;
				case '_tax_class':
					$templateOptions['is_multiple_product_tax_class'] = 'no';
					$templateOptions['single_product_tax_class'] = '{'. $element_name .'[1]}';
					break;
				case '_backorders':
					$templateOptions['product_allow_backorders'] = 'xpath';
					$templateOptions['single_product_allow_backorders'] = '{'. $element_name .'[1]}';
					break;
                case '_variation_description':
                    $templateOptions['single_product_variation_description'] = '{'. $element_name .'[1]}';
                    break;
                case '_product_attributes':
                case '_default_attributes':
                    $templateOptions['custom_name'][] = $element_key;
                    $templateOptions['custom_value'][] = '{'. $element_name .'[1]}';
                    $templateOptions['custom_format'][] = 0;
                    break;
				case 'attributes':
						
					global $wp_taxonomies;									

					foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;
						
						if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3)
						{
                            $attribute_name = preg_replace('/[^a-z0-9_]/i', '', str_replace("-", "_", $obj->name));

                            if ($is_xml_template)
                            {
                                $attributeOptions = array(
                                    'attribute_name' => 'AttributeName',
                                    'attribute_value' => 'AttributeValue',
                                    'advanced_in_variations_xpath' => 'AttributeInVariations',
                                    'advanced_is_visible_xpath' => 'AttributeIsVisible',
                                    'advanced_is_taxonomy_xpath' => 'AttributeIsTaxonomy'
                                );

                                foreach ($attributeOptions as $templateKey => $xpathKey){

                                    if ( ! in_array('{'. $xpathKey . $attribute_name .'[1]}', $templateOptions[$templateKey]) ){
                                        $templateOptions[$templateKey][]  = '{'. $xpathKey . $attribute_name .'[1]}';
                                    }
                                    else{
                                        $is_added = false;
                                        $i = 2;
                                        do {
                                            $new_element_name = '{'. $xpathKey . $attribute_name .'['. $i .']}';

                                            if ( ! in_array($new_element_name, $templateOptions[$templateKey]) ) {
                                                $templateOptions[$templateKey][] = $new_element_name;
                                                $is_added = true;
                                            }
                                            $i++;
                                        }
                                        while ( ! $is_added );
                                    }
                                }
                            }
                            else
                            {
                                $attributeOptions = array(
                                    'attribute_name' => 'attributename',
                                    'attribute_value' => 'attributevalue',
                                    'advanced_in_variations_xpath' => 'attributeinvariations',
                                    'advanced_is_visible_xpath' => 'attributeisvisible',
                                    'advanced_is_taxonomy_xpath' => 'attributeistaxonomy'
                                );

                                foreach ($attributeOptions as $templateKey => $xpathKey){

                                    if ( ! in_array('{'. $xpathKey . $attribute_name .'[1]}', $templateOptions[$templateKey]) ){
                                        $templateOptions[$templateKey][]  = '{'. $xpathKey . $attribute_name .'[1]}';
                                    }
                                    else{
                                        $is_added = false;
                                        $i = 2;
                                        do {
                                            $new_element_name = '{'. $xpathKey . $attribute_name .'_'. $i .'[1]}';

                                            if ( ! in_array($new_element_name, $templateOptions[$templateKey]) ) {
                                                $templateOptions[$templateKey][] = $new_element_name;
                                                $is_added = true;
                                            }
                                            $i++;
                                        }
                                        while ( ! $is_added );
                                    }
                                }
                            }
							
							$templateOptions['in_variations'][] = "1";
							$templateOptions['is_visible'][] = "1";
							$templateOptions['is_taxonomy'][] = "1";
							$templateOptions['create_taxonomy_in_not_exists'][] = "1";	
							
							$templateOptions['is_advanced'][] = "1";
							$templateOptions['advanced_is_create_terms'][] = "yes";
							$templateOptions['advanced_in_variations'][] = "xpath";
							$templateOptions['advanced_is_visible'][] = "xpath";
							$templateOptions['advanced_is_taxonomy'][] = "xpath";							

							$attr_list[] = $obj->name;
						}
					}

					global $wpdb;

					$table_prefix = $wpdb->prefix;

					$attributes = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT meta_key FROM {$table_prefix}postmeta 
							WHERE {$table_prefix}postmeta.meta_key LIKE %s AND {$table_prefix}postmeta.meta_key NOT LIKE %s", 'attribute_%', 'attribute_pa_%'));	

					if ( ! empty($attributes))
					{
						foreach ($attributes as $attribute) 
						{
							if ($is_xml_template)
							{
							    if (strpos($attribute->meta_key, "%") === false){
                                    $attribute_name = ucfirst(str_replace('attribute_', '', $attribute->meta_key));
                                }
                                else{
                                    $attribute_name = str_replace('attribute_', '', preg_replace('/[^a-z0-9_]/i', '', $attribute->meta_key));
                                }

								$templateOptions['attribute_name'][]  = '{AttributeName' . $attribute_name .'[1]}';
								$templateOptions['attribute_value'][] = '{AttributeValue' . $attribute_name .'[1]}';
								$templateOptions['advanced_in_variations_xpath'][] = '{AttributeInVariations' . $attribute_name .'[1]}';
								$templateOptions['advanced_is_visible_xpath'][] = '{AttributeIsVisible' . $attribute_name .'[1]}';
								$templateOptions['advanced_is_taxonomy_xpath'][] = '{AttributeIsTaxonomy' . $attribute_name .'[1]}';
							}
							else
							{
								$attributeOptions = array(
								    'attribute_name' => 'attributename',
                                    'attribute_value' => 'attributevalue',
                                    'advanced_in_variations_xpath' => 'attributeinvariations',
                                    'advanced_is_visible_xpath' => 'attributeisvisible',
                                    'advanced_is_taxonomy_xpath' => 'attributeistaxonomy'
                                );

							    $attribute_name = preg_replace('/[^a-z0-9_]/i', '', str_replace('attribute_', '', XmlExportEngine::sanitizeFieldName($attribute->meta_key)));

                                foreach ($attributeOptions as $templateKey => $xpathKey){

                                    if ( ! in_array('{'. $xpathKey . $attribute_name .'[1]}', $templateOptions[$templateKey]) ){
                                        $templateOptions[$templateKey][]  = '{'. $xpathKey . $attribute_name .'[1]}';
                                    }
                                    else{
                                        $is_added = false;
                                        $i = 2;
                                        do {
                                            $new_element_name = '{'. $xpathKey . $attribute_name .'_'. $i .'[1]}';

                                            if ( ! in_array($new_element_name, $templateOptions[$templateKey]) ) {
                                                $templateOptions[$templateKey][] = $new_element_name;
                                                $is_added = true;
                                            }
                                            $i++;
                                        }
                                        while ( ! $is_added );
                                    }
                                }
							}
														
							$templateOptions['in_variations'][] = "1";
							$templateOptions['is_visible'][] = "1";
							$templateOptions['is_taxonomy'][] = "1";										
							$templateOptions['create_taxonomy_in_not_exists'][] = "1";	

							$templateOptions['is_advanced'][] = "1";
							$templateOptions['advanced_is_create_terms'][] = "yes";
							$templateOptions['advanced_in_variations'][] = "xpath";
							$templateOptions['advanced_is_visible'][] = "xpath";
							$templateOptions['advanced_is_taxonomy'][] = "xpath";							

							$attr_list[] = str_replace('attribute_', '', $attribute->meta_key);
						}
					}

					break;
				default:
					# code...
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
