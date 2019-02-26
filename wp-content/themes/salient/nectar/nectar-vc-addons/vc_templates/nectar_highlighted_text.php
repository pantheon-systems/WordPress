<?php
$text = $color = '';
extract(shortcode_atts(array(
	'highlight_color' => '',
  'style' => 'full_text'
), $atts));

$using_custom_color = (!empty($highlight_color)) ? 'true' : 'false';

echo '<div class="nectar-highlighted-text" data-style="'.$style.'" data-using-custom-color="'.$using_custom_color.'" data-color="'.$highlight_color.'" style="">'.$content.'</div>';