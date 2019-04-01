<?php 
  

  
$order_by_values = array(
  '',
  esc_html__('Date', 'js_composer' ) => 'date',
  esc_html__('ID', 'js_composer' ) => 'ID',
  esc_html__('Author', 'js_composer' ) => 'author',
  esc_html__('Title', 'js_composer' ) => 'title',
  esc_html__('Modified', 'js_composer' ) => 'modified',
  esc_html__('Random', 'js_composer' ) => 'rand',
  esc_html__('Menu order', 'js_composer' ) => 'menu_order',
);

$order_way_values = array(
  '',
  esc_html__('Descending', 'js_composer' ) => 'DESC',
  esc_html__('Ascending', 'js_composer' ) => 'ASC',
);

$is_admin = is_admin();


$post_types = array('Posts' => 'posts');


$blog_types = ($is_admin) ? get_categories() : array('All' => 'all');

$blog_options = array("All" => "all");

if($is_admin) {
	foreach ($blog_types as $type) {
		if(isset($type->name) && isset($type->slug))
			$blog_options[htmlspecialchars($type->name)] = htmlspecialchars($type->slug);
	}
} else {
	$blog_options['All'] = 'all';
}
    
    
    
$woo_args = array(
	'taxonomy' => 'product_cat',
);

global $woocommerce;

