<?php

function pmxe_wpmu_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta)
{
	// create/update required database tables
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	require PMXE_Plugin::ROOT_DIR . '/schema.php';
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id	                
        $old_blog = $wpdb->blogid;
       
        switch_to_blog($blog_id);
        require PMXE_Plugin::ROOT_DIR . '/schema.php';
        dbDelta($plugin_queries);		                
        		
        switch_to_blog($old_blog);
        return;	                 
    }
}