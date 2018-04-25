<?php
if ( ! function_exists('wp_all_import_get_gz')){
	function wp_all_import_get_gz($filename, $use_include_path = 0, $targetDir = false) {					

		$type = 'csv';
		$uploads = wp_upload_dir();	
		$targetDir = ( ! $targetDir ) ? wp_all_import_secure_file($uploads['basedir'] . DIRECTORY_SEPARATOR . PMXI_Plugin::UPLOADS_DIRECTORY ) : $targetDir;

		$tmpname = wp_unique_filename($targetDir, (strlen(basename($filename)) < 30) ? basename($filename) : time() );	
		$localPath = $targetDir  .'/'. urldecode(sanitize_file_name($tmpname));

		$fp = @fopen($localPath, 'w');			
	    $file = @gzopen($filename, 'rb', $use_include_path);
	    
	    if ($file) {
	        $first_chunk = true;
	        while (!gzeof($file)) {
	            $chunk = gzread($file, 1024);		            
	            if ($first_chunk and strpos($chunk, "<?") !== false and strpos($chunk, "</") !== false) { $type = 'xml'; $first_chunk = false; } // if it's a 1st chunk, then chunk <? symbols to detect XML file
	            @fwrite($fp, $chunk);
	        }
	        gzclose($file);
	    } 
	    else{

	    	$tmpname = wp_unique_filename($targetDir, (strlen(basename($filename)) < 30) ? basename($filename) : time() );	
	    	$localGZpath = $targetDir  .'/'. urldecode(sanitize_file_name($tmpname));
			$request = get_file_curl($filename, $localGZpath, false, true);				

			if ( ! is_wp_error($request) ){

				$file = @gzopen($localGZpath, 'rb', $use_include_path);

				if ($file) {
			        $first_chunk = true;
			        while (!gzeof($file)) {
			            $chunk = gzread($file, 1024);			            
			            if ($first_chunk and strpos($chunk, "<?") !== false and strpos($chunk, "</") !== false) { $type = 'xml'; $first_chunk = false; } // if it's a 1st chunk, then chunk <? symbols to detect XML file
			            @fwrite($fp, $chunk);
			        }
			        gzclose($file);
			    } 

			    @unlink($localGZpath);

			}
			else return $request;

	    }
	    @fclose($fp);

	    if (preg_match('%\W(gz)$%i', basename($localPath))){		    	
		    if (@rename($localPath, str_replace('.gz', '.' . $type, $localPath)))
		    	$localPath = str_replace('.gz', '.' . $type, $localPath);
		}
		else{
			if (@rename($localPath, $localPath . '.' . $type))
		    	$localPath = $localPath . '.' . $type;
		}
	   
	    return array('type' => $type, 'localPath' => $localPath);
	}
}