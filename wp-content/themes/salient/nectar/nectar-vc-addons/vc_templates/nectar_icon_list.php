<?php 

extract(shortcode_atts(array("columns" => "", "direction" => 'vertical', "columns" => '3', "animate" => "", "color" => 'default', 'icon_size' => '', 'icon_style' => 'border'), $atts));

$GLOBALS['nectar-list-item-count'] = 1;
$GLOBALS['nectar-list-item-icon-color'] = $color;
$GLOBALS['nectar-list-item-icon-style'] = $icon_style;

echo '<div class="nectar-icon-list" data-icon-color="'.strtolower($color).'" data-icon-style="'.$icon_style.'" data-columns="'.$columns.'" data-direction="'.$direction.'" data-icon-size="'.$icon_size.'" data-animate="'.$animate.'">'.do_shortcode($content).'</div>';


?>