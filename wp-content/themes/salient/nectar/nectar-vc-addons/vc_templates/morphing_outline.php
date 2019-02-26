<?php


$title = $el_class = $value = $label_value= $units = '';
extract(shortcode_atts(array(
'starting_color' => '#27CFC3',
'hover_color' => '#ffffff',
'border_thickness' => '10',

), $atts));


$output = '<div class="morphing-outline" data-starting-color="'.$starting_color.'" data-hover-color="'.$hover_color.'" data-border-thickness="'.$border_thickness.'">';
$output .= '<div class="inner">'.do_shortcode($content).'</div></div>';


echo $output;