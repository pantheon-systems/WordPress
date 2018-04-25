<?php
if ( ! function_exists('wp_all_import_clear_directory') ){
	function wp_all_import_clear_directory($path){
		if (($dir = @opendir($path . '/')) !== false or ($dir = @opendir($path)) !== false) {				
			while(($file = @readdir($dir)) !== false) {
				$filePath = $path . '/' . $file;					
				if ( is_dir($filePath) && ( ! in_array($file, array('.', '..'))) ){
					wp_all_import_rmdir($filePath);
				}
				elseif( is_file($filePath) ){
					@unlink($filePath);
				}					
			}
		}
	}
}	