<?php 

extract(shortcode_atts(array(
	"link_style" => "play_button", 
  'hover_effect' => 'default', 
	"font_style" => "p", 
	"video_url" => '#', 
	"link_text" => "", 
	"play_button_color" => "default", 
	"nectar_button_color" => "default", 
	'nectar_play_button_color' => 'Accent-Color', 
	'image_url' => '', 
	'border_radius' => 'none',
	'play_button_size' => 'default',
	'box_shadow' => ''), $atts));

$extra_attrs = ($link_style == 'nectar-button') ? 'data-color-override="false"': null;
$the_link_text = ($link_style == 'nectar-button') ? $link_text : '<span class="play"><span class="inner-wrap"><svg version="1.1"
	 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600px" height="800px" x="0px" y="0px" viewBox="0 0 600 800" enable-background="new 0 0 600 800" xml:space="preserve"><path fill="none" d="M0-1.79v800L600,395L0-1.79z"></path> </svg></span></span>';
$the_color = ($link_style == 'nectar-button') ? $nectar_button_color : $play_button_color;

if($link_style == 'play_button_with_text') {
	$the_color = $nectar_play_button_color;
}

if($link_style == 'play_button_2') {

	  $image = null;

	  if(!empty($image_url)) {
        	if(!preg_match('/^\d+$/',$image_url)){
        		$image = '<img src="'.$image_url.'" alt="video preview" />';
        	} else {
        		$image = wp_get_attachment_image($image_url, 'full');
        	}  
		}

	echo '<div class="nectar-video-box" data-color="'.strtolower($nectar_play_button_color).'" data-play-button-size="'.$play_button_size.'" data-border-radius="'.$border_radius.'" data-hover="'.$hover_effect.'" data-shadow="'.$box_shadow.'"><div class="inner-wrap"><a href="'.$video_url.'" class="full-link pp"></a>'. $image;
}

$pbwt = ($link_style == 'play_button_with_text') ? '<span class="link-text"><'.$font_style.'>'.$link_text.'</'.$font_style.'></span>' : null;

echo '<a href="'.$video_url.'" '.$extra_attrs.' data-color="'.strtolower($the_color).'" class="'.$link_style.' large nectar_video_lightbox pp">'.$the_link_text .$pbwt.'</a>';

if($link_style == 'play_button_2') echo '</div></div>';

?>