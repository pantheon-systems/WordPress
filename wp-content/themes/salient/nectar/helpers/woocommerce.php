<?php
/**
 * WooCommerce helpers
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$main_shop_layout      = ( ! empty( $nectar_options['main_shop_layout'] ) ) ? $nectar_options['main_shop_layout'] : 'no-sidebar';
$single_product_layout = ( ! empty( $nectar_options['single_product_layout'] ) ) ? $nectar_options['single_product_layout'] : 'no-sidebar';

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// needed to let WooCommerce know Salient has theme options for columns
if ( function_exists( 'is_customize_preview' ) ) {
	if ( $woocommerce && is_customize_preview() ) {
		add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns' );
	}
}

function nectar_shop_wrapper_start() {
	echo '<div class="container-wrap" data-midnight="dark"><div class="container main-content"><div class="row"><div class="nectar-shop-header">';
	do_action( 'nectar_shop_header_markup' );
	echo '</div>';
}

function nectar_shop_wrapper_end() {
	echo '</div></div></div>';
	do_action( 'nectar_shop_fixed_social' );
}


function nectar_shop_wrapper_start_sidebar_left() {

	echo '<div class="container-wrap" data-midnight="dark"><div class="container main-content"><div class="nectar-shop-header">';
	do_action( 'nectar_shop_header_markup' );
	echo '</div><div class="row"><div id="sidebar" class="col span_3 col">';
	if ( function_exists( 'dynamic_sidebar' ) ) {
		dynamic_sidebar( 'woocommerce-sidebar' );
	}
	echo '</div><div class="post-area col span_9 col_last">';
}

function nectar_shop_wrapper_end_sidebar_left() {
	echo '</div></div></div></div>';
		do_action( 'nectar_shop_fixed_social' );
}


function nectar_shop_wrapper_start_sidebar_right() {
	echo '<div class="container-wrap" data-midnight="dark"><div class="container main-content"><div class="nectar-shop-header">';
	do_action( 'nectar_shop_header_markup' );
	echo '</div><div class="row"><div class="post-area col span_9">';
}

function nectar_shop_wrapper_end_sidebar_right() {
		echo '</div><div id="sidebar" class="col span_3 col_last">';
	if ( function_exists( 'dynamic_sidebar' ) ) {
		dynamic_sidebar( 'woocommerce-sidebar' );
	}
	echo '</div></div></div></div>';
		do_action( 'nectar_shop_fixed_social' );
}

function nectar_shop_wrapper_start_fullwidth() {

	echo '<div class="container-wrap" data-midnight="dark"><div class="container main-content"><div class="row"><div class="full-width-content nectar-shop-outer"><div class="nectar-shop-header">';
	do_action( 'nectar_shop_header_markup' );
	echo '</div>';
}

function nectar_shop_wrapper_end_fullwidth() {
	echo '</div></div></div></div>';
}

if ( ! function_exists( 'nectar_shop_loop_columns' ) ) {
	function nectar_shop_loop_columns() {
		return 3; // 3 products per row
	}
}

if ( ! function_exists( 'nectar_shop_loop_columns_std' ) ) {
	function nectar_shop_loop_columns_std() {
		return 4; // 3 products per row
	}
}

// change header
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_filter( 'woocommerce_show_page_title', '__return_false' );
add_filter( 'woocommerce_breadcrumb_defaults', 'nectar_change_breadcrumb_delimiter' );
function nectar_change_breadcrumb_delimiter( $defaults ) {
	$defaults['delimiter'] = ' <i class="fa fa-angle-right"></i> ';
	return $defaults;
}


if ( $woocommerce ) {
	add_action( 'wp', 'nectar_woo_shop_markup' );

	// alter gallery thumbnail width
	add_action( 'after_setup_theme', 'nectar_custom_gallery_thumb_woocommerce_theme_support' );
	function nectar_custom_gallery_thumb_woocommerce_theme_support() {
		add_theme_support(
			'woocommerce',
			array(
				'gallery_thumbnail_image_width' => 150,
			)
		);
	}
}

function nectar_woo_shop_markup() {

	global $single_product_layout;
	global $main_shop_layout;
	global $woocommerce;

	if ( $woocommerce && ! is_product() ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	}

	// page header
	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {

		add_action( 'woocommerce_before_main_content', 'salient_shop_header', 10 );

		if ( ! function_exists( 'salient_shop_header' ) ) {
			function salient_shop_header() {
				global $woocommerce;
				// page header for main shop page
				if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
					nectar_page_header( wc_get_page_id( 'shop' ) );
				} else {
					nectar_page_header( woocommerce_get_page_id( 'shop' ) );
				}
			}
		}

		if ( ! function_exists( 'salient_woo_shop_title' ) ) {
			function salient_woo_shop_title() {
				echo '<h1 class="page-title">';
				woocommerce_page_title();
				echo '</h1>';
			}
		}

		if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
			$header_title    = get_post_meta( wc_get_page_id( 'shop' ), '_nectar_header_title', true );
			$header_bg_color = get_post_meta( wc_get_page_id( 'shop' ), '_nectar_header_bg_color', true );
			$header_bg_image = get_post_meta( wc_get_page_id( 'shop' ), '_nectar_header_bg', true );
		} else {
			$header_title    = get_post_meta( woocommerce_get_page_id( 'shop' ), '_nectar_header_title', true );
			$header_bg_color = get_post_meta( woocommerce_get_page_id( 'shop' ), '_nectar_header_bg_color', true );
			$header_bg_image = get_post_meta( woocommerce_get_page_id( 'shop' ), '_nectar_header_bg', true );
		}

		if ( is_shop() ) {
			if ( empty( $header_bg_color ) && empty( $header_bg_image ) ) {
				add_action( 'nectar_shop_header_markup', 'salient_woo_shop_title', 10 );
			}
		} elseif ( is_product_category() ) {

			$cate          = get_queried_object();
			$t_id          = ( property_exists( $cate, 'term_id' ) ) ? $cate->term_id : '';
			$product_terms = get_option( "taxonomy_$t_id" );

			$using_cat_bg = ( ! empty( $product_terms['product_category_image'] ) ) ? true : false;

			if ( empty( $header_bg_color ) && empty( $header_bg_image ) && ! $using_cat_bg ) {
				add_action( 'nectar_shop_header_markup', 'salient_woo_shop_title', 10 );
			}
		} elseif ( is_product_tag() || is_product_taxonomy() ) {

			if ( empty( $header_bg_color ) && empty( $header_bg_image ) ) {
				add_action( 'nectar_shop_header_markup', 'salient_woo_shop_title', 10 );
			}
		}


		add_action( 'nectar_shop_header_markup', 'woocommerce_catalog_ordering', 10 );
		add_action( 'nectar_shop_header_markup', 'woocommerce_result_count', 10 );
		add_action( 'nectar_shop_header_markup', 'woocommerce_breadcrumb', 10 );

	}

	// no sidebar shop single
	if ( is_product() && $single_product_layout != 'right-sidebar' && is_product() && $single_product_layout != 'left-sidebar' ) {
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
		add_action( 'woocommerce_before_main_content', 'nectar_shop_wrapper_start', 10 );
		add_action( 'woocommerce_after_main_content', 'nectar_shop_wrapper_end', 10 );

		add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns_std' );
	}

	// no sidebar shop
	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
		if ( $main_shop_layout != 'right-sidebar' && $main_shop_layout != 'left-sidebar' && $main_shop_layout != 'fullwidth' ) {
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
			add_action( 'woocommerce_before_main_content', 'nectar_shop_wrapper_start', 10 );
			add_action( 'woocommerce_after_main_content', 'nectar_shop_wrapper_end', 10 );

			add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns_std' );
		}

		if ( $main_shop_layout == 'fullwidth' ) {
			add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns_std' );
		}
	}

	// using sidebar
	if ( is_shop() || is_product_category() || is_product_tag() || is_product() || is_product_taxonomy() ) {

		$nectar_shop_layout = ( is_product() ) ? $single_product_layout : $main_shop_layout;

		if ( $nectar_shop_layout == 'right-sidebar' ) {

			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			add_action( 'woocommerce_before_main_content', 'nectar_shop_wrapper_start_sidebar_right', 10 );
			add_action( 'woocommerce_after_main_content', 'nectar_shop_wrapper_end_sidebar_right', 10 );

			add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns' );

		} elseif ( $nectar_shop_layout == 'left-sidebar' ) {

			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			add_action( 'woocommerce_before_main_content', 'nectar_shop_wrapper_start_sidebar_left', 10 );
			add_action( 'woocommerce_after_main_content', 'nectar_shop_wrapper_end_sidebar_left', 10 );

			add_filter( 'loop_shop_columns', 'nectar_shop_loop_columns' );
		} elseif ( $nectar_shop_layout == 'fullwidth' ) {

			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			add_action( 'woocommerce_before_main_content', 'nectar_shop_wrapper_start_fullwidth', 10 );
			add_action( 'woocommerce_after_main_content', 'nectar_shop_wrapper_end_fullwidth', 10 );

		}
	}

}





add_theme_support( 'woocommerce' );

/* custom gallery thumb size */
if ( $woocommerce ) {
	add_filter( 'woocommerce_gallery_thumbnail_size', 'nectar_woocommerce_gallery_thumbnail_size' );
}

