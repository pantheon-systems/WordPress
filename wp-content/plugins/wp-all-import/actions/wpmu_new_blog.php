<?php

function pmxi_wpmu_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta)
{
	// create/update required database tables
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	require PMXI_Plugin::ROOT_DIR . '/schema.php';
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id	                
        $old_blog = $wpdb->blogid;
       
        switch_to_blog($blog_id);
        require PMXI_Plugin::ROOT_DIR . '/schema.php';
        dbDelta($plugin_queries);		                

		// sync data between plugin tables and wordpress (mostly for the case when plugin is reactivated)
			
		$post = new PMXI_Post_Record();
		$wpdb->query('DELETE FROM ' . $post->getTable() . ' WHERE post_id NOT IN (SELECT ID FROM ' . $wpdb->posts . ')');
        
        switch_to_blog($old_blog);
        return;	                 
    }
}