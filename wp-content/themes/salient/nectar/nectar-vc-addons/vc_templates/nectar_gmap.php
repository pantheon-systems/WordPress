<?php

   extract(shortcode_atts(array(
    'map_type' => 'google',
    'size' => '400',
	  "img_link_target" => '',
	  'map_center_lat'=> '', 
	  'map_center_lng'=> '', 
	  'zoom' => '12', 
	  'enable_zoom' => '', 
	  'marker_image'=> '', 
	  'map_greyscale' => '',
	  'map_color' => '',
	  'ultra_flat' => '',
	  'dark_color_scheme' => '',
	  'marker_animation'=> 'false',
	  'map_markers' => '',
	  'marker_style' => 'default',
    'marker_image_width' => '50',
    'marker_image_height' => '50',
	  'nectar_marker_color' => 'accent-color',
    'leaflet_map_greyscale' => '',
    'infowindow_start_open' => '',
    'color' => 'accent-color' //leaflet marker color
	),
	$atts));
	
  if($map_type == 'google') {
  	wp_enqueue_script('nectarMap', get_template_directory_uri() . '/js/map.js', array('jquery'), '8.5.4', TRUE);
  } else if($map_type == 'leaflet') {
    wp_enqueue_script('leaflet'); 
    wp_enqueue_script('nectar_leaflet_map'); 
    wp_enqueue_style('leaflet'); 
  }
  
	$markersArr = array();
	$explodedByBr = explode("\n", $map_markers);	
	$count = 0;
	
	foreach ($explodedByBr as $brExplode) {
		
	    $markersArr[$count] = array();
	  
	    $explodedBySep = explode('|', $brExplode);
	  
	    foreach ($explodedBySep as $sepExploded) {
	        $markersArr[$count][] = $sepExploded;
	    }
	  
	    $count++;
	}

	
	$map_data = null;
	$unique_id = uniqid("map_");
	
	$marker_image_src = null;
	if(!empty($marker_image)) {
		$marker_image_src = wp_get_attachment_image_src($marker_image, 'full');
		$marker_image_src = $marker_image_src[0];
	}
	
  $map_class = ($map_type == 'google') ? 'nectar-google-map' : 'nectar-leaflet-map';
  if($map_class == 'nectar-leaflet-map') {
      $map_greyscale = $leaflet_map_greyscale;
      $nectar_marker_color = $color;
  }
  
  
	echo '<div id="'.$unique_id.'" style="height: '.$size.'px;" class="'.$map_class.'" data-infowindow-start-open="'. $infowindow_start_open .'" data-dark-color-scheme="'. $dark_color_scheme .'" data-marker-style="'.$marker_style.'" data-nectar-marker-color="'.$nectar_marker_color.'" data-ultra-flat="'.$ultra_flat.'" data-greyscale="'.$map_greyscale.'" data-extra-color="'.$map_color.'" data-enable-animation="'.$marker_animation.'" data-enable-zoom="'.$enable_zoom.'" data-zoom-level="'.$zoom.'" data-center-lat="'.$map_center_lat.'" data-center-lng="'.$map_center_lng.'" data-marker-img="'.$marker_image_src.'"></div>';
	echo '<div class="'.$unique_id.' map-marker-list">';
		$count = 0;
		for($i = 1; $i <= sizeof($markersArr); $i++){
			
			if(empty($markersArr[$count][0])) $markersArr[$count][0] = null;
			if(empty($markersArr[$count][1])) $markersArr[$count][1] = null;
			if(empty($markersArr[$count][2])) $markersArr[$count][2] = null;
		
			echo '<div class="map-marker" data-marker-image-width="'. $marker_image_width .'" data-marker-image-height="'. $marker_image_height .'" data-lat="'.$markersArr[$count][0].'" data-lng="'.$markersArr[$count][1].'" data-mapinfo="'.$markersArr[$count][2].'"></div>';

			$count++;
		}
	echo '</div>';
	
?>