function nectar_woocommerce_gallery_thumbnail_size() {
	return 'nectar_small_square';
}


// remove parentheses in counts
function nectar_remove_categories_count( $variable ) {
	$variable = str_replace( '(', '<span class="post_count"> ', $variable );
	$variable = str_replace( ')', ' </span>', $variable );
	return $variable;
}

if ( $woocommerce ) {
	add_filter( 'wp_list_categories', 'nectar_remove_categories_count' );
	add_filter( 'woocommerce_layered_nav_count', 'nectar_remove_categories_count' );
}

add_filter( 'woocommerce_pagination_args', 'nectar_override_pagination_args' );
function nectar_override_pagination_args( $args ) {
	$args['prev_text'] = __( 'Previous', 'salient' );
	$args['next_text'] = __( 'Next', 'salient' );
	return $args;
}


if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'nectar_add_to_cart_fragment' );
} else {
	add_filter( 'add_to_cart_fragments', 'nectar_add_to_cart_fragment' );
}


// update the cart with ajax
function nectar_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	ob_start();
	$fragments['a.cart-parent'] = ob_get_clean();
	return $fragments;
}


// change summary html markup to fit responsive
if ( empty( $nectar_options['product_tab_position'] ) || $nectar_options['product_tab_position'] == 'in_sidebar' ) {
	add_action( 'woocommerce_before_single_product_summary', 'nectar_woocommerce_summary_div', 35 );
	add_action( 'woocommerce_after_single_product_summary', 'nectar_woocommerce_close_div', 4 );
}

