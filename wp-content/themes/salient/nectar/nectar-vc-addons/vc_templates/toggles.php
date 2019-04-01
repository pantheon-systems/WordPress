<?php 
extract(shortcode_atts(array("accordion" => 'false', 'style' => 'default'), $atts));  
	
($accordion == 'true') ? $accordion_class = 'accordion': $accordion_class = null ;
echo '<div class="toggles '.$accordion_class.'" data-style="'.$style.'">' . do_shortcode($content) . '</div>'; 

?>




















