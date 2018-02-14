<?php
/**
 * RapidAddon
 *
 * @package     WP All Import RapidAddon
 * @copyright   Copyright (c) 2014, Soflyy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @version 	1.1.0
 */

if (!class_exists('RapidAddon')) {
	
	class RapidAddon {

		public $name;
		public $slug;
		public $fields;
		public $options = array();
		public $accordions = array();
		public $image_sections = array();
		public $import_function;
		public $post_saved_function;
		public $notice_text;
		public $logger = null;
		public $when_to_run = false;
		public $image_options = array(
			'download_images' => 'yes', 
			'download_featured_delim' => ',', 
			'download_featured_image' => '',
			'gallery_featured_image' => '',
			'gallery_featured_delim' => ',',
			'featured_image' => '',
			'featured_delim' => ',', 
			'search_existing_images' => 1,
			'is_featured' => 0,
			'create_draft' => 'no',
			'set_image_meta_title' => 0,
			'image_meta_title_delim' => ',',
			'image_meta_title' => '',
			'set_image_meta_caption' => 0,
			'image_meta_caption_delim' => ',',
			'image_meta_caption' => '',
			'set_image_meta_alt' => 0,
			'image_meta_alt_delim' => ',',
			'image_meta_alt' => '',
			'set_image_meta_description' => 0,
			'image_meta_description_delim' => ',',
			'image_meta_description_delim_logic' => 'separate',
			'image_meta_description' => '',
			'auto_rename_images' => 0,
			'auto_rename_images_suffix' => '',
			'auto_set_extension' => 0,
			'new_extension' => '',
			'do_not_remove_images' => 1,
		);

		protected $isWizard = true;

		function __construct($name, $slug) {

			$this->name = $name;
			$this->slug = $slug;
			if (!empty($_GET['id'])){
				$this->isWizard = false;
			}
		}

		function set_import_function($name) {
			$this->import_function = $name;
		}

		function set_post_saved_function($name) {
			$this->post_saved_function = $name;
		}

		function is_active_addon($post_type = null) {
			
			if ( ! is_plugin_active('wp-all-import-pro/wp-all-import-pro.php') and ! is_plugin_active('wp-all-import/plugin.php') ){
				return false;
			}

			$addon_active = false;

			if ($post_type !== null) {
				if (@in_array($post_type, $this->active_post_types) or empty($this->active_post_types)) {
					$addon_active = true;
				}
			}			

			if ($addon_active){
				
				$current_theme = wp_get_theme();

				$parent_theme = $current_theme->parent();				

				$theme_name = $current_theme->get('Name');
				
				$addon_active = (@in_array($theme_name, $this->active_themes) or empty($this->active_themes)) ? true : false;

				if ( ! $addon_active and $parent_theme ){
					$parent_theme_name = $parent_theme->get('Name');
					$addon_active = (@in_array($parent_theme_name, $this->active_themes) or empty($this->active_themes)) ? true : false;

				}
				
				if ( $addon_active and ! empty($this->active_plugins) ){

					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

					foreach ($this->active_plugins as $plugin) {
						if ( ! is_plugin_active($plugin) ) {
							$addon_active = false;
							break;
						}
					}					
				}

			}

			if ($this->when_to_run == "always") {
				$addon_active = true;
			}

			return apply_filters('rapid_is_active_add_on', $addon_active, $post_type, $this->slug);
		}
		
		/**
		* 
		* Add-On Initialization
		*
		* @param array $conditions - list of supported themes and post types
		*
		*/
		function run($conditions = array()) {

			if (empty($conditions)) {
				$this->when_to_run = "always";
			}

			@$this->active_post_types = ( ! empty($conditions['post_types'])) ? $conditions['post_types'] : array();
			@$this->active_themes = ( ! empty($conditions['themes'])) ? $conditions['themes'] : array();
			@$this->active_plugins = ( ! empty($conditions['plugins'])) ? $conditions['plugins'] : array();			

			add_filter('pmxi_addons', array($this, 'wpai_api_register'));
			add_filter('wp_all_import_addon_parse', array($this, 'wpai_api_parse'));
			add_filter('wp_all_import_addon_import', array($this, 'wpai_api_import'));
			add_filter('wp_all_import_addon_saved_post', array($this, 'wpai_api_post_saved'));
			add_filter('pmxi_options_options', array($this, 'wpai_api_options'));
			add_filter('wp_all_import_image_sections', array($this, 'additional_sections'), 10, 1);
			add_action('pmxi_extend_options_featured',  array($this, 'wpai_api_metabox'), 10, 2);
			add_action('admin_init', array($this, 'admin_notice_ignore'));			

		}

		function parse($data) {
			
			if ( ! $this->is_active_addon($data['import']->options['custom_type'])) return false;

			$parsedData = $this->helper_parse($data, $this->options_array());
			return $parsedData;

		}


		function add_field($field_slug, $field_name, $field_type, $enum_values = null, $tooltip = "", $is_html = true, $default_text = '') {

			$field =  array("name" => $field_name, "type" => $field_type, "enum_values" => $enum_values, "tooltip" => $tooltip, "is_sub_field" => false, "is_main_field" => false, "slug" => $field_slug, "is_html" => $is_html, 'default_text' => $default_text);

			$this->fields[$field_slug] = $field;

			if ( ! empty($enum_values) ){
				foreach ($enum_values as $key => $value) {
					if (is_array($value))
					{
						if ($field['type'] == 'accordion')
						{
							$this->fields[$value['slug']]['is_sub_field'] = true;
						}
						else
						{
							foreach ($value as $n => $param) {							
								if (is_array($param) and ! empty($this->fields[$param['slug']])){
									$this->fields[$param['slug']]['is_sub_field'] = true;								
								}
							}
						}
					}
				}
			}

			return $field;

		}

		function add_acf_field($field){
			$this->fields[$field->post_name] = array(
				'type' => 'acf',
				'field_obj' => $field
			);
		}

		private $acfGroups = array();

		function use_acf_group($acf_group){
			$this->add_text(
				'<div class="postbox acf_postbox default acf_signle_group rad4">
    <h3 class="hndle" style="margin-top:0;"><span>'.$acf_group['title'].'</span></h3>
	    <div class="inside">');
			$acf_fields = get_posts(array('posts_per_page' => -1, 'post_type' => 'acf-field', 'post_parent' => $acf_group['ID'], 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC'));
			if (!empty($acf_fields)){
				foreach ($acf_fields as $field) {
					$this->add_acf_field($field);
				}
			}
			$this->add_text('</div></div>');
			$this->acfGroups[] = $acf_group['ID'];
			add_filter('wp_all_import_acf_is_show_group', array($this, 'acf_is_show_group'), 10, 2);
		}

		function acf_is_show_group($is_show, $acf_group){
			return (in_array($acf_group['ID'], $this->acfGroups)) ? false : true;
		}

		/**
		* 
		* Add an option to WP All Import options list
		*
		* @param string $slug - option name
		* @param string $default_value - default option value
		*
		*/
		function add_option($slug, $default_value = ''){
			$this->options[$slug] = $default_value;
		}

		function options_array() {

			$options_list = array();

			foreach ($this->fields as $field_slug => $field_params) {
				if (in_array($field_params['type'], array('title', 'plain_text', 'acf'))) continue;
				$default_value = '';
				if (!empty($field_params['enum_values'])){
					foreach ($field_params['enum_values'] as $key => $value) {						
						$default_value = $key;
						break;
					}
				}
				$options_list[$field_slug] = $default_value;
			}			

			if ( ! empty($this->options) ){
				foreach ($this->options as $slug => $value) {
					$options_arr[$slug] = $value;
				}
			}

			$options_arr[$this->slug]   = $options_list;
			$options_arr['rapid_addon'] = plugin_basename( __FILE__ );

			return $options_arr;

		}

		function wpai_api_options($all_options) {

			$all_options = $all_options + $this->options_array();

			return $all_options;

		}


		function wpai_api_register($addons) {

			if (empty($addons[$this->slug])) {
				$addons[$this->slug] = 1;
			}

			return $addons;

		}


		function wpai_api_parse($functions) {

			$functions[$this->slug] = array($this, 'parse');
			return $functions;

		}

		function wpai_api_post_saved($functions){
			$functions[$this->slug] = array($this, 'post_saved');
			return $functions;
		}


		function wpai_api_import($functions) {

			$functions[$this->slug] = array($this, 'import');
			return $functions;

		}

		function post_saved( $importData ){

			if (is_callable($this->post_saved_function))
				call_user_func($this->post_saved_function, $importData['pid'], $importData['import'], $importData['logger']);
			
		}

		function import($importData, $parsedData) {

			if (!$this->is_active_addon($importData['post_type'])) {
				return;
			}

			$import_options = $importData['import']['options'][$this->slug];

	//		echo "<pre>";
	//		print_r($import_options);
	//		echo "</pre>";

			if ( ! empty($parsedData) )	{

				$this->logger = $importData['logger'];

				$post_id = $importData['pid'];
				$index = $importData['i'];

				foreach ($this->fields as $field_slug => $field_params) {

					if (in_array($field_params['type'], array('title', 'plain_text'))) continue;

					switch ($field_params['type']) {

						case 'image':
							
							// import the specified image, then set the value of the field to the image ID in the media library

							$image_url_or_path = $parsedData[$field_slug][$index];

							$download = $import_options['download_image'][$field_slug];

							$uploaded_image = PMXI_API::upload_image($post_id, $image_url_or_path, $download, $importData['logger'], true);

							$data[$field_slug] = array(
								"attachment_id" => $uploaded_image,
								"image_url_or_path" => $image_url_or_path,
								"download" => $download
							);

							break;

						case 'file':

							$image_url_or_path = $parsedData[$field_slug][$index];

							$download = $import_options['download_image'][$field_slug];

							$uploaded_file = PMXI_API::upload_image($post_id, $image_url_or_path, $download, $importData['logger'], true, "", "files");

							$data[$field_slug] = array(
								"attachment_id" => $uploaded_file,
								"image_url_or_path" => $image_url_or_path,
								"download" => $download
							);

							break;
						
						default:
							// set the field data to the value of the field after it's been parsed
							$data[$field_slug] = $parsedData[$field_slug][$index];
							break;
					}					

					// apply mapping rules if they exist
					if (!empty($import_options['mapping'][$field_slug])) {
						$mapping_rules = json_decode($import_options['mapping'][$field_slug], true);

						if (!empty($mapping_rules) and is_array($mapping_rules)) {
							foreach ($mapping_rules as $rule_number => $map_to) {
								if (isset($map_to[trim($data[$field_slug])])){
									$data[$field_slug] = trim($map_to[trim($data[$field_slug])]);
									break;
								}
							}
						}
					}
					// --------------------


				}

				call_user_func($this->import_function, $post_id, $data, $importData['import'], $importData['articleData'], $importData['logger']);
			}

		}


		function wpai_api_metabox($post_type, $current_values) {

			if (!$this->is_active_addon($post_type)) {
				return;
			}

			echo $this->helper_metabox_top($this->name);

			$visible_fields = 0;

			foreach ($this->fields as $field_slug => $field_params) {
				if ($field_params['is_sub_field']) continue;
				$visible_fields++;
			}

			$counter = 0;

			foreach ($this->fields as $field_slug => $field_params) {				

				// do not render sub fields
				if ($field_params['is_sub_field']) continue;		

				$counter++;		

				$this->render_field($field_params, $field_slug, $current_values, $visible_fields == $counter);										

				//if ( $field_params['type'] != 'accordion' ) echo "<br />";				

			}

			echo $this->helper_metabox_bottom();

			if ( ! empty($this->image_sections) ){				
				$is_images_section_enabled = apply_filters('wp_all_import_is_images_section_enabled', true, $post_type);						
				foreach ($this->image_sections as $k => $section) {
					$section_options = array();
					foreach ($this->image_options as $slug => $value) {
						$section_options[$section['slug'] . $slug] = $value;
					}										
					if ( ! $is_images_section_enabled and ! $k ){
						$section_options[$section['slug'] . 'is_featured'] = 1;
					}
					PMXI_API::add_additional_images_section($section['title'], $section['slug'], $current_values, '', true, false, $section['type']);
				}
			}

		}		

		function render_field($field_params, $field_slug, $current_values, $in_the_bottom = false){

			if (!isset($current_values[$this->slug][$field_slug])) {
				$current_values[$this->slug][$field_slug] = isset($field_params['default_text']) ? $field_params['default_text'] : '';
			}

			if ($field_params['type'] == 'text') {

				PMXI_API::add_field(
					'simple',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => ( $current_values[$this->slug][$field_slug] == '' && $this->isWizard ) ? $field_params['default_text'] : $current_values[$this->slug][$field_slug]
					)
				);

			} else if ($field_params['type'] == 'textarea') {

				PMXI_API::add_field(
					'textarea',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => ( $current_values[$this->slug][$field_slug] == '' && $this->isWizard ) ? $field_params['default_text'] : $current_values[$this->slug][$field_slug]
					)
				);

			} else if ($field_params['type'] == 'wp_editor') {

				PMXI_API::add_field(
					'wp_editor',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => ( $current_values[$this->slug][$field_slug] == '' && $this->isWizard ) ? $field_params['default_text'] : $current_values[$this->slug][$field_slug]
					)
				);

			} else if ($field_params['type'] == 'image' or $field_params['type'] == 'file') {
				
				if (!isset($current_values[$this->slug]['download_image'][$field_slug])) { $current_values[$this->slug]['download_image'][$field_slug] = ''; }

				PMXI_API::add_field(
					$field_params['type'],
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug],
						'download_image' => $current_values[$this->slug]['download_image'][$field_slug],
						'field_key' => $field_slug,
						'addon_prefix' => $this->slug

					)
				);

			} else if ($field_params['type'] == 'radio') {					
				
				if (!isset($current_values[$this->slug]['mapping'][$field_slug])) { $current_values[$this->slug]['mapping'][$field_slug] = array(); }
				if (!isset($current_values[$this->slug]['xpaths'][$field_slug])) { $current_values[$this->slug]['xpaths'][$field_slug] = ''; }

				PMXI_API::add_field(
					'enum',
					$field_params['name'],
					array(
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",
						'field_value' => $current_values[$this->slug][$field_slug],
						'enum_values' => $field_params['enum_values'],
						'mapping' => true,
						'field_key' => $field_slug,
						'mapping_rules' => $current_values[$this->slug]['mapping'][$field_slug],
						'xpath' => $current_values[$this->slug]['xpaths'][$field_slug],
						'addon_prefix' => $this->slug,
						'sub_fields' => $this->get_sub_fields($field_params, $field_slug, $current_values)
					)
				);

			} else if($field_params['type'] == 'accordion') {

				PMXI_API::add_field(
					'accordion',
					$field_params['name'],
					array(						
						'tooltip' => $field_params['tooltip'],
						'field_name' => $this->slug."[".$field_slug."]",																
						'field_key' => $field_slug,								
						'addon_prefix' => $this->slug,
						'sub_fields' => $this->get_sub_fields($field_params, $field_slug, $current_values),
						'in_the_bottom' => $in_the_bottom						
					)
				);

			} else if($field_params['type'] == 'acf') {
				$fieldData = (!empty($field_params['field_obj']->post_content)) ? unserialize($field_params['field_obj']->post_content) : array();
				$fieldData['ID']    = $field_params['field_obj']->ID;
				$fieldData['id']    = $field_params['field_obj']->ID;
				$fieldData['label'] = $field_params['field_obj']->post_title;
				$fieldData['key']   = $field_params['field_obj']->post_name;
				if (empty($fieldData['name'])) $fieldData['name'] = $field_params['field_obj']->post_excerpt;
				if (function_exists('pmai_render_field')) {
					echo pmai_render_field($fieldData, ( ! empty($current_values) ) ? $current_values : array() );
				}
			} else if($field_params['type'] == 'title'){
				?>
				<h4 class="wpallimport-add-on-options-title"><?php _e($field_params['name'], 'wp_all_import_plugin'); ?><?php if ( ! empty($field_params['tooltip'])): ?><a href="#help" class="wpallimport-help" title="<?php echo $field_params['tooltip']; ?>" style="position:relative; top: -1px;">?</a><?php endif; ?></h4>				
				<?php

			} else if($field_params['type'] == 'plain_text'){
				if ($field_params['is_html']):					
					echo $field_params['name'];				
				else:
					?>
					<p style="margin: 0 0 12px 0;"><?php echo $field_params['name'];?></p>
					<?php
				endif;
			}


		}
		/**
		*
		* Helper function for nested radio fields
		*
		*/
		function get_sub_fields($field_params, $field_slug, $current_values){
			$sub_fields = array();	
			if ( ! empty($field_params['enum_values']) ){										
				foreach ($field_params['enum_values'] as $key => $value) {					
					$sub_fields[$key] = array();	
					if (is_array($value)){
						if ($field_params['type'] == 'accordion'){								
							$sub_fields[$key][] = $this->convert_field($value, $current_values);
						}
						else
						{
							foreach ($value as $k => $sub_field) {								
								if (is_array($sub_field) and ! empty($this->fields[$sub_field['slug']]))
								{									
									$sub_fields[$key][] = $this->convert_field($sub_field, $current_values);
								}								
							}
						}
					}
				}
			}
			return $sub_fields;
		}			

		function convert_field($sub_field, $current_values){
			$field = array();
			if (!isset($current_values[$this->slug][$sub_field['slug']])) {
				$current_values[$this->slug][$sub_field['slug']] = isset($sub_field['default_text']) ? $sub_field['default_text'] : '';
			}
			switch ($this->fields[$sub_field['slug']]['type']) {
				case 'text':
					$field = array(
						'type'   => 'simple',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => ($current_values[$this->slug][$sub_field['slug']] == '' && $this->isWizard) ? $sub_field['default_text'] : $current_values[$this->slug][$sub_field['slug']],
							'is_main_field' => $sub_field['is_main_field']
						)
					);
					break;
				case 'textarea':
					$field = array(
						'type'   => 'textarea',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => ($current_values[$this->slug][$sub_field['slug']] == '' && $this->isWizard) ? $sub_field['default_text'] : $current_values[$this->slug][$sub_field['slug']],
							'is_main_field' => $sub_field['is_main_field']
						)
					);
					break;
				case 'wp_editor':
					$field = array(
						'type'   => 'wp_editor',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => ($current_values[$this->slug][$sub_field['slug']] == '' && $this->isWizard) ? $sub_field['default_text'] : $current_values[$this->slug][$sub_field['slug']],
							'is_main_field' => $sub_field['is_main_field']
						)
					);
					break;
				case 'image':
					$field = array(
						'type'   => 'image',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => $current_values[$this->slug][$sub_field['slug']],
							'download_image' => $current_values[$this->slug]['download_image'][$sub_field['slug']],
							'field_key' => $sub_field['slug'],
							'addon_prefix' => $this->slug,
							'is_main_field' => $sub_field['is_main_field']
						)
					);
				case 'file':
					$field = array(
						'type'   => 'file',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => $current_values[$this->slug][$sub_field['slug']],
							'download_image' => $current_values[$this->slug]['download_image'][$sub_field['slug']],
							'field_key' => $sub_field['slug'],
							'addon_prefix' => $this->slug,
							'is_main_field' => $sub_field['is_main_field']
						)
					);
					break;
				case 'radio':
					$field = array(
						'type'   => 'enum',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",
							'field_value' => $current_values[$this->slug][$sub_field['slug']],
							'enum_values' => $this->fields[$sub_field['slug']]['enum_values'],
							'mapping' => true,
							'field_key' => $sub_field['slug'],
							'mapping_rules' => $current_values[$this->slug]['mapping'][$sub_field['slug']],
							'xpath' => $current_values[$this->slug]['xpaths'][$sub_field['slug']],
							'addon_prefix' => $this->slug,
							'sub_fields' => $this->get_sub_fields($this->fields[$sub_field['slug']], $sub_field['slug'], $current_values),
							'is_main_field' => $sub_field['is_main_field']
						)
					);
					break;
				case 'accordion':
					$field = array(
						'type'   => 'accordion',
						'label'  => $this->fields[$sub_field['slug']]['name'],
						'params' => array(
							'tooltip' => $this->fields[$sub_field['slug']]['tooltip'],
							'field_name' => $this->slug."[".$sub_field['slug']."]",																
							'field_key' => $sub_field['slug'],								
							'addon_prefix' => $this->slug,
							'sub_fields' => $this->get_sub_fields($this->fields[$sub_field['slug']], $sub_field['slug'], $current_values),
							'in_the_bottom' => false
						)
					);						
					break;
				default:
					# code...
					break;
			}
			return $field;
		}				

		/**
		* 
		* Add accordion options
		*
		*
		*/
		function add_options( $main_field = false, $title = '', $fields = array() ){
			
			if ( ! empty($fields) )
			{				
				
				if ($main_field){

					$main_field['is_main_field'] = true;
					$fields[] = $main_field;

				}

				return $this->add_field('accordion_' . $fields[0]['slug'], $title, 'accordion', $fields);							
			
			}

		}			

		function add_title($title = '', $tooltip = ''){

			if (empty($title)) return;

			return $this->add_field(sanitize_key($title) . time(), $title, 'title', null, $tooltip);			

		}		

		function add_text($text = '', $is_html = false){

			if (empty($text)) return;

			return $this->add_field(sanitize_key($text) . time() . uniqid() . count($this->fields), $text, 'plain_text', null, "", $is_html);			

		}			

		function helper_metabox_top($name) {

			return '
			<style type="text/css">
				.wpallimport-plugin .wpallimport-addon div.input {
					margin-bottom: 15px;
				}
				.wpallimport-plugin .wpallimport-addon .custom-params tr td.action{
					width: auto !important;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-custom-fields-actions{
					right:0 !important;
				}
				.wpallimport-plugin .wpallimport-addon table tr td.wpallimport-enum-input-wrapper{
					width: 80%;
				}
				.wpallimport-plugin .wpallimport-addon table tr td.wpallimport-enum-input-wrapper input{
					width: 100%;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-custom-fields-actions{
					float: right;	
					right: 30px;
					position: relative;				
					border: 1px solid #ddd;
					margin-bottom: 10px;
				}
				
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options {
					margin-bottom: 15px;				
					margin-top: -16px;	
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-content-section{
					padding-bottom: 8px;
					margin:0; 
					border: none;
					padding-top: 1px;
					background: #f1f2f2;				
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-collapsed-header{
					padding-left: 13px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options .wpallimport-collapsed-header h3{
					font-size: 14px;
					margin: 6px 0;
				}

				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width{
					bottom: -40px;
					margin-bottom: 0;
					margin-left: -25px;
					margin-right: -25px;
					position: relative;						
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width .wpallimport-content-section{					
					margin:0; 					
					border-top:1px solid #ddd; 
					border-bottom: none; 
					border-right: none; 
					border-left: none; 
					background: #f1f2f2;									
				}					
				.wpallimport-plugin .wpallimport-addon .wpallimport-sub-options-full-width .wpallimport-collapsed-header h3{					
					margin: 14px 0;
				}

				.wpallimport-plugin .wpallimport-addon .wpallimport-dependent-options{
					margin-left: 1px;
					margin-right: -1px;					
				}		
				.wpallimport-plugin .wpallimport-addon .wpallimport-dependent-options .wpallimport-content-section{
					border: 1px solid #ddd;
					border-top: none;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-bottom{
					margin-left: -25px; 
					margin-right: -25px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-not-bottom{
					margin: 25px -1px 25px 1px;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-full-with-not-bottom .wpallimport-content-section{
					border: 1px solid #ddd;
				}
				.wpallimport-plugin .wpallimport-addon .wpallimport-add-on-options-title{
					font-size: 14px;
  					margin: 45px 0 15px 0;
				}
			</style>
			<div class="wpallimport-collapsed wpallimport-section wpallimport-addon '.$this->slug.' closed">
				<div class="wpallimport-content-section">
					<div class="wpallimport-collapsed-header">
						<h3>'.__($name,'pmxi_plugin').'</h3>	
					</div>
					<div class="wpallimport-collapsed-content" style="padding: 0;">
						<div class="wpallimport-collapsed-content-inner">
							<table class="form-table" style="max-width:none;">
								<tr>
									<td colspan="3">';
		}

		function helper_metabox_bottom() {

			return '				</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>';

		}

		/**
		*
		* simply add an additional section for attachments
		*
		*/
		function import_files( $slug, $title ){
			$this->import_images( $slug, $title, 'files');
		}

		/**
		*
		* simply add an additional section 
		*
		*/
		function import_images( $slug, $title, $type = 'images' ){
			
			if ( empty($title) or empty($slug) ) return;

			$section_slug = 'pmxi_' . $slug; 

			$this->image_sections[] = array(
				'title' => $title,
				'slug'  => $section_slug,
				'type'  => $type
			);			
			
			foreach ($this->image_options as $option_slug => $value) {
				$this->add_option($section_slug . $option_slug, $value);
			}

			if (count($this->image_sections) > 1){
				add_filter('wp_all_import_is_show_add_new_images', array($this, 'filter_is_show_add_new_images'), 10, 2);
			}

			add_filter('wp_all_import_is_allow_import_images', array($this, 'is_allow_import_images'), 10, 2);			
			
			if (function_exists($slug)) add_action( $section_slug, $slug, 10, 4);
		}			
			/**
			*
			* filter to allow import images for free edition of WP All Import
			*
			*/
			function is_allow_import_images($is_allow, $post_type){
				return ($this->is_active_addon($post_type)) ? true : $is_allow;
			}

		/**
		*
		* filter to control additional images sections
		*
		*/
		function additional_sections($sections){
			if ( ! empty($this->image_sections) ){
				foreach ($this->image_sections as $add_section) {
					$sections[] = $add_section;
				}
			}
			
			return $sections;
		}
			/**
			*
			* remove the 'Don't touch existing images, append new images' when more than one image section is in use.
			*
			*/
			function filter_is_show_add_new_images($is_show, $post_type){
				return ($this->is_active_addon($post_type)) ? false : $is_show;
			}

		/**
		*
		* disable the default images section
		*
		*/		
		function disable_default_images($post_type = false){
									
			add_filter('wp_all_import_is_images_section_enabled', array($this, 'is_enable_default_images_section'), 10, 2);

		}
			function is_enable_default_images_section($is_enabled, $post_type){						
				
				return ($this->is_active_addon($post_type)) ? false : true;
								
			}

		function helper_parse($parsingData, $options) {

			extract($parsingData);

			$data = array(); // parsed data

			if ( ! empty($import->options[$this->slug])){

				$this->logger = $parsingData['logger'];

				$cxpath = $xpath_prefix . $import->xpath;

				$tmp_files = array();

				foreach ($options[$this->slug] as $option_name => $option_value) {					
					if ( isset($import->options[$this->slug][$option_name]) and $import->options[$this->slug][$option_name] != '') {						
						if ($import->options[$this->slug][$option_name] == "xpath") {
							if ($import->options[$this->slug]['xpaths'][$option_name] == ""){
								$count and $this->data[$option_name] = array_fill(0, $count, "");
							} else {
								$data[$option_name] = XmlImportParser::factory($xml, $cxpath, (string) $import->options[$this->slug]['xpaths'][$option_name], $file)->parse($records);
								$tmp_files[] = $file;						
							}
						} 
						else {							
							$data[$option_name] = XmlImportParser::factory($xml, $cxpath, (string) $import->options[$this->slug][$option_name], $file)->parse();
							$tmp_files[] = $file;
						}


					} else {
						$data[$option_name] = array_fill(0, $count, "");
					}

				}

				foreach ($tmp_files as $file) { // remove all temporary files created
					unlink($file);
				}

			}

			return $data;
		}


		function can_update_meta($meta_key, $import_options) {

			//echo "<pre>";
			//print_r($import_options['options']);
			//echo "</pre>";
			
			$import_options = $import_options['options'];

			if ($import_options['update_all_data'] == 'yes') return true;

			if ( ! $import_options['is_update_custom_fields'] ) return false;			

			if ($import_options['update_custom_fields_logic'] == "full_update") return true;
			if ($import_options['update_custom_fields_logic'] == "only" and ! empty($import_options['custom_fields_list']) and is_array($import_options['custom_fields_list']) and in_array($meta_key, $import_options['custom_fields_list']) ) return true;
			if ($import_options['update_custom_fields_logic'] == "all_except" and ( empty($import_options['custom_fields_list']) or ! in_array($meta_key, $import_options['custom_fields_list']) )) return true;

			return false;

		}

		function can_update_taxonomy($tax_name, $import_options) {

			//echo "<pre>";
			//print_r($import_options['options']);
			//echo "</pre>";
			
			$import_options = $import_options['options'];

			if ($import_options['update_all_data'] == 'yes') return true;

			if ( ! $import_options['is_update_categories'] ) return false;			

			if ($import_options['update_categories_logic'] == "full_update") return true;
			if ($import_options['update_categories_logic'] == "only" and ! empty($import_options['taxonomies_list']) and is_array($import_options['taxonomies_list']) and in_array($tax_name, $import_options['taxonomies_list']) ) return true;
			if ($import_options['update_categories_logic'] == "all_except" and ( empty($import_options['taxonomies_list']) or ! in_array($tax_name, $import_options['taxonomies_list']) )) return true;

			return false;

		}

		function can_update_image($import_options) {

			$import_options = $import_options['options'];

			if ($import_options['update_all_data'] == 'yes') return true;

			if (!$import_options['is_update_images']) return false;			

			if ($import_options['is_update_images']) return true;			

			return false;
		}


		function admin_notice_ignore() {
			if (isset($_GET[$this->slug.'_ignore']) && '0' == $_GET[$this->slug.'_ignore'] ) {
				update_option($this->slug.'_ignore', 'true');
			}
		}

		function display_admin_notice() {


			if ($this->notice_text) {
				$notice_text = $this->notice_text;
			} else {
				$notice_text = $this->name.' requires WP All Import <a href="http://www.wpallimport.com/" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>.';
			}

			if (!get_option(sanitize_key($this->slug).'_notice_ignore')) {

				?>

	    		<div class="error notice is-dismissible wpallimport-dismissible" style="margin-top: 10px;" rel="<?php echo sanitize_key($this->slug); ?>">
	    		    <p><?php _e(
		    		    	sprintf(
	    			    		$notice_text,
	    			    		'?'.$this->slug.'_ignore=0'
	    			    	), 
	    		    		'rapid_addon_'.$this->slug
	    		    	); ?></p>
			    </div>

				<?php

			}

		}

		/*
		*
		* $conditions - array('themes' => array('Realia'), 'plugins' => array('plugin-directory/plugin-file.php', 'plugin-directory2/plugin-file.php')) 
		*
		*/
		function admin_notice($notice_text = '', $conditions = array()) {

			$is_show_notice = false;

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( ! is_plugin_active('wp-all-import-pro/wp-all-import-pro.php') and ! is_plugin_active('wp-all-import/plugin.php') ){
				$is_show_notice = true;
			}

			// Supported Themes
			if ( ! $is_show_notice and ! empty($conditions['themes']) ){

				$themeInfo    = wp_get_theme();
				$parentInfo = $themeInfo->parent();				
				$currentTheme = $themeInfo->get('Name');
				
				$is_show_notice = in_array($currentTheme, $conditions['themes']) ? false : true;				

				if ( $is_show_notice and $parentInfo ){
					$parent_theme = $parentInfo->get('Name');
					$is_show_notice = in_array($parent_theme, $conditions['themes']) ? false : true;					
				}

			}			

			// Required Plugins
			if ( ! $is_show_notice and ! empty($conditions['plugins']) ){				

				$requires_counter = 0;
				foreach ($conditions['plugins'] as $plugin) {
					if ( is_plugin_active($plugin) ) $requires_counter++;
				}

				if ($requires_counter != count($conditions['plugins'])){ 					
					$is_show_notice = true;			
				}

			}

			if ( $is_show_notice ){

				if ( $notice_text != '' ) {
					$this->notice_text = $notice_text;
				}

				add_action('admin_notices', array($this, 'display_admin_notice'));
			}

		}

		function log( $m = false){		

			$m and $this->logger and call_user_func($this->logger, $m);

		}
	}	

}

