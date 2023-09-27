<?php

if (! function_exists('blocksy_compute_box_shadow_var_for')) {
	function blocksy_compute_box_shadow_var_for($value, $should_skip_output = true) {
		if (
			! isset($value['enable'])
			||
			! $value['enable']
			||
			(isset($value['inherit']) && $value['inherit'])
		) {
			return 'none';
		}

		if (
			$value['blur'] === 0
			&&
			$value['spread'] === 0
			&&
			$value['v_offset'] === 0
			&&
			$value['h_offset'] === 0
		) {
			return $should_skip_output ? 'CT_CSS_SKIP_RULE' : 'none';
		}

		$color = blocksy_get_colors([
			'default' => $value['color']
		], [
			'default' => $value['color']
		]);

		$box_shadow_components = [];

		if (isset($value['inset']) && $value['inset']) {
			$box_shadow_components[] = 'inset';
		}

		$box_shadow_components[] = $value['h_offset'] . 'px';
		$box_shadow_components[] = $value['v_offset'] . 'px';

		if (intval($value['blur']) !== 0) {
			$box_shadow_components[] = $value['blur'] . 'px';

			if (intval($value['spread']) !== 0) {
				$box_shadow_components[] = $value['spread'] . 'px';
			}
		}

		if (
			intval($value['blur']) === 0
			&&
			intval($value['spread']) !== 0
		) {
			$box_shadow_components[] = $value['blur'] . 'px';
			$box_shadow_components[] = $value['spread'] . 'px';
		}

		$box_shadow_components[] = $color['default'];

		return implode(' ', $box_shadow_components);
	}
}

if (! function_exists('blocksy_box_shadow_value')) {
	function blocksy_box_shadow_value($args = []) {
		return wp_parse_args(
			$args,
			[
				'inherit' => false,
				'blur' => 0,
				'spread' => 0,
				'v_offset' => 0,
				'h_offset' => 0,
				'inset' => false,
				'enable' => true,
				'color' => [
					'color' => 'rgba(44,62,80,0.2)',
				],
			]
		);
	}
}

if (! function_exists('blocksy_output_box_shadow')) {
	function blocksy_output_box_shadow($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'css' => null,
				'tablet_css' => null,
				'mobile_css' => null,

				'selector' => null,

				'desktop_selector_prefix' => '',
				'tablet_selector_prefix' => '',
				'mobile_selector_prefix' => '',

				'should_skip_output' => true,

				'variableName' => 'box-shadow',
				'value' => null,

				'important' => false,
				'responsive' => false
			]
		);

		$value = blocksy_expand_responsive_value($args['value']);
		$responsive = blocksy_expand_responsive_value($args['responsive'] || true);

		$shadow_value = [
			'desktop' => ($responsive['desktop'] ? blocksy_compute_box_shadow_var_for(
				$value['desktop'],
				$args['should_skip_output']
			) : 'none'),

			'tablet' => ($responsive['tablet'] ? blocksy_compute_box_shadow_var_for(
				$value['tablet'],
				$args['should_skip_output']
			) : 'none'),

			'mobile' => ($responsive['mobile'] ? blocksy_compute_box_shadow_var_for(
				$value['mobile'],
				$args['should_skip_output']
			) : 'none')
		];

		if (
			$shadow_value['desktop'] === 'none'
			&&
			$shadow_value['tablet'] === 'none'
			&&
			$shadow_value['mobile'] === 'none'
		) {
			$shadow_value['desktop'] = $args['should_skip_output'] ? 'CT_CSS_SKIP_RULE' : 'none';
			$shadow_value['tablet'] = $args['should_skip_output'] ? 'CT_CSS_SKIP_RULE' : 'none';
			$shadow_value['mobile'] = $args['should_skip_output'] ? 'CT_CSS_SKIP_RULE' : 'none';
		}

		$args['value'] = $shadow_value;

		if ($args['important']) {
			$args['value_suffix'] = ' !important';
		}

		blocksy_output_css_vars($args);
	}
}
