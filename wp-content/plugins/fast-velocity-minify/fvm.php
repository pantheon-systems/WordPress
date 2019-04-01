<?php
/*
Plugin Name: Fast Velocity Minify
Plugin URI: http://fastvelocity.com
Description: Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS and JavaScript files into groups, compressing HTML and other speed optimizations. 
Author: Raul Peixoto
Author URI: http://fastvelocity.com
Version: 2.6.0
License: GPL2

------------------------------------------------------------------------
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


# check for minimum requirements and prevent activation or disable if not fully compatible
function fvm_compat_checker() {
	global $wp_version; 
	
	# defaults
	$error = '';
	$nwpv = implode('.', array_slice(explode('.', $wp_version), 0, 2)); # get 2 p only
	
	# php version requirements
	if (version_compare( PHP_VERSION, '5.4', '<' )) { 
		$error = 'Fast Velocity Minify requires PHP 5.4 or higher. You’re still on '. PHP_VERSION; 
	}

	# php extension requirements	
	if (!extension_loaded('mbstring')) { 
		$error = 'Fast Velocity Minify requires the PHP mbstring module to be installed on the server.'; 
	}
	
	# wp version requirements
	if ( $nwpv < '4.5' ) { 
		$error = 'Fast Velocity Minify requires WP 4.5 or higher. You’re still on ' . $wp_version; 
	}

	
	if ((is_plugin_active(plugin_basename( __FILE__ )) && !empty($error)) || !empty($error)) { 
		if (isset($_GET['activate'])) { unset($_GET['activate']); }
			deactivate_plugins( plugin_basename( __FILE__ )); 
			add_action('admin_notices', function() use ($error){ 
				echo '<div class="notice notice-error is-dismissible"><p><strong>'.$error.'</strong></p></div>'; 
			});
	} 
}
add_action('admin_init', 'fvm_compat_checker');


# get plugin version
$fastvelocity_plugin_version_get_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$fastvelocity_plugin_version = $fastvelocity_plugin_version_get_data['Version'];

# get the plugin directory
$plugindir = plugin_dir_path( __FILE__ ); # prints with trailing slash

# reusable functions
include($plugindir.'inc/functions.php');
include($plugindir.'inc/functions-serverinfo.php');
include($plugindir.'inc/functions-upgrade.php');
include($plugindir.'inc/functions-cache.php');

# wp-cli support
if ( defined( 'WP_CLI' ) && WP_CLI ) { 
	include($plugindir.'inc/functions-cli.php');
}


# get cache directories and urls
$cachepath = fvm_cachepath();
$tmpdir = $cachepath['tmpdir'];
$cachedir =  $cachepath['cachedir'];
$cachedirurl = $cachepath['cachedirurl'];

$wp_home = site_url();   # get the current wordpress installation url
$wp_domain = trim(str_ireplace(array('http://', 'https://'), '', trim($wp_home, '/')));
$wp_home_path = ABSPATH;

# default globals
$fastvelocity_min_global_js_done = array();
$fvm_collect_google_fonts = array();
$collect_preload_css = array();
$collect_preload_js = array();
$fvm_debug = get_option('fastvelocity_fvm_debug');

###########################################
# build control panel pages ###############
###########################################

# options from the database, false if not set
$ignore = array_filter(array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_ignore', ''))));
$blacklist = array_filter(array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_blacklist', ''))));
$ignorelist = array_filter(array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_ignorelist', ''))));
$fvm_min_excludecsslist = array_filter(array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_excludecsslist', ''))));
$fvm_min_excludejslist = array_filter(array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_excludejslist', ''))));

$fvm_enable_purgemenu = get_option('fastvelocity_min_enable_purgemenu');
$default_protocol = get_option('fastvelocity_min_default_protocol', 'dynamic');
$disable_js_merge = get_option('fastvelocity_min_disable_js_merge');
$disable_css_merge = get_option('fastvelocity_min_disable_css_merge');
$disable_js_minification = get_option('fastvelocity_min_disable_js_minification');
$disable_css_minification = get_option('fastvelocity_min_disable_css_minification');
$remove_print_mediatypes = get_option('fastvelocity_min_remove_print_mediatypes'); 
$skip_html_minification = get_option('fastvelocity_min_skip_html_minification');
$strip_htmlcomments = get_option('fastvelocity_min_strip_htmlcomments');
$skip_cssorder = get_option('fastvelocity_min_skip_cssorder');
$skip_google_fonts = get_option('fastvelocity_min_skip_google_fonts');
$skip_emoji_removal = get_option('fastvelocity_min_skip_emoji_removal');
$fvm_clean_header_one = get_option('fastvelocity_fvm_clean_header_one');
$enable_defer_js = get_option('fastvelocity_min_enable_defer_js');
$exclude_defer_jquery = get_option('fastvelocity_min_exclude_defer_jquery');
$force_inline_css = get_option('fastvelocity_min_force_inline_css');
$force_inline_css_footer = get_option('fastvelocity_min_force_inline_css_footer');
$remove_googlefonts = get_option('fastvelocity_min_remove_googlefonts');
$defer_for_pagespeed = get_option('fastvelocity_min_defer_for_pagespeed');
$defer_for_pagespeed_optimize = get_option('fastvelocity_min_defer_for_pagespeed_optimize');
$exclude_defer_login = get_option('fastvelocity_min_exclude_defer_login');
$skip_defer_lists = get_option('fastvelocity_min_skip_defer_lists');
$fvm_fix_editor = get_option('fastvelocity_min_fvm_fix_editor');
$fvmloadcss = get_option('fastvelocity_min_loadcss');
$fvm_remove_css = get_option('fastvelocity_min_fvm_removecss');
$fvm_cdn_url = get_option('fastvelocity_min_fvm_cdn_url');
$fvm_enabled_css_preload = get_option('fastvelocity_enabled_css_preload');
$fvm_enabled_js_preload = get_option('fastvelocity_enabled_css_preload');
$fvm_fawesome_method = get_option("fastvelocity_fontawesome_method");

# default options
$used_css_files = array();
$force_inline_googlefonts = true;
$min_async_googlefonts = false;
$css_hide_googlefonts = false;


# define google fonts options based on a radio form
$fvm_gfonts_method = get_option("fastvelocity_gfonts_method");
if($fvm_gfonts_method != false) {
	if($fvm_gfonts_method == 2) { # load Async
		$force_inline_googlefonts = false;
		$min_async_googlefonts = true;
		$css_hide_googlefonts = false;
	} 
	if($fvm_gfonts_method == 3) { # hide from PSI
		$force_inline_googlefonts = false;
		$min_async_googlefonts = false;
		$css_hide_googlefonts = true;
	}
}


# default ua list
$fvmualist = array('nux.*oto\sG', 'x11.*fox\/54', 'x11.*ome\/39', 'x11.*ome\/62', 'oid\s6.*1.*xus\s5.*MRA58N.*ome', 'JWR66Y.*ome\/62', 'woobot', 'speed', 'ighth', 'tmetr', 'eadle');


# add admin page and rewrite defaults
if(is_admin()) {
    add_action('admin_menu', 'fastvelocity_min_admin_menu');
    add_action('admin_enqueue_scripts', 'fastvelocity_min_load_admin_jscss');
    add_action('wp_ajax_fastvelocity_min_files', 'fastvelocity_min_files_callback');
    add_action('admin_init', 'fastvelocity_min_register_settings');
    
	# This function runs when WordPress updates or installs/remove something
	add_action('upgrader_process_complete', 'fastvelocity_purge_all_global');
	add_action('after_switch_theme', 'fastvelocity_purge_all_global');
	add_action('admin_init', 'fastvelocity_purge_onsave', 1);
	
	# activation, deactivation
	register_activation_hook( __FILE__, 'fastvelocity_plugin_activate' );
	register_deactivation_hook( __FILE__, 'fastvelocity_plugin_deactivate');
	register_uninstall_hook( __FILE__, 'fastvelocity_plugin_uninstall');
	
} else {
		
	# skip on certain post_types or if there are specific keys on the url or if editor or admin
	if(!fastvelocity_exclude_contents()) {
	
		# actions for frontend only
		if(!$disable_js_merge) { 
			add_action( 'wp_print_scripts', 'fastvelocity_min_merge_header_scripts', PHP_INT_MAX );
			add_action( 'wp_print_footer_scripts', 'fastvelocity_min_merge_footer_scripts', 9.999999 ); 
		}
		if(!$disable_css_merge) {
			add_action('wp_head', 'fvm_add_criticial_path', 2); 
			
			# merge, if inline is not selected
			if($force_inline_css != true) {
				add_action('wp_print_styles', 'fastvelocity_min_merge_header_css', PHP_INT_MAX ); 
				add_action('wp_print_footer_scripts', 'fastvelocity_min_merge_footer_css', 9.999999 );
			} else {
				add_filter('style_loader_tag', 'fastvelocity_optimizecss', PHP_INT_MAX, 4 );
				add_action('wp_print_styles','fastvelocity_add_google_fonts_merged', PHP_INT_MAX);
				add_action('wp_print_footer_scripts','fastvelocity_add_google_fonts_merged', PHP_INT_MAX );
			}
			
		}
		if(!$skip_emoji_removal) { 
			add_action( 'init', 'fastvelocity_min_disable_wp_emojicons' );
			add_filter( 'tiny_mce_plugins', 'fastvelocity_disable_emojis_tinymce' );
		}
		
		if($fvm_clean_header_one) { 
			# no resource hints, generator tag, shortlinks, manifest link, etc
			remove_action('wp_head', 'wp_resource_hints', 2);
			remove_action('wp_head', 'wp_generator');
			remove_action('template_redirect', 'wp_shortlink_header', 11);
			remove_action('wp_head', 'wlwmanifest_link');
			remove_action('wp_head', 'rsd_link');
			remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
			remove_action('wp_head','feed_links', 2);
			remove_action('wp_head','feed_links_extra', 3);
			add_filter('after_setup_theme', 'fastvelocity_remove_redundant_shortlink');
		}
		
		# enable html minification
		if(!$skip_html_minification && !is_admin()) {
			add_action('template_redirect', 'fastvelocity_min_html_compression_start', PHP_INT_MAX);
		}
		
		# add the LoadCSS polyfil
		if($fvmloadcss || $fvm_fawesome_method == 2 || $fvm_gfonts_method == 2) {
			add_action('wp_footer', 'fvm_add_loadcss', PHP_INT_MAX);
		}
		
		# add the LoadAsync JavaScript function
		add_action('wp_head', 'fvm_add_loadasync', 0); 
		
		# remove query from static assets and process defering (if enabled)
		add_filter('style_loader_src', 'fastvelocity_remove_cssjs_ver', 10, 2);
		add_filter('script_loader_tag', 'fastvelocity_min_defer_js', 10, 3); 

	}
}


# exclude processing for editors and administrators (fix editors)
add_action( 'plugins_loaded', 'fastvelocity_fix_editor' );
function fastvelocity_fix_editor() {
global $fvm_fix_editor, $disable_js_merge, $disable_css_merge, $skip_emoji_removal;
	if($fvm_fix_editor == true && is_user_logged_in()) {
		remove_action('wp_print_scripts', 'fastvelocity_min_merge_header_scripts', PHP_INT_MAX );
		remove_action('wp_print_footer_scripts', 'fastvelocity_min_merge_footer_scripts', 9.999999 ); 
		remove_action('wp_print_styles', 'fastvelocity_min_merge_header_css', PHP_INT_MAX ); 
		remove_action('wp_print_footer_scripts', 'fastvelocity_min_merge_footer_css', 9.999999 );
		remove_action('wp_print_styles', 'fastvelocity_add_google_fonts_merged', PHP_INT_MAX);
		remove_action('wp_print_footer_scripts', 'fastvelocity_add_google_fonts_merged', PHP_INT_MAX );
		remove_action('init', 'fastvelocity_min_disable_wp_emojicons');
		remove_action('template_redirect', 'fastvelocity_min_html_compression_start', PHP_INT_MAX);
		remove_filter('style_loader_src', 'fastvelocity_remove_cssjs_ver', 10, 2);
		remove_filter('script_loader_tag', 'fastvelocity_min_defer_js', 10, 3); 
	} 
}

# create admin menu
function fastvelocity_min_admin_menu() {
add_options_page('Fast Velocity Minify Settings', 'Fast Velocity Minify', 'manage_options', 'fastvelocity-min', 'fastvelocity_min_settings');
}


# add admin toolbar
if($fvm_enable_purgemenu == true) {
	add_action( 'admin_bar_menu', 'fastvelocity_admintoolbar', 100 );
}

# admin toolbar processing
function fastvelocity_admintoolbar() {
	if(current_user_can('manage_options')) {
		global $wp_admin_bar;

		# Create or add new items into the Admin Toolbar.
		$wp_admin_bar->add_node(array(
			'id'    => 'fvm',
			'title' => '<span class="ab-icon"></span><span class="ab-label">' . __("FVM Purge",'fvm') . '</span>',
			'href'  => wp_nonce_url( add_query_arg('_fvmcache', 'clear'), 'fvm_clear_nonce')
		));

	}
}


# function to list all cache files
function fastvelocity_min_files_callback() {
	global $cachedir;
	
	# default
	$size = fastvelocity_get_cachestats();
	$return = array('js' => array(), 'css' => array(), 'stamp' => $_POST['stamp'], 'cachesize'=> $size);
	
	# inspect directory with opendir, since glob might not be available in some systems
	clearstatcache();
	if ($handle = opendir($cachedir.'/')) {
		while (false !== ($file = readdir($handle))) {
			$file = $cachedir.'/'.$file;
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if (in_array($ext, array('js', 'css'))) {
				$log = file_get_contents($file.'.txt');
				$mincss = substr($file, 0, -4).'.min.css';
				$minjs = substr($file, 0, -3).'.min.js';
				$filename = basename($file);
				if ($ext == 'css' && file_exists($mincss)) { $filename = basename($mincss); }
				if ($ext == 'js' && file_exists($minjs)) { $filename = basename($minjs); }
				$fsize = fastvelocity_format_filesize(filesize($file));
				$uid = hash('adler32', $filename);
				array_push($return[$ext], array('uid'=>$uid, 'filename' => $filename, 'log' => $log, 'fsize' => $fsize));
			}
		}
	closedir($handle);
	}
	
	header('Content-Type: application/json');
	echo json_encode($return);
	wp_die();
}


# load wp-admin css and js files
function fastvelocity_min_load_admin_jscss($hook) {
	if ('settings_page_fastvelocity-min' != $hook) { return; }
	wp_enqueue_script('postbox');
    wp_enqueue_style('fastvelocity-min', plugins_url('fvm.css', __FILE__), array(), filemtime(plugin_dir_path( __FILE__).'fvm.css'));
    wp_enqueue_script('fastvelocity-min', plugins_url('fvm.js', __FILE__), array('jquery'), filemtime(plugin_dir_path( __FILE__).'fvm.js'), true);
}


# register plugin settings
function fastvelocity_min_register_settings() {
    register_setting('fvm-group', 'fastvelocity_min_enable_purgemenu');
	register_setting('fvm-group', 'fastvelocity_preserve_settings_on_uninstall');
	register_setting('fvm-group', 'fastvelocity_min_default_protocol');
    register_setting('fvm-group', 'fastvelocity_min_disable_js_merge');
    register_setting('fvm-group', 'fastvelocity_min_disable_css_merge');
    register_setting('fvm-group', 'fastvelocity_min_disable_js_minification');
    register_setting('fvm-group', 'fastvelocity_min_disable_css_minification');
    register_setting('fvm-group', 'fastvelocity_min_remove_print_mediatypes');
    register_setting('fvm-group', 'fastvelocity_min_skip_html_minification');
	register_setting('fvm-group', 'fastvelocity_min_strip_htmlcomments');
	register_setting('fvm-group', 'fastvelocity_min_skip_cssorder');
	register_setting('fvm-group', 'fastvelocity_min_skip_google_fonts');
	register_setting('fvm-group', 'fastvelocity_min_skip_fontawesome_fonts');
	register_setting('fvm-group', 'fastvelocity_min_skip_emoji_removal');
	register_setting('fvm-group', 'fastvelocity_fvm_clean_header_one');
	register_setting('fvm-group', 'fastvelocity_min_enable_defer_js');
	register_setting('fvm-group', 'fastvelocity_min_exclude_defer_jquery');
	register_setting('fvm-group', 'fastvelocity_min_force_inline_css');
	register_setting('fvm-group', 'fastvelocity_min_force_inline_css_footer');
	register_setting('fvm-group', 'fastvelocity_min_remove_googlefonts');
	register_setting('fvm-group', 'fastvelocity_gfonts_method');
	register_setting('fvm-group', 'fastvelocity_fontawesome_method');
	register_setting('fvm-group', 'fastvelocity_min_defer_for_pagespeed');
	register_setting('fvm-group', 'fastvelocity_min_defer_for_pagespeed_optimize');
	register_setting('fvm-group', 'fastvelocity_min_exclude_defer_login');
	register_setting('fvm-group', 'fastvelocity_min_skip_defer_lists');
	register_setting('fvm-group', 'fastvelocity_min_fvm_fix_editor');
	register_setting('fvm-group', 'fastvelocity_min_fvm_cdn_url');
	register_setting('fvm-group', 'fastvelocity_min_fvm_cdn_force');
	register_setting('fvm-group', 'fastvelocity_min_change_cache_base_url');
	register_setting('fvm-group', 'fastvelocity_min_change_cache_path');

	# pro tab
	register_setting('fvm-group-pro', 'fastvelocity_min_ignore');
    register_setting('fvm-group-pro', 'fastvelocity_min_ignorelist');
	register_setting('fvm-group-pro', 'fastvelocity_min_excludecsslist');
	register_setting('fvm-group-pro', 'fastvelocity_min_excludejslist');
    register_setting('fvm-group-pro', 'fastvelocity_min_blacklist');
    register_setting('fvm-group-pro', 'fastvelocity_min_merge_allowed_urls');
	
	# dev tab
	register_setting('fvm-group-dev', 'fastvelocity_fvm_debug');
	register_setting('fvm-group-dev', 'fastvelocity_enabled_css_preload');
	register_setting('fvm-group-dev', 'fastvelocity_enabled_js_preload');
	register_setting('fvm-group-dev', 'fastvelocity_min_hpreload');
	register_setting('fvm-group-dev', 'fastvelocity_min_hpreconnect');
	register_setting('fvm-group-dev', 'fastvelocity_min_loadcss');
	register_setting('fvm-group-dev', 'fastvelocity_min_fvm_removecss');
	register_setting('fvm-group-dev', 'fastvelocity_min_critical_path_css');
	register_setting('fvm-group-dev', 'fastvelocity_min_critical_path_css_is_front_page');
	
	
	
}



# add settings link on plugin page
function fastvelocity_min_settings_link($links) {
	if (is_plugin_active(plugin_basename( __FILE__ ))) { 
		$settings_link = '<a href="options-general.php?page=fastvelocity-min&tab=set">Settings</a>'; 
		array_unshift($links, $settings_link); 
	}
return $links;
}
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'fastvelocity_min_settings_link' );


# purge all caches by request
add_action('init','fastvelocity_process_cache_purge_request');
function fastvelocity_process_cache_purge_request(){
	if((isset($_POST['purgeall']) && $_POST['purgeall'] == 1) || isset($_GET['_fvmcache'])) {
		
		# must be able to cleanup cache
		if (!current_user_can('manage_options')) { wp_die( __('You do not have sufficient permissions to access this page.')); }
		
		# validate nonce
		if(empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'fvm_clear_nonce')) {
			wp_die( __('Invalid or expired request... please go back and refresh before trying again!'));
		}
		
		if(is_admin()) {
			fvm_purge_all(); # purge all
			$others = fvm_purge_others(); # purge third party caches
			$notice = array('All caches from <strong>FVM</strong> have been purged!', strip_tags($others, '<strong>'));
			$notice = array_filter($notice);
			$notice = json_encode($notice); # encode
			set_transient( 'wordpress_fvmcache', $notice, 10);
			wp_safe_redirect(remove_query_arg('_wpnonce', remove_query_arg('_fvmcache', wp_get_referer())));
		} else {
			fvm_purge_all(); # purge all
			fvm_purge_others(); # purge third party caches
			wp_safe_redirect(remove_query_arg('_wpnonce', remove_query_arg('_fvmcache', wp_get_referer())));
		}
	}
}

# print admin notices after purging caches, if on wp-admin
add_action( 'admin_notices', 'fastvelocity_cachepurge_admin_notices' );
function fastvelocity_cachepurge_admin_notices() {
	
	# skip on submit
	if((isset($_POST['purgeall']) && $_POST['purgeall'] == 1) || isset($_GET['_fvmcache'])) {
		return true;
	}

	# cache purge notices
	$inf = get_transient('wordpress_fvmcache');
	if($inf != false && !empty($inf)) {
			
		# decode to array or null
		$jsonarr = json_decode($inf, true);
		if(!is_null($jsonarr) && is_array($jsonarr)){
			
			# print notices
			foreach ($jsonarr as $notice) {
				echo  __('<div class="notice notice-success is-dismissible"><p>'.$notice.'</p></div>');
			}
		}
		
		# remove
		delete_transient('wordpress_fvmcache');
	}
}


# print admin notices if we don't have enough file permissions to write
add_action( 'admin_notices', 'fastvelocity_check_permissions_admin_notices' );
function fastvelocity_check_permissions_admin_notices() {
	
	# get cache path
	$cachepath = fvm_cachepath();
	$cachebase = $cachepath['cachebase'];
	if(is_dir($cachebase) && !is_writable($cachebase)) {
		$chmod = substr(sprintf('%o', fileperms($cachebase)), -4);
		echo  __('<div class="notice notice-error is-dismissible"><p>FVM needs writting permissions on '.$cachebase.'</p></div>');
		echo  __('<div class="notice notice-error is-dismissible"><p>The current permissions for FVM are chmod '.$chmod.'</p></div>');
		echo  __('<div class="notice notice-error is-dismissible"><p>If you need something higher than 755 for it to work, your server is probaly misconfigured. Please contact your hosting provider or check the help section for other providers.</p></div>');
	}

}


# manage settings page
function fastvelocity_min_settings() {
if (!current_user_can('manage_options')) { wp_die( __('You do not have sufficient permissions to access this page.')); }

# tmp folder
global $tmpdir, $cachedir, $plugindir;

# get active tab, set default
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'status';

?>
<div class="wrap">
<h1>Fast Velocity Minify</h1>

<h2 class="nav-tab-wrapper wp-clearfix">
    <a href="?page=fastvelocity-min&tab=status" class="nav-tab <?php echo $active_tab == 'status' ? 'nav-tab-active' : ''; ?>">Status</a> 
    <a href="?page=fastvelocity-min&tab=set" class="nav-tab <?php echo $active_tab == 'set' ? 'nav-tab-active' : ''; ?>">Settings</a>
	<a href="?page=fastvelocity-min&tab=pro" class="nav-tab <?php echo $active_tab == 'pro' ? 'nav-tab-active' : ''; ?>">Pro</a>
	<a href="?page=fastvelocity-min&tab=dev" class="nav-tab <?php echo $active_tab == 'dev' ? 'nav-tab-active' : ''; ?>">Developers</a>
	<a href="?page=fastvelocity-min&tab=server" class="nav-tab <?php echo $active_tab == 'server' ? 'nav-tab-active' : ''; ?>">Server Info</a>
	<a href="?page=fastvelocity-min&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
</h2>


<?php if( $active_tab == 'status' ) { ?>

<div id="fastvelocity-min">
    <div id="poststuff">
        <div id="fastvelocity_min_processed" class="postbox-container">
			<div class="meta-box">
			
				<div class="postbox" id="tab-purge">
                    <h3 class="hndle"><span>Purge the cache files </span></h3>
                    <div class="inside" id="fastvelocity_min_topbtns">
                        <ul class="processed">
						<li id="purgeall-row">
							<span class="filename">Purge FVM cache (<span id="fvm_cache_size"><?php echo fastvelocity_get_cachestats(); ?></span>)</span> 
							<span class="actions">
							<form method="post" id="fastvelocity_min_clearall" action="<?php echo wp_nonce_url( add_query_arg('_fvmcache', 'clear'), 'fvm_clear_nonce'); ?>">
							<input type="hidden" name="purgeall" value="1" />
							<?php submit_button('Delete', 'button-secondary', 'submit', false); ?>
							</form>
						</li>
						</ul>
                    </div>
                </div>				
			
                <div class="postbox" id="tab-js">
                    <h3 class="hndle"><span>List of processed JS files</span></h3>
                    <div class="inside" id="fastvelocity_min_jsprocessed">
					<ul class="processed"></ul>
                    </div>
                </div>

                <div class="postbox" id="tab-css">
                    <h3 class="hndle"><span>List of processed CSS files</span></h3>
                    <div class="inside" id="fastvelocity_min_cssprocessed">
						<?php
							$force_inline_css = get_option('fastvelocity_min_force_inline_css');
							if($force_inline_css != false) {
								echo '<p>There are no merged CSS files listed here, because you are inlining all CSS directly.</p>';
							} else { 
								echo '<ul class="processed"></ul>'; 
							}
						?>
                        
                    </div>
                </div>
					
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if( $active_tab == 'set' ) { ?>
<form method="post" action="options.php">
<?php settings_fields('fvm-group'); do_settings_sections('fvm-group'); ?>


<div style="height: 20px;"></div>
<h2 class="title">Basic Settings</h2>
<p class="fvm-bold-green">These options are generaly safe to edit as needed. If you use a cache plugin, kindly purge all your caches once you're done with the changes.</p>

<table class="form-table fvm-settings">
<tbody>


<tr>
<th scope="row">Functionality</th>
<td>
<p class="fvm-bold-green fvm-rowintro">The HTML minification is ON by default, but you can:</p>

<fieldset>
<label for="fastvelocity_min_enable_purgemenu">
<input name="fastvelocity_min_enable_purgemenu" type="checkbox" id="fastvelocity_min_enable_purgemenu" value="1" <?php echo checked(1 == get_option('fastvelocity_min_enable_purgemenu'), true, false); ?>>
Admin Bar Purge <span class="note-info">[ If selected, a new option to purge FVM cache from the admin bar will show up ]</span></label>
<br />

<label for="fastvelocity_preserve_settings_on_uninstall">
<input name="fastvelocity_preserve_settings_on_uninstall" type="checkbox" id="fastvelocity_preserve_settings_on_uninstall" value="1" <?php echo checked(1 == get_option('fastvelocity_preserve_settings_on_uninstall'), true, false); ?>>
Preserve Settings<span class="note-info">[ If selected, all FVM settings will be preserved, even if you uninstall the plugin ]</span></label>
<br />

<label for="fastvelocity_min_fvm_fix_editor">
<input name="fastvelocity_min_fvm_fix_editor" type="checkbox" id="fastvelocity_min_fvm_fix_editor" value="1" <?php echo checked(1 == get_option('fastvelocity_min_fvm_fix_editor'), true, false); ?>>
Fix Page Editors <span class="note-info">[ Will disable merging of JS and CSS for logged in users, to improve compatibility with visual editors ]</span></label>
<br />

</fieldset></td>
</tr>


<tr>
<th scope="row">URL Options</th>
<td>
<?php 
# what to select
$sel = get_option('fastvelocity_min_default_protocol');
$a = ''; if($sel == 'dynamic' || empty($sel)) { $a = ' checked="checked"'; }
$b = ''; if($sel == 'http') { $b = ' checked="checked"'; }
$c = ''; if($sel == 'https') { $c = ' checked="checked"'; }
?>
<p class="fvm-bold-green fvm-rowintro">You may need to force http or https, for some CDN plugins to work:</p>
<fieldset>
	<label><input type="radio" name="fastvelocity_min_default_protocol" value="dynamic" <?php echo $a; ?>> Auto Detect </label><br>
	<label><input type="radio" name="fastvelocity_min_default_protocol" value="http"<?php echo $b; ?>> Force HTTP urls (if you don't have SSL)</label><br>
	<label><input type="radio" name="fastvelocity_min_default_protocol" value="https"<?php echo $c; ?>> Force HTTPS urls (recommended if you have SSL)</span></label><br>
</fieldset>
</td>
</tr>

<tr>
<th scope="row">HTML Options</th>
<td>
<p class="fvm-bold-green fvm-rowintro">The HTML minification is ON by default, but you can:</p>

<fieldset>
<label for="fastvelocity_min_skip_html_minification">
<input name="fastvelocity_min_skip_html_minification" type="checkbox" id="fastvelocity_min_skip_html_minification" value="1" <?php echo checked(1 == get_option('fastvelocity_min_skip_html_minification'), true, false); ?>>
Disable HTML Minification <span class="note-info">[ This will disable HTML minification ]</span></label>
<br />

<label for="fastvelocity_min_strip_htmlcomments">
<input name="fastvelocity_min_strip_htmlcomments" type="checkbox" id="fastvelocity_min_strip_htmlcomments" value="1" <?php echo checked(1 == get_option('fastvelocity_min_strip_htmlcomments'), true, false); ?>>
Strip HTML comments <span class="note-info">[ Only works with the default HTML minification, but note that some plugins need HTML comments to work properly ]</span></label>
<br />

<label for="fastvelocity_fvm_clean_header_one">
<input name="fastvelocity_fvm_clean_header_one" type="checkbox" id="fastvelocity_fvm_clean_header_one" value="1" <?php echo checked(1 == get_option('fastvelocity_fvm_clean_header_one'), true, false); ?>>
Cleanup Header <span class="note-info">[ Remove resource hints, generator tag, shortlinks, manifest link, etc ]</span></label>
<br />

</fieldset></td>
</tr>


<tr>
<th scope="row">Font Options</th>
<td>
<p class="fvm-bold-green fvm-rowintro">The default options are usually good for performance.</p>
<fieldset>
<label for="fastvelocity_min_skip_emoji_removal">
<input name="fastvelocity_min_skip_emoji_removal" type="checkbox" id="fastvelocity_min_skip_emoji_removal" class="jsprocessor" value="1" <?php echo checked(1 == get_option('fastvelocity_min_skip_emoji_removal'), true, false); ?> >
Stop removing Emojis and smileys <span class="note-info">[ If selected, Emojis will be left alone and won't be removed from wordpress ]</span></label>
<br />

<label for="fastvelocity_min_skip_google_fonts">
<input name="fastvelocity_min_skip_google_fonts" type="checkbox" id="fastvelocity_min_skip_google_fonts" value="1" <?php echo checked(1 == get_option('fastvelocity_min_skip_google_fonts'), true, false); ?> >
Disable Google Fonts merging <span class="note-info">[ If selected, Google Fonts will no longer be merged into one request ]</span></label>
<br />

<label for="fastvelocity_min_remove_googlefonts">
<input name="fastvelocity_min_remove_googlefonts" type="checkbox" id="fastvelocity_min_remove_googlefonts" value="1" <?php echo checked(1 == get_option('fastvelocity_min_remove_googlefonts'), true, false); ?> >
Remove Google Fonts completely <span class="note-info">[ If selected, all enqueued Google Fonts will be removed from the site ]</span></label>
<br />

</fieldset></td>
</tr>


<tr>
<th scope="row">Google Fonts</th>
<td>
<?php 
# what to select
$sel = get_option('fastvelocity_gfonts_method');
$a = ''; if($sel == 1 || empty($sel)) { $a = ' checked="checked"'; }
$b = ''; if($sel == 2) { $b = ' checked="checked"'; }
$c = ''; if($sel == 3) { $c = ' checked="checked"'; }
?>
<p class="fvm-bold-green fvm-rowintro">Choose how to include Google Fonts on your pages, when available:</p>
<fieldset>
	<label><input type="radio" name="fastvelocity_gfonts_method" value="1" <?php echo $a; ?>> Inline Google Fonts CSS</label> <span class="note-info">[ Will inline the <a target="_blank" href="https://caniuse.com/#feat=woff">woof</a> format only (with font hinting) ]</span><br>
	<label><input type="radio" name="fastvelocity_gfonts_method" value="2"<?php echo $b; ?>> Async Google Fonts CSS files</label> <span class="note-info">[ Will use <a target="_blank" href="https://caniuse.com/#feat=link-rel-preload">preload</a> with <a href="https://github.com/filamentgroup/loadCSS">LoadCSS</a> polyfill ]</span><br>
	<label><input type="radio" name="fastvelocity_gfonts_method" value="3"<?php echo $c; ?>> Async and exclude Google Fonts CSS from PSI</label> <span class="note-info">[ Will use JavaScript to load the fonts conditionally ] </span><br>
</fieldset>
</td>
</tr>

<tr>
<th scope="row">Font Awesome</th>
<td>
<?php 
# what to select
$sel = get_option('fastvelocity_fontawesome_method');
$a = ''; if($sel == 1 || empty($sel)) { $a = ' checked="checked"'; }
$b = ''; if($sel == 2) { $b = ' checked="checked"'; }
$c = ''; if($sel == 3) { $c = ' checked="checked"'; }
?>
<p class="fvm-bold-green fvm-rowintro">Only if available and if it has "font-awesome" in the url:</p>
<fieldset>
	<label><input type="radio" name="fastvelocity_fontawesome_method" value="1" <?php echo $a; ?>> Merge or Inline Font Awesome CSS</label> <span class="note-info">[ Depends on if you have the Inline CSS option enabled or not ]</span><br>
	<label><input type="radio" name="fastvelocity_fontawesome_method" value="2"<?php echo $b; ?>> Async Font Awesome CSS file</label> <span class="note-info">[ Will use <a target="_blank" href="https://caniuse.com/#feat=link-rel-preload">preload</a> with <a href="https://github.com/filamentgroup/loadCSS">LoadCSS</a> polyfill ]</span><br>
	<label><input type="radio" name="fastvelocity_fontawesome_method" value="3"<?php echo $c; ?>> Async and exclude Font Awesome CSS from PSI</label> <span class="note-info">[ Will use JavaScript to load the fonts conditionally ] </span><br>
</fieldset>
</td>
</tr>


<tr>
<th scope="row">CSS Options</th>
<td>
<p class="fvm-bold-green fvm-rowintro">It's recommended that you Inline all CSS files, if they are small enough.</p>

<fieldset>
<label for="fastvelocity_min_disable_css_merge">
<input name="fastvelocity_min_disable_css_merge" type="checkbox" id="fastvelocity_min_disable_css_merge" value="1" <?php echo checked(1 == get_option('fastvelocity_min_disable_css_merge'), true, false); ?>>
Disable CSS processing<span class="note-info">[ If selected, this plugin will ignore CSS files completely ]</span></label>
<br />
<label for="fastvelocity_min_disable_css_minification">
<input name="fastvelocity_min_disable_css_minification" type="checkbox" id="fastvelocity_min_disable_css_minification" value="1" <?php echo checked(1 == get_option('fastvelocity_min_disable_css_minification'), true, false); ?>>
Disable minification on CSS files <span class="note-info">[ If selected, CSS files will be merged but not minified ]</span></label>
<br />
<label for="fastvelocity_min_skip_cssorder">
<input name="fastvelocity_min_skip_cssorder" type="checkbox" id="fastvelocity_min_skip_cssorder" value="1" <?php echo checked(1 == get_option('fastvelocity_min_skip_cssorder'), true, false); ?> >
Preserve the order of CSS files <span class="note-info">[ If selected, you will have better CSS compatibility when merging but possibly more CSS files ]</span></label>
<br />
<label for="fastvelocity_min_remove_print_mediatypes">
<input name="fastvelocity_min_remove_print_mediatypes" type="checkbox" id="fastvelocity_min_remove_print_mediatypes" value="1" <?php echo checked(1 == get_option('fastvelocity_min_remove_print_mediatypes'), true, false); ?> >
Disable the "Print" related stylesheets <span class="note-info">[ If selected, CSS files of mediatype "print" will be removed from the site ]</span></label>
<br />
<label for="fastvelocity_min_force_inline_css_footer">
<input name="fastvelocity_min_force_inline_css_footer" type="checkbox" id="fastvelocity_min_force_inline_css_footer" value="1" <?php echo checked(1 == get_option('fastvelocity_min_force_inline_css_footer'), true, false); ?>>
Inline CSS in the footer <span class="note-info">[ If selected, any FVM generated CSS files in the footer, will be inlined ]</span></label>
<br />
<label for="fastvelocity_min_force_inline_css">
<input name="fastvelocity_min_force_inline_css" type="checkbox" id="fastvelocity_min_force_inline_css" value="1" <?php echo checked(1 == get_option('fastvelocity_min_force_inline_css'), true, false); ?>>
Inline CSS both in the header and footer <span class="note-info">[ If selected, any FVM generated CSS files (header + footer) will be inlined ]</span></label>
<br />
</fieldset></td>
</tr>


<tr>
<th scope="row">JavaScript Options</th>
<td>
<p class="fvm-bold-green fvm-rowintro">Try to disable minification (and purge the cache) first, if you have trouble with JavaScript in the frontend.</p>
<fieldset>
<label for="fastvelocity_min_disable_js_merge">
<input name="fastvelocity_min_disable_js_merge" type="checkbox" id="fastvelocity_min_disable_js_merge" value="1" <?php echo checked(1 == get_option('fastvelocity_min_disable_js_merge'), true, false); ?> >
Disable JavaScript processing <span class="note-info">[ If selected, this plugin will ignore JS files completely ]</span></label>
<br />

<label for="fastvelocity_min_disable_js_minification">
<input name="fastvelocity_min_disable_js_minification" type="checkbox" id="fastvelocity_min_disable_js_minification" value="1" <?php echo checked(1 == get_option('fastvelocity_min_disable_js_minification'), true, false); ?> >
Disable minification on JS files <span class="note-info">[ If selected, JS files will be merged but not minified ]</span></label>
<br />
</fieldset></td>
</tr>

<tr>
<th scope="row">Render-blocking JS</th>
<td>
<fieldset><legend class="screen-reader-text"><span>Render-blocking</span></legend>

<p class="fvm-bold-green fvm-rowintro">Some themes and plugins "need" render blocking scripts to work, so please take a look at the dev console for errors.</p>
<label for="fastvelocity_min_enable_defer_js">
<input name="fastvelocity_min_enable_defer_js" type="checkbox" id="fastvelocity_min_enable_defer_js" value="1" <?php echo checked(1 == get_option('fastvelocity_min_enable_defer_js'), true, false); ?>>
Enable defer parsing of FVM JS files globally <span class="note-info">[ Not all browsers, themes or plugins support this. Beware of broken functionality and design ]</span></label>
<br />

<label for="fastvelocity_min_exclude_defer_jquery">
<input name="fastvelocity_min_exclude_defer_jquery" type="checkbox" id="fastvelocity_min_exclude_defer_jquery" value="1" <?php echo checked(1 == get_option('fastvelocity_min_exclude_defer_jquery'), true, false); ?> >
Skip deferring the jQuery library <span class="note-info">[ Will probably fix "undefined jQuery" errors on the Google Chrome console log ]</span></label>
<br />
<label for="fastvelocity_min_exclude_defer_login">
<input name="fastvelocity_min_exclude_defer_login" type="checkbox" id="fastvelocity_min_exclude_defer_login" value="1" <?php echo checked(1 == get_option('fastvelocity_min_exclude_defer_login'), true, false); ?> >
Skip deferring JS on the login page <span class="note-info">[ If selected, will disable JS deferring on your login page ]</span></label>
<br />
<label for="fastvelocity_min_skip_defer_lists">
<input name="fastvelocity_min_skip_defer_lists" type="checkbox" id="fastvelocity_min_skip_defer_lists" value="1" <?php echo checked(1 == get_option('fastvelocity_min_skip_defer_lists'), true, false); ?> >
Skip deferring the ignore list <span class="note-info">[ If selected, files on the blacklist, ignore list, etc, won't be deferred ]</span></label>
<br />

</fieldset></td>
</tr>

<tr>
<th scope="row">PageSpeed Settings</th>
<td>
<p class="fvm-bold-green fvm-rowintro">Note that this will overwrite any other behaviour defined above and "may" cause errors.</p>
<fieldset>
<label for="fastvelocity_min_defer_for_pagespeed">
<input name="fastvelocity_min_defer_for_pagespeed" type="checkbox" id="fastvelocity_min_defer_for_pagespeed" value="1" <?php echo checked(1 == get_option('fastvelocity_min_defer_for_pagespeed'), true, false); ?>>
Enable defer of all JS files for PSI only <span class="note-info">[ Will use JavaScript to defer all JS files for PSI ]</span></label>

<br />
<label for="fastvelocity_min_defer_for_pagespeed_optimize">
<input name="fastvelocity_min_defer_for_pagespeed_optimize" type="checkbox" id="fastvelocity_min_defer_for_pagespeed_optimize" value="1" <?php echo checked(1 == get_option('fastvelocity_min_defer_for_pagespeed_optimize'), true, false); ?>>
Exclude JS files in the "ignore list" from PSI <span class="note-info">[ This will hide the "ignored files" from PSI instead of simply deferring ]</span></label>

</fieldset></td>
</tr>

</tbody></table>


<div style="height: 20px;"></div>
<h2 class="title">CDN Options</h2>
<p class="fvm-bold-green">When the "Enable defer of JS for Pagespeed Insights" option is enabled, JS and CSS files will not be loaded from the CDN due to <a target="_blank" href="https://www.chromestatus.com/feature/5718547946799104">compatibility</a> reasons.<br />However, you can define a CDN Domain below, in order to use it for all of the static assets "inside" your CSS and JS files.</p>

<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row"><span class="fvm-label-special">Your CDN domain</span></th>
<td><fieldset>
<label for="fastvelocity_min_fvm_cdn_url">
<p><input type="text" name="fastvelocity_min_fvm_cdn_url" id="fastvelocity_min_fvm_cdn_url" value="<?php echo get_option('fastvelocity_min_fvm_cdn_url', ''); ?>" size="80" /></p>
<p class="description">[ Will rewrite the static assets urls inside FVM merged files to your cdn domain. Usage: cdn.example.com ]</p></label>
</fieldset>
</td>
</tr>

<tr>
<th scope="row">Force the CDN Usage</th>
<td>
<p class="fvm-bold-green fvm-rowintro">If you force this, your JS files may not load for certain slow internet users on Google Chrome.</p>
<fieldset>
<label for="fastvelocity_min_fvm_cdn_force">
<input name="fastvelocity_min_fvm_cdn_force" type="checkbox" id="fastvelocity_min_fvm_cdn_force" value="1" <?php echo checked(1 == get_option('fastvelocity_min_fvm_cdn_force'), true, false); ?>>
I know what I'm doing... <span class="note-info">[ Load my JS files from the CDN, even when "defer for Pagespeed Insights" is enabled ]</span></label>
</fieldset></td>
</tr>

</tbody></table>

<div style="height: 20px;"></div>
<h2 class="title">Cache Location</h2>
<p class="fvm-bold-green">If your server blocks JavaScript on the uploads directory, you can change "wp-content/uploads" with "wp-content/cache" or other allowed public directory.</p>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row"><span class="fvm-label-special">Cache Path</span></th>
<td><fieldset>
<label for="fastvelocity_min_change_cache_path">
<p><input type="text" name="fastvelocity_min_change_cache_path" id="fastvelocity_min_change_cache_path" value="<?php echo get_option('fastvelocity_min_change_cache_path', ''); ?>" size="80" /></p>
<p class="description">[ Default cache path is: <?php echo rtrim(wp_upload_dir()['basedir'], '/'); ?> ]</p>
</label>
<br />
<label for="fastvelocity_min_change_cache_base_url">
<p><input type="text" name="fastvelocity_min_change_cache_base_url" id="fastvelocity_min_change_cache_base_url" value="<?php echo get_option('fastvelocity_min_change_cache_base_url', ''); ?>" size="80" /></p>
<p class="description">[ Default cache base url is: <?php echo rtrim(fvm_get_protocol(wp_upload_dir()['baseurl']), '/'); ?> ]</p>
</label>
</fieldset></td>
</tr>
</tbody></table>


<p class="submit"><input type="submit" name="fastvelocity_min_save_options" id="fastvelocity_min_save_options" class="button button-primary" value="Save Changes"></p>
</form>
<?php } ?>


<?php if( $active_tab == 'pro' ) { ?>

<form method="post" action="options.php">
<?php settings_fields('fvm-group-pro'); do_settings_sections('fvm-group-pro'); ?>


<div style="height: 20px;"></div>
<h2 class="title">Special JS and CSS Exceptions</h2>
<p class="fvm-bold-green">You can use this section to edit or change our default exclusions, as well as to add your own.<br />Make sure you understand the difference between Defer and Async.</p> 
<p class="fvm-bold-green">When you use an option here that uses "Async", styles and scripts load "out of order" and completely independent from the others. That means, the files that end up loading later, will overwrite any previously loaded code. On the other hand, when you use the "ignore list" or when you select an option to "defer", the order of scripts and styles is preserved and should not overwrite previously loaded code, unless there is an higher specificy somewhere else.</p>

<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Ignore List</th>
<td><fieldset>
<label for="blacklist_keys"><span class="fvm-label-pad">Ignore the following CSS and JS paths below:</span></label>
<p>
<textarea name="fastvelocity_min_ignore" rows="7" cols="50" id="fastvelocity_min_ignore" class="large-text code" placeholder="ex: /wp-includes/js/jquery/jquery.js"><?php echo get_option('fastvelocity_min_ignore'); ?></textarea>
</p>
<p class="description">[ Your own list of js /css files to ignore with wildcard support (read the faqs) ]</p>
</fieldset></td>
</tr>
</tbody></table>


<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">External URLs to Merge</th>
<td><fieldset><label for="blacklist_keys"><span class="fvm-label-pad">List of external domains that can be fetched and merged together:</span></label>
<p>
<textarea name="fastvelocity_min_merge_allowed_urls" rows="7" cols="50" id="fastvelocity_min_merge_allowed_urls" class="large-text code" placeholder="ex: example.com"><?php echo get_option('fastvelocity_min_merge_allowed_urls'); ?></textarea>
</p>
<p class="description">[ Add any external "domains" for JS or CSS files than can be merged fetched and merged together by FVM, ie: cdnjs.cloudflare.com ]</p>
</fieldset></td>
</tr>
</tbody></table>


<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Exclude JS files from PSI</th>
<td><fieldset><label for="fastvelocity_min_excludejslist"><span class="fvm-label-pad">Files will be loaded Async and excluded from PSI:</span></label>
<p>
<textarea name="fastvelocity_min_excludejslist" rows="7" cols="50" id="fastvelocity_min_excludejslist" class="large-text code" placeholder="ex: /pixelyoursite/js/public.js"><?php echo get_option('fastvelocity_min_excludejslist'); ?></textarea>
</p>
<p class="description">[ Any JS file that can load Async and completely independent, such as analytics or pixel scripts ]</p>

</fieldset></td>
</tr>
</tbody></table>


<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Exclude CSS files from PSI</th>
<td><fieldset><label for="fastvelocity_min_excludecsslist"><span class="fvm-label-pad">Files will be loaded Async and excluded from PSI:</span></label>
<p>
<textarea name="fastvelocity_min_excludecsslist" rows="7" cols="50" id="fastvelocity_min_excludecsslist" class="large-text code" placeholder="ex: /wp-content/themes/my-theme/css/some-other-font.min.css"><?php echo get_option('fastvelocity_min_excludecsslist'); ?></textarea>
</p>
<p class="description">[ Any CSS file that can load completely independent, such as fontawesome or other icons ]</p>

</fieldset></td>
</tr>
</tbody></table>




<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Default Ignore List</th>
<td>
<fieldset><label for="blacklist_keys"><span class="fvm-label-pad">Do not edit, if you're not sure what this is:</span></label>
<p>
<textarea name="fastvelocity_min_ignorelist" rows="7" cols="50" id="fastvelocity_min_ignorelist" class="large-text code" placeholder="ex: /wp-includes/js/jquery/jquery.js"><?php echo get_option('fastvelocity_min_ignorelist'); ?></textarea>
</p>
<p class="description">[ Files that have been consistently reported by other users to cause trouble when merged ]</p>
</fieldset></td>
</tr>
</tbody></table>

<div style="height: 20px;"></div>
<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Default Blacklist</th>
<td><fieldset><label for="blacklist_keys"><span class="fvm-label-pad">Do not edit, if you're not sure what this is:</span></label>
<p>
<textarea name="fastvelocity_min_blacklist" rows="7" cols="50" id="fastvelocity_min_blacklist" class="large-text code" placeholder="ex: /wp-includes/js/jquery/jquery.js"><?php echo get_option('fastvelocity_min_blacklist'); ?></textarea>
</p>
<p class="description">[ Usually, any IE css /js files that should always be ignored without incrementing the groups ]</p>
</fieldset></td>
</tr>
</tbody></table>


<p class="submit"><input type="submit" name="fastvelocity_min_save_options" id="fastvelocity_min_save_options" class="button button-primary" value="Save Changes"></p>
</form>

<?php 
}

# start developers tab
if( $active_tab == 'dev' ) { ?>

<form method="post" action="options.php">
<?php settings_fields('fvm-group-dev'); do_settings_sections('fvm-group-dev'); ?>

<div style="height: 20px;"></div>
<h2 class="title">Development</h2>
<p class="fvm-bold-green">This are handy things for the plugin author, but may be of use to you if you are looking to debug some issue.</p>
<p class="fvm-bold-green">Please note that the automatic headers, are only available after the first, uncached pageview (you may need to purge your cache to see them, or your server may not support this at all).</p>

<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Dev Options</th>
<td><fieldset>
<label for="fastvelocity_fvm_debug">
<input name="fastvelocity_fvm_debug" type="checkbox" id="fastvelocity_fvm_debug" value="1" <?php echo checked(1 == get_option('fastvelocity_fvm_debug'), true, false); ?>>
Enable FVM Debug Mode<span class="note-info">[ Add extra info to the "status page" logs as well as some comments on the HTML frontend (beta) ]</span></label>

<br />
<label for="fastvelocity_enabled_css_preload">
<input name="fastvelocity_enabled_css_preload" type="checkbox" id="fastvelocity_enabled_css_preload" value="1" <?php echo checked(1 == get_option('fastvelocity_enabled_css_preload'), true, false); ?>>
Enable FVM CSS files Preload<span class="note-info">[ Automatically create http headers for FVM generated CSS files (when not inlined) ]</span></label>

<br />
<label for="fastvelocity_enabled_js_preload">
<input name="fastvelocity_enabled_js_preload" type="checkbox" id="fastvelocity_enabled_js_preload" value="1" <?php echo checked(1 == get_option('fastvelocity_enabled_js_preload'), true, false); ?>>
Enable FVM JS files Preload<span class="note-info">[ Automatically create http headers for FVM generated JS files ]</span></label>


</fieldset>
</td>
</tr>
</tbody></table>






<div style="height: 20px;"></div>
<h2 class="title">HTTP Headers</h2>
<p class="fvm-bold-green">Preconnect Headers: This will add link headers to your http response to instruct the browser to preconnect to other domains (ex: fonts, images, videos, etc)</p>
<p class="fvm-bold-green">Preload Headers: Use this for preloading specific, high priority resources that exist across all of your pages.</p>
<p class="fvm-bold-green">Note: Some servers do not support http push or headers. If you get a server error: a) rename the plugin directory via SFTP or your hosting control panel, b) go to your plugins page (plugin will be disabled on access), c) rename it back and d) activate it back (reset to default settings).</p>

<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Preconnect Headers</th>
<td><fieldset><legend class="screen-reader-text"><span>Preconnect</span></legend>
<label for="fastvelocity_min_hpreconnect"><span class="fvm-label-pad">Use only the strictly minimum necessary domain names, (cdn or frequent embeds):</span></label>
<p>
<textarea name="fastvelocity_min_hpreconnect" rows="7" cols="50" id="fastvelocity_min_hpreconnect" class="large-text code" placeholder="https://cdn.example.com"><?php echo get_option('fastvelocity_min_hpreconnect'); ?></textarea>
</p>
<p class="description">[ Use the complete scheme (http:// or https://) followed by the domain name only (no file paths). ]</p>
<p class="description">[ Examples: ]</p>
<p class="description">https://fonts.googleapis.com</p>
<p class="description">https://fonts.gstatic.com</p>
</fieldset></td>
</tr>

</tbody></table>

<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Preload Headers</th>
<td><fieldset><legend class="screen-reader-text"><span>Preload Headers</span></legend>
<label for="fastvelocity_min_hpreload"><span class="fvm-label-pad">Insert your "complete php header code" here:</span></label>
<p>
<textarea name="fastvelocity_min_hpreload" rows="7" cols="50" id="fastvelocity_min_hpreload" class="large-text code" placeholder="Link: &lt;https://cdn.example.com/s/font/v15/somefile.woff&gt;; rel=preload; as=font; crossorigin"><?php echo get_option('fastvelocity_min_hpreload'); ?></textarea>
</p>
<p class="description">[ Example of a "complete php header code" to paste above ]</p>
<p class="description">Link: &lt;https://fonts.gstatic.com/s/opensans/v15/mem8YaGs126MiZpBA-UFVZ0d.woff&gt;; rel=preload; as=font; crossorigin</p>
<p class="description">Link: &lt;https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2&gt;; rel=preload; as=font; crossorigin</p>
</fieldset></td>
</tr>
</tbody></table>


<div style="height: 20px;"></div>
<h2 class="title">Async CSS</h2>
<p class="fvm-bold-green">If you have multiple css files per media type, they may load out of order and break your design.<br />These options won't work, if you select "Disable CSS Processing" on the settings page. </p>

<table class="form-table fvm-settings">
<tbody>
<tr>
<th scope="row">Enable Async CSS</th>
<td><fieldset>
<label for="fastvelocity_min_loadcss">
<input name="fastvelocity_min_loadcss" type="checkbox" id="fastvelocity_min_loadcss" value="1" <?php echo checked(1 == get_option('fastvelocity_min_loadcss'), true, false); ?>>
Async CSS with LoadCSS <span class="note-info">[ Note that inline CSS won't work if this is active ]</span></label>

<br />
<label for="fastvelocity_min_fvm_removecss">
<input name="fastvelocity_min_fvm_removecss" type="checkbox" id="fastvelocity_min_fvm_removecss" value="1" <?php echo checked(1 == get_option('fastvelocity_min_fvm_removecss'), true, false); ?>>
Dequeue all CSS files <span class="note-info">[ Use this if you want to test how your Critical Path CSS looks like ]</span></label>

</fieldset>
</td>
</tr>

</tbody></table>


<div style="height: 20px;"></div>
<h2 class="title">Critical Path CSS</h2>
<p class="fvm-bold-green">Files that are inlined, or end up loading later (note: async css files load out of order) will overwritte existing styles, which can cause your design to break.</p>
<p class="fvm-bold-green">It's probably better to (uncss) remove all unused CSS on your site, select the "Dequeue all CSS files" and paste your code on the "Fallback CSS" section.</p>
<p class="fvm-bold-green">All code posted here will be inline early in the header, regardless of the "Async CSS with LoadCSS" being active or not (so you can use this to inline your extra css code without other plugins).</p>

<table class="form-table fvm-settings">
<tbody>

<tr>
<th scope="row">Fallback CSS</th>
<td>
<fieldset>
<p>
<textarea name="fastvelocity_min_critical_path_css" rows="7" cols="50" id="fastvelocity_min_critical_path_css" class="large-text code" placeholder="your css code here"><?php echo get_option('fastvelocity_min_critical_path_css'); ?></textarea>
</p>
<p class="description">[ It will be overwritten, if some other more specific critical path code exists below ]</p>
</fieldset>
</td>
</tr>

<tr>
<th scope="row">is_front_page (conditional)</th>
<td>
<fieldset>
<p>
<textarea name="fastvelocity_min_critical_path_css_is_front_page" rows="7" cols="50" id="fastvelocity_min_critical_path_css_is_front_page" class="large-text code" placeholder="your css code here"><?php echo get_option('fastvelocity_min_critical_path_css_is_front_page'); ?></textarea>
</p>
<p class="description">[ Will show up if your page matches the WP conditional, is_front_page() ]</p>
</fieldset>
</td>
</tr>

</tbody></table>



<p class="submit"><input type="submit" name="fastvelocity_min_save_options" id="fastvelocity_min_save_options" class="button button-primary" value="Save Changes"></p>
</form>

<?php 
}


# start server info tab
if( $active_tab == 'server' ) { 
fvm_get_generalinfo();
}


# start help tab
if( $active_tab == 'help' ) { ?>

<div class="wrap" id="fastvelocity-min">
    <div id="poststuff">
        <div id="fastvelocity_min_processed" class="postbox-container">
			<div class="meta-box-sortables ui-sortable">
			
				<div class="postbox" id="tab-info">
                    <h3 class="hndle"><span>Paid Clients / Custom Requests</span></h3>
                    <div class="inside">
					<p>Please Visit: <a href="https://www.upwork.com/fl/fvmpeixoto">https://www.upwork.com/fl/fvmpeixoto</a></p>
					<p>Alternatively: <a href="https://fastvelocity.com/">https://fastvelocity.com/</a></p>
					</div>
                </div>
		
				<div class="postbox" id="tab-info">
                    <h3 class="hndle"><span>Frequently Asked Questions</span></h3>
                    <div class="inside">
					<p>Please Visit: <a href="https://wordpress.org/plugins/fast-velocity-minify/#faq">https://wordpress.org/plugins/fast-velocity-minify/#faq</a></p>
					</div>
                </div>
				
				<div class="postbox" id="tab-info">
                    <h3 class="hndle"><span>Open Source Support / Bug Report</span></h3>
                    <div class="inside">
					<p>Please Visit: <a href="https://wordpress.org/support/plugin/fast-velocity-minify">https://wordpress.org/support/plugin/fast-velocity-minify</a></p>
					</div>
                </div>
				
				<div class="postbox" id="tab-info">
                    <h3 class="hndle"><span>Need faster hosting?</span></h3>
                    <div class="inside">
			<p>Digital Ocean: (aff) <a href="https://m.do.co/c/039860472caf">https://www.digitalocean.com/</a></p>
			<p>Vultr: (aff) <a href="https://www.vultr.com/?ref=6879450">https://www.vultr.com/</a></p>
			<p>Linode: (aff) <a href="https://www.linode.com/?r=4b0ae524a0e54b1c11abb8014be4068f5a5d607a">https://www.linode.com/</a></p>
			<p>Amazon Lightsail: <a href="https://aws.amazon.com/lightsail/">https://aws.amazon.com/lightsail/</a></p>
			<p>Google Cloud: <a href="https://cloud.google.com/">https://cloud.google.com/</a></p>
					</div>
                </div>
				
				<div class="postbox" id="tab-info">
                    <h3 class="hndle"><span>Donations (Thank You)</span></h3>
                    <div class="inside">
					<p>PayPal: <a href="https://goo.gl/vpLrSV">https://goo.gl/vpLrSV</a><br /></p>
					</div>
                </div>
				
            </div>
        </div>
    </div>
</div>

<?php } ?>



</div>

<div class="clear"></div>

<?php
}


###########################################
# process header javascript ###############
###########################################
function fastvelocity_min_merge_header_scripts() {
global $wp_scripts, $wp_domain, $wp_home, $wp_home_path, $cachedir, $cachedirurl, $ignore, $disable_js_merge, $disable_js_minification, $enable_defer_js, $exclude_defer_jquery, $fvm_debug, $fvm_min_excludejslist, $fvmualist;
if(!is_object($wp_scripts)) { return false; }
$scripts = wp_clone($wp_scripts);
$scripts->all_deps($scripts->queue);
$ctime = get_option('fvm-last-cache-update', '0'); 
$header = array();

# mark as done (as we go)
$done = $scripts->done;

# add defaults to ignore list
$ignore = fastvelocity_default_ignore($ignore);

# get groups of handles
foreach( $scripts->to_do as $handle ) :

# is it a footer script?
$is_footer = 0; 
if (isset($wp_scripts->registered[$handle]->extra["group"]) || isset($wp_scripts->registered[$handle]->args)) { 
	$is_footer = 1; 
}

	# skip footer scripts for now
	if($is_footer != 1) {
		
	# get full url
	$hurl = fastvelocity_min_get_hurl($wp_scripts->registered[$handle]->src, $wp_domain, $wp_home);
	
	# inlined scripts without file
	if( empty($hurl)) {
		continue;
	}
	
	# Exclude JS files from PSI (Async) takes priority over the ignore list
	if($fvm_min_excludejslist != false || is_array($fvm_min_excludejslist)) {
		
		# check for string match
		$skipjs = false;
		foreach($fvm_min_excludejslist as $l) {
			if (stripos($hurl, $l) !== false) {
				# print code if there are no linebreaks, or return
				echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
				echo "loadAsync('$hurl', null);";			
				echo '}</script>';
				$skipjs = true;
				break;
			}
		}
		if($skipjs != false) { continue; }
	}
	
	
	# IE only files don't increment things
	$ieonly = fastvelocity_ie_blacklist($hurl);
	if($ieonly == true) { continue; }
	
	# skip ignore list, scripts with conditionals, external scripts
	if ((!fastvelocity_min_in_arrayi($hurl, $ignore) && !isset($wp_scripts->registered[$handle]->extra["conditional"]) && fvm_internal_url($hurl, $wp_home)) || empty($hurl)) {
			
		# process
		if(isset($header[count($header)-1]['handle']) || count($header) == 0) {
			array_push($header, array('handles'=>array()));
		}
			
		# push it to the array
		array_push($header[count($header)-1]['handles'], $handle);

		# external and ignored scripts
	} else { 
		array_push($header, array('handle'=>$handle));
	}
	
	# make sure that the scripts skipped here, show up in the footer
	} else {
		$hurl = fastvelocity_min_get_hurl($wp_scripts->registered[$handle]->src, $wp_domain, $wp_home);
		
		# inlined scripts without file
		if( empty($hurl)) {
			wp_enqueue_script($handle, false);
		} else {
			wp_enqueue_script($handle, $hurl, array(), null, true);
		}
	}
endforeach;

# loop through header scripts and merge
for($i=0,$l=count($header);$i<$l;$i++) {
	if(!isset($header[$i]['handle'])) {
		
		# static cache file info + done
		$done = array_merge($done, $header[$i]['handles']);		
		$hash = 'header-'.hash('adler32',implode('',$header[$i]['handles']));

		# create cache files and urls
		$file = $cachedir.'/'.$hash.'.min.js';
		$file_url = fvm_get_protocol($cachedirurl.'/'.$hash.'.min.js');
		
		# generate a new cache file
		clearstatcache();
		if (!file_exists($file)) {
			
			# code and log initialization
			$log = '';
			$code = '';	
		
			# minify and write to file
			foreach($header[$i]['handles'] as $handle) :
				if(!empty($wp_scripts->registered[$handle]->src)) {
					
					# get hurl per handle
					$hurl = fastvelocity_min_get_hurl($wp_scripts->registered[$handle]->src, $wp_domain, $wp_home);
					
					# inlined scripts without file
					if( empty($hurl)) {
						continue;
					}
					
					# print url
					$printurl = str_ireplace(array(site_url(), home_url(), 'http:', 'https:'), '', $hurl);
					
					# download, minify, cache
					$tkey = 'js-'.hash('adler32', $handle.$hurl).'.js';
					$json = false; $json = fvm_get_transient($tkey);
					if ( $json === false) {
						$json = fvm_download_and_minify($hurl, null, $disable_js_minification, 'js', $handle);
						if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $hurl -->" . PHP_EOL; }
						fvm_set_transient($tkey, $json);
					}
					
					# decode 
					$res = json_decode($json, true);
					
					# response has failed
					if($res['status'] != true) {
						$log.= $res['log'];
						continue;
					}
					
					# append code to merged file
					$code.= $res['code'];
					$log.= $res['log'];
					
					# Add extra data from wp_add_inline_script before
					if (!empty( $wp_scripts->registered[$handle]->extra)) {
						if (!empty( $wp_scripts->registered[$handle]->extra['before'])){
							$code.= PHP_EOL . implode(PHP_EOL, $wp_scripts->registered[$handle]->extra['before']);
						}
					}

				# consider dependencies on handles with an empty src
				} else {
					wp_dequeue_script($handle); wp_enqueue_script($handle);
				}
			endforeach;	
			
			# prepare log
			$log = "PROCESSED on ".date('r').PHP_EOL.$log."PROCESSED from ".home_url(add_query_arg( NULL, NULL )).PHP_EOL;
			
			# generate cache, write log
			if(!empty($code)) {
				file_put_contents($file.'.txt', $log);
				file_put_contents($file, $code);
				file_put_contents($file.'.gz', gzencode(file_get_contents($file), 9));
				
				# permissions
				fastvelocity_fix_permission_bits($file.'.txt');
				fastvelocity_fix_permission_bits($file);
				fastvelocity_fix_permission_bits($file.'.gz');
				
				# brotli static support
				if(function_exists('brotli_compress')) {
					file_put_contents($file.'.br', brotli_compress(file_get_contents($file), 11));
					fastvelocity_fix_permission_bits($file.'.br');
				}
			}
		}
		
		# register minified file
		wp_register_script("fvm-header-$i", $file_url, array(), null, false); 
		
		# add all extra data from wp_localize_script
		$data = array();
		foreach($header[$i]['handles'] as $handle) { 					
			if(isset($wp_scripts->registered[$handle]->extra['data'])) { $data[] = $wp_scripts->registered[$handle]->extra['data']; }
		}
		if(count($data) > 0) { $wp_scripts->registered["fvm-header-$i"]->extra['data'] = implode(PHP_EOL, $data); }
		
		# enqueue file, if not empty
		if(file_exists($file) && (filesize($file) > 0 || count($data) > 0)) {
			wp_enqueue_script("fvm-header-$i");
		} else {
			# file could not be generated, output something meaningful
			echo "<!-- ERROR: FVM was not allowed to save it's cache on - $file -->";
			echo "<!-- Please check if the path above is correct and ensure your server has writting permission there! -->";
			echo "<!-- If you found a bug, please report this on https://wordpress.org/support/plugin/fast-velocity-minify/ -->";
		}
	
	# other scripts need to be requeued for the order of files to be kept
	} else {
		wp_dequeue_script($header[$i]['handle']); 
		wp_enqueue_script($header[$i]['handle']);
	}
}

# remove from queue
$wp_scripts->done = $done;
}
###########################################



###########################################
# process js in the footer ################
###########################################
function fastvelocity_min_merge_footer_scripts() {
global $wp_scripts, $wp_domain, $wp_home, $wp_home_path, $cachedir, $cachedirurl, $ignore, $disable_js_merge, $disable_js_minification, $enable_defer_js, $exclude_defer_jquery, $fvm_debug, $fvm_min_excludejslist, $fvmualist;
if(!is_object($wp_scripts)) { return false; }
$ctime = get_option('fvm-last-cache-update', '0'); 
$scripts = wp_clone($wp_scripts);
$scripts->all_deps($scripts->queue);
$footer = array();

# mark as done (as we go)
$done = $scripts->done;

# add defaults to ignore list
$ignore = fastvelocity_default_ignore($ignore);


# get groups of handles
foreach( $scripts->to_do as $handle ) :

	# get full url
	$hurl = fastvelocity_min_get_hurl($wp_scripts->registered[$handle]->src, $wp_domain, $wp_home);
	
	# inlined scripts without file
	if( empty($hurl)) {
		continue;
	}
	
	# Exclude JS files from PSI (Async) takes priority over the ignore list
	if($fvm_min_excludejslist != false || is_array($fvm_min_excludejslist)) {
		
		# check for string match
		$skipjs = false;
		foreach($fvm_min_excludejslist as $l) {
			if (stripos($hurl, $l) !== false) {
				# print code if there are no linebreaks, or return
				echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
				echo "loadAsync('$hurl', null);";			
				echo '}</script>';
				$skipjs = true;
				break;
			}
		}
		if($skipjs != false) { continue; }
	}
	
	# IE only files don't increment things
	$ieonly = fastvelocity_ie_blacklist($hurl);
	if($ieonly == true) { continue; }
	
	# skip ignore list, scripts with conditionals, external scripts
	if ((!fastvelocity_min_in_arrayi($hurl, $ignore) && !isset($wp_scripts->registered[$handle]->extra["conditional"]) && fvm_internal_url($hurl, $wp_home)) || empty($hurl)) {
			
		# process
		if(isset($footer[count($footer)-1]['handle']) || count($footer) == 0) {
			array_push($footer, array('handles'=>array()));
		}
		
		# push it to the array
		array_push($footer[count($footer)-1]['handles'], $handle);
				
	# external and ignored scripts
	} else { 
		array_push($footer, array('handle'=>$handle));
	}
endforeach;

# loop through footer scripts and merge
for($i=0,$l=count($footer);$i<$l;$i++) {
	if(!isset($footer[$i]['handle'])) {
		
		# static cache file info + done
		$done = array_merge($done, $footer[$i]['handles']);		
		$hash = 'footer-'.hash('adler32',implode('',$footer[$i]['handles']));
		
		# create cache files and urls
		$file = $cachedir.'/'.$hash.'.min.js';
		$file_url = fvm_get_protocol($cachedirurl.'/'.$hash.'.min.js');
	
		# generate a new cache file
		clearstatcache();
		if (!file_exists($file)) {
			
			# code and log initialization
			$log = '';
			$code = '';	
		
			# minify and write to file
			foreach($footer[$i]['handles'] as $handle) :
				if(!empty($wp_scripts->registered[$handle]->src)) {
					
					# get hurl per handle
					$hurl = fastvelocity_min_get_hurl($wp_scripts->registered[$handle]->src, $wp_domain, $wp_home);
					
					# inlined scripts without file
					if( empty($hurl)) {
						continue;
					}
					
					# print url
					$printurl = str_ireplace(array(site_url(), home_url(), 'http:', 'https:'), '', $hurl);
					
					
					# download, minify, cache
					$tkey = 'js-'.hash('adler32', $handle.$hurl).'.js';
					$json = false; $json = fvm_get_transient($tkey);
					if ( $json === false) {
						$json = fvm_download_and_minify($hurl, null, $disable_js_minification, 'js', $handle);
						if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $hurl -->" . PHP_EOL; }
						fvm_set_transient($tkey, $json);
					}
					
					# decode 
					$res = json_decode($json, true);
					
					# response has failed
					if($res['status'] != true) {
						$log.= $res['log'];
						continue;
					}
					
					# append code to merged file
					$code.= $res['code'];
					$log.= $res['log'];
					
					# Add extra data from wp_add_inline_script before
					if (!empty($wp_scripts->registered[$handle]->extra)){
						if (!empty($wp_scripts->registered[$handle]->extra['before'])){
							$code.= PHP_EOL.implode(PHP_EOL, $wp_scripts->registered[$handle]->extra['before']);
						}
					}
			
				# consider dependencies on handles with an empty src
				} else {
					wp_dequeue_script($handle); 
					wp_enqueue_script($handle);
				}
			endforeach;	
			
			# prepare log
			$log = "PROCESSED on ".date('r').PHP_EOL.$log."PROCESSED from ".home_url(add_query_arg( NULL, NULL )).PHP_EOL;
		
			# generate cache, write log
			if(!empty($code)) {
				file_put_contents($file.'.txt', $log);
				file_put_contents($file, $code);
				file_put_contents($file.'.gz', gzencode(file_get_contents($file), 9));
				
				# permissions
				fastvelocity_fix_permission_bits($file.'.txt');
				fastvelocity_fix_permission_bits($file);
				fastvelocity_fix_permission_bits($file.'.gz');
				
				# brotli static support
				if(function_exists('brotli_compress')) {
					file_put_contents($file.'.br', brotli_compress(file_get_contents($file), 11));
					fastvelocity_fix_permission_bits($file.'.br');
				}
			}
		}
		
		# register minified file
		wp_register_script("fvm-footer-$i", $file_url, array(), null, false); 
		
		# add all extra data from wp_localize_script
		$data = array();
		foreach($footer[$i]['handles'] as $handle) { 					
			if(isset($wp_scripts->registered[$handle]->extra['data'])) { $data[] = $wp_scripts->registered[$handle]->extra['data']; }
		}
		if(count($data) > 0) { $wp_scripts->registered["fvm-footer-$i"]->extra['data'] = implode(PHP_EOL, $data); }
		
		# enqueue file, if not empty
		if(file_exists($file) && (filesize($file) > 0 || count($data) > 0)) {
			wp_enqueue_script("fvm-footer-$i");
		} else {
			# file could not be generated, output something meaningful
			echo "<!-- ERROR: FVM was not allowed to save it's cache on - $file -->";
			echo "<!-- Please check if the path above is correct and ensure your server has writting permission there! -->";
			echo "<!-- If you found a bug, please report this on https://wordpress.org/support/plugin/fast-velocity-minify/ -->";
		}
	
	# other scripts need to be requeued for the order of files to be kept
	} else {
		wp_dequeue_script($footer[$i]['handle']); wp_enqueue_script($footer[$i]['handle']);
	}
}

# remove from queue
$wp_scripts->done = $done;
}
##############################



###########################################
# enable defer for JavaScript (WP 4.1 and above) and remove query strings for ignored files
###########################################
function fastvelocity_min_defer_js($tag, $handle, $src) {
global $ignore, $blacklist, $ignorelist, $enable_defer_js, $defer_for_pagespeed, $wp_domain, $exclude_defer_login, $fvm_fix_editor, $fvmualist, $defer_for_pagespeed_optimize, $exclude_defer_jquery, $skip_defer_lists;

# no query strings
$tag = trim($tag); # must cleanup
if (stripos($src, '?ver') !== false) { 
	$srcf = stristr($src, '?ver', true); 
	$tag = str_ireplace($src, $srcf, $tag); 
	$src = $srcf; 
}


# fix page editors, admin, amp, etc
if (is_admin() || is_preview() || is_customize_preview() || ($fvm_fix_editor == true && is_user_logged_in()) || (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint())) { return $tag; }

# return if defer option is not selected
if ($defer_for_pagespeed != true && $enable_defer_js != true) { return $tag; }

# Skip deferring the jQuery library option
if($exclude_defer_jquery != false && (stripos($tag, '/jquery.js') !== false || stripos($tag, '/jquery.min.js') !== false || (stripos($tag, '/jquery-') !== false && stripos($tag, '.js') !== false))) {
	return $tag;
}

# return if external script url https://www.chromestatus.com/feature/5718547946799104
if (fvm_is_local_domain($src) !== true) { return $tag; }

# bypass if there are linebreaks (will break document.write) or already being optimized
if (stripos($tag, PHP_EOL) !== false || stripos($tag, 'navigator.userAgent.match') !== false) { 
	return $tag;
}

# should we exclude defer on the login page?
if($exclude_defer_login == true && stripos($_SERVER["SCRIPT_NAME"], strrchr(wp_login_url(), '/')) !== false){ 
	return $tag; 
}

# add defer attribute, but only if not having async or defer already
if (stripos($tag, 'defer') === false && stripos($tag, 'async') === false) {
	
	# defer tag globally
	$jsdefer = str_ireplace('<script ', '<script defer ', $tag);
	$jsdeferpsi = $jsdefer;
	
	# add cdn for PSI
	$fvm_cdn_url = get_option('fastvelocity_min_fvm_cdn_url');
	if(!empty($fvm_cdn_url)) {
		$fvm_cdn_url = trim(trim(str_ireplace(array('http://', 'https://'), '', trim($fvm_cdn_url, '/'))), '/');
		$jsdeferpsi = str_ireplace($src, $fvm_cdn_url, $jsdefer);
	}
	
	
	# defer tag for PSI only
	$jsdeferpsionly = '<script type="text/javascript">if(navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){document.write('.fastvelocity_escape_url_js($jsdeferpsi).');}else{document.write('.fastvelocity_escape_url_js($tag).');}</script>';
	
	# hide tag from PSI
	$jsdeferhidepsi = '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){document.write('.fastvelocity_escape_url_js($tag).');}</script>';
	
	# must return by this order...
	
	# remove FVM from the ignore list
	array_filter($ignore, function ($var) { return (stripos($var, '/fvm/') === false); });
	
	# Exclude JS files in the "ignore list" from PSI
	if($defer_for_pagespeed_optimize != false) {
		if((count($ignore) > 0 && fastvelocity_min_in_arrayi($src, $ignore)) || (count($blacklist) > 0 && fastvelocity_min_in_arrayi($src, $blacklist)) || (count($ignorelist) > 0 && fastvelocity_min_in_arrayi($src, $ignorelist))) {
			return $jsdeferhidepsi;
		}
	}
	
	# Enable defer of JS files for PSI
	if($defer_for_pagespeed != false) {
		return $jsdeferpsionly;
	}
	
	# Enable defer parsing of FVM JS files globally
	if($enable_defer_js == true) {
		
		# consider "Skip deferring the ignore list"
		if($skip_defer_lists != false && ((count($ignore) > 0 && fastvelocity_min_in_arrayi($src, $ignore)) || (count($blacklist) > 0 && fastvelocity_min_in_arrayi($src, $blacklist)) || (count($ignorelist) > 0 && fastvelocity_min_in_arrayi($src, $ignorelist)))) {
			return $tag;
		} else {
			return $jsdefer;
		}
	}

}

# fallback
return $tag;
}
###########################################


###########################################
# process header css ######################
###########################################
function fastvelocity_min_merge_header_css() {
global $wp_styles, $wp_domain, $wp_home, $wp_home_path, $cachedir, $cachedirurl, $ignore, $disable_css_merge, $disable_css_minification, $skip_google_fonts, $skip_cssorder, $remove_print_mediatypes, $force_inline_googlefonts, $css_hide_googlefonts, $min_async_googlefonts, $remove_googlefonts, $fvmloadcss, $fvm_remove_css, $fvmualist, $fvm_min_excludecsslist, $fvm_debug, $fvm_min_excludecsslist, $fvm_fawesome_method;
if(!is_object($wp_styles)) { return false; }
$ctime = get_option('fvm-last-cache-update', '0'); 
$styles = wp_clone($wp_styles);
$styles->all_deps($styles->queue);
$done = $styles->done;
$header = array();
$google_fonts = array();
$process = array();
$inline_css = array();
$log = '';

# dequeue all styles
if($fvm_remove_css != false) {
	foreach( $styles->to_do as $handle ) :
		$done = array_merge($done, array($handle));
	endforeach;
	
	# remove from queue
	$wp_styles->done = $done;
	return false;
}

# add defaults to ignore list
$ignore = fastvelocity_default_ignore($ignore);

# get list of handles to process, dequeue duplicate css urls and keep empty source handles (for dependencies)
$uniq = array(); $gfonts = array();
foreach( $styles->to_do as $handle):

	# conditionals
	$conditional = NULL; if(isset($wp_styles->registered[$handle]->extra["conditional"])) { 
		$conditional = $wp_styles->registered[$handle]->extra["conditional"]; # such as ie7, ie8, ie9, etc
	}
	
	# mediatype
	$mt = isset($wp_styles->registered[$handle]->args) ? $wp_styles->registered[$handle]->args : 'all';
	if ($mt == 'screen' || $mt == 'screen, print' || empty($mt) || is_null($mt) || $mt == false) { $mt = 'all'; } 
	$mediatype = $mt;
	
	# full url or empty
	$hurl = fastvelocity_min_get_hurl($wp_styles->registered[$handle]->src, $wp_domain, $wp_home);
	
	# inlined scripts without file
	if( empty($hurl)) {
		continue;
	}
	
	# mark duplicates as done and remove from the queue
	if(!empty($hurl)) {
		$key = hash('adler32', $hurl); 
		if (isset($uniq[$key])) { $done = array_merge($done, array($handle)); continue; } else { $uniq[$key] = $handle; }
	}
	
	# Exclude specific CSS files from PSI?
	if($fvm_min_excludecsslist != false && is_array($fvm_min_excludecsslist) && fastvelocity_min_in_arrayi($hurl, $fvm_min_excludecsslist)) {
		$cssguid = 'fvm'.hash('adler32', $hurl);
		echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
		echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$hurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$mediatype.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
		echo '}</script>';
		$done = array_merge($done, array($handle)); continue;
	}
	
	# font awesome processing, async css
	if($fvm_fawesome_method == 2 && stripos($hurl, 'font-awesome') !== false) {
		echo '<link rel="preload" href="'.$hurl.'" as="style" media="'.$mediatype.'" onload="this.onload=null;this.rel=\'stylesheet\'" />';
		echo '<noscript><link rel="stylesheet" href="'.$hurl.'" media="'.$mediatype.'" /></noscript>';
		echo '<!--[if IE]><link rel="stylesheet" href="'.$hurl.'" media="'.$mediatype.'" /><![endif]-->';
		$done = array_merge($done, array($handle)); continue;
	}	
	
	# font awesome processing, async and exclude from PSI
	if($fvm_fawesome_method == 3 && stripos($hurl, 'font-awesome') !== false) {
		$cssguid = 'fvm'.hash('adler32', $hurl);
		echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
		echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$hurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$mediatype.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
		echo '}</script>';
		$done = array_merge($done, array($handle)); continue;
	}
	
	# array of info to save
	$arr = array('handle'=>$handle, 'url'=>$hurl, 'conditional'=>$conditional, 'mediatype'=>$mediatype);
	
	# google fonts to the top (collect and skip process array)
	if (stripos($hurl, 'fonts.googleapis.com') !== false) { 
	if($remove_googlefonts != false) { $done = array_merge($done, array($handle)); continue; } # mark as done if to be removed
	if($skip_google_fonts != true || $force_inline_googlefonts != false) { 
		$google_fonts[$handle] = $hurl; 

	} else {
		wp_enqueue_style($handle); # skip google fonts optimization?
	}
	continue; 
	} 
	
	# all else
	$process[$handle] = $arr;

endforeach;


# concat google fonts, if enabled
if(!$skip_google_fonts && count($google_fonts) > 0 || ($force_inline_googlefonts != false && count($google_fonts) > 0)) {
	foreach ($google_fonts as $h=>$a) { $done = array_merge($done, array($h)); } # mark as done
	
	# merge google fonts if force inlining is enabled?
	$nfonts = array();
	if($skip_google_fonts != true) {
		$nfonts[] = fvm_get_protocol(fastvelocity_min_concatenate_google_fonts($google_fonts));
	} else {
		foreach ($google_fonts as $a) { if(!empty($a)) { $nfonts[] = $a; } }
	}
	
	# foreach google font (will be one if merged is not disabled)
	if(count($nfonts) > 0) {
		foreach($nfonts as $gfurl) {
	
			# hide from PSI, async, inline, or default
			if($css_hide_googlefonts == true) {
				
				# make a stylesheet, hide from PSI
				$cssguid = 'fvm'.hash('adler32', $gfurl);
				echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
				echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$gfurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="all"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
				echo '}</script>';	
				
			# async CSS
			} elseif ($min_async_googlefonts == true) {
				echo '<link rel="preload" href="'.$gfurl.'" as="style" media="all" onload="this.onload=null;this.rel=\'stylesheet\'" />';
				echo '<noscript><link rel="stylesheet" href="'.$gfurl.'" media="all" /></noscript>';
				echo '<!--[if IE]><link rel="stylesheet" href="'.$gfurl.'" media="all" /><![endif]-->';
			
			# inline css
			} elseif($force_inline_googlefonts == true) {
				
				# download, minify, cache
				$tkey = 'css-'.hash('adler32', $gfurl).'.css';
				$json = false; $json = fvm_get_transient($tkey);
				if ( $json === false) {
					$json = fvm_download_and_minify($gfurl, null, $disable_css_minification, 'css', null);
					if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $gfurl -->" . PHP_EOL; }
					fvm_set_transient($tkey, $json);
				}
				
				# decode 
				$res = json_decode($json, true);
				
				# response has failed
				if($res['status'] != true) {
					$log.= $res['log'];
					continue;
				}
				
				# inline css or fail
				if($res['code'] !== false) { 
				
					# add font-display
					# https://developers.google.com/web/updates/2016/02/font-display
					$res['code'] = str_ireplace('font-style:normal;', 'font-display:block;font-style:normal;', $res['code']);
					
					# inline
					echo '<style type="text/css" media="all">'.$res['code'].'</style>'.PHP_EOL;				
				} else {
					echo "<!-- GOOGLE FONTS REQUEST FAILED for $gfurl -->\n";
				}
				
			# fallback, enqueue google fonts
			} else {
				wp_enqueue_style('header-fvm-fonts', $gfurl, array(), null, 'all');
			}
			
		}
	}
}


# get groups of handles
foreach( $styles->to_do as $handle ) :

# skip already processed google fonts and empty dependencies
if(isset($google_fonts[$handle])) { continue; }                     # skip google fonts
if(empty($wp_styles->registered[$handle]->src)) { continue; } 		# skip empty src
if (fastvelocity_min_in_arrayi($handle, $done)) { continue; }       # skip if marked as done before
if (!isset($process[$handle])) { continue; } 						# skip if not on our unique process list

# get full url
$hurl = $process[$handle]['url'];
$conditional = $process[$handle]['conditional'];
$mediatype = $process[$handle]['mediatype'];

	# IE only files don't increment things
	$ieonly = fastvelocity_ie_blacklist($hurl);
	if($ieonly == true) { continue; }
	
	# skip ignore list, conditional css, external css, font-awesome merge
	if ( (!fastvelocity_min_in_arrayi($hurl, $ignore) && !isset($conditional) && fvm_internal_url($hurl, $wp_home)) 
		|| empty($hurl) 
		|| ($fvm_fawesome_method == 1 && stripos($hurl, 'font-awesome') !== false)) {
	
	# colect inline css for this handle
	if(isset($wp_styles->registered[$handle]->extra['after']) && is_array($wp_styles->registered[$handle]->extra['after'])) { 
		$inline_css[$handle] = fastvelocity_min_minify_css_string(implode('', $wp_styles->registered[$handle]->extra['after'])); # save
		$wp_styles->registered[$handle]->extra['after'] = null; # dequeue
	}	
	
	# process
	if(isset($header[count($header)-1]['handle']) || count($header) == 0 || $header[count($header)-1]['media'] != $mediatype) {
		array_push($header, array('handles'=>array(), 'media'=>$mediatype)); 
	}
	
	# push it to the array
	array_push($header[count($header)-1]['handles'], $handle);

	# external and ignored css
	} else {
		
		# normal enqueuing
		array_push($header, array('handle'=>$handle));
	}
endforeach;

# reorder CSS by mediatypes
if(!$skip_cssorder) {
	if(count($header) > 0) {

		# get unique mediatypes
		$allmedia = array(); 
		foreach($header as $array) { 
			if(isset($array['media'])) { $allmedia[$array['media']] = ''; } 
		}
		
		# extract handles by mediatype
		$grouphandles = array(); 
		foreach ($allmedia as $md=>$var) { 
			foreach($header as $array) { 
				if (isset($array['media']) && $array['media'] === $md) { 
					foreach($array['handles'] as $h) { $grouphandles[$md][] = $h; } 
				} 
			} 
		}

		# reset and reorder header by mediatypes
		$newheader = array();
		foreach ($allmedia as $md=>$var) { $newheader[] = array('handles' => $grouphandles[$md], 'media'=>$md); }
		if(count($newheader) > 0) { $header = $newheader; }
	}
}

# loop through header css and merge
for($i=0,$l=count($header);$i<$l;$i++) {
	if(!isset($header[$i]['handle'])) {
		
		# get has for the inline css in this group
		$inline_css_group = array();
		foreach($header[$i]['handles'] as $h) { if(isset($inline_css[$h]) && !empty($inline_css[$h])) { $inline_css_group[] = $inline_css[$h]; } }
		$inline_css_hash = md5(implode('',$inline_css_group));
		
		# static cache file info + done
		$done = array_merge($done, $header[$i]['handles']);		
		$hash = 'header-'.hash('adler32',implode('',$header[$i]['handles']).$inline_css_hash);

		# create cache files and urls
		$file = $cachedir.'/'.$hash.'.min.css';
		$file_url = fvm_get_protocol($cachedirurl.'/'.$hash.'.min.css'); 
		
		# generate a new cache file
		clearstatcache();
		if (!file_exists($file)) {
			
			# code and log initialization
			$log = '';
			$code = '';	
		
			# minify and write to file
			foreach($header[$i]['handles'] as $handle) :
				if(!empty($wp_styles->registered[$handle]->src)) {
					
					# get hurl per handle
					$hurl = fastvelocity_min_get_hurl($wp_styles->registered[$handle]->src, $wp_domain, $wp_home);
					
					# inlined scripts without file
					if( empty($hurl)) {
						continue;
					}
					
					# print url
					$printurl = str_ireplace(array(site_url(), home_url(), 'http:', 'https:'), '', $hurl);
					
					# download, minify, cache
					$tkey = 'css-'.hash('adler32', $handle.$hurl).'.css';
					$json = false; $json = fvm_get_transient($tkey);
					if ( $json === false) {
						$json = fvm_download_and_minify($hurl, null, $disable_css_minification, 'css', $handle);
						if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $hurl -->" . PHP_EOL; }
						fvm_set_transient($tkey, $json);
					}
					
					# decode 
					$res = json_decode($json, true);
					
					# response has failed
					if($res['status'] != true) {
						$log.= $res['log'];
						continue;
					}
					
					# append code to merged file
					$code.= $res['code'];
					$log.= $res['log'];
					
					# append inlined styles
					if(isset($inline_css[$handle]) && !empty($inline_css[$handle])) { 
						$code.= $inline_css[$handle]; 
					}
				
				# consider dependencies on handles with an empty src
				} else {
					wp_dequeue_script($handle); 
					wp_enqueue_script($handle);
				}
			endforeach;	
			
			# prepare log
			$log = "PROCESSED on ".date('r').PHP_EOL.$log."PROCESSED from ".home_url(add_query_arg( NULL, NULL )).PHP_EOL;
			
			# generate cache, write log
			if(!empty($code)) {
				file_put_contents($file.'.txt', $log);
				file_put_contents($file, $code);
				file_put_contents($file.'.gz', gzencode(file_get_contents($file), 9));
				
				# permissions
				fastvelocity_fix_permission_bits($file.'.txt');
				fastvelocity_fix_permission_bits($file);
				fastvelocity_fix_permission_bits($file.'.gz');
				
				# brotli static support
				if(function_exists('brotli_compress')) {
					file_put_contents($file.'.br', brotli_compress(file_get_contents($file), 11));
					fastvelocity_fix_permission_bits($file.'.br');
				}
			}
		}
		
		# register and enqueue minified file, consider excluding of mediatype "print" and inline css
		if ($remove_print_mediatypes != true || ($remove_print_mediatypes == true && $header[$i]['media'] != 'print')) {

			# the developers tab, takes precedence
			
			# Async CSS with loadCSS ?
			if($fvmloadcss != false && $fvm_remove_css != true) {
				$mt = $header[$i]['media'];
				echo '<link rel="preload" href="'.$file_url.'" as="style" media="'.$mt.'" onload="this.onload=null;this.rel=\'stylesheet\'" />';
				echo '<noscript><link rel="stylesheet" href="'.$file_url.'" media="'.$mt.'" /></noscript>';
				echo '<!--[if IE]><link rel="stylesheet" href="'.$file_url.'" media="'.$mt.'" /><![endif]-->';
			
			# enqueue file, if not empty
			} else {
				if(file_exists($file) && filesize($file) > 0) {
					
					# inline CSS if mediatype is not of type "all" (such as mobile only), if the file is smaller than 20KB
					if(filesize($file) < 20000 && $header[$i]['media'] != 'all') {
						echo '<style id="fvm-header-'.$i.'" media="'.$header[$i]['media'].'">'.file_get_contents($file).'</style>';
					} else {
						# enqueue it
						wp_enqueue_style("fvm-header-$i", $file_url, array(), null, $header[$i]['media']);  
					}
				} else {
					# file could not be generated, output something meaningful
					echo "<!-- ERROR: FVM was not allowed to save it's cache on - $file -->";
					echo "<!-- Please check if the path above is correct and ensure your server has writting permission there! -->";
					echo "<!-- If you found a bug, please report this on https://wordpress.org/support/plugin/fast-velocity-minify/ -->";
				}
			}
		}

	# other css need to be requeued for the order of files to be kept
	} else {
		wp_dequeue_style($header[$i]['handle']); 
		wp_enqueue_style($header[$i]['handle']);
	}
}

# remove from queue
$wp_styles->done = $done;

}
###########################################


###########################################
# process css in the footer ###############
###########################################
function fastvelocity_min_merge_footer_css() {
global $wp_styles, $wp_domain, $wp_home, $wp_home_path, $cachedir, $cachedirurl, $ignore, $disable_css_merge, $disable_css_minification, $skip_google_fonts, $skip_cssorder, $remove_print_mediatypes, $force_inline_googlefonts, $css_hide_googlefonts, $min_async_googlefonts, $remove_googlefonts, $fvmloadcss, $fvm_remove_css, $fvmualist, $fvm_debug, $fvm_fawesome_method, $fvm_min_excludecsslist, $force_inline_css_footer;

if(!is_object($wp_styles)) { return false; }
$ctime = get_option('fvm-last-cache-update', '0'); 
$styles = wp_clone($wp_styles);
$styles->all_deps($styles->queue);
$done = $styles->done;
$footer = array();
$google_fonts = array();
$inline_css = array();

# dequeue all styles
if($fvm_remove_css != false) {
	foreach( $styles->to_do as $handle ) :
		$done = array_merge($done, array($handle));
	endforeach;
	
	# remove from queue
	$wp_styles->done = $done;
	return false;
}


# add defaults to ignore list
$ignore = fastvelocity_default_ignore($ignore);

# google fonts to the top
foreach( $styles->to_do as $handle ) :

	# dequeue and get a list of google fonts, or requeue external
	$hurl = fastvelocity_min_get_hurl($wp_styles->registered[$handle]->src, $wp_domain, $wp_home);
	
	# inlined scripts without file
	if( empty($hurl)) {
		continue;
	}
	
	if (stripos($hurl, 'fonts.googleapis.com') !== false) { 
		wp_dequeue_style($handle); 
		if($remove_googlefonts != false) { $done = array_merge($done, array($handle)); continue; } # mark as done if to be removed
		if($skip_google_fonts != true || $force_inline_googlefonts != false) { 
			$google_fonts[$handle] = $hurl; 
		} else {
			wp_enqueue_style($handle); # skip google fonts optimization?
		}
	} else { 
		wp_dequeue_style($handle); wp_enqueue_style($handle); # failsafe
	}
	
endforeach;


# concat google fonts, if enabled
if(!$skip_google_fonts && count($google_fonts) > 0 || ($force_inline_googlefonts != false && count($google_fonts) > 0)) {
	foreach ($google_fonts as $h=>$a) { $done = array_merge($done, array($h)); } # mark as done
	
	# merge google fonts if force inlining is enabled?
	$nfonts = array();
	if($skip_google_fonts != true) {
		$nfonts[] = fvm_get_protocol(fastvelocity_min_concatenate_google_fonts($google_fonts));
	} else {
		foreach ($google_fonts as $a) { if(!empty($a)) { $nfonts[] = $a; } }
	}
	
	# foreach google font (will be one if merged is not disabled)
	if(count($nfonts) > 0) {
		foreach($nfonts as $gfurl) {
	
			# hide from PSI, async, inline, or default
			if($css_hide_googlefonts == true) {
				
				# make a stylesheet, hide from PSI
				$cssguid = 'fvm'.hash('adler32', $gfurl);
				echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
				echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$gfurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="all"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
				echo '}</script>';	
				
			# async CSS
			} elseif ($min_async_googlefonts == true) {
				echo '<link rel="preload" href="'.$gfurl.'" as="style" media="all" onload="this.onload=null;this.rel=\'stylesheet\'" />';
				echo '<noscript><link rel="stylesheet" href="'.$gfurl.'" media="all" /></noscript>';
				echo '<!--[if IE]><link rel="stylesheet" href="'.$gfurl.'" media="all" /><![endif]-->';
			
			# inline css
			} elseif($force_inline_googlefonts == true) {
				
				# download, minify, cache
				$tkey = 'css-'.hash('adler32', $gfurl).'.css';
				$json = false; $json = fvm_get_transient($tkey);
				if ( $json === false) {
					$json = fvm_download_and_minify($gfurl, null, $disable_css_minification, 'css', null);
					if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $gfurl -->" . PHP_EOL; }
					fvm_set_transient($tkey, $json);
				}
				
				# decode 
				$res = json_decode($json, true);
				
				# response has failed
				if($res['status'] != true) {
					$log.= $res['log'];
					continue;
				}
				
				# append code to merged file
				$code.= $res['code'];
				$log.= $res['log'];
				
				# inline css or fail
				if($res['code'] !== false) { 
					echo '<style type="text/css" media="all">'.$res['code'].'</style>'.PHP_EOL;				
				} else {
					echo "<!-- GOOGLE FONTS REQUEST FAILED for $gfurl -->\n";
				}
				
			# fallback, enqueue google fonts
			} else {
				wp_enqueue_style('footer-fvm-fonts', $gfurl, array(), null, 'all');
			}
			
		}
	}
}


# get groups of handles
$uniq = array(); 
foreach( $styles->to_do as $handle ) :

	# skip already processed google fonts
	if(isset($google_fonts[$handle])) { continue; }
	
	# conditionals
	$conditional = NULL; if(isset($wp_styles->registered[$handle]->extra["conditional"])) { 
		$conditional = $wp_styles->registered[$handle]->extra["conditional"]; # such as ie7, ie8, ie9, etc
	}
	
	# mediatype
	$mt = isset($wp_styles->registered[$handle]->args) ? $wp_styles->registered[$handle]->args : 'all';
	if ($mt == 'screen' || $mt == 'screen, print' || empty($mt) || is_null($mt) || $mt == false) { $mt = 'all'; } 
	$mediatype = $mt;
	
	# get full url
	$hurl = fastvelocity_min_get_hurl($wp_styles->registered[$handle]->src, $wp_domain, $wp_home);
	
	# inlined scripts without file
	if( empty($hurl)) {
		continue;
	}
	
	# mark duplicates as done and remove from the queue
	if(!empty($hurl)) {
		$key = hash('adler32', $hurl); 
		if (isset($uniq[$key])) { $done = array_merge($done, array($handle)); continue; } else { $uniq[$key] = $handle; }
	}
	
	# IE only files don't increment things
	$ieonly = fastvelocity_ie_blacklist($hurl);
	if($ieonly == true) { continue; }
	
	
	# Exclude specific CSS files from PSI?
	if($fvm_min_excludecsslist != false && is_array($fvm_min_excludecsslist) && fastvelocity_min_in_arrayi($hurl, $fvm_min_excludecsslist)) {
		$cssguid = 'fvm'.hash('adler32', $hurl);
		echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
		echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$hurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$mediatype.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
		echo '}</script>';
		$done = array_merge($done, array($handle)); continue;
	}
	
	# font awesome processing, async css
	if($fvm_fawesome_method == 2 && stripos($hurl, 'font-awesome') !== false) {
		echo '<link rel="preload" href="'.$hurl.'" as="style" media="'.$mediatype.'" onload="this.onload=null;this.rel=\'stylesheet\'" />';
		echo '<noscript><link rel="stylesheet" href="'.$hurl.'" media="'.$mediatype.'" /></noscript>';
		echo '<!--[if IE]><link rel="stylesheet" href="'.$hurl.'" media="'.$mediatype.'" /><![endif]-->';
		$done = array_merge($done, array($handle)); continue;
	}	
	
	# font awesome processing, async and exclude from PSI
	if($fvm_fawesome_method == 3 && stripos($hurl, 'font-awesome') !== false) {
		$cssguid = 'fvm'.hash('adler32', $hurl);
		echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
		echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$hurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$mediatype.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
		echo '}</script>';
		$done = array_merge($done, array($handle)); continue;
	}
	
	# skip ignore list, conditional css, external css, font-awesome merge
	if ( (!fastvelocity_min_in_arrayi($hurl, $ignore) && !isset($conditional) && fvm_internal_url($hurl, $wp_home)) 
		|| empty($hurl) 
		|| ($fvm_fawesome_method == 1 && stripos($hurl, 'font-awesome') !== false)) {
			
		# colect inline css for this handle
		if(isset($wp_styles->registered[$handle]->extra['after']) && is_array($wp_styles->registered[$handle]->extra['after'])) { 
			$inline_css[$handle] = fastvelocity_min_minify_css_string(implode('', $wp_styles->registered[$handle]->extra['after'])); # save
			$wp_styles->registered[$handle]->extra['after'] = null; # dequeue
		}	
			
		# process
		if(isset($footer[count($footer)-1]['handle']) || count($footer) == 0 || $footer[count($footer)-1]['media'] != $wp_styles->registered[$handle]->args) {
			array_push($footer, array('handles'=>array(),'media'=>$mediatype));
		}
	
		# push it to the array get latest modified time
		array_push($footer[count($footer)-1]['handles'], $handle);
		
	# external and ignored css
	} else {
		
		# normal enqueueing
		array_push($footer, array('handle'=>$handle));
	}
endforeach;


# reorder CSS by mediatypes
if(!$skip_cssorder) {
	if(count($footer) > 0) {

		# get unique mediatypes
		$allmedia = array(); 
		foreach($footer as $key=>$array) { 
			if(isset($array['media'])) { $allmedia[$array['media']] = ''; } 
		}

		# extract handles by mediatype
		$grouphandles = array(); 
		foreach ($allmedia as $md=>$var) { 
			foreach($footer as $array) { 
				if (isset($array['media']) && $array['media'] === $md) { 
					foreach($array['handles'] as $h) { $grouphandles[$md][] = $h; } 
				} 
			} 
		}

		# reset and reorder footer by mediatypes
		$newfooter = array();
		foreach ($allmedia as $md=>$var) { $newfooter[] = array('handles' => $grouphandles[$md], 'media'=>$md); }
		if(count($newfooter) > 0) { $footer = $newfooter; }
	}
}

# loop through footer css and merge
for($i=0,$l=count($footer);$i<$l;$i++) {
	if(!isset($footer[$i]['handle'])) {
		
		# get has for the inline css in this group
		$inline_css_group = array();
		foreach($footer[$i]['handles'] as $h) { if(isset($inline_css[$h]) && !empty($inline_css[$h])) { $inline_css_group[] = $inline_css[$h]; } }
		$inline_css_hash = md5(implode('',$inline_css_group));
		
		# static cache file info + done
		$done = array_merge($done, $footer[$i]['handles']);		
		$hash = 'footer-'.hash('adler32',implode('',$footer[$i]['handles']).$inline_css_hash);

		# create cache files and urls
		$file = $cachedir.'/'.$hash.'.min.css';
		$file_url = fvm_get_protocol($cachedirurl.'/'.$hash.'.min.css');
		
		# generate a new cache file
		clearstatcache();
		if (!file_exists($file)) {
			
			# code and log initialization
			$log = '';
			$code = '';	
		
			# minify and write to file
			foreach($footer[$i]['handles'] as $handle) :
				if(!empty($wp_styles->registered[$handle]->src)) {
					
					# get hurl per handle
					$hurl = fastvelocity_min_get_hurl($wp_styles->registered[$handle]->src, $wp_domain, $wp_home);
					
					# inlined scripts without file
					if( empty($hurl)) {
						continue;
					}
					
					# print url
					$printurl = str_ireplace(array(site_url(), home_url(), 'http:', 'https:'), '', $hurl);
					
					# download, minify, cache
					$tkey = 'css-'.hash('adler32', $handle.$hurl).'.css';
					$json = false; $json = fvm_get_transient($tkey);
					if ( $json === false) {
						$json = fvm_download_and_minify($hurl, null, $disable_css_minification, 'css', $handle);
						if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $hurl -->" . PHP_EOL; }
						fvm_set_transient($tkey, $json);
					}
					
					# decode 
					$res = json_decode($json, true);
					
					# response has failed
					if($res['status'] != true) {
						$log.= $res['log'];
						continue;
					}
					
					# append code to merged file
					$code.= $res['code'];
					$log.= $res['log'];
					
					# append inlined styles
					if(isset($inline_css[$handle]) && !empty($inline_css[$handle])) { 
						$code.= $inline_css[$handle]; 
					}
				
				# consider dependencies on handles with an empty src
				} else {
					wp_dequeue_script($handle); 
					wp_enqueue_script($handle);
				}
			endforeach;	
			
			# prepare log
			$log = "PROCESSED on ".date('r').PHP_EOL.$log."PROCESSED from ".home_url(add_query_arg( NULL, NULL )).PHP_EOL;
			
			# generate cache, add inline css, write log
			if(!empty($code)) {
				file_put_contents($file.'.txt', $log);
				file_put_contents($file, $code); # preserve style tags
				file_put_contents($file.'.gz', gzencode(file_get_contents($file), 9));
				
				# permissions
				fastvelocity_fix_permission_bits($file.'.txt');
				fastvelocity_fix_permission_bits($file);
				fastvelocity_fix_permission_bits($file.'.gz');
				
				# brotli static support
				if(function_exists('brotli_compress')) {
					file_put_contents($file.'.br', brotli_compress(file_get_contents($file), 11));
					fastvelocity_fix_permission_bits($file.'.br');
				}
			}
		}

		# register and enqueue minified file, consider excluding of mediatype "print" and inline css
		if ($remove_print_mediatypes != true || ($remove_print_mediatypes == true && $footer[$i]['media'] != 'print')) {

			# the developers tab, takes precedence
			
			# Async CSS with loadCSS ?
			if($fvmloadcss != false && $fvm_remove_css != true) {
				$mt = $footer[$i]['media'];
				echo '<link rel="preload" href="'.$file_url.'" as="style" media="'.$mt.'" onload="this.onload=null;this.rel=\'stylesheet\'" />';
				echo '<noscript><link rel="stylesheet" href="'.$file_url.'" media="'.$mt.'" /></noscript>';
				echo '<!--[if IE]><link rel="stylesheet" href="'.$file_url.'" media="'.$mt.'" /><![endif]-->';
			
			# enqueue file, if not empty
			} else {
				if(file_exists($file) && filesize($file) > 0) {

					# inline if the file is smaller than 20KB or option has been enabled
					if(filesize($file) < 20000 || $force_inline_css_footer != false) {
						echo '<style id="fvm-footer-'.$i.'" media="'.$footer[$i]['media'].'">'.file_get_contents($file).'</style>';
					} else {
						# enqueue it
						wp_enqueue_style("fvm-footer-$i", $file_url, array(), null, $footer[$i]['media']); 
					}
				} else {
					# file could not be generated, output something meaningful
					echo "<!-- ERROR: FVM was not allowed to save it's cache on - $file -->";
					echo "<!-- Please check if the path above is correct and ensure your server has writting permission there! -->";
					echo "<!-- If you found a bug, please report this on https://wordpress.org/support/plugin/fast-velocity-minify/ -->";
				}
			}
		}

	# other css need to be requeued for the order of files to be kept
	} else {
		wp_dequeue_style($footer[$i]['handle']); 
		wp_enqueue_style($footer[$i]['handle']);
	}
}

# remove from queue
$wp_styles->done = $done;
}
###########################################




###########################################
# defer CSS globally from the header (order matters)
# dev: https://www.filamentgroup.com/lab/async-css.html
###########################################
function fvm_add_loadcss() { 

echo <<<EOF
<script>
/* loadCSS. [c]2017 Filament Group, Inc. MIT License */
(function(w){if(!w.loadCSS)w.loadCSS=function(){};var rp=loadCSS.relpreload={};rp.support=function(){var ret;try{ret=w.document.createElement("link").relList.supports("preload")}catch(e){ret=false}return function(){return ret}}();rp.bindMediaToggle=function(link){var finalMedia=link.media||"all";function enableStylesheet(){if(link.addEventListener)link.removeEventListener("load",enableStylesheet);else if(link.attachEvent)link.detachEvent("onload",enableStylesheet);link.setAttribute("onload",null);link.media=finalMedia}if(link.addEventListener)link.addEventListener("load",enableStylesheet);else if(link.attachEvent)link.attachEvent("onload",enableStylesheet);setTimeout(function(){link.rel="stylesheet";link.media="only x"});setTimeout(enableStylesheet,3E3)};rp.poly=function(){if(rp.support())return;var links=w.document.getElementsByTagName("link");for(var i=0;i<links.length;i++){var link=links[i];if(link.rel==="preload"&&link.getAttribute("as")==="style"&&!link.getAttribute("data-loadcss")){link.setAttribute("data-loadcss", true);rp.bindMediaToggle(link)}}};if(!rp.support()){rp.poly();var run=w.setInterval(rp.poly,500);if(w.addEventListener)w.addEventListener("load",function(){rp.poly();w.clearInterval(run)});else if(w.attachEvent)w.attachEvent("onload",function(){rp.poly();w.clearInterval(run)})}if(typeof exports!=="undefined")exports.loadCSS=loadCSS;else w.loadCSS=loadCSS})(typeof global!=="undefined"?global:this);
</script>
EOF;

}

