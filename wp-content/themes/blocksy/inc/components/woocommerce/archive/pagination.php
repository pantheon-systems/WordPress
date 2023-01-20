<?php

add_action(
	'woocommerce_before_template_part',
	function ($template_name, $template_path, $located, $args) {
		if ($template_name !== 'loop/pagination.php') {
			return;
		}

		$base = isset($args['base']) ? $args['base'] : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));

		echo blocksy_display_posts_pagination([
			'prefix' => 'woo_categories',
			'total_pages' => isset($args['total']) ? $args['total'] : wc_get_loop_prop('total_pages'),
			'current_page' => isset($args['current']) ? $args['current'] : wc_get_loop_prop('current_page'),
			'format' => isset($args['format']) ? $args['format'] : '',
			'base' => $base
		]);

		ob_start();
	},
	1,
	4
);

add_action(
	'woocommerce_after_template_part',
	function ($template_name, $template_path, $located, $args) {
		if ($template_name !== 'loop/pagination.php') {
			return;
		}

		ob_get_clean();
	},
	1,
	4
);
