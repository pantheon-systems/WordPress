<?php 

extract(shortcode_atts(array("size" => 'small', "url" => '#', 'button_style' => '', 'button_color_2' => '', 'button_color' => '', 'color_override' => '', 'solid_text_color_override' => '', 'hover_color_override' => '', 'hover_text_color_override' => '#fff', "text" => 'Button Text', 'icon_family' => '', 'icon_fontawesome' => '', 'icon_linecons' => '', 'icon_iconsmind' => '', 'icon_steadysets' => '', 'open_new_tab' => '0', 
	'margin_top' => '','margin_right' => '','margin_bottom' => '', 'margin_left' => '', 'css_animation' => '', 'el_class' => ''), $atts));


global $nectar_options;
 
$target = ($open_new_tab == 'true') ? 'target="_blank"' : null;
	
	//icon
	switch($icon_family) {
		case 'fontawesome':
			$icon = $icon_fontawesome;
			break;
		case 'steadysets':
			$icon = $icon_steadysets;
			break;
		case 'linecons':
			$icon = $icon_linecons;
			break;
		case 'iconsmind':
			$icon = $icon_iconsmind;
			break;
		case 'default_arrow':
			$icon = 'icon-button-arrow';
			break;
		default:
			$icon = '';
			break;
	}
	
	
	$starting_custom_icon_color = '';
	if(!empty($solid_text_color_override) && $button_style == 'regular' || !empty($solid_text_color_override) && $button_style == 'regular-tilt') {
		$starting_custom_icon_color = 'style="color: '.$solid_text_color_override.';" ';
	}
	
	if(!empty($icon_family) && $icon_family != 'none') {
		$button_icon = '<i '.$starting_custom_icon_color.' class="' . $icon .'"></i>'; $has_icon = ' has-icon'; 
	} 
	else {
		$button_icon = null; $has_icon = null;
	}

	$color = ($button_style == 'regular' || $button_style == 'see-through') ? $button_color_2 : $button_color;
	
	$stnd_button = $this->getCSSAnimation( $css_animation );
	if( strtolower($color) == 'accent-color' || strtolower($color) == 'extra-color-1' || strtolower($color) == 'extra-color-2' || strtolower($color) == 'extra-color-3') {
		if($button_style != 'see-through')	$stnd_button = " " . $this->getCSSAnimation( $css_animation ) . " regular-button";
	}

	if(!empty($el_class)) {
		$stnd_button .= ' ' . $el_class;
	}
	
	$button_open_tag = '';

	if($button_style == 'regular-tilt') {
		$color = $color . ' tilt';
		$button_open_tag = '<div class="tilt-button-wrap"> <div class="tilt-button-inner">';
	}

	
	//stop regular grad class for material skin 
	$theme_skin = ( !empty($nectar_options['theme-skin']) ) ? $nectar_options['theme-skin'] : 'original';
	$headerFormat = (!empty($nectar_options['header_format'])) ? $nectar_options['header_format'] : 'default';
	if($headerFormat == 'centered-menu-bottom-bar') $theme_skin = 'material';
	
	if($theme_skin == 'material' && $color == 'extra-color-gradient-1') {
		$color = 'm-extra-color-gradient-1';
	} else if( $theme_skin == 'material' && $color == 'extra-color-gradient-2') {
		$color = 'm-extra-color-gradient-2';
	} 
	
	if($color == 'extra-color-gradient-1' && $button_style == 'see-through' || $color == 'extra-color-gradient-2' && $button_style == 'see-through')
		$style_color = $button_style . '-'. strtolower($color);
	else
		$style_color = $button_style . ' '. strtolower($color);

	//margins
	$margins = '';
	if(!empty($margin_top)) {
		$margins .= 'margin-top: '.intval($margin_top).'px; ';
	}
	if(!empty($margin_right)) {
		$margins .= 'margin-right: '.intval($margin_right).'px; ';
	}
	if(!empty($margin_bottom)) {
		$margins .= 'margin-bottom: '.intval($margin_bottom).'px; ';
	}
	if(!empty($margin_left)) {
		$margins .= 'margin-left: '.intval($margin_left).'px;';
	}
	
	$starting_custom_color = '';
	if(!empty($solid_text_color_override) && $button_style == 'regular' || !empty($solid_text_color_override) && $button_style == 'regular-tilt') {
		$starting_custom_color = 'color: '.$solid_text_color_override.'; ';
	}
	
	if(!empty($color_override)) {
		$color_or = 'data-color-override="'. $color_override.'" ';	
		
		if($button_style == 'see-through' || $button_style == 'see-through-2') {
			$starting_custom_color .= 'border-color: '.$color_override.'; color: '.$color_override.';';
		} 
		else if($button_style == 'see-through-3') {
			$starting_custom_color .= 'border-color: '.$color_override.';';
		} else {
			$starting_custom_color .= 'background-color: '.$color_override.';';
		}

	} else {
		$color_or = 'data-color-override="false" ';	
	}
		
	switch ($size) {

		case 'small' :
			$button_open_tag .= '<a class="nectar-button small '. $style_color . $has_icon . $stnd_button.'" style="'. $margins . $starting_custom_color.'" '. $target;
			break;
		case 'medium' :
			$button_open_tag .= '<a class="nectar-button medium ' . $style_color . $has_icon . $stnd_button.'" style="'. $margins . $starting_custom_color.'" '. $target;
			break;
		case 'large' :
			$button_open_tag .= '<a class="nectar-button large '. $style_color . $has_icon . $stnd_button.'" style="'.$margins . $starting_custom_color.'" '. $target;
			break;	
		case 'jumbo' :
			$button_open_tag .= '<a class="nectar-button jumbo '. $style_color . $has_icon . $stnd_button.'" style="' . $margins . $starting_custom_color.'" '. $target;
			break;	
		case 'extra_jumbo' :
			$button_open_tag .= '<a class="nectar-button extra_jumbo '. $style_color . $has_icon . $stnd_button.'" style="' . $margins . $starting_custom_color.'" '. $target;
			break;	
	}
	
	$hover_color_override = (!empty($hover_color_override)) ? ' data-hover-color-override="'. $hover_color_override.'"' : 'data-hover-color-override="false"';
	$hover_text_color_override = (!empty($hover_text_color_override)) ? ' data-hover-text-color-override="'. $hover_text_color_override.'"' :  null;	
	$button_close_tag = null;

	if(strtolower($color) == 'accent-color tilt' || strtolower($color) == 'extra-color-1 tilt' || strtolower($color) == 'extra-color-2 tilt' || strtolower($color) == 'extra-color-3 tilt') $button_close_tag = '</div></div>';

	if($button_style != 'see-through-3d') {
		
		if($color == 'extra-color-gradient-1' || $color == 'extra-color-gradient-2') {
			echo $button_open_tag . ' href="' . $url . '" '.$color_or.$hover_color_override.$hover_text_color_override.'><span class="start loading">' . $text . $button_icon. '</span><span class="hover">' . $text . $button_icon. '</span></a>'. $button_close_tag;
		}
		else {
			echo $button_open_tag . ' href="' . $url . '" '.$color_or.$hover_color_override.$hover_text_color_override.'><span>' . $text . '</span>'. $button_icon . '</a>'. $button_close_tag;
		}
    	
	}
	else {

		$color = (!empty($color_override)) ? $color_override : '#ffffff';
		$border = ($size != 'jumbo') ? 8 : 10;
		if($size =='extra_jumbo') $border = 20;
		echo '
		<div class="nectar-3d-transparent-button" style="'.$margins.'" data-size="'.$size.'">
		     <a href="'.$url.'" '. $target.' class="'.$el_class.'"><span class="hidden-text">'.$text.'</span>
			<div class="inner-wrap">
				<div class="front-3d">
					<svg>
						<defs>
							<mask>
								<rect width="100%" height="100%" fill="#ffffff"></rect>
								<text class="mask-text button-text" fill="#000000" text-anchor="middle">'.$text.'</text>
							</mask>
						</defs>
						<rect fill="'.$color.'" width="100%" height="100%" ></rect>
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



?>