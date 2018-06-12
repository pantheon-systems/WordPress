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

<<<<<<< HEAD
?><!DOCTYPE html>
<!-- 
~~~~~~~~~~********** This a Qiigo template below are instructions for initial set up. **********~~~~~~~~~~~
#
# this is the shortcode for 123 contact forms do_shortcode('[qiigo-contact-form id="form number"]');
#
#
#
#
#
#
#
#
~~~~~~~~~~********** This a Qiigo template above are instructions for initial set up. **********~~~~~~~~~~~
-->


<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M83BBD');</script>
<!-- End Google Tag Manager -->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M83BBD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- Qiigo Q Tag number q_tag field is created as a custom field-->
<div data-qtag-num="<?php //the_field('q_tag'); ?>" hidden></div>
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
=======
?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>

    <head>
        <!-- Google Tag Manager -->
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-XXXXX');

        </script>
        <!-- End Google Tag Manager -->
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <!--[if lt IE 9]>
	    <script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/selectivizr-min.js"></script>
	    <![endif]-->
	    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
       
        <?php wp_head(); ?>
        <?php  //eliminates phone changer error in console: ?> 
        <script type="text/javascript"><!--
        vs_account_id      = "";
            //--></script>    
            <?php
        if ( is_user_logged_in() ) {
            echo "<style>.container-fluid.redcon{top:147px;}</style>";
        } else {
            //do nothing
        }
        ?>
        
        <?php
        
//TESTING FOR REDIRECTS IN CONFIG FILE FOR SUBDOMAINS//////
//$array_shift = (explode('.', $_SERVER['HTTP_HOST']));
//        var_dump( $array_shift);
//        echo "array_shift<br>";
//$myurl = htmlspecialchars($_SERVER["REQUEST_URI"]);
//        var_dump( $myurl);
//        echo "myurl<br>";
//$myexp = ( explode ('.', $myurl) );
//        var_dump( $myexp);
//        echo "myexp<br>";
//$my_ref_url = $_SERVER['HTTP_REFERER'];
//        var_dump($my_ref_url);
//        echo "my_ref_url<br>";
//$my_ref_exp =  explode ('/', $my_ref_url);
//        var_dump($my_ref_exp);
//        echo "my_ref_exp<br>";
//TESTING FOR REDIRECTS IN CONFIG FILE FOR SUBDOMAINS//////


//	THIS BLOCK OF CODE WRITES LINK TAG THAT ADDS THE HREFLANG ATTRIBUTE 

        //get the qiigo location path variable, supposed to be the same as the location slug:
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
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    </head>

    <body <?php body_class(); ?>>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <!-- Qiigo Q Tag number -->
        <div data-qtag-num="<?php the_field('q_tag'); ?>" hidden></div>
        <!-- END Qiigo Q Tag number -->




        <!-- Modal -->
        <div class="modal fade" id="myModalCTA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
>>>>>>> cfd83a2ab8ea94d0c861619dd855f2faa36d4f00
                                   <span aria-hidden="true">&times;</span>
                                   <span class="sr-only">Close</span>
                            </button>
                        <!--optional modal label here -->
                        <h3>Schedule a Free Estimate</h3>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" id="123contactForm">
                        <!-- qiigoforms.com script begins here -->
                        <?php
						   $getmyurl = $_SERVER['REQUEST_URI'];	      
						   $myexplode = ( explode ('/', $getmyurl) );				   						   
						   ?>
                            <div class="qiigoforms-wrapper">
                                <script type="text/javascript" defer src="//qiigoforms.com/embed/2231765.js" data-role="form" data-custom-vars="control23252913=<?php echo $myexplode[1];?>"></script>
                                <?php  //sandy, you created input control28863378 as a textarea to try and catch the referrer  ?>
                                <!-- qiigoforms.com script ends here -->
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- off search modal -->

        <!-- Modal for Mobile Navigation -->
        <div id="theredbar" class="container-fluid redcon noBumper">
            <div class="container">
                <div class="modal fade in" id="navbar-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                   <span aria-hidden="true">&times;</span>
                                   <span class="sr-only">Close</span>
                            </button>
                            </div>
                            <!-- Modal Body -->
                            <div class="modal-body">
                               <nav class="site-main-navigation">
                    <div class="container">
                       <?php get_sidebar(nav);?>
                    </div>
                </nav>
                            </div>
                        </div>
<<<<<<< HEAD
                        
                        <!-- Modal Body -->
                        <div class="modal-body">
                         	<div class=""><p><?php do_shortcode('[qiigo-contact-form id="Enter 123 contact form number here"]'); ?>Usually a form goes here.</p></div>
                        </div>  
