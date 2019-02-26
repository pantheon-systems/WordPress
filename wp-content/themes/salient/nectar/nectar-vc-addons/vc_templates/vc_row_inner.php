<?php

   extract(shortcode_atts(array(
	  "type" => 'in_container',
	  'bg_image'=> '', 
	  'background_image_mobile_hidden' => '',
	  'bg_position'=> '', 
	  'bg_repeat' => '', 
	  'parallax_bg' => '', 
	  'parallax_bg_speed' => 'slow',
	  'bg_color'=> '', 
	  'exclude_row_header_color_inherit' => '',
	  'text_align'=> '', 
	  'vertically_center_columns' => '',
	  
	  'mouse_based_parallax_bg' => '',
	  'layer_one_image' => '',
	  'layer_two_image' => '',
	  'layer_three_image' => '',
	  'layer_four_image' => '',
	  'layer_five_image' => '',

	  'layer_one_strength' => '.20',
	  'layer_two_strength' => '.40',
	  'layer_three_strength' => '.60',
	  'layer_four_strength' => '.80',
	  'layer_five_strength' => '1.00',
	  'scene_position' => '',
	  'mouse_sensitivity' => '10',
    
    'disable_element' => '',
	  'video_bg'=> '', 
	  'enable_video_color_overlay'=> '', 
	  'video_overlay_color'=> '', 
	  'video_webm'=> '', 
	  'video_mp4'=> '', 
	  'video_ogv'=> '', 
	  'video_image'=> '', 
	  
	  "top_padding" => "0", 
	  "bottom_padding" => "0",
	  'text_color' => 'dark',  
	  'custom_text_color' => '',  
	  'id' => '',
	  'el_id' => '',
	  'equal_height' => '',
	  'content_placement' => '',
	  'column_margin' => 'default',
	  'css' => '',
	  'class' => '',
    'translate_x' => '',
    'translate_y' => ''
  ), 
	$atts));
	
	wp_enqueue_style( 'js_composer_front' );
	wp_enqueue_script( 'wpb_composer_front_js' );
	wp_enqueue_style('js_composer_custom_css');
	
	if($mouse_based_parallax_bg == 'true'){
		wp_enqueue_script('nectar_parallax');
	}
	
  $style = null;
  $bg_props = null;
	$etxra_class = null;
	$using_image_class = null;
	$using_bg_color_class = null;
	$using_custom_text_color = null;
  
  if ( 'yes' !== $disable_element ) {
  
    	if($this->shortcode == 'vc_row_inner') $text_color = null;
    	
    	if(!empty($bg_image)) {

    		if(!preg_match('/^\d+$/',$bg_image)){
    				
    			$bg_props .= 'background-image: url('. $bg_image . '); ';
    			$bg_props .= 'background-position: '. $bg_position .'; ';
    		
    		} else {
    			$bg_image_src = wp_get_attachment_image_src($bg_image, 'full');
    			
    			$bg_props .= 'background-image: url('. $bg_image_src[0]. '); ';
    			$bg_props .= 'background-position: '. $bg_position .'; ';
    		}
    		
    		//for pattern bgs
    		if(strtolower($bg_repeat) == 'repeat'){
    			$bg_props .= 'background-repeat: '. strtolower($bg_repeat) .'; ';
    			$etxra_class = 'no-cover';
    		} else {
    			$bg_props .= 'background-repeat: '. strtolower($bg_repeat) .'; ';
    			$etxra_class = null;
    		}

    		$using_image_class = 'using-image';
    	}

    	if(!empty($bg_color)) {
    		$bg_props .= 'background-color: '. $bg_color.'; ';
    		if($exclude_row_header_color_inherit != 'true') $using_bg_color_class = 'using-bg-color';
    	}
    	
    	if(strtolower($parallax_bg) == 'true'){
    		$parallax_class = 'parallax_section';
    		$parallax_speed = 'data-parallax-speed="'.$parallax_bg_speed.'"';
    	} else {
    		$parallax_class = 'standard_section';
    		$parallax_speed = null;
    	}
    	
    	if(strtolower($vertically_center_columns) == 'true'){
    		$vertically_center_class = 'vertically-align-columns';
    	} else {
    		$vertically_center_class = null;
    	}
    	

    	if(strpos($top_padding,'%') !== false) {
    		$style .= 'padding-top: '. $top_padding .'; ';
    	} else {
    		$style .= 'padding-top: '. $top_padding .'px; ';
    	}

    	if(strpos($bottom_padding,'%') !== false){
    		$style .= 'padding-bottom: '. $bottom_padding .'; ';
    	} else {	
    		$style .= 'padding-bottom: '. $bottom_padding .'px; ';
    	}
      
      
      /*transforms*/
      if(!empty($translate_y) || !empty($translate_x)) {
          
          /* twice one for regular and -webkit- for older devices */
          for($i=0;$i<2;$i++) {
            
            if($i == 0) {
              $style .= '-webkit-transform: ';
            } else {
              $style .= ' transform: ';
            } 
            
            
            if(!empty($translate_y)) {
            
                if(strpos($translate_y,'%') !== false){
                    $style .= ' translateY('. intval($translate_y) .'%)';
                } else {    
                    $style .= ' translateY('. intval($translate_y) .'px)';
                }
              
            }  
            
            if(!empty($translate_x)) {
            
                if(strpos($translate_x,'%') !== false){
                    $style .= ' translateX('. intval($translate_x) .'%)';
                } else {    
                    $style .= ' translateX('. intval($translate_x) .'px)';
                }
              
            }  
            $style .= ';';
            
          } //loop
          
      }


    	if($text_color == 'custom' && !empty($custom_text_color)) {
    		$style .= 'color: '. $custom_text_color .'; ';
    		$using_custom_text_color = 'data-using-ctc="true"';
    	}
    	
    	//main class
    	if($type == 'in_container') {
    		
    		$main_class = "";
    		
    	} else if($type == 'full_width_background'){
    		
    		$main_class = "full-width-section ";
    		
    	} else if($type == 'full_width_content'){
    		
    		$main_class = "full-width-content ";
    	}

    	//equal height
    	if($equal_height == 'yes')
    		$equal_height_class = ' vc_row-o-equal-height vc_row-flex ';
    	else 
    	 	$equal_height_class = '';

    	 if ( ! empty( $content_placement ) ) {
    		$equal_height_class .= ' vc_row-o-content-' . $content_placement.' ';
    	}
    		 
    	 
    	$row_id = (!empty($el_id)) ? $el_id: uniqid("fws_");
    	//if(!empty($id)) { $row_id = $id; }
    	$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );
    	
        echo'
    	<div id="'.$row_id.'" data-midnight="'.strtolower($text_color).'" data-column-margin="'.$column_margin.'" data-bg-mobile-hidden="'.$background_image_mobile_hidden.'" class="wpb_row vc_row-fluid vc_row inner_row '. $main_class . $equal_height_class . $parallax_class . ' ' .  $css_class . ' '. $vertically_center_class . ' '. $class . ' " '.$using_custom_text_color.' style="'.$style.'">';
    	
    	//row bg 
    	echo '<div class="row-bg-wrap"> <div class="row-bg '.$using_image_class . ' ' . $using_bg_color_class . ' '. $etxra_class.'" '.$parallax_speed.' style="'.$bg_props.'"></div> </div>';

    	//mouse based parallax
    	if($mouse_based_parallax_bg == 'true') {

            echo '<ul class="nectar-parallax-scene" data-scene-position="'.$scene_position.'" data-scene-strength="'.$mouse_sensitivity.'">';

            echo '<li class="layer" data-depth="0.00"></li>';

            if(!empty($layer_one_image)) {
            	if(!preg_match('/^\d+$/',$layer_one_image)){
            		$layer_one_image_src = $layer_one_image;
            	} else {
            		$layer_one_image_src = wp_get_attachment_image_src($layer_one_image, 'full');
            		$layer_one_image_src = $layer_one_image_src[0];
            	}  

            	echo '<li class="layer" data-depth="'.$layer_one_strength.'"><div style="background-image:url(\''. $layer_one_image_src .'\');"></div></li>';
            }
            if(!empty($layer_two_image)) {

            	if(!preg_match('/^\d+$/',$layer_two_image)){
            		$layer_two_image_src = $layer_two_image;
            	} else {
            		$layer_two_image_src = wp_get_attachment_image_src($layer_two_image, 'full');
            		$layer_two_image_src = $layer_two_image_src[0];
            	}  

            	echo '<li class="layer" data-depth="'.$layer_two_strength.'"><div style="background-image:url(\''. $layer_two_image_src .'\');"></div></li>';
            }
            if(!empty($layer_three_image)) {

            	if(!preg_match('/^\d+$/',$layer_three_image)){
            		$layer_three_image_src = $layer_three_image;
            	} else {
            		$layer_three_image_src = wp_get_attachment_image_src($layer_three_image, 'full');
            		$layer_three_image_src = $layer_three_image_src[0];
            	}  

            	echo '<li class="layer" data-depth="'.$layer_three_strength.'"><div style="background-image:url(\''. $layer_three_image_src .'\');"></div></li>';
            }
            if(!empty($layer_four_image)) {

            	if(!preg_match('/^\d+$/',$layer_four_image)){
            		$layer_four_image_src = $layer_four_image;
            	} else {
            		$layer_four_image_src = wp_get_attachment_image_src($layer_four_image, 'full');
            		$layer_four_image_src = $layer_four_image_src[0];
            	}  

            	echo '<li class="layer" data-depth="'.$layer_four_strength.'"><div style="background-image:url(\''. $layer_four_image_src .'\');"></div></li>';
            }
            if(!empty($layer_five_image)) {

            	if(!preg_match('/^\d+$/',$layer_five_image)){
            		$layer_five_image_src = $layer_five_image;
            	} else {
            		$layer_five_image_src = wp_get_attachment_image_src($layer_five_image, 'full');
            		$layer_five_image_src = $layer_five_image_src[0];
            	}  

            	echo '<li class="layer" data-depth="'.$layer_five_strength.'"><div style="background-image:url(\''. $layer_five_image_src .'\');"></div></li>';
            }
            echo '</ul>';

            global $nectar_options;
            $loading_animation = (!empty($nectar_options['loading-image-animation']) && !empty($nectar_options['loading-image'])) ? $nectar_options['loading-image-animation'] : null; 
    		$default_loader = (empty($nectar_options['loading-image']) && !empty($nectar_options['theme-skin']) && $nectar_options['theme-skin'] == 'ascend') ? '<span class="default-loading-icon spin"></span>' : null;
    		$default_loader_class = (empty($nectar_options['loading-image']) && !empty($nectar_options['theme-skin']) && $nectar_options['theme-skin'] == 'ascend') ? 'default-loader' : null;

            echo '<div class="nectar-slider-loading '.$default_loader_class.'"> <span class="loading-icon '.$loading_animation.'"> '.$default_loader.'  </span> </div>';
    	}

    	//video bg
    	if($video_bg) {
    		
    		if ( floatval(get_bloginfo('version')) >= "3.6" ) {
    			//wp_enqueue_script('wp-mediaelement');
    			//wp_enqueue_style('wp-mediaelement');
    		} else {
    			//register media element for WordPress 3.5
    			wp_register_script('wp-mediaelement', get_template_directory_uri() . '/js/mediaelement-and-player.min.js', array('jquery'), '1.0', TRUE);
    			wp_register_style('wp-mediaelement', get_template_directory_uri() . '/css/mediaelementplayer.min.css');
    			
    			wp_enqueue_script('wp-mediaelement');
    			wp_enqueue_style('wp-mediaelement');
    		}
    		
    		//parse video image
    		if(strpos($video_image, "http://") !== false){
    			$video_image_src = $video_image;
    		} else {
    			$video_image_src = wp_get_attachment_image_src($video_image, 'full');
    			$video_image_src = $video_image_src[0];
    		}
    		
    		//$poster_markup = (!empty($video_image)) ? 'poster="'.$video_image_src.'"' : null ;
    		$poster_markup = null;
    		$video_markup = null;
    		
    		if($enable_video_color_overlay != 'true') $video_overlay_color = null;
    		$video_markup .=  '<div class="video-color-overlay" data-color="'.$video_overlay_color.'"></div>';
    		
    			 
    		$video_markup .= '
    		
    		<div class="mobile-video-image" style="background-image: url('.$video_image_src.')"></div>
    		<div class="nectar-video-wrap" data-bg-alignment="'.$bg_position.'">
    			
    			
    			<video class="nectar-video-bg" width="1800" height="700" '.$poster_markup.' controls="controls" preload="auto" loop autoplay>';
    	
    			    if(!empty($video_webm)) { $video_markup .= '<source src="'.$video_webm.'" type="video/webm">'; }
    			    if(!empty($video_mp4)) { $video_markup .= '<source src="'.$video_mp4.'"  type="video/mp4">'; }
    			    if(!empty($video_ogv)) { $video_markup .= '<source src="'. $video_ogv.'" type="video/ogg">'; }
    			  
    		   $video_markup .='</video>
    	
    		</div>';
    		
    		echo $video_markup;
    	}
    	
        echo '<div class="col span_12 '.strtolower($text_color).' '.$text_align.'">'.do_shortcode($content).'</div></div>';
  
  } //disable row
    	
?>