function nectar_woocommerce_summary_div() {
	echo "<div class='span_7 col col_last single-product-summary'>";
}
function nectar_woocommerce_close_div() {
	echo '</div>';
}

// change tab position to be inside summary
if ( empty( $nectar_options['product_tab_position'] ) || $nectar_options['product_tab_position'] == 'in_sidebar' ) {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1 );
}

// wrap single product image in an extra div
add_action( 'woocommerce_before_single_product_summary', 'nectar_woocommerce_images_div', 8 );
add_action( 'woocommerce_before_single_product_summary', 'nectar_woocommerce_close_div', 29 );
function nectar_woocommerce_images_div() {
	echo "<div class='span_5 col single-product-main-image'>";
}


// display upsells and related products within dedicated div with different column and number of products
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 10 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

function woocommerce_output_related_products() {
	$output = null;

	ob_start();
	woocommerce_related_products(
		array(
			'columns'        => 4,
			'posts_per_page' => 4,
		)
	);
	$content = ob_get_clean();
	if ( $content ) {
		$output .= $content; }

	echo '<div class="clear"></div>' . $output;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 10 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_upsells', 21 );

function woocommerce_output_upsells() {

	$output = null;

	ob_start();
	woocommerce_upsell_display( 4, 4 );
	$content = ob_get_clean();
	if ( $content ) {
		$output .= $content; }

	echo $output;
}


if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'nectar_woocommerce_header_add_to_cart_fragment' );
	if ( $nectar_theme_skin == 'material' ) {
		add_filter( 'woocommerce_add_to_cart_fragments', 'nectar_mobile_woocommerce_header_add_to_cart_fragment' );
	}
} else {
	add_filter( 'add_to_cart_fragments', 'nectar_woocommerce_header_add_to_cart_fragment' );
}


