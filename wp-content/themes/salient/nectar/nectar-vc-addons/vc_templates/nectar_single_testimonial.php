<?php 

extract(shortcode_atts(array(
	'testimonial_style' => 'small_modern',
  'quote' => '',
  'image' => '',
  'name' => '',
  'subtitle' => '',
  'color' => '',
	'add_image_shadow' => ''
), $atts));

$bg_markup = null;
$image_icon_markup = null;

if(!empty($image)){
	if(preg_match('/^\d+$/',$image)){
		$image_src = wp_get_attachment_image_src($image, 'medium');
		$image = $image_src[0];
	}

	$bg_markup = 'style="background-image: url('.$image.');"';
  
  $image_icon_markup = '<div data-shadow="' . $add_image_shadow . '" class="image-icon " '.$bg_markup.'></div>';

}

$open_quote = ($testimonial_style == 'basic') ? '&#8220;' : null; 
$close_quote = ($testimonial_style == 'basic') ? '&#8221;' : null; 

if($testimonial_style != 'basic' && $testimonial_style != 'basic_left_image') {
	$open_quote = '<span class="open-quote">&#8221;</span>'; 
}


echo '<blockquote class="nectar_single_testimonial" data-color="'.strtolower($color).'" data-style="'.$testimonial_style.'">';  
echo '<div class="inner">';
echo ' <p>'.$open_quote.$quote.$close_quote.' </p>';
echo $image_icon_markup.'<span class="wrap"><span>'.$name.'</span><span class="title">'.$subtitle.'</span></span>';
echo '</div></blockquote>';

?>