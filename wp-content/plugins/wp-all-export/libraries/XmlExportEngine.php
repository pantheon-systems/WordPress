<?php

if ( ! class_exists('XmlExportEngine') ){

	require_once dirname(__FILE__) . '/XmlExportACF.php';
	require_once dirname(__FILE__) . '/XmlExportWooCommerce.php';
	require_once dirname(__FILE__) . '/XmlExportWooCommerceOrder.php';
	require_once dirname(__FILE__) . '/XmlExportUser.php';
	require_once dirname(__FILE__) . '/XmlExportComment.php';
	require_once dirname(__FILE__) . '/XmlExportTaxonomy.php';

	final class XmlExportEngine
	{		

		const VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION = 1;
		const VARIABLE_PRODUCTS_EXPORT_VARIATION = 2;
		const VARIABLE_PRODUCTS_EXPORT_PARENT = 3;
		
		const VARIATION_USE_PARENT_TITLE = 1;
		const VARIATION_USE_DEFAULT_TITLE = 2;

		/**
		 * Custom XML Loop begin statement
		 * @var string
		 */
		const XML_LOOP_START = '<!-- BEGIN LOOP -->';

		/**
		 * Custom XML Loop end statement
		 * @var string
		 */
		const XML_LOOP_END = '<!-- END LOOP -->';

		const EXPORT_TYPE_GOOLE_MERCHANTS = 'XmlGoogleMerchants';
		const EXPORT_TYPE_XML = 'xml';
		const EXPORT_TYPE_CSV = 'csv';

		public static $acf_export;
		public static $woo_export;	
		public static $woo_order_export;
		public static $woo_coupon_export;
		public static $woo_refund_export;
		public static $user_export;
		public static $comment_export;
		public static $taxonomy_export;

		public static $is_preview = false;

        public static $implode = ',';

		private $post;
		private $_existing_meta_keys = array();
		private $_existing_taxonomies = array();

		private $init_fields = array(			
			array(
				'label' => 'id',
				'name'  => 'ID',
				'type'  => 'id'
			),
			array(
				'label' => 'title',
				'name'  => 'Title',
				'type'  => 'title'
			),
			array(
				'label' => 'content',
				'name'  => 'Content',
				'type'  => 'content'
			)
		);

		public static $default_fields = array( 
			array(
				'label' => 'id', 
				'name'  => 'ID',
				'type'  => 'id'
			),
			array(
				'label' => 'title', 
				'name'  => 'Title',
				'type'  => 'title'
			),
			array(
				'label' => 'content', 
				'name'  => 'Content',
				'type'  => 'content'
			),
			array(
				'label' => 'excerpt', 
				'name'  => 'Excerpt',
				'type'  => 'excerpt'
			),
			array(
				'label' => 'date', 
				'name'  => 'Date',
				'type'  => 'date'
			),
			array(
				'label' => 'post_type', 
				'name'  => 'Post Type',
				'type'  => 'post_type'
			),
			array(
				'label' => 'permalink', 
				'name'  => 'Permalink',
				'type'  => 'permalink'
			)
		);

		private $other_fields = array( 
			array(
				'label' => 'status', 
				'name'  => 'Status',
				'type'  => 'status'
			),
			array(
				'label' => 'author', 
				'name'  => 'Author',
				'type'  => 'author'
			),
			array(
				'label' => 'slug', 
				'name'  => 'Slug',
				'type'  => 'slug'
			),
			array(
				'label' => 'format', 
				'name'  => 'Format',
				'type'  => 'format'
			),
			array(
				'label' => 'template', 
				'name'  => 'Template',
				'type'  => 'template'
			),
			array(
				'label' => 'parent', 
				'name'  => 'Parent',
				'type'  => 'parent'
			),
			array(
				'label' => 'parent_slug',
				'name'  => 'Parent Slug',
				'type'  => 'parent_slug'
			),
			array(
				'label' => 'order', 
				'name'  => 'Order',
				'type'  => 'order'
			),			
			array(
				'label' => 'comment_status', 
				'name'  => 'Comment Status',
				'type'  => 'comment_status'
			),
			array(
				'label' => 'ping_status', 
				'name'  => 'Ping Status',
				'type'  => 'ping_status'
			),
			array(
				'label' => 'post_modified',
				'name'  => 'Post Modified Date',
				'type'  => 'post_modified'
			)
		);	

		private $available_sections = array();		
		private $filter_sections = array();
		
		private $errors;				

		private $available_data = array(
			'acf_groups' => array(),
			'existing_acf_meta_keys' => array(),
			'existing_meta_keys' => array(),
			'init_fields' => array(),
			'default_fields' => array(),
			'other_fields' => array(),
			'woo_data' => array(),
			'existing_attributes' => array(),
			'existing_taxonomies' => array()
		);

		private $filters;

		public static $is_user_export    = false;	
		public static $is_comment_export = false;
		public static $is_taxonomy_export = false;
		public static $post_types    = array();	
		public static $exportOptions = array();
		public static $exportQuery;
		public static $exportID     = false;
		public static $exportRecord = false;
		public static $globalAvailableSections;

		public static $is_auto_generate_enabled = true;

		public function __construct( $post, & $errors = false ){		

			$this->post   = $post;
			$this->errors = $errors;			

			$this->available_sections = array(
				'default' => array(
					'title'   => __("Standard", "wp_all_export_plugin"), 
					'content' => 'default_fields'
				),
				'media' => array(
					'title'   => __("Media", "wp_all_export_plugin"),
					'content' => '',
					'additional' => array(
						'images' => array(
							'title' => __("Images", "wp_all_export_plugin"),
							'meta' => array(								
								array(
									'name'  => 'URL',
									'label' => 'url',
									'type'  => 'image_url',
									'auto'  => 1
								),
								array(
									'name'  => 'Filename',
									'label' => 'filename',
									'type'  => 'image_filename'
								),
								array(
									'name'  => 'Path',
									'label' => 'path',
									'type'  => 'image_path'
								),
								array(
									'name'  => 'ID',
									'label' => 'image_id',
									'type'  => 'image_id'
								),
								array(
									'name'  => 'Title',
									'label' => 'title',
									'type'  => 'image_title',
									'auto'  => 1
								),
								array(
									'name'  => 'Caption',
									'label' => 'caption',
									'type'  => 'image_caption',
									'auto'  => 1
								),
								array(
									'name'  => 'Description',
									'label' => 'description',
									'type'  => 'image_description',
									'auto'  => 1
								),
								array(
									'name'  => 'Alt Text',
									'label' => 'alt',
									'type'  => 'image_alt',
									'auto'  => 1
								),
							)
						),
						'attachments' => array(
							'title' => __("Attachments", "wp_all_export_plugin"),
							'meta' => array(
								array(
									'name'  => 'URL',
									'label' => 'url',
									'type'  => 'attachment_url',
									'auto'  => 1
								),
								array(
									'name'  => 'Filename',
									'label' => 'filename',
									'type'  => 'attachment_filename'
								),
								array(
									'name'  => 'Path',
									'label' => 'path',
									'type'  => 'attachment_path'
								),
								array(
									'name'  => 'ID',
									'label' => 'attachment_id',
									'type'  => 'attachment_id'
								),
								array(
									'name'  => 'Title',
									'label' => 'title',
									'type'  => 'attachment_title'
								),
								array(
									'name'  => 'Caption',
									'label' => 'caption',
									'type'  => 'attachment_caption'
								),
								array(
									'name'  => 'Description',
									'label' => 'description',
									'type'  => 'attachment_description'
								),
								array(
									'name'  => 'Alt Text',
									'label' => 'alt',
									'type'  => 'attachment_alt'
								),
							)
						)
					)
				), 
				'cats' => array(
					'title'   => __("Taxonomies", "wp_all_export_plugin"),
					'content' => 'existing_taxonomies'					
				),
				'cf' => array(
					'title'   => __("Custom Fields", "wp_all_export_plugin"), 
					'content' => 'existing_meta_keys'					
				),
				'other' => array(
					'title'   => __("Other", "wp_all_export_plugin"), 
					'content' => 'other_fields'					
				)
			);					

			$this->filter_sections = array(				
				'author' => array(
					'title'  => __("Author", "wp_all_export_plugin"),
					'fields' => array(
						'user_ID' => 'User ID',
						'user_login' => 'User Login',
						'user_nicename' => 'Nicename',
						'user_email' => 'Email',
						'user_registered' => 'Date Registered (Y-m-d H:i:s)',
						'display_name' => 'Display Name',
						'cf_first_name' => 'First Name',
						'cf_last_name' => 'Last Name',
						'nickname' => 'Nickname',
						'description' => 'User Description',
						'wp_capabilities' => 'User Role'						
					)
				)						
			);

			if ( 'specific' == $this->post['export_type']) 
			{ 

				self::$post_types = ( ! is_array($this->post['cpt']) ) ? array($this->post['cpt']) : $this->post['cpt'];								

				if ( in_array('product', self::$post_types) and ! in_array('product_variation', self::$post_types)) self::$post_types[] = 'product_variation';	

				self::$is_user_export = ( in_array('users', self::$post_types) or in_array('shop_customer', self::$post_types) ) ? true : false;

				self::$is_comment_export = ( in_array('comments', self::$post_types) ) ? true : false;

				self::$is_taxonomy_export = ( in_array('taxonomies', self::$post_types) ) ? true : false;

			}	
			else
			{				
				self::$is_user_export    = ( 'wp_user_query' == $this->post['wp_query_selector'] );
				self::$is_comment_export = ( 'wp_comment_query' == $this->post['wp_query_selector'] );
			}			

			if ( ! self::$is_user_export && ! self::$is_comment_export && ! self::$is_taxonomy_export)
			{
				add_filter("wp_all_export_filters", array( &$this, "filter_export_filters"), 10, 1);

				// When WPML is active and at least one post in the export has a trid
				if (class_exists('SitePress')) 
				{
					self::$default_fields[] = array(
						'label' => 'wpml_trid', 
						'name'  => 'WPML Translation ID',
						'type'  => 'wpml_trid'
					);

					self::$default_fields[] = array(
						'label' => 'wpml_lang', 
						'name'  => 'WPML Language Code',
						'type'  => 'wpml_lang'
					);
				}
			}

			self::$exportOptions = $post;
			
			if ( ! empty(PMXE_Plugin::$session) and PMXE_Plugin::$session->has_session() )
			{
				$filter_args = array(
					'filter_rules_hierarhy' => $this->post['filter_rules_hierarhy'],
					'product_matching_mode' => $this->post['product_matching_mode'],
					'taxonomy_to_export' => empty($this->post['taxonomy_to_export']) ? '' : $this->post['taxonomy_to_export']
				);

				$this->filters = \Wpae\Pro\Filtering\FilteringFactory::getFilterEngine();
				$this->filters->init($filter_args);

				$this->init();						
			}

			if (empty(self::$exportOptions['delimiter'])) self::$exportOptions['delimiter'] = ',';

            self::$implode = (self::$exportOptions['delimiter'] == ',') ? '|' : ',';

            self::$implode = apply_filters('wp_all_export_implode_delimiter', self::$implode, self::$exportID);

            if ( !empty(self::$exportOptions['xml_template_type']) && in_array(self::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ) self::$implode = '#delimiter#';

			self::$acf_export  		 = new XmlExportACF();
			self::$woo_export  		 = new XmlExportWooCommerce();
			self::$user_export 		 = new XmlExportUser();
			self::$comment_export    = new XmlExportComment();
			self::$taxonomy_export   = new XmlExportTaxonomy();
			self::$woo_order_export  = new XmlExportWooCommerceOrder(); 
			self::$woo_coupon_export = new XmlExportWooCommerceCoupon(); 			

		}	

		// [FILTERS]

			/**
			*
			* Filter data for advanced filtering
			*
			*/
			public function filter_export_filters($filters){				
				return array_merge($filters, $this->filter_sections);
			}

		// [\FILTERS]	

		protected function init(){

			PMXE_Plugin::$session->set('is_user_export', self::$is_user_export);	
			PMXE_Plugin::$session->set('is_comment_export', self::$is_comment_export);
			PMXE_Plugin::$session->set('is_taxonomy_export', self::$is_taxonomy_export);
			PMXE_Plugin::$session->save_data();	

			if ('advanced' == $this->post['export_type']) {

				if( "" == $this->post['wp_query'] ){
					$this->errors->add('form-validation', __('WP Query field is required', 'pmxe_plugin'));
				}
				else 
				{
					$this->filters->parse();
									
					PMXE_Plugin::$session->set('whereclause', $this->filters->get('queryWhere'));
					PMXE_Plugin::$session->set('joinclause', $this->filters->get('queryJoin'));
					PMXE_Plugin::$session->set('wp_query', $this->post['wp_query']);
					PMXE_Plugin::$session->save_data();							
				}
			}
			else 
			{						
				$this->filters->parse();
				
				PMXE_Plugin::$session->set('cpt', self::$post_types);
				PMXE_Plugin::$session->set('whereclause', $this->filters->get('queryWhere'));
				PMXE_Plugin::$session->set('joinclause', $this->filters->get('queryJoin'));				
				PMXE_Plugin::$session->save_data();																			
			}

			PMXE_Plugin::$session->save_data();
			
		}		

		public function init_additional_data(){

			self::$woo_order_export->init_additional_data();
			self::$woo_export->init_additional_data();

		}

		public function init_available_data(){

			global $wpdb;
			$table_prefix = $wpdb->prefix;

			// Prepare existing taxonomies
			if ( 'specific' == $this->post['export_type'] and ! self::$is_user_export and ! self::$is_comment_export and ! self::$is_taxonomy_export )
			{ 
				$this->_existing_taxonomies = wp_all_export_get_existing_taxonomies_by_cpt( self::$post_types[0] );								

				$this->_existing_meta_keys = wp_all_export_get_existing_meta_by_cpt( self::$post_types[0] );				
			}	
			if ( 'advanced' == $this->post['export_type'] and ! self::$is_user_export and ! self::$is_comment_export and ! self::$is_taxonomy_export )
			{
				$meta_keys = $wpdb->get_results("SELECT DISTINCT meta_key FROM {$table_prefix}postmeta WHERE {$table_prefix}postmeta.meta_key NOT LIKE '_edit%' LIMIT 500");
				if ( ! empty($meta_keys)){
					$exclude_keys = array('_first_variation_attributes', '_is_first_variation_created');
					foreach ($meta_keys as $meta_key) {
						if ( strpos($meta_key->meta_key, "_tmp") === false && strpos($meta_key->meta_key, "_v_") === false && ! in_array($meta_key->meta_key, $exclude_keys)) 
							$this->_existing_meta_keys[] = $meta_key->meta_key;
					}
				}

				global $wp_taxonomies;	

				foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

					if (strpos($obj->name, "pa_") !== 0 and strlen($obj->name) > 3)
						$this->_existing_taxonomies[] = array(
							'name' => empty($obj->label) ? $obj->name : $obj->label,
							'label' => $obj->name,
							'type' => 'cats'
						);
				}
			}							

			// Prepare existing ACF groups & fields
			self::$acf_export->init($this->_existing_meta_keys);
			
			// Prepare existing WooCommerce data
			self::$woo_export->init($this->_existing_meta_keys);

			// Prepare existing WooCommerce Order data
			self::$woo_order_export->init($this->_existing_meta_keys);

			// Prepare existing WooCommerce Coupon data
			self::$woo_coupon_export->init($this->_existing_meta_keys);			

			// Prepare existing Users data
			self::$user_export->init($this->_existing_meta_keys);

			// Prepare existing Comments data
			self::$comment_export->init($this->_existing_meta_keys);

			// Prepare existing Taxonomy data
			self::$taxonomy_export->init($this->_existing_meta_keys);

			return $this->get_available_data();
		}

		public function get_available_data(){			

			$this->available_data['acf_groups'] 			= self::$acf_export->get('_acf_groups');
			$this->available_data['existing_acf_meta_keys'] = self::$acf_export->get('_existing_acf_meta_keys');
			$this->available_data['existing_meta_keys'] 	= $this->_existing_meta_keys;
			$this->available_data['existing_taxonomies']    = $this->_existing_taxonomies;

			$this->available_data['init_fields']    = apply_filters('wp_all_export_init_fields', $this->init_fields);	
			$this->available_data['default_fields'] = apply_filters('wp_all_export_default_fields', self::$default_fields);
			$this->available_data['other_fields']   = apply_filters('wp_all_export_other_fields', $this->other_fields);

			$this->available_data = apply_filters("wp_all_export_available_data", $this->available_data);;

			return $this->available_data;

		}		

		public function get_fields_options( $field_keys = array() ){

			$fields = array(
				'ids' => array(),
				'cc_label' => array(),
				'cc_php' => array(),
				'cc_code' => array(),
				'cc_sql' => array(),
				'cc_options' => array(),
				'cc_type' => array(),
				'cc_value' => array(),
				'cc_name' => array(),
				'cc_settings' => array()
			);				

			self::$woo_order_export->get_fields_options( $fields, $field_keys );

			$available_sections = apply_filters("wp_all_export_available_sections", $this->available_sections);

			foreach ($available_sections as $slug => $section)
			{
				if ( ! empty($this->available_data[$section['content']]) ):

					foreach ($this->available_data[$section['content']] as $field) 
					{

						$field_key = (is_array($field)) ? $field['name'] : $field;

						if ( ! in_array($field_key, $field_keys) ) continue;

						$fields['ids'][] = 1;
						$fields['cc_label'][] = (is_array($field)) ? $field['label'] : $field;
						$fields['cc_php'][] = '';
						$fields['cc_code'][] = '';
						$fields['cc_sql'][] = '';
						$fields['cc_options'][] = '';
						$fields['cc_type'][] = (is_array($field)) ? $field['type'] : $slug;
						$fields['cc_value'][] = (is_array($field)) ? $field['label'] : $field;
						$fields['cc_name'][] = $field_key;
						$fields['cc_settings'][] = '';
					}
				endif;

				if ( ! empty($section['additional']) )
				{
					foreach ($section['additional'] as $sub_slug => $sub_section) 
					{
						
						foreach ($sub_section['meta'] as $field) {
							$key_to_check = (is_array($field)) ? $field['name'] : $field;

							if ( in_array($sub_slug, array('images', 'attachments')) ){
								$key_to_check = preg_replace("%s$%","",ucfirst($sub_slug)) . ' ' . $key_to_check;															
							}												
							
							if ( ! in_array($key_to_check, $field_keys) ) continue;

							$field_options = ( in_array($sub_slug, array('images', 'attachments')) ) ? esc_attr('{"is_export_featured":true,"is_export_attached":true,"image_separator":"|"}') : '0';

							$fields['ids'][] = 1;
							$fields['cc_label'][] = (is_array($field)) ? $field['label'] : $field;
							$fields['cc_php'][] = '';
							$fields['cc_code'][] = '';
							$fields['cc_sql'][] = '';
							$fields['cc_options'][] = $field_options;
							$fields['cc_type'][] = (is_array($field)) ? $field['type'] : $sub_slug;
							$fields['cc_value'][] = (is_array($field)) ? $field['label'] : $field;
							$fields['cc_name'][] = $key_to_check;
							$fields['cc_settings'][] = '';	
						}																
					}					
				}	
			}

			if ( ! self::$is_comment_export )
			{							
				self::$acf_export->get_fields_options( $fields, $field_keys );
			}

			$sort_fields = array();
			foreach ($field_keys as $i => $field_key){
				foreach ($fields['cc_name'] as $j => $cc_name){
					if (!empty($cc_name) && $cc_name == $field_key){
						$sort_fields['ids'][] = 1;
						$sort_fields['cc_label'][] = $fields['cc_label'][$j];
						$sort_fields['cc_php'][] = $fields['cc_php'][$j];
						$sort_fields['cc_code'][] = $fields['cc_code'][$j];
						$sort_fields['cc_sql'][] = $fields['cc_sql'][$j];
						$sort_fields['cc_options'][] = $fields['cc_options'][$j];
						$sort_fields['cc_type'][] = $fields['cc_type'][$j];
						$sort_fields['cc_value'][] = $fields['cc_value'][$j];
						$sort_fields['cc_name'][] = $fields['cc_name'][$j];
						$sort_fields['cc_settings'][] = $fields['cc_settings'][$j];
						break;
					}
				}
			}

			return $sort_fields;

		}

		public function render(){

			$i = 0;

			ob_start();

			$available_sections = apply_filters("wp_all_export_available_sections", $this->available_sections);
			self::$globalAvailableSections = $available_sections;

			// Render Available WooCommerce Orders Data
			self::$woo_order_export->render($i);

			foreach ($available_sections as $slug => $section)
			{
				if ( ! empty($this->available_data[$section['content']]) or ! empty($section['additional']) ): 
				?>										
				<p class="wpae-available-fields-group"><?php echo $section['title']; ?><span class="wpae-expander">+</span></p>
				<div class="wpae-custom-field">
					<ul>
						<?php if ( ! empty($this->available_data[$section['content']]) ): ?>
						<li>
							<div class="default_column" rel="">
								<label class="wpallexport-element-label"><?php echo __("All", "wp_all_export_plugin") . ' ' . $section['title']; ?></label>															
								<input type="hidden" name="rules[]" value="pmxe_<?php echo $slug; ?>"/>															
							</div>
						</li>
						<?php					
						foreach ($this->available_data[$section['content']] as $field) 
						{
							$field_type = is_array($field) ? $field['type'] : $slug;
							$field_name = is_array($field) ? $field['name'] : $field;

							if ( $field_type == 'cf' && $field_name == '_thumbnail_id' ) continue;

							$is_auto_field = ( ! empty($field['auto']) or self::$is_auto_generate_enabled and ('specific' != $this->post['export_type'] or 'specific' == $this->post['export_type'] and ! in_array(self::$post_types[0], array('product'))));
							
							?>
							<li class="pmxe_<?php echo $slug; ?> <?php if ( $is_auto_field ) echo 'wp_all_export_auto_generate';?>">
								<div class="custom_column" rel="<?php echo ($i + 1);?>">															
									<label class="wpallexport-xml-element"><?php echo (is_array($field)) ? $field['name'] : $field; ?></label>
									<input type="hidden" name="ids[]" value="1"/>
									<input type="hidden" name="cc_label[]" value="<?php echo (is_array($field)) ? $field['label'] : $field; ?>"/>										
									<input type="hidden" name="cc_php[]" value="0"/>										
									<input type="hidden" name="cc_code[]" value=""/>
									<input type="hidden" name="cc_sql[]" value="0"/>
									<input type="hidden" name="cc_options[]" value="0"/>										
									<input type="hidden" name="cc_type[]"  value="<?php echo (is_array($field)) ? $field['type'] : $slug; ?>"/>										
									<input type="hidden" name="cc_value[]" value="<?php echo (is_array($field)) ? $field['label'] : $field; ?>"/>
									<input type="hidden" name="cc_name[]"  value="<?php echo (is_array($field)) ? $field['name'] : $field;?>"/>
									<input type="hidden" name="cc_settings[]"  value="0"/>
								</div>
							</li>
							<?php
							$i++;
						}
						endif;

						if ( ! empty($section['additional']) )
						{
							foreach ($section['additional'] as $sub_slug => $sub_section) 
							{
								?>
								<li class="available_sub_section">
									<p class="wpae-available-fields-group"><?php echo $sub_section['title']; ?><span class="wpae-expander">+</span></p>
									<div class="wpae-custom-field">
									<ul>
										<li>
											<div class="default_column" rel="">								
												<label class="wpallexport-element-label"><?php echo __("All", "wp_all_export_plugin") . ' ' . $sub_section['title']; ?></label>
												<input type="hidden" name="rules[]" value="pmxe_<?php echo $slug;?>_<?php echo $sub_slug;?>"/>
											</div>
										</li>
										<?php
										foreach ($sub_section['meta'] as $field) {
											$is_auto_field = empty($field['auto']) ? false : true;
											$field_options = ( in_array($sub_slug, array('images', 'attachments')) ) ? esc_attr('{"is_export_featured":true,"is_export_attached":true,"image_separator":"|"}') : '0';
											?>
											<li class="pmxe_<?php echo $slug; ?>_<?php echo $sub_slug;?> <?php if ( $is_auto_field ) echo 'wp_all_export_auto_generate';?>">
												<div class="custom_column" rel="<?php echo ($i + 1);?>">
													<label class="wpallexport-xml-element"><?php echo (is_array($field)) ? XmlExportEngine::sanitizeFieldName($field['name']) : $field; ?></label>
													<input type="hidden" name="ids[]" value="1"/>
													<input type="hidden" name="cc_label[]" value="<?php echo (is_array($field)) ? $field['label'] : $field; ?>"/>										
													<input type="hidden" name="cc_php[]" value="0"/>										
													<input type="hidden" name="cc_code[]" value="0"/>
													<input type="hidden" name="cc_sql[]" value="0"/>
													<input type="hidden" name="cc_options[]" value="<?php echo $field_options; ?>"/>										
													<input type="hidden" name="cc_type[]" value="<?php echo (is_array($field)) ? $field['type'] : $sub_slug; ?>"/>
													<input type="hidden" name="cc_value[]" value="<?php echo (is_array($field)) ? $field['label'] : $field; ?>"/>
													<input type="hidden" name="cc_name[]" value="<?php echo (is_array($field)) ? XmlExportEngine::sanitizeFieldName($field['name']) : $field;?>"/>
													<input type="hidden" name="cc_settings[]" value=""/>
												</div>
											</li>
											<?php
											$i++;												
										}																		
										?>
									</ul>
								</li>
								<?php
							}
						}
					?>
					</ul>
				</div>										
				<?php
				endif;							
			}

			if ( ! self::$is_comment_export )
			{			
				// Render Available ACF
				self::$acf_export->render($i);		
			}

			return ob_get_clean();

		}

		public function render_filters(){

			$available_sections = apply_filters("wp_all_export_available_sections", apply_filters('wp_all_export_filters', $this->available_sections) );			

			// Render Filters for WooCommerce Orders
			self::$woo_order_export->render_filters();

			if ( ! empty($available_sections) )
			{
				$exclude = array('wpml_lang', 'wpml_trid');

				foreach ($available_sections as $slug => $section) 
				{											
					if ( ! empty($section['content']) and ! empty($this->available_data[$section['content']]) or ! empty($section['fields'])): 
					?>	

					<optgroup label="<?php echo $section['title']; ?>">
					
						<?php if ( ! empty($section['content']) && ! empty($this->available_data[$section['content']]) ): ?>
						
							<?php foreach ($this->available_data[$section['content']] as $field) : ?>
							
								<?php

								$field_label = is_array($field) ? $field['label'] : $field;
								$field_type  = is_array($field) ? $field['type'] : $slug;
								$field_name  = is_array($field) ? $field['name'] : $field;

								if ( in_array($field_label, $exclude) ) continue;

								switch ($field_type) 
								{
									case 'woo':
										$exclude_fields = array('attributes');
										if ( ! in_array($field_label, $exclude_fields)):											
										?>
										<option value="<?php echo 'cf_' . $field_label; ?>"><?php echo $field_name; ?></option>
										<?php
										endif;
										break;
									case 'cf':									
										?>
										<option value="<?php echo 'cf_' . $field_label; ?>"><?php echo $field_name; ?></option>							
										<?php
										break;
									case 'cats':
									case 'attr':
										?>
										<option value="<?php echo 'tx_' . $field_label; ?>"><?php echo $field_name; ?></option>							
										<?php
										break;
									default:		

										if (self::$is_user_export)
										{
											switch ($field_label) 
											{
												case 'id':
													$field_label = strtoupper($field_label);
													break;
												case 'user_nicename':
													?>
													<option value="user_role"><?php _e('User Role', 'wp_all_export_plugin'); ?></option>
													<?php
													break;																							
											}
										}	
										else
										{
											switch ($field_label) {
												case 'id':
													$field_label = strtoupper($field_label);
													break;
												case 'parent':
												case 'author':
												case 'status':
												case 'title':
												case 'content':
												case 'date':
												case 'excerpt':
													$field_label = 'post_' . $field_label;
													break;
												case 'permalink':
													$field_label = 'guid';
													break;
												case 'slug':
													$field_label = 'post_name';
													break;
												case 'order':
													$field_label = 'menu_order';
													break;
												case 'template':
													$field_label = 'cf__wp_page_template';
													break;
												case 'format':
													$field_label = 'tx_post_format';
													break;
												default:
													# code...
													break;
											}
										}									
										?>
										<option value="<?php echo  $field_label; ?>"><?php echo $field_name; ?></option>
										<?php										
										break;
								}
								?>	

							<?php endforeach; ?>
						
						<?php endif; ?>

						<?php if ( ! empty($section['fields'])): ?>

							<?php foreach ($section['fields'] as $key => $title) : ?>
								
								<option value="<?php echo  $key; ?>"><?php echo $title; ?></option>

							<?php endforeach; ?>

						<?php endif; ?>

					</optgroup>

					<?php
											
					endif;		

					if ( ! empty($section['additional']) )
					{
						foreach ($section['additional'] as $sub_slug => $sub_section) 
						{
							if ( $sub_slug == 'attributes' ) {
								?>
								<optgroup label="<?php echo $sub_section['title']; ?>">
									<?php 
									foreach ($sub_section['meta'] as $field) :
										
										switch ($field['type']) {
											case 'attr':
												?>
												<option value="<?php echo 'tx_' . $field['label']; ?>"><?php echo $field['name']; ?></option>
												<?php
												break;
											case 'cf':
												?>
												<option value="<?php echo 'cf_' . $field['label']; ?>"><?php echo $field['name']; ?></option>
												<?php
												break;
											default:
												# code...
												break;
										}										

									endforeach; 
									?>
								</optgroup>
								<?php
							}
						}
					}					
				}
			}

			if ( ! self::$is_comment_export )
			{	
				// Render Available ACF
				self::$acf_export->render_filters();	
			}

		}		

		public function render_new_field(){

			ob_start();

			$available_sections = apply_filters("wp_all_export_available_sections", $this->available_sections);

			// Render Available WooCommerce Orders Data
			self::$woo_order_export->render_new_field();

			if ( ! empty($available_sections) ):?>
				
				<select class="wp-all-export-chosen-select" name="column_value_type" style="width:350px;">
					
					<?php			
					foreach ($available_sections as $slug => $section) 
					{											
						if ( ! empty($this->available_data[$section['content']]) or ! empty($section['additional']) ): 
						?>			
						<optgroup label="<?php echo $section['title']; ?>">
						
							<?php
							if ( ! empty($this->available_data[$section['content']]) )
							{
								foreach ($this->available_data[$section['content']] as $field) 
								{
									$field_label = is_array($field) ? $field['label'] : $field;
									$field_type = is_array($field) ? $field['type'] : $slug;
									$field_name = is_array($field) ? $field['name'] : $field;
									$field_options = empty  ($field['options']) ? '' : $field['options'];

									if ( $field_type == 'cf' && $field_name == '_thumbnail_id' ) continue;
									?>
									<option 
										value="<?php echo $field_type;?>" 
										label="<?php echo $field_label;?>" 									
										options="<?php echo $field_options; ?>"><?php echo $field_name;?></option>
									<?php								
								}
							}
							?>

						</optgroup>

						<?php

							if ( ! empty($section['additional']) )
							{
								foreach ($section['additional'] as $sub_slug => $sub_section) 
								{
									?>
									<optgroup label="<?php echo $sub_section['title']; ?>">
									
										<?php
										foreach ($sub_section['meta'] as $field) 
										{											
											$field_label   = is_array($field) ? $field['label'] : $field;
											$field_type    = is_array($field) ? $field['type'] : $slug;
											$field_name    = is_array($field) ? $field['name'] : $field;
											$field_options = empty($field['options']) ? '{"is_export_featured":true,"is_export_attached":true,"image_separator":"|"}' : $field['options'];
											?>
											<option 
												value="<?php echo $field_type;?>" 
												label="<?php echo $field_label;?>" 											
												options="<?php echo $field_options; ?>"><?php echo $field_name;?></option>
											<?php										
										}	
										?>
									</optgroup>
									<?php
								}
							}
						endif;							
					}

					if ( ! self::$is_comment_export )
					{	
						// Render Available ACF
						self::$acf_export->render_new_field();	
					}

					?>
					<optgroup label="Advanced">
						<option value="sql" label="sql"><?php _e("SQL Query", "wp_all_export_plugin"); ?></option>					
					</optgroup>
				</select>		
				<?php
			endif;

			return ob_get_clean();

		}

		public function parse_custom_xml_template(){

			preg_match("%". self::XML_LOOP_START ."(.*)". self::XML_LOOP_END ."%", $this->post['custom_xml_template'], $matches);
			$parts = explode(self::XML_LOOP_START, $this->post['custom_xml_template']);
			$loopContent = $parts[1];
			$parts = explode(self::XML_LOOP_END, $loopContent);
			$loopContent = $parts[0];
			$line_numbers = substr_count($loopContent, "\n") +1;

			$result['original_post_loop'] = $loopContent;
			$result['line_numbers'] = $line_numbers;

			$custom_xml_template = str_replace("\n", "", $this->post['custom_xml_template']);
			// retrieve XML header
			preg_match("%(.*)". self::XML_LOOP_START ."%", $custom_xml_template, $matches);					
			$result['custom_xml_template_header'] = empty($matches[1]) ? '' : rtrim($matches[1]);
			// retrieve XML POST LOOP
			preg_match("%". self::XML_LOOP_START ."(.*)". self::XML_LOOP_END ."%", $custom_xml_template, $matches);
			$result['custom_xml_template_loop'] = empty($matches[1]) ? '' : rtrim($matches[1]);
			// retrieve XML footer
			preg_match("%". self::XML_LOOP_END ."(.*)%", $custom_xml_template, $matches);
			$result['custom_xml_template_footer'] = empty($matches[1]) ? '' : $matches[1];

			// Validate Custom XML Template header
			if ( empty($result['custom_xml_template_header']) )
			{
				$this->errors->add('form-validation', __('Missing custom XML template header.', 'wp_all_export_plugin'));
			}
			// Validate Custom XML Template post LOOP
			if ( empty($result['custom_xml_template_loop']) )
			{
				$this->errors->add('form-validation', __('Missing custom XML template post loop.', 'wp_all_export_plugin'));
			}
			// Validate Custom XML Template footer
			if ( empty($result['custom_xml_template_footer']) )
			{
				$this->errors->add('form-validation', __('Missing custom XML template footer.', 'wp_all_export_plugin'));
			}

			if ( ! $this->errors->get_error_codes()) {

				// retrieve all placeholders in the XML loop
				preg_match_all("%(\[[^\]\[]*\])%", $result['custom_xml_template_loop'], $matches); 
				$loop_placeholders = empty($matches) ? array() : $matches[0];

				$field_keys = array();					
				// looking for placeholders e.q. {Post Type}, {Title}
				if ( ! empty($loop_placeholders) ){

					foreach ($loop_placeholders as $snippet) {  
					  preg_match("%\{(.*)\}%", $snippet, $matches);
					  if ( ! empty($matches[1]) ) $field_keys[] = $matches[1];
					}			
				}

				preg_match_all("%(\{[^\}\{]*\})%", $result['custom_xml_template_loop'], $matches); 
				$loop_placeholders = empty($matches) ? array() : $matches[0];

				$field_keys = array();					
				// looking for placeholders e.q. {Post Type}, {Title}
				if ( ! empty($loop_placeholders) ){

					foreach ($loop_placeholders as $snippet) {  
					  preg_match("%\{(.*)\}%", $snippet, $matches);
					  if ( ! empty($matches[1]) and ! in_array($matches[1], $field_keys)) $field_keys[] = $matches[1];
					}			
				}			
					
				if ( ! empty($field_keys)){						
					$result['custom_xml_template_options'] = $this->get_fields_options( $field_keys );						
				}
			}
			return $result;
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

		public static function getProductVariationMode()
		{
			if(!isset(self::$exportOptions['export_variations'])) {
				self::$exportOptions['export_variations'] = self::VARIABLE_PRODUCTS_EXPORT_PARENT_AND_VARIATION;
			}
			
			return apply_filters('wp_all_export_product_variation_mode', self::$exportOptions['export_variations'], self::$exportID);
		}

		public static function getProductVariationTitleMode()
		{
			if(!isset(self::$exportOptions['export_variations_title'])) {
				self::$exportOptions['export_variations_title'] = self::VARIATION_USE_PARENT_TITLE;
			}

			return self::$exportOptions['export_variations_title'];
		}

		public static function sanitizeFieldName($fieldName)
		{
			if (class_exists('XmlExportWooCommerce') && XmlExportWooCommerce::$is_active) {
				return urldecode($fieldName);
			}

			return $fieldName;
		}
	}

}
