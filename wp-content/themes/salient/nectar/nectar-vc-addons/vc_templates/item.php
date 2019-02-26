<?php 


if( isset($_GET['vc_editable']) ) {
	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
} else {
	$nectar_using_VC_front_end_editor = false;
}

//imit script choices on front end editor
if($nectar_using_VC_front_end_editor) {
	$nectar_carousel_script_store = 'flickity';
} else {
	$nectar_carousel_script_store = $GLOBALS['nectar-carousel-script'];
}

if($nectar_carousel_script_store == 'carouFredSel') {
	echo '<li class="col span_4">' . do_shortcode($content) . '</li>';
} else if($nectar_carousel_script_store == 'owl_carousel') {
	echo '<div class="carousel-item">' . do_shortcode($content) . '</div>';
} else if($nectar_carousel_script_store == 'flickity') {
	$column_bg_markup = (!empty($GLOBALS['nectar_carousel_column_color'])) ? 'style=" background-color: ' . sanitize_text_field($GLOBALS['nectar_carousel_column_color']) . ';"': '';
	echo '<div class="cell"><div class="inner-wrap-outer"><div class="inner-wrap" '.$column_bg_markup.'>' . do_shortcode($content) . '</div></div></div>';
}

?>