# fvm load async scripts with callback
function fvm_add_loadasync() { 
global $fvm_min_excludejslist;
if($fvm_min_excludejslist != false && is_array($fvm_min_excludejslist) && count($fvm_min_excludejslist) > 0) {

echo <<<EOF
<script>function loadAsync(e,a){var t=document.createElement("script");t.src=e,null!==a&&(t.readyState?t.onreadystatechange=function(){"loaded"!=t.readyState&&"complete"!=t.readyState||(t.onreadystatechange=null,a())}:t.onload=function(){a()}),document.getElementsByTagName("head")[0].appendChild(t)}</script>
EOF;

}
}



# add inline CSS code / Critical Path
function fvm_add_criticial_path() {
	$no_global_critical_path_css = false;
	$critical_path_css = get_option('fastvelocity_min_critical_path_css');
	$critical_path_css_is_front_page = get_option('fastvelocity_min_critical_path_css_is_front_page');

	# critical path (is_front_page only)
	if(!empty($critical_path_css_is_front_page) && $critical_path_css_is_front_page !== false) {
		echo '<style id="critical-path-is-front-page" type="text/css" media="all">'.$critical_path_css_is_front_page.'</style>'.PHP_EOL;
		$no_global_critical_path_css = 1;
	}

	# global path, except if there's something else more specific
	if(!empty($critical_path_css) && $critical_path_css !== false && $no_global_critical_path_css === false) {
		echo '<style id="critical-path-global" type="text/css" media="all">'.$critical_path_css.'</style>'.PHP_EOL; 
	}
}





