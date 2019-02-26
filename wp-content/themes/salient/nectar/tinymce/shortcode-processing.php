<?php

#-----------------------------------------------------------------#
# Columns
#-----------------------------------------------------------------# 

//half columns 
function nectar_one_half( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
    $parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_6' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('one_half', 'nectar_one_half');

function nectar_one_half_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_6 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">' . $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('one_half_last', 'nectar_one_half_last');



//one third columns
function nectar_one_third( $atts, $content = null ) {
	extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}

    return '<div class="col span_4' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('one_third', 'nectar_one_third');

function nectar_one_third_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_4 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('one_third_last', 'nectar_one_third_last');

function nectar_two_thirds( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_8' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('two_thirds', 'nectar_two_thirds');

function nectar_two_thirds_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_8 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('two_thirds_last', 'nectar_two_thirds_last');



//one fourth columns
function nectar_one_fourth( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_3' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('one_fourth', 'nectar_one_fourth');

function nectar_one_fourth_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_3 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('one_fourth_last', 'nectar_one_fourth_last');

function nectar_three_fourths( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_9' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('three_fourths', 'nectar_three_fourths');

function nectar_three_fourths_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_9 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('three_fourths_last', 'nectar_three_fourths_last');



//one sixth columns
function nectar_one_sixth( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_2' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('one_sixth', 'nectar_one_sixth');

function nectar_one_sixth_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_2 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('one_sixth_last', 'nectar_one_sixth_last');

function nectar_five_sixths( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_10' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div>';
}
add_shortcode('five_sixths', 'nectar_five_sixths');

function nectar_five_sixths_last( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_10 col_last' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('five_sixths_last', 'nectar_five_sixths_last');



function nectar_one_whole( $atts, $content = null ) {
    extract(shortcode_atts(array("boxed" => 'false', "centered_text" => 'false', 'animation' => '', 'delay' => '0'), $atts));
	$column_classes = null;
	$box_border = null;
	$parsed_animation = null;	
	
	if($boxed == 'true')  { $column_classes .= ' boxed'; $box_border = '<span class="bottom-line"></span>'; }
	if($centered_text == 'true') $column_classes .= ' centered-text';
	if(!empty($animation)) {
		 $column_classes .= ' has-animation';
		 
		 $parsed_animation = str_replace(" ","-",$animation);
		 $delay = intval($delay);
	}
	
    return '<div class="col span_12' . $column_classes . '" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'">'. $box_border . do_shortcode($content) . '</div><div class="clear"></div>';
}
add_shortcode('one_whole', 'nectar_one_whole');

#-----------------------------------------------------------------#
# Elements
#-----------------------------------------------------------------# 

//full width section
function nectar_full_width_section($atts, $content = null) {
   	extract(shortcode_atts(array("top_padding" => "40", "bottom_padding" => "40", 'image_url'=> '', 'bg_pos'=> '', 'background_color'=> '', 'bg_repeat' => '', 'text_color' => 'light', 'parallax_bg' => '', 'class' => ''), $atts));
		
	$style = null;
	$etxra_class = null;

	$bg_props = null;
	$using_image_class = null;
	$using_bg_color_class = null;
	
	if(!empty($image_url)) {
		$bg_props .= 'background-image: url('. $image_url. '); ';
		$bg_props .= 'background-position: '. $bg_pos .'; ';
		
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
	
	if(!empty($background_color)) {
		$style .= 'background-color: '. $background_color.'; ';
		$using_bg_color_class = 'using-bg-color';
	}
	
	if(strtolower($parallax_bg) == 'true'){
		$parallax_class = 'parallax_section';
	} else {
		$parallax_class = 'standard_section';
	}
	
	$style .= 'padding-top: '. $top_padding .'px; ';
	$style .= 'padding-bottom: '. $bottom_padding .'px; ';
	 
    return'
	<div id="'.uniqid("fws_").'" class="full-width-section '.$parallax_class . ' ' . $class . ' " style="'.$style.'"> 

	<div class="row-bg-wrap"> <div class="row-bg '.$using_image_class . ' ' . $using_bg_color_class . ' '. $etxra_class.'" style="'.$bg_props.'"></div> </div>

    <div class="col span_12 '.strtolower($text_color).'">'.do_shortcode($content).'</div></div>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('full_width_section', 'nectar_full_width_section');
}

//image with animation
function nectar_image_with_animation($atts, $content = null) { 
    extract(shortcode_atts(array("animation" => 'Fade In', "delay" => '0', "image_url" => '', 'alt' => '', 'margin_top' => '', 'margin_right' => '', 'margin_bottom' => '', 'margin_left' => '', 'alignment' => 'left', 'border_radius' => '', 'img_link_target' => '_self', 'img_link' => '', 'img_link_large' => '', 'box_shadow' => 'none', 'box_shadow_direction' => 'middle', 'max_width' => '100%','el_class' => ''), $atts));
	
	$parsed_animation = str_replace(" ","-",$animation);
	(!empty($alt)) ? $alt_tag = $alt : $alt_tag = null;
	
	$image_width = '100';
	$image_height = '100';
	$image_srcset = null;

	if(preg_match('/^\d+$/',$image_url)){
		$image_src = wp_get_attachment_image_src($image_url, 'full');

		if (function_exists('wp_get_attachment_image_srcset')) {

			$image_srcset_values = wp_get_attachment_image_srcset($image_url, 'full');
			if($image_srcset_values) {
				$image_srcset = 'srcset="';
				$image_srcset .= $image_srcset_values;
				$image_srcset .= '" sizes="100vw"';
			}
		}
		
		$image_meta = wp_get_attachment_metadata($image_url);
		if(!empty($image_meta['width'])) $image_width = $image_meta['width'];
		if(!empty($image_meta['height'])) $image_height = $image_meta['height'];

		$wp_img_alt_tag = get_post_meta( $image_url, '_wp_attachment_image_alt', true );
		if(!empty($wp_img_alt_tag)) $alt_tag = $wp_img_alt_tag;
		$image_url = $image_src[0];
		
	}
  
  $margins = '';
	if(!empty($margin_top)) {
    
    if(strpos($margin_top,'%') !== false) {
      $margins .= 'margin-top: '.intval($margin_top).'%; ';
    } else {
      $margins .= 'margin-top: '.intval($margin_top).'px; ';
    }

	}
	if(!empty($margin_right)) {
    
    if(strpos($margin_right,'%') !== false) {
      $margins .= 'margin-right: '.intval($margin_right).'%; ';
    } else {
      $margins .= 'margin-right: '.intval($margin_right).'px; ';
    }
		
	}
	if(!empty($margin_bottom)) {
    
    if(strpos($margin_bottom,'%') !== false) {
      $margins .= 'margin-bottom: '.intval($margin_bottom).'%; ';
    } else {
      $margins .= 'margin-bottom: '.intval($margin_bottom).'px; ';
    }
		
	}
	if(!empty($margin_left)) {
    
    if(strpos($margin_left,'%') !== false) {
      $margins .= 'margin-left: '.intval($margin_left).'%;';
    } else {
      $margins .= 'margin-left: '.intval($margin_left).'px;';
    }
		
	}
  
  $margin_style_attr = '';
  
  if(!empty($margins)) {
     $margin_style_attr = 'style="'.$margins.'"';
  }
	
	$box_shadow_attrs = 'data-shadow="'.$box_shadow.'" data-shadow-direction="'.$box_shadow_direction.'"';
	
	if(!empty($img_link) || !empty($img_link_large)){
		
		if(!empty($img_link) && empty($img_link_large)) {
			
			return '<div class="img-with-aniamtion-wrap '.$alignment.'" data-max-width="'.$max_width.'" data-border-radius="'.$border_radius.'"><div class="inner"><a href="'.$img_link.'" target="'.$img_link_target.'" class="'.$alignment.'"><img '.$box_shadow_attrs.' class="img-with-animation skip-lazy '.$el_class.'" data-delay="'.$delay.'" height="'.$image_height.'" width="'.$image_width.'" '.$margin_style_attr.' data-animation="'.strtolower($parsed_animation).'" src="'.$image_url.'" '.$image_srcset.' alt="'.$alt_tag.'" /></a></div></div>';
			
		} elseif(!empty($img_link_large)) {
			
			return '<div class="img-with-aniamtion-wrap '.$alignment.'" data-max-width="'.$max_width.'" data-border-radius="'.$border_radius.'"><div class="inner"><a href="'.$image_url.'" class="pp '.$alignment.'"><img '.$box_shadow_attrs.' class="img-with-animation skip-lazy '.$el_class.'" data-delay="'.$delay.'" height="'.$image_height.'" width="'.$image_width.'" '.$margin_style_attr.' data-animation="'.strtolower($parsed_animation).'" src="'.$image_url.'" '.$image_srcset.' alt="'.$alt_tag.'" /></a></div></div>';
		}
		
	} else {
		return '<div class="img-with-aniamtion-wrap '.$alignment.'" data-max-width="'.$max_width.'" data-border-radius="'.$border_radius.'"><div class="inner"><img '.$box_shadow_attrs.' class="img-with-animation skip-lazy '.$el_class.'" data-delay="'.$delay.'" height="'.$image_height.'" width="'.$image_width.'" '.$margin_style_attr.' data-animation="'.strtolower($parsed_animation).'" src="'.$image_url.'" '.$image_srcset.' alt="'.$alt_tag.'" /></div></div>';
	}
   
}

add_shortcode('image_with_animation', 'nectar_image_with_animation');


//testimonial slider
function nectar_testimonial_slider($atts, $content = null) { 
    extract(shortcode_atts(array("autorotate"=>''), $atts));
	
	
    return '<div class="col span_12 testimonial_slider" data-autorotate="'.$autorotate.'"><div class="slides">'.do_shortcode($content).'</div></div>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('testimonial_slider', 'nectar_testimonial_slider');
}

//testimonial 
function nectar_testimonial($atts, $content = null) { 
    extract(shortcode_atts(array("name" => '', "quote" => ''), $atts));
	
    return '<blockquote><p>'.$quote.'</p>'. '<span>'.$name.'</span></blockquote>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('testimonial', 'nectar_testimonial');
}


//heading
function nectar_heading($atts, $content = null) { 
    extract(shortcode_atts(array("title" => 'Title', "subtitle" => 'Subtitle'), $atts));
	$subtitle_holder = null;
	
	if($subtitle != 'Subtitle') $subtitle_holder = '<p>'.$subtitle.'</p>';
    return'
    <div class="col span_12 section-title text-align-center extra-padding">
		<h2>'.$content.'</h2>'. $subtitle_holder .'</div><div class="clear"></div>';
}
add_shortcode('heading', 'nectar_heading');



//divider
function nectar_divider($atts, $content = null) {  
    extract(shortcode_atts(array("line" => 'false', "custom_height" => '25', "line_type" => 'No Line', "line_alignment" => 'default', 'line_thickness' => '1', 'custom_line_width' => '20%', 'divider_color' => 'default', 'animate' => '', 'delay' => ''), $atts));
	
	if($line_type == 'Small Thick Line' || $line_type == 'Small Line' ){
		$height = (!empty($custom_height)) ? 'style="margin-top: '.intval($custom_height/2).'px; width: '.$custom_line_width.'px; height: '.$line_thickness.'px; margin-bottom: '.intval($custom_height/2).'px;"' : null;
		$divider = '<div '.$height.' data-width="'.$custom_line_width.'" data-animate="'.$animate.'" data-animation-delay="'.$delay.'" data-color="'.$divider_color.'" class="divider-small-border"></div>';
	} else if($line_type == 'Full Width Line'){
		$height = (!empty($custom_height)) ? 'style="margin-top: '.intval($custom_height/2).'px; height: '.$line_thickness.'px; margin-bottom: '.intval($custom_height/2).'px;"' : null;
		$divider = '<div '.$height.' data-width="100%" data-animate="'.$animate.'" data-animation-delay="'.$delay.'" data-color="'.$divider_color.'" class="divider-border"></div>';
	} else {
		$height = (!empty($custom_height)) ? 'style="height: '.intval($custom_height).'px;"' : null;
		$divider = '<div '.$height.' class="divider"></div>';
	}
	//old option
	if($line == 'true') $divider = '<div class="divider-border"></div>';
    return '<div class="divider-wrap" data-alignment="' . $line_alignment . '">'.$divider.'</div>';
}
add_shortcode('divider', 'nectar_divider');


//divider
function nectar_dropcap_proc($atts, $content = null) {  
	 extract(shortcode_atts(array("color" => ''), $atts));

	 $color_str = null;
	if(!empty($color)) $color_str = 'style=" color: '.$color.';"'; 
    return '<span class="nectar-dropcap" '.$color_str.'>'.$content.'</span>';
}

add_shortcode('nectar_dropcap', 'nectar_dropcap_proc');


//milestone
function nectar_milestone($atts, $content = null) {  
    extract(shortcode_atts(array("subject" => '', 'symbol' => '', 'milestone_alignment' => 'default', 'heading_inherit' => 'default', 'symbol_position' => 'after', 'subject_padding' => '0%','symbol_alignment' => 'default', 'number_font_size' => '62', 'symbol_font_size' => '62', 'effect' => 'count', 'number' => '0', 'color' => 'Default'), $atts));
	
	if(!empty($symbol)) {
		$symbol_markup = 'data-symbol="'.$symbol.'" data-symbol-alignment="'.strtolower($symbol_alignment).'" data-symbol-pos="'.$symbol_position.'" data-symbol-size="'.$symbol_font_size.'"';
	} else {
		$symbol_markup = null;
	}

	$motion_blur = null;
	$milestone_wrap = null;
	$milestone_wrap_close = null;
	$span_open = null;
	$span_close = null;

	if($effect == 'motion_blur') {
		$motion_blur = 'motion_blur';
		$milestone_wrap = '<div class="milestone-wrap">';
		$milestone_wrap_close = '</div>';
	} else {
		$span_open = '<span>';
		$span_close = '</span>';
	}
	

	if($heading_inherit != 'default') {
		$milestone_h_open = '<'.$heading_inherit.'>';
		$milestone_h_close = '</'.$heading_inherit.'>';
	} else {
		$milestone_h_open = null;
		$milestone_h_close = null;
	}

	$subject_padding_html = (!empty($subject_padding) && $subject_padding != '0%') ? 'style="padding: '.$subject_padding.';"' : null;

	$number_markup = '<div class="number '.strtolower($color).'" data-number-size="'.$number_font_size.'">'.$milestone_h_open.$span_open.$number.$span_close.$milestone_h_close.'</div>';
	$subject_markup = '<div class="subject" '.$subject_padding_html.'>'.$subject.'</div>';
	
    return $milestone_wrap . '<div class="nectar-milestone '. $motion_blur . '" '. $symbol_markup.' data-ms-align="'.$milestone_alignment.'" > '.$number_markup.' '.$subject_markup.' </div>' . $milestone_wrap_close;
}
add_shortcode('milestone', 'nectar_milestone');



//text with icon
function nectar_text_with_icon($atts, $content = null) {  
    extract(shortcode_atts(array('color' => 'Accent-Color', 'icon_type' => 'font_icon', 'icon' => 'icon-glass', 'icon_image' => ''), $atts));
	
	$icon_markup = null;
	$output = null;

	if($icon_type == 'font_icon'){
		$icon_markup = '<i class="icon-default-style '.$icon.' '. strtolower($color).'"></i>';
	} else {
		$icon_markup = wp_get_attachment_image_src($icon_image, 'medium');
		if(!empty($icon_markup)) {
			
			$icon_alt = get_post_meta($icon_image, '_wp_attachment_image_alt', true);
			
			$icon_markup = '<img src="'.$icon_markup[0].'" alt="'.$icon_alt.'" />';
		} else {
			$icon_markup = null;
		}
	}
	
	$output .= '<div class="iwithtext"><div class="iwt-icon"> '.$icon_markup.' </div>';
	$output .= '<div class="iwt-text"> '.do_shortcode($content).' </div><div class="clear"></div></div>';
	
    return $output;
}
add_shortcode('text-with-icon', 'nectar_text_with_icon');


//fancy list
function nectar_fancy_list($atts, $content = null) {  
    extract(shortcode_atts(array('color' => 'Accent-Color', 'alignment' => 'left' ,'icon_type' => 'standard_dash', 'icon' => 'icon-glass', 'enable_animation' => 'false', 'delay' => ''), $atts));
	
	$icon_markup = null;
	$output = null;
	$delay = intval($delay);

	if($icon_type == 'font_icon'){
		$icon_markup = 'data-list-icon="'.$icon.'" data-animation="'.$enable_animation.'" data-animation-delay="'.$delay.'" data-color="'. strtolower($color).'"';
	} else if($icon_type == 'none') {
		$icon_markup = 'data-list-icon="none" data-animation="'.$enable_animation.'" data-animation-delay="'.$delay.'" data-color="'. strtolower($color).'"';
	} else {
		$icon_markup = 'data-list-icon="icon-salient-thin-line" data-animation="'.$enable_animation.'" data-animation-delay="'.$delay.'" data-color="'. strtolower($color).'"';
	}
	
	$output .= '<div class="nectar-fancy-ul" '.$icon_markup.' data-alignment="'.$alignment.'"> '.do_shortcode($content).' </div>';
	
    return $output;
}
add_shortcode('fancy-ul', 'nectar_fancy_list');




//button
function nectar_button($atts, $content = null) {  
    extract(shortcode_atts(array("size" => 'small', "url" => '#', 'color' => 'Accent-Color', 'color_override' => '', 'hover_color_override' => '', 'hover_text_color_override' => '#fff', "text" => 'Button Text', 'image' => '', 'open_new_tab' => '0'), $atts));
	
	$target = ($open_new_tab == 'true') ? 'target="_blank"' : null;
	
	//icon
	if(!empty($image) && strpos($image,'.svg') !== false) {
		if(!empty($image)) { $button_icon = '<img src="'.get_template_directory_uri() . '/css/fonts/svg/'.$image.'" alt="icon" />'; $has_icon = ' has-icon'; } else { $button_icon = null; $has_icon = null; }
	} else {

		if(!empty($image)) { 
			$fontawesome_extra = null; 
			if(strpos($image, 'fa-') !== false) $fontawesome_extra = 'fa '; 
			$button_icon = '<i class="' . $fontawesome_extra . $image .'"></i>'; $has_icon = ' has-icon'; 
		} 
		else { $button_icon = null; $has_icon = null; }
	}
	
	//standard arrow icon
	if($image == 'default-arrow') $button_icon = '<i class="icon-button-arrow"></i>';
	
	$stnd_button = null;
	if( strtolower($color) == 'accent-color' || strtolower($color) == 'extra-color-1' || strtolower($color) == 'extra-color-2' || strtolower($color) == 'extra-color-3') {
		$stnd_button = " regular-button";
	}
	
	$button_open_tag = '';

	if($color == 'accent-color-tilt' || $color == 'extra-color-1-tilt' || $color == 'extra-color-2-tilt' || $color == 'extra-color-3-tilt') {
		$color = substr($color, 0, -5);
		$color = $color . ' tilt';
		$button_open_tag = '<div class="tilt-button-wrap"> <div class="tilt-button-inner">';
	}

	switch ($size) {
		case 'small' :
			$button_open_tag .= '<a class="nectar-button small '. strtolower($color) . $has_icon . $stnd_button.'" '. $target;
			break;
		case 'medium' :
			$button_open_tag .= '<a class="nectar-button medium ' . strtolower($color) . $has_icon . $stnd_button.'" '. $target;
			break;
		case 'large' :
			$button_open_tag .= '<a class="nectar-button large '. strtolower($color) . $has_icon . $stnd_button.'" '. $target;
			break;	
		case 'jumbo' :
			$button_open_tag .= '<a class="nectar-button jumbo '. strtolower($color) . $has_icon . $stnd_button.'" '. $target;
			break;	
		case 'extra_jumbo' :
			$button_open_tag .= '<a class="nectar-button extra_jumbo '. strtolower($color) . $has_icon . $stnd_button.'" '. $target;
			break;	
	}
	
	$color_or = (!empty($color_override)) ? 'data-color-override="'. $color_override.'" ' : 'data-color-override="false" ';	
	$hover_color_override = (!empty($hover_color_override)) ? ' data-hover-color-override="'. $hover_color_override.'"' : 'data-hover-color-override="false"';
	$hover_text_color_override = (!empty($hover_text_color_override)) ? ' data-hover-text-color-override="'. $hover_text_color_override.'"' :  null;	
	$button_close_tag = null;

	if($color == 'accent-color tilt' || $color == 'extra-color-1 tilt' || $color == 'extra-color-2 tilt' || $color == 'extra-color-3 tilt') $button_close_tag = '</div></div>';

	if($color != 'see-through-3d') {
		if($color == 'extra-color-gradient-1' || $color == 'extra-color-gradient-2' || $color == 'see-through-extra-color-gradient-1' || $color == 'see-through-extra-color-gradient-2')
			return $button_open_tag . ' href="' . $url . '" '.$color_or.$hover_color_override.$hover_text_color_override.'><span class="start loading">' . $text . $button_icon. '</span><span class="hover">' . $text . $button_icon. '</span></a>'. $button_close_tag;
		else
			return $button_open_tag . ' href="' . $url . '" '.$color_or.$hover_color_override.$hover_text_color_override.'><span>' . $text . '</span>'. $button_icon . '</a>'. $button_close_tag;
	

    	
	}
	else {

		$color = (!empty($color_override)) ? $color_override : '#ffffff';
		$border = ($size != 'jumbo') ? 8 : 10;
		if($size =='extra_jumbo') $border = 20;
		return '
		<div class="nectar-3d-transparent-button" data-size="'.$size.'">
		     <a href="'.$url.'"><span class="hidden-text">'.$text.'</span>
			<div class="inner-wrap">
				<div class="front-3d">
					<svg>
						<defs>
							<mask>
								<rect width="100%" height="100%" fill="#ffffff"></rect>
								<text class="mask-text button-text" fill="#000000" width="100%" text-anchor="middle">'.$text.'</text>
							</mask>
						</defs>
						<rect id="" fill="'.$color.'" width="100%" height="100%" ></rect>
					</svg>
				</div>
				<div class="back-3d">
					<svg>
						<rect stroke="'.$color.'" stroke-width="'.$border.'" fill="transparent" width="100%" height="100%"></rect>
						<text class="button-text" fill="'.$color.'" text-anchor="middle">'.$text.'</text>
					</svg>
				</div>
			</div>
			</a>
		</div>
		';
}
}
add_shortcode('button', 'nectar_button');



//icon
function nectar_icon($atts, $content = null) {
	extract(shortcode_atts(array("size" => 'large', 'color' => 'Accent-Color', 'image' => 'icon-circle', 'icon_size' => '64', 'enable_animation' => 'false', 'animation_delay' => '0', 'animation_speed' => 'medium'), $atts)); 
	
	if($size == 'large-2') {
		$size_class = 'icon-3x alt-style';
	} 
	else if($size == 'large') {
		$size_class = 'icon-3x';
	}
	else if($size == 'regular') {
		$size_class = 'icon-default-style';
	}  
	else if($size == 'tiny') {
		$size_class = 'icon-tiny';
	}
	else {
		$size_class = 'icon-normal'; 
	}
	
	($size == 'large') ? $border = '<i class="circle-border"></i>' : $border = ''; 
	
	if(strpos($image,'.svg') !== false) {

		//gradient loads from font family
		if(strtolower($color) == 'extra-color-gradient-1' || strtolower($color) == 'extra-color-gradient-2') {
			$converted_class = str_replace('_', '-', $image);
			$converted_class = str_replace('.svg', '', $converted_class);
			return '<i class="icon-'.$converted_class.'" data-color="'.strtolower($color).'" style="font-size: '.$icon_size.'px;"></i>';
		}
		//non gradient uses svg
		else {
			if(strtolower($animation_speed) == 'slow') $animation_speed_time = 200;
			if(strtolower($animation_speed) == 'medium') $animation_speed_time = 150;
			if(strtolower($animation_speed) == 'fast') $animation_speed_time = 65;

			$svg_icon = '<div class="nectar_icon_wrap"><span class="svg-icon-holder" data-size="'. $icon_size . '" data-animation-speed="'.$animation_speed_time.'" data-animation="'.$enable_animation.'" data-animation-delay="'.$animation_delay.'" data-color="'.strtolower($color) .'"><span>';
      
      ob_start();
    
    	//$svg_icon .= file_get_contents( get_template_directory() . '/css/fonts/svg/' . $image);
      
    	get_template_part( 'css/fonts/svg/'. $image );
      
    	$svg_icon .= ob_get_contents();
    	ob_end_clean();
      
      $svg_icon .= '</span></span></div>';
      
			return $svg_icon;
		} 
	}
	else {
		$fontawesome_extra = null;
		if(strpos($image, 'fa-') !== false) $fontawesome_extra = ' fa';
		return '<i class="'. $size_class . $fontawesome_extra . ' ' . $image . ' ' . strtolower($color) .'">' . $border . '</i>';
	}
    
}
add_shortcode('icon', 'nectar_icon');



//bar graph - must remain for legacy users
function nectar_bar_graph($atts, $content = null) {  
    return do_shortcode($content);
}
add_shortcode('bar_graph', 'nectar_bar_graph');


function nectar_bar($atts, $content = null) {
	extract(shortcode_atts(array("title" => 'Title', "percent" => '1', 'color' => 'Accent-Color', 'id' => ''), $atts));  
	$bar = '
	<div class="nectar-progress-bar">
		<p>' . $title . '</p>
		<div class="bar-wrap"><span class="'.strtolower($color).'" data-width="' . $percent . '"> <strong><i>' . $percent . '</i>%</strong> </span></div>
	</div>';
    return $bar;
}
add_shortcode('bar', 'nectar_bar');



//Team Member
function nectar_team_member($atts, $content = null) {
	
    extract(shortcode_atts(array(
      "description" => '', 
      'team_member_bio' => '',
      'team_memeber_style' => '', 
      'color' => 'Accent-Color', 
      'name' => 'Name', 
      'job_position' => '', 
      'image_url' => '', 
      'bio_image_url' => '', 
      'social' => '', 
      'social_icon_1' => '', 
      'social_link_1' => '', 
      'social_icon_2' => '', 
      'social_link_2' => '', 
      'social_icon_3' => '', 
      'social_link_3' => '', 
      'social_icon_4' => '', 
      'social_link_4' => '', 
      'link_element' => 'none', 
      'link_url' => '', 
      'link_url_2' => '',
      'team_member_link_new_tab' => ''), $atts));
	
	$html = null;
  $link_new_tab_markup = ($team_member_link_new_tab == 'true') ? 'target="_blank"': '';

  
	//fullscreen bio
    if($team_memeber_style == 'bio_fullscreen') {

    	$bio_image_url_src = null;
    	$team_alt = null;

    	if(!empty($bio_image_url)){
	    	$bio_image_url_src = $bio_image_url;

	    	if(preg_match('/^\d+$/',$bio_image_url)){
				$bio_image_src = wp_get_attachment_image_src($bio_image_url, 'full');
				$bio_image_url_src = $bio_image_src[0];
			}
		}

		if(!empty($image_url)){
				
			if(preg_match('/^\d+$/',$image_url)){
				$team_alt = get_post_meta( $image_url, '_wp_attachment_image_alt', true );
				$image_src = wp_get_attachment_image_src($image_url, 'regular');
				$image_url = $image_src[0];
			}
			
		}
     
     $social_markup = '<div class="bottom_meta">';
     for($i=1; $i<5; $i++) {
        if(isset($atts['social_icon_'.$i]) && !empty($atts['social_icon_'.$i])) {
          
          $social_link_url = ( !empty($atts['social_link_'.$i]) ) ? $atts['social_link_'.$i] : '';
          
          $social_markup .= '<a href="'.$social_link_url.'" target="_blank"><i class="icon-default-style '.$atts['social_icon_'.$i].'"></i>'.'</a>';
        }
     }
     $social_markup .= '</div>';
     
    	$html .= '<div class="team-member" data-style="'.$team_memeber_style.'">
    	<div class="team-member-image"><img src="'.$image_url.'" alt="'.$team_alt.'" width="500" height="500" /></div>
    	<div class="team-member-overlay"></div>
    	<div class="team-meta"><h3>' . $name . '</h3><p>' . $job_position . '</p><div class="arrow-end fa fa-angle-right"></div><div class="arrow-line"></div></div>
    	<div class="nectar_team_bio_img" data-img-src="'.$bio_image_url_src.'"></div>
    	<div class="nectar_team_bio">'.$team_member_bio.  $social_markup .'</div>
    	</div>';

    	return str_replace("\r\n", '', $html);
    }
		


	$html .= '<div class="team-member" data-style="'.$team_memeber_style.'">';
	
	if($team_memeber_style == 'meta_overlaid' || $team_memeber_style == 'meta_overlaid_alt'){
		
		$html .= '<div class="team-member-overlay"></div>';
		
		if(!empty($image_url)){
				
				if(preg_match('/^\d+$/',$image_url)){
					$image_src = wp_get_attachment_image_src($image_url, 'portfolio-thumb');
					$image_url = $image_src[0];
				}
				
				//image link
				if(!empty($link_url_2)){
					$html .= '<a href="'.$link_url_2.'" '.$link_new_tab_markup.'></a> <div class="team-member-image" style="background-image: url('.$image_url.');"></div>';
				} else {
					$html .= '<div class="team-member-image" style="background-image: url('.$image_url.');"></div>';
				}
				
			}
			else {
				//image link
				if(!empty($link_url_2)){
					$html .= '<a href="'.$link_url_2.'" '.$link_new_tab_markup.'></a><div class="team-member-image" style="background-image: url('. NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/team-member-default.jpg);"></div>';
				} else {
					$html .= '<div class="team-member-image" style="background-image: url('. NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/team-member-default.jpg);"></div>';
				}
		
			}
			
			//name link
			$html .= '<div class="team-meta">';
				$html .= '<h3>' . $name . '</h3>';
				$html .= '<p>' . $job_position . '<p>';
			$html .= '</div>';
			
	} else {
		
		if(!empty($image_url)){
			
			$team_alt = $name;
			
			if(preg_match('/^\d+$/',$image_url)){
				$image_src = wp_get_attachment_image_src($image_url, 'full');
				$team_alt = get_post_meta( $image_url, '_wp_attachment_image_alt', true );
				$image_url = $image_src[0];
			}
			
			//image link
			if($link_element == 'image' || $link_element == 'both'){
				$html .= '<a href="'.$link_url.'" '.$link_new_tab_markup.'><img alt="'.$team_alt.'" src="' . $image_url .'" title="' . $name . '" /></a>';
			} else {
				$html .= '<img alt="'.$team_alt.'" src="' . $image_url .'" title="' . $name . '" />';
			}
			
		}
		else {
			//image link
			if($link_element == 'image' || $link_element == 'both'){
				$html .= '<a href="'.$link_url.'" '.$link_new_tab_markup.'><img alt="'.$name.'" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/team-member-default.jpg" title="' . $name . '" /></a>';
			} else {
				$html .= '<img alt="'.$name.'" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/team-member-default.jpg" title="' . $name . '" />';
			}
	
		}
		
		//name link
		if($link_element == 'name' || $link_element == 'both'){
			$html .= '<h4 class="light"><a class="'.strtolower($color).'" href="'.$link_url.'" '.$link_new_tab_markup.'>' . $name . '</a></h4>';
		} else {
			$html .= '<h4 class="light">' . $name . '</h4>';
		}
	
		$html .= '<div class="position">' . $job_position . '</div>';
		$html .= '<p class="description">' . $description . '</p>';
		
		if (!empty($social) && strlen($social) > 1) {
			 
       $social = str_replace(array("\r\n", "\r", "\n", "<br/>", "<br />"), " ", $social);
	     $social_arr = explode(",", $social);
			 

			 $html .= '<ul class="social '.strtolower($color).'">';
          
	        for ($i = 0 ; $i < count($social_arr) ; $i = $i + 2) {
	         	
            if(isset($social_arr[$i + 1])) {	
  					  $target = null;
  	         	$url_host = parse_url($social_arr[$i + 1], PHP_URL_HOST);
  				    $base_url_host = parse_url(get_template_directory_uri(), PHP_URL_HOST);
  				    if($url_host != $base_url_host || empty($url_host)) {
  				    	$target = 'target="_blank"';
  				    }
  					 
  	         $html .=  "<li><a ".$target." href='" . $social_arr[$i + 1] . "'>" . $social_arr[$i] . "</a></li>";   
           }
           
	       }
         
			 $html .= '</ul>'; 
       
	     }
		
     }
	
	$html .= '</div>';
	
	return str_replace("\r\n", '', $html);
	 
}
add_shortcode('team_member', 'nectar_team_member');



//carousel
function nectar_carousel($atts, $content = null) {  
    extract(shortcode_atts(array("carousel_title" => 'Title', "scroll_speed" => 'medium', 'easing' => 'easeInExpo'), $atts));
	
	$carousel_html = null;
	$carousel_html .= '
	<div class="carousel-wrap" data-full-width="false">
	<div class="carousel-heading">
		<div class="container">
			<h2 class="uppercase">'. $carousel_title .'</h2>
				<div class="control-wrap">
					<a class="carousel-prev" href="#"><i class="icon-angle-left"></i></a>
					<a class="carousel-next" href="#"><i class="icon-angle-right"></i></a>
				</div>
		</div>
	</div>
	</span><ul class="row carousel" data-scroll-speed="' . $scroll_speed . '" data-easing="' . $easing . '">';
	
    return $carousel_html . do_shortcode($content) . '</ul></div>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('carousel', 'nectar_carousel');
}

function nectar_carousel_item($atts, $content = null) {  
    return '<li class="col span_4">' . do_shortcode($content) . '</li>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('item', 'nectar_carousel_item');
}


//clients
function nectar_clients($atts, $content = null) {  
    extract(shortcode_atts(array("carousel" => "false", "fade_in_animation" => "false", "columns" => '4'), $atts));
	
	$opening = null;
	$closing = null;
	$column_class = null;
	
	switch ($columns) {
		case '2' :
			$column_class = 'two-cols';
			break;
		case '3' :
			$column_class = 'three-cols';
			break;
		case '4' :
			$column_class = 'four-cols';
			break;	
		case '5' :
			$column_class = 'five-cols';
			break;
		case '6' :
			$column_class = 'six-cols';
			break;
	}
	
	($fade_in_animation == "true") ? $animation = 'fade-in-animation' : $animation = null ;
	
	if($carousel == "true"){
		$opening .= '<div class="carousel-wrap"><div class="row carousel clients '.$column_class.' ' .$animation.'" data-max="'.$columns.'">';
		$closing .= '</div></div>';
	}
	else{
		$opening .= '<div class="clients no-carousel '.$column_class.' ' .$animation.'">';
		$closing .= '</div>';
	}
	
    return $opening . do_shortcode($content) . $closing;
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('clients', 'nectar_clients');
}

function nectar_client($atts, $content = null) {
	extract(shortcode_atts(array("image" => "", "url" => '#', "alt" => ""), $atts));
	$client_content = null;
	$image_dimensions = null;
	
	if(preg_match('/^\d+$/',$image)){
		$image_src = wp_get_attachment_image_src($image, 'full');
		$image = $image_src[0];
		$image_dimensions = 'width="'.$image_src[1].'" height="'.$image_src[2].'"';
	}

	(!empty($alt)) ? $alt_tag = $alt : $alt_tag = 'client';
	if(!empty($url) && $url != 'none'){
		$client_content = '<div><a href="'.$url.'" target="_blank"><img src="'.$image.'" '.$image_dimensions.' alt="'.$alt_tag.'" /></a></div>';
	}  
	else {
		$client_content = '<div><img src="'.$image.'" '.$image_dimensions.' alt="'.$alt_tag.'" /></div>';
	}
    return $client_content;
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('client', 'nectar_client');
}



//pricing tables
function nectar_pricing_table($atts, $content = null) {  
    extract(shortcode_atts(array("columns" => '4', "style" => "default"), $atts));
	$column_class = null;
	
	switch ($columns) {
		case '2' :
			$column_class = 'two-cols';
			break;
		case '3' :
			$column_class = 'three-cols';
			break;
		case '4' :
			$column_class = 'four-cols';
			break;	
		case '5' :
			$column_class = 'five-cols';
			break;
	}
	
    return '<div class="row pricing-table '.$column_class.'" data-style="'.$style.'">' . do_shortcode($content) . '</div>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('pricing_table', 'nectar_pricing_table');
}

function nectar_pricing_column($atts, $content = null) {
	extract(shortcode_atts(array("title"=>'Column title', "highlight" => 'false', "highlight_reason" => 'Most Popular', 'color' => 'Accent-Color', "price" => "99", "currency_symbol" => '$', "interval" => 'Per Month'), $atts));
	
	$highlight_class = null;
	$hightlight_reason_html = null;
	
	if($highlight == 'true') {
		$highlight_class = 'highlight ' . strtolower($color); 
		$hightlight_reason_html = '<span class="highlight-reason">'.$highlight_reason.'</span>';
	}
	
    return '<div class="pricing-column '.$highlight_class.'">
  			<h3>'.$title. $hightlight_reason_html .'</h3>
            <div class="pricing-column-content">
				<h4> <span class="dollar-sign">'.$currency_symbol.'</span>'.$price.' </h4>
				<span class="interval">'.$interval.'</span>' . do_shortcode($content) . '</div></div>';
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('pricing_column', 'nectar_pricing_column');
}



//tabbed sections
function nectar_tabs($atts, $content = null) {
    $GLOBALS['tab_count'] = 0;
	do_shortcode( $content );
	
	if( is_array( $GLOBALS['tabs'] ) ){
		
		foreach( $GLOBALS['tabs'] as $tab ){
			$tabs[] = '<li><a href="#'.$tab['id'].'">'.$tab['title'].'</a></li>';
			$panes[] = '<div id="'.$tab['id'].'">'.$tab['content'].'</div>';
		}
		
		$return = '<div class="tabbed vc_clearfix"><ul>'.implode( "\n", $tabs ).'</ul>'.implode( "\n", $panes )."</div>\n";
	}
	return $return;
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode('tabbed_section', 'nectar_tabs');
}

function nectar_tab( $atts, $content ){
	extract(shortcode_atts(array( 'title' => '%d', 'id' => '%d'), $atts));
	
	$x = $GLOBALS['tab_count'];
	$GLOBALS['tabs'][$x] = array(
		'title' => sprintf( $title, $GLOBALS['tab_count'] ),
		'content' =>  do_shortcode($content),
		'id' =>  $id );
	
	$GLOBALS['tab_count']++;
}
if (!class_exists('WPBakeryVisualComposerAbstract') || class_exists('WPBakeryVisualComposerAbstract') && !defined('SALIENT_VC_ACTIVE')) {
	add_shortcode( 'tab', 'nectar_tab' );
}

//toggle panel - accordion chosen
function nectar_toggles($atts, $content = null) { 
	extract(shortcode_atts(array("accordion" => 'false', 'style' => 'default'), $atts));  
	
	($accordion == 'true') ? $accordion_class = 'accordion': $accordion_class = null ;
    return '<div class="toggles '.$accordion_class.'" data-style="'.$style.'">' . do_shortcode($content) . '</div>'; 
}
add_shortcode('toggles', 'nectar_toggles');

//toggle
function nectar_toggle($atts, $content = null) {
	extract(shortcode_atts(array("title" => 'Title', 'color' => 'Accent-Color'), $atts));  
    return '<div class="toggle '.strtolower($color).'"><h3><a href="#"><i class="icon-plus-sign"></i>'. $title .'</a></h3><div>' . do_shortcode($content) . '</div></div>';
}
add_shortcode('toggle', 'nectar_toggle');



 



#-----------------------------------------------------------------#
# Nectar Slider 
#-----------------------------------------------------------------# 
function nectar_slider_processing($atts, $content = null) {
	
	extract(shortcode_atts(array("arrow_navigation" => 'false', "autorotate"=> '', "tablet_header_font_size" => "auto", "tablet_caption_font_size" => "auto", "phone_header_font_size" => "auto", "phone_caption_font_size" => "auto", "button_sizing"=> 'regular', "slider_button_styling"=> 'btn_with_count', "overall_style" => 'classic', "slider_transition"=> 'swipe', "flexible_slider_height"=> '', "min_slider_height"=> '', "loop" => 'false', 'fullscreen' => 'false', "bullet_navigation" => 'false', "bullet_navigation_style" => 'see_through', "disable_parallax_mobile" => '', "bullet_navigation_position" => 'bottom', "caption_transition" => 'fade_in_from_bottom', "parallax" => 'false', "parallax_style" => "parallax_bg_and_content", "bg_animation" => "none", "full_width" => '', "slider_height" => '650', "desktop_swipe" => 'false', "location" => ''), $atts));   
    
  if($overall_style == 'directional') {
    $desktop_swipe = 'false';
  }
    
  if( isset($_GET['vc_editable']) ) {
  	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
  	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
  } else {
  	$nectar_using_VC_front_end_editor = false;
  }
  
  if($nectar_using_VC_front_end_editor) {
    $autorotate = '';
  }
      
	$slider_config = array(
	  'slider_height' => $slider_height,
	  'full_width' => $full_width,
	  'flexible_slider_height' => $flexible_slider_height,
	  'min_slider_height' => $min_slider_height,
	  'autorotate' => $autorotate,
	  'arrow_navigation' => $arrow_navigation,
	  'bullet_navigation' => $bullet_navigation,
	  'bullet_navigation_style' => $bullet_navigation_style,
    'bullet_navigation_position' => $bullet_navigation_position,
	  'desktop_swipe' => $desktop_swipe,
	  'parallax' => $parallax,
    'parallax_style' => $parallax_style,
    'disable_parallax_mobile' => $disable_parallax_mobile,
	  'slider_transition' => $slider_transition,
	  'overall_style' => $overall_style,
	  'slider_button_styling' => $slider_button_styling,
	  'loop' => $loop,
	  'fullscreen' => $fullscreen,
	  'button_sizing' => $button_sizing,
	  'location' => $location,
    'bg_animation' => $bg_animation,
    'caption_transition' => $caption_transition,
	  "tablet_header_font_size" => $tablet_header_font_size,
	  "tablet_caption_font_size" => $tablet_caption_font_size,
	  "phone_header_font_size" => $phone_header_font_size,
	  "phone_caption_font_size" => $phone_caption_font_size
	);
  
	 
	return do_shortcode(nectar_slider_display($slider_config));
}

add_shortcode('nectar_slider', 'nectar_slider_processing');



#-----------------------------------------------------------------#
# Social Buttons
#-----------------------------------------------------------------# 
function nectar_social_buttons($atts, $content = null) {
	extract(shortcode_atts(array("full_width_icons" => "", "hide_share_count" => "true", "nectar_love" => 'false', "facebook" => 'false', "twitter" => 'false', "google_plus" => 'false', "linkedin" => 'false', "nectar-love" => 'false', "pinterest" => 'false'), $atts));  
    
	$fw_class = ($full_width_icons == 'true') ? ' full-width' : null;
	$hide_share_count_class = ( $hide_share_count == 'true') ? ' hide-share-count' : null;
	
	$fw_items = 0;
	if($nectar_love == 'true') $fw_items += 1;
	if($facebook == 'true') $fw_items += 1;
	if($twitter == 'true') $fw_items += 1;
	if($google_plus == 'true') $fw_items += 1;
	if($linkedin == 'true') $fw_items += 1;
	if($pinterest == 'true') $fw_items += 1;
	
	global $post;
	
	$buttons = '<div class="nectar-social '. $hide_share_count_class . $fw_class.' items_'.$fw_items.'">';
	
    if($nectar_love == 'true'){
		$buttons .= '<span class="n-shortcode">'.nectar_love('return').'</span>';
    }
	
	if($facebook == 'true'){
    	$buttons .= "<a class='facebook-share nectar-sharing' href='#' title='".esc_html__( 'Share this', 'salient')."'> <i class='fa fa-facebook'></i> <span class='count'></span></a>";
    }
	
	if($twitter == 'true'){
    	$buttons .= "<a class='twitter-share nectar-sharing' href='#' title='".esc_html__( 'Tweet this', 'salient')."'> <i class='fa fa-twitter'></i> <span class='count'></span></a>";
    }

	if($google_plus == 'true'){
    	$buttons .= "<a class='google-plus-share nectar-sharing-alt' href='#' title='".esc_html__( 'Share this', 'salient')."'> <i class='fa fa-google-plus'></i> <span class='count'></span></a>";
    }
	
	if($linkedin == 'true'){
    	$buttons .= "<a class='linkedin-share nectar-sharing' href='#' title='".esc_html__( 'Share this', 'salient')."'> <i class='fa fa-linkedin'></i> <span class='count'></span></a>";
    }
	
	if($pinterest == 'true'){
    	$buttons .= "<a class='pinterest-share nectar-sharing' href='#' title='".esc_html__( 'Pin this', 'salient')."'> <i class='fa fa-pinterest'></i> <span class='count'></span></a>";
    }
	
	$buttons .= '</div>';
	
    return $buttons;
}
add_shortcode('social_buttons', 'nectar_social_buttons');


#-----------------------------------------------------------------#
# Portfolio/Blog
#-----------------------------------------------------------------# 



//Portfolio
function nectar_portfolio_processing($atts, $content = null) {
	extract(shortcode_atts(array("layout" => '3', 'category' => 'all', 'project_style' => '1', 'project_offset' => '0', 'bypass_image_cropping' => '', 'item_spacing' => 'default','load_in_animation' => 'none','starting_category' => '', 'filter_alignment' => 'default', 'filter_color' => 'default' ,'masonry_style' => '0', 'enable_sortable' => '0', 'pagination_type' => '', 'constrain_max_cols' => 'false', 'remove_column_padding' => 'false', 'horizontal_filters' => '0','lightbox_only' => '0', 'enable_pagination' => '0', 'projects_per_page' => '-1'), $atts));   
	
	global $post;
	global $nectar_options;
	
	//calculate cols
	switch($layout){
    case '2':
			$cols = 'cols-2';
			break; 
		case '3':
			$cols = 'cols-3';
			break; 
		case '4':
			$cols = 'cols-4';
			break; 
		case 'fullwidth':
			$cols = 'elastic';
			break; 
		case 'constrained_fullwidth':
			$cols = 'elastic';
			break; 
	}
		
	switch($cols){
    case 'cols-2':
			$span_num = 'span_6';
			break; 
		case 'cols-3':
			$span_num = 'span_4';
			break; 
		case 'cols-4':
			$span_num = 'span_3';
			break; 
		case 'elastic':
			$span_num = 'elastic-portfolio-item';
			break; 
			
	}
		
	if($masonry_style == 'true' && $project_style == '6' && ($layout != 'fullwidth' && $layout != 'constrained_fullwidth' && $bypass_image_cropping != 'true')) $masonry_style = 'false';

	$masonry_layout = ($masonry_style == 'true') ? 'true' : 'false';
	$masonry_sizing_type = (!empty($nectar_options['portfolio_masonry_grid_sizing']) && $nectar_options['portfolio_masonry_grid_sizing'] == 'photography') ? 'photography' : 'default';
	$constrain_col_class = (!empty($constrain_max_cols) && $constrain_max_cols == 'true') ? ' constrain-max-cols' : null ;
	$infinite_scroll_class = null;

	//disable masonry for default project style fullwidtrh
	if($project_style == '1' && $cols == 'elastic' && $bypass_image_cropping != 'true') $masonry_layout = 'false';
	
	$filters_id = ($horizontal_filters == 'true') ? 'portfolio-filters-inline' : 'portfolio-filters';
	
	if($pagination_type == 'infinite_scroll' && $enable_pagination == 'true') {
		$infinite_scroll_class = ' infinite_scroll';
	}

	ob_start(); 
	
	if( $enable_sortable == 'true' && $horizontal_filters == 'true') {

		$filters_width = (!empty($nectar_options['header-fullwidth']) && $nectar_options['header-fullwidth'] == '1' && $cols == 'elastic') ? 'full-width-content ': 'full-width-section ';
		if($layout == 'constrained_fullwidth') $filters_width = 'full-width-section';

	 	?>
		<div class="<?php echo esc_attr( $filters_id ) . ' '; echo esc_attr( $filters_width );  if($layout == 'constrained_fullwidth') echo ' fullwidth-constrained '; if($span_num != 'elastic-portfolio-item' || $layout == 'constrained_fullwidth') echo 'non-fw'; ?>" data-alignment="<?php echo esc_attr( $filter_alignment ); ?>" data-color-scheme="<?php echo strtolower( esc_attr( $filter_color ) ); ?>">
			<div class="container <?php if($span_num == 'elastic-portfolio-item') { echo 'normal-container'; } ?>">
				<?php if($filter_alignment != 'center' && $filter_alignment != 'left') { ?> <span id="current-category"><?php echo esc_html__( 'All', 'salient'); ?></span> <?php } ?>
				<ul>
				   <?php if($filter_alignment != 'center' && $filter_alignment != 'left') { ?> <li id="sort-label"><?php echo (!empty($nectar_options['portfolio-sortable-text'])) ? $nectar_options['portfolio-sortable-text'] : esc_html__( 'Sort Portfolio','salient'); ?>:</li> <?php } ?>
				   <li><a href="#" data-filter="*"><?php echo esc_html__( 'All', 'salient'); ?></a></li>
               	   <?php wp_list_categories(array('title_li' => '', 'taxonomy' => 'project-type', 'show_option_none'   => '', 'walker' => new Walker_Portfolio_Filter())); ?>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	<?php } else if($enable_sortable == 'true' && $horizontal_filters != 'true') { ?>
		<div class="<?php echo esc_attr( $filters_id );?>" data-color-scheme="<?php echo strtolower( esc_attr($filter_color ) ); ?>">
			<a href="#" data-sortable-label="<?php echo (!empty($nectar_options['portfolio-sortable-text'])) ? $nectar_options['portfolio-sortable-text'] :'Sort Portfolio'; ?>" id="sort-portfolio"><span><?php echo (!empty($nectar_options['portfolio-sortable-text'])) ?  wp_kses_post( $nectar_options['portfolio-sortable-text'] ) : esc_html__( 'Sort Portfolio','salient'); ?></span> <i class="icon-angle-down"></i></a> 
			<ul>
			   <li><a href="#" data-filter="*"><?php echo esc_html__( 'All', 'salient'); ?></a></li>
           	   <?php wp_list_categories(array('title_li' => '', 'taxonomy' => 'project-type', 'show_option_none'   => '', 'walker' => new Walker_Portfolio_Filter())); ?>
			</ul>
		</div>
		<div class="clear portfolio-filter-clear"></div>
	<?php } ?>
	
	


	<div class="portfolio-wrap <?php if($project_style == '1' && $span_num == 'elastic-portfolio-item') echo 'default-style ';  if($project_style == '6' && $span_num == 'elastic-portfolio-item') echo 'spaced'; ?>">
			
			<?php 
			$default_loader_class = (empty($nectar_options['loading-image']) && !empty($nectar_options['theme-skin']) && $nectar_options['theme-skin'] == 'ascend') ? 'default-loader' : null; 
			$default_loader = (empty($nectar_options['loading-image']) && !empty($nectar_options['theme-skin']) && $nectar_options['theme-skin'] == 'ascend') ? '<span class="default-loading-icon spin"></span>' : null;?>

			<span class="portfolio-loading <?php echo esc_attr( $default_loader_class ); ?> <?php echo (!empty($nectar_options['loading-image-animation']) && !empty($nectar_options['loading-image'])) ? $nectar_options['loading-image-animation'] : null; ?>">  <?php echo $default_loader; // WPCS: XSS ok. ?> </span>

			
			<?php 
			//incase only all was selected
			if($category == 'all') {
				$category = null;
			}

		
			?>
			
			<div class="row portfolio-items <?php if($masonry_layout == 'true') echo 'masonry-items'; else { echo 'no-masonry'; } ?> <?php if($layout == 'constrained_fullwidth') echo ' fullwidth-constrained '; echo esc_attr( $infinite_scroll_class ); ?> <?php echo esc_attr( $constrain_col_class ); ?>" <?php if($layout != 'fullwidth') echo 'data-rcp="'. esc_attr( $remove_column_padding ) .'"'; ?> data-masonry-type="<?php echo esc_attr( $masonry_sizing_type ) ; ?>" data-ps="<?php echo esc_attr($project_style); ?>" data-starting-filter="<?php echo esc_attr( $starting_category ); ?>" data-gutter="<?php echo esc_attr( $item_spacing ) ; ?>" data-categories-to-show="<?php echo esc_attr( $category ); ?>" data-bypass-cropping="<?php echo esc_attr( $bypass_image_cropping ); ?>" data-lightbox-only="<?php echo esc_attr( $lightbox_only ); ?>" data-col-num="<?php echo esc_attr( $cols ); ?>">
				<?php 
				

				$posts_per_page = (!empty($projects_per_page)) ? $projects_per_page : '-1';

				if ( get_query_var('paged') ) {
				  $paged = get_query_var('paged');
				} elseif ( get_query_var('page') ) {
				  $paged = get_query_var('page');
				} else {
				  $paged = 1;
				}
	       
        //remove offset for pagination
        if($enable_pagination == 'true') {
          $project_offset = '';
        }
         
				$portfolio_arr = array(
					'posts_per_page' => $posts_per_page,
					'post_type' => 'portfolio',
					'project-type'=> $category,
          'offset' => $project_offset, 
					'paged'=> $paged
				);
				
				query_posts($portfolio_arr);

 				if(have_posts()) : while(have_posts()) : the_post(); ?>
					
					<?php 
						
					   $terms = get_the_terms($post->id,"project-type");
					   $project_cats = NULL;
					   
					   if ( !empty($terms) ){
					      foreach ( $terms as $term ) {
					        $project_cats .= strtolower($term->slug) . ' ';
					      }
					   }
					  

					  global $post;

					  $masonry_item_sizing = ($masonry_layout == 'true') ? get_post_meta($post->ID, '_portfolio_item_masonry_sizing', true) : null;
	                  if(empty($masonry_item_sizing) && $masonry_layout == 'true') $masonry_item_sizing = 'regular';

					  $masonry_item_content_pos = get_post_meta($post->ID, '_portfolio_item_masonry_content_pos', true);
					  if(empty($masonry_item_content_pos)) $masonry_item_content_pos = 'middle';

					  $masonry_sizing_type = (!empty($nectar_options['portfolio_masonry_grid_sizing']) && $nectar_options['portfolio_masonry_grid_sizing'] == 'photography') ? 'photography' : 'default';

					  //no tall size for photography
					  if($masonry_sizing_type == 'photography' && $masonry_item_sizing == 'tall') $masonry_item_sizing = 'wide_tall';

					  $custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
					  $the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
					  
					  $project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);
					  $project_image_caption = get_post(get_post_thumbnail_id())->post_content;
					  $project_image_caption = strip_tags($project_image_caption);
					  
					  $project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);
					  $project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
					  $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);
            
            $customProjectClass = get_post_meta($post->ID, '_nectar_project_css_class', true);
            if(!empty($customProjectClass)) $customProjectClass = ' ' . sanitize_text_field($customProjectClass);

					  $thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
					  if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
						    $thumb_size = $thumb_size.'_photography';

							//no tall size in photography
							if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
					   }


					    //adaptive image sizing
						$image_sizes = null;
						$image_srcset = null;
            
            //still do basic check for custom thumbnail setup
            if($masonry_layout == 'false' || $layout == '2' || $layout == '3' || $layout == '4') {
              if($layout == '2') {
                $image_sizes = 'sizes="(min-width: 1000px) 50vw, (min-width: 690px) 50vw, 100vw"';
              }
              else if($layout == '3') {
                $image_sizes = 'sizes="(min-width: 1000px) 33.3vw, (min-width: 690px) 50vw, 100vw"';
              } else if($layout == '4') {
                $image_sizes = 'sizes="(min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
              } else if($layout == 'fullwidth' && $constrain_max_cols != 'true') {
                $image_sizes = 'sizes="(min-width: 1300px) 20vw, (min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
              } else if($layout == 'fullwidth' && $constrain_max_cols == 'true') {
                $image_sizes = 'sizes="(min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
              }
            }
            

						if(has_post_thumbnail()) {

							$featured_ID = get_post_thumbnail_id( $post->ID );

							$image_meta = wp_get_attachment_metadata($featured_ID);

							$regular_size = wp_get_attachment_image_src($featured_ID, $thumb_size, array('title' => ''));
							$small_size = null;
              $large_size = null;
							
							if($thumb_size == 'tall') {
								
								if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size]))
									$small_size = wp_get_attachment_image_src($featured_ID, $thumb_size, array('title' => ''));

							} else if($thumb_size == 'wide_tall') {
								
								if(!empty($image_meta['sizes']) && !empty($image_meta['sizes']['regular']))
									$small_size = wp_get_attachment_image_src($featured_ID,'regular', array('title' => ''));

							} else if($thumb_size == 'wide_tall_photography') {
								
								if(!empty($image_meta['sizes']) && !empty($image_meta['sizes']['regular_photography']))
									$small_size = wp_get_attachment_image_src($featured_ID,'regular_photography', array('title' => ''));

							} else if($thumb_size == 'wide' || $thumb_size == 'wide_photography' || $thumb_size == 'regular' || $thumb_size == 'regular_photography') {
								
								if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size.'_small']))
									$small_size = wp_get_attachment_image_src($featured_ID, $thumb_size.'_small', array('title' => ''));
							}

						

							if($masonry_layout == 'false' || $layout == '2' || $layout == '3' || $layout == '4') {
                if($layout == '2') {
									$image_sizes = 'sizes="(min-width: 1000px) 50vw, (min-width: 690px) 50vw, 100vw"';
								} else if($layout == '3') {
									$image_sizes = 'sizes="(min-width: 1000px) 33.3vw, (min-width: 690px) 50vw, 100vw"';
								} else if($layout == '4') {
									$image_sizes = 'sizes="(min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
								} else if($layout == 'fullwidth' && $constrain_max_cols != 'true') {
									$image_sizes = 'sizes="(min-width: 1300px) 20vw, (min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
								} else if($layout == 'fullwidth' && $constrain_max_cols == 'true') {
									$image_sizes = 'sizes="(min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
								}

								$regular_size = wp_get_attachment_image_src($featured_ID, 'portfolio-thumb', array('title' => ''));

								if(!empty($image_meta['sizes']) && !empty($image_meta['sizes']['portfolio-thumb_small'])) {
									$small_size = wp_get_attachment_image_src($featured_ID, 'portfolio-thumb_small', array('title' => ''));
                }
                if(!empty($image_meta['sizes']) && !empty($image_meta['sizes']['portfolio-thumb_large'])) {
									$large_size = wp_get_attachment_image_src($featured_ID, 'portfolio-thumb_large', array('title' => ''));
                }
                
                $large_size = ($large_size) ? $large_size[0] .' 900w, ' : null; 
								$regular_size = ($regular_size) ? $regular_size[0] .' 600w, ' : null; 
								$small_size = ($small_size) ? $small_size[0] .' 400w' : null; 

								$image_srcset = 'srcset="'.$large_size.$regular_size.$small_size.'"';

							} else if($masonry_layout == 'true' && $masonry_sizing_type != 'photography')  {

								if($constrain_max_cols != 'true') {
									//no column constraint
									if($thumb_size == 'regular' || $thumb_size == 'tall') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 500w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 350w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';
										
										$image_sizes = 'sizes="(min-width: 1600px) 20vw, (min-width: 1300px) 25vw, (min-width: 1000px) 33.3vw, (min-width: 690px) 50vw, 100vw"';

									} else if($thumb_size == 'wide_tall') {
										
										$regular_size = ($regular_size) ? $regular_size[0] .' 1000w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 500w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1600px) 40vw, (min-width: 1300px) 50vw, (min-width: 1000px) 66.6vw, (min-width: 690px) 100vw, 100vw"';
									} 
									else if($thumb_size == 'wide') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 1000w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 670w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1600px) 40vw, (min-width: 1300px) 50vw, (min-width: 1000px) 66.6vw, (min-width: 690px) 100vw, 100vw"';
									}

								} else {
									//constrained to 4 cols
									if($thumb_size == 'regular' || $thumb_size == 'tall') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 500w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 350w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
									} else if($thumb_size == 'wide_tall') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 1000w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 500w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 50vw, (min-width: 690px) 100vw, 100vw"';

									} else if($thumb_size == 'wide') {


										$regular_size = ($regular_size) ? $regular_size[0] .' 1000w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 670w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 50vw, (min-width: 690px) 100vw, 100vw"';
									}
								}
								
							} else if($masonry_layout == 'true' && $masonry_sizing_type == 'photography') {

								if($constrain_max_cols != 'true') {
									//no column constraint
									if($thumb_size == 'regular_photography' || $thumb_size == 'tall_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 450w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 350w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1600px) 16.6vw, (min-width: 1300px) 20vw, (min-width: 1000px) 25vw, (min-width: 690px) 50vw, 100vw"';
									} else if($thumb_size == 'wide_tall_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 900w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 450w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1600px) 33.3vw, (min-width: 1300px) 40vw, (min-width: 1000px) 50vw, 100vw"';
									} else if( $thumb_size == 'wide_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 900w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 700w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1600px) 33.3vw, (min-width: 1300px) 40vw, (min-width: 1000px) 50vw, 100vw"';
									}
								} else {
									//constrained to 4 cols
									if($thumb_size == 'regular_photography' || $thumb_size == 'tall_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 450w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 350w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 20vw, (min-width: 690px) 50vw, 100vw"';
									} else if($thumb_size == 'wide_tall_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 900w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 450w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 40vw, (min-width: 690px) 100vw, 100vw"';
									} else if($thumb_size == 'wide_photography') {

										$regular_size = ($regular_size) ? $regular_size[0] .' 900w' : null; 
										$small_size = ($small_size) ? ', '. $small_size[0] .' 700w' : null; 
										$image_srcset = 'srcset="'.$regular_size.$small_size.'"';

										$image_sizes = 'sizes="(min-width: 1000px) 40vw, (min-width: 690px) 100vw, 100vw"';
									}
								}
							}
						}
          

						

					?>
					
					<div class="col <?php echo esc_attr( $span_num ) . ' '. esc_attr( $masonry_item_sizing ) . esc_attr( $customProjectClass ); ?> element <?php echo esc_attr( $project_cats ); ?>"  data-project-cat="<?php echo esc_attr( $project_cats ); ?>" <?php if(!empty($project_accent_color)) { echo 'data-project-color="' . esc_attr( $project_accent_color ) .'"'; } else { echo 'data-default-color="true"';} ?> data-title-color="<?php echo esc_attr( $project_title_color ); ?>" data-subtitle-color="<?php echo esc_attr( $project_subtitle_color ); ?>">
						
						<div class="inner-wrap animated" data-animation="<?php echo esc_attr( $load_in_animation ); ?>">

						<?php //project style 1
							
							if($project_style == '1') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
								
							<div class="work-item style-1" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								 
								<?php
				 				
				 				$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {

										//create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
  										$image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
                  
										if(!empty($image_src)) $image_src = $image_src[0];

							      	 	$project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

										echo $project_featured_img; // WPCS: XSS ok.
									} 
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img class="skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="no image added yet." />';
									 }   
									
								} ?>
								
								<div class="work-info-bg"></div>
								<div class="work-info"> 
									
									<?php
									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_attr( $the_project_link ) .'"></a>';
										echo '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div></div></div>';
									//default
									} else { ?>

										<div class="vert-center">
											<?php 
											
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {

										    	echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
					
									        } 
											
											//image
										    else {

										       echo '<a href="'. esc_url( $featured_image[0] ) .'"'; 
										       if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'"';
										       echo ' class="pretty_photo default-link">'.esc_html__("View Larger", 'salient').'</a> ';
										    }
											
											if($lightbox_only != 'true') {
										    	echo '<a class="default-link" href="' . esc_url( $the_project_link ) . '">'.esc_html__("More Details", 'salient').'</a>'; 
										    } ?>
										    
										</div><!--/vert-center-->
									</div>
								</div><!--work-item-->
								
								<div class="work-meta">
									<h4 class="title"><?php the_title(); ?></h4>
									
									<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; ?>
									
								</div>
								<div class="nectar-love-wrap">
									<?php if( function_exists('nectar_love') ) nectar_love(); ?>
								</div><!--/nectar-love-wrap-->	

							<?php } 
						
						  } //project style 1 
						
						
						//project style 2
						else if($project_style == '2') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-2" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										 
										 //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
										   $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
                    
										if(!empty($image_src)) $image_src = $image_src[0];
                        
                        $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

										echo $project_featured_img; // WPCS: XSS ok.

									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img class="skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="no image added yet." />';
									 }   
									
								} ?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">
									
									<?php
									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else { ?>

										
										<?php if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {
				
												echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);	 
											     
									        } else { 
									        	
                            if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                              <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                            <?php } else { ?>
                            	<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ).'" '; ?> class="pretty_photo"></a>  
                            <?php }

									         } 

											  }

										 } ?>
									
		
									<div class="vert-center">
										<?php 
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="custom-content">' . do_shortcode($custom_content) . '</div>';
										} else { ?>	
											<h3><?php echo get_the_title(); ?></h3> 
											<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; 
										} ?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 2 
						
												
						
						else if($project_style == '3') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-3" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>" data-text-align="<?php echo esc_attr( $masonry_item_content_pos ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										
										 //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;

                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
                      $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
										
										if(!empty($image_src)) $image_src = $image_src[0];

                      $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                    echo $project_featured_img; // WPCS: XSS ok.

									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" class="no-img skip-lazy" alt="no image added yet." />';
									 }   
									
								} ?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">
									
									<?php
									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else {

										 if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ) ; ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {
				
												  echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
											     
									     } else {
									        	
                            if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                              <a href="<?php echo esc_url( $the_project_link ) ; ?>"></a>
                            <?php } else { ?>
									        	  <a href="<?php echo esc_url( $featured_image[0] ); ?>"  <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
									        	 <?php  } 
									       } 

									     } 

									} ?>

									<div class="vert-center">
										<?php 
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="custom-content">' . do_shortcode($custom_content) . '</div>';
										} else { ?>	
											<h3><?php echo get_the_title(); ?> </h3> 
											<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; ?>
										<?php } ?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 3 
						
						
						else if($project_style == '4') { 
							
							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>

							<div class="work-item style-4" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										 
										 //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
										   $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
                    
										if(!empty($image_src)) $image_src = $image_src[0];

                      $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                      echo $project_featured_img; // WPCS: XSS ok.

									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" class="no-img skip-lazy" alt="no image added yet." />';
									 }   
									
								} 

								if(!empty($using_custom_content) && $using_custom_content == 'on' && !empty($project_accent_color)) echo '<div class="work-info-bg"></div>'; ?>

								<div class="work-info">
									
									<?php

									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else {

										 if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {
				
												   echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);

									     } else { 
									        	
                           if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                             <a href="<?php echo esc_url( $the_project_link ) ; ?>"></a>
                            <?php } else { ?>
  									        	<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
                            <?php } 
                          
									         } 

											 }

										} 
									
									  if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div>';
										} else { ?>	

										<div class="bottom-meta">
											<h3><?php echo get_the_title(); ?> </h3> 
											<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; ?>
										</div><!--/bottom-meta-->

									<?php } ?>
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 4 

						else if($project_style == '5') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-3-alt" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>" data-text-align="<?php echo esc_attr( $masonry_item_content_pos ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										 
										 //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
  										$image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
										if(!empty($image_src)) $image_src = $image_src[0];

                        $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                    echo $project_featured_img; // WPCS: XSS ok.
										
									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" class="no-img skip-lazy" alt="'.get_the_title().'" />';
									 }   
									
								} ?>

								<div class="work-info-bg"></div>
								<div class="work-info">
									
									<?php 

									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else {

										if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {
				
											    
											   	echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
											     
									       } else { 
                            
                            
                            if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                              <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                              <?php } else { ?>
									        	  <a href="<?php echo esc_url( $featured_image[0] ); ?>"  <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
                            <?php } 
                          
									       }

										   }

									} ?>
									
		
									<div class="vert-center">
										<?php 
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="custom-content">' . do_shortcode($custom_content) . '</div>';
										} else { ?>	
											<h3><?php echo get_the_title(); ?> </h3> 
											<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; ?>
										
										<?php }	?>
										
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 5 

						else if($project_style == '6') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-5" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>" data-text-align="<?php echo esc_attr( $masonry_item_content_pos ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								$parallax_images = get_post_meta($post->ID, '_nectar_3d_parallax_images', true); 

								if(!empty($parallax_images)) {

									echo '<div class="parallaxImg">';

									$images = explode( ',', $parallax_images);
									$i = 0;
									foreach ( $images as $attach_id ) {
										$i++;

										$img = wp_get_attachment_image_src(  $attach_id, $thumb_size );
										//add one sizer img
										if($i == 1) echo '<img class="sizer skip-lazy" src="'. esc_url( $img[0] ) .'" alt="'.get_the_title().'" />';
    									echo '<div class="parallaxImg-layer" data-img="'. esc_url( $img[0] ) .'" Layer-'.$i.'"></div>';

									}

									echo '</div>';

								} 
								//no parallax images set
								else {
									
									if (!empty($custom_thumbnail)) {

										echo '<img class="sizer skip-lazy" src="'. esc_url( $custom_thumbnail ) .'" alt="'.get_the_title().'" />';

										echo '<div class="parallaxImg">';
										echo '<div class="parallaxImg-layer" data-img="'. esc_url( $custom_thumbnail ) .'"></div>';
										echo '<div class="parallaxImg-layer"><div class="bg-overlay"></div> <div class="work-meta"><div class="inner">';
										echo '	<h4 class="title"> '.get_the_title().'</h4>';
													
												if(!empty($project_excerpt)) echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>';  
												elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; 
													
										echo '</div></div></div></div>';
										
									}

									else if ( has_post_thumbnail() ) {

										$thumbnail_id = get_post_thumbnail_id($post->ID);
                    
                    if($bypass_image_cropping == 'true') {
                      $thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, 'full');
                    } else {
										   $thumbnail_url = wp_get_attachment_image_src($thumbnail_id,$thumb_size); 
                    }

										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
                    
                    if($bypass_image_cropping == 'true') {
                      echo '<img class="sizer skip-lazy" src="'. esc_url( $thumbnail_url[0] ) .'" alt="'.get_the_title().'" />';
                    } else {
										echo '<img class="sizer skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="'.get_the_title().'" />';
                    }

										echo '<div class="parallaxImg">';
										echo '<div class="parallaxImg-layer" data-img="'. esc_url( $thumbnail_url[0] ) .'"></div>';
										echo '<div class="parallaxImg-layer"><div class="bg-overlay"></div> <div class="work-meta"><div class="inner">';
										echo '	<h4 class="title"> '.get_the_title().'</h4>';
													
												if(!empty($project_excerpt)) echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>';  
												elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; 
													
										echo '</div></div></div></div>';
									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}

										echo '<img class="sizer skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="'.get_the_title().'" />';

										echo '<div class="parallaxImg">';
										echo '<div class="parallaxImg-layer" data-img="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'"></div>';
										echo '<div class="parallaxImg-layer"><div class="bg-overlay"></div> <div class="work-meta"><div class="inner">';
										echo '	<h4 class="title"> '.get_the_title().'</h4>';
													
												if(!empty($project_excerpt)) echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>';  
												elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; 
													
										echo '</div></div></div></div>';

									 }   
								}

								if($lightbox_only != 'true') { ?>
											
									<a href="<?php echo esc_url( $the_project_link ) ; ?>"></a>
								
								<?php } else {
									 
									$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
									$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
									$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
									
									//video 
								    if( !empty($video_embed) || !empty($video_m4v) ) {

										   echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
		
							      } else { 
							        	
                        
                        if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                          <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                          <?php } else { ?>
							        	  <a href="<?php echo esc_url( $featured_image[0] ); ?>"  <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
							        	 <?php } 
                        
							       }

								   }
									
								?>

							
							</div><!--work-item-->

						
							
						<?php } //project style 6 



						//project style 7
						else if($project_style == '7') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-2" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										 
										  //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
										   $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
                    
										if(!empty($image_src)) $image_src = $image_src[0];

                      $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                    echo $project_featured_img; // WPCS: XSS ok.
										
									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="no image added yet." />';
									 }   
									
								} ?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">
									
									<?php
									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else { ?>

										
										<?php if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										    if( !empty($video_embed) || !empty($video_m4v) ) {

												   echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);

									      } else { 
									        	
                            
                           if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                            <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                           <?php } else { ?>
									        	<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
                            <?php } 

									         } 

											  }

										 } ?>
									
		
									<div class="vert-center">
										<?php 
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="custom-content">' . do_shortcode($custom_content) . '</div>';
										} else { ?>	
											<h3><?php echo get_the_title(); ?></h3> 
											<?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; 
										} ?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 7 




						//project style 8
						else if($project_style == '8') { 

							$using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
							
							<div class="work-item style-2" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
								<?php
								$thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
								if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
									$thumb_size = $thumb_size.'_photography';

									//no tall size in photography
									if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
								}

								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" alt="'. get_the_title() .'" />';
								}
								else {
									
									if ( has_post_thumbnail() ) {
										 
										  //create featured image with srcset
										$image_width = null;
										$image_height = null;

										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
										if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

										$wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

										$image_src = null;
                    
                    if($bypass_image_cropping == 'true') {
                      $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                      
                      if (function_exists('wp_get_attachment_image_srcset')) {
                        $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset = 'srcset="';
                  				$image_srcset .= $image_srcset_values;
                  				$image_srcset .= '"';
                  			}
                      }
                      
                    } else {
										     $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                    }
                    
										if(!empty($image_src)) $image_src = $image_src[0];

                      $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                    echo $project_featured_img; // WPCS: XSS ok.
										
									} 
									
									//no image added
									else {
										switch($thumb_size) {
											case 'wide_photography':
												$no_image_size = 'no-portfolio-item-photography-wide.jpg';
												break;
											case 'regular_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide_tall_photography':
												$no_image_size = 'no-portfolio-item-photography-regular.jpg';
												break;
											case 'wide':
												$no_image_size = 'no-portfolio-item-wide.jpg';
												break;
											case 'tall':
												$no_image_size = 'no-portfolio-item-tall.jpg';
												break;
											case 'regular':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											case 'wide_tall':
												$no_image_size = 'no-portfolio-item-tiny.jpg';
												break;
											default:
												$no_image_size = 'no-portfolio-item-small.jpg';
												break;
										}
										 echo '<img class="skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ).'" alt="no image added yet." />';
									 }   
									
								} ?>

								<div class="work-info-bg"></div>
								<div class="work-info">
									
									<?php
									//custom content
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'. esc_url( $the_project_link ) .'"></a>';
									//default
									} else { ?>

										
										<?php if($lightbox_only != 'true') { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
										<?php } else {
											 
											$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
											$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
											$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
											
											//video 
										   if( !empty($video_embed) || !empty($video_m4v) ) {
				
												  echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);	 
											     
									      } else { 
									        	
                            
                           if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                            <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                           <?php } else { ?>
									        	<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
                           <?php } 
                          
									        } 

											  }

										 } ?>
									
									
									<div class="vert-center">
										<?php 
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											echo '<div class="custom-content">' . do_shortcode($custom_content) . '</div>';
										} else { ?>	
											<?php if(!empty($project_excerpt)) { echo '<p><span>'. wp_kses_post( $project_excerpt ) .'</span></p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p><span>' . get_the_date() . '</span></p>'; ?> 
											<h3><?php echo get_the_title(); ?></h3> 
											
											<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"/><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"/></svg><span class="line"></span></span>

										<?php } ?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
						<?php } //project style 8 
            
            
            
            else if($project_style == '9') { 

            $using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
            $custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true); ?>
              
            <div class="work-item style-1" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
               
              <?php
              
              $thumb_size = (!empty($masonry_item_sizing)) ? $masonry_item_sizing : 'portfolio-thumb';
              if($masonry_sizing_type == 'photography' && !empty($masonry_item_sizing)) {
                $thumb_size = $thumb_size.'_photography';

                //no tall size in photography
                if($thumb_size == 'tall_photography') $thumb_size = 'wide_tall_photography';
              }

              //custom thumbnail
              $custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
              
              if( !empty($custom_thumbnail) ){
                
               $image_srcset = '';
               $custom_thumbnail_id = fjarrett_get_attachment_id_from_url($custom_thumbnail); 
  
                if(!is_null($custom_thumbnail_id) && !empty($custom_thumbnail_id)) {

                		if (function_exists('wp_get_attachment_image_srcset')) {
                
                			  $image_srcset_values = wp_get_attachment_image_srcset( $custom_thumbnail_id, 'full');
                  			if($image_srcset_values) {
                  				$image_srcset .= 'srcset="' . $image_srcset_values . '" ';
                  			}
                		}
                  
                }
        
                echo '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check( esc_url( $custom_thumbnail ) ).'" '. $image_srcset. $image_sizes .' alt="'. get_the_title() .'" />';
              
              }
              
              else {
                
                if ( has_post_thumbnail() ) {

                  //create featured image with srcset
                  $image_width = null;
                  $image_height = null;

                  if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_width = $image_meta['sizes'][$thumb_size]['width'];
                  if(!empty($image_meta['sizes']) && !empty($image_meta['sizes'][$thumb_size])) $image_height = $image_meta['sizes'][$thumb_size]['height'];

                  $wp_img_alt_tag = get_post_meta( $featured_ID, '_wp_attachment_image_alt', true );

                  $image_src = null;
                  
                  if($bypass_image_cropping == 'true') {
                    $image_src = wp_get_attachment_image_src( $featured_ID, 'full');
                    
                    if (function_exists('wp_get_attachment_image_srcset')) {
                      $image_srcset_values = wp_get_attachment_image_srcset($featured_ID, 'full');
                      if($image_srcset_values) {
                        $image_srcset = 'srcset="';
                        $image_srcset .= $image_srcset_values;
                        $image_srcset .= '"';
                      }
                    }
                    
                  } else {
                    $image_src = wp_get_attachment_image_src( $featured_ID, $thumb_size);
                  }
                  
                  if(!empty($image_src)) $image_src = $image_src[0];

                    $project_featured_img = '<img class="size-'. esc_attr( $masonry_item_sizing ) .' skip-lazy" src="'. esc_url( $image_src ) .'" alt="'. esc_attr( $wp_img_alt_tag ) .'" height="'. esc_attr( $image_height ).'" width="'. esc_attr( $image_width ).'" ' . $image_srcset.' '.$image_sizes.' />';

                    echo $project_featured_img; // WPCS: XSS ok.
                } 
                //no image added
                else {
                  switch($thumb_size) {
                    case 'wide_photography':
                      $no_image_size = 'no-portfolio-item-photography-wide.jpg';
                      break;
                    case 'regular_photography':
                      $no_image_size = 'no-portfolio-item-photography-regular.jpg';
                      break;
                    case 'wide_tall_photography':
                      $no_image_size = 'no-portfolio-item-photography-regular.jpg';
                      break;
                    case 'wide':
                      $no_image_size = 'no-portfolio-item-wide.jpg';
                      break;
                    case 'tall':
                      $no_image_size = 'no-portfolio-item-tall.jpg';
                      break;
                    case 'regular':
                      $no_image_size = 'no-portfolio-item-tiny.jpg';
                      break;
                    case 'wide_tall':
                      $no_image_size = 'no-portfolio-item-tiny.jpg';
                      break;
                    default:
                      $no_image_size = 'no-portfolio-item-small.jpg';
                      break;
                  }
                   echo '<img class="skip-lazy" src="'.get_template_directory_uri().'/img/'. esc_attr( $no_image_size ) .'" alt="no image added yet." />';
                 }   
                
              } ?>
              

              <div class="work-info"> 
                
              
                <?php if($lightbox_only != 'true') { ?>
                  
                  <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                
                <?php } else {
                   
                  $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  							
                  $video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
                  $video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
                  
                  //video 
                    if( !empty($video_embed) || !empty($video_m4v) ) {
    
                      echo nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);	 

                    } else { 
                        
                        
                      if( !empty($custom_project_link) && strlen($custom_project_link) > 3 ) { ?>
                        <a href="<?php echo esc_url( $the_project_link ); ?>"></a>
                       <?php } else { ?>
                        <a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if(!empty($project_image_caption)) echo ' title="'. wp_kses_post( $project_image_caption ) .'" '; ?> class="pretty_photo"></a>
                      <?php } 
                      
                    } 

                 } ?>
                      
      
              </div>
                
              </div><!--work-item-->
              
              <div class="work-meta">
                <h4 class="title"><?php the_title(); ?></h4>
                
                <?php if(!empty($project_excerpt)) { echo '<p>'. wp_kses_post( $project_excerpt ) .'</p>'; } elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) echo '<p>' . get_the_date() . '</p>'; ?>
                
              </div>

          
          <?php } //project style 9  ?>
						
						
					</div><!--/inner-wrap-->
					</div><!--/col-->
					
				<?php endwhile; endif; ?>

			</div><!--/portfolio-->
	   </div><!--/portfolio wrap-->
		
		<?php 

		 if( !empty($nectar_options['portfolio_extra_pagination']) && $nectar_options['portfolio_extra_pagination'] == '1' && $enable_pagination == 'true'){
		 	
				    global $wp_query, $wp_rewrite;  
			 		
					$fw_pagination = ($span_num == 'elastic-portfolio-item') ? 'fw-pagination': null;
					$masonry_padding = ($project_style != '2') ? 'alt-style-padding' : null;
					
	                $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1; 
				    $total_pages = $wp_query->max_num_pages;  
					
					$permalink_structure = get_option('permalink_structure');
					
				    $query_type = (count($_GET)) ? '&' : '?';	
				    $get_compiled = array_keys($_GET);
				  	$first_get_param = reset($get_compiled); 
				    if($first_get_param == 'paged') $query_type = '?';

			   	    // $format = empty( $permalink_structure ) ? $query_type.'paged=%#%' : 'page/%#%/';  
				    if ($total_pages > 1){  
				      
					  echo '<div id="pagination" class="'. esc_attr( $fw_pagination ).' '. esc_attr( $masonry_padding ) . esc_attr( $infinite_scroll_class ) .'" data-is-text="'.esc_html__("All items loaded", 'salient').'">';
					   
				      echo paginate_links(array(  
				          'base' => get_pagenum_link(1) .'%_%', 
	    			      'format' => $query_type.'paged=%#%',
				          'current' => $current,  
				          'total' => $total_pages,  
				        )); 
						
					  echo  '</div>'; 
						
				    }  
			}
			//regular pagination
			else if($enable_pagination == 'true'){
				
				$fw_pagination = ($span_num == 'elastic-portfolio-item') ? 'fw-pagination': null;
				$masonry_padding = ($project_style == '1') ? 'alt-style-padding' : null;
				
				if( get_next_posts_link() || get_previous_posts_link() ) { 
					echo '<div id="pagination" class="'. esc_attr( $fw_pagination ) .' '. esc_attr( $masonry_padding ) . esc_attr( $infinite_scroll_class ) .'" data-is-text="'.esc_html__("All items loaded", 'salient').'">
					      <div class="prev">'.get_previous_posts_link('&laquo; Previous Entries').'</div>
					      <div class="next">'.get_next_posts_link('Next Entries &raquo;','').'</div>
				          </div>';
				
		        }
			}  
	
	
	
	wp_reset_query();
	
	$portfolio_markup = ob_get_contents();
	
	ob_end_clean();
	

	return $portfolio_markup;
}
add_shortcode('nectar_portfolio', 'nectar_portfolio_processing');




