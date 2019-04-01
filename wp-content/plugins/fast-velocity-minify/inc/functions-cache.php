<?php


# Fix the permission bits on generated files
function fastvelocity_fix_permission_bits($file){
	if(function_exists('stat') && fvm_function_available('stat')) {
		if ($stat = @stat(dirname($file))) {
			$perms = $stat['mode'] & 0007777;
			@chmod($file, $perms);
			clearstatcache();
			return true;
		}
	}
	
	
	# get permissions from parent directory
	$perms = 0777; 
	if(function_exists('stat') && fvm_function_available('stat')) {
		if ($stat = @stat(dirname($file))) { $perms = $stat['mode'] & 0007777; }
	}
	
	if (file_exists($file)){
		if ($perms != ($perms & ~umask())){
			$folder_parts = explode( '/', substr( $file, strlen(dirname($file)) + 1 ) );
				for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ ) {
				@chmod(dirname($file) . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $perms );
			}
		}
	}

	return true;
}


# get cache directories and urls
function fvm_cachepath() {

# custom directory
$fvm_change_cache_path = get_option('fastvelocity_min_change_cache_path');
$fvm_change_cache_base = get_option('fastvelocity_min_change_cache_base_url');
$upload = array();
if($fvm_change_cache_path !== false && $fvm_change_cache_base !== false && strlen($fvm_change_cache_path) > 1) {
	$upload['basedir'] = trim($fvm_change_cache_path);
	$upload['baseurl'] = trim($fvm_change_cache_base);
} else {
	$up = wp_upload_dir(); # default 
	
	# upload path for multisite
	$upload['basedir'] = rtrim($up['basedir']);
	$upload['baseurl'] = rtrim($up['baseurl']);
	
	# for single sites, change to the cache directory
	if(basename($up['basedir']) == 'uploads') {
		$upload['basedir'] = dirname($upload['basedir']);
		$upload['baseurl'] = dirname($upload['baseurl']);
	}
	
}

# last update or zero
$ctime = get_option('fvm-last-cache-update', '0'); 

# create
$uploadsdir  = $upload['basedir'].'/cache';
$uploadsurl  = $upload['baseurl'].'/cache';
$cachebase   = $uploadsdir.'/fvm/'.$ctime;
$cachebaseurl  = $uploadsurl.'/fvm/'.$ctime;
$cachedir    = $cachebase.'/out';
$tmpdir      = $cachebase.'/tmp';
$headerdir   = $cachebase.'/header';
$cachedirurl = $cachebaseurl.'/out';

# get permissions from uploads directory
$dir_perms = 0777; 
if(is_dir($uploadsdir.'/cache') && function_exists('stat') && fvm_function_available('stat')) {
	if ($stat = @stat($uploadsdir.'/cache')) { $dir_perms = $stat['mode'] & 0007777; }
}

# mkdir and check if umask requires chmod
$dirs = array($cachebase, $cachedir, $tmpdir, $headerdir);
foreach ($dirs as $target) {
	if(!is_dir($target)) {
		if (@mkdir($target, $dir_perms, true)){
			if ($dir_perms != ($dir_perms & ~umask())){
				$folder_parts = explode( '/', substr($target, strlen(dirname($target)) + 1 ));
					for ($i = 1, $c = count($folder_parts ); $i <= $c; $i++){
					@chmod(dirname($target) . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
				}
			}
		} else {
			# fallback
			wp_mkdir_p($target);
		}
	}
}

# return
return array('cachebase'=>$cachebase,'tmpdir'=>$tmpdir, 'cachedir'=>$cachedir, 'cachedirurl'=>$cachedirurl, 'headerdir'=>$headerdir);
}



# increment file names
function fvm_cache_increment() {
	update_option('fvm-last-cache-update', time());
}

# will delete temporary intermediate stuff but leave final css/js alone for compatibility
function fvm_purge_all() {
	
	# get cache directories and urls
	$cachepath = fvm_cachepath();
	$tmpdir = $cachepath['tmpdir'];
	$headerdir = $cachepath['headerdir'];
	
	# increment cache file names
	fvm_cache_increment();
	
	# delete temporary directories only
	if(is_dir($tmpdir)) { fastvelocity_rrmdir($tmpdir); }
	if(is_dir($headerdir)) { fastvelocity_rrmdir($headerdir); }
	
	# extra hook for developers
	do_action('fvm_after_purge_all');
	return true;
}


# purge all public files on uninstall
function fvm_purge_all_uninstall() {
	$cachepath = fvm_cachepath();
	$cachebaseparent = dirname($cachepath['cachebase']);
	if(is_dir($cachebaseparent)) { fastvelocity_rrmdir($cachebaseparent); }	
	return true;
}

# purge cache files older than 3 months
fvm_purge_old();
function fvm_purge_old() {
	
	# get cache directories and urls
	$cachepath = fvm_cachepath();
	$cachebaseparent = dirname($cachepath['cachebase']);
	$ctime = get_option('fvm-last-cache-update', '0');
	$expires = time() - 86400 * 90; # three months
	
	# get all directories that are a direct child of current directory
	if ($handle = opendir($cachebaseparent)) {
		while (false !== ($d = readdir($handle))) {
			if (strcmp($d, '.')==0 || strcmp($d, '..')==0) { continue; }
			if($d != $ctime && (is_numeric($d) && $d <= $expires)) {
				$dir = $cachebaseparent.'/'.$d;
				if(is_dir($dir)) { 
					fastvelocity_rrmdir($dir); 
					rmdir($dir);
				}
			}
			
		}
		
		closedir($handle);
	}
	
	return true;
}


# purge temp cache on save settings
function fastvelocity_purge_onsave() {
	if(current_user_can( 'manage_options') && isset($_POST['fastvelocity_min_save_options'])) {
		fvm_purge_all();
		fvm_purge_others();
	}
}


# purge temp cache globally, after updates
function fastvelocity_purge_all_global() {
	if(current_user_can( 'manage_options')) {
		fvm_purge_all();
		fvm_purge_others();
	}
}


# get transients on the disk
function fvm_get_transient($key) {
	$cachepath = fvm_cachepath();
	$tmpdir = $cachepath['tmpdir'];
	$f = $tmpdir.'/'.$key.'.transient';
	clearstatcache();
	if(file_exists($f)) {
		return file_get_contents($f);
	} else {
		return false;
	}
}

# set cache on disk
function fvm_set_transient($key, $code) {
	if(is_null($code) || empty($code)) { return false; }
	$cachepath = fvm_cachepath();
	$tmpdir = $cachepath['tmpdir'];
	$f = $tmpdir.'/'.$key.'.transient';
	file_put_contents($f, $code);
	fastvelocity_fix_permission_bits($f);
	return true;
}



# get cache size and count
function fastvelocity_get_cachestats() {
	clearstatcache();
	$cachepath = fvm_cachepath();
	$cachedir = $cachepath['cachedir'];
	if(is_dir($cachedir)) {
		$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cachedir, FilesystemIterator::SKIP_DOTS));
		$size = 0; 
		foreach ($dir as $file) { 
			$size += $file->getSize(); 
		}
		return fastvelocity_format_filesize($size);
	} else { 
		return 'Error: '.$cachedir. ' is not a directory!';
	}
}