###########################################
# add preconnect and preload headers
###########################################
add_action( 'send_headers', 'fvm_extra_preload_headers' );
function fvm_extra_preload_headers() { 

# fetch headers
$preload = array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_hpreload')));
$preconnect = array_map('trim', explode(PHP_EOL, get_option('fastvelocity_min_hpreconnect')));

# preconnect
if(is_array($preload) && count($preload) > 0) {
	foreach ($preload as $h) {
		if(!empty($h)) {
			header($h, false);
		}
	}
}

# preload
if(is_array($preconnect) && count($preconnect) > 0) {
	foreach ($preconnect as $url) {
		if(!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
			header("Link: <$url>; rel=preconnect", false);
		}
	}
}

# fvm css and js generated files
$fvm_headers = fastvelocity_get_preload_headers();
if($fvm_headers != false) {
	$nh = array_map('trim', explode(PHP_EOL, $fvm_headers));
	foreach ($nh as $h) {
		if(!empty($h)) {
			header($h, false);
		}
	}
}

}



# inline css in place, instead of inlining the large file
function fastvelocity_optimizecss($html, $handle, $href, $media){
	global $fvm_debug, $wp_domain, $wp_home, $force_inline_css, $fvmualist, $fvm_collect_google_fonts, $force_inline_googlefonts, $min_async_googlefonts, $remove_googlefonts, $skip_google_fonts, $css_hide_googlefonts, $remove_print_mediatypes, $ignore, $blacklist, $ignorelist, $wp_home, $fvmloadcss, $fvm_remove_css, $fvm_cdn_url, $disable_minification, $fvm_min_excludecsslist, $disable_css_minification, $fvm_fix_editor, $fvm_fawesome_method;
		
		# current timestamp
		$ctime = get_option('fvm-last-cache-update', '0'); 
		
		# make sure href is complete
		$href = fastvelocity_min_get_hurl($href, $wp_domain, $wp_home);
		
		if($fvm_debug == true) { echo "<!-- FVM DEBUG: Inline CSS processing start $handle / $href -->" . PHP_EOL; }
		
		# prevent optimization for these locations
		if (is_admin() || is_preview() || is_customize_preview() || ($fvm_fix_editor == true && is_user_logged_in()) || (function_exists( 'is_amp_endpoint' ) && is_amp_endpoint())) {
			return $html;
		}
		
		# skip all this, if the async css option is enabled
		if($fvmloadcss != false) {
			return $html;
		}
		
		# remove all css?
		if($fvm_remove_css != false) {
			return false; 
		}
		
		# leave conditionals alone
		$conditional = wp_styles()->get_data($handle, 'conditional');
		if($conditional != false) {
			return $html;
		}
		
		# mediatype fix for some plugins + remove print mediatypes
		if ($media == 'screen' || $media == 'screen, print' || empty($media) || is_null($media) || $media == false) { $media = 'all'; }
		if($remove_print_mediatypes != false && $media == 'print') {
			return false; 
		}
		
		# Exclude specific CSS files from PSI?
		if($fvm_min_excludecsslist != false && is_array($fvm_min_excludecsslist) && fastvelocity_min_in_arrayi($href, $fvm_min_excludecsslist)) {
			$cssguid = 'fvm'.hash('adler32', $href);
			echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
			echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$href.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$media.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
			echo '}</script>';
			return false;
		}
		
		# remove FVM from the ignore list
		array_filter($ignore, function ($var) { return (stripos($var, '/fvm/') === false); });
		
		# return if in any ignore or black list
		if (count($ignore) > 0 && fastvelocity_min_in_arrayi($href, $ignore) || count($blacklist) > 0 && fastvelocity_min_in_arrayi($href, $blacklist) || count($ignorelist) > 0 && fastvelocity_min_in_arrayi($href, $ignorelist)) { 
				return $html;
		}
		
		# remove google fonts completely?
		if($remove_googlefonts != false && stripos($href, 'fonts.googleapis.com') !== false) {
			return false; 
		}
		
		# handle google fonts here, when merging is disabled
		if(stripos($href, 'fonts.googleapis.com') !== false && $skip_google_fonts != false) {
			
			# hide google fonts from PSI
			if($css_hide_googlefonts == true) {
				$cssguid = 'fvm'.hash('adler32', $href);
				echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
				echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$href.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="all"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
				echo '}</script>';
				return false; 
			}
			
			# load google fonts async
			if($min_async_googlefonts != false) {
				echo '<link rel="preload" href="'.$href.'" as="style" media="all" onload="this.onload=null;this.rel=\'stylesheet\'" />';
				echo '<noscript><link rel="stylesheet" href="'.$href.'" media="all" /></noscript>';
				echo '<!--[if IE]><link rel="stylesheet" href="'.$href.'" media="all" /><![endif]-->';
				return false; 
			}
		}
		
		# font awesome processing, async css
		if($fvm_fawesome_method == 2 && stripos($href, 'font-awesome') !== false) {
			echo '<link rel="preload" href="'.$href.'" as="style" media="'.$media.'" onload="this.onload=null;this.rel=\'stylesheet\'" />';
			echo '<noscript><link rel="stylesheet" href="'.$href.'" media="'.$media.'" /></noscript>';
			echo '<!--[if IE]><link rel="stylesheet" href="'.$href.'" media="'.$media.'" /><![endif]-->';
			return false;
		}	
		
		# font awesome processing, async and exclude from PSI
		if($fvm_fawesome_method == 3 && stripos($href, 'font-awesome') !== false) {
			$cssguid = 'fvm'.hash('adler32', $href);
			echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
			echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$href.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="'.$media.'"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
			echo '}</script>';
			return false;
		}
		
		# font awesome processing, inline
		if($fvm_fawesome_method == 1 && stripos($href, 'font-awesome') !== false) {
			
			# download, minify, cache
			$tkey = 'css-'.hash('adler32', $handle.$href).'.css';
			$json = false; $json = fvm_get_transient($tkey);
			if ( $json === false) {
				$json = fvm_download_and_minify($href, null, $disable_css_minification, 'css', $handle);
				if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $href -->" . PHP_EOL; }
				fvm_set_transient($tkey, $json);
			}
			
			# decode 
			$res = json_decode($json, true);
			
			# add font-display
			# https://developers.google.com/web/updates/2016/02/font-display
			$res['code'] = str_ireplace('font-style:normal;', 'font-display:block;font-style:normal;', $res['code']);
			
			# inline css or fail
			if($res['status'] != false) { 
				echo '<style type="text/css" media="all">'.$res['code'].'</style>'.PHP_EOL;
				return false;
			} else {
				if($fvm_debug == true) { echo "<!-- FVM DEBUG: Font Awesome request failed for $href -->" . PHP_EOL; }
				return $html;
			}
		}
		
		# inline google fonts, do not collect
		if(stripos($href, 'fonts.googleapis.com') !== false && $force_inline_googlefonts != false && $css_hide_googlefonts != true && $min_async_googlefonts != true) {
			
			# download, minify, cache
			$tkey = 'css-'.hash('adler32', $handle.$href).'.css';
			$json = false; $json = fvm_get_transient($tkey);
			if ( $json === false) {
				$json = fvm_download_and_minify($href, null, $disable_css_minification, 'css', $handle);
				if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $href -->" . PHP_EOL; }
				fvm_set_transient($tkey, $json);
			}
			
			# decode 
			$res = json_decode($json, true);
			
			# add font-display
			# https://developers.google.com/web/updates/2016/02/font-display
			$res['code'] = str_ireplace('font-style:normal;', 'font-display:block;font-style:normal;', $res['code']);
			
			# inline css or fail
			if($res['status'] != false) { 
				echo '<style type="text/css" media="all">'.$res['code'].'</style>'.PHP_EOL;
				return false;
			} else {
				if($fvm_debug == true) { echo "<!-- FVM DEBUG: Google fonts request failed for $href -->" . PHP_EOL; }
				return $html;
			}
		}
		
		# collect and remove google fonts for merging
		if(stripos($href, 'fonts.googleapis.com') !== false){
			$fvm_collect_google_fonts[$handle] = $href;
			return false; 
		}
		
		# skip external scripts that are not specifically allowed
		if (fvm_internal_url($href, $wp_home) === false || empty($href)) {
			if($fvm_debug == true) { echo "<!-- FVM DEBUG: Skipped the next external enqueued CSS -->" . PHP_EOL; }
			return $html;
		}
		
		# download, minify, cache
		$tkey = 'css-'.hash('adler32', $handle.$href).'.css';
		$json = false; $json = fvm_get_transient($tkey);
		if ( $json === false) {
			$json = fvm_download_and_minify($href, null, $disable_css_minification, 'css', $handle);
			if($fvm_debug == true) { echo "<!-- FVM DEBUG: Uncached file processing now for $handle / $href -->" . PHP_EOL; }
			fvm_set_transient($tkey, $json);
		}
		
		# decode 
		$res = json_decode($json, true);
		
		# inline it + other inlined children styles
		if($res['status'] != false) {
			echo '<style type="text/css" media="'.$media.'">'.$res['code'].'</style>'; 
			
			# get inline_styles for this handle, minify and print
			$inline_styles = array();
			$inline_styles = wp_styles()->get_data( $handle, 'after' );
			if($inline_styles != false) {

				# string type
				if(is_string($inline_styles)) {
					$code = fastvelocity_min_get_css($href, $inline_styles, $disable_css_minification);
					if(!empty($code) && $code != false) { 
						echo '<style type="text/css" media="'.$media.'">'.$code.'</style>'; 
					}
				}
				
				# array type
				if(is_array($inline_styles)) {
					foreach ($inline_styles as $st) {
						$code = fastvelocity_min_get_css($href, $st, $disable_css_minification);
						if(!empty($code) && $code != false) { 
							echo '<style type="text/css" media="'.$media.'">'.$code.'</style>'; 
						}
					}
				}
			}
			
			# prevent default
			return false;
			
		} else {
			if($fvm_debug == true) { echo "<!-- FVM DEBUG:  $handle / $href returned an empty from minification -->" . PHP_EOL; }
			return $html;
		}
		
	# fallback, for whatever reason
	echo "<!-- ERROR: FVM couldn't catch the CSS file below. Please report this on https://wordpress.org/support/plugin/fast-velocity-minify/ -->";
	return $html;
}