//blog
function nectar_blog_processing($atts, $content = null) {
	
	global $layout;
	
	extract(shortcode_atts(array(
    "layout" => 'std-blog-sidebar', 'blog_masonry_style' => 'inherit', 
    'auto_masonry_spacing' => '', 'auto_masonry_spacing' => '', 
    'blog_standard_style' => 'inherit', 'enable_ss' => '', 
    'post_offset' => '', 'order' => 'DESC', 
    'orderby' => 'date', 'category' => 'all', 
    'enable_pagination' => 'false', 'load_in_animation' => 'none', 
    'posts_per_page' => '10', 'pagination_type' => '',
    'blog_remove_post_date' => '', 'blog_remove_post_author' => '',
    'blog_remove_post_comment_number' => '', 'blog_remove_post_nectar_love' => ''
  ), $atts));  
	
  
  if($blog_remove_post_date == 'true') { $blog_remove_post_date = '1'; }
  if($blog_remove_post_author == 'true') { $blog_remove_post_author = '1'; }
  if($blog_remove_post_comment_number == 'true') { $blog_remove_post_comment_number = '1'; }
  if($blog_remove_post_nectar_love == 'true') { $blog_remove_post_nectar_love = '1'; }
	
	ob_start(); ?>
	
	<div class="row">
	
	 <?php $nectar_options = get_nectar_theme_options(); 

		$masonry_class = null;
		$infinite_scroll_class = null;
		$full_width_article = ($posts_per_page == 1) ? 'full-width-article': null;

		if($blog_standard_style != 'inherit') {
			$blog_standard_type = $blog_standard_style;
		} else {
			$blog_standard_type = (!empty($nectar_options['blog_standard_type'])) ? $nectar_options['blog_standard_type'] : 'classic';
		}

		$GLOBALS['nectar_blog_std_style'] = $blog_standard_type;
		$GLOBALS['nectar_blog_masonry_style'] = 'inherit';

		//enqueue masonry script if selected
		if($layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width') {
			$masonry_class = 'masonry';
		}
		
		if($pagination_type == 'infinite_scroll' && $enable_pagination == 'true') {
			$infinite_scroll_class = ' infinite_scroll';
		}
		
		if($masonry_class != null) {
			if($blog_masonry_style != 'inherit') {
				$masonry_style = $blog_masonry_style;
			} else {
				$masonry_style = (!empty($nectar_options['blog_masonry_type'])) ? $nectar_options['blog_masonry_type']: 'classic';
			}

			$GLOBALS['nectar_blog_masonry_style'] = $masonry_style;
			
		}
		else {
			$masonry_style = null;
		}
    
    //std class
		if($blog_standard_type == 'minimal' && $layout == 'std-blog-fullwidth')
			$std_minimal_class = 'standard-minimal full-width-content';
		else if($blog_standard_type == 'minimal' && $layout == 'std-blog-sidebar')
			$std_minimal_class = 'standard-minimal';
		else
			$std_minimal_class = '';

    if($masonry_style == null && $blog_standard_type == 'featured_img_left')
			$std_minimal_class = 'featured_img_left';
		
      

		if($layout == 'std-blog-sidebar' || $layout == 'masonry-blog-sidebar'){
			echo '<div class="post-area col '.$std_minimal_class.' span_9 '.$masonry_class.' '.$masonry_style.' '.$infinite_scroll_class.'" data-ams="'.$auto_masonry_spacing.'" data-remove-post-date="'.$blog_remove_post_date.'" data-remove-post-author="'.$blog_remove_post_author.'" data-remove-post-comment-number="'.$blog_remove_post_comment_number.'" data-remove-post-nectar-love="'.$blog_remove_post_nectar_love.'"> <div class="posts-container" data-load-animation="'.$load_in_animation.'">';
		} else {
      
      if($layout == 'masonry-blog-full-screen-width' && $blog_masonry_style == 'auto_meta_overlaid_spaced' || $layout == 'masonry-blog-full-screen-width' && $blog_masonry_style == 'meta_overlaid') { echo '<div class="full-width-content blog-fullwidth-wrap meta-overlaid">'; }
      else if($layout == 'masonry-blog-full-screen-width') { echo '<div class="full-width-content blog-fullwidth-wrap">'; }
      
			echo '<div class="post-area col '.$std_minimal_class.' span_12 col_last '.$masonry_class.' '.$masonry_style.' '.$infinite_scroll_class.' '.$full_width_article.'" data-ams="'.$auto_masonry_spacing.'" data-remove-post-date="'.$blog_remove_post_date.'" data-remove-post-author="'.$blog_remove_post_author.'" data-remove-post-comment-number="'.$blog_remove_post_comment_number.'" data-remove-post-nectar-love="'.$blog_remove_post_nectar_love.'"> <div class="posts-container" data-load-animation="'.$load_in_animation.'">';
		
    }
			
			if ( get_query_var('paged') ) {
				  $paged = get_query_var('paged');
			} elseif ( get_query_var('page') ) {
				  $paged = get_query_var('page');
			} else {
				  $paged = 1;
			}
			
			//incase only all was selected
			if($category == 'all') {
				$category = null;
			}
      
      //remove offset for pagination
      if($enable_pagination == 'true') {
        $post_offset = '';
      }

	     
      if($orderby != 'view_count') {
        
  			$nectar_blog_arr = array(
  			  'posts_per_page' => $posts_per_page,
  				'post_type' => 'post',
          'order' => $order,
          'orderby' => $orderby,
          'offset' => $post_offset,
  				'category_name' => $category,
  				'paged'=> $paged
  			);
    
      } else {
        
        $nectar_blog_arr = array(
  			  'posts_per_page' => $posts_per_page,
  				'post_type' => 'post',
          'order' => $order,
          'orderby' => 'meta_value_num',
          'meta_key' => 'nectar_blog_post_view_count',
  				'category_name' => $category,
          'offset' => $post_offset,
  				'paged'=> $paged
  			);
        
      }

			query_posts($nectar_blog_arr);  
      
      add_filter('wp_get_attachment_image_attributes','nectar_remove_lazy_load_functionality');

			if(have_posts()) : while(have_posts()) : the_post(); ?>
				
				<?php 
				
				global $more;
				$more = 0;

       $nectar_post_format = (get_post_format() == 'image' || get_post_format() == 'aside') ? false : get_post_format();
			 get_template_part( 'includes/post-templates/entry', $nectar_post_format ); 
		

		    endwhile; endif; 
      
      remove_filter('wp_get_attachment_image_attributes','nectar_remove_lazy_load_functionality');
      
      ?>
			
			</div><!--/posts container-->
			
			<?php

			global $nectar_options;
			//extra pagination
			if( !empty($nectar_options['extra_pagination']) && $nectar_options['extra_pagination'] == '1' && $enable_pagination == 'true'){
				
				    global $wp_query, $wp_rewrite; 
	      
				    $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1; 
				    $total_pages = $wp_query->max_num_pages; 
				      
				    if ($total_pages > 1){  
				      
				      $permalink_structure = get_option('permalink_structure');
				      $query_type = (count($_GET)) ? '&' : '?';	
			      	$format = empty( $permalink_structure ) ? $query_type.'paged=%#%' : 'page/%#%/';  
					  
            if(defined('ICL_SITEPRESS_VERSION')) { $format = $query_type.'paged=%#%'; }
            
					  echo '<div id="pagination" data-is-text="'.esc_html__("All items loaded", 'salient').'">';
					   
				      echo paginate_links(array(  
				          'base' => get_pagenum_link(1) . '%_%',  
				          'format' => $format,  
				          'current' => $current,  
				          'total' => $total_pages,  
				        )); 
						
					  echo  '</div>'; 
						
				    }  
			}
			//regular pagination
			else if($enable_pagination == 'true'){
				
				if( get_next_posts_link() || get_previous_posts_link() ) { 
					echo '<div id="pagination" data-is-text="'.esc_html__("All items loaded", 'salient').'">
					      <div class="prev">'.get_previous_posts_link('&laquo; Previous Entries').'</div>
					      <div class="next">'.get_next_posts_link('Next Entries &raquo;','').'</div>
				          </div>';
				
		        }
			}
				
		?>
		
  </div><!--/post area-->
  
  <?php if($layout == 'masonry-blog-full-screen-width') { echo '</div>'; } ?>
		
	<?php  if($layout == 'std-blog-sidebar' || $layout == 'masonry-blog-sidebar') { ?>
		<div id="sidebar" data-nectar-ss="<?php echo esc_attr( $enable_ss ); ?>" class="col span_3 col_last">
			<?php dynamic_sidebar('blog-sidebar'); ?>
		</div><!--/span_3-->
   <?php } ?>

	</div>
	
	<?php 

	wp_reset_query();
  
	$blog_markup = ob_get_contents();
	
	ob_end_clean();
	
	return $blog_markup;
	
}
add_shortcode('nectar_blog', 'nectar_blog_processing');





