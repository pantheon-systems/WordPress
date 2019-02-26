<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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


	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }

$options = get_nectar_theme_options(); 
$product_style = (!empty($options['product_style'])) ? $options['product_style'] : 'classic';
$product_gallery_style = (!empty($options['single_product_gallery_type'])) ? $options['single_product_gallery_type'] : 'default';
$product_hide_sku = (!empty($options['woo_hide_product_sku'])) ? $options['woo_hide_product_sku'] : 'false';

?>

<?php if( function_exists('wc_product_class') ) { ?>
	<div itemscope data-project-style="<?php echo esc_attr($product_style); ?>" data-hide-product-sku="<?php echo esc_attr($product_hide_sku); ?>" data-gallery-style="<?php echo esc_attr($product_gallery_style); ?>" data-tab-pos="<?php echo (!empty($options['product_tab_position'])) ? esc_attr($options['product_tab_position']) : 'default'; ?>" id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
<?php } else { ?>
	<div itemscope data-project-style="<?php echo esc_attr($product_style); ?>" data-hide-product-sku="<?php echo esc_attr($product_hide_sku); ?>" data-gallery-style="<?php echo esc_attr($product_gallery_style); ?>" data-tab-pos="<?php echo (!empty($options['product_tab_position'])) ? esc_attr($options['product_tab_position']) : 'default'; ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php } ?>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * woocommerce_single_product_summary hook.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

