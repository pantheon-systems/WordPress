<?php 

$top = $left = $position = '';
extract(shortcode_atts(array(
	'top' => '',
	'left' => '',
	'position' => 'top',
), $atts));


if( isset($_GET['vc_editable']) ) {
	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
} else {
	$nectar_using_VC_front_end_editor = false;
}

if($nectar_using_VC_front_end_editor) {
	$hotspot_icon = '';
	$click_class = null;
} else {
	$hotspot_icon = ($GLOBALS['nectar-image_hotspot-icon'] == 'plus_sign') ? '': $GLOBALS['nectar-image_hotspot-count'];
	$click_class = ($GLOBALS['nectar-image_hotspot-tooltip-func'] == 'click') ? 'click': null;
}


$tooltip_content_class = (empty($content)) ? 'empty-tip' : null;

echo '<div class="nectar_hotspot_wrap" style="top: '.$top.'; left: '.$left.';"><div class="nectar_hotspot '.$click_class.'"><span>'.$hotspot_icon.'</span></div><div class="nttip '.$tooltip_content_class.'" data-tooltip-position="'.$position.'"><div class="inner">'.$content.'</div></div></div>';

if( ! $nectar_using_VC_front_end_editor) {
	$GLOBALS['nectar-image_hotspot-count']++;
}


?>