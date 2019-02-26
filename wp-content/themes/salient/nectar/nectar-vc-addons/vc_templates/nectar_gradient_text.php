<?php
$text = $heading_tag = $gradient_direction = $color = $margin_top = $margin_right = $margin_bottom = $margin_left = '';
extract(shortcode_atts(array(
	'heading_tag' => 'h1',
	'text' => '',
	'color' => 'extra-color-gradient-1',
	'gradient_direction' => '',
	'margin_top' => '',
	'margin_right' => '',
	'margin_bottom' => '', 
	'margin_left' => ''
), $atts));

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

echo '<div class="nectar-gradient-text" data-direction="'.$gradient_direction.'" data-color="'.$color.'" style="'.$margins.'"><'.$heading_tag.'>'.$text.'</'.$heading_tag.'></div>';