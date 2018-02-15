<?php
	
	if ( ! function_exists('wp_all_export_isValidMd5')){
		function wp_all_export_isValidMd5($md5 ='')
		{
		    return preg_match('/^[a-f0-9]{32}$/', $md5);
		}
	}	

	if ( ! function_exists('wp_all_export_get_relative_path') ){
		function wp_all_export_get_relative_path($path){

			$uploads = wp_upload_dir();

			return str_replace($uploads['basedir'], '', $path);			

		}
	}

	if ( ! function_exists('wp_all_export_get_absolute_path') ){
		function wp_all_export_get_absolute_path($path){			
			$uploads = wp_upload_dir();
			return ( strpos($path, $uploads['basedir']) === false and ! preg_match('%^https?://%i', $path)) ? $uploads['basedir'] . $path : $path;			
		}
	}

	if ( ! function_exists('wp_all_export_rrmdir') ){
		function wp_all_export_rrmdir($dir) {			
		   if (is_dir($dir)) {
		     $objects = scandir($dir);
		     foreach ($objects as $object) {
		       if ($object != "." && $object != "..") {
		         if (filetype($dir . "/" . $object) == "dir") wp_all_export_rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
		       }
		     }
		     reset($objects);
		     rmdir($dir);
		   }
		}
	}

	if ( ! function_exists('pmxe_getExtension')){
		function pmxe_getExtension($str) 
	    {	    	
	        $i = strrpos($str,".");        
	        if (!$i) return "";
	        $l = strlen($str) - $i;        
	        $ext = substr($str,$i+1,$l);	        
	        return (strlen($ext) <= 4) ? $ext : "";
		}
	}

	if ( ! function_exists('wp_all_export_get_existing_meta_by_cpt'))
	{
		function wp_all_export_get_existing_meta_by_cpt( $post_type = false )
		{
			if (empty($post_type)) return array();

			$post_type = ($post_type == 'product' and class_exists('WooCommerce')) ? array('product', 'product_variation') : array($post_type);

			global $wpdb;
			$table_prefix = $wpdb->prefix;
			$meta_keys = $wpdb->get_results("SELECT DISTINCT {$table_prefix}postmeta.meta_key FROM {$table_prefix}postmeta, {$table_prefix}posts WHERE {$table_prefix}postmeta.post_id = {$table_prefix}posts.ID AND {$table_prefix}posts.post_type IN ('" . implode('\',\'', $post_type) . "') AND {$table_prefix}postmeta.meta_key NOT LIKE '_edit%' LIMIT 500");			

			$_existing_meta_keys = array();
			if ( ! empty($meta_keys)){
				$exclude_keys = array('_first_variation_attributes', '_is_first_variation_created');
				foreach ($meta_keys as $meta_key) {
					if ( strpos($meta_key->meta_key, "_tmp") === false && strpos($meta_key->meta_key, "_v_") === false && ! in_array($meta_key->meta_key, $exclude_keys)) 
						$_existing_meta_keys[] = $meta_key->meta_key;
				}
			}
			return $_existing_meta_keys;
		}	
	}

	if ( ! function_exists('wp_all_export_get_existing_taxonomies_by_cpt'))
	{
		function wp_all_export_get_existing_taxonomies_by_cpt( $post_type = false )
		{
			if (empty($post_type)) return array();

			$post_taxonomies = array_diff_key(get_taxonomies_by_object_type(array($post_type), 'object'), array_flip(array('post_format')));
			$_existing_taxonomies = array();
			if ( ! empty($post_taxonomies)){
				foreach ($post_taxonomies as $tx) {
					if (strpos($tx->name, "pa_") !== 0)		
						$_existing_taxonomies[] = array(
							'name' => empty($tx->label) ? $tx->name : $tx->label,
							'label' => $tx->name,
							'type' => 'cats'
						);
				}
			}
			return $_existing_taxonomies;
		}	
	}

    if ( ! function_exists('wp_all_export_get_taxonomies')) {
        function wp_all_export_get_taxonomies() {
            // get all taxonomies
            $taxonomies = get_taxonomies(FALSE, 'objects');
            $ignore = array('nav_menu', 'link_category');
            $r = array();
            // populate $r
            foreach ($taxonomies as $taxonomy) {
                if (in_array($taxonomy->name, $ignore)) {
                    continue;
                }
                if ( ! empty($taxonomy->labels->name) && strpos($taxonomy->labels->name, "_") === false){
                    $r[$taxonomy->name] = $taxonomy->labels->name;
                }
                else{
                    $r[$taxonomy->name] = empty($taxonomy->labels->singular_name) ? $taxonomy->name : $taxonomy->labels->singular_name;
                }
            }
            asort($r, SORT_FLAG_CASE | SORT_STRING);
            // return
            return $r;

        }
    }

    if ( ! function_exists('wp_all_export_cmp_custom_types')){
        function wp_all_export_cmp_custom_types($a, $b)
        {
            return strcmp($a->labels->name, $b->labels->name);
        }
    }

	if ( ! function_exists('prepare_date_field_value')){
        function prepare_date_field_value($fieldOptions, $timestamp, $defaultFormat = false){

            if ( ! empty($fieldOptions))
            {
                switch ($fieldOptions)
                {
                    case 'unix':
                        $post_date = $timestamp;
                        break;
                    default:
                        $post_date = date($fieldOptions, $timestamp);
                        break;
                }
            }
            else
            {
                // Check if export was created before v1.4.2-beta-2.0
                if ( PMXE_Plugin::isExistingExport("1.4.2-beta-2.0") ){
                    // Do not change date fields for exports created before v1.4.2-beta-2.0
                    $post_date = $defaultFormat ? date($defaultFormat, $timestamp) : $timestamp;
                }
                else
                {
                    if ( in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants')) ){
                        $post_date = date("Y-m-d H:i:s", $timestamp);
                    } else {
                        $post_date = date("Y-m-d", $timestamp);
                    }
                }
            }
            return $post_date;
        }
    }