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
        

        <link rel="dns-prefetch" href="//maxcdn.bootstrapcdn.com">
        <link rel="stylesheet" id="bootstrap.min.css-css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css?ver=4.9.4" type="text/css" media="all">

<style>
/* HEADER */
.header{
	box-shadow: 0 5px 22px 0 rgba(0,0,0,0.5);
	padding:30px 0px 20px 0px;
	position: relative;
	z-index: 10px;
}
.header .logo{
	display: inline-block;
	position: relative;
	width:75%;
    padding: 0px 0px 19px 0px;
}
.header .location{
	color: #033162;
	font-size: 20px;
	font-weight: 600;
	position:relative;
	padding-bottom: 20px;
}

.header .menu  ul{
	font-size:0px;
	list-style: none;
	margin: 0px;
	padding: 0px;
	text-align: right;
}
.header .menu li{
	display: block;
	position:relative;
}
.header .menu li a{
	color: #033162;
	display: inline-block;
	font-size: 14px;
	font-weight: 600;
	padding:10px 12px;
	text-decoration: none;
}

#header-menu li > a:after { font-family:fontawesome; content: ' \f0d7'; } 
#header-menu li > a:only-child:after { content: ''; }   

/*DROP*/
#header-menu ul ul{
	display:none;
}
#header-menu ul ul li a{
	color:#4a4a4a;
	font-size: 13px;
    font-weight: normal;
	padding:10px 28px;
}
#header-menu ul ul li a:hover{
	color:#000;
}
#header-menu ul li:hover > ul{
	display: block;
}



.header .phone{
	text-align: right;
	display: block;
}
.header .phone a{
	color: #033162;
	font-size: 27px;
	font-weight: 600;
	text-decoration: none;
}

#menu-toggle{
	display: inline-block;
	width: 40px;
	height: 25px;
	position: absolute;
	top:16px;
	right:15px;
	-webkit-transform: rotate(0deg);
	-moz-transform: rotate(0deg);
	-o-transform: rotate(0deg);
	transform: rotate(0deg);
	-webkit-transition: .5s ease-in-out;
	-moz-transition: .5s ease-in-out;
	-o-transition: .5s ease-in-out;
	transition: .5s ease-in-out;
	cursor: pointer;
}
#menu-toggle span{
	display: block;
	position: absolute;
	height: 5px;
	width: 100%;
	background: #003e7e;
	opacity: 1;
	left: 0;
	-webkit-transform: rotate(0deg);
	-moz-transform: rotate(0deg);
	-o-transform: rotate(0deg);
	transform: rotate(0deg);
	-webkit-transition: .25s ease-in-out;
	-moz-transition: .25s ease-in-out;
	-o-transition: .25s ease-in-out;
	transition: .25s ease-in-out;
}
#menu-toggle span:nth-child(1) {
	top: 0px;
}
#menu-toggle span:nth-child(2) {
	top: 10px;
}
#menu-toggle span:nth-child(3) {
	top: 20px;
}
#menu-toggle.open span:nth-child(1) {
	top: 10px;
	-webkit-transform: rotate(135deg);
	-moz-transform: rotate(135deg);
	-o-transform: rotate(135deg);
	transform: rotate(135deg);
}
#menu-toggle.open span:nth-child(2) {
	opacity: 0;
	left: -60px;
}
#menu-toggle.open span:nth-child(3) {
	top: 10px;
	-webkit-transform: rotate(-135deg);
	-moz-transform: rotate(-135deg);
	-o-transform: rotate(-135deg);
	transform: rotate(-135deg);
}



