<?php
if ( ! function_exists('wp_all_export_rand_char')){

	function wp_all_export_rand_char($length) {
		
		$random = '';
	  
		do
		{
	  		$random .= str_replace(array('-', '_'), '', wp_all_export_url_title(chr(mt_rand(33, 126))));
	  	} 
	  	while (strlen($random) < $length); 

	  	return $random;
	}
}