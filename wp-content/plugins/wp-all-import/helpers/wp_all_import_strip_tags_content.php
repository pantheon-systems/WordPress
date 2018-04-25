<?php
if ( ! function_exists('wp_all_import_strip_tags_content')){

	function wp_all_import_strip_tags_content($text, $tags = '', $invert = FALSE) {

	  preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
	  $tags = array_unique($tags[1]);
	   
	  if(is_array($tags) AND count($tags) > 0) {
	    if($invert == FALSE) {
	      return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
	    }
	    else {
	      return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
	    }
	  }
	  elseif($invert == FALSE) {
	    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
	  }
	  return $text;
	} 
}