# remove all cache files
function fastvelocity_rrmdir($path) {
	# purge
	clearstatcache();
	if(is_dir($path)) {
		$i = new DirectoryIterator($path);
		foreach($i as $f){
			if($f->isFile()){ unlink($f->getRealPath());
			} else if(!$f->isDot() && $f->isDir()){
				fastvelocity_rrmdir($f->getRealPath());
				rmdir($f->getRealPath());
			}
		}
	}
}


# return size in human format
function fastvelocity_format_filesize($bytes, $decimals = 2) {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    for ($i = 0; ($bytes / 1024) > 0.9; $i++, $bytes /= 1024) {}
    return sprintf( "%1.{$decimals}f %s", round( $bytes, $decimals ), $units[$i] );
}


# Purge Godaddy Managed WordPress Hosting (Varnish)
# https://github.com/wp-media/wp-rocket/blob/master/inc/3rd-party/hosting/godaddy.php
function fastvelocity_godaddy_request( $method, $url = null ) {
	$url  = empty( $url ) ? home_url() : $url;
	$host = parse_url( $url, PHP_URL_HOST );
	$url  = set_url_scheme( str_replace( $host, WPaas\Plugin::vip(), $url ), 'http' );
	wp_cache_flush();
	update_option( 'gd_system_last_cache_flush', time() ); # purge apc
	wp_remote_request( esc_url_raw( $url ), array('method' => $method, 'blocking' => false, 'headers' => array('Host' => $host)) );
}


