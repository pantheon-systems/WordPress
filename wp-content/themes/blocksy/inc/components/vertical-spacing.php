<?php

function blocksy_get_v_spacing($args = []) {
	$args = wp_parse_args(
		$args,
		[
			'array' => false
		]
	);

	$v_spacing_output = [
		'data-vertical-spacing' => 'top:bottom'
	];

	$prefix = blocksy_manager()->screen->get_prefix();

	if (is_singular() || blocksy_is_page()) {
		$post_options = blocksy_get_post_options();

		$page_vertical_spacing_source = blocksy_default_akg(
			'vertical_spacing_source',
			$post_options,
			'inherit'
		);

		$post_content_area_spacing = get_theme_mod(
			$prefix . '_content_area_spacing',
			'both'
		);

		if ($page_vertical_spacing_source === 'custom') {
			$post_content_area_spacing = blocksy_default_akg(
				'content_area_spacing',
				$post_options,
				'both'
			);
		}

		$v_spacing_components = [];

		if (
			$post_content_area_spacing === 'top'
			||
			$post_content_area_spacing === 'both'
		) {
			$v_spacing_components[] = 'top';
		}

		if (
			$post_content_area_spacing === 'bottom'
			||
			$post_content_area_spacing === 'both'
		) {
			$v_spacing_components[] = 'bottom';
		}

		$v_spacing_output = [];

		if (! empty($v_spacing_components)) {
			$v_spacing_output['data-vertical-spacing'] = implode(':', $v_spacing_components);
		}
	}

	if ($args['array']) {
		return $v_spacing_output;
	}

	if (empty($v_spacing_output)) {
		return '';
	}

	return 'data-vertical-spacing="' . $v_spacing_output['data-vertical-spacing'] . '"';
}