@media screen and (min-width: 992px) {

	/* HEADER */
	#menu-toggle{
		display: none;
	}
	.header{
		padding:30px 0px 0px 0px;
	}
	.header .menu{
	    margin: 30px 0px 0px 0px;
	}
	.header .menu li{
		display: inline-block;
	}
	.header .menu li a{
		font-size:11px;
		padding:10px 12px;
		position: relative;
    	z-index: 10;
		border-bottom: 4px solid rgba(255, 255, 255, 0.0);
	}
	.header .menu  li a:hover{
		border-bottom: 4px solid #033162;
	}
	.header .menu  li li a, .header .menu  li li a:hover{
		border-bottom: 0px;
	}
	#header-menu{
		display:block !important;
	}
	/*DROP*/
	#header-menu ul ul{
		display:none;
	}

	#header-menu ul > li > ul {
	    position: absolute;
	    top: 40px;
	    left: 0px;
	    z-index:10;
	    text-align: left;

	}
	#header-menu ul ul li a{
		padding:10px 15px;
		display:block;
	}
	#header-menu ul li:hover > ul{
		display: block;
		background-color: #fff;
		box-shadow: 2px 3px 3px #333;
		width: 220px;
		max-width: 320px;
		border: 0px solid #235f96;
		border-radius: 0px;
		margin-top: 5px;
	}
	.header > .menu > li:hover > a{
		border-bottom: 4px solid #033162;
	}

	#header-menu li li {
		display:block;
		position: relative;
	}
	#header-menu ul ul ul{
	    position: absolute;
	    top: 0px;
		left: 100%;
		margin-top: 0px !important;
	}	

}
            
@media screen and (min-width: 1200px) {

	.header .menu li a{
		font-size:14px;
		padding:10px 12px;
	}
	#menu-default li a{
		font-size: 14px;
	}
}
 </style>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js?ver=4.9.4"></script> 
<script type="text/javascript" src="https://360painting.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1"></script>
<script type="text/javascript" src="https://360painting.com/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js?ver=4.8.2"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js?ver=4.8.2"></script>
<script type="text/javascript" src="https://jan-pro.com/wp-content/themes/underboot/js/doubletaptogo.js?ver=4.8.2"></script>
       <script type="text/javascript" src="https://jan-pro.com/wp-content/themes/underboot/js/html5.js?ver=4.8.2"></script>
       <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        
        

        <?php wp_head(); ?>
        <?php  //eliminates phone changer error in console: ?>
        <script type="text/javascript">
            <!--
            vs_account_id = "";
            //-->

        </script>
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
                <div class="modal fade in" id="navbar-modal" tabindex="-1" role="dialog" 
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
                            </div>              
                            <!-- Modal Body -->
                            <div class="modal-body">
                                <nav class="main-navigation" role="navigation" id="header-menu">		
                                    <a href="#nav" title="Show navigation">Show navigation</a>
                                    <a href="#" title="Hide navigation">Hide navigation</a>
                                    <ul>
                                        <li><a href="/">Home</a></li>
                                        <li>
                                            <a href="/" aria-haspopup="true">Blog</a>
                                            <ul>
                                                <li><a href="/">Design</a></li>
                                                <li><a href="/">HTML</a></li>
                                                <li><a href="/">CSS</a></li>
                                                <li><a href="/">JavaScript</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="/" aria-haspopup="true">Work</a>
                                            <ul>
                                                <li><a href="/">Web Design</a></li>
                                                <li><a href="/">Typography</a></li>
                                                <li><a href="/">Front-End</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="/">About</a></li>
                                    </ul>
                                </nav>  
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
         </div>
        <script>

                

				$(document).ready(function(){
                        $("#header-menu").doubleTapToGo();
                    $(".main-navigation").doubleTapToGo();
                    $("#header-menu li:has(ul)").doubleTapToGo();
						$("#header-menu").slideToggle('fast');
					});
				
        </script>
        <!-- off nav modal -->

        <div class="fixthejump">
            <!--<header id="masthead" class="site-header" data-spy="affix" data-offset-top="100">-->
            <header id="masthead" class="site-header noaffix">

                <div class="container headcon">

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
                                    <?php echo do_shortcode( '[bng_location id="DUBTAP2GO"]');?>
                                    </span>
                                </div>

                                <div class="buttonwrap col-xs-2">
                                    <button type="button" class="btn" data-toggle="modal" data-target="#navbar-modal" aria-expanded="false">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
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
                <?php echo do_shortcode( '[bng_location id="DUBTAP2GO"]');?>
                </span>
            </div>


            <div class="buttonwrap col-xs-2">
 <button type="button" class="btn" data-toggle="modal" data-target="#navbar-modal" aria-expanded="false">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
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
        </div><!--container-->
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
