<?php

function blocksy_output_product_toolbar() {
	$components = [];

	if (function_exists('blocksy_output_add_to_wish_list')) {
		$maybe_wish_list = blocksy_output_add_to_wish_list('archive');

		if (! empty($maybe_wish_list)) {
			$components[] = $maybe_wish_list;
		}
	}

	if (function_exists('blocksy_output_quick_view_link')) {
		$maybe_quick_view = blocksy_output_quick_view_link();

		if (! empty($maybe_quick_view)) {
			$components[] = $maybe_quick_view;
		}
	}

	if (! empty($components)) {
		return '<div class="ct-woo-card-extra">' . implode(' ', $components) . '</div>';
	}

	return '';
}

function blocksy_get_product_card_categories() {
	if (
		get_theme_mod('has_product_categories', 'no') === 'yes'
	) {
		return blocksy_post_meta([
			[
				'id' => 'categories',
				'enabled' => true
			]
		], [
			'attr' => get_theme_mod(
				'has_product_categories', 'no'
			) !== 'yes' ? ['data-customize-hide' => ''] : []
		]);
	}

	return '';
}

$action_to_hook = 'wp';

if (wp_doing_ajax()) {
	$action_to_hook = 'init';
}

add_action($action_to_hook, function () {
	if (! get_option('woocommerce_thumbnail_cropping', null)) {
		update_option('woocommerce_thumbnail_cropping', 'custom');
		update_option('woocommerce_thumbnail_cropping_custom_width', 3);
		update_option('woocommerce_thumbnail_cropping_custom_height', 4);
	}

	$products_layout = blocksy_get_products_listing_layout();

	if (
		get_theme_mod('has_star_rating', 'yes') !== 'yes'
		&&
		!is_product()
	) {
		add_filter(
			'woocommerce_product_get_rating_html',
			function ($html) {
				return str_replace(
					'class="star-rating"',
					'class="star-rating" data-customize-hide',
					$html
				);
			}
		);
	}

	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

	if ($products_layout !== 'type-1') {
		// Products cards
		remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);

		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

		remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

		remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

		// Category cards
		remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail');

		blocksy_manager()->hooks->redirect_callbacks([
			'token' => 'product_card_type_2',
			'source' => [
				'woocommerce_before_shop_loop_item_title',
				'woocommerce_shop_loop_item_title'
			],
			'destination' => 'blocksy:woocommerce:product-card:title:before'
		]);

		blocksy_manager()->hooks->redirect_callbacks([
			'token' => 'product_card_type_2',
			'source' => [
				'woocommerce_after_shop_loop_item_title',
			],
			'destination' => 'blocksy:woocommerce:product-card:title:after'
		]);
	}

	// Cards type 1
	if ($products_layout === 'type-1') {
		// Products cards
		remove_action(
			'woocommerce_before_shop_loop_item_title',
			'woocommerce_template_loop_product_thumbnail'
		);

		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

		if (get_theme_mod('has_product_action_button', 'yes') === 'no') {
			remove_action(
				'woocommerce_after_shop_loop_item',
				'woocommerce_template_loop_add_to_cart',
				10
			);
		}

		if (get_theme_mod('has_star_rating', 'yes') === 'yes') {
			add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 20);
		}

		add_action(
			'woocommerce_before_shop_loop_item_title',
			function () {
				global $product;

				if ($product->is_in_stock()) {
					if (get_theme_mod('has_sale_badge', 'yes') === 'yes') {
						woocommerce_show_product_loop_sale_flash();
					}
				} else {
					echo blocksy_get_woo_out_of_stock_badge([
						'location' => 'archive'
					]);
				}

				$gallery_images = blocksy_product_get_gallery_images(
					$product
				);

				$hover_value = get_theme_mod('product_image_hover', 'none');

				$has_archive_video_thumbnail = get_theme_mod(
					'has_archive_video_thumbnail',
					'no'
				);

				$has_lazy_load_shop_card_image = get_theme_mod('has_lazy_load_shop_card_image', 'yes');

				$image = blocksy_image([
					'no_image_type' => 'woo',
					'attachment_id' => $product->get_image_id(),
					'post_id' => $product->get_id(),
					'other_images' => count($gallery_images) > 1 && $hover_value === 'swap' ? [
						$gallery_images[1]
					] : [],
					'size' => 'woocommerce_thumbnail',
					'ratio' => blocksy_get_woocommerce_ratio(),
					'tag_name' => 'span',
					'display_video' => $has_archive_video_thumbnail === 'yes',
					'lazyload' => $has_lazy_load_shop_card_image === 'yes',
				]);

				echo apply_filters(
					'woocommerce_product_get_image',
					$image,
					$product,
					'woocommerce_thumbnail',
					[],
					'',
					$image
				);

				do_action('blocksy:woocommerce:product-card:title:before');
			}
		);

		add_action('woocommerce_shop_loop_item_title', function () {
			do_action('blocksy:woocommerce:product-card:title:after');
		}, 20);

		add_action('woocommerce_after_shop_loop_item_title', function () {
			do_action('blocksy:woocommerce:product-card:price:before');
		}, 1);

		add_action('woocommerce_after_shop_loop_item_title', function () {
			do_action('blocksy:woocommerce:product-card:price:after');
		}, 50);

		add_action(
			'woocommerce_after_shop_loop_item',
			function () {
				echo blocksy_get_product_card_categories();

				$has_excerpt = get_theme_mod('has_excerpt', 'no') === 'yes';
				if ( $has_excerpt ) {
					$excerpt_length = get_theme_mod('excerpt_length', '40');
					echo blocksy_entry_excerpt( $excerpt_length, 'entry-excerpt', null );
				}

				do_action('blocksy:woocommerce:product-card:actions:before');

				echo '<div class="ct-woo-card-actions">';
			},
			6
		);

		add_action(
			'woocommerce_after_shop_loop_item',
			function () {
				echo '</div>';
				do_action('blocksy:woocommerce:product-card:actions:after');
				echo blocksy_output_product_toolbar();
			},
			20
		);

		// Categories cards
		remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail');

		add_action(
			'woocommerce_before_subcategory_title',
			function ($category) {
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

				echo blocksy_image([
					'attachment_id' => $thumbnail_id,
					'size' => 'woocommerce_thumbnail',
					'ratio' => blocksy_get_woocommerce_ratio(),
					'tag_name' => 'span',
					'no_image_type' => 'woo'
				]);
			}
		);
	}

	// Cards type 2
	if ($products_layout === 'type-2') {
		add_action(
			'woocommerce_before_shop_loop_item',
			function () {
				global $product;

				echo '<figure>';

				do_action('blocksy:woocommerce:product-card:thumbnail:start');

				if ($product->is_in_stock()) {
					if (get_theme_mod('has_sale_badge', 'yes') === 'yes') {
						woocommerce_show_product_loop_sale_flash();
					}
				} else {
					echo blocksy_get_woo_out_of_stock_badge([
						'location' => 'archive'
					]);
				}

				echo blocksy_output_product_toolbar();

				$gallery_images = blocksy_product_get_gallery_images(
					$product
				);

				$hover_value = get_theme_mod('product_image_hover', 'none');

				$has_archive_video_thumbnail = get_theme_mod(
					'has_archive_video_thumbnail',
					'no'
				);

				$has_lazy_load_shop_card_image = get_theme_mod('has_lazy_load_shop_card_image', 'yes');

				$image = blocksy_image([
					'no_image_type' => 'woo',
					'attachment_id' => $product->get_image_id(),
					'post_id' => $product->get_id(),
					'other_images' => count($gallery_images) > 1 && $hover_value === 'swap' ? [
						$gallery_images[1]
					] : [],
					'size' => 'woocommerce_thumbnail',
					'ratio' => blocksy_get_woocommerce_ratio(),
					'tag_name' => 'a',
					'html_atts' => [
						'href' => apply_filters(
							'woocommerce_loop_product_link',
							get_permalink($product->get_id()),
							$product
						),
						'aria-label' => $product->get_name(),
					],
					'display_video' => $has_archive_video_thumbnail === 'yes',
					'lazyload' => $has_lazy_load_shop_card_image === 'yes',

				]);

				echo apply_filters(
					'woocommerce_product_get_image',
					$image,
					$product,
					'woocommerce_thumbnail',
					[],
					'',
					$image
				);

				do_action('blocksy:woocommerce:product-card:thumbnail:end');

				echo '</figure>';

				do_action('blocksy:woocommerce:product-card:title:before');

				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_title();
				woocommerce_template_loop_product_link_close();

				do_action('blocksy:woocommerce:product-card:title:after');

				echo blocksy_get_product_card_categories();

				if (
					get_theme_mod('has_star_rating', 'yes') === 'yes'
				) {
					woocommerce_template_loop_rating();
				}

				$has_excerpt = get_theme_mod('has_excerpt', 'no') === 'yes';
				if ( $has_excerpt ) {
					$excerpt_length = get_theme_mod('excerpt_length', '40');
					echo blocksy_entry_excerpt( $excerpt_length, 'entry-excerpt', null );
				}

				do_action('blocksy:woocommerce:product-card:actions:before');

				echo '<div class="ct-woo-card-actions">';

				woocommerce_template_loop_price();

				if (get_theme_mod('has_product_action_button', 'yes') === 'yes') {
					woocommerce_template_loop_add_to_cart();
				}

				echo '</div>';

				do_action('blocksy:woocommerce:product-card:actions:after');
			}
		);

		add_action(
			'woocommerce_before_subcategory',
			function ($category) {
				$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);

				echo '<figure>';

				echo blocksy_image([
					'no_image_type' => 'woo',
					'attachment_id' => $thumbnail_id,
					'size' => 'woocommerce_thumbnail',
					'ratio' => blocksy_get_woocommerce_ratio(),
					'tag_name' => 'a',
					'html_atts' => [
						'href' => get_term_link($category, 'product_cat')
					]
				]);

				echo '</figure>';
			},
			5
		);
	}
}, 15000);

