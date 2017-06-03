<?php
   /*
    * This adds no-cache headers to certain pages within the WordPress site
    */
    function add_nocache_headers($content) {
       if ($GLOBALS['post']->ID == '539') { 
          nocache_headers();
       }
       return $content;
    }
    add_filter('the_content', 'add_nocache_headers');
	
	add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
	function theme_enqueue_styles() {
    	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	}
?>
