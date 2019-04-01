<?php 

extract(shortcode_atts(array("autorotate"=>'', "disable_height_animation"=>'','style'=>'default', 'color' => '', 'star_rating_color' => 'accent-color', 'add_border' => ''), $atts));

$height_animation_class = null;
if($disable_height_animation == 'true') { $height_animation_class = 'disable-height-animation'; }

$GLOBALS['nectar-testimonial-slider-style'] = $style;

$flickity_markup_opening = ($style == 'multiple_visible' || $style == 'multiple_visible_minimal') ? '<div class="flickity-viewport"> <div class="flickity-slider">' : '';
$flickity_markup_closing = ($style == 'multiple_visible' || $style == 'multiple_visible_minimal') ? '</div></div>' : '';

echo '<div class="col span_12 testimonial_slider '.$height_animation_class.'" data-color="'.$color.'"  data-rating-color="'.$star_rating_color.'" data-add-border="'.$add_border.'" data-autorotate="'.$autorotate.'" data-style="'.$style.'" ><div class="slides">'.$flickity_markup_opening.do_shortcode($content).$flickity_markup_closing.'</div></div>';

?>