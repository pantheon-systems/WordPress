<?php
/**
 * The sidebar containing the Main Navigation.
 *
 *
 * @package qiigo\'s_theme
 */

 $homenav = do_shortcode('[bng_location id="location_path"]');
 
	if($homenav !== '') {
		   //path to the location is included in URLS
		   echo get_sidebar('nav-locations');
	  }
	  else {
		  //empty home nav means its corporate, no path to location is needed
		  echo get_sidebar('nav-corp');
	  }
                        
 ?>                     