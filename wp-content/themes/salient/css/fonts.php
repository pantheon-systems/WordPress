<?php 

$options = get_nectar_theme_options();

$external_dynamic = (!empty($options['external-dynamic-css']) && $options['external-dynamic-css'] == 1) ? 'on' : 'off';


	$options = get_nectar_theme_options(); 

	$legacy_options = get_option('salient');
	$current_options = get_option('salient_redux');

	//load custom fonts
	if(!empty($current_options)) {
		$font_fields = array('navigation_font_family','navigation_dropdown_font_family','portfolio_filters_font_family','portfolio_caption_font_family','page_heading_font_family','page_heading_subtitle_font_family','off_canvas_nav_font_family','off_canvas_nav_subtext_font_family','body_font_family','h1_font_family','h2_font_family','h3_font_family','h4_font_family','h5_font_family','h6_font_family','i_font_family','label_font_family','nectar_slider_heading_font_family','home_slider_caption_font_family','testimonial_font_family','sidebar_footer_h_font_family','team_member_h_font_family','nectar_dropcap_font_family','nectar_sidebar_footer_headers_font_family','nectar_woo_shop_product_title_font_family','nectar_woo_shop_product_secondary_font_family');
			
		//legacy formatting
		foreach($font_fields as $k => $v) { 
			$options[str_replace('_family', '', $v)] = (empty($options[$v]['font-family'])) ? '-' : $options[$v]['font-family'];
			$options[str_replace('_family', '', $v) . '_size'] = (empty($options[$v]['font-size'])) ? '-' : $options[$v]['font-size'];
			$options[str_replace('_family', '', $v) . '_line_height'] = (empty($options[$v]['line-height'])) ? '-' : $options[$v]['line-height'];
			$options[str_replace('_family', '', $v) . '_spacing'] = (empty($options[$v]['letter-spacing'])) ? '-' : $options[$v]['letter-spacing'];
			$options[str_replace('_family', '', $v) . '_weight'] = (empty($options[$v]['font-weight'])) ? '-' : $options[$v]['font-weight'];
			$options[str_replace('_family', '', $v) . '_transform'] = (empty($options[$v]['text-transform'])) ? '-' : $options[$v]['text-transform'];
			$options[str_replace('_family', '', $v) . '_style'] = (empty($options[$v]['font-weight'])) ? '-' : $options[$v]['font-weight'];
			
			$options[$v]['attrs_in_use'] = false;
			if(!empty( $options[str_replace('_family', '', $v)] ) && $options[str_replace('_family', '', $v)] != '-' ||
				 !empty( $options[str_replace('_family', '', $v) . '_size'] ) && $options[str_replace('_family', '', $v) . '_size'] != '-' ||
			 	 !empty( $options[str_replace('_family', '', $v) . '_line_height'] ) && $options[str_replace('_family', '', $v) . '_line_height'] != '-' || 
			   !empty( $options[str_replace('_family', '', $v) . '_spacing'] ) && $options[str_replace('_family', '', $v) . '_spacing'] != '-' ||
			   !empty( $options[str_replace('_family', '', $v) . '_weight'] ) && $options[str_replace('_family', '', $v) . '_weight'] != '-' ||
			   !empty( $options[str_replace('_family', '', $v) . '_transform'] ) && $options[str_replace('_family', '', $v) . '_transform'] != '-' ||
			   !empty( $options[str_replace('_family', '', $v) . '_style'] ) && $options[str_replace('_family', '', $v) . '_style'] != '-') {
				 $options[$v]['attrs_in_use'] = true;
			}
			
			if(!empty($options[$v]['font-weight']) && !empty($options[$v]['font-style'])) $options[str_replace('_family', '', $v) . '_style'] = $options[$v]['font-weight'] . $options[$v]['font-style'];
		}

	}


	
	if($external_dynamic != 'on') { ob_start(); }

	$body = $options['body_font'];
	$navigation = $options['navigation_font'];
	$navigation_dropdown = $options['navigation_dropdown_font'];
	$sidebar_carousel_footer_header = $options['sidebar_footer_h_font'];
	$team_member_names = $options['team_member_h_font'];
	
	if($external_dynamic != 'on') { echo '<style type="text/css">'; }


	/*responsive heading values */
	$using_custom_responsive_sizing = ( !empty($options['use-responsive-heading-typography']) && $options['use-responsive-heading-typography'] == '1') ? true : false;

	$nectar_h1_small_desktop = ( !empty($options['h1-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h1-small-desktop-font-size'])/100 : 0.75;
	$nectar_h1_tablet = ( !empty($options['h1-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h1-tablet-font-size'])/100 : 0.7;
	$nectar_h1_phone = ( !empty($options['h1-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h1-phone-font-size'])/100 : 0.65;
	$nectar_h1_default_size = 54;

	$nectar_h2_small_desktop = ( !empty($options['h2-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h2-small-desktop-font-size'])/100 : 0.85;
	$nectar_h2_tablet = ( !empty($options['h2-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h2-tablet-font-size'])/100 : 0.8;
	$nectar_h2_phone = ( !empty($options['h2-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h2-phone-font-size'])/100 : 0.75;
	$nectar_h2_default_size = 34;

	$nectar_h3_small_desktop = ( !empty($options['h3-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h3-small-desktop-font-size'])/100 : 0.85;
	$nectar_h3_tablet = ( !empty($options['h3-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h3-tablet-font-size'])/100 : 0.8;
	$nectar_h3_phone = ( !empty($options['h3-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h3-phone-font-size'])/100 : 0.8;
	$nectar_h3_default_size = 22;

	$nectar_h4_small_desktop = ( !empty($options['h4-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h4-small-desktop-font-size'])/100 : 1;
	$nectar_h4_tablet = ( !empty($options['h4-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h4-tablet-font-size'])/100 : 1;
	$nectar_h4_phone = ( !empty($options['h4-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h4-phone-font-size'])/100 : 0.9;
	$nectar_h4_default_size = 18;

	$nectar_h5_small_desktop = ( !empty($options['h5-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h5-small-desktop-font-size'])/100 : 1;
	$nectar_h5_tablet = ( !empty($options['h5-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h5-tablet-font-size'])/100 : 1;
	$nectar_h5_phone = ( !empty($options['h5-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h5-phone-font-size'])/100 : 1;
	$nectar_h5_default_size = 16;

	$nectar_h6_small_desktop = ( !empty($options['h6-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['h6-small-desktop-font-size'])/100 : 1;
	$nectar_h6_tablet = ( !empty($options['h6-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['h6-tablet-font-size'])/100 : 1;
	$nectar_h6_phone = ( !empty($options['h6-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['h6-phone-font-size'])/100 : 1;
	$nectar_h6_default_size = 14;
	
	$nectar_body_small_desktop = ( !empty($options['body-small-desktop-font-size']) && $using_custom_responsive_sizing) ? intval($options['body-small-desktop-font-size'])/100 : 1;
	$nectar_body_tablet = ( !empty($options['body-tablet-font-size']) && $using_custom_responsive_sizing) ? intval($options['body-tablet-font-size'])/100 : 1;
	$nectar_body_phone = ( !empty($options['body-phone-font-size']) && $using_custom_responsive_sizing) ? intval($options['body-phone-font-size'])/100 : 1;
	$nectar_body_default_size = 14;


	/*-------------------------------------------------------------------------*/
	/*	Body Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['body_font_style']);

	( intval( substr($options['body_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['body_font_size'],0,-2)) * 1.8 .'px' : $line_height = null ;  ?>
	
	<?php 
	if($options['body_font_family']['attrs_in_use']) {
		
		echo 'body, .toggle h3 a, body .ui-widget, table, .bar_graph li span strong, #slide-out-widget-area .tagcloud a, body .container .woocommerce-message a.button, #search-results .result .title span, .woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .row .col.section-title .nectar-love span, body .nectar-love span, body .nectar-social .nectar-love .nectar-love-count, body .carousel-heading h2, .sharing-default-minimal .nectar-social .social-text, body .sharing-default-minimal .nectar-love, .widget ul.nectar_widget[class*="nectar_blog_posts_"] > li .post-date
		{'; ?>
			<?php if($options['body_font'] != '-') {
				$font_family = (1 === preg_match('~[0-9]~', $options['body_font'])) ? '"'. $options['body_font'] .'"' : $options['body_font'];
			}
				  if($options['body_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['body_font_transform'] != '-') echo 'text-transform: ' . $options['body_font_transform'] .';'; 
				  if($options['body_font_spacing'] != '-') echo 'letter-spacing: ' . $options['body_font_spacing'] .';'; 
			    if($options['body_font_size'] != '-') echo 'font-size:' . $options['body_font_size'] .';'; ?>
			
			<?php 
			//user set line-height
			 if($options['body_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['body_font_line_height'] .';'; 
			 	$the_line_height = $options['body_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>

			<?php 
			if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; 
		
	     if($options['body_font'] != '-') {
		   echo '.bold, strong, b { font-family: ' . $font_family .'; font-weight: 600; } ';
		   echo '.single #single-below-header span { font-family: ' . $font_family .';  }';
		 }
		
		 echo '.nectar-fancy-ul ul li .icon-default-style[class^="icon-"] {'; 
			if(!empty($the_line_height)) echo 'line-height:' . $the_line_height .'!important;';
		 echo '}'; 
		 
		 
		
			 $defined_font_size = (!empty($options['body_font_size']) && $options['body_font_size'] != '-') ? intval($options['body_font_size']) : $nectar_body_default_size;
			 $defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : intval($nectar_body_default_size) * 1.8;
		 ?>

		 @media only screen and (max-width: 1300px) and (min-width: 1000px) {
			 body {
				 font-size: <?php echo esc_html( ceil($defined_font_size*$nectar_body_small_desktop) ) . 'px'; ?>;
				 line-height: <?php echo esc_html( ceil($defined_line_height*$nectar_body_small_desktop) ) . 'px'; ?>;
			 }
		 }
		 @media only screen and (max-width: 1000px) and (min-width: 690px) {
			 body {
				 font-size: <?php echo esc_html( ceil($defined_font_size*$nectar_body_tablet) ) . 'px'; ?>;
				 line-height: <?php echo esc_html( ceil($defined_line_height*$nectar_body_tablet) ) . 'px'; ?>;
			 }
			 
		 }
		 @media only screen and (max-width: 690px) {
			 body {
				 font-size: <?php echo esc_html( ceil($defined_font_size*$nectar_body_phone) ) . 'px'; ?>;
				 line-height: <?php echo esc_html( ceil($defined_line_height*$nectar_body_phone) ) . 'px'; ?>;
			 }

		 }
		 
	 
 <?php } //attrs in use ?>
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Navigation Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['navigation_font_style']);
	
	( intval( substr($options['navigation_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['navigation_font_size'],0,-2)) *1.4 .'px' : $line_height = null ;  ?>
	
	<?php 
	if($options['navigation_font_family']['attrs_in_use']) {
		
		echo 'header#top nav > ul > li > a, .span_3 .pull-left-wrap > ul > li > a, body.material #search-outer #search input[type="text"], #header-secondary-outer .nectar-center-text, #slide-out-widget-area .secondary-header-text
		{'; ?>	
			<?php if($options['navigation_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['navigation_font'])) ? '"'. $options['navigation_font'] .'"' : $options['navigation_font'];
			}
				  if($options['navigation_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['navigation_font_transform'] != '-') echo 'text-transform: ' . $options['navigation_font_transform'] .';'; 
				  if($options['navigation_font_spacing'] != '-') echo 'letter-spacing: ' . $options['navigation_font_spacing'] .';'; 
			      if($options['navigation_font_size'] != '-') echo 'font-size:' . $options['navigation_font_size'] .';'; ?>
		
			<?php if(!empty($line_height)) echo 'line-height:' . $line_height .';'; ?>
			<?php 
			      if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				  if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; 

		    if($options['navigation_font_size'] != '-') {
					
		    	echo 'header#top nav > ul > li[class*="button_solid_color"] > a:before, #header-outer.transparent header#top nav > ul > li[class*="button_solid_color"] > a:before { 
		    		height: ' . floor((intval(substr($options['navigation_font_size'],0,-2)) *1.4)+ 5)  .'px; 
		    	}';

		    	echo 'header#top nav > ul > li[class*="button_bordered"] > a:before, #header-outer.transparent header#top nav > ul > li[class*="button_bordered"] > a:before { 
		    		height: ' . floor((intval(substr($options['navigation_font_size'],0,-2)) *1.4)+ 15)  .'px; 
		    	}';
					
					if(intval(substr($options['navigation_font_size'],0,-2)) >= 16) {
						echo '.material .sf-menu > li > a > .sf-sub-indicator [class^="icon-"] { font-size: 18px; }';
					}
			}

			//make search font match main nav font
			//if($options['navigation_font'] != '-') echo '#search-outer #search input[type="text"] { font-family: ' . $font_family .'; }';
			
		}//attrs in use
	?>
	
	
	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Navigation Dropdown Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['navigation_dropdown_font_style']);
	( intval( substr($options['navigation_dropdown_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['navigation_dropdown_font_size'],0,-2)) + 10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['navigation_dropdown_font_family']['attrs_in_use']) {
		
		echo 'header#top .sf-menu li ul li a, #header-secondary-outer nav > ul > li > a, #header-secondary-outer ul ul li a, #header-outer .widget_shopping_cart .cart_list a
		{';?>	
			<?php if($options['navigation_dropdown_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['navigation_dropdown_font'])) ? '"'. $options['navigation_dropdown_font'] .'"' : $options['navigation_dropdown_font'];
			}
				  if($options['navigation_dropdown_font'] != '-') echo 'font-family: ' . $font_family .';';
				  if($options['navigation_dropdown_font_transform'] != '-') echo 'text-transform: ' . $options['navigation_dropdown_font_transform'] .';'; 
				  if($options['navigation_dropdown_font_spacing'] != '-') echo 'letter-spacing: ' . $options['navigation_dropdown_font_spacing'] .';'; 
			      if($options['navigation_dropdown_font_size'] != '-') echo 'font-size:' . $options['navigation_dropdown_font_size'] .';'; ?>
				
			<?php 
			//user set line-height
			 if($options['navigation_dropdown_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['navigation_dropdown_font_line_height'] .';'; 
			 	$the_line_height = $options['navigation_dropdown_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
				  if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				  if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>
		
		
		<?php echo '@media only screen 
		and (min-width : 1px) and (max-width : 1000px) 
		{
		  header#top .sf-menu a {
		  	font-family: '. $options['navigation_dropdown_font'] .'!important;
		  	font-size: 14px!important;
		  }
		}'; 
		
	} // attrs in use ?>
	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h1
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h1_font_style']);
	
	( intval( substr($options['h1_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h1_font_size'],0,-2)) +6 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['h1_font_family']['attrs_in_use']) {
					
			echo '#page-header-bg h1, body h1, body .row .col.section-title h1, .full-width-content .nectar-recent-posts-slider .recent-post-container .inner-wrap h2, body #error-404 h1
			{'; ?>	
				<?php if($options['h1_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['h1_font'])) ? '"'. $options['h1_font'] .'"' : $options['h1_font'];
				}
					  if($options['h1_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['h1_font_transform'] != '-') echo 'text-transform: ' . $options['h1_font_transform'] .';'; 
					  if($options['h1_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h1_font_spacing'] .';'; 
				      if($options['h1_font_size'] != '-') echo 'font-size:' . $options['h1_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['h1_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['h1_font_line_height'] .';'; 
				 	$the_line_height = $options['h1_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
					  if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
					  if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			

			<?php 
				$defined_font_size = (!empty($options['h1_font_size']) && $options['h1_font_size'] != '-') ? intval($options['h1_font_size']) : $nectar_h1_default_size;
				$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h1_default_size + 6;
			?>

			@media only screen and (max-width: 1300px) and (min-width: 1000px) {
				body .row .col.section-title h1, body h1, .full-width-content .recent-post-container .inner-wrap h2 {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_small_desktop ) . 'px'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_small_desktop ) . 'px'; ?>;
				}
			}
			@media only screen and (max-width: 1000px) and (min-width: 690px) {
				body .row .col.section-title h1, body h1, html body .row .col.section-title.span_12 h1, .full-width-content .nectar-recent-posts-slider .recent-post-container .inner-wrap h2 {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_tablet ) . 'px'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_tablet ) . 'px'; ?>;
				}
				.full-width-content .recent-post-container .inner-wrap h2 {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_tablet ) . 'px'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_tablet ) . 'px'; ?>;
				}
				
				.wpb_wrapper h1.vc_custom_heading {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_tablet ) . 'px!important'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_tablet ) . 'px!important'; ?>;
				}
				
			}
			@media only screen and (max-width: 690px) {
				body .row .col.section-title h1, body h1, html body .row .col.section-title.span_12 h1, .full-width-content .nectar-recent-posts-slider .recent-post-container .inner-wrap h2 {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_phone ) . 'px'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_phone ) . 'px'; ?>;
				}
				
				.wpb_wrapper h1.vc_custom_heading {
					font-size: <?php echo esc_html( $defined_font_size*$nectar_h1_phone ) . 'px!important'; ?>;
					line-height: <?php echo esc_html( $defined_line_height*$nectar_h1_phone ) . 'px!important'; ?>;
				}

			}

<?php 	} // attrs in use ?>
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h2
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h2_font_style']);
	
	( intval( substr($options['h2_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h2_font_size'],0,-2)) + intval(substr($options['h2_font_size'],0,-2))*0.65 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['h2_font_family']['attrs_in_use']) {
		
		echo '#page-header-bg h2, body h2, article.post .post-header h2, article.post.quote .post-content h2, article.post.link .post-content h2, article.post.format-status .post-content h2,
		#call-to-action span, .woocommerce .full-width-tabs #reviews h3, .row .col.section-title h2, .nectar_single_testimonial[data-style="bold"] p, .woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control,
		body #error-404 h2, .woocommerce-page .woocommerce p.cart-empty
		{'; ?>	
			<?php if($options['h2_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['h2_font'])) ? '"'. $options['h2_font'] .'"' : $options['h2_font'];
			}
				  if($options['h2_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['h2_font_transform'] != '-') echo 'text-transform: ' . $options['h2_font_transform'] .';'; 
				  if($options['h2_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h2_font_spacing'] .';'; 
			    if($options['h2_font_size'] != '-') echo 'font-size:' . $options['h2_font_size'] .';'; ?>
		
			<?php 
			//user set line-height
			 if($options['h2_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['h2_font_line_height'] .';'; 
			 	$the_line_height = $options['h2_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>

			<?php 
			     if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			     if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>


		<?php 
			$defined_font_size = (!empty($options['h2_font_size']) && $options['h2_font_size'] != '-') ? intval($options['h2_font_size']) : $nectar_h2_default_size;
			$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h2_default_size + 8;
		?>
		
		.single-product div.product h1.product_title {
			font-size: <?php echo intval($defined_font_size) . 'px'; ?>;
			line-height: <?php echo intval($defined_line_height) . 'px'; ?>;
		}
		
		@media only screen and (max-width: 1300px) and (min-width: 1000px) {
		 	body h2, .single-product div.product h1.product_title {
		 		font-size: <?php echo esc_html( $defined_font_size*$nectar_h2_small_desktop ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h2_small_desktop ) . 'px'; ?>;
			}
			.row .span_2 h2, .row .span_3 h2, .row .span_4 h2, .row .vc_col-sm-2 h2, .row .vc_col-sm-3 h2, .row .vc_col-sm-4 h2 { 
				font-size: <?php echo esc_html( $defined_font_size*0.7 ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*0.7 ) . 'px'; ?>;
			}
			
		}

		@media only screen and (max-width: 1000px) and (min-width: 690px) {
		.col h2, h2, .single-product div.product h1.product_title, .woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h2_tablet ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h2_tablet ) . 'px'; ?>;
			}
			.wpb_wrapper h2.vc_custom_heading {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h2_tablet ) . 'px!important'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h2_tablet ) . 'px!important'; ?>;
			}
			
		}

		@media only screen and (max-width: 690px) {
		.col h2, h2, .single-product div.product h1.product_title, .woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h2_phone ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h2_phone ) . 'px'; ?>;
			}
			.wpb_wrapper h2.vc_custom_heading {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h2_phone ) . 'px!important'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h2_phone ) . 'px!important'; ?>;
			}
		}
		
		
	<?php } // attrs in use ?>
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h3
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h3_font_style']);
	
	( intval( substr($options['h3_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h3_font_size'],0,-2)) +8 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['h3_font_family']['attrs_in_use']) {
		
	echo 'body h3, .row .col h3, .toggle h3 a, .ascend #respond h3, .ascend h3#comments, .woocommerce ul.products li.product.text_on_hover h3, 
	.masonry.classic_enhanced .masonry-blog-item h3.title, .woocommerce ul.products li.product.material h3, .woocommerce-page ul.products li.product.material h3, .portfolio-items[data-ps="8"] .col h3,
	.nectar-hor-list-item[data-font-family="h3"], .woocommerce ul.products li.product h2, .nectar-quick-view-box h1
	{'; ?>	
		<?php if($options['h3_font'] != '-') {
			  $font_family = (1 === preg_match('~[0-9]~', $options['h3_font'])) ? '"'. $options['h3_font'] .'"' : $options['h3_font'];
		}
			  if($options['h3_font'] != '-') echo 'font-family: ' . $font_family .';'; 
			  if($options['h3_font_transform'] != '-') echo 'text-transform: ' . $options['h3_font_transform'] .';'; 
			  if($options['h3_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h3_font_spacing'] .';'; 
		      if($options['h3_font_size'] != '-') echo 'font-size:' . $options['h3_font_size'] .';'; ?>
	
		<?php 
		//user set line-height
		 if($options['h3_font_line_height'] != '-') { 
		 	echo 'line-height:' . $options['h3_font_line_height'] .';'; 
		 	$the_line_height = $options['h3_font_line_height'];
		 } else if(!empty($line_height)) {
		//auto line-height
			echo 'line-height:' . $line_height .';';
			$the_line_height = $line_height;
		} else {
			$the_line_height = null;
		}
		
		?>

		<?php 
              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
		      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
			  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
			  	  $the_weight = explode("i",$styles[0]);
			  	  echo 'font-weight:' .  $the_weight[0] .';'; 
			  	  echo 'font-style: italic';
			  }
			  else if(!empty($styles[0])) {
			  	  if(strpos($styles[0],'italic') !== false) {
			  	    echo 'font-weight: 400;'; 
			  	    echo 'font-style: italic';
			  	 }
			  }
		?>
		<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
	<?php echo '}'; ?>
	
	@media only screen and (min-width: 1000px) {
		.ascend .comments-section .comment-wrap.full-width-section > h3, .blog_next_prev_buttons[data-post-header-style="default_minimal"] .col h3 {
			font-size: <?php if(!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') echo intval($options['h3_font_size'])*1.7 . 'px!important' ?>;
			line-height: <?php if(!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') echo (intval($options['h3_font_size'])*1.7) +8 . 'px!important' ?>;
		}

		.masonry.classic_enhanced .masonry-blog-item.large_featured h3.title {
			font-size: <?php if(!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') echo intval($options['h3_font_size'])*1.5 . 'px!important' ?>;
			line-height: <?php if(!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') echo intval($the_line_height)*1.5 . 'px!important' ?>;
		}
	}

	@media only screen and (min-width: 1300px) and (max-width: 1500px){
		body .portfolio-items.constrain-max-cols.masonry-items .col.elastic-portfolio-item h3 {
			font-size: <?php if(!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') echo intval($options['h3_font_size'])*0.85 . 'px!important' ?>;
			line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.85) . 'px' ?>;
		}
	}


	<?php 
		$defined_font_size = (!empty($options['h3_font_size']) && $options['h3_font_size'] != '-') ? intval($options['h3_font_size']) : $nectar_h3_default_size;
		$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h3_default_size + 10;
	?>

	@media only screen and (max-width: 1300px) and (min-width: 1000px) {
		.row .span_2 h3, .row .span_3 h3, .row .span_4 h3, .row .vc_col-sm-2 h3, .row .vc_col-sm-3 h3, .row .vc_col-sm-4 h3, .row .col h3, body h3 {
			font-size: <?php echo esc_html( $defined_font_size*$nectar_h3_small_desktop ) . 'px'; ?>;
			line-height: <?php echo esc_html( $defined_line_height*$nectar_h3_small_desktop ) . 'px'; ?>;
		}
	}

	@media only screen and (max-width: 1000px) and (min-width: 690px) {
		.row .span_2 h3, .row .span_3 h3, .row .span_4 h3, .row .vc_col-sm-2 h3, .row .vc_col-sm-3 h3, .row .vc_col-sm-4 h3, .row .col h3, body h3 {
			font-size: <?php echo esc_html( $defined_font_size*$nectar_h3_tablet ) . 'px'; ?>;
			line-height: <?php echo esc_html( $defined_line_height*$nectar_h3_tablet ) . 'px'; ?>;
		}
		.wpb_wrapper h3.vc_custom_heading {
			font-size: <?php echo esc_html( $defined_font_size*$nectar_h3_tablet ) . 'px!important'; ?>;
			line-height: <?php echo esc_html( $defined_line_height*$nectar_h3_tablet ) . 'px!important'; ?>;
		}
	}

	@media only screen and (max-width: 690px) {
		.row .span_2 h3, .row .span_3 h3, .row .span_4 h3, .row .vc_col-sm-2 h3, .row .vc_col-sm-3 h3, .row .vc_col-sm-4 h3, .row .col h3, body h3 {
			font-size: <?php echo esc_html( $defined_font_size*$nectar_h3_phone ) . 'px'; ?>;
			line-height: <?php echo esc_html( $defined_line_height*$nectar_h3_phone ) . 'px'; ?>;
		}
		.wpb_wrapper h3.vc_custom_heading {
			font-size: <?php echo esc_html( $defined_font_size*$nectar_h3_phone ) . 'px!important'; ?>;
			line-height: <?php echo esc_html( $defined_line_height*$nectar_h3_phone ) . 'px!important'; ?>;
		}
	}

<?php } // attrs in use ?>


	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h4
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h4_font_style']);
	
	( intval( substr($options['h4_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h4_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
		
		if($options['h4_font_family']['attrs_in_use']) {
			
		echo 'body h4, .row .col h4, .portfolio-items .work-meta h4, .list-icon-holder[data-icon_type="numerical"] span, .portfolio-items .col.span_3 .work-meta h4, #respond h3, .blog-recent.related-posts h3.title, h3#comments, .portfolio-items[data-ps="6"] .work-meta h4,
		.nectar-hor-list-item[data-font-family="h4"], .toggles[data-style="minimal_small"] .toggle > h3 a, .woocommerce #reviews #reply-title, p.woocommerce.add_to_cart_inline > span.woocommerce-Price-amount, p.woocommerce.add_to_cart_inline ins > span.woocommerce-Price-amount,
		#header-outer .total, #header-outer .total strong
		{'; ?>	
			<?php if($options['h4_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['h4_font'])) ? '"'. $options['h4_font'] .'"' : $options['h4_font'];
			}
				  if($options['h4_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['h4_font_transform'] != '-') echo 'text-transform: ' . $options['h4_font_transform'] .';'; 
				  if($options['h4_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h4_font_spacing'] .';'; 
			      if($options['h4_font_size'] != '-') echo 'font-size:' . $options['h4_font_size'] .';'; ?>
		
			<?php 
			//user set line-height
			 if($options['h4_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['h4_font_line_height'] .';'; 
			 	$the_line_height = $options['h4_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>
		
		@media only screen and (min-width: 690px) {
			.portfolio-items[data-ps="6"] .wide_tall .work-meta h4 {
				font-size: <?php if(!empty($options['h4_font_size']) && $options['h4_font_size'] != '-') echo intval($options['h4_font_size'])*1.7 . 'px!important' ?>;
				line-height: <?php if(!empty($options['h4_font_size']) && $options['h4_font_size'] != '-') echo (intval($options['h4_font_size'])*1.7) +8 . 'px!important' ?>;
			}

			.nectar-slide-in-cart .widget_shopping_cart .cart_list .mini_cart_item > a:not(.remove) {
				<?php if($options['h4_font'] != '-') echo 'font-family: ' . $font_family .'!important;'; 
				if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			    if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; } ?>
			}

		}


		<?php 
			$defined_font_size = (!empty($options['h4_font_size']) && $options['h4_font_size'] != '-') ? intval($options['h4_font_size']) : $nectar_h4_default_size;
			$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h4_default_size + 10;
		?>

		@media only screen and (max-width: 1300px) and (min-width: 1000px) {
			.row .col h4, body h4 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h4_small_desktop ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h4_small_desktop ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 1000px) and (min-width: 690px) {
			.row .col h4, body h4 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h4_tablet ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h4_tablet ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 690px) {
			.row .col h4, body h4 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h4_phone ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h4_phone ) . 'px'; ?>;
			}
		}
		
	<?php } // attrs in use ?>
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h5
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h5_font_style']);
	
	( intval( substr($options['h5_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h5_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['h5_font_family']['attrs_in_use']) {
		
		echo 'body h5, .row .col h5, .portfolio-items .work-item.style-3-alt p, .nectar-hor-list-item[data-font-family="h5"]
		{'; ?>	
			<?php if($options['h5_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['h5_font'])) ? '"'. $options['h5_font'] .'"' : $options['h5_font'];
			}
				  if($options['h5_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['h5_font_transform'] != '-') echo 'text-transform: ' . $options['h5_font_transform'] .';'; 
				  if($options['h5_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h5_font_spacing'] .';'; 
			      if($options['h5_font_size'] != '-') echo 'font-size:' . $options['h5_font_size'] .';'; ?>
		
			<?php 
			//user set line-height
			 if($options['h5_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['h5_font_line_height'] .';'; 
			 	$the_line_height = $options['h5_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
				  if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>



		body .wpb_column > .wpb_wrapper > .morphing-outline .inner > h5 {
			font-size: <?php if(!empty($options['h5_font_size']) && $options['h5_font_size'] != '-') echo ceil(intval($options['h5_font_size'])*1.35) . 'px!important' ?>;
		}
		

		<?php 
			$defined_font_size = (!empty($options['h5_font_size']) && $options['h5_font_size'] != '-') ? intval($options['h5_font_size']) : $nectar_h5_default_size;
			$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h5_default_size + 10;
		?>

		@media only screen and (max-width: 1300px) and (min-width: 1000px) {
			.row .col h5, body h5 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h5_small_desktop ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h5_small_desktop ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 1000px) and (min-width: 690px) {
			.row .col h5, body h5 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h5_tablet ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h5_tablet ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 690px) {
			.row .col h5, body h5 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h5_phone ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h5_phone ) . 'px'; ?>;
			}
		}
	
	<?php } // attrs in use ?>
	

	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Page Heading Font - h6
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['h6_font_style']);
	
	( intval( substr($options['h6_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['h6_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['h6_font_family']['attrs_in_use']) {
				
		echo 'body h6, .row .col h6, .nectar-hor-list-item[data-font-family="h6"]
		{'; ?>	
			<?php if($options['h6_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['h6_font'])) ? '"'. $options['h6_font'] .'"' : $options['h6_font'];
			}
				  if($options['h6_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['h6_font_transform'] != '-') echo 'text-transform: ' . $options['h6_font_transform'] .';'; 
				  if($options['h6_font_spacing'] != '-') echo 'letter-spacing: ' . $options['h6_font_spacing'] .';'; 
			      if($options['h6_font_size'] != '-') echo 'font-size:' . $options['h6_font_size'] .';'; ?>
		
			<?php 
			//user set line-height
			 if($options['h6_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['h6_font_line_height'] .';'; 
			 	$the_line_height = $options['h6_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
			      if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

		
		
		<?php 
			$defined_font_size = (!empty($options['h6_font_size']) && $options['h6_font_size'] != '-') ? intval($options['h6_font_size']) : $nectar_h6_default_size;
			$defined_line_height = (!empty($the_line_height)) ? intval($the_line_height) : $nectar_h6_default_size + 10;
		?>

		@media only screen and (max-width: 1300px) and (min-width: 1000px) {
			.row .col h6, body h6 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h6_small_desktop ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h6_small_desktop ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 1000px) and (min-width: 690px) {
			.row .col h6, body h6 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h6_tablet ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h6_tablet ) . 'px'; ?>;
			}
		}

		@media only screen and (max-width: 690px) {
			.row .col h6, body h6 {
				font-size: <?php echo esc_html( $defined_font_size*$nectar_h6_phone ) . 'px'; ?>;
				line-height: <?php echo esc_html( $defined_line_height*$nectar_h6_phone ) . 'px'; ?>;
			}
		}
	
	<?php } // attrs in use ?>	


	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Italic Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['i_font_style']);
	
	( intval( substr($options['i_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['i_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['i_font_family']['attrs_in_use']) {
		
		echo 'body i, body em, .masonry.meta_overlaid article.post .post-header .meta-author > span, .post-area.masonry.meta_overlaid article.post .post-meta .date,
		.post-area.masonry.meta_overlaid article.post.quote .quote-inner .author, .post-area.masonry.meta_overlaid  article.post.link .post-content .destination,
		body .testimonial_slider[data-style="minimal"] blockquote span.title
		{'; ?>	
			<?php if($options['i_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['i_font'])) ? '"'. $options['i_font'] .'"' : $options['i_font'];
			}
				  if($options['i_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['i_font_transform'] != '-') echo 'text-transform: ' . $options['i_font_transform'] .';'; 
				  if($options['i_font_spacing'] != '-') echo 'letter-spacing: ' . $options['i_font_spacing'] .';'; 
			      if($options['i_font_size'] != '-') echo 'font-size:' . $options['i_font_size'] .';'; ?>
		
			<?php 
			//user set line-height
			 if($options['i_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['i_font_line_height'] .';'; 
			 	$the_line_height = $options['i_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>
		
		<?php } // attrs in use ?>		
		
		
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Form Label Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['label_font_style']);
	
	( intval( substr($options['label_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['label_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['label_font_family']['attrs_in_use']) {
			
		echo 'form label, .woocommerce-checkout-review-order-table .product-info .amount, .woocommerce-checkout-review-order-table .product-info .product-quantity,
		.nectar-progress-bar p, .nectar-progress-bar span strong i, .nectar-progress-bar span strong, .testimonial_slider:not([data-style="minimal"]) blockquote span,  .woocommerce-ordering .select2-container--default .select2-selection--single .select2-selection__rendered, .woocommerce-ordering .select2-container .select2-choice>.select2-chosen,
		.tabbed[data-style="minimal_alt"] > ul li a, .material .widget .nectar_widget[class*="nectar_blog_posts_"] > li .post-title, body.material .tagcloud a, .material .widget li a, .nectar-recent-posts-slider_multiple_visible .recent-post-container.container .strong a,  .material .recentcomments .comment-author-link,
		.single .post-area .content-inner > .post-tags a,  .masonry.material .masonry-blog-item .grav-wrap a, .nectar-recent-posts-single_featured .grav-wrap a, .masonry.material .masonry-blog-item .meta-category a, .post-area.featured_img_left article .meta-category a, .post-area.featured_img_left article .grav-wrap .text a, .related-posts[data-style="material"] .meta-category a, 
		.masonry.auto_meta_overlaid_spaced article.post.quote .author, .masonry.material article.post.quote .author, body.search-results #search-results[data-layout="list-no-sidebar"] .result .inner-wrap h2 span,
		.material .tabbed >ul li a, .post-area.featured_img_left article.post.quote .author, .related-posts[data-style="material"] .grav-wrap .text a, .auto_meta_overlaid_spaced .masonry-blog-item .meta-category a, [data-style="list_featured_first_row"] .meta-category a, .nectar-recent-posts-single_featured .strong a, .nectar-recent-posts-single_featured.multiple_featured .controls li .title,
		body .woocommerce .nectar-woo-flickity[data-controls="arrows-and-text"] .woo-flickity-count, body.woocommerce ul.products li.minimal.product span.onsale, .nectar-woo-flickity ul.products li.minimal.product span.onsale, .nectar-quick-view-box span.onsale,  .nectar-quick-view-box .nectar-full-product-link a, body .nectar-quick-view-box .single_add_to_cart_button, .nectar-quick-view-box .single_add_to_cart_button, .woocommerce .cart .quantity input.qty, .woocommerce .cart .quantity input.plus, .woocommerce .cart .quantity input.minus, .pum-theme-salient-page-builder-optimized .pum-container .pum-content+.pum-close,
		.nectar-quick-view-box .cart .quantity input.qty, .nectar-quick-view-box .cart .quantity input.plus, .nectar-quick-view-box .cart .quantity input.minus, .woocommerce-account .woocommerce-form-login .lost_password, .woocommerce div.product .woocommerce-tabs .full-width-content[data-tab-style="fullwidth"] ul.tabs li a, .woocommerce div.product_meta,
		body.material .nectar_single_testimonial[data-style="basic"] span.wrap, body.material .nectar_single_testimonial[data-style="basic_left_image"] span.wrap, .woocommerce table.cart td.product-name, .woocommerce table.shop_table th, #header-outer .widget_shopping_cart .cart_list a, .woocommerce .yith-wcan-reset-navigation.button, .single-product .entry-summary p.stock.out-of-stock
		{'; ?>	
			<?php if($options['label_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['label_font'])) ? '"'. $options['label_font'] .'"' : $options['label_font'];
			}
				  if($options['label_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['label_font_transform'] != '-') echo 'text-transform: ' . $options['label_font_transform'] .';'; 
				  if($options['label_font_spacing'] != '-') echo 'letter-spacing: ' . $options['label_font_spacing'] .';'; 
			      if($options['label_font_size'] != '-') echo 'font-size:' . $options['label_font_size'] .'!important;'; ?>
		
			<?php 
			//user set line-height
			 if($options['label_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['label_font_line_height'] .';'; 
			 	$the_line_height = $options['label_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

	<?php } // attrs in use ?>	



	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Portfolio Filter Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['portfolio_filters_font_style']);
	
	( intval( substr($options['portfolio_filters_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['portfolio_filters_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['portfolio_filters_font_family']['attrs_in_use']) {
			
		echo '.portfolio-filters-inline .container > ul a, .portfolio-filters > ul a, .portfolio-filters > a span
		{'; ?>	
			<?php if($options['portfolio_filters_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['portfolio_filters_font'])) ? '"'. $options['portfolio_filters_font'] .'"' : $options['portfolio_filters_font'];
			}
				  if($options['portfolio_filters_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['portfolio_filters_font_transform'] != '-') echo 'text-transform: ' . $options['portfolio_filters_font_transform'] .';'; 
				  if($options['portfolio_filters_font_spacing'] != '-') echo 'letter-spacing: ' . $options['portfolio_filters_font_spacing'] .';'; 
			      if($options['portfolio_filters_font_size'] != '-') echo 'font-size:' . $options['portfolio_filters_font_size'] .'!important;'; ?>
		
			<?php 
			//user set line-height
			 if($options['portfolio_filters_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['portfolio_filters_font_line_height'] .';'; 
			 	$the_line_height = $options['portfolio_filters_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

		<?php if($the_line_height !== null) echo '.portfolio-filters-inline #current-category { line-height: '.$the_line_height.'; }'; ?>

	<?php } // attrs in use ?>	
	

	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Portfolio Captions/Excerpts Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['portfolio_caption_font_style']);
	
	( intval( substr($options['portfolio_caption_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['portfolio_caption_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['portfolio_caption_font_family']['attrs_in_use']) {
			
		echo '.portfolio-items .col p, .container-wrap[data-nav-pos="after_project_2"] .bottom_controls li span:not(.text)
		{'; ?>	
			<?php if($options['portfolio_caption_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['portfolio_caption_font'])) ? '"'. $options['portfolio_caption_font'] .'"' : $options['portfolio_caption_font'];
			}
				  if($options['portfolio_caption_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['portfolio_caption_font_transform'] != '-') echo 'text-transform: ' . $options['portfolio_caption_font_transform'] .';'; 
				  if($options['portfolio_caption_font_spacing'] != '-') echo 'letter-spacing: ' . $options['portfolio_caption_font_spacing'] .';'; 
			      if($options['portfolio_caption_font_size'] != '-') echo 'font-size:' . $options['portfolio_caption_font_size'] .'!important;'; ?>
		
			<?php 
			//user set line-height
			 if($options['portfolio_caption_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['portfolio_caption_font_line_height'] .';'; 
			 	$the_line_height = $options['portfolio_caption_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

   <?php } // attrs in use ?>	

	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Dropcap Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['nectar_dropcap_font_style']);
	
	( intval( substr($options['nectar_dropcap_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['nectar_dropcap_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['nectar_dropcap_font_family']['attrs_in_use']) {
		
		echo '.nectar-dropcap
		{'; ?>	
			<?php if($options['nectar_dropcap_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['nectar_dropcap_font'])) ? '"'. $options['nectar_dropcap_font'] .'"' : $options['nectar_dropcap_font'];
			}
				  if($options['nectar_dropcap_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['nectar_dropcap_font_transform'] != '-') echo 'text-transform: ' . $options['nectar_dropcap_font_transform'] .';'; 
				  if($options['nectar_dropcap_font_spacing'] != '-') echo 'letter-spacing: ' . $options['nectar_dropcap_font_spacing'] .';'; 
			      if($options['nectar_dropcap_font_size'] != '-') echo 'font-size:' . $options['nectar_dropcap_font_size'] .'!important;'; ?>
		
			<?php 
			//user set line-height
			 if($options['nectar_dropcap_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['nectar_dropcap_font_line_height'] .';'; 
			 	$the_line_height = $options['nectar_dropcap_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

  <?php } // attrs in use ?>	

	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Sidebar/Footer Header Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['nectar_sidebar_footer_headers_font_style']);
	
	( intval( substr($options['nectar_sidebar_footer_headers_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['nectar_sidebar_footer_headers_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['nectar_sidebar_footer_headers_font_family']['attrs_in_use']) {
		
		echo 'body #sidebar h4, body .widget h4, body #footer-outer .widget h4
		{'; ?>	
			<?php if($options['nectar_sidebar_footer_headers_font'] != '-') {
				  $font_family = (1 === preg_match('~[0-9]~', $options['nectar_sidebar_footer_headers_font'])) ? '"'. $options['nectar_sidebar_footer_headers_font'] .'"' : $options['nectar_sidebar_footer_headers_font'];
			}
				  if($options['nectar_sidebar_footer_headers_font'] != '-') echo 'font-family: ' . $font_family .';'; 
				  if($options['nectar_sidebar_footer_headers_font_transform'] != '-') echo 'text-transform: ' . $options['nectar_sidebar_footer_headers_font_transform'] .'!important;';  
				  if($options['nectar_sidebar_footer_headers_font_spacing'] != '-') echo 'letter-spacing: ' . $options['nectar_sidebar_footer_headers_font_spacing'] .';'; 
			      if($options['nectar_sidebar_footer_headers_font_size'] != '-') echo 'font-size:' . $options['nectar_sidebar_footer_headers_font_size'] .'!important;'; ?>
		
			<?php 
			//user set line-height
			 if($options['nectar_sidebar_footer_headers_font_line_height'] != '-') { 
			 	echo 'line-height:' . $options['nectar_sidebar_footer_headers_font_line_height'] .';'; 
			 	$the_line_height = $options['nectar_sidebar_footer_headers_font_line_height'];
			 } else if(!empty($line_height)) {
			//auto line-height
				echo 'line-height:' . $line_height .';';
				$the_line_height = $line_height;
			} else {
				$the_line_height = null;
			}
			?>
			<?php 
	              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
			      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .'!important;'; }
				  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
				  	  $the_weight = explode("i",$styles[0]);
				  	  echo 'font-weight:' .  $the_weight[0] .';'; 
				  	  echo 'font-style: italic';
				  }
				  else if(!empty($styles[0])) {
				  	  if(strpos($styles[0],'italic') !== false) {
				  	    echo 'font-weight: 400;'; 
				  	    echo 'font-style: italic';
				  	 }
				  }
			?>
			<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
		<?php echo '}'; ?>

	<?php } // attrs in use ?>	
	
	
	<?php 

	/*-------------------------------------------------------------------------*/
	/*	Page Header Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['page_heading_font_style']);
	
	( intval( substr($options['page_heading_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['page_heading_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['page_heading_font_family']['attrs_in_use']) {
				
			echo 'body #page-header-bg h1, html body .row .col.section-title h1, .nectar-box-roll .overlaid-content h1
			{'; ?>	
				<?php if($options['page_heading_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['page_heading_font'])) ? '"'. $options['page_heading_font'] .'"' : $options['page_heading_font'];
				}
					  if($options['page_heading_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['page_heading_font_transform'] != '-') echo 'text-transform: ' . $options['page_heading_font_transform'] .';'; 
					  if($options['page_heading_font_spacing'] != '-') echo 'letter-spacing: ' . $options['page_heading_font_spacing'] .';'; 
					  if($options['page_heading_font_size'] != '-') echo 'font-size:' . $options['page_heading_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['page_heading_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['page_heading_font_line_height'] .';'; 
				 	$the_line_height = $options['page_heading_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
				if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>

			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				#page-header-bg .span_6 h1, .overlaid-content h1 {
					font-size: <?php if(!empty($options['page_heading_font_size']) && $options['page_heading_font_size'] != '-') echo intval($options['page_heading_font_size'])*0.7 . 'px!important' ?>;
					line-height: <?php if(!empty($options['page_heading_font_size']) && $options['page_heading_font_size'] != '-') echo (intval($options['page_heading_font_size'])*0.7) +4 . 'px!important' ?>;
				}
			}

			@media only screen and (min-width: 1000px) and (max-width: 1300px) {
				#page-header-bg .span_6 h1, .nectar-box-roll .overlaid-content h1 {
					font-size: <?php if(!empty($options['page_heading_font_size']) && $options['page_heading_font_size'] != '-') echo intval($options['page_heading_font_size'])*0.85 . 'px' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.85) . 'px' ?>;
				}
			}

			@media only screen and (min-width: 1300px) and (max-width: 1500px) {
				#page-header-bg .span_6 h1, .nectar-box-roll .overlaid-content h1 {
					font-size: <?php if(!empty($options['page_heading_font_size']) && $options['page_heading_font_size'] != '-') echo intval($options['page_heading_font_size'])*0.9 . 'px' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.9) . 'px' ?>;
				}
			}

			@media only screen and (max-width: 690px) {
				#page-header-bg.fullscreen-header .span_6 h1, .overlaid-content h1 {
					font-size: <?php if(!empty($options['page_heading_font_size']) && $options['page_heading_font_size'] != '-') echo intval($options['page_heading_font_size'])*0.45 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.45) . 'px!important' ?>;
				}
			}
		
	<?php } // attrs in use ?>		

	<?php
	/*-------------------------------------------------------------------------*/
	/*	Page Header Subtitle Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['page_heading_subtitle_font_style']);
	
	( intval( substr($options['page_heading_subtitle_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['page_heading_subtitle_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['page_heading_subtitle_font_family']['attrs_in_use']) {
		
			echo 'body #page-header-bg .span_6 span.subheader, #page-header-bg span.result-num,  body .row .col.section-title > span, .nectar-box-roll .overlaid-content .subheader
			{'; ?>	
				<?php if($options['page_heading_subtitle_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['page_heading_subtitle_font'])) ? '"'. $options['page_heading_subtitle_font'] .'"' : $options['page_heading_subtitle_font'];
				}
					  if($options['page_heading_subtitle_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['page_heading_subtitle_font_transform'] != '-') echo 'text-transform: ' . $options['page_heading_subtitle_font_transform'] .';'; 
					  if($options['page_heading_subtitle_font_spacing'] != '-') echo 'letter-spacing: ' . $options['page_heading_subtitle_font_spacing'] .';'; 
					  if($options['page_heading_subtitle_font_size'] != '-') echo 'font-size:' . $options['page_heading_subtitle_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['page_heading_subtitle_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['page_heading_subtitle_font_line_height'] .';'; 
				 	$the_line_height = $options['page_heading_subtitle_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
		              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			
			@media only screen and (min-width: 1000px) and (max-width: 1300px) {
				body #page-header-bg:not(.fullscreen-header) .span_6 span.subheader,  body .row .col.section-title > span {
					font-size: <?php if(!empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] != '-') echo intval($options['page_heading_subtitle_font_size'])*0.8 . 'px' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.8) . 'px' ?>;
				}
			}

			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				body #page-header-bg.fullscreen-header .span_6 span.subheader, .overlaid-content .subheader {
					font-size: <?php if(!empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] != '-') echo intval($options['page_heading_subtitle_font_size'])*0.9 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.9) . 'px!important' ?>;
				}

				<?php if(!empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] != '-' && $options['page_heading_subtitle_font_size'] > 22) { ?>
					#page-header-bg .span_6 span.subheader {
				  		font-size: 22px!important;
				  	} 
			  	<?php } else if( empty($options['page_heading_subtitle_font_size']) || !empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] == '-' ) { ?>
				  	#page-header-bg .span_6 span.subheader {
				  		font-size: 22px!important;
				  	} 
				 <?php } ?>
			}

			@media only screen and (max-width: 690px) {
				body #page-header-bg.fullscreen-header .span_6 span.subheader, .overlaid-content .subheader {
					font-size: <?php if(!empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] != '-') echo intval($options['page_heading_subtitle_font_size'])*0.7 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.7) . 'px!important' ?>;
				}

				<?php if(!empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] != '-' && $options['page_heading_subtitle_font_size'] > 15) { ?>
					#page-header-bg .span_6 span.subheader {
				  		font-size: 15px!important;
				  	}
			  	<?php } else if( empty($options['page_heading_subtitle_font_size']) || !empty($options['page_heading_subtitle_font_size']) && $options['page_heading_subtitle_font_size'] == '-' ) { ?>
			  		#page-header-bg .span_6 span.subheader {
				  		font-size: 15px!important;
				  	} 
				 <?php } ?>
			}

  <?php } // attrs in use ?>	

	<?php
	/*-------------------------------------------------------------------------*/
	/*	Off Canvas Navigation Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['off_canvas_nav_font_style']);
	
	( intval( substr($options['off_canvas_nav_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['off_canvas_nav_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['off_canvas_nav_font_family']['attrs_in_use']) {
				
			echo 'body #slide-out-widget-area .inner .off-canvas-menu-container li a, body #slide-out-widget-area.fullscreen .inner .off-canvas-menu-container li a,
			body #slide-out-widget-area.fullscreen-alt .inner .off-canvas-menu-container li a, body #slide-out-widget-area.slide-out-from-right-hover .inner .off-canvas-menu-container li a, body #nectar-ocm-ht-line-check 
			{'; ?>	
				<?php if($options['off_canvas_nav_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['off_canvas_nav_font'])) ? '"'. $options['off_canvas_nav_font'] .'"' : $options['off_canvas_nav_font'];
				}
					  if($options['off_canvas_nav_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['off_canvas_nav_font_transform'] != '-') echo 'text-transform: ' . $options['off_canvas_nav_font_transform'] .';'; 
					  if($options['off_canvas_nav_font_spacing'] != '-') echo 'letter-spacing: ' . $options['off_canvas_nav_font_spacing'] .';'; 
					  if($options['off_canvas_nav_font_size'] != '-') echo 'font-size:' . $options['off_canvas_nav_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['off_canvas_nav_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['off_canvas_nav_font_line_height'] .';'; 
				 	$the_line_height = $options['off_canvas_nav_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
		              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>


			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				body #slide-out-widget-area.fullscreen .inner .off-canvas-menu-container li a,
				body #slide-out-widget-area.fullscreen-alt .inner .off-canvas-menu-container li a  {
					font-size: <?php if(!empty($options['off_canvas_nav_font_size']) && $options['off_canvas_nav_font_size'] != '-') echo intval($options['off_canvas_nav_font_size'])*0.9 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.9) . 'px!important' ?>;
				}
			}

			@media only screen and (max-width: 690px) {
				body #slide-out-widget-area.fullscreen .inner .off-canvas-menu-container li a,
				body #slide-out-widget-area.fullscreen-alt .inner .off-canvas-menu-container li a {
					font-size: <?php if(!empty($options['off_canvas_nav_font_size']) && $options['off_canvas_nav_font_size'] != '-') echo intval($options['off_canvas_nav_font_size'])*0.7 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.7) . 'px!important' ?>;
				}
			}

			<?php 
				if(!empty($options['off_canvas_nav_font_size']) && $options['off_canvas_nav_font_size'] != '-' && intval($options['off_canvas_nav_font_size']) < 25) {
					echo 'body.material #slide-out-widget-area.slide-out-from-right  .off-canvas-menu-container li li a,
					#slide-out-widget-area[data-dropdown-func="separate-dropdown-parent-link"]  .off-canvas-menu-container li li a { font-size: '. $options['off_canvas_nav_font_size']*0.7 .'px; line-height: '. $options['off_canvas_nav_font_size']*0.7 .'px; }';
				}
			?>

  <?php } // attrs in use ?>	
			

	<?php
	/*-------------------------------------------------------------------------*/
	/*	Off Canvas Navigation Font Subtext
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['off_canvas_nav_subtext_font_style']);
	
	( intval( substr($options['off_canvas_nav_subtext_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['off_canvas_nav_subtext_font_size'],0,-2)) +10 .'px' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['off_canvas_nav_subtext_font_family']['attrs_in_use']) {
		
			echo '#slide-out-widget-area .menuwrapper li small
			{'; ?>	
				<?php if($options['off_canvas_nav_subtext_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['off_canvas_nav_subtext_font'])) ? '"'. $options['off_canvas_nav_subtext_font'] .'"' : $options['off_canvas_nav_subtext_font'];
				}
					  if($options['off_canvas_nav_subtext_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['off_canvas_nav_subtext_font_transform'] != '-') echo 'text-transform: ' . $options['off_canvas_nav_subtext_font_transform'] .';'; 
					  if($options['off_canvas_nav_subtext_font_spacing'] != '-') echo 'letter-spacing: ' . $options['off_canvas_nav_subtext_font_spacing'] .';'; 
					  if($options['off_canvas_nav_subtext_font_size'] != '-') echo 'font-size:' . $options['off_canvas_nav_subtext_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['off_canvas_nav_subtext_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['off_canvas_nav_subtext_font_line_height'] .';'; 
				 	$the_line_height = $options['off_canvas_nav_subtext_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
		              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>


			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				#slide-out-widget-area .menuwrapper li small {
					font-size: <?php if(!empty($options['off_canvas_nav_subtext_font_size']) && $options['off_canvas_nav_subtext_font_size'] != '-') echo esc_html( $options['off_canvas_nav_subtext_font_size']*0.9 ) . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo esc_html( $the_line_height*0.9 ) . 'px!important' ?>;
				}
			}

			@media only screen and (max-width: 690px) {
				#slide-out-widget-area .menuwrapper li small {
					font-size: <?php if(!empty($options['off_canvas_nav_subtext_font_size']) && $options['off_canvas_nav_subtext_font_size'] != '-') echo esc_html( $options['off_canvas_nav_subtext_font_size']*0.7 ) . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo esc_html( $the_line_height*0.7 ) . 'px!important' ?>;
				}
			}
			
	<?php } // attrs in use ?>	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Nectar Slider Heading Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['nectar_slider_heading_font_style']);
	( intval( substr($options['nectar_slider_heading_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['nectar_slider_heading_font_size'],0,-2)) + 19 .'px!important' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['nectar_slider_heading_font_family']['attrs_in_use']) {
	
			echo '.swiper-slide .content h2
			{'; ?>
				<?php if($options['nectar_slider_heading_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['nectar_slider_heading_font'])) ? '"'. $options['nectar_slider_heading_font'] .'"' : $options['nectar_slider_heading_font'];	
			     }  
					  if($options['nectar_slider_heading_font'] != '-') echo 'font-family: ' . $font_family .';';
					  if($options['nectar_slider_heading_font_transform'] != '-') echo 'text-transform: ' . $options['nectar_slider_heading_font_transform'] .';';  
					  if($options['nectar_slider_heading_font_spacing'] != '-') echo 'letter-spacing: ' . $options['nectar_slider_heading_font_spacing'] .';'; 
					  if($options['nectar_slider_heading_font_size'] != '-') echo 'font-size:' . $options['nectar_slider_heading_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['nectar_slider_heading_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['nectar_slider_heading_font_line_height'] .';'; 
				 	$the_line_height = $options['nectar_slider_heading_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>

				<?php 

		             if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				     if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>

			@media only screen and (min-width: 1000px) and (max-width: 1300px) {
				body .nectar-slider-wrap[data-full-width="true"] .swiper-slide .content h2, 
				body .nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content h2, 
				body .full-width-content .vc_span12 .swiper-slide .content h2 {
					font-size: <?php if(!empty($options['nectar_slider_heading_font_size']) && $options['nectar_slider_heading_font_size'] != '-') echo intval($options['nectar_slider_heading_font_size'])*0.8 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.8) . 'px!important' ?>;
				}
			}

			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				body .nectar-slider-wrap[data-full-width="true"] .swiper-slide .content h2, 
				body .nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content h2, 
				body .full-width-content .vc_span12 .swiper-slide .content h2 {
					font-size: <?php if(!empty($options['nectar_slider_heading_font_size']) && $options['nectar_slider_heading_font_size'] != '-') echo intval($options['nectar_slider_heading_font_size'])*0.6 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.6) . 'px!important' ?>;
				}
			}

			@media only screen and (max-width: 690px) {
				body .nectar-slider-wrap[data-full-width="true"] .swiper-slide .content h2, 
				body .nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content h2, 
				body .full-width-content .vc_span12 .swiper-slide .content h2 {
					font-size: <?php if(!empty($options['nectar_slider_heading_font_size']) && $options['nectar_slider_heading_font_size'] != '-') echo intval($options['nectar_slider_heading_font_size'])*0.5 . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo (intval($the_line_height)*0.5) . 'px!important' ?>;
				}
			}
	
	<?php } // attrs in use ?>	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Nectar/Home Slider Caption 
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['home_slider_caption_font_style']);
	( intval( substr($options['home_slider_caption_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['home_slider_caption_font_size'],0,-2)) + 19 .'px!important' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['home_slider_caption_font_family']['attrs_in_use']) {
	
			echo '#featured article .post-title h2 span, .swiper-slide .content p, #portfolio-filters-inline #current-category, body .vc_text_separator div
			{'; ?>	
				<?php if($options['home_slider_caption_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['home_slider_caption_font'])) ? '"'. $options['home_slider_caption_font'] .'"' : $options['home_slider_caption_font'];	
				}  
					  if($options['home_slider_caption_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['home_slider_caption_font_transform'] != '-') echo 'text-transform: ' . $options['home_slider_caption_font_transform'] .';';  
					  if($options['home_slider_caption_font_spacing'] != '-') echo 'letter-spacing: ' . $options['home_slider_caption_font_spacing'] .';';  
				      if($options['home_slider_caption_font_size'] != '-') echo 'font-size:' . $options['home_slider_caption_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['home_slider_caption_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['home_slider_caption_font_line_height'] .';'; 
				 	$the_line_height = $options['home_slider_caption_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
		              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			
			
			<?php 
				  echo '#portfolio-filters-inline ul { line-height: '.$line_height.'; }';
				  echo '.swiper-slide .content p.transparent-bg span { '; $nectar_slider_line_height_2 = intval(substr($options["home_slider_caption_font_size"],0,-2)) + 25; ?>
			     <?php if(!empty($line_height)) echo 'line-height:' . $nectar_slider_line_height_2 .'px;'; ?>
			<?php echo '}'; ?>

			@media only screen and (min-width: 1000px) and (max-width: 1300px) {
				.nectar-slider-wrap[data-full-width="true"] .swiper-slide .content p, 
				.nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content p, 
				.full-width-content .vc_span12 .swiper-slide .content p {
					font-size: <?php if(!empty($options['home_slider_caption_font_size']) && $options['home_slider_caption_font_size'] != '-') echo esc_html( $options['home_slider_caption_font_size']*0.8 ) . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo esc_html( $the_line_height*0.8 ) . 'px!important' ?>;
				}
			}

			@media only screen and (min-width: 690px) and (max-width: 1000px) {
				.nectar-slider-wrap[data-full-width="true"] .swiper-slide .content p, 
				.nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content p, 
				.full-width-content .vc_span12 .swiper-slide .content p {
					font-size: <?php if(!empty($options['home_slider_caption_font_size']) && $options['home_slider_caption_font_size'] != '-') echo esc_html( $options['home_slider_caption_font_size']*0.7 ) . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo esc_html( $the_line_height*0.7 ) . 'px!important' ?>;
				}
			}

			@media only screen and (max-width: 690px) {
				body .nectar-slider-wrap[data-full-width="true"] .swiper-slide .content p, 
				body .nectar-slider-wrap[data-full-width="boxed-full-width"] .swiper-slide .content p, 
				body .full-width-content .vc_span12 .swiper-slide .content p {
					font-size: <?php if(!empty($options['home_slider_caption_font_size']) && $options['home_slider_caption_font_size'] != '-') echo esc_html( $options['home_slider_caption_font_size']*0.7 ) . 'px!important' ?>;
					line-height: <?php if($the_line_height) echo esc_html( $the_line_height*0.7 ) . 'px!important' ?>;
				}
			}

	<?php } // attrs in use ?>	

	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Testimonial Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['testimonial_font_style']);
	( intval( substr($options['testimonial_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['testimonial_font_size'],0,-2)) + 19 .'px!important' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['testimonial_font_family']['attrs_in_use']) {
		
			echo '.testimonial_slider blockquote, .testimonial_slider blockquote span, .testimonial_slider[data-style="minimal"] blockquote span:not(.title), .testimonial_slider[data-style="minimal"] blockquote,  blockquote, .testimonial_slider[data-style="minimal"] .controls
			{'; ?>	
				<?php if($options['testimonial_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['testimonial_font'])) ? '"'. $options['testimonial_font'] .'"' : $options['testimonial_font'];	
				}  
					  if($options['testimonial_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['testimonial_font_transform'] != '-') echo 'text-transform: ' . $options['testimonial_font_transform'] .';';  
					  if($options['testimonial_font_spacing'] != '-') echo 'letter-spacing: ' . $options['testimonial_font_spacing'] .';';  
				      if($options['testimonial_font_size'] != '-') echo 'font-size:' . $options['testimonial_font_size'] .';'; ?>
			
				<?php 
				//user set line-height
				 if($options['testimonial_font_line_height'] != '-') { 
				 	echo 'line-height:' . $options['testimonial_font_line_height'] .';'; 
				 	$the_line_height = $options['testimonial_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
		              if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				      if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  }
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			
			
	<?php } // attrs in use ?>	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Woo Product Title
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['nectar_woo_shop_product_title_font_style']);
	( intval( substr($options['nectar_woo_shop_product_title_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['nectar_woo_shop_product_title_font_size'],0,-2)) + 10 .'px!important' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['nectar_woo_shop_product_title_font_family']['attrs_in_use']) {
		
			echo '.woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3, .woocommerce ul.products li.product h2,
			 .woocommerce ul.products li.product h2, .woocommerce-page ul.products li.product h2
			{'; ?>	
				<?php if($options['nectar_woo_shop_product_title_font'] != '-') {
						$font_family = (1 === preg_match('~[0-9]~', $options['nectar_woo_shop_product_title_font'])) ? '"'. $options['nectar_woo_shop_product_title_font'] .'"' : $options['nectar_woo_shop_product_title_font'];	
				}  
						if($options['nectar_woo_shop_product_title_font'] != '-') echo 'font-family: ' . $font_family .';'; 
						if($options['nectar_woo_shop_product_title_font_transform'] != '-') echo 'text-transform: ' . $options['nectar_woo_shop_product_title_font_transform'] .';';  
						if($options['nectar_woo_shop_product_title_font_spacing'] != '-') echo 'letter-spacing: ' . $options['nectar_woo_shop_product_title_font_spacing'] .';';  
						if($options['nectar_woo_shop_product_title_font_size'] != '-') echo 'font-size:' . $options['nectar_woo_shop_product_title_font_size'] .'!important;'; ?>
			
				<?php 
				//user set line-height
				 if($options['nectar_woo_shop_product_title_font_line_height'] != '-') { 
					echo 'line-height:' . $options['nectar_woo_shop_product_title_font_line_height'] .'!important;'; 
					$the_line_height = $options['nectar_woo_shop_product_title_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
									if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
							if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
						else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
								$the_weight = explode("i",$styles[0]);
								echo 'font-weight:' .  $the_weight[0] .';'; 
								echo 'font-style: italic';
						}
						else if(!empty($styles[0])) {
								if(strpos($styles[0],'italic') !== false) {
									echo 'font-weight: 400;'; 
									echo 'font-style: italic';
							 }
						}
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			
	<?php } // attrs in use ?>	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Woo Product Secondary
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['nectar_woo_shop_product_secondary_font_style']);
	( intval( substr($options['nectar_woo_shop_product_secondary_font_size'],0,-2) ) > 8 ) ? $line_height =  intval(substr($options['nectar_woo_shop_product_secondary_font_size'],0,-2)) + 10 .'px!important' : $line_height = null ;  ?>
	
	<?php 
	
	if($options['nectar_woo_shop_product_secondary_font_family']['attrs_in_use']) {
				
			echo '.woocommerce .material.product .product-wrap .product-add-to-cart .price .amount, .woocommerce .material.product .product-wrap .product-add-to-cart a,
			            .woocommerce .material.product .product-wrap .product-add-to-cart a > span, .woocommerce .material.product .product-wrap .product-add-to-cart a.added_to_cart,
									html .woocommerce ul.products li.product.material .price, .woocommerce ul.products li.product.material .price ins, .woocommerce ul.products li.product.material .price ins .amount,
									.woocommerce-page ul.products li.product.material .price ins span, .material.product .product-wrap .product-add-to-cart a span, html .woocommerce ul.products .text_on_hover.product .add_to_cart_button,
									.woocommerce ul.products li.product .price, .woocommerce ul.products li.product .price ins, .woocommerce ul.products li.product .price ins .amount, html .woocommerce .material.product .product-wrap .product-add-to-cart a.added_to_cart,
									.text_on_hover.product a.added_to_cart, .products li.product.minimal .product-meta .price, .products li.product.minimal .product-meta .amount
			{'; ?>	
				<?php if($options['nectar_woo_shop_product_secondary_font'] != '-') {
						$font_family = (1 === preg_match('~[0-9]~', $options['nectar_woo_shop_product_secondary_font'])) ? '"'. $options['nectar_woo_shop_product_secondary_font'] .'"' : $options['nectar_woo_shop_product_secondary_font'];	
				}  
						if($options['nectar_woo_shop_product_secondary_font'] != '-') echo 'font-family: ' . $font_family .';'; 
						if($options['nectar_woo_shop_product_secondary_font_transform'] != '-') echo 'text-transform: ' . $options['nectar_woo_shop_product_secondary_font_transform'] .'!important;';  
						if($options['nectar_woo_shop_product_secondary_font_spacing'] != '-') echo 'letter-spacing: ' . $options['nectar_woo_shop_product_secondary_font_spacing'] .';';  
						if($options['nectar_woo_shop_product_secondary_font_size'] != '-') echo 'font-size:' . $options['nectar_woo_shop_product_secondary_font_size'] .'!important;'; ?>
			
				<?php 
				//user set line-height
				 if($options['nectar_woo_shop_product_secondary_font_line_height'] != '-') { 
					echo 'line-height:' . $options['nectar_woo_shop_product_secondary_font_line_height'] .';'; 
					$the_line_height = $options['nectar_woo_shop_product_secondary_font_line_height'];
				 } else if(!empty($line_height)) {
				//auto line-height
					echo 'line-height:' . $line_height .';';
					$the_line_height = $line_height;
				} else {
					$the_line_height = null;
				}
				?>
				<?php 
									if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
							if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
						else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
								$the_weight = explode("i",$styles[0]);
								echo 'font-weight:' .  $the_weight[0] .';'; 
								echo 'font-style: italic';
						}
						else if(!empty($styles[0])) {
								if(strpos($styles[0],'italic') !== false) {
									echo 'font-weight: 400;'; 
									echo 'font-style: italic';
							 }
						}
				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>

		<?php } // attrs in use ?>	
	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Sidear, Carousel & Nectar Button Header Font
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['sidebar_footer_h_font_style']);
	$line_height =  substr($options['sidebar_footer_h_font_size'],0,-2); ?>
	
	<?php 
	
	if($options['sidebar_footer_h_font_family']['attrs_in_use']) {
				
			echo '#footer-outer .widget h4, #sidebar h4, #call-to-action .container a, .uppercase, .nectar-button, .nectar-button.medium, .nectar-button.small, .nectar-3d-transparent-button, body .widget_calendar table th, body #footer-outer #footer-widgets .col .widget_calendar table th, .swiper-slide .button a,
			body:not([data-header-format="left-header"]) header#top nav > ul > li.megamenu > ul > li > a, .carousel-heading h2, body .gform_wrapper .top_label .gfield_label, body .vc_pie_chart .wpb_pie_chart_heading, #infscr-loading div, #page-header-bg .author-section a, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .ascend input[type="submit"], .ascend button[type="submit"],
			.widget h4, .text-on-hover-wrap .categories a, .text_on_hover.product .add_to_cart_button, .woocommerce-page .single_add_to_cart_button, .woocommerce div[data-project-style="text_on_hover"]  .cart .quantity input.qty, .woocommerce-page #respond input#submit,
			.meta_overlaid article.post .post-header h2, .meta_overlaid article.post.quote .post-content h2, .meta_overlaid article.post.link .post-content h2, .meta_overlaid article.post.format-status .post-content h2, .meta_overlaid article .meta-author a, .pricing-column.highlight h3 .highlight-reason,
			.blog-recent[data-style="minimal"] .col > span, body .masonry.classic_enhanced .posts-container article .meta-category a,  body .masonry.classic_enhanced .posts-container article.wide_tall .meta-category a, .blog-recent[data-style*="classic_enhanced"] .meta-category a, .nectar-recent-posts-slider .container .strong,  body.material #page-header-bg.fullscreen-header .inner-wrap >a, #page-header-bg[data-post-hs="default_minimal"] .inner-wrap > a, .single .heading-title[data-header-style="default_minimal"] .meta-category a, .nectar-fancy-box .link-text, .woocommerce-account .woocommerce-form-login button.button, .woocommerce-account .woocommerce-form-register  button.button,
			 .post-area.standard-minimal article.post .post-meta .date a, .post-area.standard-minimal article.post .more-link span, .nectar-slide-in-cart .widget_shopping_cart .buttons a, .material.product .product-wrap .product-add-to-cart a .price .amount,  .material.product .product-wrap .product-add-to-cart a span, ul.products li.material.product  span.onsale,
			body[data-button-style="rounded"] #pagination > a, html body #pagination > span, .woocommerce nav.woocommerce-pagination ul li a, html body nav.woocommerce-pagination ul li a, html body nav.woocommerce-pagination ul li span, .woocommerce .material.product .product-wrap .product-add-to-cart a.added_to_cart,
			.woocommerce-page ul.products li.product.material .price, .woocommerce-page ul.products li.product.material .price ins span, body[data-form-submit="see-through-2"] input[type=submit], body[data-form-submit="see-through-2"] button[type=submit], body[data-form-submit="see-through"] input[type=submit], body[data-form-submit="see-through"] button[type=submit], 
			 body[data-form-submit="regular"] input[type=submit] body[data-form-submit="regular"] button[type=submit], .nectar_team_member_overlay .team_member_details .title, body:not([data-header-format="left-header"]) header#top nav > ul > li.megamenu > ul > li > ul > li.has-ul > a, .nectar_fullscreen_zoom_recent_projects .project-slide .project-info .normal-container > a,
			 .nectar-hor-list-item .nectar-list-item-btn, .nectar-category-grid-item .content span.subtext, body .woocommerce .nectar-woo-flickity[data-controls="arrows-and-text"] .nectar-woo-carousel-top, .products li.product.minimal .product-add-to-cart a, .woocommerce div.product form.cart .button, .nectar-quick-view-box .nectar-full-product-link, .woocommerce-page .nectar-quick-view-box  button[type="submit"].single_add_to_cart_button, #header-outer .widget_shopping_cart a.button,
			 .woocommerce .classic .product-wrap .product-add-to-cart .add_to_cart_button, .text_on_hover.product .nectar_quick_view, .woocommerce .classic .product-wrap .product-add-to-cart .button.product_type_variable, .woocommerce.add_to_cart_inline a.button.add_to_cart_button, .woocommerce .classic .product-wrap .product-add-to-cart .button.product_type_grouped, .woocommerce-page .woocommerce p.return-to-shop a.wc-backward, .yikes-easy-mc-form .yikes-easy-mc-submit-button
			{'; ?>	
				<?php if($options['sidebar_footer_h_font'] != '-') {
					   $font_family = (1 === preg_match('~[0-9]~', $options['sidebar_footer_h_font'])) ? '"'. $options['sidebar_footer_h_font'] .'"' : $options['sidebar_footer_h_font'];
				}
					  if($options['sidebar_footer_h_font'] != '-') echo 'font-family: ' . $font_family .';';
					  if($options['sidebar_footer_h_font_transform'] != '-') echo 'text-transform: ' . $options['sidebar_footer_h_font_transform'] .'!important;';  
					  if($options['sidebar_footer_h_font_spacing'] != '-') echo 'letter-spacing: ' . $options['sidebar_footer_h_font_spacing'] .';';  
				      if($options['sidebar_footer_h_font_size'] != '-') echo 'font-size:' . $options['sidebar_footer_h_font_size'] .';'; ?>
						
				<?php 

		            if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				    if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  } 
					
					else {
					  	echo 'font-weight: normal;';
					}  ?>

				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
			<?php echo '}'; ?>
			
	<?php } // attrs in use ?>	

	
	<?php 
	/*-------------------------------------------------------------------------*/
	/*	Team member names & heading subtitles
	/*-------------------------------------------------------------------------*/
	$styles = explode('-', $options['team_member_h_font_style']);
	$line_height =  substr($options['team_member_h_font_size'],0,-2); ?>
	
	<?php 
	
	if($options['team_member_h_font_family']['attrs_in_use']) {
			
			echo '.team-member h4, .row .col.section-title p, .row .col.section-title span, #page-header-bg .subheader, .nectar-milestone .subject, .testimonial_slider blockquote span 
			{'; ?>	
			<?php if($options['team_member_h_font'] != '-') {
					  $font_family = (1 === preg_match('~[0-9]~', $options['team_member_h_font'])) ? '"'. $options['team_member_h_font'] .'"' : $options['team_member_h_font'];
			}  		
					  if($options['team_member_h_font'] != '-') echo 'font-family: ' . $font_family .';'; 
					  if($options['team_member_h_font_transform'] != '-') echo 'text-transform: ' . $options['team_member_h_font_transform'] .';';  
					  if($options['team_member_h_font_spacing'] != '-') echo 'letter-spacing: ' . $options['team_member_h_font_spacing'] .';';  
				      if($options['team_member_h_font_size'] != '-') echo 'font-size:' . $options['team_member_h_font_size'] .';'; ?>
					
				<?php 
		             if(!empty($styles[0]) && $styles[0] == 'regular') $styles[0] = '400';
				     if(!empty($styles[0]) && strpos($styles[0],'italic') === false) { echo 'font-weight:' .  $styles[0] .';'; }
					  else if(!empty($styles[0]) && strpos($styles[0],'0italic') == true) {
					  	  $the_weight = explode("i",$styles[0]);
					  	  echo 'font-weight:' .  $the_weight[0] .';'; 
					  	  echo 'font-style: italic';
					  }
					  else if(!empty($styles[0])) {
					  	  if(strpos($styles[0],'italic') !== false) {
					  	    echo 'font-weight: 400;'; 
					  	    echo 'font-style: italic';
					  	 }
					  } 

				?>
				<?php if(!empty($styles[1])) echo 'font-style:' . $styles[1]; ?>
					
			<?php echo '}'; ?>
			
			
			<?php echo 'article.post .post-meta .month { line-height:'. ($line_height + -6) . 'px!important; }'; 
			
	 } // attrs in use 	
	
	if($external_dynamic != 'on') {

		echo '</style>';
		
		
		$dynamic_css = ob_get_contents();
		ob_end_clean();
		
		echo nectar_quick_minify($dynamic_css);	

	}
	


?>