//Recent Posts
function nectar_recent_posts($atts, $content = null) {
	extract(shortcode_atts(
    array("title_labels" => 'false',  'category' => 'all', 
    'order' => 'DESC', 'orderby' => 'date', 
    'hover_shadow_type' => 'default', 'button_color' => '', 
    'bg_overlay' => '', 'slider_size' => '600', 
    'mlf_navigation_location' => 'side', 'large_featured_padding' => '10%', 
    'color_scheme' => 'light','auto_rotate' => 'none', 
    'slider_above_text' => '', 'multiple_large_featured_num' => '4', 
    'posts_per_page' => '4', 'columns' => '4', 
    'style' => 'default', 'post_offset' => '0',
    'blog_remove_post_date' => '', 'blog_remove_post_author' => '',
    'blog_remove_post_comment_number' => '', 'blog_remove_post_nectar_love' => ''
  ), $atts));  
	
	global $post;  
	global $nectar_options;
  
  if( isset($_GET['vc_editable']) ) {
  	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
  	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
    if($nectar_using_VC_front_end_editor) {
      $auto_rotate = 'none';
    }
  }
	
	$posts_page_id = get_option('page_for_posts');
	$posts_page = get_page($posts_page_id);
	$posts_page_title = $posts_page->post_title;
	$posts_page_link = get_page_uri($posts_page_id);
	
	$title_label_output = null;
	$recent_posts_title_text = (!empty($nectar_options['recent-posts-title'])) ? $nectar_options['recent-posts-title'] :'Recent Posts';		
	$recent_posts_link_text = (!empty($nectar_options['recent-posts-link'])) ? $nectar_options['recent-posts-link'] :'View All Posts';		
	
  if($blog_remove_post_date == 'true') { $blog_remove_post_date = '1'; }
  if($blog_remove_post_author == 'true') { $blog_remove_post_author = '1'; }
  if($blog_remove_post_comment_number == 'true') { $blog_remove_post_comment_number = '1'; }
  if($blog_remove_post_nectar_love == 'true') { $blog_remove_post_nectar_love = '1'; }
  
	//incase only all was selected
	if($category == 'all') {
		$category = null;
	}
	
	if($style != 'slider' && $style != 'slider_multiple_visible' && $style != 'single_large_featured' && $style != 'multiple_large_featured') {

		($title_labels == 'true') ? $title_label_output = '<h2 class="uppercase recent-posts-title">'. wp_kses_post( $recent_posts_title_text ) .'<a href="'. esc_url( $posts_page_link ) .'" class="button"> / '. wp_kses_post( $recent_posts_link_text ) .'</a></h2>' : $title_label_output = null;
			
			ob_start(); 
			
			echo $title_label_output; // WPCS: XSS ok.
			$modded_style = $style;
      if($style == 'list_featured_first_row_tall') {
        $modded_style = 'list_featured_first_row';
      }
      ?>
			<div class="row blog-recent columns-<?php echo esc_attr( $columns ); ?>" data-style="<?php echo esc_attr( $modded_style ); ?>" data-color-scheme="<?php echo esc_attr( $color_scheme ); ?>" data-remove-post-date="<?php echo esc_attr( $blog_remove_post_date ); ?>" data-remove-post-author="<?php echo esc_attr( $blog_remove_post_author ); ?>" data-remove-post-comment-number="<?php echo esc_attr( $blog_remove_post_comment_number ); ?>" data-remove-post-nectar-love="<?php echo esc_attr($blog_remove_post_nectar_love ); ?>">
				
				<?php 
          
          $r_post_count = 0;

          if($orderby != 'view_count') {
            
            $recentBlogPosts = array(
  			      'showposts' => $posts_per_page,
  			      'category_name' => $category,
  			      'ignore_sticky_posts' => 1,
  			      'offset' => $post_offset,
              'order' => $order,
              'orderby' => $orderby,
  			      'tax_query' => array(
  		              array( 'taxonomy' => 'post_format',
  		                  'field' => 'slug',
  		                  'terms' => array('post-format-link'),
  		                  'operator' => 'NOT IN'
  		                  )
  		              )
  			    );
            
          } else {

            $recentBlogPosts = array(
  			      'showposts' => $posts_per_page,
  			      'category_name' => $category,
  			      'ignore_sticky_posts' => 1,
  			      'offset' => $post_offset,
              'order' => $order,
              'orderby' => 'meta_value_num',
              'meta_key' => 'nectar_blog_post_view_count',
  			      'tax_query' => array(
  		              array( 'taxonomy' => 'post_format',
  		                  'field' => 'slug',
  		                  'terms' => array('post-format-link'),
  		                  'operator' => 'NOT IN'
  		                  )
  		              )
  			    );
            
          }
          
			    

				$recent_posts_query = new WP_Query($recentBlogPosts);  

				if( $recent_posts_query->have_posts() ) :  while( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post();  

        $r_post_count++;
        
				if($columns == '4') {
					$col_num = 'span_3';
				} else if($columns == '3') {
					$col_num = 'span_4';
				} else if($columns == '2') {
					$col_num = 'span_6';
				} else {
					$col_num = 'span_12';
				}
				
				?>

				<div <?php post_class('col'. ' '. $col_num); ?> >
					
					<?php 
						
						$wp_version = floatval(get_bloginfo('version'));
						
						if($style == 'default') {

							if(get_post_format() == 'video'){

								 if ( $wp_version < "3.6" ) {
									 $video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
										
						             if( !empty( $video_embed ) ) {
						                 echo '<div class="video-wrap">' . stripslashes(wp_specialchars_decode($video_embed)) . '</div>';
						             } else { 
						                 //nectar_video($post->ID); 
						             }
								 }
							  	 else {
									
									$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
								    $video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
								    $video_ogv = get_post_meta($post->ID, '_nectar_video_ogv', true); 
								    $video_poster = get_post_meta($post->ID, '_nectar_video_poster', true); 
								  
								    if( !empty($video_embed) || !empty($video_m4v) ){
				
						               $wp_version = floatval(get_bloginfo('version'));
												
									  //video embed
									  if( !empty( $video_embed ) ) {
										
							               echo '<div class="video">' . do_shortcode($video_embed) . '</div>';
										
							          } 
							          //self hosted video pre 3-6
							          else if( !empty($video_m4v) && $wp_version < "3.6") {
							        	
							          	   echo '<div class="video">'; 
							              	   //nectar_video($post->ID); 
										   echo '</div>'; 
										 
							          } 
							          //self hosted video post 3-6
							          else if($wp_version >= "3.6"){
						
							        	  if(!empty($video_m4v) || !empty($video_ogv)) {
							        		
											  $video_output = '[video ';
											
											  if(!empty($video_m4v)) { $video_output .= 'mp4="'. $video_m4v .'" '; }
											  if(!empty($video_ogv)) { $video_output .= 'ogv="'. $video_ogv .'"'; }
											
											  $video_output .= ' poster="'.$video_poster.'"]';
											
							        		  echo '<div class="video">' . do_shortcode($video_output) . '</div>';	
							        	  }
							          }
									
								   } // endif for if there's a video
									
							    } // endif for 3.6 
							    
							} //endif for post format video
							
							else if(get_post_format() == 'audio'){ ?>
								<div class="audio-wrap">		
									<?php 
									if ( $wp_version < "3.6" ) {
									    //nectar_audio($post->ID);
									} 
									else {
										$audio_mp3 = get_post_meta($post->ID, '_nectar_audio_mp3', true);
									    $audio_ogg = get_post_meta($post->ID, '_nectar_audio_ogg', true); 
										
										if(!empty($audio_ogg) || !empty($audio_mp3)) {
								        	
											$audio_output = '[audio ';
											
											if(!empty($audio_mp3)) { $audio_output .= 'mp3="'. $audio_mp3 .'" '; }
											if(!empty($audio_ogg)) { $audio_output .= 'ogg="'. $audio_ogg .'"'; }
											
											$audio_output .= ']';
											
							        		echo  do_shortcode($audio_output);	
							        	}
									} ?>
								</div><!--/audio-wrap-->
							<?php }
							
							else if(get_post_format() == 'gallery'){
								
								if ( $wp_version < "3.6" ) {
									
									
									if ( has_post_thumbnail() ) { echo get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')); }
									
								}
								
								else {
									
									$gallery_ids = grab_ids_from_gallery(); ?>
						
									<div class="flex-gallery"> 
											 <ul class="slides">
											 	<?php 
												foreach( $gallery_ids as $image_id ) {
												     echo '<li>' . wp_get_attachment_image($image_id, 'portfolio-thumb', false) . '</li>';
												} ?>
									    	</ul>
								   	 </div><!--/gallery-->

						   <?php }
										
							}
							
							else {
								if ( has_post_thumbnail() ) { echo '<a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')) . '</a>'; }
							}
					
						?>

							<div class="post-header">
								<h3 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>	
								<span class="meta-author"><?php the_author_posts_link(); ?> </span> <span class="meta-category"> | <?php the_category(', '); ?> </span> <span class="meta-comment-count"> | <a href="<?php comments_link(); ?>">
								<?php comments_number( esc_html__( 'No Comments','salient'), esc_html__( 'One Comment','salient'), '% '. esc_html__( 'Comments','salient') ); ?></a> </span>
							</div><!--/post-header-->
							
							<?php 
              $excerpt_length = (!empty($nectar_options['blog_excerpt_length'])) ? intval($nectar_options['blog_excerpt_length']) : 30; 
							echo '<div class="excerpt">' . nectar_excerpt($excerpt_length) . '</div>';

						} // default style
						else if($style == 'minimal') { ?>

							<a href="<?php the_permalink(); ?>"></a>
							<div class="post-header">
								<span class="meta"> <span> <?php echo get_the_date() . '</span> ' . esc_html__( 'in','salient'); ?> <?php the_category(', '); ?> </span> 
								<h3 class="title"><?php the_title(); ?></h3>	
							</div><!--/post-header-->
							<?php 
                $excerpt_length = (!empty($nectar_options['blog_excerpt_length'])) ? intval($nectar_options['blog_excerpt_length']) : 30; 
  							echo '<div class="excerpt">' . nectar_excerpt($excerpt_length) . '</div>';
              ?>
							<span><?php echo esc_html__( 'Read More','salient'); ?> <i class="icon-button-arrow"></i></span>

						<?php } else if($style == 'title_only') { ?>

							<a href="<?php the_permalink(); ?>"></a>
							<div class="post-header">
								<span class="meta"> <?php echo get_the_date(); ?> </span> 
								<h2 class="title"><?php the_title(); ?></h2>	
							</div><!--/post-header-->

						<?php } 
            
            else if($style == 'list_featured_first_row' || $style == 'list_featured_first_row_tall') { ?>
              
              <?php 
              
              $list_heading_tag = ($r_post_count <= $columns) ? 'h3' : 'h5';
              
              $list_featured_image_size = ($r_post_count <= $columns) ? 'portfolio-thumb' : 'nectar_small_square';
              
    
              $list_featured_image_class = ($r_post_count <= $columns) ? 'featured' : 'small';
              
              echo '<a class="full-post-link" href="' . esc_url(get_permalink()) . '"></a>';
              
              if ( has_post_thumbnail() ) { 
                if($style == 'list_featured_first_row_tall' && $r_post_count <= $columns){
                   echo'<a href="' . esc_url(get_permalink()) . '" class="'.$list_featured_image_class.'"><span class="post-featured-img" style="background-image: url('.get_the_post_thumbnail_url($post->ID, 'regular', array('title' => '')).');"></span></a>'; 
                } else {
                  echo '<a class="'.$list_featured_image_class.'" href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail($post->ID, $list_featured_image_size, array('title' => '')) . '</a>'; 
                }
              }
              else { echo '<a class="'.$list_featured_image_class.'" href="' . esc_url(get_permalink()) . '"></a>';  }
              ?>
							<div class="post-header <?php echo esc_attr( $list_featured_image_class ); ?>">
								
                <?php echo '<span class="meta-category">';
  							$categories = get_the_category();
  							if ( ! empty( $categories ) ) {
  								$output = null;
  							    foreach( $categories as $category ) {
  							        $output .= '<a class="'.$category->slug.'" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
  							        break;
                    }
  							    echo trim( $output);
  								}
  							echo '</span>'; ?>
								<?php echo '<' . $list_heading_tag . '> <a href="'.esc_url(get_permalink()).'">'. get_the_title() .'</a></'. $list_heading_tag .'>'; ?>
                  
							</div><!--/post-header-->
              
              <?php 
              if($r_post_count <= $columns) {
                $excerpt_length = (!empty($nectar_options['blog_excerpt_length'])) ? intval($nectar_options['blog_excerpt_length']) : 15; 
                echo '<div class="excerpt">'.nectar_excerpt($excerpt_length).'</div>';
              
              }

					 } 

						else if($style == 'classic_enhanced' || $style == 'classic_enhanced_alt') { 

							if($columns == '4') {
								$image_attrs =  array('title' => '', 'sizes' => '(min-width: 1300px) 25vw, (min-width: 1000px) 33vw, (min-width: 690px) 100vw, 100vw');
							} else if($columns == '3') {
								$image_attrs =  array('title' => '', 'sizes' => '(min-width: 1300px) 33vw, (min-width: 1000px) 33vw, (min-width: 690px) 100vw, 100vw');
							} else if($columns == '2') {
								$image_attrs =  array('title' => '', 'sizes' => '(min-width: 1600px) 50vw, (min-width: 1300px) 50vw, (min-width: 1000px) 50vw, (min-width: 690px) 100vw, 100vw');
							} else {
								$image_attrs =  array('title' => '', 'sizes' => '(min-width: 1000px) 100vw, (min-width: 690px) 100vw, 100vw');
							} ?>

							<div <?php post_class('inner-wrap'); ?>>

							<?php
              
              $post_link_target = (get_post_format() == 'link') ? 'target="_blank"' : '';
                
							if ( has_post_thumbnail() ) { 
								if($style == 'classic_enhanced') {
									echo '<a href="' . esc_url(get_permalink()) . '" '.$post_link_target.' class="img-link"><span class="post-featured-img">'.get_the_post_thumbnail($post->ID, 'portfolio-thumb', $image_attrs) .'</span></a>'; 
								} else if($style == 'classic_enhanced_alt') {
									$masonry_sizing_type = (!empty($nectar_options['portfolio_masonry_grid_sizing']) && $nectar_options['portfolio_masonry_grid_sizing'] == 'photography') ? 'photography' : 'default';
									$cea_size = ($masonry_sizing_type == 'photography') ? 'regular_photography' : 'tall';
									echo '<a href="' . esc_url(get_permalink()) . '" class="img-link" '.$post_link_target.'><span class="post-featured-img">'.get_the_post_thumbnail($post->ID, $cea_size, $image_attrs) .'</span></a>'; 
								}
							} ?>

							<?php
							echo '<span class="meta-category">';
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								$output = null;
							    foreach( $categories as $category ) {
							        $output .= '<a class="'.$category->slug.'" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
							    }
							    echo trim( $output);
								}
							echo '</span>'; ?>
								
							<a class="entire-meta-link" <?php echo $post_link_target; // WPCS: XSS ok. ?> href="<?php the_permalink(); ?>"></a>

							<div class="article-content-wrap">
								<div class="post-header">
									<span class="meta"> <?php echo get_the_date(); ?> </span> 
									<h3 class="title"><?php the_title(); ?></h3>	
								</div><!--/post-header-->
								<div class="excerpt">
									<?php 
                  $excerpt_length = (!empty($nectar_options['blog_excerpt_length'])) ? intval($nectar_options['blog_excerpt_length']) : 30; 
    							echo nectar_excerpt($excerpt_length);
                  ?>
								</div>
							</div>
							
							<div class="post-meta">
								<span class="meta-author"> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"> <i class="icon-default-style icon-salient-m-user"></i> <?php the_author(); ?></a> </span> 
								
								<?php if(comments_open()) { ?>
									<span class="meta-comment-count">  <a href="<?php comments_link(); ?>">
										<i class="icon-default-style steadysets-icon-chat-3"></i> <?php comments_number( '0', '1','%' ); ?></a>
									</span>
								<?php } ?>
								
								<div class="nectar-love-wrap">
									<?php if( function_exists('nectar_love') ) nectar_love(); ?>
								</div><!--/nectar-love-wrap-->	
							</div>

						</div>

						<?php }  ?>
					
				</div><!--/col-->
				
				<?php endwhile; endif; 
					  wp_reset_postdata();
				?>
		
			</div><!--/blog-recent-->
		
		<?php

		$recent_posts_content = ob_get_contents();
		
		ob_end_clean();
	
	} // regular recent posts
  
  else if($style == 'single_large_featured') { //single_large_featured
 
    ob_start(); 
      
    echo $title_label_output; // WPCS: XSS ok. ?>
    
    <?php 
      
      if($orderby != 'view_count') {
        
        $recentBlogPosts = array(
          'showposts' => 1,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => $orderby,
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
    } else {
      
        $recentBlogPosts = array(
          'showposts' => 1,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => 'meta_value_num',
          'meta_key' => 'nectar_blog_post_view_count',
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
      
    }
 
    $recent_posts_query = new WP_Query($recentBlogPosts);  
 
 
    $animate_in_effect = (!empty($nectar_options['header-animate-in-effect'])) ? $nectar_options['header-animate-in-effect'] : 'none';
    echo '<div id="'.uniqid('rps_').'" class="nectar-recent-posts-single_featured parallax_section" data-padding="'. esc_attr( $large_featured_padding ) .'" data-bg-overlay="'. esc_attr( $bg_overlay ) .'" data-height="'. esc_attr( $slider_size ) .'" data-animate-in-effect="'. esc_attr( $animate_in_effect ) .'" data-remove-post-date="'. esc_attr( $blog_remove_post_date ) .'" data-remove-post-author="'. esc_attr( $blog_remove_post_author ) .'" data-remove-post-comment-number="'.$blog_remove_post_comment_number.'" data-remove-post-nectar-love="'.$blog_remove_post_nectar_love.'">';

    $i = 0;
    if( $recent_posts_query->have_posts() ) :  while( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post(); global $post; ?>
 
        <?php 
          $bg = get_post_meta($post->ID, '_nectar_header_bg', true);
          $bg_color = get_post_meta($post->ID, '_nectar_header_bg_color', true);
          $bg_image_id = null;
          $featured_img = null;
          
          if(!empty($bg)){
            //page header
            $featured_img = $bg;
 
          } elseif(has_post_thumbnail($post->ID)) {
            $bg_image_id = get_post_thumbnail_id($post->ID);
            $image_src = wp_get_attachment_image_src($bg_image_id, 'full');
            $featured_img = $image_src[0];
          }
 
 
        ?>
 
        <div class="nectar-recent-post-slide <?php if($bg_image_id == null) echo 'no-bg-img'; ?> post-ref-<?php echo esc_attr($i); ?>">
 
          <div class="row-bg using-image" data-parallax-speed="fast"><div class="nectar-recent-post-bg" style=" <?php if(!empty($bg_color)) { ?> background-color: <?php echo esc_attr( $bg_color );?>; <?php } ?> background-image: url(<?php echo esc_url( $featured_img ) ;?>);" > </div></div>
 
          <?php 
 
          echo '<div class="recent-post-container container"><div class="inner-wrap">';

          
              $categories = get_the_category();
              if ( ! empty( $categories ) ) {
                $cat_output = null;
                  $i = 0;
                  foreach( $categories as $category ) {
                     $i++;
                     $cat_output .= '<a class="'.$category->slug.'" href="' . esc_url( get_category_link( $category->term_id ) ) . '"><span class="'.$category->slug.'">'.esc_html( $category->name ) .'</span></a>';
                     if($i > 0) break;  
                  }
    
              }
          
            
            echo '<div class="grav-wrap"><a href="'.get_author_posts_url($post->post_author).'">'.get_avatar( get_the_author_meta('email'), 70,  null, get_the_author() ). '</a><div class="text"><span>'.esc_html__( 'By','salient').' <a href="'.get_author_posts_url($post->post_author).'" rel="author">' .get_the_author().'</a></span><span> '.esc_html__( 'In','salient').'</span> '. trim( $cat_output) . '</div></div>'; 
            ?>
          
            <h2 class="post-ref-<?php echo esc_attr($i); ?>"><a href=" <?php echo esc_url(get_permalink()); ?>" class="full-slide-link"> <?php echo the_title(); ?> </a></h2>
            <?php echo '<div class="excerpt">' . nectar_excerpt(20) . '</div>';  ?>
          
            <?php 
            //stop regular grad class for material skin 
            $button_color = strtolower($button_color);
            $regular_btn_class = ' regular-button';
            
            if($button_color == 'extra-color-gradient-1' || $button_color == 'extra-color-gradient-2') {
              $regular_btn_class = '';
            }
            
          	if($nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-1') {
          		$button_color = 'm-extra-color-gradient-1';
          	} else if( $nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-2') {
          		$button_color = 'm-extra-color-gradient-2';
          	} 
            ?>
            <a class="nectar-button large regular <?php echo esc_attr( $button_color ) .  esc_attr( $regular_btn_class ); ?> has-icon" href="<?php echo esc_url(get_permalink()); ?>" data-color-override="false" data-hover-color-override="false" data-hover-text-color-override="#fff" ><span><?php echo esc_html__( 'Read More', 'salient'); ?></span> <i class="icon-button-arrow"></i></a>
            
          
          </div>
            
 
        </div>
 
        <?php $i++; ?>
 
    <?php endwhile; endif; 
 
        wp_reset_postdata();
  
     echo '</div></div>';
 
    wp_reset_query();
    
    $recent_posts_content = ob_get_contents();
    
    ob_end_clean();
  }
  
  else if($style == 'multiple_large_featured') { //multiple_large_featured
 
    ob_start(); 
      
    echo $title_label_output; // WPCS: XSS ok. ?>
    
    <?php 
      
      if($orderby != 'view_count') {
        $recentBlogPosts = array(
          'showposts' => $multiple_large_featured_num,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => $orderby,
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
      } else {
        
        $recentBlogPosts = array(
          'showposts' => $multiple_large_featured_num,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => 'meta_value_num',
          'meta_key' => 'nectar_blog_post_view_count',
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
        
      }
    $recent_posts_query = new WP_Query($recentBlogPosts);  
 
    $button_color = strtolower($button_color);
    $animate_in_effect = (!empty($nectar_options['header-animate-in-effect'])) ? $nectar_options['header-animate-in-effect'] : 'none';
    echo '<div id="'.uniqid('rps_').'" class="nectar-recent-posts-single_featured multiple_featured parallax_section" data-button-color="'. esc_attr( $button_color ) .'" data-nav-location="'. esc_attr( $mlf_navigation_location ) .'" data-bg-overlay="'. esc_attr( $bg_overlay ) .'" data-padding="'. esc_attr( $large_featured_padding ) .'" data-autorotate="'. esc_attr( $auto_rotate ) .'" data-height="'. esc_attr( $slider_size ) .'" data-animate-in-effect="'. esc_attr( $animate_in_effect ) .'" data-remove-post-date="'. esc_attr( $blog_remove_post_date ) .'" data-remove-post-author="'. esc_attr( $blog_remove_post_author ) .'" data-remove-post-comment-number="'. esc_attr( $blog_remove_post_comment_number ) .'" data-remove-post-nectar-love="'. esc_attr( $blog_remove_post_nectar_love ) .'">';

    $i = 0;
    if( $recent_posts_query->have_posts() ) :  while( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post(); global $post; ?>
 
        <?php 
          $bg = get_post_meta($post->ID, '_nectar_header_bg', true);
          $bg_color = get_post_meta($post->ID, '_nectar_header_bg_color', true);
          $bg_image_id = null;
          $featured_img = null;
          
          if(!empty($bg)){
            //page header
            $featured_img = $bg;
 
          } elseif(has_post_thumbnail($post->ID)) {
            $bg_image_id = get_post_thumbnail_id($post->ID);
            $image_src = wp_get_attachment_image_src($bg_image_id, 'full');
            $featured_img = $image_src[0];
          }
 
 
        ?>
 
        <div class="nectar-recent-post-slide <?php if($bg_image_id == null) echo 'no-bg-img'; ?> <?php if($i == 0) echo 'active'; ?> post-ref-<?php echo esc_attr($i); ?>">
 
          <div class="row-bg using-image" data-parallax-speed="fast"><div class="nectar-recent-post-bg" style=" <?php if(!empty($bg_color)) { ?> background-color: <?php echo esc_attr($bg_color);?>; <?php } ?> background-image: url(<?php echo esc_url( $featured_img );?>);" > </div></div>
 
          <?php 
 
          echo '<div class="recent-post-container container"><div class="inner-wrap">';

          
              $categories = get_the_category();
              if ( ! empty( $categories ) ) {
                $cat_output = null;
                  $i = 0;
                  foreach( $categories as $category ) {
                     $i++;
                     $cat_output .= '<a class="'.$category->slug.'" href="' . esc_url( get_category_link( $category->term_id ) ) . '"><span class="'.$category->slug.'">'.esc_html( $category->name ) .'</span></a>';
                      if($i > 0) break;  
                  }
    
              }
          
            
            echo '<div class="grav-wrap"><a href="'.get_author_posts_url($post->post_author).'">'.get_avatar( get_the_author_meta('email'), 70,  null, get_the_author() ). '</a><div class="text"><span>'.esc_html__( 'By','salient').' <a href="'.get_author_posts_url($post->post_author).'" rel="author">' .get_the_author().'</a></span><span> '.esc_html__( 'In','salient').'</span> '. trim( $cat_output) . '</div></div>'; 
            ?>
          
            <h2 class="post-ref-<?php echo esc_attr($i); ?>"><a href="<?php echo esc_url(get_permalink()); ?>" class="full-slide-link"> <?php echo the_title(); ?> </a></h2>
            
            <?php 
            //stop regular grad class for material skin 
            $regular_btn_class = ' regular-button';
            
            if($button_color == 'extra-color-gradient-1' || $button_color == 'extra-color-gradient-2') {
              $regular_btn_class = '';
            }
            
          	if($nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-1') {
          		$button_color = 'm-extra-color-gradient-1';
          	} else if( $nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-2') {
          		$button_color = 'm-extra-color-gradient-2';
          	} 
            ?>
            <a class="nectar-button large regular <?php echo esc_attr($button_color) .  esc_attr($regular_btn_class); ?> has-icon" href="<?php echo esc_url(get_permalink()); ?>" data-color-override="false" data-hover-color-override="false" data-hover-text-color-override="#fff" ><span><?php echo esc_html__( 'Read Article', 'salient'); ?> </span><i class="icon-button-arrow"></i></a>
            
          
          </div></div></div>
 
        <?php $i++; ?>
 
    <?php endwhile; endif; 
 
        wp_reset_postdata();
     echo '</div>';
 
    wp_reset_query();
    
    $recent_posts_content = ob_get_contents();
    
    ob_end_clean();
  }
  
  
  else if($style == 'slider_multiple_visible') { //slider multiple visible
 
    ob_start(); 
      
    echo $title_label_output; // WPCS: XSS ok. ?>
    
    <?php 
      if($orderby != 'view_count') {
          $recentBlogPosts = array(
            'showposts' => $posts_per_page,
            'category_name' => $category,
            'ignore_sticky_posts' => 1,
            'offset' => $post_offset,
            'order' => $order,
            'orderby' => $orderby,
            'tax_query' => array(
                  array( 'taxonomy' => 'post_format',
                      'field' => 'slug',
                      'terms' => array('post-format-link'),
                      'operator' => 'NOT IN'
                      )
                  )
          );
    } else {
        
        $recentBlogPosts = array(
          'showposts' => $posts_per_page,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => 'meta_value_num',
          'meta_key' => 'nectar_blog_post_view_count',
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
      
    }
    $recent_posts_query = new WP_Query($recentBlogPosts);  
 
 
      $animate_in_effect = (!empty($nectar_options['header-animate-in-effect'])) ? $nectar_options['header-animate-in-effect'] : 'none';
    echo '<div class="nectar-recent-posts-slider_multiple_visible" data-columns="'.$columns.'" data-height="'.$slider_size.'" data-shadow-hover-type="'.$hover_shadow_type.'" data-animate-in-effect="'.$animate_in_effect.'" data-remove-post-date="'.$blog_remove_post_date.'" data-remove-post-author="'.$blog_remove_post_author.'" data-remove-post-comment-number="'.$blog_remove_post_comment_number.'" data-remove-post-nectar-love="'.$blog_remove_post_nectar_love.'">';

    echo '<div class="nectar-recent-posts-slider-inner"><div class="flickity-viewport"><div class="flickity-slider">'; 
    $i = 0;
    if( $recent_posts_query->have_posts() ) :  while( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post(); global $post; ?>
 
        <?php 
          $bg = get_post_meta($post->ID, '_nectar_header_bg', true);
          $bg_color = get_post_meta($post->ID, '_nectar_header_bg_color', true);
          $bg_image_id = null;
          $featured_img = null;
          
          if(has_post_thumbnail($post->ID)) {
            $bg_image_id = get_post_thumbnail_id($post->ID);
            $image_src = wp_get_attachment_image_src($bg_image_id, 'medium_featured');
            $featured_img = $image_src[0];
          }
 
 
        ?>
 
        <div class="nectar-recent-post-slide <?php if($bg_image_id == null) echo 'no-bg-img'; ?> post-ref-<?php echo esc_attr($i); ?>">
 
          <div class="nectar-recent-post-bg-wrap"><div class="nectar-recent-post-bg"  style=" <?php if(!empty($bg_color)) { ?> background-color: <?php echo esc_attr($bg_color);?>; <?php } ?> background-image: url(<?php echo esc_url($featured_img);?>);" > </div></div>
          <div class="nectar-recent-post-bg-blur"  style=" <?php if(!empty($bg_color)) { ?> background-color: <?php echo esc_attr($bg_color) ;?>; <?php } ?> background-image: url(<?php echo esc_url($featured_img) ;?>);" > </div>
 
          <?php 
 
          echo '<div class="recent-post-container container"><div class="inner-wrap">';
 
          echo '<span class="strong">';
              $categories = get_the_category();
              if ( ! empty( $categories ) ) {
                $output = null;
                  foreach( $categories as $category ) {
                      $output .= '<a class="'. esc_attr( $category->slug ).'" href="' . esc_url( get_category_link( $category->term_id ) ) . '"><span class="'. esc_attr( $category->slug ) .'">'.esc_html( $category->name ) .'</span></a>';
                  }
                  echo trim( $output);
              }
            echo '</span>'; ?>
          
            <h3 class="post-ref-<?php echo esc_attr($i); ?>"><a href=" <?php echo esc_url(get_permalink()); ?>" class="full-slide-link"> <?php echo the_title(); ?> </a></h3>
            
            
            <?php 
            //stop regular grad class for material skin 
            $button_color = strtolower($button_color);
            $regular_btn_class = ' regular-button';
            
            if($button_color == 'extra-color-gradient-1' || $button_color == 'extra-color-gradient-2') {
              $regular_btn_class = '';
            }
            
          	if($nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-1') {
          		$button_color = 'm-extra-color-gradient-1';
          	} else if( $nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-2') {
          		$button_color = 'm-extra-color-gradient-2';
          	} 
            ?>
            
            <?php if(!empty($nectar_options['theme-skin']) && $nectar_options['theme-skin'] == 'material') { ?>
              <a class="nectar-button large regular  <?php echo esc_attr($button_color) .  esc_attr($regular_btn_class); ?>" href="<?php echo esc_url(get_permalink()); ?>" data-color-override="false" data-hover-color-override="false" data-hover-text-color-override="#fff" ><span><?php echo esc_html__( 'Read Article','salient'); ?> </span></a>
            <?php } else { ?>
                <a class="nectar-button large regular  <?php echo esc_attr($button_color) .  esc_attr($regular_btn_class); ?> has-icon" href="<?php echo esc_url(get_permalink()); ?>" data-color-override="false" data-hover-color-override="false" data-hover-text-color-override="#fff" ><span><?php echo esc_html__( 'Read Article','salient'); ?> </span><i class="icon-button-arrow"></i></a>
            <?php } ?>
            
          </div>
          
        </div>
            
 
        </div>
 
        <?php $i++; ?>
 
    <?php endwhile; endif; 
 
        wp_reset_postdata();
  
     echo '</div></div></div></div>';
 
    wp_reset_query();
    
    $recent_posts_content = ob_get_contents();
    
    ob_end_clean();
  }
  
  
	else { //slider


		ob_start(); 
			
		echo $title_label_output; // WPCS: XSS ok. ?>
		
		<?php 
      if($orderby != 'view_count') {
        
  	    $recentBlogPosts = array(
  	      'showposts' => $posts_per_page,
  	      'category_name' => $category,
  	      'ignore_sticky_posts' => 1,
  	      'offset' => $post_offset,
          'order' => $order,
          'orderby' => $orderby,
  	      'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
  	    ); 
    } else {
      
        $recentBlogPosts = array(
          'showposts' => $posts_per_page,
          'category_name' => $category,
          'ignore_sticky_posts' => 1,
          'offset' => $post_offset,
          'order' => $order,
          'orderby' => 'meta_value_num',
          'meta_key' => 'nectar_blog_post_view_count',
          'tax_query' => array(
                array( 'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-link'),
                    'operator' => 'NOT IN'
                    )
                )
        );
    }

		$recent_posts_query = new WP_Query($recentBlogPosts);  


	    $animate_in_effect = (!empty($nectar_options['header-animate-in-effect'])) ? $nectar_options['header-animate-in-effect'] : 'none';
		echo '<div class="nectar-recent-posts-slider" data-height="'.$slider_size.'" data-animate-in-effect="'.$animate_in_effect.'" data-remove-post-date="'.$blog_remove_post_date.'" data-remove-post-author="'.$blog_remove_post_author.'" data-remove-post-comment-number="'.$blog_remove_post_comment_number.'" data-remove-post-nectar-love="'.$blog_remove_post_nectar_love.'">';

		echo '<div class="nectar-recent-posts-slider-inner generate-markup">'; 
		$i = 0;
		if( $recent_posts_query->have_posts() ) :  while( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post(); global $post; ?>

				<?php 
					$bg = get_post_meta($post->ID, '_nectar_header_bg', true);
					$bg_color = get_post_meta($post->ID, '_nectar_header_bg_color', true);
					$bg_image_id = null;
					$featured_img = null;
					
					if(!empty($bg)){
						//page header
						$featured_img = $bg;

					} elseif(has_post_thumbnail($post->ID)) {
						$bg_image_id = get_post_thumbnail_id($post->ID);
						$image_src = wp_get_attachment_image_src($bg_image_id, 'full');
						$featured_img = $image_src[0];
					}


				?>

				<div class="nectar-recent-post-slide <?php if($bg_image_id == null) echo 'no-bg-img'; ?> post-ref-<?php echo esc_attr($i); ?>">

					<div class="nectar-recent-post-bg"  style=" <?php if(!empty($bg_color)) { ?> background-color: <?php echo esc_attr( $bg_color ) ;?>; <?php } ?> background-image: url(<?php echo esc_url($featured_img) ;?>);" > </div>

					<?php 

					echo '<div class="recent-post-container container"><div class="inner-wrap">';

					echo '<span class="strong">';
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								$output = null;
							    foreach( $categories as $category ) {
							        $output .= '<a class="'.esc_attr($category->slug).'" href="' . esc_url( get_category_link( $category->term_id ) ) . '"><span class="'. esc_attr( $category->slug ) .'">'.esc_html( $category->name ) .'</span></a>';
							    }
							    echo trim( $output);
							}
						echo '</span>'; ?>
					
						<h2 class="post-ref-<?php echo esc_attr($i); ?>"><a href=" <?php echo esc_url(get_permalink()); ?>" class="full-slide-link"> <?php echo the_title(); ?> </a></h2> 
					</div></div>
						

				</div>

				<?php $i++; ?>

		<?php endwhile; endif; 

			  wp_reset_postdata();
	
		 echo '</div></div>';

		wp_reset_query();
		
		$recent_posts_content = ob_get_contents();
		
		ob_end_clean();
	}


	return $recent_posts_content;

}
add_shortcode('recent_posts', 'nectar_recent_posts');


 
//recent projects
function nectar_recent_projects($atts, $content = null) {
	extract(shortcode_atts(array("title_labels" => 'false', 'project_style' => '', 'heading' => '', 'page_link_text' => '', 'display_project_excerpt' => '', 'custom_link_text' => '', 'project_offset' => '0', 'control_text_color' => 'dark', 'slider_text_color'=>'light', 'overlay_strength' => '0', 'autorotate' => '', 'slider_controls'=>'arrows', 'page_link_url' => '', 'hide_controls' => 'false', 'lightbox_only' => '0', 'number_to_display' => '6','full_width' => 'false', 'category' => 'all'), $atts));   
	
	global $post; 
	global $nectar_options;
	global $nectar_love; 

	$nectar_options = get_nectar_theme_options(); 
	
	$title_label_output = null;
	$recent_projects_title_text = (!empty($nectar_options['carousel-title'])) ? $nectar_options['carousel-title'] : 'Recent Work';		
	$recent_projects_link_text = (!empty($nectar_options['carousel-link'])) ? $nectar_options['carousel-link'] : 'View All Work';		
	$portfolio_link = get_portfolio_page_link(get_the_ID()); 
	if(!empty($nectar_options['main-portfolio-link'])) $portfolio_link = $nectar_options['main-portfolio-link'];
	
	
	//project style
	if(empty($project_style) && $full_width == 'true') {
		$project_style = '2';
	} elseif(empty($project_style) && $full_width == 'false') {
		$project_style = '1';
	}

	
	$full_width_carousel = ($full_width == 'true') ? 'true': 'false';
			
	//incase only all was selected
	if($category == 'all') {
		$category = null;
	}
	
	$projects_to_display = (intval($number_to_display) == 0) ? '6' : $number_to_display; 
	
	if(!empty($heading)) {
		if($full_width_carousel == 'true'){
			$title_label_output = '<h2>'.$heading.'</h2>';
		} else {
			$title_label_output = '<h2>'.$heading;
			if(!empty($page_link_text)) $title_label_output .= '<a href="'. $page_link_url.'" class="button"> / '. $page_link_text .'</a>';
			$title_label_output .= '</h2>';
		}
	}
	
	//keep old label option to not break legacy users
	if($title_labels == 'true') { 
		$title_label_output = '<h2>'.$recent_projects_title_text;
		if(!empty($recent_projects_link_text) && strlen($recent_projects_link_text) > 2) $title_label_output .= '<a href="'. $portfolio_link.'" class="button"> / '. $recent_projects_link_text .'</a>';
		$title_label_output .= '</h2>';
	}

				$portfolio = array(
					'posts_per_page' => $projects_to_display,
					'post_type' => 'portfolio',
          'offset' => $project_offset, 
					'project-type'=> $category
				);

				$the_query = new WP_Query($portfolio);

				if(	$project_style != 'fullscreen_zoom_slider') {

					if($full_width_carousel == 'true'){
						$arrow_markup = '<div class="controls"><a class="portfolio-page-link" href="'.$page_link_url.'"><i class="icon-salient-back-to-all"></i></a>
										 <a class="carousel-prev" href="#"><i class="icon-salient-left-arrow-thin"></i></a>
						    	         <a class="carousel-next" href="#"><i class="icon-salient-right-arrow-thin"></i></a></div>';
					} else {
						$arrow_markup = '<div class="control-wrap"><a class="carousel-prev" href="#"><i class="icon-angle-left"></i></a>
						    	         <a class="carousel-next" href="#"><i class="icon-angle-right"></i></a></div>'; 
					} 
					
					if($hide_controls == 'true') $arrow_markup = null;
				}
				
				if ( $the_query->have_posts() && $project_style != 'fullscreen_zoom_slider'  ) { 
					
					$default_style = ($project_style == '1') ? 'default-style' : null;
					
					$recent_projects_content = '<div class="carousel-wrap recent-work-carousel '.$default_style.'" data-ctc="'.$control_text_color.'" data-full-width="'.$full_width_carousel.'">
					
					<div class="carousel-heading"><div class="container">'.$title_label_output . $arrow_markup .'</div></div>
					
					<ul class="row portfolio-items text-align-center carousel" data-scroll-speed="800" data-easing="easeInOutQuart">';
				 } 
				

				//standard layout
				if($project_style == '1'){
					
					if ( $the_query->have_posts() ) {
						while ( $the_query->have_posts() ) {
							$the_query->the_post();

						$project_image_caption = get_post(get_post_thumbnail_id())->post_content;
						$project_image_caption = strip_tags($project_image_caption);
						$project_image_caption_markup = null;
						if(!empty($project_image_caption)) $project_image_caption_markup = ' title="'.$project_image_caption.'" '; 	
					
						$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
						$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
						$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
						$media = null;
						$date = null;
						$love = $nectar_love->add_love(); 
						
						$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
						$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
						
						//video 
					    if( !empty($video_embed) || !empty($video_m4v) ) {
		
							$media = nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);	 
						     
				        } 
						
						//image
					    else {
					       $media .= '<a href="'. $featured_image[0].'" class="pretty_photo default-link">'.esc_html__("View Larger", 'salient').'</a> ';
					    }
						
						$project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);

						if(!empty($project_excerpt)) {
							 $date = '<p>'.$project_excerpt.'</p>'; 
						} elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) {
							 $date = '<p>' . get_the_date() . '</p>'; 
						} 
									
						$project_img = '<img src="'.get_template_directory_uri().'/img/no-portfolio-item-small.jpg" alt="no image added yet." />';
						if ( has_post_thumbnail() ) { $project_img = get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')); } 
						
						//custom thumbnail
						$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
						
						if( !empty($custom_thumbnail) ){
							$project_img = '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check($custom_thumbnail).'" alt="'. get_the_title() .'" />';
						}
						
						$more_details_html = ($lightbox_only != 'true') ? '<a class="default-link" href="' . $the_project_link . '">'.esc_html__("More Details", 'salient').'</a>' : null; 
					    
						$project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);	 
						if(!empty($project_accent_color)) { $project_accent_color_markup = 'data-project-color="' . $project_accent_color .'"'; } else { $project_accent_color_markup = 'data-default-color="true"';} 
						$project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
					    $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);

					    $using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
						$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true);

						$recent_projects_content .='<li class="col span_4" '.$project_accent_color_markup.' data-title-color="'.$project_title_color.'" data-subtitle-color="'.$project_subtitle_color.'">
							<div class="inner-wrap animated" data-animation="none">
							<div class="work-item" data-custom-content="'.$using_custom_content.'">' . $project_img . '
			
								<div class="work-info-bg"></div>
								<div class="work-info">';
									
									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'.$the_project_link.'"></a>';
										$recent_projects_content .= '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div>';
									   //default
									} else { 
										$recent_projects_content .= '<div class="vert-center">' . $media . $more_details_html .'</div><!--/vert-center-->';
									}

								$recent_projects_content .= '</div>
							</div><!--work-item-->
							
							<div class="work-meta">
								<h4 class="title"> '. get_the_title() .'</h4>
								'.$date.'
							</div><div class="nectar-love-wrap">
							
							'.$love.'</div>
							
							<div class="clear"></div>
							</div>
						</li><!--/span_4-->';
					
					} 

				  } 
				
				} 
				
				//alt project style
				elseif($project_style == '2') {
					
					if ( $the_query->have_posts() ) {
						while ( $the_query->have_posts() ) {
							$the_query->the_post();

						$project_image_caption = get_post(get_post_thumbnail_id())->post_content;
						$project_image_caption = strip_tags($project_image_caption);
						$project_image_caption_markup = null;
						if(!empty($project_image_caption)) $project_image_caption_markup = ' title="'.$project_image_caption.'" '; 		
						
						$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
						$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
						$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
						$media = null;
						$date = null;
						$love = $nectar_love->add_love(); 
						$margin = ($full_width_carousel == 'true') ? 'no-margin' : null;
						
						$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
						$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
						
						$project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);
						if(!empty($project_excerpt)) {
							 $date = '<p>'.$project_excerpt.'</p>'; 
						} elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) {
							 $date = '<p>' . get_the_date() . '</p>'; 
						} 
									
						$project_img = '<img src="'.get_template_directory_uri().'/img/no-portfolio-item-small.jpg" alt="no image added yet." />';
						if ( has_post_thumbnail() ) { $project_img = get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')); } 
						
						//custom thumbnail
						$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
						
						if( !empty($custom_thumbnail) ){
							$project_img = '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check($custom_thumbnail).'" alt="'. get_the_title() .'" />';
						}
						
						if($lightbox_only != 'true') {
							$link_markup = '<a href="' . $the_project_link . '"></a>';
						} else {
							
							//video 
						    if( !empty($video_embed) || !empty($video_m4v) ) {
								
							    
								$link_markup = nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
							     
			
					        } 
							
					        //image
					        else {
					        	$link_markup = '<a href="'. $featured_image[0].'" '.$project_image_caption_markup.' class="pretty_photo"></a>';
					        }
							
						}
						
						$project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);	 
						if(!empty($project_accent_color)) { $project_accent_color_markup = 'data-project-color="' . $project_accent_color .'"'; } else { $project_accent_color_markup = 'data-default-color="true"';} 
						$project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
					    $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);

					    $using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
						$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true);

						$recent_projects_content .='<li class="col span_4 '.$margin.'" '.$project_accent_color_markup.' data-title-color="'.$project_title_color.'" data-subtitle-color="'.$project_subtitle_color.'">
							
							<div class="work-item style-2" data-custom-content="'.$using_custom_content.'">' . $project_img . '
			
								<div class="work-info-bg"></div>
								<div class="work-info">
									
									
									'.$link_markup;

									if($using_custom_content == 'on') {
										if(!empty($custom_project_link)) echo '<a href="'.$the_project_link.'"></a>';
										$recent_projects_content .= '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div>';
									   //default
									} else { 
										$recent_projects_content .= '<div class="vert-center"><h3>' . get_the_title() . '</h3> ' . $date.'</div><!--/vert-center-->';
									}

								$recent_projects_content .= '</div>
							</div><!--work-item-->

						</li><!--/span_4-->';
					
					}

                  }
					
					
				}//full width
				
				
				
				elseif($project_style == '3') {
							
						if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
							
							$project_image_caption = get_post(get_post_thumbnail_id())->post_content;
							$project_image_caption = strip_tags($project_image_caption);
							$project_image_caption_markup = null;
							if(!empty($project_image_caption)) $project_image_caption_markup = ' title="'.$project_image_caption.'" '; 	

							$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
							$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
							$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
							$media = null;
							$date = null;
							$love = $nectar_love->add_love(); 
							$margin = ($full_width_carousel == 'true') ? 'no-margin' : null;
							
							$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
							$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
							
							$project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);
							if(!empty($project_excerpt)) {
								 $date = '<p>'.$project_excerpt.'</p>'; 
							} elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) {
								 $date = '<p>' . get_the_date() . '</p>'; 
							} 
										
							$project_img = '<img src="'.get_template_directory_uri().'/img/no-portfolio-item-small.jpg" alt="no image added yet." />';
							if ( has_post_thumbnail() ) { $project_img = get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')); } 
							
							//custom thumbnail
							$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
							
							if( !empty($custom_thumbnail) ){
								$project_img = '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check($custom_thumbnail).'" alt="'. get_the_title() .'" />';
							}
							
							if($lightbox_only != 'true') {
								$link_markup = '<a href="' . $the_project_link . '"></a>';
							} else {
								
								//video 
							    if( !empty($video_embed) || !empty($video_m4v) ) {
				
									$link_markup = nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);

						        } 
								
						        //image
						        else {
						        	$link_markup = '<a href="'. $featured_image[0].'" '.$project_image_caption_markup.' class="pretty_photo"></a>';
						        }
								
							}
							
							$project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);	 
							if(!empty($project_accent_color)) { $project_accent_color_markup = 'data-project-color="' . $project_accent_color .'"'; } else { $project_accent_color_markup = 'data-default-color="true"';} 
							$project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
						    $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);

						    $using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true);

							$recent_projects_content .='<li class="col span_4 '.$margin.'" '.$project_accent_color_markup.' data-title-color="'.$project_title_color.'" data-subtitle-color="'.$project_subtitle_color.'">
								
								<div class="work-item style-3" data-custom-content="'.$using_custom_content.'">' . $project_img . '
				
									<div class="work-info-bg"></div>
									<div class="work-info">
										
										'.$link_markup;

										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											if(!empty($custom_project_link)) echo '<a href="'.$the_project_link.'"></a>';
											$recent_projects_content .= '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div>';
										   //default
										} else { 
											$recent_projects_content .= '<div class="vert-center"><h3>' . get_the_title() . '</h3>' . $date.'</div><!--/vert-center-->';
										}
		
										
									$recent_projects_content .= '</div>
								</div><!--work-item-->
	
							</li><!--/span_4-->';
						
						}

                      }
						
					} //project style 3
				
				
				elseif($project_style == '4') {
							
						if ( $the_query->have_posts() ) {
						  while ( $the_query->have_posts() ) {
							$the_query->the_post();

							$project_image_caption = get_post(get_post_thumbnail_id())->post_content;
							$project_image_caption = strip_tags($project_image_caption);
							$project_image_caption_markup = null;
							if(!empty($project_image_caption)) $project_image_caption_markup = ' title="'.$project_image_caption.'" '; 	
						
							$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
							$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
							$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
							$media = null;
							$date = null;
							$love = $nectar_love->add_love(); 
							$margin = ($full_width_carousel == 'true') ? 'no-margin' : null;
							
							$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
							$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
							
							$project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);
							if(!empty($project_excerpt)) {
								 $date = '<p>'.$project_excerpt.'</p>'; 
							} elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) {
								 $date = '<p>' . get_the_date() . '</p>'; 
							} 
										
							$project_img = '<img src="'.get_template_directory_uri().'/img/no-portfolio-item-small.jpg" alt="no image added yet." />';
							if ( has_post_thumbnail() ) { $project_img = get_the_post_thumbnail($post->ID, 'portfolio-thumb', array('title' => '')); } 
							
							//custom thumbnail
							$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
							
							if( !empty($custom_thumbnail) ){
								$project_img = '<img class="custom-thumbnail skip-lazy" src="'.nectar_ssl_check($custom_thumbnail).'" alt="'. get_the_title() .'" />';
							}
							
							if($lightbox_only != 'true') {
								$link_markup = '<a href="' . $the_project_link . '"></a>';
							} else {
								
								//video 
							    if( !empty($video_embed) || !empty($video_m4v) ) {
				
								   
									$link_markup = nectar_portfolio_video_popup_link($post, $project_style, $video_embed, $video_m4v);
	
						        } 
								
						        //image
						        else {
						        	$link_markup = '<a href="'. $featured_image[0].'" '.$project_image_caption_markup.' class="pretty_photo"></a>';
						        }
								
							}
							
							$project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);	 
							if(!empty($project_accent_color)) { $project_accent_color_markup = 'data-project-color="' . $project_accent_color .'"'; } else { $project_accent_color_markup = 'data-default-color="true"';} 
							$project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
						    $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);

						    $using_custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); 
							$custom_content = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item_content', true);

							$recent_projects_content .='<li class="col span_4 '.$margin.'" '.$project_accent_color_markup.' data-title-color="'.$project_title_color.'" data-subtitle-color="'.$project_subtitle_color.'">
								
								<div class="work-item style-4" data-custom-content="'.$using_custom_content.'">' . $project_img . '
				
									<div class="work-info">
										
										'.$link_markup;
										
										if(!empty($using_custom_content) && $using_custom_content == 'on') {
											if(!empty($custom_project_link)) echo '<a href="'.$the_project_link.'"></a>';
											$recent_projects_content .= '<div class="vert-center"><div class="custom-content">' . do_shortcode($custom_content) . '</div></div>';
										   //default
										} else { 
											$recent_projects_content .= '<div class="bottom-meta"><h3>' . get_the_title() . '</h3>' . $date.'</div><!--/bottom-meta-->';
										}

									$recent_projects_content .= '</div>
								</div><!--work-item-->
	
							</li><!--/span_4-->';
						
						}

                      }
						
					} //project style 4
				
			
			if ( $the_query->have_posts() && $project_style != 'fullscreen_zoom_slider' ) {
			 $recent_projects_content .= '</ul><!--/carousel--></div><!--/carousel-wrap-->';
			}


			//fullscreen
			if($project_style == 'fullscreen_zoom_slider') {

				$recent_projects_content = '<div class="nectar_fullscreen_zoom_recent_projects" data-autorotate="'.$autorotate.'" data-slider-text-color="'.$slider_text_color.'" data-slider-controls="'.$slider_controls.'" data-overlay-opacity="'.$overlay_strength.'"><div class="project-slides">';

				$projcount = 0;

				if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								
								$project_image_caption = get_post(get_post_thumbnail_id())->post_content;
								$project_image_caption = strip_tags($project_image_caption);
								$project_image_caption_markup = null;
								if(!empty($project_image_caption)) $project_image_caption_markup = ' title="'.$project_image_caption.'" '; 	

								$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
								$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
								$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
								$media = null;
								$date = null;

								$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
								$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());
								
                $project_excerpt = get_post_meta($post->ID, '_nectar_project_excerpt', true);
                
                $fullscreen_slider_excerpt = '';
                
                if($display_project_excerpt == 'true') {
                  $fullscreen_slider_excerpt = (!empty($project_excerpt)) ? '<p>'.$project_excerpt.'</p>' : '';
                }
                
								if(!empty($project_excerpt)) {
									 $date = '<p>'.$project_excerpt.'</p>'; 
								} elseif(!empty($nectar_options['portfolio_date']) && $nectar_options['portfolio_date'] == 1) {
									 $date = '<p>' . get_the_date() . '</p>'; 
								} 
											
								$project_img = get_template_directory_uri().'/img/no-portfolio-item-small.jpg';
								if ( has_post_thumbnail() ) { $project_img = get_the_post_thumbnail_url($post->ID, 'full', array('title' => '')); } 
								
								//custom thumbnail
								$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
								
								if( !empty($custom_thumbnail) ){
									$project_img = nectar_ssl_check($custom_thumbnail);
								}

								$project_accent_color = get_post_meta($post->ID, '_nectar_project_accent_color', true);	 
								if(empty($project_accent_color)) { $project_accent_color = '#000000'; } 
								$project_title_color = get_post_meta($post->ID, '_nectar_project_title_color', true);
							    $project_subtitle_color = get_post_meta($post->ID, '_nectar_project_subtitle_color', true);

							    $active_class = ($projcount == 0) ? 'current': 'next';
                
                if(!empty($custom_link_text)) {
                  $fullscreen_slider_link_text = $custom_link_text;
                } else {
                  $fullscreen_slider_link_text = esc_html__("View Project", 'salient');
                }
                
								$recent_projects_content .='<div class="project-slide '.$active_class.'">';
								$recent_projects_content .= '<div class="bg-outer-wrap"><div class="bg-outer"><div class="bg-inner-wrap" style="background-color: '.$project_accent_color.';"><div class="slide-bg" style="background-image:url('.$project_img.')"></div></div></div></div>';
								$recent_projects_content .= '<div class="project-info"><div class="container normal-container"><h1>'. get_the_title(). '</h1> '.$fullscreen_slider_excerpt.' <a href="'.$the_project_link.'">' . $fullscreen_slider_link_text . '</a></div></div>';
								$recent_projects_content .= '</div><!--project slide-->';

								$projcount++;

						}

                  }

                  if($slider_controls == 'both' || $slider_controls == 'arrows') {
                    $next_prev_markup = '<div class="zoom-slider-controls"><a class="prev" href="#"><i class="fa fa-angle-left" aria-hidden="true"></i></a><a class="next" href="#"><i class="fa fa-angle-right" aria-hidden="true"></i></a></div>';
              	  } else {
              	  	 $next_prev_markup = null;
              	  }

				$recent_projects_content .= '</div><div class="container normal-container">'.$next_prev_markup.'</div></div><!--nectar_fullscreen_zoom_recent_projects-->';
			}	


		wp_reset_postdata();


	
    return $recent_projects_content; 
	

}
add_shortcode('recent_projects', 'nectar_recent_projects');
 
 
 
 
 
