<?php
function wp_all_export_rmdir($dir) {
	$scanned_files = @scandir($dir);
	if (!empty($scanned_files) and is_array($scanned_files)){
	   	$files = array_diff($scanned_files, array('.','..'));
	    if (!empty($files)){
		    foreach ($files as $file) {
		      (is_dir("$dir/$file")) ? wp_all_export_rmdir("$dir/$file") : @unlink("$dir/$file");
		    }
		}
	    return @rmdir($dir);
	}
} 