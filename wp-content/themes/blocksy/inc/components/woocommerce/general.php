<?php

add_action(
	'blocksy:content:top',
	function () {
		if (
			! is_shop()
			&&
			! is_woocommerce()
			&&
			! is_cart()
			&&
			! is_checkout()
			&&
			! is_account_page()
		) {
			global $blocksy_messages_content;
			ob_start();
			echo '<div class="blocksy-woo-messages-default woocommerce-notices-wrapper">';
			echo do_shortcode('[woocommerce_messages]');
			echo '</div>';
			$blocksy_messages_content = ob_get_clean();
		}
	}
);

add_action(
	'blocksy:single:top',
	function () {
		global $blocksy_messages_content;

		if (! empty($blocksy_messages_content)) {
			echo $blocksy_messages_content;
		}
	}
);

add_filter(
	'woocommerce_format_sale_price',
	function ($price, $regular_price, $sale_price) {
		return '<span class="sale-price">' . $price . '</span>';
	},
	10,
	3
);

add_filter('woocommerce_quantity_input_args', function ($args, $product) {
	global $blocksy_quantity_args;
	$blocksy_quantity_args = $args;

	$blocksy_quantity_args['min_value'] = max($blocksy_quantity_args['min_value'], 0);
	$blocksy_quantity_args['max_value'] = 0 < $blocksy_quantity_args['max_value'] ? $blocksy_quantity_args['max_value'] : '';

	if ('' !== $blocksy_quantity_args['max_value'] && $blocksy_quantity_args['max_value'] < $blocksy_quantity_args['min_value']) {
		$blocksy_quantity_args['max_value'] = $blocksy_quantity_args['min_value'];
	}

	return $args;
}, 10, 2);

add_action(
	'woocommerce_before_quantity_input_field',
	function () {
		if (get_theme_mod('has_custom_quantity', 'yes') !== 'yes') {
			return;
		}

		echo '<span class="ct-increase"></span>';
		echo '<span class="ct-decrease"></span>';
	}
);

add_action(
	'woocommerce_before_main_content',
	function () {
		$prefix = blocksy_manager()->screen->get_prefix();

		if ($prefix === 'woo_categories' || $prefix === 'search') {
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_output_hero_section() used here escapes the value properly.
			 */
			echo blocksy_output_hero_section([
				'type' => 'type-2'
			]);
		}

		$attr = [
			'class' => 'ct-container'
		];

		if (blocksy_get_page_structure() === 'narrow') {
			$attr['class'] = 'ct-container-narrow';
		}

		if ($prefix === 'product') {
			if (blocksy_sidebar_position() === 'none') {
				$attr['class'] = 'ct-container-full';

				$attr['data-content'] = 'normal';

				if (blocksy_get_page_structure() === 'narrow') {
					$attr['data-content'] = 'narrow';
				}
			}

			echo blocksy_output_hero_section([
				'type' => 'type-2'
			]);
		}

		echo '<div ' . blocksy_attr_to_html($attr) . ' ' . wp_kses(blocksy_sidebar_position_attr(), []) . ' ' . blocksy_get_v_spacing() . '>';

		if (is_product()) {
			echo '<article class="post-' . get_the_ID() . '">';
		} else {
			echo '<section>';
		}

		if (
			$prefix === 'woo_categories'
			||
			$prefix === 'search'
			||
			$prefix === 'product'
		) {
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_output_hero_section() used here escapes the value properly.
			 */
			echo blocksy_output_hero_section([
				'type' => 'type-1'
			]);
		}
	}
);

add_action(
	'woocommerce_after_main_content',
	function () {
		if (is_product()) {
			echo '</article>';
		} else {
			echo '</section>';
		}

		get_sidebar();
		echo '</div>';
	}
);

add_action(
	'woocommerce_before_template_part',
	function ($template_name, $template_path, $located, $args) {
		global $blocksy_is_offcanvas_cart;

		if ($template_name === 'global/quantity-input.php') {
			ob_start();
		}

		if ($template_name === 'single-product/up-sells.php') {
			ob_start();
		}

		if ($template_name === 'single-product/related.php') {
			ob_start();
		}
	},
	10,
	4
);

