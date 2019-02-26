<?php 

return array(
		  "name" => esc_html__("Interactive Map", "js_composer"),
		  "base" => "nectar_gmap",
		  "icon" => "icon-wpb-map",
		  "category" => esc_html__('Nectar Elements', 'js_composer'),
		  "description" => esc_html__('Flexible Custom Map', 'js_composer'),
		  "params" => array(
				array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Map Type", "js_composer"),
			  "param_name" => "map_type",
			  "value" => array(
			     "Google Map" => "google",
				 	 "Leaflet (Recommended)" => "leaflet",
			   ),
			  'save_always' => true,
			  "description" => esc_html__("With the introduction of the new Google Maps Platform (June 2018), Dynamic Google Maps are now a premium service which require a monthly fee. Because of this, we've integrated a free open source map solution known as Leaflet. If you choose to use Google, ensure you have a valid map API key entered into Salient options panel > general settings > css/script related tab.", "js_composer")
			),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Map height", "js_composer"),
		      "param_name" => "size",
		      "description" => esc_html__('Enter map height in pixels. Example: 200.', "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Map Center Point Latitude", "js_composer"),
		      "param_name" => "map_center_lat",
		      "description" => esc_html__("Please enter the latitude for the maps center positioning point.", "js_composer")
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => esc_html__("Map Center Point Longitude", "js_composer"),
		      "param_name" => "map_center_lng",
		      "description" => esc_html__("Please enter the longitude for the maps center positioning point.", "js_composer")
		    ),
		    
		  	array(
		      "type" => "dropdown",
		      "heading" => esc_html__("Map Zoom", "js_composer"),
		      "param_name" => "zoom",
		      'save_always' => true,
		      "value" => array(esc_html__("14 - Default", "js_composer") => 14, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Eanble Zoom In/Out", "js_composer"),
		      "param_name" => "enable_zoom",
					"dependency" => Array('element' => "map_type", 'value' => array('google')),
		      "description" => esc_html__("Do you want users to be able to zoom in/out on the map?", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),

		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Marker Style", "js_composer"),
			  "param_name" => "marker_style",
			  "value" => array(
			     "Default Style" => "default",
				  "Nectar Animated" => "nectar",
			   ),
			  'save_always' => true,
			  "description" => esc_html__("Please select the marker style you would like. Default Style will display the standard map marker and also allow you to optionally override it with an image. Nectar Animated will use a custom Nectar CSS based marker (the modern option).", "js_composer")
			),

		    array(
			  "type" => "dropdown",
			  "heading" => esc_html__("Marker Color", "js_composer"),
			  "param_name" => "nectar_marker_color",
			  "value" => array(
				 "Accent-Color" => "accent-color",
				 "Extra-Color-1" => "extra-color-1",
				 "Extra-Color-2" => "extra-color-2",	
				 "Extra-Color-3" => "extra-color-3"
			   ),
			  'save_always' => true,
			  "dependency" => Array('element' => "map_type", 'value' => array('google')),
			  "description" => esc_html__("Please select the color for your nectar marker. Will only be used with the Nectar Animated Marker Style", "js_composer")
			),
			
			array(
			"type" => "dropdown",
			"heading" => esc_html__("Marker Color", "js_composer"),
			"param_name" => "color",
			"value" => array(
			 "Accent-Color" => "accent-color",
			 "Extra-Color-1" => "extra-color-1",
			 "Extra-Color-2" => "extra-color-2",	
			 "Extra-Color-3" => "extra-color-3"
			 ),
			'save_always' => true,
			"dependency" => Array('element' => "map_type", 'value' => array('leaflet')),
			"description" => esc_html__("Please select the color for your marker.", "js_composer")
		),

		    array(
		      "type" => "attach_image",
		      "heading" => esc_html__("Marker Image", "js_composer"),
		      "param_name" => "marker_image",
		      "value" => "",
		      "description" => esc_html__("Select image from media library. Will only be used with the Default Marker Style.", "js_composer")
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Image Width", "js_composer"),
		      "param_name" => "marker_image_width",
					"dependency" => Array('element' => "map_type", 'value' => array('leaflet')),
		      "description" => esc_html__("Please enter the width in px that you would like your custom marker image to display as", "js_composer")
		    ),
				array(
		      "type" => "textfield",
		      "heading" => esc_html__("Image Height", "js_composer"),
		      "param_name" => "marker_image_height",
					"dependency" => Array('element' => "map_type", 'value' => array('leaflet')),
		      "description" => esc_html__("Please enter the height in px that you would like your custom marker image to display as", "js_composer")
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Marker Animation", "js_composer"),
		      "param_name" => "marker_animation",
					"dependency" => Array('element' => "map_type", 'value' => array('google')),
		      "description" => esc_html__("This will cause your markers to do a quick bounce as they load in.", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
		    
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Greyscale Color", "js_composer"),
		      "param_name" => "map_greyscale",
					"dependency" => Array('element' => "map_type", 'value' => array('google')),
		      "description" => esc_html__("Toggle a greyscale color scheme (will also unlock further custom options)", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
				array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Greyscale Color", "js_composer"),
		      "param_name" => "leaflet_map_greyscale",
					"dependency" => Array('element' => "map_type", 'value' => array('leaflet')),
		      "description" => esc_html__("Toggle a greyscale color scheme", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
				array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Marker Description Info Window(s) Start Open", "js_composer"),
		      "param_name" => "infowindow_start_open",
					"dependency" => Array('element' => "map_type", 'value' => array('leaflet')),
		      "description" => esc_html__("Enabling this will cause the description(s) you set for your markers to be visible to your users before they click on the marker", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
		    array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => "Map Extra Color",
				"param_name" => "map_color",
				"value" => "",
				"dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
				"description" => "Use this to define a main color that will be used in combination with the greyscale option for your map"
			),
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Ultra Flat Map", "js_composer"),
		      "param_name" => "ultra_flat",
		      "dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
		      "description" => esc_html__("This removes street/landmark text & some extra details for a clean look", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
		    array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Dark Color Scheme", "js_composer"),
		      "param_name" => "dark_color_scheme",
		      "dependency" => Array('element' => "map_greyscale", 'not_empty' => true),
		      "description" => esc_html__("Enable this option for a dark colored map (This will override the extra color choice)", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => true),
		    ),
			
		    array(
		      "type" => "textarea",
		      "heading" => esc_html__("Map Marker Locations", "js_composer"),
		      "param_name" => "map_markers",
		      "description" => esc_html__("Please enter the the list of locations you would like with a latitude|longitude|description format. Divide values with linebreaks (Enter). Example:", "js_composer") . "<br/>" . esc_html__("39.949|-75.171|Our Location", "js_composer") . '<br/>' . esc_html__("40.793|-73.954|Our Location #2", "js_composer")
		    ),
		    
		  )
		);
?>