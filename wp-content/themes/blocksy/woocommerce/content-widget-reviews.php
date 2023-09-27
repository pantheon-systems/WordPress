<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-reviews.php
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

?>
<li>
	<?php do_action( 'woocommerce_widget_product_review_item_start', $args ); ?>

	<?php

		echo blocksy_image([
			'attachment_id' => $product->get_image_id(),
			'size' => 'woocommerce_gallery_thumbnail',
			'ratio' => '1/1',
			'tag_name' => 'a',
			'post_id' => $product->get_id(),
			'html_atts' => [
				'href' => esc_url( get_comment_link( $comment->comment_ID ) )
			],
			'no_image_type' => 'woo'
		]);

	?>

	<div class="product-data">
		<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="product-title">
			<?php echo $product->get_name(); ?>
		</a>

		<?php echo wc_get_rating_html( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) );?>

		<span class="reviewer">
			<?php echo sprintf( esc_html__( 'by %s', 'blocksy' ), get_comment_author( $comment->comment_ID ) ); ?>
		</span>
	</div>

	<?php do_action( 'woocommerce_widget_product_review_item_end', $args ); ?>
</li>