if($woocommerce) {
  
  $post_types["Products"] = 'products';
  
  $woo_types = ($is_admin) ? get_categories($woo_args) : array('All' => 'all');
  $woo_options = array("All" => "all");

  if($is_admin) {
  	foreach ($woo_types as $type) {
  		$woo_options[$type->name] = $type->slug;
  	}
  } else {
  	$woo_options['All'] = 'all';
  }

} else {
  $woo_options['All'] = 'all';
}





  return array(
    'name' => __( 'Category Grid', 'js_composer' ),
    'base' => 'nectar_category_grid',
    'icon' => 'icon-wpb-portfolio',
    "category" => esc_html__('Nectar Elements', 'js_composer'),
    'description' => esc_html__('Show categories in a stylish grid', 'js_composer' ),
    'params' => array(
    array(
      'type' => 'dropdown',
      'heading' => __( 'Post Type', 'js_composer' ),
      'param_name' => 'post_type',
      'value' => $post_types,
      'save_always' => true,
      "admin_label" => true,
      'description' => 'Select the post type you wish to display the categories from.',
    ),
    array(
  	  "type" => "dropdown_multi",
  	  "heading" => esc_html__("Product Categories", "js_composer"),
  	  "param_name" => "product_category",
  	  "admin_label" => true,
  	  "value" => $woo_options,
  	  'save_always' => true,
      "dependency" => array('element' => "post_type", 'value' => 'products'),
  	  "description" => esc_html__("Please select the categories you would like to display in the grid. You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
  	),
    array(
      "type" => "dropdown_multi",
      "heading" => esc_html__("Blog Categories", "js_composer"),
      "param_name" => "blog_category",
      "admin_label" => true,
      "value" => $blog_options,
      'save_always' => true,
      "dependency" => array('element' => "post_type", 'value' => 'posts'),
      "description" => esc_html__("Please select the categories you would like to display for your blog. You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
    ),
    
    
    array(
      'type' => 'dropdown',
      'heading' => __( 'Text Content Alignment', 'js_composer' ),
      'param_name' => 'text_content_alignment',
      'value' => array(
        'Top Left' => 'top_left',
        'Top Middle' => 'top_middle',
        'Top Right' => 'top_right',
        'Middle' => 'middle',
        'Bottom Left' => 'bottom_left',
        'Bottom Middle' => 'bottom_middle',
        'Bottom Right' => 'bottom_right',
      ),
      'save_always' => true,
      'description' => __( 'Select the alignment of your text content.', 'salient')
    ),
    
    array(
      'type' => 'dropdown',
      'heading' => __( 'Subtext', 'js_composer' ),
      'param_name' => 'subtext',
      'value' => array(
        'None' => 'none',
        'Category Item Count' => 'cat_item_count',
        'Custom' => 'custom',
      ),
      'save_always' => true,
      'description' => __( 'Select what will display under the category names.', 'salient')
    ),
    array(
        "type" => "textfield",
        "heading" => esc_html__("Text Content", "js_composer"),
        "param_name" => "custom_subtext",
        "admin_label" => true,
        "dependency" => array('element' => "subtext", 'value' => 'custom'),
        "description" => esc_html__("Enter custom text that will be shown below each category title", "js_composer")
      ),


      array(
        'type' => 'dropdown',
        'heading' => __( 'Columns', 'js_composer' ),
        'param_name' => 'columns',
        'value' => array(
          '4' => '4',
          '3' => '3',
          '2' => '2',
          '1' => '1'
        ),
        'std' => '4',
        'save_always' => true
      ),
      
      array(
  		  "type" => "dropdown",
  		  "heading" => esc_html__("Grid Item Spacing", "js_composer"),
  		  "param_name" => "grid_item_spacing",
  		  'save_always' => true,
  		  "value" => array(
  		  		"None" => "none",
  			    "5px" => "5px",
  			    "10px" => "10px",
  			    "15px" => "15px",
            "25px" => "25px"
  			),
  		  "description" => esc_html__("Please select the spacing you would like between your items. ", "js_composer")
  		),
      
      array(
         "type" => 'checkbox',
         "heading" => esc_html__("Masonry Layout", "js_composer"),
         "param_name" => "enable_masonry",
         "description" => esc_html__("This will allow your portfolio items to display in a masonry layout as opposed to a fixed grid", "js_composer"),
         "value" => Array(esc_html__("Yes, please", "js_composer") => 'yes')
       ),
       
 
       array(
         "type" => "colorpicker",
         "class" => "",
         "group" => 'Grid Item Coloring/Style',
         "heading" => "Color Overlay",
         "param_name" => "color_overlay",
         "value" => "",
         "description" => "Use this to set a BG color that will be overlaid on your grid items"
     ),
     
     array(
       "type" => "dropdown",
       "class" => "",
       "group" => 'Grid Item Coloring/Style',
       'save_always' => true,
       "heading" => "Color Overlay Opacity",
       "param_name" => "color_overlay_opacity",
       "value" => array(
         "0" => "0",
         "0.1" => "0.1",
         "0.2" => "0.2",
         "0.3" => "0.3",
         "0.4" => "0.4",
         "0.5" => "0.5",
         "0.6" => "0.6",
         "0.7" => "0.7",
         "0.8" => "0.8",
         "0.9" => "0.9",
         "1" => "1"
       ),
       'std' => '0.3',
     ),
       
       array(
         "type" => "dropdown",
         "class" => "",
         "group" => 'Grid Item Coloring/Style',
         'save_always' => true,
         "heading" => "Color Overlay Hover Opacity",
         "param_name" => "color_overlay_hover_opacity",
         "value" => array(
           "0" => "0",
           "0.1" => "0.1",
           "0.2" => "0.2",
           "0.3" => "0.3",
           "0.4" => "0.4",
           "0.5" => "0.5",
           "0.6" => "0.6",
           "0.7" => "0.7",
           "0.8" => "0.8",
           "0.9" => "0.9",
           "1" => "1"
         ),
         'std' => '0.4',
       ),
       
       
       array(
         "type" => "dropdown",
         "class" => "",
         "group" => 'Grid Item Coloring/Style',
         'save_always' => true,
         "heading" => "Text Color",
         "param_name" => "text_color",
         "value" => array(
           "Dark" => "dark",
           "Light" => "light",
         ),
         'std' => 'light',
       ),
       
       array(
         "type" => "dropdown",
         "class" => "",
         "group" => 'Grid Item Coloring/Style',
         'save_always' => true,
         "heading" => "Text Color Hover",
         "param_name" => "text_color_hover",
         "value" => array(
           "Dark" => "dark",
           "Light" => "light",
         ),
          'std' => 'light',
       ),
       
       array(
          "type" => 'checkbox',
          "heading" => esc_html__("Shadow on Hover", "js_composer"),
          "param_name" => "shadow_on_hover",
          "group" => 'Grid Item Coloring/Style',
          "description" => esc_html__("This will add a shadow effect on hover to your grid items", "js_composer"),
          "value" => Array(esc_html__("Yes, please", "js_composer") => 'yes')
        ),
        
        array(
          'type' => 'dropdown',
          'heading' => __( 'Subtext Visibility', 'js_composer' ),
          'param_name' => 'subtext_visibility',
          "group" => 'Grid Item Coloring/Style',
          'value' => array(
            'Always Shown' => 'always',
            'Shown on Hover' => 'on_hover',
          ),
          'save_always' => true
        ),
        
        /*
        array(
          'type' => 'dropdown',
          'heading' => __( 'Text Content Style', 'js_composer' ),
          'param_name' => 'text_style',
          "group" => 'Grid Item Coloring/Style',
          'value' => array(
            'Default' => 'default',
            'Color Strip' => 'color_strip',
          ),
          "dependency" => array('element' => "subtext_visibility", 'value' => 'always'),
          'save_always' => true
        ),
        
        array(
          "type" => "colorpicker",
          "class" => "",
          "group" => 'Grid Item Coloring/Style',
          "heading" => "Color Strip BG",
          "param_name" => "color_strip_bg",
          "value" => "#ffffff",
          "dependency" => array('element' => "text_style", 'value' => 'color_strip'),
          "description" => ""
      ),
      
      array(
        "type" => "colorpicker",
        "class" => "",
        "group" => 'Grid Item Coloring/Style',
        "heading" => "Color Strip BG Hover",
        "param_name" => "color_strip_bg_hover",
        "value" => "#3452ff",
        "dependency" => array('element' => "text_style", 'value' => 'color_strip'),
        "description" => ""
    ),

    
    array(
      "type" => "dropdown",
      "class" => "",
      'save_always' => true,
      "group" => 'Grid Item Coloring/Style',
      "heading" => "Color Strip Text",
      "param_name" => "color_strip_text",
      "value" => array(
        "Dark" => "dark",
        "Light" => "light",
      ),
      "dependency" => array('element' => "text_style", 'value' => 'color_strip'),
      "description" => ""
  ),

    
    array(
      "type" => "dropdown",
      "class" => "",
      'save_always' => true,
      "group" => 'Grid Item Coloring/Style',
      "heading" => "Color Strip Text Hover",
      "param_name" => "color_strip_text_hover",
      "value" => array(
        "Light" => "light",
        "Dark" => "dark",
      ),
      "dependency" => array('element' => "text_style", 'value' => 'color_strip'),
      "description" => ""
  ), */
      

      
    ),
  );
  
  ?>