add_action(
	'woocommerce_after_template_part',
	function ($template_name, $template_path, $located, $args) {
		global $blocksy_is_offcanvas_cart;

		if ($template_name === 'global/quantity-input.php') {
			$quantity = ob_get_clean();

			$final_quantity_look = 'class="quantity"';

			global $blocksy_quantity_args;

			$args = $blocksy_quantity_args;

			if ($args['max_value'] && $args['min_value'] === $args['max_value']) {
				$final_quantity_look = 'class="quantity hidden"';
			} else {
				if (get_theme_mod('has_custom_quantity', 'yes') === 'yes') {
					$final_quantity_look .= ' data-type="' . get_theme_mod('quantity_type', 'type-2') . '"';
				}
			}

			echo str_replace(
				'class="quantity"',
				$final_quantity_look,
				$quantity
			);
		}

		if ($template_name === 'single-product/up-sells.php') {
			$upsells = ob_get_clean();

			echo str_replace(
				'class="up-sells upsells products"',
				'class="up-sells upsells products ' . trim(
					blocksy_visibility_classes(
						get_theme_mod(
							'upsell_products_visibility',
							[
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							]
						)
					)
				) . '"',
				$upsells
			);
		}

		if ($template_name === 'single-product/related.php') {
			$related = ob_get_clean();

			echo str_replace(
				'class="related products"',
				'class="related products ' . trim(
					blocksy_visibility_classes(
						get_theme_mod(
							'related_products_visibility',
							[
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							]
						)
					)
				) . '"',
				$related
			);
		}
	},
	4,
	4
);

if (! function_exists('blocksy_product_get_gallery_images')) {
	function blocksy_product_get_gallery_images($product) {
		$root_product = $product;

		if ($product->post_type === 'product_variation') {
			$root_product = wc_get_product( $product->get_parent_id() );
		}

		$thumb_id = apply_filters(
			'woocommerce_product_get_image_id',
			get_post_thumbnail_id($root_product->get_id()),
			$root_product
		);

		$thumb_id = get_post_thumbnail_id($root_product->get_id());

		$gallery_images = $root_product->get_gallery_image_ids();

		if ($thumb_id) {
			array_unshift($gallery_images, intval($thumb_id));
		} else {
			$gallery_images = [null];
		}

		if ($product->post_type === 'product_variation') {
			$variation_main_image = $product->get_image_id();

			$variation_values = get_post_meta(
				$product->get_id(),
				'blocksy_post_meta_options'
			);

			if (empty($variation_values)) {
				$variation_values = [[]];
			}

			$variation_values = $variation_values[0];

			$variation_gallery_images = blocksy_akg('images', $variation_values, []);
			$gallery_source = blocksy_akg('gallery_source', $variation_values, 'default');

			if ($gallery_source === 'default') {
				if (! in_array($variation_main_image, $gallery_images)) {
					$gallery_images[0] = $variation_main_image;
				}
			} else {
				$gallery_images = [$variation_main_image];

				foreach ($variation_gallery_images as $variation_gallery_image) {
					$gallery_images[] = $variation_gallery_image['attachment_id'];
				}
			}
		}

		return $gallery_images;
	}
}

add_action('rest_api_init', function () {
	if (! function_exists('is_shop')) {
		return;
	}

	if (
		isset($_GET['post_type'])
		&&
		(
			str_contains($_GET['post_type'], 'product')
			||
			$_GET['post_type'] === 'ct_forced_any'
		)
		&&
		isset($_GET['product_price'])
		&&
		$_GET['product_price'] === 'true'
	) {
		register_rest_field('post', 'product_price', array(
			'get_callback' => function ($post, $field_name, $request) {
				if ($post['type'] !== 'product') {
					return 0;
				}

				$product = wc_get_product($post['id']);
				$price = $product->get_regular_price();
				$sale = $product->get_sale_price();

				if ($product->is_taxable()) {
					if (defined('WC_ABSPATH')) {
						// WC 3.6+ - Cart and other frontend functions are not included for REST requests.
						include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
						include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
						include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
					}

					if (null === WC()->session) {
						$session_class = apply_filters(
							'woocommerce_session_handler',
							'WC_Session_Handler'
						);

						WC()->session = new $session_class();
						WC()->session->init();
					}

					if (null === WC()->customer) {
						WC()->customer = new WC_Customer(
							get_current_user_id(),
							true
						);
					}

					$tax_display_mode = get_option('woocommerce_tax_display_shop');

					if ($tax_display_mode === 'incl') {
						$price = wc_get_price_including_tax($product);
						$sale = wc_get_price_including_tax($product, ['price' => $sale]);
					} else {
						$price = wc_get_price_excluding_tax($product);
						$sale = wc_get_price_excluding_tax($product, ['price' => $sale]);
					}
				}

				if ( $sale ) {
	
					$sale_html = $sale? blocksy_html_tag(
						'ins',
						[
							'aria-hidden' => 'true'
						],
						wc_price($sale)
					) : '';

					$price_html = blocksy_html_tag(
						'del',
						[],
						wc_price($price)
					);

					return $price ? blocksy_html_tag(
						'span',
						[
							'class' => 'sale-price'
						],
						$price_html . $sale_html
					) : 0;
				}

				return $price ? wc_price($price) : 0;
			},
			'update_callback' => null,
			'schema' => [
				'description' => __('Product Price', 'blocksy'),
				'type' => 'string'
			],
		));
	}
});
