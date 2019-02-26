<?php 

extract(shortcode_atts(array(
	"columns" => "1", 
	"column_layout_using_2_columns" => 'even',
	"column_layout_using_3_columns" => 'even',
	"column_layout_using_4_columns" => 'even',
	"col_1_text_align" => "left",
	"col_2_text_align" => "left",
	"col_3_text_align" => "left",
	"col_4_text_align" => "left",
	"col_1_content" => '',
	"col_2_content" => '',
	"col_3_content" => '',
	"col_4_content" => '',
	"cta_1_text" => '',
	"cta_1_url" => '',
	"cta_1_open_new_tab" => '',
	"cta_2_text" => '',
	"cta_2_url" => '',
	"cta_2_open_new_tab" => '',
	"open_new_tab" => '',
	"url" => '',
	"hover_color" => 'accent-color',
	'font_family' => 'p'
), $atts));


if($columns == '2') {
	$column_layout_to_use = $column_layout_using_2_columns;
} else if($columns == '3') {
	$column_layout_to_use = $column_layout_using_3_columns;
} else if($columns == '4') {
	$column_layout_to_use = $column_layout_using_4_columns;
} else {
	$column_layout_to_use = 'default';
}

$hasbtn_class = (!empty($cta_1_text) || !empty($cta_2_text)) ? 'has-btn' : null;

echo '<div class="nectar-hor-list-item '.$hasbtn_class.'" data-font-family="'.$font_family.'" data-color="'.$hover_color.'" data-columns="'.$columns.'" data-column-layout="'.$column_layout_to_use.'">'; 
	for($i = 0; $i < intval($columns); $i++) {

		$index_to_grab = $i+1;

		if(!isset($atts['col_'.$index_to_grab.'_text_align'])) { $atts['col_'.$index_to_grab.'_text_align'] = null; }
		if(!isset($atts['col_'.$index_to_grab.'_content'])) { $atts['col_'.$index_to_grab.'_content'] = null; }

		$cta_1_markup = $cta_2_markup = null;

		//add btns into last col
		if($index_to_grab == intval($columns)) {
			if(!empty($cta_1_text)) {

				$btn_target_markup = (!empty($cta_1_open_new_tab) && $cta_1_open_new_tab == 'true' ) ? 'target="_blank"' : null;
				$cta_1_markup = '<a class="nectar-list-item-btn" href="'.$cta_1_url.'" '.$btn_target_markup.'>'.$cta_1_text.'</a>';
			}
			if(!empty($cta_2_text)) {
				$btn_target_markup = (!empty($cta_2_open_new_tab) && $cta_2_open_new_tab == 'true' ) ? 'target="_blank"' : null;
				$cta_2_markup = '<a class="nectar-list-item-btn second" href="'.$cta_2_url.'" '.$btn_target_markup.'>'.$cta_2_text.'</a>';
			}
		}

		echo '<div class="nectar-list-item" data-text-align="'.$atts['col_'.$index_to_grab.'_text_align'].'">'.$atts['col_'.$index_to_grab.'_content'].$cta_1_markup.$cta_2_markup.'</div>';
	}

$url_markup = null;

if(!empty($url)) {
	$target = null;
	if(!empty($open_new_tab) && $open_new_tab == 'true'){
		$target = 'target="_blank"';
	}
	$url_markup = '<a class="full-link" '.$target.' href="'.$url.'"></a>';
}

echo $url_markup.'</div>';

?>