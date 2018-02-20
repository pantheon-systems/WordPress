<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package underboot
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
		<?php

		//	THIS BLOCK OF CODE WRITES LINK TAG THAT ADDS THE HREFLANG ATTRIBUTE 

		$host = $_SERVER['SERVER_NAME'];
		$field = get_field_object('languagetype');
		$value = $field['value'];
		//$label = $field['choices'][ $value ];
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		

			// CHECKS FOR THE URL FROM ADDRESS BAR TO PRINT print $host . '<br>';
			// GATHERS FIELD OPTIONS CANADIAN AND ENGLISH print $field[0] . '<br>';
			// RETURNS ENGLISH OR CANADIAN print $value . '<br>';
			// RETURNS HTTP OR HTTPS print $protocol;


		 // THE FIRST IF STATEMENT IS CHECKING FOR THE RADIO BUTTON VALUE OF "CANADIAN" AND THEN IT LOOKS TO SEE IF THE "hreflang" FIELD HAS BEEN POPULATED. IF BOTH OF THESE ARE TRUE THEN THE LINK IS WRITTEN AS CANADIAN AND THEN PULLS THE URL AND ADDS WHATEVER HAS BEEN ENTERED IN 'hreflang' BY THE USER.
		
		if ($value === 'Canadian' && get_field('hreflang')) {?>
			<link rel="alternate" href="<?php echo $protocol . '://' . $host; the_field('hreflang');?>" hreflang="en-ca" />
		<?php }
		 // IF ONLY CANADIAN === TRUE THEN WRITE THE URL AS AN EXACT MATCH FOR CANADIAN VERSION.
		elseif ($value === 'Canadian') {?>
			<link rel="alternate" href="<?php echo get_permalink();?>" hreflang="en-ca" />

		<?php }
		 //THIS IS A REPEAT OF ABOVE FOR ENGLISH
		elseif ($value === 'English' && get_field('hreflang')) {?>
			<link rel="alternate" href="<?php echo $protocol . '://' . $host; the_field('hreflang');?>" hreflang="en-us" />
		<?php }
 		//THIS GETS PRINTED IF ALL ELSE FAILS
		else {?>
			<link rel="alternate" href="<?php echo get_permalink();?>" hreflang="en-us" />
		<?php }	?>
</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Qiigo Q Tag number -->
    <div data-qtag-num="<?php the_field('q_tag'); ?>" hidden></div>
<!-- END Qiigo Q Tag number -->

<div id="page" class="site container">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'underboot' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<?php
			if ( is_front_page() && is_home() ) : ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php
			endif;

			$description = get_bloginfo( 'description', 'display' );
			if ( $description || is_customize_preview() ) : ?>
				<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
			<?php
			endif; ?>
            
             <button class="btn-cta" data-toggle="modal" data-target="#myModalCTA">
            	<a href="#" title="Schedule a Free Estimate">Call to Action</a>
        	</button>        
            <!-- Modal -->
            <div class="modal fade" id="myModalCTA" tabindex="-1" role="dialog" 
                 aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <button type="button" class="close" 
                               data-dismiss="modal">
                                   <span aria-hidden="true">&times;</span>
                                   <span class="sr-only">Close</span>
                            </button>
                            <!--optional modal label here -->
                            <p class="text-primary text-capitalize">Call to Action</p>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="modal-body">
                         	<p>Usually a form goes here.</p>
                        </div>  
                    </div>
                </div>
            </div>
            <!-- off search modal -->
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation navbar navbar-default" role="navigation" data-spy="affix" data-offset-top="20">
			<button class="navbar-toggle collapsed" aria-controls="primary-menu" data-toggle="collapse" data-target="#primary-menu" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
        	</button>		
			<?php //wp_nav_menu( array( 'theme_location' => 'default', 'menu_id' => 'default') ); ?>   
            <?php // fyi: Bootstrap only supports 2 levels of dropdowns; if we need more, go back to wp_nav_menu and create css/JS to make dropdowns work.
                        wp_nav_menu( array(
                            'menu'              => 'default',
                            'theme_location'    => 'default',
                            'depth'             => 4,
                            'container'         => 'div',
                            'container_class'   => 'collapse navbar-collapse',
                   			'container_id'      => 'primary-menu',
                            'menu_class'        => 'nav navbar-nav',
                            'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                            'walker'            => new wp_bootstrap_navwalker())
                        );
                    ?> 			
       
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="content" class="site-content row">