//old video player	
if ( floatval(get_bloginfo('version')) < "3.6" ) {
		 
	//video
	function nectar_shortcode_video($atts, $content = null) {
		extract(shortcode_atts(array("title" => 'Title', 'm4v_url' => null, 'ogv_url' => null, 'image_url' => null, 'm4v' => null, 'ogv' => null, 'poster' => null), $atts));  
		$video_markup = null;
		
		$id = rand(); 
		$id = $id*rand(1,50);
	
		$video_m4v = null; 
		$video_ogv = null;
		$video_image = null;
		
		if (!empty($m4v_url)) { $video_m4v = $m4v_url; }
		if (!empty($m4v)) { $video_m4v = $m4v; }
		
		if (!empty($ogv_url)) { $video_ogv = $ogv_url; }
		if (!empty($ogv)) { $video_ogv = $ogv; }
		
		if (!empty($image_url)) { $video_image = $image_url; }
		if (!empty($poster)) { $video_image = $poster; } 

		if (empty($image_url) && empty($preview)) {
			$image_url = get_template_directory_uri().'/img/no-video-img.png'; 
		}

		$video_markup .= '<script type="text/javascript">
	    	jQuery(document).ready(function($){
			
	    		if( $().jPlayer ) {
	    			$("#jquery_jplayer_'.$id.'").jPlayer({
	    				ready: function () {
	    					$(this).jPlayer("setMedia", {
	    						m4v: "'.$video_m4v.'",
	    						ogv: "'. $video_ogv .'",
	    						poster: "'. $video_image .'"
	    					});
	    				},
	    				size: {
				          width: "100%",
				          height: "auto"
				        },
	    				swfPath: "'. get_template_directory_uri() .'/js",
	    				cssSelectorAncestor: "#jp_interface_'.$id.'",
	    				supplied: "m4v, ogv, all"
	    			});
	    		}
	    	});
	    </script>
	
	    <div id="jquery_jplayer_'.$id.'" class="jp-jplayer jp-jplayer-video"></div>
	
	    <div class="jp-video-container">
	        <div class="jp-video">
	            <div id="jp_interface_'.$id.'" class="jp-interface">
	                <ul class="jp-controls">
	                	<li><div class="seperator-first"></div></li>
	                    <li><div class="seperator-second"></div></li>
	                    <li><a href="#" class="jp-play" tabindex="1">play</a></li>
	                    <li><a href="#" class="jp-pause" tabindex="1">pause</a></li> 
	                    <li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
	                    <li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
	                </ul>
	                <div class="jp-progress">
	                    <div class="jp-seek-bar">
	                        <div class="jp-play-bar"></div>
	                    </div>
	                </div>
	                <div class="jp-volume-bar-container">
	                    <div class="jp-volume-bar">
	                        <div class="jp-volume-bar-value"></div>
	                    </div>
	                </div>
	            </div>
	
	        </div>
	    </div>';
		
		return $video_markup;
	
	}
	
	add_shortcode('video', 'nectar_shortcode_video');

}
 

 
//old audio player	 
if ( floatval(get_bloginfo('version')) < "3.6" ) {

	function nectar_shortcode_audio($atts, $content = null) {
		extract(shortcode_atts(array("title" => 'Title', 'mp3_url' => null, 'oga_url' => null, 'mp3' => null, 'ogg' => null), $atts));  
		$audio_markup = null;
		
		$id = rand();
		$id = $id*rand(1,50);
		
		$audio_mp3 = null;
		$audio_oga = null;
		
		if (!empty($mp3_url)) { $audio_mp3 = $m4v_url; }
		if (!empty($mp3)) { $audio_mp3 = $mp3; }
		
		if (!empty($oga_url)) { $audio_oga = $ogv_url; }
		if (!empty($ogg)) { $audio_oga = $ogg; }

		$audio_markup .= '<script type="text/javascript">
			
	    			jQuery(document).ready(function($){
		
	    				if( $().jPlayer ) {
	    					$("#jquery_jplayer_'.$id.'").jPlayer({
	    						ready: function () {
	    							$(this).jPlayer("setMedia", {
	    								mp3: "'.$audio_mp3.'",
	    								oga: "'.$audio_oga.'", 
	    							});
	    						},
	    						swfPath: "'. get_template_directory_uri().' /js",
	    						cssSelectorAncestor: "#jp_interface_'.$id.'",
	    						supplied: "oga, mp3, all"
	    					});
						
	    				}
	    			});
	    		</script>
				
				<div class="audio-wrap">
					
		    	    <div id="jquery_jplayer_'.$id.'" class="jp-jplayer jp-jplayer-audio"></div>
		
		            <div class="jp-audio-container">
		                <div class="jp-audio">
		                    <div id="jp_interface_'.$id.'" class="jp-interface">
		                        <ul class="jp-controls">
		                            <li><a href="#" class="jp-play" tabindex="1">play</a></li>
		                            <li><a href="#" class="jp-pause" tabindex="1">pause</a></li>
		                            <li><a href="#" class="jp-mute" tabindex="1">mute</a></li>
		                            <li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>
		                        </ul>
		                        <div class="jp-progress">
		                            <div class="jp-seek-bar">
		                                <div class="jp-play-bar"></div>
		                            </div>
		                        </div>
		                        <div class="jp-volume-bar-container">
		                            <div class="jp-volume-bar">
		                                <div class="jp-volume-bar-value"></div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
	            </div>';
	
	
	return $audio_markup;
	 
	}	
	
	add_shortcode('audio', 'nectar_shortcode_audio');

}

?>