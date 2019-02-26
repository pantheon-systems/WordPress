<?php 

extract(shortcode_atts(array("carousel_title" => '', "scroll_speed" => 'medium', 'loop' => 'false', 'flickity_fixed_content' => '', 
'flickity_formatting' => 'default', 'easing' => 'easeInExpo', 'autorotate' => '', 'enable_animation' => '', 'delay' => '', 
'autorotation_speed' => '5000','column_padding' => '' ,'script' => 'carouFredSel', 
'desktop_cols' => '4', 'desktop_small_cols' => '3', 'tablet_cols' => '2','mobile_cols' => '1', 
'cta_button_text' => '', 'cta_button_url' => '', 'cta_button_open_new_tab' => '', 
'button_color' => '', 'enable_column_border' => '', 'border_radius' => 'none', 
'pagination_alignment_flickity' => 'default',
'column_color' => '', 'desktop_cols_flickity' => '3', 'desktop_small_cols_flickity' => '3', 
'tablet_cols_flickity' => '2'), $atts));


if( isset($_GET['vc_editable']) ) {
	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
	//imit script choices on front end editor
	if($nectar_using_VC_front_end_editor && $script != 'flickity') {
		$script = 'flickity';
	}
}

$GLOBALS['nectar-carousel-script'] = $script;
$GLOBALS['nectar_carousel_column_color'] = $column_color;

if($script == 'carouFredSel') {
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
	<ul class="row carousel" data-scroll-speed="' . $scroll_speed . '" data-easing="' . $easing . '" data-autorotate="' . $autorotate . '">';

	echo $carousel_html . do_shortcode($content) . '</ul></div>';
} else if($script == 'owl_carousel') {
	$delay = intval($delay);
	echo '<div class="owl-carousel" data-enable-animation="'.$enable_animation.'" data-loop="'.$loop.'"  data-animation-delay="'.$delay.'" data-autorotate="' . $autorotate . '" data-autorotation-speed="'.$autorotation_speed.'" data-column-padding="'.$column_padding.'" data-desktop-cols="'.$desktop_cols.'" data-desktop-small-cols="'.$desktop_small_cols.'" data-tablet-cols="'.$tablet_cols.'" data-mobile-cols="'.$mobile_cols.'">';
	echo do_shortcode($content);
	echo '</div>';
} else if($script == 'flickity') {
	
	if($flickity_formatting == 'fixed_text_content_fullwidth') {
		echo '<div class="nectar-carousel-flickity-fixed-content"> <div class="nectar-carousel-fixed-content">';
		echo do_shortcode($flickity_fixed_content);
		
		if(!empty($cta_button_text)) {
			
			global $nectar_options;
			
			$button_color = strtolower($button_color);
			$regular_btn_class = ' regular-button';
			
			$btn_text_markup = '<span>'.$cta_button_text.'</span> <i class="icon-button-arrow"></i>';
			
			if($button_color == 'extra-color-gradient-1' || $button_color == 'extra-color-gradient-2') {
				$regular_btn_class = '';
				$btn_text_markup = '<span class="start">'.$cta_button_text.' <i class="icon-button-arrow"></i></span><span class="hover">'.$cta_button_text.' <i class="icon-button-arrow"></i></span>';
			}
			
			if($nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-1') {
				$button_color = 'm-extra-color-gradient-1';
				$btn_text_markup = '<span>'.$cta_button_text.'</span> <i class="icon-button-arrow"></i>';
			} else if( $nectar_options['theme-skin'] == 'material' && $button_color == 'extra-color-gradient-2') {
				$button_color = 'm-extra-color-gradient-2';
				$btn_text_markup = '<span>'.$cta_button_text.'</span> <i class="icon-button-arrow"></i>';
			} 
			
			$btn_target_markup = (!empty($cta_button_open_new_tab) && $cta_button_open_new_tab == 'true' ) ? 'target="_blank"' : null;
			
			echo '<div><a class="nectar-button large regular '. $button_color .  $regular_btn_class . ' has-icon" href="'.$cta_button_url.'" '.$btn_target_markup.' data-color-override="false" data-hover-color-override="false" data-hover-text-color-override="#fff">'.$btn_text_markup.'</a></div>';
		}
		
		echo '</div>';
		
	}
	
	$flickity_markup_opening = '<div class="flickity-viewport"> <div class="flickity-slider">';
	$flickity_markup_closing = '</div></div>';
	
	echo '<div class="nectar-flickity not-initialized nectar-carousel" data-pagination-alignment="'.$pagination_alignment_flickity.'" data-border-radius="'.$border_radius.'" data-column-border="'.$enable_column_border.'" data-column-padding="'.$column_padding.'" data-format="'.$flickity_formatting.'" data-autoplay="'.$autorotate.'" data-autoplay-dur="'.$autorotation_speed.'"  data-controls="material_pagination" data-desktop-columns="'.$desktop_cols_flickity.'" data-small-desktop-columns="'.$desktop_small_cols_flickity.'" data-tablet-columns="'.$tablet_cols_flickity.'" data-column-color="'.$column_color.'">';
		echo $flickity_markup_opening . do_shortcode($content) . $flickity_markup_closing;
	echo '</div>';
	
	if($flickity_formatting == 'fixed_text_content_fullwidth') {
		echo '</div>';
	}
	
}

?>