<?php 


global $woocommerce_loop;

extract( shortcode_atts( array(
	'product_type' => 'all',
	'per_page' 	=> '12',
	'columns' 	=> '4',
	'carousel' 	=> 'false',
	'category' 	=> 'all',
	'controls_on_hover' => 'false',
	'orderby' => 'date',   
	'order' => 'DESC',
	'pagination' => '',
	'script' => 'carouFredSel',
	'flickity_controls' => 'bottom-pagination',
	'flickity_heading_tag' => 'h2',
	'flickity_heading_text' => '',
	'flickity_link_text' => '',
	'flickity_link_url' => '',
	'item_shadow' => '',
	'autorotate' => '',
	'autorotation_speed' => '5000'
), $atts ) );

//incase only all was selected
if($category == 'all') {
	$category = null;
}


if($product_type == 'all') {

	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'orderby'             => $orderby,
		'order'               => $order,
		'posts_per_page' 		  => $per_page,
		'product_cat'         => $category,
		'meta_query'          => WC()->query->get_meta_query(),
		'tax_query'           => WC()->query->get_tax_query(),
	);

} else if($product_type == 'sale') {


	$args = array(
		'posts_per_page'	=> $per_page,
		'no_found_rows'  => 1,
		'orderby'        => $orderby,
		'order'          => $order,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'product_cat'    => $category,
		'meta_query'     => WC()->query->get_meta_query(),
		'tax_query'      => WC()->query->get_tax_query(),
		'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
	);


} else if($product_type == 'featured') {


	$meta_query  = WC()->query->get_meta_query();
	$tax_query   = WC()->query->get_tax_query();
	$tax_query[] = array(
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => 'featured',
		'operator' => 'IN',
	);

	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'orderby'             => $orderby,
		'order'               => $order,
		'ignore_sticky_posts' => 1,
		'product_cat'         => $category,
		'posts_per_page' 	    => $per_page,
		'meta_query'          => $meta_query,
		'tax_query'           => $tax_query,
	);


} else if($product_type == 'best_selling') {


	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'orderby'             => $orderby,
		'order'               => $order,
		'product_cat'         => $category,
		'posts_per_page'		  => $per_page,
		'meta_key'            => 'total_sales',
		'orderby'             => 'meta_value_num',
		'meta_query'          => WC()->query->get_meta_query(),
		'tax_query'           => WC()->query->get_tax_query(),
	);


}




if($pagination == '1') {
	
	$shortcode_attrs = '';
	
	if(!empty($product_type) && $product_type == 'best_selling') {
		$shortcode_attrs .= 'best_selling ';
	}
	elseif(!empty($product_type) && $product_type == 'sale') {
		$shortcode_attrs .= 'on_sale ';
	}
	elseif(!empty($product_type) && $product_type == 'featured') {
		$shortcode_attrs .= 'visibility="featured" ';
	}
	
	if(!empty($per_page)) {
		$shortcode_attrs .= 'limit="'.$per_page.'" ';
	}
	
	if(!empty($columns)) {
		$shortcode_attrs .= 'columns="'.$columns.'" ';
	}
	
	if(!empty($category)) {
		$shortcode_attrs .= 'category="'.$category.'" ';
	}
	
	if(!empty($orderby)) {
		$shortcode_attrs .= 'orderby="'.$orderby.'" ';
	}
	
	if(!empty($order)) {
		$shortcode_attrs .= 'order="'.$order.'" ';
	}
	
	//use regular shortcode to handle query
	echo do_shortcode('[products paginate="true" '.$shortcode_attrs.']');
	
} else {
	
	if( isset($_GET['vc_editable']) ) {
		$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
		$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
		//imit script choices on front end editor
		if($nectar_using_VC_front_end_editor && $script != 'flickity') {
			$script = 'flickity';
		}
	}

	ob_start();

	$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

	if($carousel != '1' && $columns == 'dynamic' || $carousel == '1' && $script != 'flickity' && $columns == 'dynamic') {
		$columns = 4;
	}

	$woocommerce_loop['columns'] = $columns;

	if ( $products->have_posts() ) : ?>
		
		<?php if($carousel == '1' && $script == 'carouFredSel') { ?> 
			<div class="carousel-wrap products-carousel" data-controls="<?php echo esc_attr( $controls_on_hover ); ?>"> 
		<?php	} ?>
		
		<?php if($carousel == '1' && $script == 'flickity') { ?>
			<div class="nectar-woo-flickity" data-autorotate="<?php echo esc_attr( $autorotate ); ?>" data-autorotate-speed="<?php echo esc_attr( $autorotation_speed ); ?>" data-item-shadow="<?php echo esc_attr( $item_shadow ); ?>" data-controls="<?php echo esc_attr( $flickity_controls ); ?>"> 
				
		<?php 
				if($flickity_controls == 'arrows-and-text') {
					echo '<div class="nectar-woo-carousel-top"> <'.$flickity_heading_tag.'>'.$flickity_heading_text.'</'.$flickity_heading_tag.'>';
					
					if(strlen($flickity_link_text) > 0) {
						echo '<a href="'.$flickity_link_url.'">'.$flickity_link_text.'</a>';
					}
					
					echo '</div>';
				}
		} ?>

		<?php wc_get_template( 'loop/loop-start.php' ); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php  wc_get_template( 'loop/loop-end.php' ); ?>

		<?php if($carousel == '1') { ?> </div> <?php } ?>

	<?php endif;

	wp_reset_postdata();

	echo '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
		
} //no pag

?>