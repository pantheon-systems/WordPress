<?php

add_action('wp', function () {
	remove_action('wp_footer', 'woocommerce_demo_store');
	add_action('wp_body_open', 'woocommerce_demo_store');
});

add_filter(
	'woocommerce_demo_store',
	function ($notice) {
		$parser = new Blocksy_Attributes_Parser();

		$notice = $parser->add_attribute_to_images_with_tag(
			$notice,
			'data-position',
			get_theme_mod('store_notice_position', 'bottom'),
			'p'
		);

		return $notice;
	}
);
