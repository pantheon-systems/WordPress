<?php 

extract(shortcode_atts(array("alignment" => "center", "sticky" => "false", "bg_color" => '#ffffff', "link_color" => '#000000'), $atts));

global $nectar_options;

$wrapping_class = ($alignment != 'center') ?  'full-width-section' : 'full-width-content';

echo '<div class="page-submenu" data-bg-color="'.$bg_color.'" data-sticky="'.$sticky.'" data-alignment="'.$alignment.'"><div class="'.$wrapping_class.'"  style="background-color:'.$bg_color.'; color: '.$link_color.';">';
if($wrapping_class == 'full-width-section') echo '<div class="container">';
echo '<a href="#" class="mobile-menu-link"><i class="salient-page-submenu-icon"></i>'.__('Menu','salient').'</a><ul  style="background-color:'.$bg_color.'; color: '.$link_color.';" >'.do_shortcode($content).'</ul>';
if($wrapping_class == 'full-width-section') echo '</div>';
echo'</div></div>';



?>