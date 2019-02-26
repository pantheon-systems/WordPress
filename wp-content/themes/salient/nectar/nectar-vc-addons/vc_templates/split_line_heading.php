<?php
$title = $el_class = $value = $label_value= $units = '';

$array = preg_split("/\r\n|\n|\r/", $content);
$heading_lines = array_filter($array);

echo '<div class="nectar-split-heading">';

foreach($heading_lines as $k => $v) {
	echo '<div class="heading-line"> <div>' . do_shortcode($v) . ' </div> </div>';
}

echo '</div>';