# purge supported hosting and plugins
function fvm_purge_others(){
	
# wordpress default cache
if (function_exists('wp_cache_flush')) {
	wp_cache_flush();
}
	
# Purge all W3 Total Cache
if (function_exists('w3tc_pgcache_flush')) {
	w3tc_pgcache_flush();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>W3 Total Cache</strong> have also been purged.</p></div>');
}

# Purge WP Super Cache
if (function_exists('wp_cache_clear_cache')) {
	wp_cache_clear_cache();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>WP Super Cache</strong> have also been purged.</p></div>');
}

# Purge WP Rocket
if (function_exists('rocket_clean_domain')) {
	rocket_clean_domain();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>WP Rocket</strong> have also been purged.</p></div>');
}

# Purge Wp Fastest Cache
if(isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')){
	$GLOBALS['wp_fastest_cache']->deleteCache();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>Wp Fastest Cache</strong> have also been purged.</p></div>');
}

# Purge Cachify
if (function_exists('cachify_flush_cache')) {
	cachify_flush_cache();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>Cachify</strong> have also been purged.</p></div>');
}

# Purge Comet Cache
if ( class_exists("comet_cache") ) {
	comet_cache::clear();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>Comet Cache</strong> have also been purged.</p></div>');
}

# Purge Zen Cache
if ( class_exists("zencache") ) {
	zencache::clear();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>Comet Cache</strong> have also been purged.</p></div>');
}

# Purge LiteSpeed Cache 
if (class_exists('LiteSpeed_Cache_Tags')) {
	LiteSpeed_Cache_Tags::add_purge_tag('*');
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>LiteSpeed Cache</strong> have also been purged.</p></div>');
}

# Purge SG Optimizer
if (function_exists('sg_cachepress_purge_cache')) {
	sg_cachepress_purge_cache();
	return __('<div class="notice notice-info is-dismissible"><p>All caches from <strong>SG Optimizer</strong> have also been purged.</p></div>');
}

# Purge Hyper Cache
if (function_exists('hyper_cache_flush_all')) {
	hyper_cache_flush_all();
	return __( '<div class="notice notice-info is-dismissible"><p>All caches from <strong>HyperCache</strong> have also been purged.</p></div>');
}

# Purge Godaddy Managed WordPress Hosting (Varnish + APC)
if (class_exists('WPaaS\Plugin')) {
	fastvelocity_godaddy_request('BAN');
	return __('<div class="notice notice-info is-dismissible"><p>A cache purge request has been sent to <strong>Go Daddy Varnish</strong></p></div><div class="notice notice-info is-dismissible"><p>Please note that it may not work 100% of the time, due to cache rate limiting by your host!</p></div>');
}

# purge cache enabler
if ( has_action('ce_clear_cache') ) {
    do_action('ce_clear_cache');
	return __( '<div class="notice notice-info is-dismissible"><p>All caches from <strong>Cache Enabler</strong> have also been purged.</p></div>');
}


# Purge WP Engine
if (class_exists("WpeCommon")) {
	if (method_exists('WpeCommon', 'purge_memcached')) { WpeCommon::purge_memcached(); }
	if (method_exists('WpeCommon', 'clear_maxcdn_cache')) { WpeCommon::clear_maxcdn_cache(); }
	if (method_exists('WpeCommon', 'purge_varnish_cache')) { WpeCommon::purge_varnish_cache(); }

	if (method_exists('WpeCommon', 'purge_memcached') || method_exists('WpeCommon', 'clear_maxcdn_cache') || method_exists('WpeCommon', 'purge_varnish_cache')) {
		return __('<div class="notice notice-info is-dismissible"><p>A cache purge request has been sent to <strong>WP Engine</strong></p></div><div class="notice notice-info is-dismissible"><p>Please note that it may not work 100% of the time, due to cache rate limiting by your host!</p></div>');
	}
}

# add breeze cache purge support
if (class_exists("Breeze_PurgeCache")) {
	Breeze_PurgeCache::breeze_cache_flush();
	return __( '<div class="notice notice-info is-dismissible"><p>All caches from <strong>Breeze</strong> have also been purged.</p></div>');
}

}