function nectar_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start(); ?>
	<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>"><div class="cart-icon-wrap"><i class="icon-salient-cart"></i> <div class="cart-wrap"><span><?php echo esc_html( $woocommerce->cart->cart_contents_count ); ?> </span></div> </div></a>
	<?php

	$fragments['a.cart-contents'] = ob_get_clean();

	return $fragments;
}

function nectar_mobile_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	ob_start();
	?>
	<a id="mobile-cart-link" href="<?php echo wc_get_cart_url(); ?>"><i class="icon-salient-cart"></i><div class="cart-wrap"><span><?php echo esc_html( $woocommerce->cart->cart_contents_count ); ?> </span></div></a>
	<?php

	$fragments['a#mobile-cart-link'] = ob_get_clean();

	return $fragments;
}


// change how many products are displayed per page
global $nectar_options;

$product_hover_alt_image      = ( ! empty( $nectar_options['product_hover_alt_image'] ) ) ? $nectar_options['product_hover_alt_image'] : 'off';
$nectar_woo_products_per_page = ( ! empty( $nectar_options['woo-products-per-page'] ) ) ? $nectar_options['woo-products-per-page'] : '12';

add_filter(
	'loop_shop_per_page',
	function( $cols ) {
		global $nectar_woo_products_per_page;
		return $nectar_woo_products_per_page;
	},
	20
);

// change the position of add to cart
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

$product_style = ( ! empty( $nectar_options['product_style'] ) ) ? $nectar_options['product_style'] : 'classic';
if ( $product_style == 'classic' ) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'product_thumbnail_with_cart', 10 );
} elseif ( $product_style == 'text_on_hover' ) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'product_thumbnail_with_cart_alt', 10 );

	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 5 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 10 );
} elseif ( $product_style == 'material' ) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'product_thumbnail_material', 10 );
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
} else {
	add_action( 'woocommerce_before_shop_loop_item_title', 'product_thumbnail_minimal', 10 );

	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	add_action( 'nectar_woo_minimal_price', 'woocommerce_template_loop_price', 5 );

	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
}


/*add 3.0 gallery support when using default lightbox option in theme*/
$nectar_product_gal_type = ( ! empty( $nectar_options['single_product_gallery_type'] ) ) ? $nectar_options['single_product_gallery_type'] : 'default';

if ( $nectar_product_gal_type != 'ios_slider' ) {
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} else {
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}

if ( ! function_exists( 'product_thumbnail_with_cart' ) ) {

	function product_thumbnail_with_cart() {
		global $product;
		global $woocommerce;
		global $product_hover_alt_image;
		global $nectar_quick_view_in_use;
		?>
		
	   <div class="product-wrap">

			<a href="<?php the_permalink(); ?>">	
							<?php

							$product_second_image = null;
							if ( $product_hover_alt_image == '1' ) {

								if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
									$product_attach_ids = $product->get_gallery_image_ids();
								} else {
									$product_attach_ids = $product->get_gallery_attachment_ids();
								}

								if ( isset( $product_attach_ids[0] ) ) {
									$product_second_image = wp_get_attachment_image( $product_attach_ids[0], 'shop_catalog', false, array( 'class' => 'hover-gallery-image' ) );
								}
							}

							echo woocommerce_get_product_thumbnail() . $product_second_image;
							?>
			 </a>
			<?php
					echo '<div class="product-add-to-cart" data-nectar-quickview="' . $nectar_quick_view_in_use . '">';
						woocommerce_template_loop_add_to_cart();
						do_action( 'nectar_woocommerce_before_add_to_cart' );
					 echo '</div>';
			?>
		   </div>
		<?php
	}
}



