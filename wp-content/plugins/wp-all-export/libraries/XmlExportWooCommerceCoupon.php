<?php

if ( ! class_exists('XmlExportWooCommerceCoupon') )
{
	final class XmlExportWooCommerceCoupon
	{		
		public static $is_active = true;

		public $init_fields = array(			
			array(
				'name'    => 'Coupon ID',
				'type'    => 'id',
				'options' => '',
				'label'   => 'id'
			),
			array(
				'name'    => 'Coupon Code',
				'type'    => 'title',
				'options' => '',
				'label'   => 'title'
			),
			array(
				'name'    => 'Expiry Date',
				'type'    => 'cf',
				'options' => '',
				'label'   => 'expiry_date'
			)					
		);		

		public $default_fields = array( 
			array(
				'name'    => 'Coupon ID',
				'type'    => 'id',				
				'label'   => 'id'				
			),
			array(
				'name'    => 'Coupon Code',
				'type'    => 'title',				
				'label'   => 'title'				
			),
			array(
				'name'    => 'Coupon Description',
				'type'    => 'excerpt',				
				'label'   => 'excerpt'				
			),
			array(
				'name'    => 'Discount Type',
				'type'    => 'cf',				
				'label'   => 'discount_type'				
			),
			array(
				'name'    => 'Coupon Amount',
				'type'    => 'cf',				
				'label'   => 'coupon_amount'				
			),
			array(
				'name'    => 'Expiry Date',
				'type'    => 'cf',				
				'label'   => 'expiry_date'				
			),	
			array(
				'name'    => 'Free Shipping',
				'type'    => 'cf',				
				'label'   => 'free_shipping'				
			),	
			array(
				'name'    => 'Exclude Sale Items',
				'type'    => 'cf',				
				'label'   => 'exclude_sale_items'				
			)
		);
		
		public $other_fields = array( 
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
				'label' => 'date', 
				'name'  => 'Date',
				'type'  => 'date'				
			),
			array(
				'label' => 'post_type', 
				'name'  => 'Post Type',
				'type'  => 'post_type'				
			)
		);


		private $_coupon_data = array();

		public function __construct()
		{
			if ( ! class_exists('WooCommerce') 
					or ( XmlExportEngine::$exportOptions['export_type'] == 'specific' and ! in_array('shop_coupon', XmlExportEngine::$post_types) ) 
						or ( XmlExportEngine::$exportOptions['export_type'] == 'advanced' and strpos(XmlExportEngine::$exportOptions['wp_query'], 'shop_coupon') === false ) ) {
				self::$is_active = false;
				return;			
			}			

			self::$is_active = true;

			$this->_coupon_data = array(
				'discount_type', 'coupon_amount', 'expiry_date', 'free_shipping', 'exclude_sale_items'
			);		
			
			add_filter("wp_all_export_default_fields", 		        array( &$this, "filter_default_fields"), 10, 1);
			add_filter("wp_all_export_other_fields", 				array( &$this, "filter_other_fields"), 10, 1);
			add_filter("wp_all_export_available_data", 				array( &$this, "filter_available_data"), 10, 1);
			add_filter("wp_all_export_available_sections", 			array( &$this, "filter_available_sections" ), 10, 1);			
			add_filter("wp_all_export_init_fields", 				array( &$this, "filter_init_fields"), 10, 1);			
			
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
				return $this->other_fields;
			}													

			/**
			*
			* Filter Available Data
			*
			*/	
			public function filter_available_data($available_data){

				$available_data['existing_meta_keys'] = array_merge($available_data['other_fields'], $available_data['existing_meta_keys']);				

				return $available_data;
			}				

			/**
			*
			* Filter Sections in Available Data
			*
			*/
			public function filter_available_sections($sections){						
				unset($sections['media']);
				unset($sections['other']);
				$sections['cf']['title'] = __("Other", "wp_all_export_plugin");
				return $sections;
			}								

		// [\FILTERS]

		public function init( & $existing_meta_keys = array() ){

			if ( ! self::$is_active ) return;	

			if ( ! empty($existing_meta_keys) )
			{
				foreach ($existing_meta_keys as $key => $record_meta_key) 
				{
					if ( in_array($record_meta_key, $this->_coupon_data) )
					{
						unset($existing_meta_keys[$key]);						
					}
				}				
			}								
		}
	}
}