<?php
/**
 * The sidebar containing the Main Navigation.
 *
 *
 * @package qiigo\'s_theme
 */

// $homenav = do_shortcode('[bng_location id="location_path"]');
 
//	if($homenav !== '') {
		   //path to the location is included in URLS
//		   echo get_sidebar('nav-locations');
//	  }
//	  else {
		  //empty home nav means its corporate, no path to location is needed
//		  echo get_sidebar('nav-corp');
//	  }
	  
 
 
	$location_code = do_shortcode('[bng_location id="location_code"]');		
	?>
	

 	<nav class="main-navigation" role="navigation" id="header-menu">
		
		<?php 
            wp_nav_menu(
                array(
                    //'menu'              => $location_code,
                    'menu'              => 'index',
                    'theme_location'    => 'primary-menu',
                    'depth'             => 4,
					'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                	//'walker'            => new WP_Bootstrap_Navwalker()
                )
                
            ); 
        ?>  
    </nav>     