if ( ! function_exists( 'product_thumbnail_material' ) ) {

	function product_thumbnail_material() {

			global $product;
			global $woocommerce;
			global $product_hover_alt_image;
		 	global $nectar_quick_view_in_use;
		?>
		
	   <div class="product-wrap">
			<?php

			$product_second_image = null;
			if ( $product_hover_alt_image == '1' ) {

				if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
					$product_attach_ids = $product->get_gallery_image_ids();
				} else {
					$product_attach_ids = $product->get_gallery_attachment_ids();
				}

				if ( isset( $product_attach_ids[0] ) ) {
					$product_second_image = wp_get_attachment_image( $product_attach_ids[0], 'shop_catalog', false, array( 'class' => 'hover-gallery-image' ) );
				}
			}

			echo '<a href="' . esc_url( get_permalink() ) . '">';
			echo woocommerce_get_product_thumbnail() . $product_second_image;
			echo '</a>';
			echo '<div class="product-meta">';
			echo '<a href="' . esc_url( get_permalink() ) . '">';
			do_action( 'woocommerce_shop_loop_item_title' );
			echo '</a>';
			do_action( 'woocommerce_after_shop_loop_item_title' );

			echo '<div class="product-add-to-cart" data-nectar-quickview="' . $nectar_quick_view_in_use . '">';
			  woocommerce_template_loop_add_to_cart();
					do_action( 'nectar_woocommerce_before_add_to_cart' );
			echo '</div></div>';
			?>
		   </div>
		<?php
	}
}



if ( ! function_exists( 'product_thumbnail_minimal' ) ) {

	function product_thumbnail_minimal() {

		global $product;
		global $woocommerce;
		global $product_hover_alt_image;
		 global $nectar_quick_view_in_use;
		?>
		 <div class="background-color-expand"></div>
	   <div class="product-wrap">
			<?php

			$product_second_image = null;
			if ( $product_hover_alt_image == '1' ) {

				if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
					$product_attach_ids = $product->get_gallery_image_ids();
				} else {
					$product_attach_ids = $product->get_gallery_attachment_ids();
				}

				if ( isset( $product_attach_ids[0] ) ) {
					$product_second_image = wp_get_attachment_image( $product_attach_ids[0], 'shop_catalog', false, array( 'class' => 'hover-gallery-image' ) );
				}
			}

			echo '<a href="' . esc_url( get_permalink() ) . '">';
			echo woocommerce_get_product_thumbnail() . $product_second_image;
			echo '</a>';
			echo '<div class="product-meta">';
			echo '<a href="' . esc_url( get_permalink() ) . '">';
			do_action( 'woocommerce_shop_loop_item_title' );
			echo '</a>';
			do_action( 'woocommerce_after_shop_loop_item_title' );
			echo '<div class="price-hover-wrap">';
			do_action( 'nectar_woo_minimal_price' );
			echo '<div class="product-add-to-cart" data-nectar-quickview="' . $nectar_quick_view_in_use . '">';
			  woocommerce_template_loop_add_to_cart();
					do_action( 'nectar_woocommerce_before_add_to_cart' );
			echo '</div></div></div>';
			?>
		   </div>
		<?php
	}
}



