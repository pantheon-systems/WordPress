<?php 

extract(shortcode_atts(array(
	'icon_family' => 'fontawesome',
	'icon_fontawesome' => '',
	'icon_linecons' => '',
	'icon_linea' => '',
	'icon_iconsmind' => '',
	'icon_steadysets' => '',
	'icon_color' => 'accent-color',
	'icon_size' => '50',
	'icon_style' => '',
	'icon_border_thickness' => '2px',
	'enable_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
	'url' => '',
	'open_new_tab' => '',
	'icon_padding' => '20px',
	'margin_top' => '',
	'margin_right' => '',
	'margin_bottom' => '',
	'margin_left' => '',
), $atts));

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
		wp_enqueue_style( 'vc_linecons' );
		break;
	case 'iconsmind':
		$icon = $icon_iconsmind;
		break;
	default:
		$icon = '';
		break;
}

$icon_size_val = (!empty($icon_style) && $icon_style == 'border-basic' || !empty($icon_style) && $icon_style == 'border-animation' || !empty($icon_style) && $icon_style == 'soft-bg') ? intval($icon_size)*1.5 : intval($icon_size);

//regular icon only grad extra space
if(!empty($icon_style) && $icon_style == 'default') {
	if(strtolower($icon_color) == 'extra-color-gradient-1' || strtolower($icon_color) == 'extra-color-gradient-2') {
		$icon_size_val = intval($icon_size)*1.2;
	}
}

//needed because display: initial will cause imperfect cirles
$grad_dimensions = '';
if(strtolower($icon_color) == 'extra-color-gradient-1' || strtolower($icon_color) == 'extra-color-gradient-2') {
	$circle_size = ($icon_size_val + (intval($icon_padding)*2) + intval($icon_border_thickness));
	$grad_dimensions = 'style="height: '. $circle_size .'px; width: '.$circle_size.'px;"';
}

//svg
if($icon_family == 'linea' && $enable_animation == 'true' && $icon != '' && strlen($grad_dimensions) < 2) {
	wp_enqueue_script('vivus'); 
	$converted_icon = str_replace('-', '_', $icon);
	$converted_icon = str_replace('icon_', '', $converted_icon);
	$icon_markup = '<span class="svg-icon-holder" data-size="'. $icon_size . '" data-animation-speed="'.$animation_speed.'" data-animation="'.$enable_animation.'" data-animation-delay="'.$animation_delay.'" data-color="'.strtolower($icon_color) .'"><span>';
	ob_start();
	
	//$icon_markup .= file_get_contents(get_template_directory() .'/css/fonts/svg/'. $converted_icon .'.svg');
	get_template_part( 'css/fonts/svg/'. $converted_icon .'.svg' );
	
	$icon_markup .=  ob_get_contents();
	ob_end_clean();
	
	$icon_markup .= '</span></span>';
} 
//regular
else {

	//regular(grad) linea
	if(!empty($icon_family) && $icon_family == 'linea') {
		wp_enqueue_style('linea'); 
	}

	if(!empty($icon_family) && $icon_family != 'none') {
		$icon_markup = '<i style="font-size: '.intval($icon_size).'px; line-height: '. $icon_size_val .'px; height: '. $icon_size_val .'px; width: '. $icon_size_val .'px;" class="' . $icon .'"></i>'; 
		
	} 
	else {
		$icon_markup = null; 
	}
}

//margins
$margins = '';
if(!empty($margin_top))
	$margins .= 'margin-top: '.intval($margin_top).'px; ';
if(!empty($margin_right))
	$margins .= 'margin-right: '.intval($margin_right).'px; ';
if(!empty($margin_bottom))
	$margins .= 'margin-bottom: '.intval($margin_bottom).'px; ';
if(!empty($margin_left))
	$margins .= 'margin-left: '.intval($margin_left).'px;';

//link
if(!empty($url)) {
	$target = ($open_new_tab == 'true') ? 'target="_blank"' : null;
	$icon_link = '<a href="'.$url.'" '.$target.'></a>';
} else {
	$icon_link = null;
}

echo '<div class="nectar_icon_wrap" data-style="'.$icon_style.'" data-draw="'.$enable_animation.'" data-border-thickness="'.$icon_border_thickness.'" data-padding="'.$icon_padding.'" data-color="'.strtolower($icon_color).'" style="'.$margins.'" >
		<div class="nectar_icon" '.$grad_dimensions.'>'.$icon_link.$icon_markup.'</div>
	</div>';



?>