# critical css for the page
function fastvelocity_add_google_fonts_merged() {
	global $fvm_collect_google_fonts, $fvmualist, $css_hide_googlefonts, $skip_google_fonts, $min_async_googlefonts, $fvm_debug;
	
	# prevent optimization for logged in users
	if (is_admin() || is_preview() || is_customize_preview()) {
		return false;
	}
	
	# must have something to do
	if(!is_array($fvm_collect_google_fonts) || count($fvm_collect_google_fonts) == 0) {
		return false;
	}
	
	# merge google fonts
	$gfurl = fastvelocity_min_concatenate_google_fonts($fvm_collect_google_fonts);
	if(empty($gfurl)) {
		return false;
	}
	
	# hide google fonts from PSI
	if($css_hide_googlefonts == true) {
		
		# make a stylesheet, hide from PSI
		$cssguid = 'fvm'.hash('adler32', $gfurl);
		echo '<script type="text/javascript">if(!navigator.userAgent.match(/'.implode('|', $fvmualist).'/i)){';
		echo 'var '.$cssguid.'=document.createElement("link");'.$cssguid.'.rel="stylesheet",'.$cssguid.'.type="text/css",'.$cssguid.'.media="async",'.$cssguid.'.href="'.$gfurl.'",'.$cssguid.'.onload=function(){'.$cssguid.'.media="all"},document.getElementsByTagName("head")[0].appendChild('.$cssguid.');';
		echo '}</script>';
		
	# load google fonts async	
	} elseif($min_async_googlefonts != false) {
		echo '<link rel="preload" href="'.$gfurl.'" as="style" media="all" onload="this.onload=null;this.rel=\'stylesheet\'" />';
		echo '<noscript><link rel="stylesheet" href="'.$gfurl.'" media="all" /></noscript>';
		echo '<!--[if IE]><link rel="stylesheet" href="'.$gfurl.'" media="all" /><![endif]-->';
	
	# fallback to normal inline
	} else {
		echo '<link rel="stylesheet" href="'.$gfurl.'" media="all" />';
	}
	
	# unset per hook
	foreach($fvm_collect_google_fonts as $k=>$v) {
		unset($fvm_collect_google_fonts[$k]);
	}

} 



