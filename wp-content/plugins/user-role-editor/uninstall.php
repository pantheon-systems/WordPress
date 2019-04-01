<?php
/* 
 * User Role Editor plugin uninstall script
 * Author: vladimir@shinephp.com
 *
 */

global $wpdb;

if (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')) {
	 exit();  // silence is golden
}


function ure_delete_options() {
  global $wpdb;

  $backup_option_name = $wpdb->prefix.'backup_user_roles';
  delete_option($backup_option_name);
  delete_option('ure_caps_readable');
  delete_option('ure_show_deprecated_caps');
  delete_option('ure_hide_pro_banner');
  delete_option('user_role_editor');
  delete_option('ure_role_additional_options_values');
  delete_option('ure_task_queue');
  
}


if (!is_multisite()) {
  ure_delete_options();
} else {
  $old_blog = $wpdb->blogid;
  // Get all blog ids
  $network = get_current_site();
  $query = $wpdb->prepare(
                    "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id=%d",
                    array($network->id)
                         );
  $blogIds = $wpdb->get_col($query);
  foreach ($blogIds as $blog_id) {
    switch_to_blog($blog_id);
    ure_delete_options();    
  }
  switch_to_blog($old_blog);
}
