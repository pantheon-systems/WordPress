<?php 

extract(shortcode_atts(array("title" => "", "link_url" => "#", "link_new_tab" => 'false'), $atts));

$new_tab_markup = ($link_new_tab == 'true') ? 'target="_blank"' : null;

echo '<li><a '.$new_tab_markup.' href="'.$link_url.'">'.$title.'</a></li>';

?>