<?php

if ( ! function_exists('get_file_curl') ):

	function get_file_curl($url, $fullpath, $to_variable = false, $iteration = false) {				
		
		if ( ! preg_match('%^(http|ftp)s?://%i', $url) ) return;

		$response = wp_remote_get($url);

		if ( ! is_wp_error($response) and ( ! isset($response['response']['code']) or isset($response['response']['code']) and ! in_array($response['response']['code'], array(401, 403, 404))) ) 
		{
			$rawdata = wp_remote_retrieve_body( $response );

			if (empty($rawdata))
			{
				$result = pmxi_curl_download($url, $fullpath, $to_variable);					
				if ( ! $result and $iteration === false)
				{
					$new_url = wp_all_import_translate_uri($url);
					return ($new_url !== $url) ? get_file_curl($new_url, $fullpath, $to_variable, true) : $result;
				}
				return $result;
			}

			if ( ! @file_put_contents($fullpath, $rawdata) ) 
			{
				$fp = fopen($fullpath,'w');
			    fwrite($fp, $rawdata);
			    fclose($fp);
			}													
			
		    if ( preg_match('%\W(svg)$%i', basename($fullpath)) or preg_match('%\W(jpg|jpeg|gif|png)$%i', basename($fullpath)) and ( ! ($image_info = apply_filters('pmxi_getimagesize', @getimagesize($fullpath), $fullpath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)) ) )
			{			
				$result = pmxi_curl_download($url, $fullpath, $to_variable);	
				if ( ! $result and $iteration === false)
				{
					$new_url = wp_all_import_translate_uri($url);
					return ($new_url !== $url) ? get_file_curl($new_url, $fullpath, $to_variable, true) : $result;
				}
				return $result;
			}
			
		    return ($to_variable) ? $rawdata : true;

		}
		else
		{

            $use_only_wp_http_api = apply_filters('wp_all_import_use_only_wp_http_api', false);

            if ( false == $use_only_wp_http_api ){
                $curl = pmxi_curl_download($url, $fullpath, $to_variable);

                if ($curl === false and $iteration === false)
                {
                    $new_url = wp_all_import_translate_uri($url);
                    return ($new_url !== $url) ? get_file_curl($new_url, $fullpath, $to_variable, true) : ( is_wp_error($response) ? $response : false );
                }

                return ($curl === false) ? ( is_wp_error($response) ? $response : false ) : $curl;
            }

            return $response;
		}
	}

endif;

if ( ! function_exists('pmxi_curl_download') ) {

	function pmxi_curl_download($url, $fullpath, $to_variable){

		if ( ! function_exists('curl_version') ) return false;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$rawdata = curl_exec_follow($ch);	    	    

	    $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    
		curl_close ($ch);

		if ( empty($rawdata) ) return false;

		if (!@file_put_contents($fullpath, $rawdata)){
			$fp = fopen($fullpath,'w');	    
		    fwrite($fp, $rawdata);
		    fclose($fp);			
		}

        if ( preg_match('%\W(jpg|jpeg|gif|png)$%i', basename($fullpath)) and ( ! ($image_info = apply_filters('pmxi_getimagesize', @getimagesize($fullpath), $fullpath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) ) ){
            return false;
        }

	    return ($result == 200) ? (($to_variable) ? $rawdata : true) : false;
	}

}

if ( ! function_exists('curl_exec_follow') ):

	function curl_exec_follow($ch, &$maxredirect = null) {
	  
	  $mr = $maxredirect === null ? 5 : intval($maxredirect);

	  if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	  } else {
	    
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

	    if ($mr > 0)
	    {
	      $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	      $newurl = $original_url;
	      
	      $url_data = parse_url($newurl);

	      if (!empty($url_data['user']) and !empty($url_data['pass'])){
	      	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
			curl_setopt($ch, CURLOPT_USERPWD, $url_data['user']. ":" . $url_data['pass']); 
			$newurl = $url_data['scheme'] . '://' . $url_data['host'];
            if (!empty($url_data['port'])){
                $newurl .= ':' . $url_data['port'];
            }
            $newurl .= $url_data['path'];
			if (!empty($url_data['query']))
			{
				$newurl .= '?' . $url_data['query'];	
			}
	      }

	      $rch = curl_copy_handle($ch);
	      
	      curl_setopt($rch, CURLOPT_HEADER, true);
	      curl_setopt($rch, CURLOPT_NOBODY, true);
	      curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
	      curl_setopt($rch, CURLOPT_CONNECTTIMEOUT, 5);
	      do
	      {
	        curl_setopt($rch, CURLOPT_URL, $newurl);
	        $header = curl_exec($rch);
	        if (curl_errno($rch)) {
	          $code = 0;
	        } else {
	          $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
	          if ($code == 301 || $code == 302) {
	            preg_match('/Location:(.*?)\n/', $header, $matches);
	            $newurl = trim(array_pop($matches));
	            
	            // if no scheme is present then the new url is a
	            // relative path and thus needs some extra care
	            if(!preg_match("/^https?:/i", $newurl)){
	              $newurl = $original_url . $newurl;
	            }   
	          } else {
	            $code = 0;
	          }
	        }
	      } while ($code && --$mr);
	      
	      curl_close($rch);
	      
	      if (!$mr)
	      {
	        if ($maxredirect !== null)	        
	        	$maxredirect = 0;
	        
	        return false;
	      }
	      curl_setopt($ch, CURLOPT_URL, $newurl);
	    }
	  }
	  return curl_exec($ch);
	}
	
endif;