if ( ! function_exists( 'product_thumbnail_with_cart_alt' ) ) {

	function product_thumbnail_with_cart_alt() {
		?>
		
	   <div class="product-wrap">
			<?php
			global $product;
			global $woocommerce;
			global $product_hover_alt_image;
				global $nectar_quick_view_in_use;

			$product_second_image = null;
			if ( $product_hover_alt_image == '1' ) {

				if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
					$product_attach_ids = $product->get_gallery_image_ids();
				} else {
					$product_attach_ids = $product->get_gallery_attachment_ids();
				}

				if ( isset( $product_attach_ids[0] ) ) {
					$product_second_image = wp_get_attachment_image( $product_attach_ids[0], 'shop_catalog', false, array( 'class' => 'hover-gallery-image' ) );
				}
			}

			echo woocommerce_get_product_thumbnail() . $product_second_image;
			?>

			   <div class="bg-overlay"></div>
			   <a href="<?php the_permalink(); ?>" class="link-overlay"></a>
			   <div class="text-on-hover-wrap">
				<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
				<?php

				if ( $woocommerce && version_compare( $woocommerce->version, '3.0', '>=' ) ) {
					echo '<div class="categories">' . wc_get_product_category_list( $product->get_id() ) . '</div>';
				} else {
					echo '<div class="categories">' . $product->get_categories() . '</div>';
				}

				?>
			</div> 
			
			<?php do_action( 'nectar_woocommerce_before_add_to_cart' ); ?>


		   </div>
		   <a href="<?php the_permalink(); ?>"><?php do_action( 'woocommerce_shop_loop_item_title' ); ?></a>
		<?php
		woocommerce_template_loop_add_to_cart();
	}
}



function nectar_header_cart_output() {
	global $woocommerce;
	global $nectar_options;

	$header_format         = ( ! empty( $nectar_options['header_format'] ) ) ? $nectar_options['header_format'] : 'default';
	$userSetSideWidgetArea = ( ! empty( $nectar_options['header-slide-out-widget-area'] ) && $header_format != 'left-header' ) ? $nectar_options['header-slide-out-widget-area'] : 'off';

	ob_start();

	if ( $woocommerce ) {

			$nav_cart_style = ( ! empty( $nectar_options['ajax-cart-style'] ) ) ? $nectar_options['ajax-cart-style'] : 'default';
		?>
			
		<div class="cart-outer" data-user-set-ocm="<?php echo esc_attr( $userSetSideWidgetArea ); ?>" data-cart-style="<?php echo esc_attr( $nav_cart_style ); ?>">
			<div class="cart-menu-wrap">
				<div class="cart-menu">
					<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>"><div class="cart-icon-wrap"><i class="icon-salient-cart"></i> <div class="cart-wrap"><span><?php echo esc_html( $woocommerce->cart->cart_contents_count ); ?> </span></div> </div></a>
				</div>
			</div>
			
			<div class="cart-notification">
				<span class="item-name"></span> <?php echo esc_html__( 'was successfully added to your cart.', 'salient' ); ?>
			</div>
			
			<?php
			if ( $nav_cart_style != 'slide_in' ) {
				// Check for WooCommerce 2.0 and display the cart widget
				if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0' ) >= 0 ) {
					the_widget( 'WC_Widget_Cart' );
				} else {
					the_widget( 'WooCommerce_Widget_Cart', 'title= ' );
				}
			}
			?>
				
		</div>
			
		<?php
	}

	$captured_cart_content = ob_get_clean();
	return $captured_cart_content;

}

add_action( 'wp', 'nectar_woo_social_add' );

function nectar_woo_social_add() {

	global $nectar_options;
	global $woocommerce;
	$nectar_woo_social_style = ( ! empty( $nectar_options['woo_social_style'] ) ) ? $nectar_options['woo_social_style'] : 'default';

	if ( empty( $nectar_options['product_tab_position'] ) || $nectar_options['product_tab_position'] == 'in_sidebar' ) {

		if ( $woocommerce && $nectar_woo_social_style == 'fixed_bottom_right' && is_product() ) {
			add_action( 'nectar_shop_fixed_social', 'nectar_review_quickview', 10 );
		} else {
			add_action( 'woocommerce_after_single_product_summary', 'nectar_review_quickview', 7 );
		}
	} else {

		if ( $woocommerce && $nectar_woo_social_style == 'fixed_bottom_right' && is_product() ) {
			add_action( 'nectar_shop_fixed_social', 'nectar_review_quickview', 10 );
		} else {
			add_action( 'woocommerce_single_product_summary', 'nectar_review_quickview', 100 );
		}

		add_action( 'woocommerce_after_single_product_summary', 'nectar_woo_clearfix', 7 );
	}

} //nectar_woo_social_add


