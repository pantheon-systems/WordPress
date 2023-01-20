<?php

if (! function_exists('blocksy_validate_color')) {
	function blocksy_validate_color($color) {
		/**
		 * Allow hex colors
		 */
		if (sanitize_hex_color($color)) {
			return $color;
		}

		/**
		 * Allow rgb* colors
		 */
		if (strpos($color, 'rgb') !== false) {
			return $color;
		}

		/**
		 * Allow var(--global) values
		 */
		if (strlen($color) > 2 && substr( $color, 0, 5 ) === "var(--") {
			return $color;
		}

		if ($color === 'CT_CSS_SKIP_RULE') {
			return $color;
		}

		return null;
	}
}

if (! function_exists('blocksy_validate_single_slider')) {
	function blocksy_validate_single_slider($option, $value) {
		if (! intval($value) && intval($value) !== 0) {
			return null;
		}

		return true;
	}
}

if (! function_exists('blocksy_validate_for')) {
	function blocksy_validate_for($option, $input) {
		if (
			$option['type'] === 'ct-switch'
			||
			$option['type'] === 'ct-panel'
		) {
			if (in_array($input, ['yes', 'no'], true)) {
				return $input;
			}

			return $option['value'];
		}

		if ($option['type'] === 'text' || $option['type'] === 'textarea') {
			return wp_kses_post($input);
		}

		if ($option['type'] === 'ct-color-picker') {
			if (! is_array($input)) {
				return $option['value'];
			}

			foreach ($input as $single_color) {
				if (! isset($single_color['color'])) {
					return $option['value'];
				}

				if (! blocksy_validate_color($single_color['color'])) {
					return $option['value'];
				}
			}
		}

		if (
			$option['type'] === 'ct-select'
			||
			$option['type'] === 'ct-image-picker'
			||
			$option['type'] === 'ct-radio'
		) {
			if (! in_array(
				$input,
				array_reduce(
					blocksy_ordered_keys($option['choices']),
					function ($current, $item) {
						return array_merge($current, [$item['key']]);
					},
					[]
				),
				true
			)) {
				return $option['value'];
						}
		}

		if (
			$option['type'] === 'ct-checkboxes'
			||
			$option['type'] === 'ct-visibility'
		) {
			foreach ($input as $key => $value) {
				if (
					! is_bool($value)
					||
					! in_array(
						$key,
						array_reduce(
							blocksy_ordered_keys($option['choices']),
							function ($current, $item) {
								return array_merge($current, [$item['key']]);
							},
							[]
						),
						true
					)
								) {
									return $option['value'];
								}
			}
		}

		if ($option['type'] === 'ct-number') {
			if (! is_numeric($input)) {
				return $option['value'];
			}

			return max(
				intval($option['min']),
				min(intval($option['max']), intval($current))
			);
		}

		if ($option['type'] === 'ct-slider') {
			if ($option['responsive']) {
				foreach (
					array_values(
						blocksy_expand_responsive_value($input)
					) as $single_value
				) {
					if (! blocksy_validate_single_slider($single_value)) {
						return $option['value'];
					}
				}
			}

			if (
				is_array($input)
				||
				! blocksy_validate_single_slider($input)
			) {
				return $option['value'];
			}
		}

		if ($option['type'] === 'ct-image-uploader') {
			if (
				!is_array($input)
				||
				! isset($input['attachment_id'])
				||
				!wp_attachment_is_image($input['attachment_id'])
			) {
				return $option['value'];
			}
		}

		return $input;
	}
}

if (! function_exists('blocksy_include_sanitizer')) {
	function blocksy_include_sanitizer($option) {
		if (isset($option['sanitize_callback'])) {
			return $option;
		}

		$option['sanitize_callback'] = function ($input, $setting) use ($option) {
			return blocksy_validate_for($option, $input);
		};

		return $option;
	}
}
