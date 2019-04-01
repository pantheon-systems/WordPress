<?php 

$is_admin = is_admin();

$portfolio_types = ($is_admin) ? get_terms('project-type') : array('All' => 'all');

	$types_options = array("All" => "all");
	$types_options_2 = array("Default" => "default");

	if($is_admin) {
	
		foreach ($portfolio_types as $type) {
			$types_options[$type->slug] = $type->slug;
			$types_options_2[$type->slug] = $type->slug;
		}
		

	} else {
		$types_options['All'] = 'all';
		$types_options_2['All'] = 'all';
	}


	return array(
	  "name" => esc_html__("Portfolio", "js_composer"),
	  "base" => "nectar_portfolio",
	  "weight" => 8,
	  "icon" => "icon-wpb-portfolio",
	  "category" => esc_html__('Nectar Elements', 'js_composer'),
	  "description" => esc_html__('Add a portfolio element', 'js_composer'),
	  "params" => array(
		array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Layout", "js_composer"),
		  "param_name" => "layout",
		  "admin_label" => true,
		  "value" => array(
				  "4 Columns" => "4",
			    "3 Columns" => "3",
			    "2 Columns" => "2",
			    "Fullwidth" => "fullwidth",
			    "Constrained Fullwidth" => "constrained_fullwidth"
			),
		  "description" => esc_html__("Please select the layout you would like for your portfolio. The Constrained Fullwidth option will allow your projects to display with the same formatting options only available to full width layout, but will limit the width to your website container area.", "js_composer"),
		  'save_always' => true
		),
		array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Constrain Max Columns to 4?", "js_composer"),
	      "param_name" => "constrain_max_cols",
	      "description" => esc_html__("This will change the max columns to 4 (default is 5 for fullwidth). Activating this will make it easier to create a grid with no empty spaces at the end of the list on all screen sizes.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "layout", 'value' => 'fullwidth')
	    ),
	    array(
		  "type" => "dropdown_multi",
		  "heading" => esc_html__("Portfolio Categories", "js_composer"),
		  "param_name" => "category",
		  "admin_label" => true,
		  "value" => $types_options,
		  'save_always' => true,
		  "description" => esc_html__("Please select the categories you would like to display for your portfolio. You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
		),
		array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Starting Category", "js_composer"),
		  "param_name" => "starting_category",
		  "admin_label" => false,
		  "value" => $types_options_2,
		  'save_always' => true,
		  "description" => esc_html__("Please select the category you would like you're portfolio to start filtered on.", "js_composer"),
		  "dependency" => Array('element' => "enable_sortable", 'not_empty' => true)
		),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Project Style", "js_composer"),
		  "param_name" => "project_style",
		  "admin_label" => true,
		  'save_always' => true,
		  "value" => array(
			    "Meta below thumb w/ links on hover" => "1",
			    "Meta on hover + entire thumb link" => "2",
			    "Meta on hover w/ zoom + entire thumb link" => "7",
			    "Meta overlaid - bottom left aligned" => "8",
			    "Meta overlaid w/ zoom effect on hover" => "3",
			    "Meta overlaid w/ zoom effect on hover alt" => "5",
			    "Meta from bottom on hover + entire thumb link" => "4",
			    "3D Parallax on hover" => "6",
					"Meta below thumb w/ shadow hover" => "9"
			),
		  "description" => esc_html__("Please select the style you would like your projects to display in ", "js_composer")
		),
		array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Item Spacing", "js_composer"),
		  "param_name" => "item_spacing",
		  'save_always' => true,
		  "value" => array(
		  		"Default" => "default",
			    "1px" => "1px",
			    "2px" => "2px",
			    "3px" => "3px",
			    "4px" => "4px",
			    "5px" => "5px",
			    "6px" => "6px",
			    "7px" => "7px",
			    "8px" => "8px",
			    "9px" => "9px",
			    "10px" => "10px",
			    "15px" => "15px",
			    "20px" => "20px"
			),
		  "description" => esc_html__("Please select the spacing you would like between your items. ", "js_composer")
		),
		array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Masonry Style", "js_composer"),
	      "param_name" => "masonry_style",
	      "description" => esc_html__('This will allow your portfolio items to display in a masonry layout as opposed to a fixed grid. When using a fullwidth layout, project image sizes will be determined based on the size set in each project via the "Masonry Item Sizing" field.', "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
	    ),
			
			array(
		      "type" => 'checkbox',
		      "heading" => esc_html__("Bypass Image Cropping", "js_composer"),
		      "param_name" => "bypass_image_cropping",
		      "description" => esc_html__("Enabling this will cause your portfolio to bypass the default Salient image cropping which varies based on project settings/above layout selection. The result will be a traditional masonry layout rather than a structured grid.", "js_composer"),
		      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
		    ),

	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Enable Sortable", "js_composer"),
	      "param_name" => "enable_sortable",
	      "description" => esc_html__("Checking this box will allow your portfolio to display sortable filters", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
	    ),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Horizontal Filters", "js_composer"),
	      "param_name" => "horizontal_filters",
	      "description" => esc_html__("This will allow your filters to display horizontally instead of in a dropdown. (Only used if you enable sortable above.)", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true'),
	      "dependency" => Array('element' => "enable_sortable", 'not_empty' => true)
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Filter Alignment", "js_composer"),
		  "param_name" => "filter_alignment",
		  "value" => array(
		     "Default" => "default",
			 "Centered" => "center",
			 "Left" => "left"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "horizontal_filters", 'not_empty' => true),
		  "description" => esc_html__("Please select the alignment you would like for your horizontal filters", "js_composer")
		),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Filter Color Scheme", "js_composer"),
		  "param_name" => "filter_color",
		  "value" => array(
		     "Default" => "default",
			 "Accent-Color" => "accent-color",
			 "Extra-Color-1" => "extra-color-1",
			 "Extra-Color-2" => "extra-color-2",	
			 "Extra-Color-3" => "extra-color-3",
			 "Accent-Color Underline" => "accent-color-underline",
			 "Extra-Color-1 Underline" => "extra-color-1-underline",
			 "Extra-Color-2 Underline" => "extra-color-2-underline",	
			 "Extra-Color-3 Underline" => "extra-color-3-underline",
			 "Black" => "black"
		   ),
		  'save_always' => true,
		  "dependency" => Array('element' => "enable_sortable", 'not_empty' => true),
		  "description" => esc_html__("Please select the color scheme you would like for your filters. Only applies to full width inline filters and regular dropdown filters", "js_composer")
		),

	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Enable Pagination", "js_composer"),
	      "param_name" => "enable_pagination",
	      "description" => esc_html__("Would you like to enable pagination for this portfolio?", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Pagination Type", "js_composer"),
		  "param_name" => "pagination_type",
		  "admin_label" => true,
		  "value" => array(	
			    'Default' => 'default',
			    'Infinite Scroll' => 'infinite_scroll',
			),
		  'save_always' => true,
		  "description" => esc_html__("Please select your pagination type here.", "js_composer"),
		  "dependency" => Array('element' => "enable_pagination", 'not_empty' => true)
		),
	    array(
	      "type" => "textfield",
	      "heading" => esc_html__("Projects Per Page", "js_composer"),
	      "param_name" => "projects_per_page",
	      "description" => esc_html__("How many projects would you like to display per page? If pagination is not enabled, will simply show this number of projects. Enter as a number example \"20\"", "js_composer")
	    ),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Project Offset", "js_composer"),
				"param_name" => "project_offset",
				"description" => esc_html__("Will not be used when \"Enable Pagination\" is on - Optioinally enter a number e.g. \"2\" to offset your project by.", "js_composer")
			),
	    array(
	      "type" => 'checkbox',
	      "heading" => esc_html__("Lightbox Only", "js_composer"),
	      "param_name" => "lightbox_only",
	      "description" => esc_html__("This will remove the single project page from being accessible thus rendering your portfolio into only a gallery.", "js_composer"),
	      "value" => Array(esc_html__("Yes, please", "js_composer") => 'true')
	    ),
	    array(
		  "type" => "dropdown",
		  "heading" => esc_html__("Load In Animation", "js_composer"),
		  "param_name" => "load_in_animation",
		  'save_always' => true,
		  "value" => array(
			    "None" => "none",
			    "Fade In" => "fade_in",
			    "Fade In From Bottom" => "fade_in_from_bottom",
			    "Perspective Fade In" => "perspective"
			),
		  "description" => esc_html__("Please select the style you would like your projects to display in ", "js_composer")
		)
	  )
	);

?>