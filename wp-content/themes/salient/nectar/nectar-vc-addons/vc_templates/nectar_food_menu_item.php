<?php
extract(shortcode_atts(array("style" => "default", "item_name" => '', 'item_name_heading_tag' => 'h4', "item_price" => "", 'item_description' => '', 'class' => ''), $atts));


$line_markup = '<div class="line_spacer"></div>';

echo '<div class="nectar_food_menu_item '.$class.'" data-style="'.$style.'"><div class="inner"><div class="item_name"><'.$item_name_heading_tag.'>'.$item_name.'</'.$item_name_heading_tag.'></div>'.$line_markup.'<div class="item_price"><'.$item_name_heading_tag.'>'.$item_price.'</'.$item_name_heading_tag.'></div></div><div class="item_description">'.$item_description.'</div></div>';
?>