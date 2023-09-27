<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

?>
<li>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

		<?php

			echo blocksy_image([
				'no_image_type' => 'woo',
				'attachment_id' => $product->get_image_id(),
				'post_id' => $product->get_id(),
				'size' => 'woocommerce_gallery_thumbnail',
				'ratio' => '1/1',
				'tag_name' => 'a',
				'html_atts' => [
					'href' => esc_url( $product->get_permalink() )
				],
			]);

		?>

		<div class="product-data">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-title">
				<?php echo wp_kses_post( $product->get_name() ); ?>
			</a>

			<?php if ( ! empty( $show_rating ) ) : ?>
				<?php echo wc_get_rating_html( $product->get_average_rating() ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>

			<span class="price">
				<?php echo $product->get_price_html(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
