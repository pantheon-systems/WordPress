<?php

add_filter(
	'woocommerce_sale_flash',
	function ($text, $post, $product) {
		$text = get_theme_mod(
			'sale_badge_default_value',
			__('SALE!', 'blocksy')
		);

		$default_text = $text;

		if (get_theme_mod('sale_badge_value', 'default') === 'custom') {
			$text = get_theme_mod('sale_badge_custom_value', '-[value]%');

			if ($product->is_type('variable')) {
				$percentages = [];

				$prices = $product->get_variation_prices();

				foreach($prices['price'] as $key => $price) {
					if ($prices['regular_price'][$key] !== $price) {
						$percentages[] = round(
							100 - (
								$prices['sale_price'][$key] / $prices['regular_price'][$key] * 100
							)
						);
					}
				}

				if (empty($percentages)) {
					$percentages[] = 0;
				}

				$percentage = max($percentages);
			} else {
				$regular_price = (float) $product->get_regular_price();
				$sale_price = (float) $product->get_sale_price();

				$percentage = 0;

				if ($regular_price > 0) {
					$percentage = round(100 - ($sale_price / $regular_price * 100));
				}
			}

			$text = str_replace(
				'[value]',
				$percentage,
				$text
			);

			if ($product->is_type('grouped')) {
				$text = $default_text;
			}
		}

		return blocksy_html_tag(
			'span',
			[
				'class' => 'onsale',
				'data-shape' => get_theme_mod('sale_badge_shape', 'type-2')
			],
			$text
		);
	},
	10,
	3
);

