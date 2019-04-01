<?php 

extract(shortcode_atts(array("title" => 'Title', 'color' => 'Accent-Color'), $atts));  

echo '<div class="toggle '.strtolower($color).'"><h3><a href="#"><i class="icon-plus-sign"></i>'. $title .'</a></h3><div>' . do_shortcode($content) . '</div></div>';
	
?>