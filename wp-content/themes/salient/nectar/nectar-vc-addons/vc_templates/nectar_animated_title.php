<?php
$text = $heading_tag = $color = '';
extract(shortcode_atts(array(
	'heading_tag' => 'h6',
	'text' => '',
	'color' => 'accent-color',
	'text_color' => '#ffffff',
	'style' => ''
), $atts));

$style_tag = ( !empty($text_color) ) ? 'style="color: '.$text_color.';"' : null;

echo '<div class="nectar-animated-title" data-style="'.$style.'" data-color="'.strtolower($color).'"><div class="nectar-animated-title-outer"><div class="nectar-animated-title-inner"><div class="wrap"> <'.$heading_tag.' '.$style_tag.'>'.$text.'</'.$heading_tag.'></div></div></div></div>';