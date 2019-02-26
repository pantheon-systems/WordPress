<?php 

$title = $image = $style = $color_1 = $color_2 = $hotspot_icon  = $tooltip = $tooltip_shadow = $animation = '';
extract(shortcode_atts(array(
	'image' => '',
	'style' => 'color_pulse',
	'color_1' => 'Accent-Color',
	'color_2' => 'light',
	'hotspot_icon' => 'plus_sign',
	'tooltip' => 'hover',
	'tooltip_shadow' => 'none',
	'animation' => '',
), $atts));

$GLOBALS['nectar-image_hotspot-icon'] = $hotspot_icon;
$GLOBALS['nectar-image_hotspot-count'] = 1;
$GLOBALS['nectar-image_hotspot-tooltip-func'] = $tooltip;

if($style == 'color_pulse')
	$color_attr = strtolower($color_1);
else
	$color_attr = strtolower($color_2);

$image_el = null;
$image_class = 'no-img';

if(!empty($image)) {
	if(!preg_match('/^\d+$/',$image)){
		$image_el = '<img src="'.$image.'" alt="hotspot image" />';
	} else {
		$image_el = wp_get_attachment_image($image, 'full');
	}  

	$image_class = null;
}

echo '<div class="nectar_image_with_hotspots '.$image_class.'" data-stlye="'.$style.'" data-hotspot-icon="'.$hotspot_icon.'" data-size="medium" data-color="'.$color_attr.'" data-tooltip-func="'.$tooltip.'" data-tooltip_shadow="'.$tooltip_shadow.'" data-animation="'.$animation.'">';

echo $image_el . do_shortcode($content);

echo '</div>';

?>