<?php 

$cascading_attrs = shortcode_atts(array(
  "image_1_url" => '',
  "image_1_bg_color" => "",
  "image_1_offset_x_sign" => "+",
  "image_1_offset_x" => "",
  "image_1_offset_y_sign" => "+",
  "image_1_offset_y" => "",
  "image_1_rotate_sign" => "+",
  "image_1_rotate" => "none",
  "image_1_animation" => "Fade In",
  "image_1_box_shadow" => "none",
  "image_2_url" => '',
  "image_2_bg_color" => "",
  "image_2_offset_x_sign" => "+",
  "image_2_offset_x" => "",
  "image_2_offset_y_sign" => "+",
  "image_2_offset_y" => "",
  "image_2_rotate_sign" => "+",
  "image_2_rotate" => "none",
  "image_2_animation" => "Fade In",
  "image_2_box_shadow" => "none",
  "image_3_url" => '',
  "image_3_bg_color" => "",
  "image_3_offset_x_sign" => "+",
  "image_3_offset_x" => "",
  "image_3_offset_y_sign" => "+",
  "image_3_offset_y" => "",
  "image_3_rotate_sign" => "+",
  "image_3_rotate" => "none",
  "image_3_animation" => "Fade In",
  "image_3_box_shadow" => "none",
  "image_4_url" => '',
  "image_4_bg_color" => "",
  "image_4_offset_x_sign" => "+",
  "image_4_offset_x" => "",
  "image_4_offset_y_sign" => "+",
  "image_4_offset_y" => "",
  "image_4_rotate_sign" => "+",
  "image_4_rotate" => "none",
  "image_4_animation" => "Fade In",
  "image_4_box_shadow" => "none",
  "animation_timing" => '175',
  "border_radius" => 'none'
),
$atts);

echo '<div class="nectar_cascading_images" data-border-radius="'.$cascading_attrs['border_radius'].'" data-animation-timing="'.$cascading_attrs['animation_timing'].'">';

//find largest transform val

$transform_arr = array(0);
for($i=1;$i<5;$i++){
	if(!empty($cascading_attrs['image_'.$i.'_offset_x']) && $cascading_attrs['image_'.$i.'_offset_x'] != 'none') $transform_arr[] = intval($cascading_attrs['image_'.$i.'_offset_x']);
	if(!empty($cascading_attrs['image_'.$i.'_offset_y'])  && $cascading_attrs['image_'.$i.'_offset_y'] != 'none') $transform_arr[] = intval($cascading_attrs['image_'.$i.'_offset_y']);
}
$transform_arr = max($transform_arr);
switch($transform_arr) {
	case $transform_arr <= 10:
		$divider = 1.15;
		break; 
	case $transform_arr <= 20:
		$divider = 1.35;
		break;
	case $transform_arr <= 30:
		$divider = 1.55;
		break;
	case $transform_arr <= 40:
		$divider = 1.75;
		break;
	case $transform_arr <= 50:
		$divider = 2;
		break;
	case $transform_arr <= 60:
		$divider = 2.25;
		break;
	case $transform_arr <= 70:
		$divider = 2.45;
		break;
	case $transform_arr <= 80:
		$divider = 2.7;
		break;
	case $transform_arr <= 90:
		$divider = 2.85;
		break;
	case $transform_arr < 100:
		$divider = 3;
		break;  
	default:
		$divider = 3;

}

$transform_arr = floor($transform_arr/$divider);

//output layers
for($i=1;$i<5;$i++){

	$image_url = null;
	$image_alt = null;

	if(!empty($cascading_attrs['image_'.$i.'_url'])) {
		
		if(!preg_match('/^\d+$/',$cascading_attrs['image_'.$i.'_url'])){
				
			$image_url = $cascading_attrs['image_'.$i.'_url'];
		
		} else {
			$image_src = wp_get_attachment_image_src($cascading_attrs['image_'.$i.'_url'], 'full');
			
			$image_url = $image_src[0];

			$image_alt = get_post_meta( $cascading_attrs['image_'.$i.'_url'], '_wp_attachment_image_alt', true );
		}
		
	}

	$transform_string = null;
	$transform_x_sign_string = ($cascading_attrs['image_'.$i.'_offset_x_sign'] == '+') ? '': '-';
	$transform_y_sign_string = ($cascading_attrs['image_'.$i.'_offset_y_sign'] == '+') ? '': '-';
	$rotate_sign_string = ($cascading_attrs['image_'.$i.'_rotate_sign'] == '+') ? '': '-';

	$parsed_animation = str_replace(" ","-",$cascading_attrs['image_'.$i.'_animation']);

	if(!empty($cascading_attrs['image_'.$i.'_offset_x'])) $transform_string .='translateX('.$transform_x_sign_string . $cascading_attrs['image_'.$i.'_offset_x'].') '; 
	if(!empty($cascading_attrs['image_'.$i.'_offset_y'])) $transform_string .= 'translateY('.$transform_y_sign_string . $cascading_attrs['image_'.$i.'_offset_y'].') '; 
	if(!empty($cascading_attrs['image_'.$i.'_rotate']) && $cascading_attrs['image_'.$i.'_rotate'] != 'none') $transform_string .= 'rotate('.$rotate_sign_string . $cascading_attrs['image_'.$i.'_rotate'].'deg) ';

	$img_markup = (!empty($image_url)) ? '<div style=" -webkit-transform:'.$transform_string.';  -ms-transform:'.$transform_string.'; transform:'.$transform_string.';" class="img-wrap"> <img src="'.$image_url.'" class="skip-lazy" alt="'.$image_alt.'" /> </div>': null;
	$data_has_bg_img = (!empty($image_url)) ? 'true': 'false';
	$data_has_bg_color = (!empty($cascading_attrs['image_'.$i.'_bg_color'])) ? 'true' : 'false';
	$bg_color_markup = ($data_has_bg_color == 'true') ? '<div class="bg-color" style=" -webkit-transform:'.$transform_string.';  -ms-transform:'.$transform_string.';  transform: '.$transform_string.'; background-color: '.$cascading_attrs['image_'.$i.'_bg_color'].';" data-has-bg-color="'.$data_has_bg_color.'"></div>' : null;
	
	if(!empty($image_url) || $data_has_bg_color == 'true') {
		echo '<div class="cascading-image" data-has-img="'.$data_has_bg_img.'" style=" padding:'.$transform_arr .'%;" data-animation="'.strtolower($parsed_animation).'" data-shadow="'.$cascading_attrs['image_'.$i.'_box_shadow'].'"><div class="inner-wrap">'.$bg_color_markup . $img_markup.'</div></div>';
	}
}

echo '</div>';

?>