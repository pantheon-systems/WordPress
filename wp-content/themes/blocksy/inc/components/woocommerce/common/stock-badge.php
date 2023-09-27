<?php

if ( ! function_exists( 'blocksy_get_woo_out_of_stock_badge' ) ) {
	function blocksy_get_woo_out_of_stock_badge($args = []) {
		$args = wp_parse_args(
			$args,
			[
				// archive | single
				'location' => 'archive'
			]
		);

		$has_stock_badge = get_theme_mod('has_stock_badge', [
			'archive' => true,
			'single' => true,
		]);

		if (
			$args['location'] === 'archive' && ! $has_stock_badge['archive']
			||
			$args['location'] === 'single' && ! $has_stock_badge['single']
		) {
			return;
		}

		return blocksy_html_tag(
			'span',
			[
				'class' => 'out-of-stock-badge',
				'data-shape' => get_theme_mod('sale_badge_shape', 'type-2')
			],
			get_theme_mod(
				'stock_badge_value',
				__('OUT OF STOCK', 'blocksy')
			)
		);
	}
}
