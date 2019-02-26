<?php

$title = $el_class = $value = $label_value= $units = '';
extract(shortcode_atts(array(
	'image_url_1' => '',
	'image_url_2' => '',
	'bg_color' => '#fff',
	'bg_color_2' => '#fff',
	'bg_color_overlay' => '',
	'bg_color_overlay_2' => '',
	'min_height' => '300',
	'text_color' => '',
	'text_color_2' => '',
	'h_text_align' => 'center',
	'v_text_align' => 'center',
	'front_content' => '',
	'box_shadow' => '',
	'icon_family' => 'fontawesome',
	'icon_fontawesome' => '',
	'icon_linea' => '',
	'icon_iconsmind' => '',
	'icon_steadysets' => '',
	'icon_color' => 'accent-color',
	'icon_size' => '60',
	'flip_direction' => 'horizontal-to-left'
), $atts));

$style = null;
$style2 = null;


if(!empty($image_url_1)) {
	
	if(!preg_match('/^\d+$/',$image_url_1)){
                    
        $style .= 'background-image: url('.$image_url_1 . '); ';
    
    } else {
		$bg_image_src = wp_get_attachment_image_src($image_url_1, 'full');
		$style .= 'background-image: url(\''.$bg_image_src[0].'\'); ';
	}
}

if(!empty($image_url_2)) {

	if(!preg_match('/^\d+$/',$image_url_2)){
                    
        $style2 .= 'background-image: url('.$image_url_2 . '); ';
    
    } else {
		$bg_image_src_2 = wp_get_attachment_image_src($image_url_2, 'full');
		$style2 .= 'background-image: url(\''.$bg_image_src_2[0].'\'); ';
	}
}

if(!empty($bg_color)) 
	$style .= 'background-color: '.$bg_color.'; ';
if(!empty($bg_color_2)) 
	$style2 .= 'background-color: '.$bg_color_2.'; ';



if(!empty($min_height)) {
	$style .= 'min-height: '.$min_height.'px;';
	$style2 .= 'min-height: '.$min_height.'px;';
}


$box_link = null;
if(!empty($link_url)) {
	$box_link = '<a '.$new_tab_markup.' href="'.$link_url.'" class="box-link"></a>';
}
$text_link = null;
if(!empty($link_text)) {
	$text_link = '<div class="link-text">'.$link_text.'<span class="arrow"></span></div>';
}

$icon_markup = null;

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
	case 'iconsmind':
			$icon = $icon_iconsmind;
			break;
	default:
		$icon = '';
		break;
}

if($icon_family == 'linea') wp_enqueue_style('linea'); 

if(!empty($icon)) {
	$icon_markup = '<i class="icon-default-style '.$icon.'" data-color="'.strtolower($icon_color).'" style="font-size: '.$icon_size.'px!important; line-height: '.$icon_size.'px!important;"></i>';
}

$output = '<div class="nectar-flip-box" data-min-height="'.$min_height.'" data-flip-direction="'.$flip_direction.'" data-h_text_align="'.$h_text_align.'" data-v_text_align="'.$v_text_align.'">';
$output .= '<div class="flip-box-front" data-bg-overlay="'.$bg_color_overlay.'" data-text-color="'.$text_color.'" style="'.$style.'"> <div class="inner">'.$icon_markup . do_shortcode($front_content).'</div> </div>';
$output .= '<div class="flip-box-back"  data-bg-overlay="'.$bg_color_overlay_2.'" data-text-color="'.$text_color_2.'" style="'.$style2.'"> <div class="inner">'.do_shortcode($content).'</div> </div>';
$output .= '</div>';

echo $output;

