<?php

$title = $el_class = $value = $label_value= $units = '';
extract(shortcode_atts(array(
	'image_url' => '',
	'link_url' => '',
	'link_new_tab' => '',
	'link_text' => '',
	'min_height' => '300',
	'color' => 'accent-color',
	'box_style' => 'default',
	'icon_family' => '',
  'icon_fontawesome' => '',
  'icon_linecons' => '',
	'icon_linea' => '',
  'icon_iconsmind' => '',
  'icon_steadysets' => '',
	'icon_size' => '50',
	'secondary_content' => '',
	'box_color' => '',
	'content_color' => '#ffffff',
	'box_color_opacity' => '1',
	'css' => '',
	'enable_animation' => '',
	'animation' => '', 
	'enable_border' => '',
	'box_alignment' => 'left',
	'delay' => ''
	
), $atts));

$style = null;
$icon_markup = null;

//icon
switch($icon_family) {
	case 'fontawesome':
		$icon = $icon_fontawesome;
		break;
	case 'steadysets':
		$icon = $icon_steadysets;
		break;
	case 'linea':
		$icon = $icon_linea;
		break;
	case 'linecons':
		$icon = $icon_linecons;
		break;
	case 'iconsmind':
		$icon = $icon_iconsmind;
		break;
	default:
		$icon = '';
		break;
}

if($icon_family == 'linea') wp_enqueue_style('linea'); 

if(!empty($icon)) {

	$color_attr = 'data-color="'.strtolower($color).'"';

	$icon_markup = '<i class="icon-default-style '.$icon.'" '.$color_attr.' style="font-size: '.$icon_size.'px!important; line-height: '.$icon_size.'px!important;"></i>';
	
	//needs two for fancy gradient hover
	if($box_style == 'color_box_hover' && strtolower($color) == 'extra-color-gradient-2' || $box_style == 'color_box_hover' && strtolower($color) == 'extra-color-gradient-1') {
		$icon_markup .= '<i class="icon-default-style hover-only '.$icon.'" data-color="white" style="font-size: '.$icon_size.'px!important; line-height: '.$icon_size.'px!important;"></i>';
	}
}

$new_tab_markup = ($link_new_tab == true) ? 'target="_blank"' : null;

$using_img_class = null;
$bg_image_src = array('0' => '');
if(!empty($image_url)) {
		
		$using_img_class = 'using-img';
		
  	if(!preg_match('/^\d+$/',$image_url)){
				 
		  	 $bg_image_src = $image_url;
				  $style .= ' style="background-image: url(\''.$bg_image_src.'\'); "';
					
		} else {
		
		    $bg_image_src = wp_get_attachment_image_src($image_url, 'full');
		    $style .= ' style="background-image: url(\''.$bg_image_src[0].'\'); "';
	 }
	
}

$style2 = '';

if($box_style == 'color_box_basic' && !empty($box_color)) { $color = $box_color; }

if( !empty($box_color) && $box_style == 'color_box_basic' || !empty($content_color) && $box_style == 'color_box_basic') {
		
		$basic_box_coloring = '';
		
		if(!empty($box_color)) {
			$basic_box_coloring .= ' background-color: '.$box_color.';';
		}
		if(!empty($content_color)) {
			$style2 = 'style="color: '.$content_color.';"';
		}
		
		if($style == null) {
				 $style = 'style="'.$basic_box_coloring.'"';
		}
		else {
				//remove the ending quotation first since it's already closed
				$style = substr($style,0,-1);
				$style .= $basic_box_coloring .'"';
				
		}
	 
}

$box_link = null;
if(!empty($link_url)) {
	$box_link = '<a '.$new_tab_markup.' href="'.$link_url.'" class="box-link"></a>';
}
$text_link = null;
if(!empty($link_text)) {
	$text_link = '<div class="link-text">'.$link_text.'<span class="arrow"></span></div>';
}

$extra_wrap_open = $extra_wrap_close = $extra_wrap_open2 = $extra_wrap_close2 = null;
if($box_style == 'color_box_hover') {
	$extra_wrap_open = '<div class="inner-wrap">';
	$extra_wrap_open2 = '<div class="box-inner-wrap">';
	$extra_wrap_close = $extra_wrap_close2 ='</div>';

}

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );

$parsed_animation = '';
if(!empty($animation) && $animation != 'none' && $enable_animation == 'true') {
     $css_class .= ' has-animation';
     
     $parsed_animation = str_replace(" ","-",$animation);
     $delay = intval($delay);
}

if($box_style == 'parallax_hover') {
	
	if(!preg_match('/^\d+$/',$image_url)){
		$parallax_bg_img = $image_url;
	} else {
		$parallax_bg_img = $bg_image_src[0];
	}
	
	$output = '<div class="nectar-fancy-box style-5 '.$using_img_class.' '.$css_class.'" data-style="'. $box_style .'" data-animation="'.strtolower($parsed_animation).'" data-delay="'.$delay.'" data-color="'.strtolower($color).'">';
	
	$output .= $box_link;
	
	$output .= '<div class="parallaxImg">';
	$output .= '<div class="parallaxImg-layer" data-img="'.$parallax_bg_img.'"></div>';
	$output .= '<div class="parallaxImg-layer"> <div class="meta-wrap" style="min-height: '.$min_height.'px"><div class="inner">';
	$output .= $icon_markup . do_shortcode($content);
			 
	$output .= '</div> </div></div></div>';
	
	$output .= '</div>';
	 
} else {
	

	$output = '<div class="nectar-fancy-box '.$using_img_class.' '.$css_class.'" data-style="'. $box_style .'" data-animation="'.strtolower($parsed_animation).'" data-border="'.$enable_border.'" data-box-color-opacity="'.$box_color_opacity.'" data-delay="'.$delay.'" data-alignment="'.$box_alignment.'" data-color="'.strtolower($color).'" '.$style2.'>';
	$output .= $extra_wrap_open2 . '<div class="box-bg" '.$style.'></div> <div class="inner" style="min-height: '.$min_height.'px">'.$extra_wrap_open . $icon_markup . do_shortcode($content) . $extra_wrap_close. '</div> '.$text_link.' '.$box_link. $extra_wrap_close2 .' </div>';

}

echo $output;