# collect all fvm JS files and save them to an headers file
add_filter('script_loader_tag', 'fastvelocity_collect_js_preload_headers', PHP_INT_MAX, 3 );
function fastvelocity_collect_js_preload_headers($html, $handle, $src){
	global $collect_preload_js, $fvm_enabled_css_preload, $fvm_enabled_js_preload;
	
	# return if disabled
	if ($fvm_enabled_js_preload != true) { 
		return $html;
	}
	
	# collect
	if (stripos($src, '/fvm/out/') !== false) { 
		$collect_preload_js[] = $src;
	}
	return $html;
}

# generate preload headers file
add_action('wp_footer', 'fastvelocity_generate_preload_headers', PHP_INT_MAX); 
function fastvelocity_generate_preload_headers(){
	global $collect_preload_css, $collect_preload_js, $fvm_enabled_css_preload, $fvm_enabled_js_preload;
	
	# return if disabled
	if ($fvm_enabled_css_preload != true && $fvm_enabled_js_preload != true) { 
		return false;
	}

	# get host with multisite support and query strings
	$host = htmlentities($_SERVER['SERVER_NAME']);
	if(empty($hosts)) { $host = htmlentities($_SERVER['HTTP_HOST']); }
	$request_query = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	$request_uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$is_admin = strpos( $request_uri, '/wp-admin/' );
	
	# always false for admin pages
	if( false !== $is_admin){ 
		return false;
	}
	
	# initialize headers
	$headers = array();
	
	# css headers
	if ($fvm_enabled_css_preload != false && is_array($collect_preload_css) && count($collect_preload_css) > 0) { 
		foreach($collect_preload_css as $u) {
			
			# filter out footer footer files, because they are not in the critical path
			if(stripos($u, '/fvm/out/footer-') !== false) { continue; }
			
			# add headers
			$headers[] = "Link: <$u>; rel=preload; as=style";
		}
	}
	
	# js headers
	if ($fvm_enabled_js_preload != false && is_array($collect_preload_js) && count($collect_preload_js) > 0) { 
		foreach($collect_preload_js as $u) {
			
			# filter out footer footer files, because they are not in the critical path
			if(stripos($u, '/fvm/out/footer-') !== false) { continue; }
			
			# add headers
			$headers[] = "Link: <$u>; rel=preload; as=script";
		}
	}
	
	# must have something
	if(count($headers) == 0) {
		return false;
	} else {
		$headers = implode(PHP_EOL, $headers);
	}
	
	# get cache path
	$cachepath = fvm_cachepath();
	$headerdir = $cachepath['headerdir'];
	$cachefilebase = $headerdir.'/';
	
	# possible cache file locations
	$b = $cachefilebase . md5($host.'-'.$request_uri).'.header';
	$a = $cachefilebase . md5($host.'-'.$request_uri.$request_query).'.header';
	
	# reset file cache
	clearstatcache();
	
	# if there are no query strings
	if($b == $a) {
		if(!file_exists($a)) { 
			file_put_contents($a, $headers); 
			fastvelocity_fix_permission_bits($a);
		}
		return false;
	}
	
	# b fallback
	if($b != $a && !file_exists($b)) {
		file_put_contents($b, $headers);
		fastvelocity_fix_permission_bits($b);
	}
	
	return false;
}


