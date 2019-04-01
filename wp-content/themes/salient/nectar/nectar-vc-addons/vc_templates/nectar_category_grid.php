<?php 
extract( shortcode_atts( array(
	'post_type' => 'posts',
	'product_category' 	=> '',
  'blog_category' 	=> '',
  'text_content_alignment' => 'top_left',
  'subtext' => 'none',
  'orderby' => '',
	'order' 	=> '',
  'grid_item_spacing' => '10px',
  'columns' => '4',
  'enable_masonry' => '',
  'color_overlay' => '',
  'color_overlay_opacity' => '',
  'color_overlay_hover_opacity' => '',
  'text_color' => 'dark',
  'text_color_hover' => 'dark',
  'custom_subtext' => '',
  'subtext_visibility' => 'always',
  'shadow_on_hover' => '',
  'text_style' => 'default'

), $atts ) );


echo '<div class="nectar-category-grid" data-columns="'. $columns .'" data-text-style="'.$text_style.'" data-grid-spacing="'.$grid_item_spacing.'" data-alignment="'.$text_content_alignment.'" data-text-color="'.$text_color.'" data-text-hover-color="'.$text_color_hover.'" data-shadow-hover="'.$shadow_on_hover.'" data-masonry="'. $enable_masonry .'">';


//posts
if($post_type == 'posts') {
  
    //all categories
    if($blog_category == 'all') {

      $categories = get_categories();
      
      foreach( $categories as $temp_cat_obj_holder ){
            
            echo nectar_grid_item_markup($temp_cat_obj_holder, $atts);
            
       } //loop
    
    } //end post category all conditional
    
    else {
      
      if(empty($blog_category)) return;
    
      $category_slug_list = explode(",", $blog_category);
      
      foreach($category_slug_list as $k => $slug) {
        $temp_cat_obj_holder = get_term_by( 'slug', $slug, 'category' );
  
        echo nectar_grid_item_markup($temp_cat_obj_holder, $atts);
        
      } //loop
      
    }


} //end blog post type 

else if($post_type == 'products') {
  
  //all categories
  if($product_category == 'all') {
    $grid_query = array(
      'taxonomy'   =>  'product_cat'
    );
    
    $categories = get_categories($grid_query);
    
    
    foreach( $categories as $temp_cat_obj_holder ){
          
          echo nectar_grid_item_markup($temp_cat_obj_holder, $atts);
          
     } //loop

  } //end product category all conditional
  
  else { 

    if(empty($product_category)) return;
  
    $category_slug_list = explode(",", $product_category);

    foreach($category_slug_list as $k => $slug) {
      $temp_cat_obj_holder = get_term_by( 'slug', $slug, 'product_cat' );

      echo nectar_grid_item_markup($temp_cat_obj_holder, $atts);
      
    } //loop
  
  }// end selected category conditional
  
}// end product post type


echo '</div>';

?>