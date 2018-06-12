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
</head>

<body <?php body_class(); ?>>
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
