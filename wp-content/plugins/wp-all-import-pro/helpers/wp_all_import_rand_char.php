<?php
if ( ! function_exists('wp_all_import_rand_char')){

	function wp_all_import_rand_char($length) {
	  $random = '';
	  for ($i = 0; $i < $length; $i++) {
	    $random .= chr(mt_rand(33, 126));
	  }
	  return $random;
	}
}