function nectar_woo_clearfix() {
	echo '<div class="after-product-summary-clear"></div>';
}

function nectar_review_quickview() {
	global $product, $nectar_options, $post;

	$nectar_woo_social_style = ( ! empty( $nectar_options['woo_social_style'] ) ) ? $nectar_options['woo_social_style'] : 'default';

	?>
		
		<div id="single-meta" data-fixed-sharing="<?php echo esc_attr( $nectar_woo_social_style ); ?>" data-sharing="<?php echo ( ! empty( $nectar_options['woo_social'] ) && $nectar_options['woo_social'] == 1 ) ? '1' : '0'; ?>">
			<?php

				// portfolio social sharting icons
			if ( ! empty( $nectar_options['woo_social'] ) && $nectar_options['woo_social'] == 1 ) {

				if ( $nectar_woo_social_style != 'fixed_bottom_right' ) {
					echo '<ul class="product-sharing"><li class="meta-share-count"><a href="#"><i class="icon-default-style steadysets-icon-share"></i><span class="share-count-total">0</span> <span class="plural">' . esc_html__( 'Shares', 'salient' ) . '</span> <span class="singular">' . esc_html__( 'Share', 'salient' ) . '</span></a> <div class="nectar-social"><div class="nectar-social woo">';
				} else {
					echo '<div class="nectar-social-sharing-fixed"><a href="#"><i class="icon-default-style steadysets-icon-share"></i></a> <div class="nectar-social woo">';
				}

				// facebook
				if ( ! empty( $nectar_options['woo-facebook-sharing'] ) && $nectar_options['woo-facebook-sharing'] == 1 ) {
					echo "<a class='facebook-share nectar-sharing' href='#' title='Share this'> <i class='fa fa-facebook'></i> <span class='count'></span></a>";
				}
				// twitter
				if ( ! empty( $nectar_options['woo-twitter-sharing'] ) && $nectar_options['woo-twitter-sharing'] == 1 ) {
					echo "<a class='twitter-share nectar-sharing' href='#' title='Tweet this'> <i class='fa fa-twitter'></i> <span class='count'></span></a>";
				}

				// google plus
				if ( ! empty( $nectar_options['woo-google-plus-sharing'] ) && $nectar_options['woo-google-plus-sharing'] == 1 ) {
					echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> <span class='count'>0</span></a>";
				}

				// linkedIn
				if ( ! empty( $nectar_options['woo-linkedin-sharing'] ) && $nectar_options['woo-linkedin-sharing'] == 1 ) {
					echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> <span class='count'> </span></a>";
				}

				// pinterest
				if ( ! empty( $nectar_options['woo-pinterest-sharing'] ) && $nectar_options['woo-pinterest-sharing'] == 1 ) {
					echo "<a class='pinterest-share nectar-sharing' href='#' title='Pin this'> <i class='fa fa-pinterest'></i> <span class='count'></span></a>";
				}

				if ( $nectar_woo_social_style != 'fixed_bottom_right' ) {
					  echo '</div></div></li></ul>';
				} else {
						echo '</div></div>';
				}
			}

			?>
				 
			
		</div> 
	<?php

}

// Image sizes
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
	add_action( 'init', 'nectar_woocommerce_image_dimensions', 1 ); }


// Define image sizes
function nectar_woocommerce_image_dimensions() {
	$catalog = array(
		'width'  => '375',
		'height' => '400',
		'crop'   => 1,
	);

	$single = array(
		'width'  => '600',
		'height' => '630',
		'crop'   => 1,
	);

	$thumbnail = array(
		'width'  => '130',
		'height' => '130',
		'crop'   => 1,
	);

	update_option( 'shop_catalog_image_size', $catalog ); // Product category thumbs
	update_option( 'shop_single_image_size', $single ); // Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); // Image gallery thumbs
}
