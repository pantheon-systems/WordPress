<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $woocommerce;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}


// Extra post classes
$classes = array();

if(!version_compare( $woocommerce->version, '2.6', ">=" )) {

	global $woocommerce_loop;

	// Store loop count we're currently on
	if ( empty( $woocommerce_loop['loop'] ) ) {
		$woocommerce_loop['loop'] = 0;
	}

	// Store column count for displaying the grid
	if ( empty( $woocommerce_loop['columns'] ) ) {
		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
	}

	// Increase loop count
	$woocommerce_loop['loop']++;

	// Extra post classes
	if ( 0 === ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 === $woocommerce_loop['columns'] ) {
		$classes[] = 'first';
	}
	if ( 0 === $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
		$classes[] = 'last';
	}

} 

$options = get_nectar_theme_options(); 
$product_style = (!empty($options['product_style'])) ? $options['product_style'] : 'classic';
$classes[] = $product_style;

?>

<?php if( function_exists('wc_product_class') ) { ?>
	<li <?php wc_product_class( $classes ); ?> >
<?php } else { ?>
	<li <?php post_class( $classes ); ?> >
<?php } ?>


	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>


		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
		
	  

		<?php if($product_style == 'classic') { 
			do_action( 'woocommerce_shop_loop_item_title' );
 			do_action( 'woocommerce_after_shop_loop_item_title' ); 
		} ?>

	

	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

</li>