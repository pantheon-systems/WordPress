<?php

if ( ! class_exists('XmlExportACF') )
{
	final class XmlExportACF
	{				
		private $_existing_acf_meta_keys = array();

		private $_acf_groups = array();			

		public function __construct() 
		{			
			add_filter("wp_all_export_csv_rows", array( &$this, "filter_csv_rows"), 10, 3);							
		}

		public function init( & $existing_meta_keys = array() ){

			if ( ! class_exists( 'acf' ) ) return;
			
			global $acf;			

			if ($acf and version_compare($acf->settings['version'], '5.0.0') >= 0){

				$saved_acfs = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field-group'));	

				$acfs = acf_local()->groups;																			

				if ( ! empty($acfs) and is_array($acfs)) $this->_acf_groups = $acfs;

			}
			else{

				$this->_acf_groups = apply_filters('acf/get_field_groups', array());	

			}			

			if ( ! empty($saved_acfs) ){
				foreach ($saved_acfs as $key => $obj) {
					if ( ! isset($acfs[$obj->post_name]))
					{
						$this->_acf_groups[] = array(
							'ID' => $obj->ID,
							'title' => $obj->post_title
						);
					}
				}
			}								

			if ( ! empty($this->_acf_groups) ){

				foreach ($this->_acf_groups as $key => $acfObj) 
				{
					if (empty($this->_acf_groups[$key]['ID']) and ! empty($this->_acf_groups[$key]['key']))
					{
						$this->_acf_groups[$key]['ID'] = $acfs[$key]['key'];								
					}
					elseif (empty($this->_acf_groups[$key]['ID']) and ! empty($this->_acf_groups[$key]['id']))
					{
						$this->_acf_groups[$key]['ID'] = $this->_acf_groups[$key]['id'];								
					}
				}

				// get all ACF fields
				if ($acf->settings['version'] and version_compare($acf->settings['version'], '5.0.0') >= 0)
				{		

					foreach ($this->_acf_groups as $key => $acf_obj) {

						if ( is_numeric($acf_obj['ID'])){

							$acf_fields = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field', 'post_parent' => $acf_obj['ID'], 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC'));				

							if ( ! empty($acf_fields) ){

								foreach ($acf_fields as $field) {				

									$fieldData = (!empty($field->post_content)) ? unserialize($field->post_content) : array();			
									
									$fieldData['ID']    = $field->ID;
									$fieldData['id']    = $field->ID;
									$fieldData['label'] = $field->post_title;
									$fieldData['key']   = $field->post_name;					

									if (in_array($fieldData['type'], array('tab'))) continue;

									if (empty($fieldData['name'])) $fieldData['name'] = $field->post_excerpt;

									if ( ! empty($fieldData['name'])){ 
										$this->_existing_acf_meta_keys[] = $fieldData['name'];										
									}
									
									$this->_acf_groups[$key]['fields'][] = $fieldData;
									
								}
							}
						}
						else
						{
							$acf_fields = acf_local()->fields;
							
							if ( ! empty($acf_fields) )
							{
								foreach ($acf_fields as $field_key => $field) 
								{
									if ($field['parent'] == $acf_obj['key'])
									{										
										$fieldData = $field;									
										
										if (empty($fieldData['ID'])) 
										{
											$fieldData['ID'] = $fieldData['id'] = uniqid();
										}

										if ( ! empty($fieldData['name'])){ 
											$this->_existing_acf_meta_keys[] = $fieldData['name'];										
										}
										
										$this->_acf_groups[$key]['fields'][] = $fieldData;
									}
								}
							}			
						}
					}
				}
				else
				{
					foreach ($this->_acf_groups as $key => $acf_obj) {

						$fields = array();

						if (is_numeric($acf_obj['id'])){							

							foreach (get_post_meta($acf_obj['id'], '') as $cur_meta_key => $cur_meta_val)
							{	
								if (strpos($cur_meta_key, 'field_') !== 0) continue;

								$fields[] = (!empty($cur_meta_val[0])) ? unserialize($cur_meta_val[0]) : array();			
														
							}							
						}
						else
						{							
							global $acf_register_field_group;

							if ( ! empty($acf_register_field_group) )
							{
								foreach ($acf_register_field_group as $group) 
								{																
									if ($group['id'] == $acf_obj['ID'])
									{																										
										foreach ($group['fields'] as $field) 
										{																	
											$fields[] = $field;											
										}
									}
								}
							}
						}

						if (count($fields)){

							$sortArray = array();

							foreach($fields as $field){
							    foreach($field as $key2=>$value){
							        if(!isset($sortArray[$key2])){
							            $sortArray[$key2] = array();
							        }
							        $sortArray[$key2][] = $value;
							    }
							}

							$orderby = "order_no"; 

							@array_multisort($sortArray[$orderby],SORT_ASC, $fields); 
							
							foreach ($fields as $field){ 								
								if (in_array($field['type'], array('tab'))) continue;
								$this->_acf_groups[$key]['fields'][] = $field;									
								if ( ! empty($field['name'])) $this->_existing_acf_meta_keys[] = $field['name'];
							}								
						}
					}											
				}

				if ( ! empty($existing_meta_keys)){					
					foreach ($existing_meta_keys as $key => $meta_key) {
						foreach ($this->_existing_acf_meta_keys as $acf_key => $acf_value) {
							if (in_array($meta_key, array($acf_value, "_" . $acf_value))) {
								unset($existing_meta_keys[$key]);
							}
						}						
					}
				}
			}			
		}

		private static $additional_articles = array();

        private static $fc_sub_field_names = array();

		public static function export_acf_field($field_value  = '', $exportOptions, $ID, $pid, &$article, $xmlWriter = false, &$acfs, $element_name = '', $element_name_ns = '', $fieldSnipped = '', $group_id = '', $preview = false, $return_value = false, $is_sub_field = false )
		{
			global $acf;

			$put_to_csv = true;			

			$field_name    = (!empty($exportOptions['cc_label'][$ID])) ? $exportOptions['cc_label'][$ID] : $exportOptions['name'];
			$field_options = (!empty($exportOptions['cc_options'][$ID])) ? unserialize($exportOptions['cc_options'][$ID]) : $exportOptions;
			$field_settings = (!empty($exportOptions['cc_settings'][$ID])) ? json_decode($exportOptions['cc_settings'][$ID], true) : false;

			$is_xml_export = false;

			if ( ! empty($xmlWriter) and XmlExportEngine::$exportOptions['export_to'] == 'xml' and ! in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ){
				$is_xml_export = true;
			}

			$is_custom_xml_export = false;

			if ( XmlExportEngine::$exportOptions['export_to'] == 'xml' and in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom')) ){
				$is_custom_xml_export = true;
			}

			if ($field_options['type'] == 'message'){				
				$field_value = empty($field_options['message']) ? '' : $field_options['message'];				
			}

			if ( ! empty($field_value) ) 
			{						
				$field_value = maybe_unserialize($field_value);

				$implode_delimiter = XmlExportEngine::$implode;

				switch ($field_options['type']) 
				{			
					case 'date_time_picker':
						$format = empty($field_options['return_format']) ? 'Y-m-d H:i:s' : $field_options['return_format'];
						$field_value = date($format, (is_numeric($field_value) ? $field_value : strtotime($field_value)));
						break;		
					case 'date_picker':
						$field_value = date('Ymd', strtotime($field_value));				
						break;		

					case 'file':
					case 'image':
						if (is_numeric($field_value))
						{
							$field_value = wp_get_attachment_url($field_value);
						}
						elseif(is_array($field_value))
						{
							$field_value = empty($field_value['url']) ? '' : $field_value['url'];
						}
						break;	

					case 'gallery':																	
						$v = array();						

						foreach ($field_value as $key => $item) 
						{			
							if (!empty($item['url'])){
								$v[] = $item['url'];											
							}							
							elseif (is_numeric($item)){
								$gallery_item_url = wp_get_attachment_url($item);
								if (!empty($gallery_item_url)){
									$v[] = $gallery_item_url;
								}
							}									
						}
						$field_value = implode($implode_delimiter, $v);

						break;																																										
					case 'location-field':
						$localion_parts = explode("|", $field_value);

						if ($is_xml_export)
						{
							if ( ! empty($localion_parts) ){

								$xmlWriter->beginElement($element_name_ns, $element_name, null);
									$xmlWriter->startElement('address');
										$xmlWriter->writeData($localion_parts[0], 'address');
									$xmlWriter->closeElement();

									if (!empty($localion_parts[1])){
										$coordinates = explode(",", $localion_parts[1]);
										if (!empty($coordinates)){
											$xmlWriter->startElement('lat');
												$xmlWriter->writeData($coordinates[0], 'lat');
											$xmlWriter->closeElement();
											$xmlWriter->startElement('lng');
												$xmlWriter->writeData($coordinates[1], 'lng');
											$xmlWriter->closeElement();
										}
									}
								$xmlWriter->closeElement();

							}
						}
						else
						{
							if ( ! $return_value )
							{
								$acfs[$element_name] = array(
									$element_name . '_address',
									$element_name . '_lat',
									$element_name . '_lng'
								);

								if ( ! empty($localion_parts) )
								{
									$article[$element_name . '_address'] = $localion_parts[0];												
									if (!empty($localion_parts[1]))
									{
										$coordinates = explode(",", $localion_parts[1]);
										if (!empty($coordinates))
										{
											$article[$element_name . '_lat'] = $coordinates[0];							
											$article[$element_name . '_lng'] = $coordinates[1];							
										}
									}					
								}
							}
							else
							{
								if ( ! empty($localion_parts) )
								{
									$return_value = array(
										'address' => $localion_parts[0],										
									);
									if (!empty($localion_parts[1]))
									{
										$coordinates = explode(",", $localion_parts[1]);
										if (!empty($coordinates))
										{
											$return_value['lat'] = $coordinates[0];							
											$return_value['lng'] = $coordinates[1];							
										}
									}
								}
							}
						}
																		
						$put_to_csv = false;
						break;

					case 'paypal_item':										

						if ($is_xml_export)
						{
							$xmlWriter->beginElement($element_name_ns, $element_name, null);
								if ( is_array($field_value) ){
									foreach ($field_value as $key => $value) {
										$xmlWriter->beginElement($element_name_ns, $key, null);
											$xmlWriter->writeData($value, $key);
										$xmlWriter->closeElement();
									}
								}													
							$xmlWriter->closeElement();
						}	
						else
						{
							if ( ! $return_value )
							{
								if ( $is_custom_xml_export ){
									$article[$element_name] = json_encode($field_value);
								}
								else{
									$acfs[$element_name] = array($element_name . '_item_name', $element_name . '_item_description', $element_name . '_price');

									if ( is_array($field_value) )
									{
										foreach ($field_value as $key => $value)
										{
											$article[$element_name . '_' . $key] = $value;
										}
									}
								}
							}
						}																

						$put_to_csv = false;

						break;

					case 'google_map':

						if ($is_xml_export)
						{							
							$xmlWriter->beginElement($element_name_ns, $element_name, null);
								$xmlWriter->startElement('address');
									$xmlWriter->writeData($field_value['address'], 'address');
								$xmlWriter->closeElement();
								$xmlWriter->startElement('lat');
									$xmlWriter->writeData($field_value['lat'], 'lat');
								$xmlWriter->closeElement();
								$xmlWriter->startElement('lng');
									$xmlWriter->writeData($field_value['lng'], 'lng');
								$xmlWriter->closeElement();
							$xmlWriter->closeElement();
						}
						else
						{
							if ( ! $return_value )
							{
								if ( $is_custom_xml_export ){
									$article[$element_name] = json_encode($field_value);
								}
								else{
									$acfs[$element_name] = array($element_name . '_address', $element_name . '_lat', $element_name . '_lng');
									$article[$element_name . '_address'] = $field_value['address'];
									$article[$element_name . '_lat'] = $field_value['lat'];
									$article[$element_name . '_lng'] = $field_value['lng'];
								}
							}														
						}								
						$put_to_csv = false;															

						break;

					case 'acf_cf7':
					case 'gravity_forms_field':			

						if ( ! empty($field_options['multiple']) )
						{
							$field_value = implode($implode_delimiter, $field_value);
						}							
						
						break;											

					case 'page_link':

						if (is_array($field_value))
						{
							$field_value = implode($implode_delimiter, $field_value);
						}								
						
						break;

					case 'post_object':													

						if ( ! empty($field_options['multiple'])){
							$v = array();
							foreach ($field_value as $key => $pid) {														

								if (is_numeric($pid)){
									$entry = get_post($pid);
									if ($entry)
									{
										$v[] = $entry->post_name;
									}
								}
								else{
									$v[] = $pid->post_name;
								}
							}
							$field_value = implode($implode_delimiter, $v);
						}
						else{							
							if (is_numeric($field_value)){
								$entry = get_post($field_value);
								if ($entry)
								{
									$field_value = $entry->post_name;
								}
							}
							else{
								$field_value = $field_value->post_name;
							}
						}

						break;		

					case 'relationship':

						$v = array();
						foreach ($field_value as $key => $pid) {
							$entry = get_post($pid);
							if ($entry)
							{
								$v[] = $entry->post_title;
							}
						}
						$field_value = implode($implode_delimiter, $v);

						break;		

					case 'user':	

						if ( ! empty($field_options['multiple'])){
							$v = array();
							foreach ($field_value as $key => $user) {																												
								if (is_numeric($user)){
									$entry = get_user_by('ID', $user);
									if ($entry)
									{
										$v[] = $entry->user_email;
									}
								}				
								else{
									$v[] = $user['user_email'];
								}										
							}
							$field_value = implode($implode_delimiter, $v);
						}
						else{													
							if (is_numeric($field_value)){
								$entry = get_user_by('ID', $field_value);
								if ($entry)
								{
									$field_value = $entry->user_email;
								}
							}
							else{
								$field_value = $field_value['user_email'];
							}
						}	

						break;	

					case 'taxonomy':

						if ($is_xml_export)
						{
							$xmlWriter->beginElement($element_name_ns, $element_name, null);

								if ( ! in_array($field_options['field_type'], array('radio', 'select'))){						
									foreach ($field_value as $key => $tid) {
										$entry = get_term($tid , $field_options['taxonomy']);
										if ($entry and !is_wp_error($entry))
										{
											$xmlWriter->startElement('term');
												$xmlWriter->writeData($entry->name, 'term');
											$xmlWriter->closeElement();
										}
									}						
								}
								else{
									$entry = get_term($field_value, $field_options['taxonomy']);
									if ($entry)
									{
										$xmlWriter->startElement('term');
											$xmlWriter->writeData($entry->name, 'term');
										$xmlWriter->closeElement();
									}
								}

							$xmlWriter->closeElement();

							$put_to_csv = false;
						}
						else
						{
							if ( ! in_array($field_options['field_type'], array('radio', 'select'))){
								$v = array();
								foreach ($field_value as $key => $tid) {
									$entry = get_term($tid , $field_options['taxonomy']);
									if ($entry and !is_wp_error($entry))
									{
										$v[] = $entry->name;
									}
								}
								$field_value = implode($implode_delimiter, $v);
							}
							else{
								$entry = get_term($field_value, $field_options['taxonomy']);
								if ($entry)
								{
									$field_value = $entry->name;
								}
							}
						}						

						break;

					case 'select':

						if ( ! empty($field_options['multiple']))
						{
							$field_value = implode($implode_delimiter, $field_value);
						}

						break;

					case 'checkbox':		
						
						if ( is_array($field_value) )
						{
							$field_value = implode($implode_delimiter, $field_value);																							
						}

						break;

					case 'clone':

						if ( ! empty($field_options['clone']) ) {
							$values = maybe_unserialize($field_value);

							$sub_fields = array();
							foreach ($field_options['clone'] as $sub_field_key) {

								if (strpos($sub_field_key, 'group_') === 0){
									$acf_groups = get_posts(array(
										'posts_per_page' => 1,
										'post_type' => 'acf-field-group',
										'name' => $sub_field_key,
										'post_status' => 'publish'
									));
									if (!empty($acf_groups)){
										foreach ($acf_groups as $acf_group){
											$sub_fields = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field', 'post_parent' => $acf_group->ID, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC'));
										}
									}
								}
								else{
									$args = array(
										'name' => $sub_field_key,
										'post_type' => 'acf-field',
										'post_status' => 'publish',
										'posts_per_page' => 1
									);
									$my_posts = get_posts($args);
									if ($my_posts) {
										$sub_fields[] = $my_posts[0];
									}
								}
							}
							if ( ! empty($sub_fields) ){

								foreach ($sub_fields as $sub_field){

									$field_value = isset($values[$sub_field->post_excerpt]) ? $values[$sub_field->post_excerpt] : '';

									$sub_field_name = empty($sub_field->post_excerpt) ? str_replace("-","_", sanitize_title($sub_field->post_title)) : $sub_field->post_excerpt;

									$field_options = unserialize($sub_field->post_content);

									$sub_field_value = self::export_acf_field(
										$field_value,
										$field_options,
										false,
										$pid,
										$article,
										$xmlWriter,
										$acfs,
										$is_xml_export ? $sub_field_name : $element_name . '_' . $sub_field_name,
										$element_name_ns,
										'',
										'',
										$preview,
										$is_xml_export ? false : true,
										true
									);

									$acfs[$element_name][] = $element_name . '_' . $sub_field_name;
									$article[$element_name . '_' . $sub_field_name] = ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($sub_field_value))) : $sub_field_value;
								}
							}
						}

						$put_to_csv = false;

						break;
					
					case 'repeater':

						if ($is_xml_export) $xmlWriter->beginElement($element_name_ns, $element_name, null);	

						if( have_rows($field_name, $pid) ): 							

							$rowValues = array();

							$repeater_sub_field_names = array();											
		 										
						    while( have_rows($field_name, $pid) ): 								    		    	
						    	
						    	the_row(); 									    							    

						    	$row = self::acf_get_row();

						    	if ($is_xml_export) $xmlWriter->startElement('row');				

						    	foreach ($row['field']['sub_fields'] as $sub_field) {

						    		if ($acf and version_compare($acf->settings['version'], '5.0.0') >= 0)
						    		{
						    			$v = $row['value'][ $row['i'] ][ $sub_field['key'] ]; 						    			
						    			$cache_slug = "format_value/post_id=".$row['post_id']."/name={$sub_field['name']}";
						    			wp_cache_delete($cache_slug, 'acf');
										if ($is_xml_export) $v = acf_format_value($v, $row['post_id'], $sub_field);
						    		}
						    		else
						    		{
										$v = get_sub_field($sub_field['name']);
						    		}							

						    		if ($preview && ! $is_xml_export){
						    			switch ($sub_field['type']) {
											case 'textarea':
											case 'oembed':
											case 'wysiwyg':
											case 'wp_wysiwyg':
											case 'date_time_picker':
											case 'date_picker':
												$v = preg_replace( "/\r|\n/", "", esc_html($v) );
												break;
											default:
												break;
										}
						    		}		    			
									
									$sub_field['delimiter'] = $implode_delimiter;

									$sub_field_name = empty($sub_field['name']) ? str_replace("-","_", sanitize_title($sub_field['label'])) : $sub_field['name'];

						    		$sub_field_value = self::export_acf_field(
				    					$v, 
				    					$sub_field, 
				    					false, 
				    					$pid, 
				    					$article, 
				    					$xmlWriter, 
				    					$acfs, 
				    					$is_xml_export ? $sub_field_name : $element_name . '_' . $sub_field_name, 
				    					$element_name_ns, 
				    					$fieldSnipped, 
				    					'', 
				    					$preview,
										$is_xml_export ? false : true,
                      					true
				    				);				    				
						    		
						    		if ( ! $is_xml_export )
						    		{						    									    									    			
							    		switch ($sub_field['type']) 
							    		{
							    			case 'google_map':
											case 'paypal_item':
											case 'location-field':
													if ( ! empty($sub_field_value))
													{
														foreach ($sub_field_value as $repeater_key => $repeater_value)
														{
															$rowValues[$sub_field['name']][$repeater_key][] = (is_array($repeater_value)) ? implode($exportOptions['delimiter'], $repeater_value) : $repeater_value;
														}
													}
												break;
							    			case 'repeater':
													if ( ! empty($sub_field_value)){
														foreach ($sub_field_value as $repeater_key => $repeater_value) {
															if (is_array($repeater_value)){
																$rv = array();
																foreach ($repeater_value as $repeater_key2 => $repeater_value2) {
																	$rv[] = (is_array($repeater_value2)) ? implode($exportOptions['delimiter'], $repeater_value2) : $repeater_value2;
																}
																$rowValues[$sub_field_name][$repeater_key][] = implode($exportOptions['delimiter'], $rv);
															}
															else{
																$rowValues[$sub_field_name][$repeater_key][] = $repeater_value;
															}
														}
													}
							    				break;							    
							    			default:
							    				$sub_field_name = empty($sub_field['name']) ? str_replace("-","_", sanitize_title($sub_field['label'])) : $sub_field['name'];
							    				$rowValues[$sub_field_name][] = apply_filters('pmxe_acf_field', pmxe_filter( (is_array($sub_field_value)) ? implode($exportOptions['delimiter'], $sub_field_value) : $sub_field_value, $fieldSnipped), $sub_field_name, $pid);																	    				
							    				break;
							    		}
						    		}						    																						
						    	}						    	

					    		if ($is_xml_export) $xmlWriter->closeElement();				    		    				    					       				    
						        				        				        				        				    
						    endwhile;

						    if ($return_value) return $rowValues;

						    if ( ! $is_xml_export )
					    	{							
					    		$additional_articles = array();

							    foreach ($rowValues as $key => $values) 
							    {			
							    	$is_have_subvalues = array_filter(array_keys($values), 'is_numeric');			   		
							    	
							    	if (empty($is_have_subvalues))
							    	{											  							    	
								    	foreach ($values as $subkey => $subvalue) 
								    	{																	    		
						    				if ( ! in_array($element_name . '_' . $key . '_' . $subkey, $repeater_sub_field_names)) 
						    				{
						    					$repeater_sub_field_names[] = $element_name . '_' . $key . '_' . $subkey;		
						    				}
						    				// Display each repeater row in its own csv line
						    				if ( ! empty($field_settings) and $field_settings['repeater_field_item_per_line'] )
						    				{
						    					$base_value = array_shift($subvalue);

						    					$article[$element_name . '_' . $key . '_' . $subkey] = ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($base_value))) : $base_value;

						    					if ( ! empty($subvalue)) 
						    					{
						    						foreach ($subvalue as $i => $addRowValue) 
							    					{
							    						$additional_articles[$i]['settings'] = $field_settings;
							    						$additional_articles[$i]['content'][$element_name . '_' . $key . '_' . $subkey] = $addRowValue;
							    					}
						    					}						    						
						    				}
						    				else
						    				{
                          						if ($is_custom_xml_export){
													$article[$element_name][$key . '_' . $subkey] = apply_filters('pmxe_acf_field', pmxe_filter(($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars(implode("|", $subvalue)))) : implode("|", $subvalue), $fieldSnipped), $key . '_' . $subkey, $pid);
												}
												else{
													$article[$element_name . '_' . $key . '_' . $subkey] = apply_filters('pmxe_acf_field', pmxe_filter(($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars(implode($implode_delimiter, $subvalue)))) : implode($implode_delimiter, $subvalue), $fieldSnipped), $element_name . '_' . $key . '_' . $subkey, $pid);
												}
						    				}
								    	}

								    }
								    else
								    {
						    			if ( ! in_array($element_name . '_' . $key, $repeater_sub_field_names)) 
						    			{
						    				$repeater_sub_field_names[] = $element_name . '_' . $key;		
						    			}
						    			// Display each repeater row in its own csv line
					    				if ( ! empty($field_settings) and $field_settings['repeater_field_item_per_line'] )
					    				{
					    					$base_value = array_shift($values);

					    					$article[$element_name . '_' . $key] = ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($base_value))) : $base_value;

					    					if ( ! empty($values)) 
					    					{
					    						foreach ($values as $i => $addRowValue) 
						    					{
						    						$additional_articles[$i]['settings'] = $field_settings;
						    						$additional_articles[$i]['content'][$element_name . '_' . $key]	= $addRowValue;
						    					}
					    					}
					    				}
					    				else
					    				{
											if ($is_custom_xml_export){
												$article[$element_name][$key] = apply_filters('pmxe_acf_field', pmxe_filter(($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars(implode("|", $values)))) : implode("|", $values), $fieldSnipped), $key, $pid);
											}
											else{
												$article[$element_name . '_' . $key] = apply_filters('pmxe_acf_field', pmxe_filter(($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars(implode($implode_delimiter, $values)))) : implode($implode_delimiter, $values), $fieldSnipped), $element_name . '_' . $key, $pid);
											}
					    				}
								    }						    							    			
							    }

								if ($is_custom_xml_export){
									$article[$element_name] = serialize($article[$element_name]);
								}

								if ( ! empty($repeater_sub_field_names)) $acfs[$element_name] = $repeater_sub_field_names;

								if ( ! empty($additional_articles) )
								{
									foreach ($additional_articles as $i => $additional_article) {
										self::$additional_articles[] = $additional_article;
									}
								}
							}
						 
						endif; 

						if ($is_xml_export) $xmlWriter->closeElement();												

						$put_to_csv = false;

						break;

					case 'flexible_content':																	

						if ( ! $is_sub_field ) self::$fc_sub_field_names = array();

						if ($is_xml_export) $xmlWriter->beginElement($element_name_ns, $element_name, null);	

						// check if the flexible content field has rows of data
						if( have_rows($field_name, $pid) ):

						 	// loop through the rows of data
						    while ( have_rows($field_name, $pid) ) : the_row();

								$row = self::acf_get_row();

								foreach ($row['field']['layouts'] as $layout) {

									if ($layout['name'] == $row['value'][ $row['i'] ]['acf_fc_layout']){

										if ($is_xml_export) $xmlWriter->startElement(preg_replace('#^\d+#', '', $row['value'][ $row['i'] ]['acf_fc_layout']) . '_' . $row['i']);

								    	foreach ($layout['sub_fields'] as $sub_field) {				    					    		
								    		
								    		$layout_field_name = $element_name . '_' . $layout['name'] . '_' . $row['i'];	

								    		$v = '';

								    		if (isset($row['value'][ $row['i'] ][ $sub_field['key'] ]))
								    		{
								    			$v = $row['value'][ $row['i'] ][ $sub_field['key'] ];

								    			if ($is_xml_export)
								    			{
                                                    // apply filters
                                                    $v = apply_filters( "acf/format_value", $v, $pid, $sub_field );
                                                    $v = apply_filters( "acf/format_value/type={$sub_field['type']}", $v, $pid, $sub_field );
                                                    $v = apply_filters( "acf/format_value/name={$sub_field['_name']}", $v, $pid, $sub_field );
                                                    $v = apply_filters( "acf/format_value/key={$sub_field['key']}", $v, $pid, $sub_field );
								    			}
								    		}

								    		if ($preview && ! $is_xml_export){
								    			switch ($sub_field['type']) {
													case 'textarea':
													case 'oembed':
													case 'wysiwyg':
													case 'wp_wysiwyg':
													case 'date_time_picker':
													case 'date_picker':											
														$v = preg_replace( "/\r|\n/", "", esc_html($v) );														
														break;											
													default:
														break;
												}
								    		}	

								    		$sub_field['delimiter'] = $implode_delimiter;

							    			$sub_field_values = self::export_acf_field(
							    				$v, 
							    				$sub_field, 
							    				false, 
							    				$pid, 
							    				$article, 
							    				$xmlWriter, 
							    				$acfs, 
							    				$is_xml_export ? $sub_field['name'] : $layout_field_name . '_' . $sub_field['name'], 
							    				$element_name_ns, 
							    				$fieldSnipped, 
							    				'', 
							    				$preview, 
							    				$is_xml_export ? false : true,
                                                true
							    			);

								    		if ( ! $is_xml_export ) 						    				
						    				{
						    					switch ($sub_field['type']) 
									    		{
									    			case 'repeater':									    												    			
													
														if ( ! empty($sub_field_values))
														{
															foreach ($sub_field_values as $key => $values) {
														    	$article[$layout_field_name . '_' . $key] =  ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars(implode($implode_delimiter, $values)))) : implode($implode_delimiter, $values);				    	
														    	if ( ! in_array($layout_field_name . '_' . $key, self::$fc_sub_field_names)) self::$fc_sub_field_names[] = $layout_field_name . '_' . $key;
														   }					    																									
														}

									    				break;
                                                    case 'flexible_content':


                                                        break;

									    			default:

									    				$article[$layout_field_name . '_' . $sub_field['name']] = is_array($sub_field_values) ? implode($implode_delimiter, $sub_field_values) : $sub_field_values;

														if ( ! in_array($layout_field_name . '_' . $sub_field['name'], self::$fc_sub_field_names))
															self::$fc_sub_field_names[] = $layout_field_name . '_' . $sub_field['name'];

									    				break;
									    		}
						    				}								    								    		
								    	}
								    	if ($is_xml_export) $xmlWriter->closeElement();							    	
								    }						    					    	
							    }

						    endwhile;				    

						else :

						    // no layouts found

						endif;					

						if ($is_xml_export) $xmlWriter->closeElement();

						$put_to_csv = false;
						
						break;											
					
					default:
						
						break;
				}
			}

			if ($return_value) return $field_value;

            if ( ! empty(self::$fc_sub_field_names)) $acfs[$element_name] = self::$fc_sub_field_names;

			if ($put_to_csv)
			{					
				$val = apply_filters('pmxe_acf_field', pmxe_filter( ( ! empty($field_value) ) ? maybe_serialize($field_value) : '', $fieldSnipped), $field_name, $pid);	

				if ($is_xml_export)
		    	{
		    		$xmlWriter->beginElement($element_name_ns, $element_name, null);
		    			$xmlWriter->writeData($val, $element_name);
					$xmlWriter->closeElement();	    		
		    	}
		    	else
		    	{		    				    			
					// $article[$element_name] = ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val;
					wp_all_export_write_article( $article, $element_name, ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val);
					if ( ! isset($acfs[$element_name]) && ! in_array($field_options['type'], array('repeater', 'clone'))) $acfs[$element_name] = $element_name;
		    	}									
			}
		}
		
		public function filter_csv_rows($articles, $options, $export_id)
		{			
			if ( ! empty(self::$additional_articles) and $options['export_to'] == 'csv')
			{				
				$base_article = $articles[count($articles) - 1];				
									
				if ( ! empty(self::$additional_articles ) )
				{
					foreach (self::$additional_articles as $article) 
					{	
						if ($article['settings']['repeater_field_fill_empty_columns'])
						{
							foreach ($article['content'] as $key => $value) {
								unset($base_article[$key]);				
							}									
							$articles[] = @array_merge($base_article, $article['content']);
						}
						else
						{
							$articles[] = $article['content'];
						}						
					}
					self::$additional_articles = array();
				}				
			}			

			return $articles;
		}

		public function get_fields_options( &$fields, $field_keys = array() ){

			if ( ! empty($this->_acf_groups) )
			{
				foreach ($this->_acf_groups as $key => $group) 
				{
					if ( ! empty($group['fields']))
					{
						foreach ($group['fields'] as $field) 
						{
							$field_key = $field['label'];

							if ( ! in_array($field_key, $field_keys) ) continue;

							$fields['ids'][] = 1;
							$fields['cc_label'][] = $field['name'];
							$fields['cc_php'][] = '';
							$fields['cc_code'][] = '';
							$fields['cc_sql'][] = '';
							$fields['cc_options'][] = serialize(array_merge($field, array('group_id' => ((!empty($group['ID'])) ? $group['ID'] : $group['id']) )));
							$fields['cc_type'][] = 'acf';
							$fields['cc_value'][] = $field['name'];
							$fields['cc_name'][] = $field_key;
							$fields['cc_settings'][] = '';							
						}
					}
				}	
			}
		}

		public function render( & $i ){

			if ( ! empty($this->_acf_groups) )
			{
				?>										
				<p class="wpae-available-fields-group"><?php _e("ACF", "wp_all_export_plugin"); ?><span class="wpae-expander">+</span></p>
				<div class="wp-all-export-acf-wrapper wpae-custom-field">
				<?php

				foreach ($this->_acf_groups as $key => $group) 
				{
					$is_acf_group_visible = false;
					if (!empty($group['location'])){
						foreach ( $group['location'] as $locationRule ){
							$rule = array_shift($locationRule);
							if ( XmlExportEngine::$is_user_export && $rule['param'] == 'user_form'){
								$is_acf_group_visible = true;
								break;
							}
							elseif ( XmlExportEngine::$is_taxonomy_export && $rule['param'] == 'taxonomy'){
								$is_acf_group_visible = true;
								break;
							}
							elseif ( 'specific' == XmlExportEngine::$exportOptions['export_type'] && $rule['param'] == 'post_type'){
								if ( $rule['operator'] == '==' && in_array($rule['value'], XmlExportEngine::$post_types)){
									$is_acf_group_visible = true;
									break;
								}
								elseif ( $rule['operator'] != '==' && ! in_array($rule['value'], XmlExportEngine::$post_types)){
									$is_acf_group_visible = true;
									break;
								}
							}
							elseif( 'advanced' == XmlExportEngine::$exportOptions['export_type']){
								$is_acf_group_visible = true;
								break;
							}
						}
					}
					else{
						$is_acf_group_visible = true;
					}

					if ( ! $is_acf_group_visible ) continue;

					?>										
					<div class="wpae-acf-field">
						<ul>
							<li>
								<div class="default_column" rel="">									
									<label class="wpallexport-element-label"><?php echo $group['title']; ?></label>															
									<input type="hidden" name="rules[]" value="pmxe_acf_<?php echo (!empty($group['ID'])) ? $group['ID'] : $group['id'];?>"/>
								</div>
							</li>
							<?php
							if ( ! empty($group['fields']))
							{
								foreach ($group['fields'] as $field) 
								{
									?>
									<li class="pmxe_acf_<?php echo (!empty($group['ID'])) ? $group['ID'] : $group['id'];?> wp_all_export_auto_generate">
										<div class="custom_column" rel="<?php echo ($i + 1);?>">															
											<label class="wpallexport-xml-element"><?php echo $field['label']; ?></label>
											<input type="hidden" name="ids[]" value="1"/>
											<input type="hidden" name="cc_label[]" value="<?php echo $field['name']; ?>"/>										
											<input type="hidden" name="cc_php[]" value=""/>										
											<input type="hidden" name="cc_code[]" value=""/>
											<input type="hidden" name="cc_sql[]" value=""/>
											<input type="hidden" name="cc_options[]" value="<?php echo esc_html(serialize(array_merge($field, array('group_id' => ((!empty($group['ID'])) ? $group['ID'] : $group['id']) ))));?>"/>
											<input type="hidden" name="cc_type[]" value="acf"/>										
											<input type="hidden" name="cc_value[]" value="<?php echo $field['name']; ?>"/>
											<input type="hidden" name="cc_name[]" value="<?php echo $field['label'];?>"/>
											<input type="hidden" name="cc_settings[]" value=""/>
										</div>
									</li>
									<?php
									$i++;												
								}	
							}											
							?>
						</ul>
					</div>									
					<?php
				}
				?>
				</div>
				<?php
			}
		}

		public function render_new_field(){

			if ( ! empty($this->_acf_groups) )
			{				
				foreach ($this->_acf_groups as $key => $group) 
				{
					?>										
					<optgroup label="<?php _e("ACF", "wp_all_export_plugin"); ?> - <?php echo $group['title']; ?>">					
						<?php
						if ( ! empty($group['fields']))
						{
							foreach ($group['fields'] as $field) 
							{								
								$field_options = esc_html(serialize(array_merge($field, array('group_id' => ((!empty($group['ID'])) ? $group['ID'] : $group['id']) ))));
								?>
								<option 
									value="acf" 
									label="<?php echo $field['name'];?>" 									
									options="<?php echo $field_options; ?>"><?php echo $field['label'];?></option>								
								<?php																		
							}	
						}
						?>
					</optgroup>
					<?php
				}
				?>
				</div>
				<?php
			}
		}

		public function render_filters(){

			if ( ! empty($this->_acf_groups) ){
				?>										
				<optgroup label="<?php _e("ACF", "wp_all_export_plugin"); ?>">				
				<?php
				foreach ($this->_acf_groups as $key => $group) {					
					if ( ! empty($group['fields'])){
						foreach ($group['fields'] as $field) {																											
							?>
							<option value="<?php echo 'cf_' . $field['name']; ?>"><?php echo $field['label']; ?></option>							
							<?php							
						}	
					}																		
				}
				?>
				</optgroup>
				<?php
			}

		}

		public static function prepare_import_template( $exportOptions, &$templateOptions, &$acf_list, $element_name, $field_options)
		{
			$field_tpl_key = $element_name . '[1]';			

			$acf_list[] = '[' . $field_options['name'] . '] ' . $field_options['label'];

			$field_template = false;

			$is_multiple_field_value = false;

			$is_xml_template = $exportOptions['export_to'] == 'xml';

			$xpath_separator = $is_xml_template ? '/' : '_';

			$implode_delimiter = XmlExportEngine::$implode;

			switch ($field_options['type']) 
			{
				case 'text':
				case 'textarea':
				case 'number':
				case 'email':
				case 'password':
				case 'url':
				case 'oembed':
				case 'wysiwyg':
				case 'image':
				case 'file':				
				case 'date_picker':
				case 'color_picker':
				case 'acf_cf7':
				case 'gravity_forms_field':	
				case 'limiter':
				case 'wp_wysiwyg':
				case 'date_time_picker':				
					$field_template = '{' . $field_tpl_key . '}';							
					break;
				case 'gallery':

					$field_template = array(
						'search_in_media' => 1,
						'delim' => $implode_delimiter,
						'gallery' => '{' . $field_tpl_key . '}'
					);

					break;
				case 'relationship':

					$field_template = array(
						'delim' => $implode_delimiter,
						'value' => '{' . $field_tpl_key . '}'
					);

					break;
				case 'post_object':
				case 'page_link':				
				case 'user':

					if ($is_xml_template)
					{						
						$field_template = '{' . $field_tpl_key . '}';						
					}
					else
					{
						$field_tpl_key = str_replace("[1]", "", $field_tpl_key);

						if ($field_options['multiple'])
						{
							if ($implode_delimiter == "|")
								$field_template = '[str_replace("|", ",",{' . $field_tpl_key . '[1]})]';
							else
								$field_template = '{' . $field_tpl_key . '[1]}';
						}
						else
						{
							$field_template = '{' . $field_tpl_key . '[1]}';
						}
					}						

					break;
				case 'select':
				case 'checkbox':
					
					$templateOptions['is_multiple_field_value'][$field_options['key']] = "no";

					if ($is_xml_template)
					{
						if ($implode_delimiter == "|")
							$field_template = '[str_replace("|", ",",{' . $field_tpl_key . '})]';
						else
							$field_template = '{' . $field_tpl_key . '}';
					}
					else
					{				
						$field_tpl_key = str_replace("[1]", "", $field_tpl_key);

						if ($implode_delimiter == "|")
							$field_template = '[str_replace("|", ",",{' . $field_tpl_key . '[1]})]';														
						else
							$field_template = '{' . $field_tpl_key . '[1]}';
					}

					break;
				case 'radio':					
				case 'true_false':

					$templateOptions['is_multiple_field_value'][$field_options['key']] = "no";

					$field_template = '{' . $field_tpl_key . '}';

					break;
				case 'location-field':
				case 'google_map':					
					
					if ( ! $is_xml_template) $field_tpl_key = str_replace("[1]", "", $field_tpl_key);

					$field_template = array(
						'address' 									=> '{' . $field_tpl_key . $xpath_separator . 'address[1]}',
						'address_geocode'   						=> 'address_no_key',
						'address_google_developers_api_key' 		=> '',
						'address_google_for_work_client_id' 		=> '',
						'address_google_for_work_digital_signature' => '',
						'lat' 										=> '{' . $field_tpl_key . $xpath_separator . 'lat[1]}',
						'lng' 										=> '{' . $field_tpl_key . $xpath_separator . 'lng[1]}'
					);														

					break;
				case 'paypal_item':
					
					if ( ! $is_xml_template) $field_tpl_key = str_replace("[1]", "", $field_tpl_key);

					$field_template = array(
						'item_name' 		=> '{' . $field_tpl_key . $xpath_separator . 'item_name[1]}',
						'item_description'  => '{' . $field_tpl_key . $xpath_separator . 'item_description[1]}',
						'price' 			=> '{' . $field_tpl_key . $xpath_separator . 'price[1]}'
					);					

					break;												
				case 'taxonomy':

					$taxonomy_options = array();

					$single_term = new stdClass;
					$single_term->item_id = 1;
					$single_term->parent_id = NULL;
					$single_term->xpath = $is_xml_template ? '{' . $field_tpl_key . '/term[1]}' : '{' . $field_tpl_key . '}';
					$single_term->assign = false;

					if ($implode_delimiter == "|"){
						$single_term->xpath = '[str_replace("|", ",", '. $single_term->xpath .')]';
					}

					$taxonomy_options[] = $single_term;

					$templateOptions['is_multiple_field_value'][$field_options['key']] = "no";

					$field_template = json_encode($taxonomy_options);

					break;

				case 'clone':

					if (!empty($field_options['clone'])) {
						$sub_fields = array();
						foreach ($field_options['clone'] as $sub_field_key) {
							if (strpos($sub_field_key, 'group_') === 0) {
								$acf_groups = get_posts(array(
									'posts_per_page' => 1,
									'post_type' => 'acf-field-group',
									'name' => $sub_field_key,
									'post_status' => 'publish'
								));
								if (!empty($acf_groups)) {
									foreach ($acf_groups as $acf_group) {
										$sub_fields = get_posts(array(
											'posts_per_page' => -1,
											'post_type' => 'acf-field',
											'post_parent' => $acf_group->ID,
											'post_status' => 'publish',
											'orderby' => 'menu_order',
											'order' => 'ASC'
										));
									}
								}
							}
							else {
								$args = array(
									'name' => $sub_field_key,
									'post_type' => 'acf-field',
									'post_status' => 'publish',
									'posts_per_page' => 1
								);
								$my_posts = get_posts($args);
								if ($my_posts) {
									$sub_fields[] = $my_posts[0];
								}
							}
						}
						if ( ! empty($sub_fields) ){

							foreach ($sub_fields as $n => $sub_field){

								$sub_field_options 			= unserialize($sub_field->post_content);
								$sub_field_options['label'] = $sub_field->post_title;
								$sub_field_options['name'] 	= $sub_field->post_excerpt;
								$sub_field_options['ID'] 	= $sub_field->ID;
								$sub_field_options['key'] 	= $sub_field->post_name;

								$sub_field_tpl_key = $is_xml_template ? $sub_field->post_excerpt : $element_name . '_' . strtolower($sub_field->post_excerpt);
								$field_template[$sub_field->post_name] = self::prepare_import_template( $exportOptions, $templateOptions, $acf_list, $sub_field_tpl_key, $sub_field_options );
							}
						}
					}

					break;

				case 'repeater':

					if ($is_xml_template)
					{
						$field_template = array(
							'is_variable' => 'yes',
							'foreach' => '{' . $field_tpl_key . '/row}',
							'rows' => array()
						);					
					}					
					else
					{
						$field_template = array(
							'is_variable' => 'csv',
							'separator' => $implode_delimiter,
							'rows' => array()
						);					
					}

					if (class_exists('acf')){

						global $acf;

						if ($acf and version_compare($acf->settings['version'], '5.0.0') >= 0){
																										
							$sub_fields = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field', 'post_parent' => (( ! empty($field_options['id'])) ? $field_options['id'] : $field_options['ID']), 'post_status' => 'publish'));

							if ( ! empty($sub_fields) ){

								foreach ($sub_fields as $n => $sub_field){

									$sub_field_options 			= unserialize($sub_field->post_content);
									$sub_field_options['label'] = $sub_field->post_title;
									$sub_field_options['name'] 	= $sub_field->post_excerpt;
									$sub_field_options['ID'] 	= $sub_field->ID;
									$sub_field_options['key'] 	= $sub_field->post_name;	

									$sub_field_tpl_key = $is_xml_template ? $sub_field->post_excerpt : $element_name . '_' . strtolower($sub_field->post_excerpt);								
									$field_template['rows']['1'][$sub_field->post_name] = self::prepare_import_template( $exportOptions, $templateOptions, $acf_list, $sub_field_tpl_key, $sub_field_options );

									$templateOptions['is_multiple_field_value'][$field_options['key']]['rows']['1'][$sub_field->post_name] = "no";					

								}
							}

						} 
						else
						{									
							if ( ! empty($field['sub_fields']))
							{										
								foreach ($field['sub_fields'] as $n => $sub_field)
								{ 
									$sub_field_tpl_key = $is_xml_template ? $sub_field['name'] : $element_name . '_' . strtolower($sub_field['name']);			

									$field_template['rows']['1'][$sub_field['key']] = self::prepare_import_template( $exportOptions, $templateOptions, $acf_list, $sub_field_tpl_key, $sub_field );
								
									$templateOptions['is_multiple_field_value'][$field_options['key']]['rows']['1'][$sub_field['key']] = "no";
								}
							}
						} 
					}						

					break;

				case 'flexible_content':

					$field_template = array(						
						'layouts' => array()
					);

					if (class_exists('acf')){

						global $acf;

						if ($acf and version_compare($acf->settings['version'], '5.0.0') >= 0){

							$sub_fields = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field', 'post_parent' => (( ! empty($field_options['id'])) ? $field_options['id'] : $field_options['ID']), 'post_status' => 'publish'));

							if ( ! empty($field_options['layouts']))
							{
								foreach ($field_options['layouts'] as $key => $layout) 
								{
									if ( ! empty($sub_fields) )
									{
										$field_template['layouts'][(string)($key + 1)]['acf_fc_layout'] = $layout['name'];

										foreach ($sub_fields as $n => $sub_field)
										{
											$sub_field_options = unserialize($sub_field->post_content);

											if ($sub_field_options['parent_layout'] == $layout['key'])
											{
												$sub_field_options['label'] = $sub_field->post_title;
												$sub_field_options['name']  = $sub_field->post_excerpt;
												$sub_field_options['ID']    = $sub_field->ID;
												$sub_field_options['key']   = $sub_field->post_name;		

												if ($is_xml_template)
												{
													$sub_field_tpl_key = $field_tpl_key . '/' . $layout['name'] . '_' . $key . '[1]/' . $sub_field->post_excerpt;
												}
												else
												{
													$sub_field_tpl_key =  $element_name . '_' . $layout['name'] . '_' . $key . '_' . strtolower($sub_field->post_excerpt);	
												}																			
												
												$field_template['layouts'][(string)($key + 1)][$sub_field->post_name] = self::prepare_import_template( $exportOptions, $templateOptions, $acf_list, $sub_field_tpl_key, $sub_field_options );

												$templateOptions['is_multiple_field_value'][$field_options['key']]['layouts'][(string)($key + 1)][$sub_field->post_name] = "no";					
											}											
										}
									}
								}
							}
						} 
						else
						{			
							if ( ! empty($field['layouts']))		
							{
								foreach ($field['layouts'] as $key => $layout) 
								{
									if ( ! empty($layout['sub_fields']))
									{								
										$field_template['layouts'][(string)($key + 1)]['acf_fc_layout'] = $layout['key'];		
										
										foreach ($layout['sub_fields'] as $n => $sub_field){ 

											if ($is_xml_template)
											{
												$sub_field_tpl_key = $field_tpl_key . '/' . $layout['name'] . '_' . $key . '[1]/' . $sub_field['name'];
											}
											else
											{
												$sub_field_tpl_key =  $element_name . '_' . $layout['name'] . '_' . $key . '_' . strtolower($sub_field['name']);
											}

											$field_template['layouts'][(string)($key + 1)][$sub_field['key']] = self::prepare_import_template( $exportOptions, $templateOptions, $acf_list, $sub_field_tpl_key, $sub_field );
										
											$templateOptions['is_multiple_field_value'][$field_options['key']]['layouts'][(string)($key + 1)][$sub_field['key']] = "no";
										}
									}
								}
							}																	
						} 
					}

					break;

				default:

					$field_template = '{' . $field_tpl_key . '}';

					break;

			}							
			return $field_template;
		}

		public function auto_generate_export_fields( & $fields ){

			if ( ! empty($this->_acf_groups) )
			{
				foreach ($this->_acf_groups as $key => $group)
				{
					if ( ! empty($group['fields']))
					{
						foreach ($group['fields'] as $field)
						{
							$fields['ids'][] 	    	= 1;
							$fields['cc_label'][]   	= $field['name'];
							$fields['cc_php'][] 	   	= 0;
							$fields['cc_code'][]    	= '';
							$fields['cc_sql'][]     	= '';
							$fields['cc_settings'][]    = '';
							$fields['cc_type'][]    	= 'acf';
							$fields['cc_options'][] 	= serialize(array_merge($field, array('group_id' => ((!empty($group['ID'])) ? $group['ID'] : $group['id']) )));
							$fields['cc_value'][]   	= $field['name'];
							$fields['cc_name'][]    	= $field['label'];
						}
					}
				}
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

	    public static function acf_get_row() {

	    	global $acf;

	    	if ($acf and version_compare($acf->settings['version'], '5.3.6.0') >= 0)
	    	{
	    		return acf_get_loop('active');
	    	}
			// check and return row
			elseif( !empty($GLOBALS['acf_field']) ) {
				
				return end( $GLOBALS['acf_field'] );
				
			}
						
			// return
			return false;
			
		}
	}
}
