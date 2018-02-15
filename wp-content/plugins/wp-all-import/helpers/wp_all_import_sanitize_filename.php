<?php

function wp_all_import_sanitize_filename($filename) {
	$filename = preg_replace('/\?.*/', '', $filename);
	$filename_parts = explode('.',$filename);
	if ( ! empty($filename_parts) and count($filename_parts) > 1){
		$ext = end($filename_parts);
		// Replace all weird characters
        $sanitized = substr($filename, 0, -(strlen($ext)+1));
        $sanitized = str_replace("_", "willbetrimmed", $sanitized);
		$sanitized = sanitize_file_name($sanitized);
        $sanitized = str_replace("willbetrimmed", "_", $sanitized);
		// Replace dots inside filename
		//$sanitized = str_replace('.','-', $sanitized);
		return $sanitized . '.' . $ext;
	}
	return $filename;
}