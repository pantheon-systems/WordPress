<?php
if ( ! function_exists('wp_all_import_remove_source')){
	function wp_all_import_remove_source($file, $remove_dir = true){
		
		@unlink($file);
        
        $path_parts = pathinfo($file);

        if ( ! empty($path_parts['dirname'])){
            $path_all_parts = explode('/', $path_parts['dirname']);
            $dirname = array_pop($path_all_parts);
            
            if ( wp_all_import_isValidMd5($dirname)){                              
            	if ($remove_dir or count(@scandir($path_parts['dirname'])) == 3){
            	    if (file_exists($path_parts['dirname'] . DIRECTORY_SEPARATOR . 'index.php')){
                        @unlink($path_parts['dirname'] . DIRECTORY_SEPARATOR . 'index.php' );
                    }
            	}
                if ($remove_dir or count(@scandir($path_parts['dirname'])) == 2){
                    wp_all_import_rmdir($path_parts['dirname']);
                }                  
            }
        }
        
	}
}