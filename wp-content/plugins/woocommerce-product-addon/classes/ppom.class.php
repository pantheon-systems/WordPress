<?php
/**
 * PPOM Meta Class
 * @since version 15.0
 * 
 * */
 
 
class PPOM_Meta {
    
        protected static $wc_product;
        var $product_id;
        var $meta_id;
        
        // $product_id can be null if get instance to get data by meta_id
        function __construct( $product_id=null ) {
            
            self::$wc_product = wc_get_product( $product_id );
            $this->meta_id    = null;
            $this->product_id = $product_id;
            
            //Now we are creating properties agains each methods in our Alpha class.
            $methods = get_class_methods( $this );
            $excluded_methods = array('__construct', 
                                        'get_settings_by_id');
                                        
            foreach ( $methods as $method ) {
                if ( ! in_array($method, $excluded_methods) ) {
                    $this->$method = $this->$method();
                }
            }
        }
        
        
        // Properties functions
        function is_exists() {
            
            $this->meta_id = get_post_meta ( $this->product_id, PPOM_PRODUCT_META_KEY, true );
            if( $this->meta_id == 0 || $this->meta_id == 'None' ) {
        		$this->meta_id = null;
        	}
            
            if( $this->meta_id == null ) {
        		if($meta_found = ppom_has_category_meta( $this->product_id ) ){
            		
            		/**
            		 * checking product against categories
            		 * @since 6.4
            		 */
            		$this->meta_id = $meta_found;
            	}
        	}
        	
        	return $this->meta_id == null ? false : true;
        }
        
        
        // since 15.0 multiple meta can be set against single product
        // so we have to set one active one meta for compatiblility isues
        function single_meta_id() {
            
            $single_meta = $this->meta_id;
            
            if( $this->has_multiple_meta() ) {
    		    $single_meta = $this->meta_id[0];
    		}
    		
    		return $single_meta;
        }
        
        function has_multiple_meta() {
            
            $multiple_meta = false;
            
            if( is_array($this->meta_id) ) {
                $multiple_meta = true;
            }
            
            return $multiple_meta;
        }
        
        // getting settings
        function settings() {
            
            if( ! $this->is_exists() )
    			return null;
    			
    		$meta_id = $this->single_meta_id;
    			
    		global $wpdb;
    		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		$meta_settings = $wpdb->get_row ( $qry );
    		
    		$meta_settings = empty($meta_settings) ? null : $meta_settings;
    		
    		return apply_filters('ppom_meta_settings', $meta_settings, $this);
        }
        
        // getting fields
        function fields() {
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->settings() ) return null;
            
            $meta_fields = array();
            global $wpdb;
            if( $this->has_multiple_meta() ) {
                
                foreach( $this->meta_id as $meta_id ) {
                    
        		    $qry = "SELECT the_meta FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
        		    $fields = $wpdb->get_var ( $qry );
        		  //  var_dump($fields);
                    $fields = json_decode ( $fields, true );
        		    $meta_fields = array_merge($meta_fields, $fields);
                }
            } else {
                $meta_id = $this->meta_id;
                $qry = "SELECT the_meta FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		    $fields = $wpdb->get_var ( $qry );
                $meta_fields = json_decode ( $fields, true );
            }
    			
            // if( empty($meta_fields) ) return null;
            
            return apply_filters('ppom_meta_fields', $meta_fields, $this);
        }
        
        // check meta settings: ajax validation
        function ajax_validation_enabled() {
            
            $validation_enabled = false;
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->settings() ) return null;
            
            if( $this->settings()->productmeta_validation == 'yes' ) {
                $validation_enabled = true;
            }
    			
            return apply_filters('ppom_ajax_validation_enabled', $validation_enabled, $this);
        }
        
        // check meta settings: styels
        function inline_css() {
            
            $inline_css = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->settings() ) return null;
            
            if( $this->settings()->productmeta_style != '' ) {
                $inline_css = stripslashes(strip_tags( $this->settings()->productmeta_style ));
            }
    			
            return apply_filters('ppom_inline_css', $inline_css, $this);
        }
        
        // check meta settings: styels
        function price_display() {
            
            $price_display = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->settings() ) return null;
            
            $price_display = $this->settings()->dynamic_price_display;
    			
            return apply_filters('ppom_price_display', $price_display, $this);
        }
        
        // check meta settings: styels
        function meta_title() {
            
            $meta_title = '';
            
            if( ! $this->is_exists() )
    			return null;
    			
    		// Meta created without any fields
            if( ! $this->settings() ) return null;
            
            $meta_title = stripslashes($this->settings()->productmeta_name);
    			
            return apply_filters('ppom_meta_title', $meta_title, $this);
        }
        
        // Since 15.1: checking if all meta has unique datanames
        function has_unique_datanames() {
            
            if( ! $this->fields() ) return false;
            
            $has_unique = true;
            $datanames_array = array();
            
            foreach( $this->fields() as $field ) {
                
                $type = isset($field['type']) ? $field['type'] : '';
                
                // pricematrix does not have dataname
                if( $type == 'pricematrix' ) continue;
                
                if( !isset($field['data_name']) ) {
                    $has_unique = false;
                    break;
                }
                
                if( in_array($field['data_name'], $datanames_array) ) {
                    
                    $has_unique = false;
                    break;
                }
                
                $datanames_array[] = $field['data_name'];
                
            }
            
            // ppom_pa($datanames_array);
            return $has_unique;
        }
        
        /* ============== Get settings by metaid  ================= */
        function get_settings_by_id( $meta_id ) {
            
            global $wpdb;
            
    		$qry = "SELECT * FROM " . $wpdb->prefix . PPOM_TABLE_META . " WHERE productmeta_id = {$meta_id}";
    		$meta_settings = $wpdb->get_row ( $qry );
    		
    		$meta_settings = empty($meta_settings) ? null : $meta_settings;
    		
    		return apply_filters('ppom_get_settings_by_id', $meta_settings, $meta_id, $this);
        }
        
}
 
 