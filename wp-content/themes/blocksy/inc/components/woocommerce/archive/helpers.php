<?php

if (! function_exists('blocksy_get_woocommerce_ratio')) {
	function blocksy_get_woocommerce_ratio() {
		$cropping = get_theme_mod(
			'blocksy_woocommerce_thumbnail_cropping',
			'predefined'
		);

		if ($cropping === 'uncropped') {
			return 'original';
		}

		if ($cropping === '1:1') {
			return '1/1';
		}

		if ($cropping === 'custom' || $cropping === 'predefined') {
			$width = get_option('woocommerce_thumbnail_cropping_custom_width', 4);
			$height = get_option('woocommerce_thumbnail_cropping_custom_height', 3);

			return $width . '/' . $height;
		}

		return '1/1';
	}
}