# get current headers file for the url
function fastvelocity_get_preload_headers(){
	global $fvm_enabled_css_preload, $fvm_enabled_js_preload;
	
	# return if disabled
	if ($fvm_enabled_css_preload != true && $fvm_enabled_js_preload != true) { 
		return false;
	}
	
	# get host with multisite support and query strings
	$host = htmlentities($_SERVER['SERVER_NAME']);
	if(empty($hosts)) { $host = htmlentities($_SERVER['HTTP_HOST']); }
	$request_query = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY);
	$request_uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$is_admin = strpos( $request_uri, '/wp-admin/' );
	
	# always false for admin pages
	if( false !== $is_admin){ 
		return false;
	}
	
	# get cache path
	$cachepath = fvm_cachepath();
	$headerdir = $cachepath['headerdir'];
	$cachefilebase = $headerdir.'/';
	
	# possible cache file locations
	$b = $cachefilebase . md5($host.'-'.$request_uri).'.header';
	$a = $cachefilebase . md5($host.'-'.$request_uri.$request_query).'.header';
	
	# reset file cache
	clearstatcache();
	
	# return header files or fallback
	if($b == $a && file_exists($a)) { return file_get_contents($a); }
	if($b != $a && file_exists($b)) { return file_get_contents($b); }
	
	return false;
}



# cron job to delete old FVM cache
add_action('fastvelocity_purge_old_cron_event', 'fvm_purge_old');

