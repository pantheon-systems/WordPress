<?php 

$options = get_nectar_theme_options(); 
$external_dynamic = (!empty($options['external-dynamic-css']) && $options['external-dynamic-css'] == 1) ? 'on' : 'off';

	if($external_dynamic != 'on') {

		ob_start(); 

		//boxed css
		if(!empty($options['boxed_layout']) && $options['boxed_layout'] == '1')  {
			
			$attachment = $options["background-attachment"];
			$position = $options["background-position"];
			$repeat = $options["background-repeat"];
			$background_color = $options["background-color"];
			
			echo '<style type="text/css">
			 body {
			 	background-image: url("'.nectar_options_img($options["background_image"]).'");
				background-position: '.$position.';
				background-repeat: '.$repeat.';
				background-color: '.$background_color.'!important;
				background-attachment: '.$attachment.';';
				if(!empty($options["background-cover"]) && $options["background-cover"] == '1') {
					echo 'background-size: cover;
					-moz-background-size: cover;
					-webkit-background-size: cover;
					-o-background-size: cover;';
				}
				
			 echo '} 
			</style>';
		}
	}
	
	//top nav
	$theme_skin = ( !empty($options['theme-skin']) ) ? $options['theme-skin'] : 'original';
	$headerFormat = (!empty($options['header_format'])) ? $options['header_format'] : 'default';
	if($headerFormat == 'centered-menu-bottom-bar') { $theme_skin = 'material'; }
	
	$logo_height = (!empty($options['use-logo']) && !empty($options['logo-height'])) ? intval($options['logo-height']) : 30;
	$mobile_logo_height = (!empty($options['use-logo']) && !empty($options['mobile-logo-height'])) ? intval($options['mobile-logo-height']) : 24;
	$header_padding = (!empty($options['header-padding'])) ? intval($options['header-padding']) : 28;
	$nav_font_size = (!empty($options['navigation_font_family']['font-size']) && $options['navigation_font_family']['font-size'] != '-') ? intval(substr($options['navigation_font_family']['font-size'],0,-2) *1.4 ) : 20;
	$dd_indicator_height = (!empty($options['use-custom-fonts']) && $options['use-custom-fonts'] == 1 && !empty($options['navigation_font_size']) && $options['navigation_font_size'] != '-') ? intval(substr($options['navigation_font_size'],0,-2)) -1 : 20;
	$headerFormat = (!empty($options['header_format'])) ? $options['header_format'] : 'default';
	$shrinkNum = (!empty($options['header-resize-on-scroll-shrink-num'])) ? intval($options['header-resize-on-scroll-shrink-num']) : 6;
	$perm_trans = (!empty($options['header-permanent-transparent'])) ? $options['header-permanent-transparent'] : 'false';
  $headerResize = (!empty($options['header-resize-on-scroll']) && $perm_trans != '1' && $headerFormat != 'centered-menu-bottom-bar') ? $options['header-resize-on-scroll'] : '0'; 
	
	$hideHeaderUntilNeeded = (!empty($options['header-hide-until-needed']) && $headerFormat != 'centered-menu-bottom-bar') ? $options['header-hide-until-needed'] : '0';
	$body_border = (!empty($options['body-border'])) ? $options['body-border'] : 'off';
	$headerRemoveStickiness = (!empty($options['header-remove-fixed'])) ? $options['header-remove-fixed'] : '0'; 
	
	if($hideHeaderUntilNeeded == '1' || $body_border == '1' || $headerFormat == 'left-header' || $headerRemoveStickiness == '1') { $headerResize = '0'; }
	
	$padding_top = ceil(($logo_height/2)) - ceil(($nav_font_size/2));
	$padding_bottom = (ceil(($logo_height/2)) - ceil(($nav_font_size/2))) + $header_padding;
	
	//12 = half the search btn icon height
	$search_padding_top = ceil($logo_height/2) - 12;
	$search_padding_bottom =  ceil($logo_height/2) - 12;
	
	$using_secondary = (!empty($options['header_layout'])) ? $options['header_layout'] : ' ';
	
	$menu_item_spacing = (!empty($options['header-menu-item-spacing'])) ? $options['header-menu-item-spacing'] : '10';
	
	if($theme_skin == 'material') {
		$extra_secondary_height = ($using_secondary == 'header_with_secondary') ? 40 : 0;
	} else {
		$extra_secondary_height = ($using_secondary == 'header_with_secondary') ? 32 : 0;
	}
	
	if($headerFormat == 'centered-menu-bottom-bar') {
	 	$header_space = $logo_height + ($header_padding*3) + $nav_font_size + $extra_secondary_height;
	}	
	else if($headerFormat == 'centered-menu-under-logo') {
	 	$header_space = $logo_height + ($header_padding*2) + 20 + $nav_font_size + $extra_secondary_height;
	}	
	else {
		 	$header_space = $logo_height + ($header_padding*2) + $extra_secondary_height;
	}

	$page_transition_bg = (!empty($options['transition-bg-color'])) ? $options['transition-bg-color'] : '#ffffff';
	$page_transition_bg_2 = (!empty($options['transition-bg-color-2'])) ? $options['transition-bg-color-2'] : $page_transition_bg;

	//woo product title
	$wooSocial = ( !empty($options['woo_social']) && $options['woo_social'] == 1 ) ? '1' : '0';
	$wooSocialCount = 0;
	$wooProductTitlePadding = 0;
	
	if($wooSocial == '1') {
		if(!empty($options['woo-facebook-sharing']) && $options['woo-facebook-sharing'] == 1) $wooSocialCount++;
		if(!empty($options['woo-twitter-sharing']) && $options['woo-twitter-sharing'] == 1) $wooSocialCount++;
		if(!empty($options['woo-pinterest-sharing']) && $options['woo-pinterest-sharing'] == 1) $wooSocialCount++;
		if(!empty($options['woo-google-plus-sharing']) && $options['woo-google-plus-sharing'] == 1) $wooSocialCount++;
		if(!empty($options['woo-linkedin-sharing']) && $options['woo-linkedin-sharing'] == 1) $wooSocialCount++;

		if(empty($options['product_tab_position']) || $options['product_tab_position'] == 'in_sidebar') $wooProductTitlePadding = ($wooSocialCount*52) + 50;
	}
	
	//legacy WP header changes
	if(floatval(get_bloginfo('version')) < "3.8"){
		echo '<style>
		html .admin-bar #header-outer, html .logged-in.buddypress #header-outer { top: 28px; } html .admin-bar #header-outer[data-using-secondary="1"], html .logged-in.buddypress #header-outer[data-using-secondary="1"] { top: 60px; }
		</style>';
	}

	$custom_loading_icon = null;

	if(isset($options['loading-image']['id'])){
		$custom_loading_icon = ' .portfolio-loading, #ajax-loading-screen .loading-icon, .loading-icon, .pp_loaderIcon { background-image: url("'.nectar_options_img( $options["loading-image"] ).'"); } ';
	} else {
		if (!empty($options['loading-image'])) { 
		    $custom_loading_icon = ' .portfolio-loading, #ajax-loading-screen .loading-icon, .loading-icon, .pp_loaderIcon { background-image: url("'. $options["loading-image"] .'"); } ';
		} 
	}

	
	$headerFormat = (!empty($options['header_format'])) ? $options['header_format'] : 'default';
	$small_matieral_header_space = (($header_padding/1.8)*2) + $logo_height - $shrinkNum;

	if($external_dynamic != 'on') { echo '<style type="text/css">'; }

	  if($headerFormat != 'left-header') {

	  	$material_header_space = $logo_height + ($header_padding*2);

		  echo '

		  @media only screen and (min-width: 1001px) {

				#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9 #logo {  margin-top: -'. $header_padding/2 .'px; }
				#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9 nav >ul >li:not(#social-in-menu):not(#nectar-user-account):not(#search-btn):not(.slide-out-widget-area-toggle) > a { padding-bottom: '.$header_padding.'px;  }
			  body.material #header-outer #logo, body.material #header-outer .logo-spacing { margin-top: '.$header_padding.'px; margin-bottom: '.$header_padding.'px; position: relative; }
			  body.material #header-outer.small-nav #logo, body.material #header-outer.small-nav .logo-spacing { margin-top: '. ($header_padding/1.8) .'px; margin-bottom: '. ($header_padding/1.8) .'px; } 
			  body.material #header-outer.small-nav #logo img, body.material #header-outer.small-nav .logo-spacing img { height: '. ($logo_height - $shrinkNum) .'px; } 

			  .material #header-outer:not(.transparent) .bg-color-stripe { top: '.$material_header_space.'px; height: calc(35vh - '.$material_header_space.'px); }
			  .material #header-outer:not(.transparent).small-nav .bg-color-stripe { top: '.$small_matieral_header_space.'px; height: calc(35vh - '.$small_matieral_header_space.'px); }
		  }
		  @media only screen and (max-width: 1000px) {
		  	.material #header-outer:not([data-permanent-transparent="1"]):not(.transparent) .bg-color-stripe, .material #header-outer:not([data-permanent-transparent="1"]).transparent .bg-color-stripe { top: '. ($mobile_logo_height+24) .'px; height: calc(30vh - '. ($mobile_logo_height+24) .'px); }
		  }

		  body:not(.material) #header-outer, .ascend #header-outer[data-full-width="true"][data-using-pr-menu="true"] header#top nav ul.buttons li.menu-item, 
			.ascend #header-outer[data-full-width="true"][data-format="centered-menu"] header#top nav ul.buttons li#social-in-menu { padding-top: '.$header_padding.'px; }

		  body:not(.material) #header-outer[data-format="centered-menu-under-logo"] .span_3 { padding-bottom: '.$header_padding.'px; }
		  
		  #header-outer #logo img, body.material #header-outer .logo-spacing img { height: ' . $logo_height .'px; }';

		  echo '.ascend #header-outer[data-full-width="true"] header#top nav > ul.buttons { margin-top: -'.$header_padding.'px; }';

		  if($headerFormat != 'centered-menu-under-logo') { 
		  
			  echo 'body:not(.material) header#top nav > ul > li:not(#social-in-menu) > a {
			  	padding-bottom: '. $padding_bottom .'px;
			  	padding-top: '. $padding_top .'px;
			  }
			  body:not(.material) header#top nav > ul > li#social-in-menu > a {
				  margin-top: '. $padding_top .'px;
			  }
			  '; 
			}

		  if($headerFormat != 'centered-menu-under-logo') { 
			echo 'body:not(.material) #header-outer .cart-menu {
		 		padding-bottom: '. intval( $padding_bottom + ceil(($nav_font_size - 21)/2) ) .'px;
				padding-top: '. intval( $padding_top + $header_padding + ceil(($nav_font_size - 21)/2) ) .'px;
			 }';
		 } 
		  
		 echo' body:not(.material) header#top nav > ul li#search-btn, 
		 body:not(.material) header#top nav > ul li#nectar-user-account, 
		 header#top nav > ul li.slide-out-widget-area-toggle {
		  	 padding-bottom: '. $search_padding_bottom .'px;
			   padding-top: '. $search_padding_top .'px;
		  }
			
		.ascend #header-outer[data-full-width="true"] header#top nav > ul li#search-btn a, 
		.ascend #header-outer[data-full-width="true"] header#top nav > ul li#nectar-user-account a, 
		.ascend #header-outer[data-full-width="true"] header#top nav > ul li.slide-out-widget-area-toggle a,
		.ascend #header-outer[data-full-width="true"] .cart-menu {
			padding-bottom: '. (intval($search_padding_bottom) + intval($header_padding)) .'px;
			padding-top: '. (intval($search_padding_top) + intval($header_padding)) .'px;
		}	

		 body:not(.material) header#top .sf-menu > li.sfHover > ul { top: '.$nav_font_size.'px; }

		

		 #header-outer[data-lhe="animated_underline"] header#top nav > ul > li > a,
		 header#top nav > ul > li[class*="button_solid_color"] > a, 
		 body #header-outer:not([data-lhe="animated_underline"]) header#top nav ul li[class*="button_solid_color"] a:hover,
		 #header-outer[data-lhe="animated_underline"] header#top nav > ul > li[class*="button_bordered"] > a,
		 header#top nav > ul > li[class*="button_bordered"] > a, body #header-outer.transparent header#top nav > ul > li[class*="button_bordered"] > a,
		 body #header-outer:not([data-lhe="animated_underline"]) header#top nav ul li[class*="button_bordered"] a:hover,
		 body #header-outer.transparent header#top nav > ul > li[class*="button_solid_color"] > a, 
		 #header-outer[data-lhe="animated_underline"] header#top nav > ul > li[class*="button_solid_color"] > a { margin-left: '.$menu_item_spacing.'px; margin-right: '.$menu_item_spacing.'px; }
		 #header-outer[data-lhe="default"] header#top nav > ul > li > a { padding-left: '.$menu_item_spacing.'px; padding-right: '.$menu_item_spacing.'px; }
		 
		 #header-outer[data-lhe="animated_underline"][data-condense="true"][data-format="centered-menu-bottom-bar"].fixed-menu header#top nav > ul > li > a { margin-left: '. floor($menu_item_spacing/1.3) .'px; margin-right: '. floor($menu_item_spacing/1.3) .'px; }
		 #header-outer[data-lhe="default"][data-condense="true"][data-format="centered-menu-bottom-bar"].fixed-menu header#top nav > ul > li > a { padding-left: '. floor($menu_item_spacing/1.3) .'px; padding-right: '. floor($menu_item_spacing/1.3) .'px; }
		 ';
		 
		 $dropdownArrows = (!empty($options['header-dropdown-arrows']) && $headerFormat != 'left-header' ) ? $options['header-dropdown-arrows'] : 'inherit'; 
		 if( $dropdownArrows == 'show' && intval($menu_item_spacing) > 10) {
			 echo '#header-outer .sf-menu > .sf-with-ul > a { padding-right: '. intval(intval($menu_item_spacing) + 10) .'px!important;}
			 			 #header-outer[data-lhe="animated_underline"] .sf-menu > .sf-with-ul > a { padding-right: 10px!important;}
						 #header-outer[data-lhe="default"][data-condense="true"][data-format="centered-menu-bottom-bar"].fixed-menu header#top nav .sf-menu > .sf-with-ul > a { padding-right: '. intval( floor($menu_item_spacing/1.3) + 10) .'px!important;}';
		 }
		 
		 if(intval($menu_item_spacing) > 15) {
			 echo 'body.material[data-header-format="default"] #header-outer[data-has-buttons="yes"]:not([data-format="left-header"]) header#top nav >ul.buttons { margin-left: '. intval($menu_item_spacing)*2 .'px; }';
		 }
		 if(intval($menu_item_spacing) > 30) {
		 	  echo 'body.material[data-header-format="default"] #header-outer[data-format="default"] #social-in-menu { margin-left: '. intval($menu_item_spacing) .'px;  } ';
	   }



	} else {
		echo '#header-outer #logo img { height: ' . $logo_height .'px; } 
		html body[data-header-format="left-header"] #header-outer .row .col.span_9 { top: '. intval($logo_height+40) .'px; }';
	}
	
	//blog archive 
	if( (is_archive() || is_author() || is_category() || is_home() || is_tag()) && 'post' == get_post_type() ) {
		$blog_type = (!empty($options['blog_type'])) ? $options['blog_type'] : 'masonry-blog-fullwidth';
		if($blog_type == 'masonry-blog-full-screen-width') {
			
			$blog_masonry_type = (!empty($options['blog_masonry_type'])) ? $options['blog_masonry_type'] : 'auto_meta_overlaid_spaced';
			
			if($theme_skin == 'material') {
				echo 'body[data-header-resize] .container-wrap { padding-top: 0; }';
				echo 'html body[data-bg-header="false"].archive .container-wrap { padding-top: 40px; }
							html body[data-bg-header="true"].archive .container-wrap { padding-top: 40px!important; }';
				if($blog_masonry_type == 'auto_meta_overlaid_spaced' || $blog_masonry_type == 'meta_overlaid') {
					echo 'html body[data-bg-header="true"].archive .container-wrap { padding-top: 0!important; }';
				}
				
			} else {
				echo 'html body[data-bg-header="false"].archive .container-wrap { padding-top: 40px; margin-top: 0; }
							html body[data-bg-header="true"].archive .container-wrap { padding-top: 40px!important; }';
				if($blog_masonry_type == 'auto_meta_overlaid_spaced' || $blog_masonry_type == 'meta_overlaid') {
					echo 'html body[data-bg-header="true"].archive .container-wrap { padding-top: 0!important; }';
				}
				
			}
			
		} //using full width masonry blog
	} // if archive
	
	
	//header nav bar related
	global $post;
	if(!empty($options['transparent-header']) && $options['transparent-header'] == '1' && isset($post->ID) ) {
		$activate_transparency = using_page_header($post->ID); 
	} else {
		$activate_transparency = false;
	}

	 echo '#header-space { height: '. $header_space .'px;}
	 @media only screen and (max-width: 1000px) {
		 body.material #header-space { height: '. (intval($mobile_logo_height) + 24) .'px;}
		 body:not(.material) #header-space { height: '. (intval($mobile_logo_height) + 38) .'px;}
	 }';
	 
	 $header_extra_space_to_remove = $extra_secondary_height;
	 
	 if($headerFormat == 'centered-menu-under-logo' || $headerFormat == 'centered-menu-bottom-bar') {
		 $header_extra_space_to_remove += 20;
	 } else {
		 $header_extra_space_to_remove += intval($header_padding);
	 }
	 
	 /* desktop calcs */
	 if($external_dynamic != 'on') {
		 
				 if($activate_transparency || $headerFormat == 'left-header'){
						 echo '@media only screen and (min-width: 1000px) {
						 #page-header-wrap.fullscreen-header,
						 #page-header-wrap.fullscreen-header #page-header-bg,
						 html:not(.nectar-box-roll-loaded) .nectar-box-roll > #page-header-bg.fullscreen-header,
						 .nectar_fullscreen_zoom_recent_projects,
						 #nectar_fullscreen_rows:not(.afterLoaded) > div {
						   height: 100vh;
						 }
						 
						 .wpb_row.vc_row-o-full-height.top-level, .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: 100vh; }';
						 
						 if(is_admin_bar_showing()) {
							 echo '.admin-bar #page-header-wrap.fullscreen-header,
							 .admin-bar #page-header-wrap.fullscreen-header #page-header-bg,
							 .admin-bar .nectar_fullscreen_zoom_recent_projects,
							 .admin-bar #nectar_fullscreen_rows:not(.afterLoaded) > div {
								 height: calc(100vh - 32px);
							 }
							 .admin-bar .wpb_row.vc_row-o-full-height.top-level, .admin-bar .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - 32px); }';
						 }
						 
						 if($headerFormat != 'left-header') {
							 echo '#page-header-bg[data-alignment-v="middle"] .span_6 .inner-wrap,
							 #page-header-bg[data-alignment-v="top"] .span_6 .inner-wrap {
								 padding-top: '. (intval($header_space) - $header_extra_space_to_remove) .'px;
							 }';
						 }
						 
						 echo '.nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container {
							 height: calc(100vh + 2px)!important;
						 }
						 .admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), .admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container {
							 height: calc(100vh - 30px)!important;
						 }

						 
					 }';
					 
				 } else {
					 
					 echo '@media only screen and (min-width: 1000px) {  
						 body #ajax-content-wrap.no-scroll { min-height:  calc(100vh - '. ($header_space) .'px);	height: calc(100vh - '. ($header_space) .'px)!important; } 
					 }';
					 

					 echo '@media only screen and (min-width: 1000px) { 
						 #page-header-wrap.fullscreen-header,
						 #page-header-wrap.fullscreen-header #page-header-bg,
						 html:not(.nectar-box-roll-loaded) .nectar-box-roll > #page-header-bg.fullscreen-header,
						 .nectar_fullscreen_zoom_recent_projects,
						 #nectar_fullscreen_rows:not(.afterLoaded) > div {
							 height: calc(100vh - '. ($header_space - 1) .'px);
						 } 
						 
						 .wpb_row.vc_row-o-full-height.top-level, .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. ($header_space - 1) .'px); }
						 
						 html:not(.nectar-box-roll-loaded) .nectar-box-roll > #page-header-bg.fullscreen-header { top: '.$header_space.'px; }';
						 
						 if(is_admin_bar_showing()) {
							 echo '.admin-bar #page-header-wrap.fullscreen-header,
							 .admin-bar #page-header-wrap.fullscreen-header #page-header-bg,
							 .admin-bar .nectar_fullscreen_zoom_recent_projects,
							 .admin-bar #nectar_fullscreen_rows:not(.afterLoaded) > div {
								 height: calc(100vh - '. ($header_space - 1) .'px - 32px);
							 }
							 .admin-bar .wpb_row.vc_row-o-full-height.top-level, .admin-bar .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. ($header_space - 1) .'px - 32px); }';
						 }
						 
						 echo '.nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container {
							 height: calc(100vh - '. ($header_space - 2) .'px)!important;
						 } 
						 
						 .admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), .admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container  {
							 height: calc(100vh - '. ($header_space - 2) .'px - 32px)!important;
						 }
				 }';

		 	}
			
	} //not using external
	
	/* mobile calcs */
	if($theme_skin == 'material') {
		echo '@media only screen and (max-width: 1000px) {
			
			.using-mobile-browser #page-header-wrap.fullscreen-header,
			.using-mobile-browser #page-header-wrap.fullscreen-header #page-header-bg,
			.using-mobile-browser #nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div {
				height: calc(100vh - '. (intval($mobile_logo_height) + 76) .'px);
			}
			.using-mobile-browser .wpb_row.vc_row-o-full-height.top-level, .using-mobile-browser .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 76) .'px); }
			';
			
			if(is_admin_bar_showing()) {
				echo '.admin-bar #page-header-wrap.fullscreen-header,
				.admin-bar #page-header-wrap.fullscreen-header #page-header-bg,
				html:not(.nectar-box-roll-loaded) .admin-bar .nectar-box-roll > #page-header-bg.fullscreen-header,
				.admin-bar .nectar_fullscreen_zoom_recent_projects,
				.admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), 
				.admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container,
				.admin-bar #nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div  {
					height: calc(100vh - '. (intval($mobile_logo_height) + 23) .'px - 46px);
				}
				.admin-bar .wpb_row.vc_row-o-full-height.top-level, .admin-bar .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 23) .'px - 46px); }
				';
			} else {
				echo '#page-header-wrap.fullscreen-header,
				#page-header-wrap.fullscreen-header #page-header-bg,
				 html:not(.nectar-box-roll-loaded) .nectar-box-roll > #page-header-bg.fullscreen-header,
				 .nectar_fullscreen_zoom_recent_projects,
				 .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), 
				 .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container,
				 #nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div {
					height: calc(100vh - '. (intval($mobile_logo_height) + 23) .'px);
				}
				.wpb_row.vc_row-o-full-height.top-level, .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 23) .'px); }';
			}
			
			echo '[data-bg-header="true"][data-permanent-transparent="1"] #page-header-wrap.fullscreen-header,
			[data-bg-header="true"][data-permanent-transparent="1"] #page-header-wrap.fullscreen-header #page-header-bg,
			html:not(.nectar-box-roll-loaded) [data-bg-header="true"][data-permanent-transparent="1"] .nectar-box-roll > #page-header-bg.fullscreen-header,
			[data-bg-header="true"][data-permanent-transparent="1"] .nectar_fullscreen_zoom_recent_projects,
			[data-permanent-transparent="1"] .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), [data-permanent-transparent="1"] .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container {
				height: 100vh;
			}
			
			[data-permanent-transparent="1"] .wpb_row.vc_row-o-full-height.top-level, [data-permanent-transparent="1"] .wpb_row.vc_row-o-full-height.top-level > .col.span_12 {	min-height: 100vh; }
			
			body[data-transparent-header="false"] #ajax-content-wrap.no-scroll { min-height:  calc(100vh - '. (intval($mobile_logo_height) + 23) .'px);	height: calc(100vh - '. (intval($mobile_logo_height) + 23) .'px); } 
			

		}';
		
	} 
	
	else {
		echo '@media only screen and (max-width: 1000px) {
			
			.using-mobile-browser #page-header-wrap.fullscreen-header,
			.using-mobile-browser #page-header-wrap.fullscreen-header #page-header-bg,
			.using-mobile-browser #nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div {
				height: calc(100vh - '. (intval($mobile_logo_height) + 88) .'px);
			}
			.using-mobile-browser .wpb_row.vc_row-o-full-height.top-level, .using-mobile-browser .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 88) .'px); }
			';
			
			if(is_admin_bar_showing()) { 
				echo '.admin-bar #page-header-wrap.fullscreen-header,
				.admin-bar #page-header-wrap.fullscreen-header #page-header-bg,
				html:not(.nectar-box-roll-loaded) .admin-bar .nectar-box-roll > #page-header-bg.fullscreen-header,
				.admin-bar .nectar_fullscreen_zoom_recent_projects,
				.admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), 
				.admin-bar .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container,
				.admin-bar #nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div {
					height: calc(100vh - '. (intval($mobile_logo_height) + 35) .'px - 46px);
				}
				.admin-bar .wpb_row.vc_row-o-full-height.top-level, .admin-bar .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 35) .'px - 46px); }';
			} else {
				
				echo '#page-header-wrap.fullscreen-header,
					#page-header-wrap.fullscreen-header #page-header-bg,
					html:not(.nectar-box-roll-loaded) .nectar-box-roll > #page-header-bg.fullscreen-header,
					.nectar_fullscreen_zoom_recent_projects,
					.nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), 
					.nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container,
					#nectar_fullscreen_rows:not(.afterLoaded):not([data-mobile-disable="on"]) > div {
						height: calc(100vh - '. (intval($mobile_logo_height) + 35) .'px);
					}
					.wpb_row.vc_row-o-full-height.top-level, .wpb_row.vc_row-o-full-height.top-level > .col.span_12 { min-height: calc(100vh - '. (intval($mobile_logo_height) + 35) .'px); }';
			}
			
			echo '[data-bg-header="true"][data-permanent-transparent="1"] #page-header-wrap.fullscreen-header,
			[data-bg-header="true"][data-permanent-transparent="1"] #page-header-wrap.fullscreen-header #page-header-bg,
			html:not(.nectar-box-roll-loaded) [data-bg-header="true"][data-permanent-transparent="1"] .nectar-box-roll > #page-header-bg.fullscreen-header,
			[data-bg-header="true"][data-permanent-transparent="1"] .nectar_fullscreen_zoom_recent_projects,
			[data-permanent-transparent="1"] .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded), [data-permanent-transparent="1"] .nectar-slider-wrap[data-fullscreen="true"]:not(.loaded) .swiper-container  {
				height: 100vh;
			}
			
			[data-permanent-transparent="1"] .wpb_row.vc_row-o-full-height.top-level, [data-permanent-transparent="1"] .wpb_row.vc_row-o-full-height.top-level > .col.span_12 {	min-height: 100vh; }

			body[data-transparent-header="false"] #ajax-content-wrap.no-scroll { min-height:  calc(100vh - '. (intval($mobile_logo_height) + 35) .'px);	height: calc(100vh - '. (intval($mobile_logo_height) + 35) .'px); } 

		}';
		
	}
	
	echo 'body[data-smooth-scrolling="1"] #full_width_portfolio .project-title.parallax-effect { top: '.$header_space.'px; }
	 
	 body.single-product div.product .product_title { padding-right:'.$wooProductTitlePadding.'px; } ';

	if($page_transition_bg != '#ffffff') echo '#ajax-loading-screen, #ajax-loading-screen[data-effect="center_mask_reveal"] span { background-color: '.$page_transition_bg.'} .default-loading-icon { border-color: rgba(255,255,255,0.2); } ';
	echo '#ajax-loading-screen .reveal-1 { background-color: '.$page_transition_bg.'; }';
	echo '#ajax-loading-screen .reveal-2 { background-color: '.$page_transition_bg_2.'; }';


	 /*perma trans fix*/
	 $perm_trans = (!empty($options['header-permanent-transparent'])) ? $options['header-permanent-transparent'] : 'false';
	 if($perm_trans == 1 && $theme_skin != 'material') {
	 	echo '#header-outer[data-permanent-transparent="1"] .midnightHeader header#top #logo img, #header-outer[data-permanent-transparent="1"] .midnightHeader header#top #social-in-menu, #header-outer[data-permanent-transparent="1"] .midnightHeader header#top #logo.no-image, #header-outer[data-permanent-transparent="1"] .midnightHeader header#top ul.sf-menu > li > a { margin-top: '.$header_padding.'px; }';
	 	echo '#header-outer[data-permanent-transparent="1"][data-full-width="false"] .midnightHeader header#top nav ul.buttons li, body:not(.ascend) #header-outer[data-permanent-transparent="1"][data-full-width="true"] .midnightHeader header#top nav ul.buttons li {  margin-top: '.$header_padding.'px; }';
	 	echo '#header-outer[data-permanent-transparent="1"][data-full-width="false"][data-has-menu="false"] header#top, body:not(.ascend) #header-outer[data-permanent-transparent="1"][data-full-width="true"][data-has-menu="false"] header#top { padding-bottom: '.$header_padding.'px; }';
	 	echo '#header-outer[data-permanent-transparent="1"][data-format="centered-menu-under-logo"] .midnightHeader #logo { height: ' . $logo_height .'px; }';
	 } 

	 /*mobile logo height*/
	 echo '
	 #header-outer #logo .mobile-only-logo, #header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"] .span_9 #logo img { 	height: '.$mobile_logo_height.'px; }
	 @media only screen and (max-width: 1000px) { 
	 	body header#top #logo img, #header-outer[data-permanent-transparent="false"] #logo .dark-version { 
	 		height: '.$mobile_logo_height.'px!important; 
	 	} 
	 	body:not(.material) header#top .col.span_9 {
	 		min-height: '. intval($mobile_logo_height+26) .'px; 
	 		line-height: '. intval($mobile_logo_height+4) .'px; 
	 	}
	 }';

	 /*header mobile breakpoint*/
	 $mobile_breakpoint = (!empty($options['header-menu-mobile-breakpoint'])) ? $options['header-menu-mobile-breakpoint'] : 1000; 
	 $has_main_menu = (has_nav_menu('top_nav')) ? 'true' : 'false';

	 if(!empty($mobile_breakpoint) && $mobile_breakpoint != 1000 && $headerFormat != 'left-header' && $has_main_menu == 'true') {

	 	$mobileMenuTopPadding = ceil(($logo_height/2)) - 10;

	 	$mobileMenuTopPaddingSmall = ceil( ($logo_height-$shrinkNum) / 2  ) - 10;

	 	$starting_color = (empty($options['header-starting-color'])) ? '#ffffff' : $options['header-starting-color'];
	 	$mobile_menu_hover = $options["accent-color"];

	 	if(!empty($options['header-color']) && $options['header-color'] == 'custom' && !empty($options['header-font-hover-color'])) {
	 		$mobile_menu_hover = $options['header-font-hover-color'];
	 	}

	 	echo '@media only screen and (min-width: 1001px) and (max-width: '.$mobile_breakpoint.'px) {
	 		#header-outer header#top .span_9  nav .sf-menu > li, header#top .span_9 nav ul.buttons li.menu-item { visibility: hidden!important; pointer-events: none!important; margin: 0!important; }
	 		#header-outer header#top .span_9 nav .sf-menu > li:not(:nth-child(1)), header#top .span_9 nav ul.buttons li.menu-item { position: absolute!important;}
	 		body #header-outer header#top nav .sf-menu > li#social-in-menu { position: relative!important; visibility: visible!important; pointer-events: auto!important;}
	 		html body:not(.ascend) header#top nav >ul.buttons, body:not(.ascend) #header-outer[data-full-width="true"] header#top nav ul #search-btn, body.ascend #header-outer[data-full-width="false"] header#top nav >ul.buttons { margin-left: 8px; }
			
			#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3 .slide-out-widget-area-toggle.mobile-icon { display: flex; display: -webkit-flex; margin-left: 13px; align-items: center; -webkit-align-items: center; }
			body[data-user-set-ocm="off"] #header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3 .slide-out-widget-area-toggle.mobile-icon { margin-left: 23px; }
			
			#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3 .slide-out-widget-area-toggle.mobile-icon > div { display: flex; display: -webkit-flex;
    display: -ms-flexbox;     -webkit-align-items: center;  -moz-align-items: center;  -ms-align-items: center;-ms-flex-align: center; align-items: center; height: 100%; }
			
			body.material #header-outer[data-using-secondary="1"][data-format="centered-menu-bottom-bar"][data-condense="true"]:not([data-format="left-header"]) {
				margin-top: 0;
			}
			
	 		html .ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"] .cart-menu-wrap { right: 0!important; }
	 		body:not(.ascend) #header-outer[data-cart="true"][data-full-width="true"]  .slide-out-widget-area-toggle.product_added { padding-right: 90px;  transition: padding 0.8s ease; }
	 		body:not(.ascend) #header-outer[data-cart="true"]  .slide-out-widget-area-toggle.product_added { padding-right: 28px;  transition: padding 0.8s ease; }

	 		body:not(.ascend) #header-outer[data-cart="false"] header#top nav ul #search-btn >div { border-left: none!important; }

	 		body #header-outer header#top nav > ul.buttons { padding-right: 0!important; }

	 		body #header-outer[data-full-width="false"] header#top nav ul #search-btn >div, body #header-outer[data-full-width="false"] header#top nav ul #nectar-user-account >div { border-left: none!important}

	 		.ascend #header-outer[data-cart="true"][data-full-width="true"]  .slide-out-widget-area-toggle { padding-right: 85px; }

	 		body.ascend #header-outer[data-full-width="false"] .cart-menu { border-left: none!important; }

	 		body.material #header-outer:not([data-format="left-header"]) header#top > .container .span_9 > .slide-out-widget-area-toggle.mobile-icon {
	 			top: 50%!important;
	 			padding-right: 0!important;
	 			transform: translateY(-50%)!important;
    			-webkit-transform: translateY(-50%)!important;
	 		}

	 		body.material #header-outer:not([data-format="left-header"]) header#top > .container .span_9 >  .slide-out-widget-area-toggle.mobile-icon.slide-out-from-right-hover {
	 			padding-right: 2px!important;
	 		}
	 		body.material[data-header-search="true"] #header-outer:not([data-format="centered-menu-bottom-bar"]) header#top nav > ul.buttons,
	 		body.material[data-cart="true"] #header-outer:not([data-format="centered-menu-bottom-bar"]) header#top nav > ul.buttons { margin-right: 36px; } 

	 		body.material #header-outer[data-format="centered-menu"] header#top .span_9 nav ul.sf-menu, 
	 		body.material #header-outer[data-format="centered-logo-between-menu"] header#top .span_9 nav ul.sf-menu, 
	 		body.material #header-outer[data-format="centered-menu-under-logo"] header#top .span_9 nav {
	 			-webkit-justify-content: flex-end;
			    -moz-justify-content: flex-end;
			    -ms-flex-pack: flex-end;
			    -ms-justify-content: flex-end;
			    justify-content: flex-end;
	 		}
	 		body[data-header-search="true"].material #header-outer[data-format="centered-logo-between-menu"][data-cart="true"] header#top nav .sf-menu > li#social-in-menu {
	 			margin-right: 145px!important;
	 		}
	 		body[data-header-search="false"].material #header-outer[data-format="centered-logo-between-menu"][data-cart="true"] header#top nav .sf-menu > li#social-in-menu,
	 		body[data-header-search="true"].material #header-outer[data-format="centered-logo-between-menu"][data-cart="false"] header#top nav .sf-menu > li#social-in-menu {
	 			margin-right: 96px!important;
	 		}
			body.material #header-outer[data-format="centered-logo-between-menu"] header#top #logo { transform: none; }
			

	 		#header-outer:not([data-format="centered-menu-bottom-bar"]) header#top nav > ul.buttons { margin-right: 52px; }
	 		body.original #header-outer[data-full-width="false"][data-cart="true"]  header#top nav > ul.buttons.product_added { margin-right: 90px; }

	 		header#top nav ul .slide-out-widget-area-toggle { display: none!important; }
			#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3 .right-side ul .slide-out-widget-area-toggle.mobile-icon { display: block!important; }
	 	    #header-outer:not([data-format="centered-menu-bottom-bar"]) header#top .span_9 > .slide-out-widget-area-toggle, #slide-out-widget-area .mobile-only { display: block!important; padding-top: '. $mobileMenuTopPadding .'px; transition: padding 0.2s ease; }
	 	    body.material header#top .span_9 > .slide-out-widget-area-toggle, body.material #slide-out-widget-area .mobile-only  { padding-top: 0!important; }
	 	    .small-nav  header#top .span_9 > .slide-out-widget-area-toggle, .small-nav #slide-out-widget-area .mobile-only { display: block!important; padding-top: '. $mobileMenuTopPaddingSmall .'px; }

	 		body header#top .span_9 >.slide-out-widget-area-toggle { position: absolute; top: 0; float: right; transform: none!important; -webkit-transform: none!important; margin: 0!important; }
	 		body .midnightHeader header#top .span_9 >.slide-out-widget-area-toggle { top: 50%; float: none; transform: translateY(-50%)!important; -webkit-transform: translateY(-50%)!important; }
	 		body #header-outer .midnightHeader header#top nav .sf-menu > li#social-in-menu { visibility: hidden!important; pointer-events: none!important; }

	 		#header-outer[data-has-menu="true"] header#top .span_3, body #header-outer[data-format="centered-menu-under-logo"] .span_3 {
			    text-align: left!important;
			    left: 0!important;
			    width: auto!important;
			    float: left!important;
			}
			body #header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"] header#top .span_3 {
				text-align: center!important;
			}
			body #header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"][data-condense="true"] { position: fixed!important; }
			body.admin-bar #header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"][data-condense="true"] { top: 32px!important; }
			#header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"][data-condense="true"].fixed-menu header#top nav >ul.buttons { transform: none!important; }
			
			body #header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"] .span_9 { display: none!important; }
			
			body.material #header-outer[data-format="centered-menu-under-logo"] header#top .span_9 {
			    margin-left: auto;
			}
			body.material #header-outer[data-format="centered-menu-under-logo"] header#top .span_9 ul li#social-in-menu a,
			html body.material #header-outer[data-format="centered-menu-under-logo"] header#top .span_9 nav >ul.buttons { margin-bottom: 0!important; padding-bottom: 0;}
			body.material #header-outer[data-format="centered-menu-under-logo"] .span_3 { 
				    display: -webkit-flex!important;
				    display: -ms-flexbox!important;
				    display: flex!important;
			}
			body.material #header-outer[data-format="centered-menu-under-logo"] .row {
				-webkit-flex-direction: row; 
			    -ms-flex-direction: row; 
			    -moz-flex-direction: row; 
			    flex-direction: row; 
			}
			body.material #header-outer[data-format="centered-menu-under-logo"] header#top #logo .starting-logo { left: 0!important; }

			#header-outer[data-format="centered-menu-under-logo"] .span_9 { float: right!important; width: auto!important;}
			body #header-outer[data-format="centered-menu-under-logo"] header#top #logo img { margin: 0}
			#header-outer[data-format="centered-menu-under-logo"] header#top #logo { text-align: left;}

			#header-outer[data-has-menu="true"]:not([data-format="centered-menu-bottom-bar"]) header#top .span_3 #logo img { transform: none!important; }
	 		#mobile-menu a > .sf-sub-indicator { right: 0px!important; position: absolute; padding: 16px; left: auto!important; top: 0px!important; height: auto; width: auto; }
	 		#mobile-menu { position: absolute; width: 100%; left: 0; top: '.$header_space.'px;}

			.cart-outer, body #header-outer[data-full-width="false"] .cart-outer {
			    display: block;
			}

			header#top nav > ul.buttons {
			    padding-right: 20px!important;
			}
	 		body:not(.material) #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons {
			    padding-right: 80px!important;
			}

			#header-outer[data-format="centered-logo-between-menu"] header#top nav .sf-menu > li { float: left!important; } 
			#header-outer[data-format="centered-logo-between-menu"] header#top nav .sf-menu > li#social-in-menu { float: right!important; margin-right: 42px!important; } 

			body:not(ascend)[data-header-search="false"] #header-outer[data-format="centered-logo-between-menu"][data-cart="true"][data-full-width="true"] header#top nav .sf-menu.product_added > li#social-in-menu { margin-right: 122px!important; }

			body.ascend[data-header-search="false"] #header-outer[data-format="centered-logo-between-menu"][data-cart="true"][data-full-width="true"] header#top nav .sf-menu > li#social-in-menu { margin-right: 122px!important; } 

			body.ascend[data-header-search="true"] #header-outer[data-format="centered-logo-between-menu"] header#top nav .sf-menu > li#social-in-menu { margin-right: 92px!important; } 

			body:not(.ascend)[data-header-search="true"] #header-outer[data-format="centered-logo-between-menu"] header#top nav .sf-menu > li#social-in-menu { margin-right: 112px!important; } 
			body[data-header-search="true"].ascend #header-outer[data-format="centered-logo-between-menu"][data-cart="true"][data-full-width="true"] header#top nav .sf-menu > li#social-in-menu { margin-right: 195px!important; } 

			body[data-header-search="true"].original #header-outer[data-format="centered-logo-between-menu"][data-cart="true"][data-full-width="true"] header#top nav .sf-menu.product_added > li#social-in-menu { margin-right: 195px!important; } 
			body[data-header-search="true"].original #header-outer[data-format="centered-logo-between-menu"][data-cart="true"][data-full-width="false"] header#top nav .sf-menu.product_added > li#social-in-menu { margin-right: 145px!important; } 

			.ascend[data-header-search="false"] #header-outer[data-cart="true"][data-full-width="true"] header#top nav >ul >li#social-in-menu { margin-right: 15px!important; }

			body[data-full-width-header="false"] #slide-out-widget-area.slide-out-from-right-hover .slide_out_area_close { display: none; }

			body[data-slide-out-widget-area-style="slide-out-from-right-hover"][data-slide-out-widget-area="true"][data-user-set-ocm="off"] #header-outer[data-full-width="false"][data-cart="false"] header > .container {
			    max-width: 100%!important;
			    padding: 0 28px !important;
			}

			#mobile-menu[data-mobile-fixed="false"] { z-index: 1000; }
			
			body:not(.ascend) #header-outer[data-cart="true"][data-full-width="true"].style-slide-out-from-right.side-widget-open .slide-out-widget-area-toggle.product_added {
				padding-right: 0!important;
			}

			body[data-full-width-header="false"][data-cart="true"] .slide-out-hover-icon-effect.small {
				right: 28px!important;
			}

			body[data-full-width-header="false"][data-cart="true"] .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .lines-button.x2.no-delay .lines:before, 
			body[data-full-width-header="false"][data-cart="true"] .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .lines-button.x2.no-delay .lines:after,
			body[data-full-width-header="false"][data-cart="true"] .slide-out-hover-icon-effect.slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .no-delay.lines-button.no-delay:after {
				-webkit-transition: none!important;
				transition: none!important;
			}

			/*coloring*/
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle i.lines-button:after, 
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle i.lines:before,
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle i.lines:after {
				background-color: '.$starting_color.'!important;
				opacity: 0.75;
			}
			body:not(.mobile) #header-outer.transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle i.lines-button:after, 
			body:not(.mobile) #header-outer.transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle i.lines:before,
			body:not(.mobile) #header-outer.transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle i.lines:after {
				background-color: #000!important;
				opacity: 0.75;
			}
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle:hover i.lines-button:after, 
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle:hover i.lines:before,
			body:not(.mobile) #header-outer.transparent > header#top .span_9 > .slide-out-widget-area-toggle:hover i.lines:after { 
				opacity: 1;
			}

			body header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon a:hover i.lines:after, 
			body header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon a:hover .lines-button:after, 
			body header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon a:hover i.lines:before {
				background-color: '.$mobile_menu_hover.'!important;
			}

			body:not(.mobile) #header-outer.light-text > header#top .span_9 > .slide-out-widget-area-toggle i.lines-button:after, 
			body:not(.mobile) #header-outer.light-text > header#top .span_9 > .slide-out-widget-area-toggle i.lines:before,
			body:not(.mobile) #header-outer.light-text > header#top .span_9 > .slide-out-widget-area-toggle i.lines:after {
				background-color: #fff!important;
			}
	 	}';
	 }

	 /*material boxed desktop ocm*/
	 if(!empty($options['boxed_layout']) && $options['boxed_layout'] == '1')  {
	 	$boxed_max_width = 1200;

	 	if(!empty($options['responsive']) && $options['responsive'] == 1 && !empty($options['ext_responsive']) && $options['ext_responsive'] == '1') {
	 		$boxed_max_width = 1400;

	 		if ( !empty($options['max_container_width']) ) {
				$boxed_max_width = $options['max_container_width'];
		 	}
	 	} 
	 	
	 	echo '@media only screen and (min-width: '.$boxed_max_width.'px) {
	 		.material .ocm-effect-wrap.material-ocm-open #boxed { 
	 			margin-left: auto;
	 			margin-right: 80px;
	 		} 
	 	}';
	 }

	 /*custom header bg opacity for light/dark*/
	 if(!empty($options['header-bg-opacity']) && !empty($options['header-color'])) {
	 	if($options['header-color'] == 'light' || $options['header-color'] == 'dark') {

	 		 if($headerFormat != 'left-header') {
	 		 	
				 $navBGColor = ($options['header-color'] == 'light') ? 'ffffff' : '000000';
				 $colorR = hexdec( substr( $navBGColor, 0, 2 ) );
				 $colorG = hexdec( substr( $navBGColor, 2, 2 ) );
				 $colorB = hexdec( substr( $navBGColor, 4, 2 ) );
				 $colorA = ($options['header-bg-opacity'] != '100') ? '0.'.$options['header-bg-opacity'] : $options['header-bg-opacity'];

				 echo 'body #header-outer, body[data-header-color="dark"] #header-outer { background-color: rgba('.$colorR.','.$colorG.','.$colorB.','.$colorA.'); }';

				 //material search
				 echo '.material #header-outer:not(.transparent) .bg-color-stripe { display: none; }';
			}
		}
	}

	if(!empty($options['header-dropdown-opacity']) &&  $options['header-dropdown-opacity'] != '100' && !empty($options['header-dropdown-style']) && !empty($options['header-color'])) {

		if($options['header-color'] == 'light' || $options['header-color'] == 'dark') {

			 $dropdownBGColor = '1c1c1c';

	 		 if($options['header-dropdown-style'] == 'minimal' && $options['header-color'] == 'light' || $options['header-dropdown-style'] == 'minimal' && $options['header-color'] == 'dark') $dropdownBGColor = 'ffffff';

			 $colorR = hexdec( substr( $dropdownBGColor, 0, 2 ) );
			 $colorG = hexdec( substr( $dropdownBGColor, 2, 2 ) );
			 $colorB = hexdec( substr( $dropdownBGColor, 4, 2 ) );
			 $colorA = ($options['header-dropdown-opacity'] != '100') ? '0.'.$options['header-dropdown-opacity'] : $options['header-dropdown-opacity'];

			 echo '#search-outer .ui-widget-content, header#top .sf-menu li ul li a, body[data-dropdown-style="minimal"]:not([data-header-format="left-header"]) header#top .sf-menu li ul, header#top nav > ul > li.megamenu > ul.sub-menu, body header#top nav > ul > li.megamenu > ul.sub-menu > li > a, #header-outer .widget_shopping_cart .cart_list a, #header-secondary-outer ul ul li a, #header-outer .widget_shopping_cart .cart_list li, .woocommerce .cart-notification, #header-outer .widget_shopping_cart_content { background-color: rgba('.$colorR.','.$colorG.','.$colorB.','.$colorA.')!important; }';	
		}
	}

	echo $custom_loading_icon; // WPCS: XSS ok.
	 
	 
	 //nectar slider font calcs
	 $heading_size = (!empty($options['use-custom-fonts']) && $options['use-custom-fonts'] == 1 && $options['nectar_slider_heading_font_size'] != '-') ? intval($options['nectar_slider_heading_font_size']) : 60;
	 $caption_size = (!empty($options['use-custom-fonts']) && $options['use-custom-fonts'] == 1 && $options['home_slider_caption_font_size'] != '-') ? intval($options['home_slider_caption_font_size']) : 24;
	 
	 echo '@media only screen and (min-width: 1000px) and (max-width: 1300px) {
	    .nectar-slider-wrap[data-full-width="true"] .swiper-slide .content h2, 
	    .nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content h2,
	    .full-width-content .vc_span12 .swiper-slide .content h2 {
			font-size: ' .$heading_size*0.75 . 'px!important;
			line-height: '.$heading_size*0.85 .'px!important;
		}

		.nectar-slider-wrap[data-full-width="true"] .swiper-slide .content p, 
		.nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content p, 
	    .full-width-content .vc_span12 .swiper-slide .content p {
			font-size: ' .$caption_size *0.75 . 'px!important;
			line-height: '.$caption_size *1.3 .'px!important;
		}
	}
	
	@media only screen and (min-width : 690px) and (max-width : 1000px) {
		.nectar-slider-wrap[data-full-width="true"] .swiper-slide .content h2, 
		.nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content h2,
	    .full-width-content .vc_span12 .swiper-slide .content h2 {
			font-size: ' . (($heading_size*0.55 > 20) ? $heading_size*0.55 : 20) . 'px!important;
			line-height: '. (($heading_size*0.55 > 20) ? $heading_size*0.65 : 27) .'px!important;
		}

		.nectar-slider-wrap[data-full-width="true"] .swiper-slide .content p, 
		.nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content p, 
	    .full-width-content .vc_span12 .swiper-slide .content p {
			font-size: ' . (($caption_size *0.55 > 12) ? $caption_size *0.55 : 12). 'px!important;
			line-height: '.$caption_size *1 .'px!important;
		}
	}
	
	@media only screen and (max-width : 690px) {
		.nectar-slider-wrap[data-full-width="true"][data-fullscreen="false"] .swiper-slide .content h2, 
		.nectar-slider-wrap[data-full-width="boxed-full-width"][data-fullscreen="false"] .swiper-slide .content h2,
	    .full-width-content .vc_span12 .nectar-slider-wrap[data-fullscreen="false"] .swiper-slide .content h2 {
			font-size: ' .(($heading_size*0.25 > 14) ? $heading_size*0.25 : 14) . 'px!important;
			line-height: '.(($heading_size*0.25 > 14) ? $heading_size*0.35 : 20) .'px!important;
		}

		.nectar-slider-wrap[data-full-width="true"][data-fullscreen="false"] .swiper-slide .content p, 
		.nectar-slider-wrap[data-full-width="boxed-full-width"][data-fullscreen="false"]  .swiper-slide .content p, 
	    .full-width-content .vc_span12 .nectar-slider-wrap[data-fullscreen="false"] .swiper-slide .content p {
			font-size: ' .(($caption_size *0.32 > 10) ? $caption_size *0.32 : 10) . 'px!important;
			line-height: '.(($caption_size *0.73 > 10) ? $caption_size *0.73 : 18) .'px!important;
		}
	}
	';
	 
	$removeHeaderSearch = (!empty($options['header-disable-search']) && $options['header-disable-search'] == '1') ? 'true' : 'false';
	if($removeHeaderSearch == 'true') {
		echo '#mobile-menu #mobile-search, header#top nav ul #search-btn {
			   display: none!important;
			}';
	}

	global $post;

	if($external_dynamic != 'on') {

		//hide scrollbar during loading if using fullpage option
		$page_full_screen_rows = (isset($post->ID)) ? get_post_meta($post->ID, '_nectar_full_screen_rows', true) : '';
		if($page_full_screen_rows == 'on') {

			echo 'body,html  { overflow: hidden; height: 100%;}';
		}
		//body border
		$body_border = (!empty($options['body-border'])) ? $options['body-border'] : 'off';
		$body_border_size = (!empty($options['body-border-size'])) ? $options['body-border-size'] : '20';
		$body_border_color = (!empty($options['body-border-color'])) ? $options['body-border-color'] : '#ffffff';
		if($body_border == '1') {
			
			$headerColorScheme = (!empty($options['header-color'])) ? $options['header-color'] : 'light';
			$userSetBG = (!empty($options['header-background-color']) && $headerColorScheme == 'custom') ? $options['header-background-color'] : '#ffffff';
			$activate_transparency = using_page_header($post->ID);

			if(empty($options['transparent-header'])) {
				$activate_transparency = 'false';
			}

			echo '@media only screen and (min-width: 1001px) { 
				
				.page-submenu > .full-width-section,
				.page-submenu .full-width-content,
				.full-width-content.blog-fullwidth-wrap,
				.wpb_row.full-width-content, 
				body .full-width-section .row-bg-wrap,
				body .full-width-section > .nectar-shape-divider-wrap,
				body .full-width-section > .video-color-overlay,
				body[data-aie="zoom-out"] .first-section .row-bg-wrap, 
				body[data-aie="long-zoom-out"] .first-section .row-bg-wrap,
				body[data-aie="zoom-out"] .top-level.full-width-section .row-bg-wrap, 
				body[data-aie="long-zoom-out"] .top-level.full-width-section .row-bg-wrap,
				body .full-width-section.parallax_section .row-bg-wrap {
					margin-left: calc(-50vw + '. intval($body_border_size*2) .'px);
			    left: calc(50% - '.$body_border_size.'px);
			    width: calc(100vw - '. intval($body_border_size)*2 .'px);
				}';
				
				if($headerFormat == 'left-header') {
					echo '[data-header-format="left-header"] .full-width-content.blog-fullwidth-wrap,
				  [data-header-format="left-header"] .wpb_row.full-width-content, 
				  [data-header-format="left-header"] .page-submenu > .full-width-section,
				  [data-header-format="left-header"] .page-submenu .full-width-content,
				  [data-header-format="left-header"] .full-width-section .row-bg-wrap,
				  [data-header-format="left-header"] .full-width-section > .nectar-shape-divider-wrap,
				  [data-header-format="left-header"] .full-width-section > .video-color-overlay,
				  [data-header-format="left-header"][data-aie="zoom-out"] .first-section .row-bg-wrap, 
				  [data-header-format="left-header"][data-aie="long-zoom-out"] .first-section .row-bg-wrap,
				  [data-header-format="left-header"][data-aie="zoom-out"] .top-level.full-width-section .row-bg-wrap, 
				  [data-header-format="left-header"][data-aie="long-zoom-out"] .top-level.full-width-section .row-bg-wrap,
				  [data-header-format="left-header"] .full-width-section.parallax_section .row-bg-wrap,
				  [data-header-format="left-header"] .nectar-slider-wrap[data-full-width="true"] {
				    margin-left: -'. (61 + intval($body_border_size)) .'px;
				    width: calc(100% + '. (122 + intval($body_border_size)) .'px);
				    left: 0;
				  }
				  [data-header-format="left-header"] .full-width-section > .nectar-video-wrap {
						margin-left: -'. (61 + intval($body_border_size)) .'px;
				    width: calc(100% + '. (122 + intval($body_border_size)) .'px)!important;
				    left: 0;
				  }';
			}
				
			echo 'body {padding-bottom: '.$body_border_size.'px; }
			.container-wrap { padding-right: '.$body_border_size.'px; padding-left: '.$body_border_size.'px; padding-bottom: '.$body_border_size.'px;} 
			 .midnightInner, #footer-outer[data-full-width="1"] { padding-right: '.$body_border_size.'px; padding-left: '.$body_border_size.'px; }
			 body[data-footer-reveal="1"] #footer-outer { bottom: '.$body_border_size.'px; }
			 #slide-out-widget-area.fullscreen .bottom-text[data-has-desktop-social="false"], #slide-out-widget-area.fullscreen-alt .bottom-text[data-has-desktop-social="false"] {bottom: '. intVal($body_border_size + 28) .'px;}
			#header-outer, body #header-outer-bg-only  {box-shadow: none; -webkit-box-shadow: none;} 
			 .slide-out-hover-icon-effect.small, .slide-out-hover-icon-effect:not(.small) {margin-top: '.$body_border_size.'px; margin-right: '.$body_border_size.'px;}
			 #slide-out-widget-area-bg.fullscreen-alt { padding: '.$body_border_size.'px;  }
			 #slide-out-widget-area.slide-out-from-right-hover {margin-right: '.$body_border_size.'px;}
			 .orbit-wrapper div.slider-nav span.left, .swiper-container .slider-prev { margin-left: '.$body_border_size.'px;} .orbit-wrapper div.slider-nav span.right, .swiper-container .slider-next { margin-right: '.$body_border_size.'px;}
			 .admin-bar #slide-out-widget-area-bg.fullscreen-alt { padding-top: '. intval($body_border_size+32) .'px;  }
			 #header-outer, body.ascend #search-outer, #header-secondary-outer, body:not(.material) #slide-out-widget-area.slide-out-from-right, #slide-out-widget-area.fullscreen .bottom-text { margin-top: '.$body_border_size.'px; padding-right: '.$body_border_size.'px; padding-left: '.$body_border_size.'px; }
			 #nectar_fullscreen_rows, body:not(.material) #slide-out-widget-area-bg:not(.fullscreen-alt) { margin-top: '.$body_border_size.'px; }
			body:not(.ascend):not(.material) .cart-menu-wrap .cart-menu , #slide-out-widget-area.fullscreen .off-canvas-social-links { padding-right: '.$body_border_size.'px!important; }
			.section-down-arrow, #slide-out-widget-area.fullscreen .off-canvas-social-links, #slide-out-widget-area.fullscreen .bottom-text { padding-bottom: '.$body_border_size.'px; } 
			.ascend #search-outer #search #close, body[data-smooth-scrolling="0"]:not(.material) #header-outer .widget_shopping_cart, #page-header-bg  .pagination-navigation { margin-right:  '.$body_border_size.'px; }
			#to-top { right: '. intval($body_border_size+17) .'px; margin-bottom: '.$body_border_size.'px; }
			body[data-dropdown-style="minimal"][data-header-color="light"] #header-outer:not(.transparent) .sf-menu > li > ul { border-top: none; }
			body:not(.ascend) #header-outer .cart-menu { background-color: '.$body_border_color.'; border-left: 1px solid rgba(0,0,0,0.1); }
			.nectar-social-sharing-fixed { margin-bottom: '.$body_border_size.'px; margin-right: '.$body_border_size.'px; }
			.page-submenu.stuck { padding-left: '.$body_border_size.'px; padding-right: '.$body_border_size.'px; }
			#fp-nav { padding-right: '.$body_border_size.'px; } .body-border-left {background-color: '.$body_border_color.'; width: '.$body_border_size.'px;} .body-border-right {background-color: '.$body_border_color.'; width: '.$body_border_size.'px;} .body-border-bottom { background-color: '.$body_border_color.'; height: '.$body_border_size.'px;} 
			.body-border-top {background-color: '.$body_border_color.'; height: '.$body_border_size.'px;} 
		} @media only screen and (max-width: 1000px) { .body-border-right, .body-border-left, .body-border-top, .body-border-bottom { display: none; } }';
			
			if(($body_border_color == '#ffffff' && $headerColorScheme == 'light' || $headerColorScheme == 'custom' && $body_border_color == $userSetBG ) && $activate_transparency != 'true' ) {
				echo '#header-outer:not([data-using-secondary="1"]):not(.transparent),  body.ascend #search-outer, body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer:not([data-using-secondary="1"]) { margin-top: 0!important; } .body-border-top { z-index: 9997; } body:not(.material) #slide-out-widget-area.slide-out-from-right { z-index: 9997;} 
				#nectar_fullscreen_rows, body #slide-out-widget-area-bg { margin-top: 0px!important; }
				body #header-outer, body[data-slide-out-widget-area-style="slide-out-from-right-hover"] #header-outer { z-index: 9998; }
				
				@media only screen and (min-width: 1001px) {
					body[data-user-set-ocm="off"].material #header-outer[data-full-width="true"], body[data-user-set-ocm="off"].ascend #header-outer { z-index: 10010; }
				}	
				
				@media only screen and (min-width: 1001px) { 
					body #slide-out-widget-area.slide-out-from-right-hover { z-index: 9996; }
					#header-outer[data-full-width="true"]:not([data-transparent-header="true"]) header > .container, #header-outer[data-full-width="true"][data-transparent-header="true"].pseudo-data-transparent header > .container { padding-left: 0; padding-right: 0; }
				}
				
				@media only screen and (max-width: 1080px) and (min-width: 1001px) {
					.ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"]:not([data-transparent-header="true"]) header > .container { padding-left: 0!important; padding-right: 0!important; }
				}
				
				body[data-header-search="false"][data-slide-out-widget-area="false"].ascend #header-outer[data-full-width="true"][data-cart="true"]:not([data-transparent-header="true"]) header > .container { padding-right: 28px; }

				body:not(.ascend):not(.material) #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons { padding-right: '.intval($body_border_size+80) .'px!important; }

				body.ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"] .cart-menu-wrap { right: '.intval($body_border_size+51) .'px!important; }

				body[data-slide-out-widget-area-style="slide-out-from-right"] #header-outer[data-header-resize="0"] {
					-ms-transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important;
					-webkit-transition: -webkit-transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important;
					transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important;
				}

				@media only screen and (min-width: 1001px) { 
					body div.portfolio-items[data-gutter*="px"][data-col-num="elastic"] { padding: 0!important; }
				}

				body #header-outer[data-transparent-header="true"].transparent {  transition: none; -webkit-transition: none; }
				body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer { transition:  background-color 0.3s cubic-bezier(0.215,0.61,0.355,1); -webkit-transition:  background-color 0.3s cubic-bezier(0.215,0.61,0.355,1); }
				@media only screen and (min-width: 1001px) { 
					body.ascend[data-slide-out-widget-area="false"] #header-outer[data-header-resize="0"][data-cart="true"]:not(.transparent) { z-index: 100000; }
				} ';

			} else if($body_border_color == '#ffffff' && $headerColorScheme == 'light' || $headerColorScheme == 'custom' && $body_border_color == $userSetBG) {
			
				echo '
				@media only screen and (min-width: 1001px) { 
				#header-outer.small-nav:not(.transparent), #header-outer[data-header-resize="0"]:not([data-using-secondary="1"]).scrolled-down:not(.transparent), 
				#header-outer[data-header-resize="0"]:not([data-using-secondary="1"]).fixed-menu:not(.transparent), #header-outer.detached,  body.ascend #search-outer.small-nav, body[data-slide-out-widget-area-style="slide-out-from-right-hover"] #header-outer:not([data-using-secondary="1"]):not(.transparent), body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer:not([data-using-secondary="1"]).scrolled-down, body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer:not([data-using-secondary="1"]).transparent.side-widget-open { margin-top: 0px; z-index: 100000; }
				body[data-hhun="1"] #header-outer.detached { z-index: 100000!important; }
				body.ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"].transparent:not(.small-nav) .cart-menu-wrap,
				body.ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"].scrolled-down .cart-menu-wrap { right: '.intval($body_border_size+80) .'px!important; }
				body.ascend[data-slide-out-widget-area="true"] #header-outer[data-full-width="true"] .cart-menu-wrap,
				body.ascend[data-slide-out-widget-area="false"] #header-outer[data-full-width="true"][data-cart="true"] .cart-menu-wrap { transition: right 0.3s cubic-bezier(0.215, 0.61, 0.355, 1); -webkit-transition: all 0.3s cubic-bezier(0.215, 0.61, 0.355, 1); }
				.ascend #header-outer.transparent .cart-menu-wrap {width: 130px;}
				body:not(.ascend):not(.material) #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons { padding-right: '.intval($body_border_size+80) .'px!important; }
				#header-outer[data-full-width="true"][data-transparent-header="true"][data-header-resize="0"].scrolled-down:not(.transparent) .container,
				body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer[data-full-width="true"].scrolled-down .container,
				body[data-slide-out-widget-area-style="fullscreen-alt"] #header-outer[data-full-width="true"].transparent.side-widget-open .container { padding-left: 0!important; padding-right: 0!important; }
				
				@media only screen and (min-width: 1001px) { 
					.material #header-outer[data-full-width="true"][data-transparent-header="true"][data-header-resize="0"].scrolled-down:not(.transparent) #search-outer .container {
						padding: 0 90px!important;
					}
				}
				
				body[data-header-search="false"][data-slide-out-widget-area="false"].ascend #header-outer[data-full-width="true"][data-cart="true"]:not(.transparent) header > .container { padding-right: 28px!important; }
				body.ascend[data-slide-out-widget-area="false"] #header-outer[data-full-width="true"][data-cart="true"].transparent .cart-menu-wrap { right: '.intval($body_border_size) .'px!important; }

				body.ascend[data-slide-out-widget-area="true"]:not([data-slide-out-widget-area-style="fullscreen"]):not([data-slide-out-widget-area-style="slide-out-from-right"]) #header-outer[data-full-width="true"][data-header-resize="0"].scrolled-down .cart-menu-wrap,
				body.ascend[data-slide-out-widget-area="true"][data-slide-out-widget-area-style="fullscreen"] #header-outer[data-full-width="true"][data-header-resize="0"].scrolled-down:not(.transparent) .cart-menu-wrap,
				body.ascend[data-slide-out-widget-area="true"][data-slide-out-widget-area-style="slide-out-from-right"] #header-outer[data-full-width="true"][data-header-resize="0"].scrolled-down:not(.transparent) .cart-menu-wrap,
				body[data-slide-out-widget-area-style="fullscreen-alt"].ascend #header-outer[data-full-width="true"].transparent.side-widget-open .cart-menu-wrap { right: '.intval($body_border_size+50) .'px!important; }
				}

				@media only screen and (min-width: 1001px) { 
					body div.portfolio-items[data-gutter*="px"][data-col-num="elastic"] { padding: 0!important; }
				}
				#header-outer[data-full-width="true"][data-header-resize="0"].transparent { -ms-transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1),  background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1),  background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; -webkit-transition: -webkit-transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1),  background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; }
				body #header-outer[data-transparent-header="true"][data-header-resize="0"] { -ms-transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; -webkit-transition: -webkit-transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1), background-color 0.3s cubic-bezier(0.215,0.61,0.355,1), box-shadow 0.40s ease, margin 0.3s cubic-bezier(0.215,0.61,0.355,1)!important; }
				#header-outer[data-full-width="true"][data-header-resize="0"] header > .container { -ms-transition: padding 0.35s cubic-bezier(0.215,0.61,0.355,1); transition: padding 0.35s cubic-bezier(0.215,0.61,0.355,1); -webkit-transition: padding 0.35s cubic-bezier(0.215,0.61,0.355,1); }
				';

				$trans_header = (!empty($options['transparent-header']) && $options['transparent-header'] == '1') ? $options['transparent-header'] : 'false';
				$bg_header = (!empty($post->ID) && $post->ID != 0) ? using_page_header($post->ID) : 0;
				$perm_trans = (!empty($options['header-permanent-transparent']) && $trans_header != 'false' && $bg_header == 'true') ? $options['header-permanent-transparent'] : 'false'; 
				
				if($perm_trans != '1') {
					echo '@media only screen and (max-width: 1000px) and (min-width: 1000px) { 
					#header-outer,#nectar_fullscreen_rows, body #slide-out-widget-area-bg { margin-top: 0!important; } 
					}';
				}

			} else if ($body_border_color != '#ffffff' && $headerColorScheme == 'light' ||  $headerColorScheme == 'custom' && $body_border_color != $userSetBG ) {
				echo '@media only screen and (min-width: 1001px) { #header-space { margin-top: '.$body_border_size.'px; } }';
				echo 'html body.ascend[data-user-set-ocm="off"] #header-outer[data-full-width="true"] .cart-outer[data-user-set-ocm="off"] .cart-menu-wrap { right: '.intval($body_border_size) .'px!important; }
				html body.ascend[data-user-set-ocm="1"] #header-outer[data-full-width="true"] .cart-outer[data-user-set-ocm="1"] .cart-menu-wrap { right: '.intval($body_border_size+77) .'px!important; }';
			}

		} //body border

		 //page header
		global $woocommerce;
		if( $woocommerce && version_compare( $woocommerce->version, "3.0", ">=" ) ) {

			if(is_shop() || is_product_category() || is_product_tag()) {
				$font_color = get_post_meta(wc_get_page_id('shop'), '_nectar_header_font_color', true);
			} else {
				$font_color = get_post_meta($post->ID, '_nectar_header_font_color', true);
			}

		} 
		
		else {
			$font_color = get_post_meta($post->ID, '_nectar_header_font_color', true);
		}
		
		
		 $header_auto_title = (!empty($options['header-auto-title']) && $options['header-auto-title'] == '1') ? true : false;
		 $page_header_title = get_post_meta($post->ID, '_nectar_header_title', true);
		 if($header_auto_title && is_page() && empty($page_header_title)) {
			 if(empty($font_color)) { $font_color = (!empty($options['overall-font-color'])) ? $options['overall-font-color'] : '#333333'; }
		 }
	 
		 if(!empty($font_color) && !is_search()) {
			 echo '#page-header-bg h1, #page-header-bg .subheader, .nectar-box-roll .overlaid-content h1, .nectar-box-roll .overlaid-content .subheader, #page-header-bg #portfolio-nav a i, body .section-title #portfolio-nav a:hover i, .page-header-no-bg h1, .page-header-no-bg span, #page-header-bg #portfolio-nav a i, #page-header-bg span { color: '. $font_color .'!important; } ';
			 echo 'body #page-header-bg a.pinterest-share i, body #page-header-bg a.facebook-share i, body #page-header-bg a.linkedin-share i, body #page-header-bg .twitter-share i, body #page-header-bg .google-plus-share i, 
		 	 body #page-header-bg .icon-salient-heart, body #page-header-bg .icon-salient-heart-2 { color: '. $font_color .'; }';
		 	 echo 'body .section-title #portfolio-nav a:hover i { opacity: 0.75;}';

		 	 $font_color_no_hash =  substr($font_color,1);
		 	 $colorR = hexdec( substr( $font_color_no_hash, 0, 2 ) );
			 $colorG = hexdec( substr( $font_color_no_hash, 2, 2 ) );
			 $colorB = hexdec( substr( $font_color_no_hash, 4, 2 ) );
		 	 echo '.single #page-header-bg .blog-title #single-meta ul li > a, .single #page-header-bg .blog-title #single-meta ul .n-shortcode a { border-color: rgba('.$colorR.','.$colorG.','.$colorB.',0.4)!important; }';
		 	 echo '.single #page-header-bg .blog-title #single-meta ul li > a:hover, .single #page-header-bg .blog-title #single-meta ul .n-shortcode a:hover, .single #page-header-bg .blog-title #single-meta ul li:not(.meta-share-count):hover > a{ border-color: rgba('.$colorR.','.$colorG.','.$colorB.',1)!important; }';
		 	 echo '.single #page-header-bg #single-meta li span, .single #page-header-bg #single-meta li.meta-comment-count a, .single #page-header-bg #single-meta ul li i {  color: '. $font_color .'!important; }';
		 	 echo '.single #page-header-bg #single-meta ul li.meta-share-count .nectar-social a i { color: rgba('.$colorR.','.$colorG.','.$colorB.',0.7)!important; }';
		 	 echo '.single #page-header-bg #single-meta ul li.meta-share-count .nectar-social a:hover i { color: rgba('.$colorR.','.$colorG.','.$colorB.',1)!important; }';
		 }
		 	
	 } //external dynamic
	
		 
	 // header transparent option
	if(!empty($options['transparent-header']) && $options['transparent-header'] == '1') {
		
		$starting_color = (empty($options['header-starting-color'])) ? '#ffffff' : $options['header-starting-color'];

		echo '
				#header-outer.transparent header#top #logo, #header-outer.transparent header#top #logo:hover {
				 	color: '.$starting_color.'!important;
				 }

				 #header-outer.transparent header#top nav > ul > li > a, 
				 #header-outer.transparent header#top nav ul #search-btn a span.icon-salient-search, 
				 #header-outer.transparent header#top nav ul #nectar-user-account a span, 
				 #header-outer.transparent nav > ul > li > a > .sf-sub-indicator [class^="icon-"], 
				 #header-outer.transparent nav > ul > li > a > .sf-sub-indicator [class*=" icon-"],
				 #header-outer.transparent .cart-menu .cart-icon-wrap .icon-salient-cart,
				 .ascend #boxed #header-outer.transparent .cart-menu .cart-icon-wrap .icon-salient-cart
				  {
				 	color: '.$starting_color.'!important;
				 	opacity: 0.75!important;
					transition: opacity 0.2s ease, color 0.2s ease;
				 }
				#header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav > ul > li > a:hover, #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.sfHover > a, #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current_page_ancestor > a, 
				#header-outer.transparent header#top nav .sf-menu > li.current-menu-item > a, #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current-menu-ancestor > a, #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current-menu-item > a, #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current_page_item > a,
				#header-outer.transparent header#top nav > ul > li > a:hover > .sf-sub-indicator > i, #header-outer.transparent header#top nav > ul > li.sfHover > a > span > i,  #header-outer.transparent header#top nav ul #nectar-user-account a:hover span, #header-outer.transparent header#top nav ul #search-btn a:hover span, #header-outer.transparent header#top nav ul #nectar-user-account a:hover span, #header-outer.transparent header#top nav ul .slide-out-widget-area-toggle a:hover span,
				#header-outer.transparent header#top nav .sf-menu > li.current-menu-item > a i, body #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current_page_item > a .sf-sub-indicator i, #header-outer.transparent header#top nav .sf-menu > li.current-menu-ancestor > a i, body #header-outer.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current-menu-ancestor > a i,
				#header-outer.transparent .cart-outer:hover .icon-salient-cart, .ascend #boxed #header-outer.transparent .cart-outer:hover .cart-menu .cart-icon-wrap .icon-salient-cart
				
				{
					opacity: 1!important;
					color: '.$starting_color.'!important;
				}

				#header-outer.transparent[data-lhe="animated_underline"] header#top nav > ul > li > a:hover, #header-outer.transparent[data-lhe="animated_underline"] header#top nav .sf-menu > li.sfHover > a,
				 #header-outer.transparent[data-lhe="animated_underline"] header#top nav .sf-menu > li.current-menu-ancestor > a, #header-outer.transparent[data-lhe="animated_underline"] header#top nav .sf-menu > li.current_page_item > a {
					opacity: 1!important;
				}

				#header-outer[data-lhe="animated_underline"].transparent header#top nav > ul > li > a:after, #header-outer.transparent header#top nav>ul>li[class*="button_bordered"]>a:before {
					border-color: '.$starting_color.'!important;
				}


				#header-outer.transparent:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a i.lines, 
				#header-outer.transparent:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a i.lines:before,
				#header-outer.transparent:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a i.lines:after,
				body.material #header-outer.transparent:not(.directional-nav-effect) .slide-out-widget-area-toggle a .close-line,
				#header-outer.transparent:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .lines-button:after,
				#header-outer.transparent.directional-nav-effect > header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i, #header-outer.transparent.directional-nav-effect > header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i:after, #header-outer.transparent.directional-nav-effect > header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i:before,
				#header-outer.transparent:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a i.lines, 
				#header-outer.transparent:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a i.lines:before,
				#header-outer.transparent:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a i.lines:after,
				#header-outer.transparent.directional-nav-effect .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i, #header-outer.transparent.directional-nav-effect .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i:after, #header-outer.transparent.directional-nav-effect .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a span.light .lines-button i:before  {
					background-color: '.$starting_color.'!important;
				}
				#header-outer.transparent header#top nav ul .slide-out-widget-area-toggle a i.lines,
				body.material #header-outer.transparent .slide-out-widget-area-toggle a .close-line,
				#header-outer.transparent:not(.side-widget-open) header#top nav ul .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] a i.lines-button:after {
					opacity: 0.75!important;
				}
				#header-outer.transparent.side-widget-open header#top nav ul .slide-out-widget-area-toggle a i.lines,
				body.material #header-outer.transparent .slide-out-widget-area-toggle a:hover .close-line,
				#header-outer.transparent header#top nav ul .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] a:hover i.lines-button:after, 
				#header-outer.transparent header#top nav ul .slide-out-widget-area-toggle a:hover i.lines, 
				#header-outer.transparent header#top nav ul .slide-out-widget-area-toggle a:hover i.lines:before,
				#header-outer.transparent header#top nav ul .slide-out-widget-area-toggle a:hover i.lines:after {
					opacity: 1!important;
				}
		';

		$dark_header_color = (!empty($options['header-transparent-dark-color'])) ? $options['header-transparent-dark-color'] : '#000000';

		echo '#header-outer.transparent.dark-slide > header#top nav > ul > li > a, 
		#header-outer.transparent.dark-row > header#top nav > ul > li > a,
		 #header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul #search-btn a span, 
		  #header-outer.transparent.dark-row:not(.directional-nav-effect) > header#top nav ul #search-btn a span, 
			#header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul #nectar-user-account a span, 
			  #header-outer.transparent.dark-row:not(.directional-nav-effect) > header#top nav ul #nectar-user-account a span, 
		 #header-outer.transparent.dark-slide > header#top nav > ul > li > a > .sf-sub-indicator [class^="icon-"], 
		 #header-outer.transparent.dark-slide > header#top nav > ul > li > a > .sf-sub-indicator [class*=" icon-"],
		  #header-outer.transparent.dark-row > header#top nav > ul > li > a > .sf-sub-indicator [class*=" icon-"],
		 #header-outer.transparent.dark-slide:not(.directional-nav-effect) .cart-menu .cart-icon-wrap .icon-salient-cart,
		  #header-outer.transparent.dark-row:not(.directional-nav-effect) .cart-menu .cart-icon-wrap .icon-salient-cart,
		 body.ascend[data-header-color="custom"] #boxed #header-outer.transparent.dark-slide > header#top .cart-outer .cart-menu .cart-icon-wrap i,
		 body.ascend #boxed #header-outer.transparent.dark-slide > header#top .cart-outer .cart-menu .cart-icon-wrap i,
		 #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav > ul > li > a, 
		 #header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul #search-btn a span, 
		 #header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul #nectar-user-account a span, 
		 #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav > ul > li > a > .sf-sub-indicator [class^="icon-"], 
		 #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav > ul > li > a > .sf-sub-indicator [class*=" icon-"],
		 #header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top .cart-menu .cart-icon-wrap .icon-salient-cart,
		 body.ascend[data-header-color="custom"] #boxed #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top .cart-outer .cart-menu .cart-icon-wrap i,
		 body.ascend #boxed #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top .cart-outer .cart-menu .cart-icon-wrap i, 
		 .material #header-outer[data-permanent-transparent="1"].transparent.dark-slide .mobile-search .icon-salient-search,
		 .material #header-outer[data-permanent-transparent="1"].transparent.dark-slide .mobile-user-account .icon-salient-m-user,
		 .material #header-outer[data-permanent-transparent="1"].transparent.dark-slide header#top #mobile-cart-link i,
		 #header-outer[data-permanent-transparent="1"].transparent.dark-slide header#top #mobile-cart-link i {
		 	color: '.$dark_header_color.'!important;
		 }

		#header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a .lines-button i, 
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a .lines-button i:after,
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle a .lines-button i:before,
		body.marterial #header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul.slide-out-widget-area-toggle a .close-line,
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a .lines-button i, 
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a .lines-button i:after,
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle a .lines-button i:before,
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) > header#top nav ul .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .lines-button:after,
		#header-outer.transparent.dark-slide:not(.directional-nav-effect) .midnightHeader.nectar-slider header#top nav ul .slide-out-widget-area-toggle[data-icon-animation="simple-transform"] .lines-button:after,
		body.material #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines-button:after, 
		body.material #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines:before, 
		body.material #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines:after,
		body #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines-button:after, 
		body #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines:before, 
		body #header-outer[data-permanent-transparent="1"].transparent.dark-slide > header#top .span_9 > .slide-out-widget-area-toggle.mobile-icon i.lines:after  {
			background-color: '.$dark_header_color.'!important;
		}

		#header-outer.transparent.dark-slide > header#top nav > ul > li > a:hover, #header-outer.transparent.dark-slide > header#top nav .sf-menu > li.sfHover > a, #header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current_page_ancestor > a, 
		#header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current-menu-item > a, #header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current-menu-ancestor > a, #header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current_page_item > a,
		#header-outer.transparent.dark-slide > header#top nav > ul > li > a:hover > .sf-sub-indicator > i, #header-outer.transparent.dark-slide > header#top nav > ul > li.sfHover > a > span > i, #header-outer.transparent.dark-slide > header#top nav ul #search-btn a:hover span,
		#header-outer.transparent.dark-slide > header#top nav ul #nectar-user-account a:hover span,
		body #header-outer.dark-slide.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current_page_item > a .sf-sub-indicator i,
		#header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current-menu-item > a i, #header-outer.transparent.dark-slide > header#top nav .sf-menu > li.current-menu-ancestor > a i,
		body #header-outer.dark-slide.transparent:not([data-lhe="animated_underline"]) header#top nav .sf-menu > li.current-menu-ancestor > a i,
		#header-outer.transparent.dark-slide  > header#top .cart-outer:hover .icon-salient-cart,
		body.ascend[data-header-color="custom"] #boxed #header-outer.transparent.dark-slide > header#top .cart-outer:hover .cart-menu .cart-icon-wrap i,
		#header-outer.transparent.dark-slide > header#top #logo,
		#header-outer[data-permanent-transparent="1"].transparent.dark-slide .midnightHeader.nectar-slider header#top .span_9 > .slide-out-widget-area-toggle i,
		#header-outer.transparent:not([data-lhe="animated_underline"]).dark-slide header#top nav .sf-menu > li.current_page_item > a,
		#header-outer.transparent:not([data-lhe="animated_underline"]).dark-slide header#top nav .sf-menu > li.current-menu-ancestor > a,
		#header-outer.transparent:not([data-lhe="animated_underline"]).dark-slide header#top nav > ul > li > a:hover, #header-outer.transparent:not([data-lhe="animated_underline"]).dark-slide header#top nav .sf-menu > li.sfHover > a,
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav > ul > li > a:hover, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.sfHover > a, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current_page_ancestor > a, 
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current-menu-item > a, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current-menu-ancestor > a, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current_page_item > a,
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav > ul > li > a:hover > .sf-sub-indicator > i, #header-outer.transparent.dark-slide header#top nav > ul > li.sfHover > a > span > i, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav ul #search-btn a:hover span,
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav ul #nectar-user-account a:hover span,
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current-menu-item > a i, #header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top nav .sf-menu > li.current-menu-ancestor > a i,
		#header-outer.transparent.dark-slide  .midnightHeader.nectar-slider header#top .cart-outer:hover .icon-salient-cart,
		body.ascend[data-header-color="custom"] #boxed #header-outer.transparent.dark-slide > header#top .cart-outer:hover .cart-menu .cart-icon-wrap i,
		#header-outer.transparent.dark-slide .midnightHeader.nectar-slider header#top #logo,
		.swiper-wrapper .swiper-slide[data-color-scheme="dark"] .slider-down-arrow i.icon-default-style[class^="icon-"],
		.slider-prev.dark-cs i, .slider-next.dark-cs i, .swiper-container .dark-cs.slider-prev .slide-count span, .swiper-container .dark-cs.slider-next .slide-count span {
			color: '.$dark_header_color.'!important;
		}
		#header-outer[data-lhe="animated_underline"].transparent.dark-slide header#top nav > ul > li > a:after,
		#header-outer[data-lhe="animated_underline"].transparent:not(.side-widget-open) .midnightHeader.dark header#top nav > ul > li > a:after,
		#header-outer[data-lhe="animated_underline"].transparent:not(.side-widget-open) .midnightHeader.default header#top nav > ul > li > a:after,
		#header-outer.dark-slide.transparent:not(.side-widget-open) header#top nav>ul>li[class*="button_bordered"]>a:before {
			border-color: '.$dark_header_color.'!important;
		}
		.swiper-container[data-bullet_style="scale"] .slider-pagination.dark-cs .swiper-pagination-switch.swiper-active-switch i,
		.swiper-container[data-bullet_style="scale"] .slider-pagination.dark-cs .swiper-pagination-switch:hover i {
			background-color: '.$dark_header_color.';
		}

		.slider-pagination.dark-cs .swiper-pagination-switch {
			 border: 1px solid '.$dark_header_color.';
			 background-color: transparent;
		}
		.slider-pagination.dark-cs .swiper-pagination-switch:hover {
			background: none repeat scroll 0 0 '.$dark_header_color.';
		}

		.slider-pagination.dark-cs .swiper-active-switch {
			 background: none repeat scroll 0 0 '.$dark_header_color.';
		}
		';

	   $dark_header_color = str_replace("#", "", $dark_header_color);;
		 $darkcolorR = hexdec( substr( $dark_header_color, 0, 2 ) );
		 $darkcolorG = hexdec( substr( $dark_header_color, 2, 2 ) );
		 $darkcolorB = hexdec( substr( $dark_header_color, 4, 2 ) );
		 echo '
		 #fp-nav:not(.light-controls) ul li a span:after { background-color: #'.$dark_header_color.'; }
		 #fp-nav:not(.light-controls) ul li a span { box-shadow: inset 0 0 0 8px rgba('.$darkcolorR.','.$darkcolorG.','.$darkcolorB.',0.3); -webkit-box-shadow: inset 0 0 0 8px rgba('.$darkcolorR.','.$darkcolorG.','.$darkcolorB.',0.3); }
		 body #fp-nav ul li a.active span  { box-shadow: inset 0 0 0 2px rgba('.$darkcolorR.','.$darkcolorG.','.$darkcolorB.',0.8); -webkit-box-shadow: inset 0 0 0 2px rgba('.$darkcolorR.','.$darkcolorG.','.$darkcolorB.',0.8); }';

	 } // using transparent theme option
		 
		 
	 if($external_dynamic != 'on') {
			
			 // header transparent option
			if(!empty($options['transparent-header']) && $options['transparent-header'] == '1') {
				$activate_transparency = using_page_header($post->ID); 
				
				if($activate_transparency) {
					
					//old IE versions
					echo '.no-rgba #header-space { display: none;  } ';
					
					echo '@media only screen and (min-width: 1000px) {
						
						 #header-space {
						 	 display: none; 
						 } 
						 .nectar-slider-wrap.first-section, .parallax_slider_outer.first-section, .full-width-content.first-section, 
						 .parallax_slider_outer.first-section .swiper-slide .content, .nectar-slider-wrap.first-section .swiper-slide .content, #page-header-bg, .nder-page-header, #page-header-wrap,
						 .full-width-section.first-section {
						 	 margin-top: 0!important;
						 }
						 
						 body #page-header-bg, body #page-header-wrap {
						 	height: '.$header_space.'px;
						 }

						 
						 body #search-outer { z-index: 100000; }
						 
						 
					 	}';
				
				} //activate
				
				else if(!empty($options['header-bg-opacity'])) {
					$header_space_bg_color = (!empty($options['overall-bg-color'])) ? $options['overall-bg-color'] : '#ffffff';
					echo 'body:not(.material) #header-space { background-color: '.$header_space_bg_color.'}';
				}
				
			} //using theme option
		
	} //external dynamic

	


	//material loader color
	$loading_icon = (isset($options['loading-icon'])) ? $options['loading-icon'] : 'default';
	if($loading_icon == 'material'){
		$icon_colors = (isset($options['loading-icon-colors'])) ? $options['loading-icon-colors'] : array('from' => '#444444', 'to' => '#444444');
		echo '.loading-icon .material-icon .bar:after { background-color: '.$icon_colors['from'].'; }
			  .loading-icon .material-icon .bar { border-color: '.$icon_colors['from'].';}
			  .loading-icon .material-icon .color-2 .bar:after { background-color: '.$icon_colors['to'].'; }
			  .loading-icon .material-icon .color-2 .bar { border-color: '.$icon_colors['to'].';}';

		 if($icon_colors['from'] == $icon_colors['to']) {
		 	echo '.loading-icon .material-icon .spinner.color-2 { display: none!important; } .loading-icon .material-icon > div:first-child .right-side, .loading-icon .material-icon > div:first-child .left-side { -webkit-animation: none!important; animation: none!important; }';
		 }
	}
	 // ext responsive
	global $woocommerce;
	
	if(!empty($options['responsive']) && $options['responsive'] == 1 && !empty($options['ext_responsive']) && $options['ext_responsive'] == '1') {
		echo '@media only screen and (min-width: 1000px) {
			
			    .container, body[data-header-format="left-header"] .container, .woocommerce-tabs .full-width-content .tab-container, .nectar-recent-posts-slider .flickity-page-dots, .post-area.standard-minimal.full-width-content article.post .inner-wrap, .material #search-outer #search  {
			      max-width: 1425px; 
				  width: 100%;
				  margin: 0 auto;
				  padding: 0px 90px; 
			    } 

			    body[data-header-format="left-header"] .container, body[data-header-format="left-header"] .woocommerce-tabs .full-width-content .tab-container, body[data-header-format="left-header"] .nectar-recent-posts-slider .flickity-page-dots,
			    body[data-header-format="left-header"] .post-area.standard-minimal.full-width-content article.post .inner-wrap {
			    	padding: 0 60px;
			    }

			    body .container .page-submenu.stuck .container:not(.tab-container):not(.normal-container), .nectar-recent-posts-slider .flickity-page-dots,
			    #nectar_fullscreen_rows[data-footer="default"] #footer-widgets .container, #nectar_fullscreen_rows[data-footer="default"] #copyright .container {
			    	  padding: 0px 90px!important; 
			    }	
				
				.swiper-slide .content {
				  padding: 0px 90px; 
				}

				body[data-header-format="left-header"] .container .page-submenu.stuck .container:not(.tab-container),  body[data-header-format="left-header"] .nectar-recent-posts-slider .flickity-page-dots {
			    	  padding: 0px 60px!important; 
			    }	
				
				body[data-header-format="left-header"] .swiper-slide .content {
				  padding: 0px 60px; 
				}
				
				body .container .container:not(.tab-container):not(.recent-post-container):not(.normal-container) {
					width: 100%!important;
					padding: 0!important;
				}
				
				
				body .carousel-heading .container {
					padding: 0 10px!important;
				}
				body .carousel-heading .container .carousel-next { right: 10px; } body .carousel-heading .container .carousel-prev { right: 35px; }
				.carousel-wrap[data-full-width="true"] .carousel-heading a.portfolio-page-link { left: 90px; }
				.carousel-wrap[data-full-width="true"] .carousel-heading { margin-left: -20px; margin-right: -20px; }
				.carousel-wrap[data-full-width="true"] .carousel-next { right: 90px!important; } .carousel-wrap[data-full-width="true"] .carousel-prev { right: 115px!important; }
				.carousel-wrap[data-full-width="true"] { padding: 0!important; }
				.carousel-wrap[data-full-width="true"] .caroufredsel_wrapper { padding: 20px!important; }
				
				#search-outer #search #close a {
					right: 90px;
				}
	
	
				#boxed, #boxed #header-outer, #boxed #header-secondary-outer, #boxed #slide-out-widget-area-bg.fullscreen, #boxed #page-header-bg[data-parallax="1"], #boxed #featured, body[data-footer-reveal="1"] #boxed #footer-outer, #boxed .orbit > div, #boxed #featured article, .ascend #boxed #search-outer {
				   max-width: 1400px!important;
				   width: 90%!important;
				   min-width: 980px;
				}

				body[data-hhun="1"] #boxed #header-outer:not(.detached), body[data-hhun="1"] #boxed #header-secondary-outer,
				#boxed #header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]:not(.fixed-menu),
				#boxed #header-secondary-outer.centered-menu-bottom-bar {
					width: 100%!important;
				}


				#boxed #search-outer #search #close a {
					right: 0!important;
				}

				#boxed .container {
				  width: 92%;
				  padding: 0;
			    } 
				
				#boxed #footer-outer #footer-widgets, #boxed #footer-outer #copyright {
					padding-left: 0;
					padding-right: 0;
				}

				#boxed .carousel-wrap[data-full-width="true"] .carousel-heading a.portfolio-page-link { left: 35px; }
				#boxed .carousel-wrap[data-full-width="true"] .carousel-next { right: 35px!important; } #boxed .carousel-wrap[data-full-width="true"] .carousel-prev { right: 60px!important; }

				
			 }';

			  //custom max width
			  if(!empty($options['max_container_width'])) {
			  	   echo '@media only screen and (min-width: 1000px) { .container, body[data-header-format="left-header"] .container, .woocommerce-tabs .full-width-content .tab-container, .nectar-recent-posts-slider .flickity-page-dots, .post-area.standard-minimal.full-width-content article.post .inner-wrap, .material #search-outer #search { max-width: '.$options['max_container_width'].'px; } }';
			  }	


		if($external_dynamic != 'on') {	 

			if($woocommerce && $woocommerce->cart->cart_contents_count > 0 && !empty($options['enable-cart']) && $options['enable-cart'] == '1') {
				echo '@media only screen and (min-width: 1080px) and (max-width: 1475px) {
				    header#top nav > ul.buttons {
					  padding-right: 20px!important; 
				    } 
					#boxed header#top nav > ul.product_added.buttons {
						padding-right: 0px!important; 
					}
					#search-outer #search #close a {
						right: 110px;
					}
				 }';
			}
			elseif($woocommerce && !empty($options['enable-cart']) && $options['enable-cart'] == '1') {
				echo '@media only screen and (min-width: 1080px) and (max-width: 1475px) {
				    header#top nav > ul.product_added.buttons {
					  padding-right: 20px!important; 
				    } 
					#boxed header#top nav > ul.product_added.buttons {
						padding-right: 0px!important; 
					}
					#search-outer #search #close a.product_added {
						right: 110px;
					}
				 }';
			 }

		}
  
	} 
	
	echo '.pagination-navigation { -webkit-filter: url("'.esc_url(get_permalink()).'#goo"); filter: url("'.esc_url(get_permalink()).'#goo"); }';

	//full width header shopping cart fix
	if($external_dynamic != 'on') {	
		if($woocommerce && $woocommerce->cart->cart_contents_count > 0 && !empty($options['enable-cart']) && $options['enable-cart'] == '1' && !empty($options['header-fullwidth']) && $options['header-fullwidth'] == '1') {
			echo '@media only screen and (min-width: 1080px) {
				body:not(.material) #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons {
			 	 padding-right: 80px!important; 
		        }
		        body:not(.ascend):not(.material) #boxed #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons { padding-right: 0px!important;  }

		        body:not(.ascend):not(.material) #header-outer[data-full-width="true"][data-remove-border="true"].transparent header#top nav > ul.product_added .slide-out-widget-area-toggle,
		        body:not(.ascend):not(.material) #header-outer[data-full-width="true"][data-remove-border="true"].side-widget-open header#top nav > ul.product_added .slide-out-widget-area-toggle {
		          margin-right: -20px!important; 
		    	}
		    }';
		} elseif($woocommerce && !empty($options['enable-cart']) && $options['enable-cart'] == '1' && !empty($options['header-fullwidth']) && $options['header-fullwidth'] == '1') {
			echo '@media only screen and (min-width: 1080px) {
				body:not(.material)  #header-outer[data-full-width="true"] header#top nav > ul.product_added.buttons {
			 	 padding-right: 80px!important; 
		        }
		        body:not(.ascend):not(.material) #header-outer[data-full-width="true"][data-remove-border="true"].transparent header#top nav > ul.product_added .slide-out-widget-area-toggle,
		        body:not(.ascend):not(.material) #header-outer[data-full-width="true"][data-remove-border="true"].side-widget-open header#top nav > ul.product_added .slide-out-widget-area-toggle {
		          margin-right: -20px!important; 
		    	}
		    
		    }';
		}

		if($woocommerce && !empty($options['product_archive_bg_color'])) {
			echo '.post-type-archive-product.woocommerce .container-wrap, .tax-product_cat.woocommerce .container-wrap { background-color: '.$options['product_archive_bg_color'].'; } ';
		}

	
		if($woocommerce && !empty($options['product_tab_position']) && $options['product_tab_position'] == 'fullwidth' ||
		   $woocommerce && !empty($options['product_tab_position']) && $options['product_tab_position'] == 'fullwidth_centered') echo '
		 .woocommerce.single-product #single-meta { position: relative!important; top: 0!important; margin: 0; left: 8px; height: auto; } 
		 .woocommerce.single-product #single-meta:after { display: block; content: " "; clear: both; height: 1px;  } 
		 .woocommerce-tabs { margin-top: 40px; clear: both; }
		 @media only screen and (min-width: 1000px) {
			 .woocommerce #reviews #comments, .woocommerce #reviews #review_form_wrapper {  float: left; width: 47%; }
			 .woocommerce #reviews #comments { margin-right: 3%; width: 50%; } 
			 .woocommerce.ascend #respond { margin-top: 0px!important; }
			 .rtl.woocommerce #reviews #comments, .woocommerce #reviews #review_form_wrapper {  float: right;}
			 .rtl.woocommerce #reviews #comments { margin-left: 3%; margin-right: 0;}
			 .woocommerce .woocommerce-tabs > div { margin-top: 15px!important; }
			 .woocommerce #reviews #reply-title { margin-top: 5px!important; }
		 }';

		 if($woocommerce && !empty($options['product_bg_color'])) {
		 	echo '.woocommerce ul.products li.product.material, .woocommerce-page ul.products li.product.material { background-color: '.$options['product_bg_color'].'; }';
		 }
		 
		 if($woocommerce && !empty($options['product_minimal_bg_color'])) {
		 	echo '.woocommerce ul.products li.product.minimal .product-wrap, .woocommerce ul.products li.product.minimal .background-color-expand,
			.woocommerce-page ul.products li.product.minimal .product-wrap, .woocommerce-page ul.products li.product.minimal .background-color-expand  { background-color: '.$options['product_minimal_bg_color'].'; }';
		 }
		
	}
	
	if($external_dynamic != 'on') {

		//custom css
		if(!empty($options["custom-css"])){
			echo $options["custom-css"];
		} 

		echo '</style>';
		
		
		$dynamic_css = ob_get_contents();
		ob_end_clean();
		
		echo nectar_quick_minify($dynamic_css);	


	} else {
		//custom css
		if(!empty($options["custom-css"])){
			echo $options["custom-css"];
		} 
	}
	
	
	


?>