<?php 

extract(shortcode_atts(array(
  "image_url" => '',
  "image_2_url" => ''
),
$atts));

wp_enqueue_script('twentytwenty');
wp_enqueue_style('twentytwenty');

$alt_tag = null;
$alt_tag_2 = null;

if(!empty($image_url)) {
		
	if(!preg_match('/^\d+$/',$image_url)){
			
		$image_url = $image_url;
	
	} else {

		$wp_img_alt_tag = get_post_meta( $image_url, '_wp_attachment_image_alt', true );
		if(!empty($wp_img_alt_tag)) $alt_tag = $wp_img_alt_tag;

		$image_src = wp_get_attachment_image_src($image_url, 'full');
		
		$image_url = $image_src[0];
	}
	
} else {
	$image_url = vc_asset_url( 'images/before.jpg' );
}

if(!empty($image_2_url)) {
		
	if(!preg_match('/^\d+$/',$image_2_url)){
			
		$image_2_url = $image_2_url;
	
	} else {
		
		$wp_img_alt_tag_2 = get_post_meta( $image_2_url, '_wp_attachment_image_alt', true );
		if(!empty($wp_img_alt_tag_2)) $alt_tag_2 = $wp_img_alt_tag_2;

		$image_src = wp_get_attachment_image_src($image_2_url, 'full');
		$image_2_url = $image_src[0];
	}
	
} else {
	$image_2_url = vc_asset_url( 'images/after.jpg' );
}

echo "<div class='twentytwenty-container'>
  <img class='skip-lazy' src='".$image_url."' alt='".$alt_tag."'>
  <img class='skip-lazy' src='".$image_2_url."' alt='".$alt_tag_2."'>
</div>";

?>