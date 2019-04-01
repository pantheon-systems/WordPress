<?php

function fastvelocity_version_check() {
	global $fastvelocity_plugin_version;
	
	# current FVM install date, create if it doesn't exist
	$ver = get_option("fastvelocity_plugin_version");
	if ($ver == false) { $ver = '0.0.0'; }
	
	# save current version on upgrade
	if ($ver != $fastvelocity_plugin_version) {
		update_option( "fastvelocity_plugin_version", $fastvelocity_plugin_version);
	}
	
	# compare versions (0.1.2)
	$dots = explode('.', $ver);
	if(!is_array($dots) || count($dots) != 3) { return false; }
	
	
	# changed options in 2.4.0
	if($dots[0] < 2 || ($dots[0] == 2 && $dots[1] < 4)) {
	
		# delete some old fields and define them on a radio option, by this priority
		if(get_option("fastvelocity_css_hide_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 3);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
		
		if(get_option("fastvelocity_min_async_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 2);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
		
		if(get_option("fastvelocity_min_force_inline_googlefonts") != false) {
			update_option( "fastvelocity_gfonts_method", 1);
			delete_option('fastvelocity_min_force_inline_googlefonts');
			delete_option('fastvelocity_min_async_googlefonts');
			delete_option('fastvelocity_css_hide_googlefonts');
		}
	
	}
	
	
	# changed on 2.6.0
	if($dots[0] < 2 || ($dots[0] == 2 && $dots[1] < 6)) {
	
		# add old cache purge event cron
		if (!wp_next_scheduled ('fastvelocity_purge_old_cron')) {
			wp_schedule_event(time(), 'daily', 'fastvelocity_purge_old_cron_event');
		}
	
	}
}
add_action( 'plugins_loaded', 'fastvelocity_version_check' );


# upgrade notifications
function fastvelocity_plugin_update_message($currentPluginMetadata, $newPluginMetadata) {
	if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
		echo '<span style="display:block; background: #F7FCFE; padding: 14px 0 6px 0; margin: 10px -12px -12px -16px;">';
		echo '<span class="notice notice-info" style="display:block; padding: 10px; margin: 0;">';
		echo '<span class="dashicons dashicons-megaphone" style="margin-left: 2px; margin-right: 6px;"></span>';
		echo strip_tags($newPluginMetadata->upgrade_notice);
		echo '</span>'; 
		echo '</span>'; 
	}
}
add_action( 'in_plugin_update_message-fast-velocity-minify/fvm.php', 'fastvelocity_plugin_update_message', 10, 2 );