=======
>>>>>>> cfd83a2ab8ea94d0c861619dd855f2faa36d4f00
                    </div>
                </div>
            </div>
        </div>
        <!-- off nav modal -->

        <div class="fixthejump">
            <!--<header id="masthead" class="site-header" data-spy="affix" data-offset-top="100">-->
            <header id="masthead" class="site-header noaffix" <div class="container headcon">

                <?php
				// if it's on the slider (home page) template, then wrap it with .t-slider, and style it differently for better branding on the home page mobile-------------------
				if (is_page_template( 't-home-template.php' ) || is_page_template( 't-home-no-title.php' )){   
				?>
                    <div class="home-template">
                        <div class="row logorow">
                            <?php //logo for the desktop?>
                            <div id="logo" class="col-xs-4 col-sm-2">
                                <?php $my_locationname_code = do_shortcode( '[bng_location id="location_path"]' ); ?>
                                <a title="Home page for 360 Painting" href="<?php echo get_site_url(); ?>/<?php if ($my_locationname_code != '') {echo do_shortcode( '[bng_location id=" location_path "]' ) . '/';}?>"><img class="logo-image" src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/logo." alt="LOGO GOES HERE"/></a>

                            </div>
                            <div id="phone" class="col-xs-6 col-sm-10"><span class="fa fa-phone"></span>
                                <?php echo do_shortcode( '[bng_location id="primary_phone"]');?>
                                </span>
                            </div>

                            <div class="buttonwrap col-xs-2">
                                <div id="menu-toggle">
									<span></span>
									<span></span>
									<span></span>
								</div>
                            </div>
                        </div>
                        <!--off logorow-->
                        <?php // had to do it. a second logo visible only on mobile t-slider template ?>
                        <div class="row logo2 visible-xs">

                            <a href="<?php echo get_site_url(); ?>/<?php if ($my_locationname_code != '') {echo do_shortcode( '[bng_location id=" location_path "]' ) . '/';}?>" title="Home page for 360 Painting"><img class="logo-image" src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/logo." alt="LOGO GOES HERE"/></a>
                        </div>




                        <!-- must be inside headcon rel pos -->
                        <div id="social-media">
                            <?php get_sidebar(social);?>
                        </div>

                        <ul id="utility">
                            <li><a href="https://360painting-franchise.com/" target="_blank" title="Start a Franchise"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span><span class="textinline">Start a Franchise</span></a></li>
                            <li><a href="<?php echo get_site_url(); ?>/locations/" title="Find a location for 360 Painting" title="Find a Location"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><span class="textinline">Find a Location</span></a></li>

                        </ul>

                    </div>
                    <!--off t-slider template mobile container-->

        </div>
        <!--container-->



        <?php }
				else { 
				//now for all the other pages, including bumper styles------------------------------------------------------------------------------------------------
				?>

        <div class="row logorow">
            <div id="logo" class="col-xs-4 col-sm-2"><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id=" location_path "]' );?>/" title="Home page for 360 Painting"><img class="logo-image" src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/logo." alt="LOGO GOES HERE"/></a></div>
            <div id="phone" class="col-xs-6 col-sm-10"><span class="number"><span class="fa fa-phone"></span>
                <?php echo do_shortcode( '[bng_location id="primary_phone"]');?>
                </span>
            </div>


            <div class="buttonwrap col-xs-2">
                <div id="menu-toggle">
									<span></span>
									<span></span>
									<span></span>
								</div>
            </div>
        </div>
        <!--off logorow-->



        <!-- must be inside headcon rel pos -->
        <div id="social-media">
            <?php get_sidebar(social);?>
        </div>

        <ul id="utility">
            <li><a href="https://360painting-franchise.com/" target="_blank" title="Start a Franchise"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span><span class="textinline">Start a Franchise</span></a></li>
            <li><a href="<?php echo get_site_url(); ?>/locations/" title="Find a location for 360 Painting" title="Find a Location"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span><span class="textinline">Find a Location</span></a></li>
        </ul>
        <!--</div>container-->
        <?php } ?>
        </div>
        <!-- fix the jump-->
        </header>
        <button class="btn-cta" data-toggle="modal" data-target="#myModalCTA">
            	<a href="#" title="Schedule a Free Estimate">Call to Action</a>
        	</button>

        <?php 
			$page_id = get_queried_object_id();
			if ( has_post_thumbnail( $page_id )) :
				$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $page_id ), 'optional-size' );
				$image = $image_array[0];?>
        <div class="entry-header" style="background-image:url(<?php echo $image; ?>);background-repeat:repeat-x; background-size:cover;background-position:center;">
        </div>
        <?php 	
			else : 
				//$image = get_template_directory_uri() . '/images/default-background.jpg';
                $image = "https://360painting.com/wp-content/uploads/2016/11/happy-family-page.jpg"; ?>
        <div class="entry-header" style="background-image:url(<?php echo $image; ?>);background-repeat:repeat-x; background-size:cover;background-position:center;">
        </div>

        <?php endif;
		
	?>
        <div id="content" class="site-content row">
