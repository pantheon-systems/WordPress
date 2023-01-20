<?php

class Blocksy_Fonts_Manager {
	private $matching_fonts_collection = [];

	public function get_all_fonts() {
		return apply_filters('blocksy_typography_font_sources', [
			'system' => [
				'type' => 'system',
				'families' => $this->get_system_fonts(),
			],

			'google' => [
				'type' => 'google',
				'families' => $this->get_googgle_fonts()
				// 'families' => []
			]
		]);
	}

	public function process_matching_typography($value) {
		$family_to_push = 'Default';
		$variation_to_push = 'Default';

		if ($value && isset($value['family'])) {
			$family_to_push = $value['family'];
		}

		if ($value && isset($value['variation'])) {
			$variation_to_push = $value['variation'];
		}

		if (! isset($this->matching_fonts_collection[$family_to_push])) {
			$this->matching_fonts_collection[$family_to_push] = [$variation_to_push];
		} else {
			$this->matching_fonts_collection[$family_to_push][] = $variation_to_push;
		}

		$this->matching_fonts_collection[$family_to_push] = array_unique(
			$this->matching_fonts_collection[$family_to_push]
		);
	}

	public function get_matching_google_fonts() {
		$matching_fonts_collection = $this->matching_fonts_collection;

		$matching_google_fonts = [];

		$all_fonts = $this->get_system_fonts();

		$system_fonts_families = [];

		foreach ($all_fonts as $single_google_font) {
			$system_fonts_families[] = $single_google_font['family'];
		}

		$default_family = get_theme_mod(
			'rootTypography',
			blocksy_typography_default_values([
				'family' => 'System Default',
				'variation' => 'n4',
				'size' => '16px',
				'line-height' => '1.65',
				'letter-spacing' => '0em',
				'text-transform' => 'none',
				'text-decoration' => 'none',
			])
		);

		$default_variation = $default_family['variation'];
		$default_family = $default_family['family'];

		$all_google_fonts = $this->get_googgle_fonts(true);

		foreach ($matching_fonts_collection as $family => $variations) {
			foreach ($variations as $variation) {
				$family_to_use = $family;
				$variation_to_use = $variation;

				if ($family_to_use === 'Default') {
					$family_to_use = $default_family;
				}

				if ($variation_to_use === 'Default') {
					$variation_to_use = $default_variation;
				}

				if (
					in_array($family_to_use, $system_fonts_families)
					||
					$family_to_use === 'Default'
					||
					! isset($all_google_fonts[$family_to_use])
				) {
					continue;
				}

				if (! isset($matching_google_fonts[$family_to_use])) {
					$matching_google_fonts[$family_to_use] = [$variation_to_use];
				} else {
					$matching_google_fonts[$family_to_use][] = $variation_to_use;
				}

				$matching_google_fonts[$family_to_use] = array_unique(
					$matching_google_fonts[$family_to_use]
				);
			}
		}

		return $matching_google_fonts;
	}

	public function load_dynamic_google_fonts($matching_google_fonts) {
		$has_dynamic_google_fonts = apply_filters(
			'blocksy:typography:google:use-remote',
			true
		);

		if (! $has_dynamic_google_fonts) {
			return;
		}

		$url = $this->get_google_fonts_url($matching_google_fonts);

		if (! empty($url)) {
			wp_register_style('blocksy-fonts-font-source-google', $url, [], null);
			wp_enqueue_style('blocksy-fonts-font-source-google');
		}
	}

	public function load_editor_fonts() {
		$has_dynamic_google_fonts = apply_filters(
			'blocksy:typography:google:use-remote',
			true
		);

		if (! $has_dynamic_google_fonts) {
			return '';
		}

		$dynamic_styles_descriptor = blocksy_manager()
			->dynamic_css
			->get_dynamic_styles_descriptor();

		$matching_google_fonts = $dynamic_styles_descriptor['google_fonts'];

		$url = $this->get_google_fonts_url($matching_google_fonts);

		return $url;
	}

	private function get_google_fonts_url($to_enqueue = []) {
		$url = 'https://fonts.googleapis.com/css2?';

		$families = [];

		foreach ($to_enqueue as $family => $variations) {
			$to_push = 'family=' . $family . ':';

			$ital_vars = [];
			$wght_vars = [];

			foreach ($variations as $variation) {
				$var_to_push = intval($variation[1]) * 100;
				$var_to_push .= $variation[0] === 'i' ? 'i' : '';

				if ($variation[0] === 'i') {
					$ital_vars[] = intval($variation[1]) * 100;
				} else {
					$wght_vars[] = intval($variation[1]) * 100;
				}
			}

			sort($ital_vars);
			sort($wght_vars);

			$axis_tag_list = [];

			if (count($ital_vars) > 0) {
				$axis_tag_list[] = 'ital';
			}

			$axis_tag_list[] = 'wght';

			$to_push .= implode(',', $axis_tag_list);
			$to_push .= '@';

			$all_vars = [];

			foreach ($wght_vars as $wght_var) {
				if (count($axis_tag_list) > 1) {
					$all_vars[] = '0,' . $wght_var;
				} else {
					$all_vars[] = $wght_var;
				}
			}

			foreach ($ital_vars as $ital_var) {
				$all_vars[] = '1,' . $ital_var;
			}

			$to_push .= implode(';', array_unique($all_vars));

			$families[] = $to_push;
		}

		$families = implode('&', $families);

		if (! empty($families)) {
			$url .= $families;
			$url .= '&display=swap';

			return $url;
		}

		return false;
	}

	public function get_system_fonts($as_string = false) {
		$system = [
			'System Default',
			'Arial', 'Verdana', 'Trebuchet', 'Georgia', 'Times New Roman',
			'Palatino', 'Helvetica', 'Myriad Pro',
			'Lucida', 'Gill Sans', 'Impact', 'Serif', 'monospace'
		];

		if ($as_string) {
			return $system;
		}

		$result = [];

		foreach ($system as $font) {
			$display = $font;

			if ($font === 'System Default') {
				$display = __('System Default', 'blocksy');
			}

			$result[] = [
				'source' => 'system',
				'family' => $font,
				'display' => $display,
				'variations' => [],
				'all_variations' => $this->get_standard_variations_descriptors()
			];
		}

		return $result;
	}

	public function get_standard_variations_descriptors() {
		return [
			'n1', 'i1', 'n2', 'i2', 'n3', 'i3', 'n4', 'i4', 'n5', 'i5', 'n6',
			'i6', 'n7', 'i7', 'n8', 'i8', 'n9', 'i9'
		];
	}

	public function retrieve_all_google_fonts() {
		$saved_data = get_option('blocksy_google_fonts', false);
		$ttl = 7 * DAY_IN_SECONDS;

		/*
		$saved_data_for_log = $saved_data;

		if (
			$saved_data
			&&
			isset($saved_data['fonts'])
		) {
			$log_fonts = json_decode($saved_data['fonts'], true);

			if (isset($log_fonts['items'])) {
				$log_fonts['items'] = count($log_fonts['items']);
			}

			$saved_data_for_log['fonts'] = $log_fonts;
		}

		error_log(
			'Blocksy decoded cached blocksy_google_fonts: ' . print_r(
				$saved_data_for_log,
				true
			)
		);
		 */

		if (
			false === $saved_data
			||
			(($saved_data['last_update'] + $ttl) < time())
			||
			! is_array($saved_data)
			||
			! isset($saved_data['fonts'])
			||
			empty($saved_data['fonts'])
			||
			! json_decode($saved_data['fonts'], true)
			||
			empty(json_decode($saved_data['fonts'], true)['items'])
		) {
			add_filter('https_ssl_verify', '__return_false');

			$response = wp_remote_get(
				'https://demo.creativethemes.com/?route=google_fonts'
			);

			$body = wp_remote_retrieve_body($response);

			if (
				200 === wp_remote_retrieve_response_code($response)
				&&
				! is_wp_error($body) && ! empty($body)
			) {
				$all_fonts = json_decode($body, true);

				if (isset($all_fonts['variable_fonts'])) {
					$variable_fonts = $all_fonts['variable_fonts'];

					foreach ($all_fonts['items'] as $key => $value) {
						if (! in_array($all_fonts['items'][$key]['family'], $variable_fonts)) {
							continue;
						}

						$all_fonts['items'][$key]['variants'] = [
							"100",
							"200",
							"300",
							"regular",
							"500",
							"600",
							"700",
							"800",
							"900",
							"100italic",
							"200italic",
							"300italic",
							"italic",
							"500italic",
							"600italic",
							"700italic",
							"800italic",
							"900italic"
						];

						$all_fonts['items'][$key]['variable'] = true;
					}
				}

				update_option('blocksy_google_fonts', [
					'last_update' => time(),
					'fonts' => json_encode($all_fonts)
				], false);

				return $all_fonts;
			} else {
				if (empty($saved_data['fonts'])) {
					$saved_data['fonts'] = json_encode(['items' => []]);
				}

				update_option(
					'blocksy_google_fonts',
					array(
						'last_update' => time() - $ttl + MINUTE_IN_SECONDS,
						'fonts' => $saved_data['fonts']
					),
					false
				);
			}
		}

		return json_decode($saved_data['fonts'], true);
	}

	public function get_googgle_fonts($as_keys = false) {
		$maybe_custom_source = apply_filters(
			'blocksy-typography-google-fonts-source',
			null
		);

		if ($maybe_custom_source) {
			return $maybe_custom_source;
		}

		$response = $this->retrieve_all_google_fonts();

		if (! isset($response['items'])) {
			return false;
		}

		if (! is_array($response['items']) || !count($response['items'])) {
			return false;
		}

		foreach ($response['items'] as $key => $row) {
			$response['items'][$key] = $this->prepare_font_data($row);
		}

		if (! $as_keys) {
			return $response['items'];
		}

		$result = [];

		foreach ($response['items'] as $single_item) {
			$result[$single_item['family']] = true;
		}

		return $result;
	}

	private function prepare_font_data($font) {
		$font['source'] = 'google';

		$font['variations'] = [];

		if (isset($font['variants'])) {
			$font['all_variations'] = $this->change_variations_structure($font['variants']);
		}

		unset($font['variants']);
		return $font;
	}

	private function change_variations_structure( $structure ) {
		$result = [];

		foreach($structure as $weight) {
			$result[] = $this->get_weight_and_style_key($weight);
		}

		return $result;
	}

	private function get_weight_and_style_key($code) {
		$prefix = 'n'; // Font style: italic = `i`, regular = n.
		$sufix = '4';  // Font weight: 1 -> 9.

		$value = strtolower(trim($code));
		$value = str_replace(' ', '', $value);

		# Only number.
		if (is_numeric($value) && isset($value[0])) {
			$sufix = $value[0];
			$prefix = 'n';
		}

		// Italic.
		if (preg_match("#italic#", $value)) {
			if ('italic' === $value) {
				$sufix = 4;
				$prefix = 'i';
			} else {
				$value = trim(str_replace('italic', '', $value));
				if (is_numeric($value) && isset($value[0])) {
					$sufix = $value[0];
					$prefix = 'i';
				}
			}
		}

		// Regular.
		if (preg_match("#regular|normal#", $value)) {
			if ('regular' === $value) {
				$sufix = 4;
				$prefix = 'n';
			} else {
				$value = trim(str_replace(array('regular', 'normal') , '', $value));

				if (is_numeric($value) && isset($value[0])) {
					$sufix = $value[0];
					$prefix = 'n';
				}
			}
		}

		return "{$prefix}{$sufix}";
	}
}

if (! function_exists('blocksy_output_font_css')) {
	function blocksy_output_font_css($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'css' => null,
				'tablet_css' => null,
				'mobile_css' => null,
				'font_value' => null,
				'selector' => ':root',
				'prefix' => ''
			]
		);

		if (! $args['css']) {
			throw new Error('css missing in args!');
		}

		if (! $args['tablet_css']) {
			throw new Error('tablet_css missing in args!');
		}

		if (! $args['mobile_css']) {
			throw new Error('mobile_css missing in args!');
		}

		$args['css']->process_matching_typography($args['font_value']);

		if ($args['font_value']['family'] === 'System Default') {
			$args['font_value']['family'] = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'";
		} else {
			$fonts_manager = new Blocksy_Fonts_Manager();

			if (! in_array(
				$args['font_value']['family'],
				$fonts_manager->get_system_fonts(true)
			) && $args['font_value']['family'] !== 'Default') {
				if (
					$args['font_value']['family'] !== 'Default'
					&&
					strpos($args['font_value']['family'], ' ') !== false
				) {
					$args['font_value']['family'] = "'" . $args['font_value']['family'] . "'";
				}

				$font_family_fallback = get_theme_mod(
					'font_family_fallback',
					'Sans-Serif'
				);

				if (! empty($font_family_fallback)) {
					$args['font_value']['family'] .= ", " . $font_family_fallback;
				}
			}
		}

		$args['font_value']['family'] = apply_filters(
			'blocksy:css:typography:output:family',
			$args['font_value']['family'],
			$args['font_value']
		);

		if ($args['font_value']['family'] === 'Default') {
			$args['font_value']['family'] = 'CT_CSS_SKIP_RULE';
		}

		$correct_font_family = str_replace(
			'ct_typekit_',
			'',
			$args['font_value']['family']
		);

		$args['css']->put(
			$args['selector'],
			"--" . blocksy_camel_case_prefix('fontFamily', $args['prefix']) . ": {$correct_font_family}"
		);

		$weight_and_style = blocksy_get_css_for_variation(
			$args['font_value']['variation']
		);

		$args['css']->put(
			$args['selector'],
			"--" . blocksy_camel_case_prefix('fontWeight', $args['prefix']) . ": {$weight_and_style['weight']}"
		);

		if ($weight_and_style['style'] !== 'normal') {
			$args['css']->put(
				$args['selector'],
				"--" . blocksy_camel_case_prefix('fontStyle', $args['prefix']) . ": {$weight_and_style['style']}"
			);
		}

		$args['css']->put(
			$args['selector'],
			"--" . blocksy_camel_case_prefix('textTransform', $args['prefix']) . ": {$args['font_value']['text-transform']}"
		);

		$args['css']->put(
			$args['selector'],
			"--" . blocksy_camel_case_prefix('textDecoration', $args['prefix']) . ": {$args['font_value']['text-decoration']}"
		);

		blocksy_output_responsive([
			'css' => $args['css'],
			'tablet_css' => $args['tablet_css'],
			'mobile_css' => $args['mobile_css'],
			'selector' => $args['selector'],
			'variableName' => blocksy_camel_case_prefix('fontSize', $args['prefix']),
			'unit' => '',
			'value' => $args['font_value']['size']
		]);

		blocksy_output_responsive([
			'css' => $args['css'],
			'tablet_css' => $args['tablet_css'],
			'mobile_css' => $args['mobile_css'],
			'selector' => $args['selector'],
			'variableName' => blocksy_camel_case_prefix(
				'lineHeight',
				$args['prefix']
			),
			'unit' => '',
			'value' => $args['font_value']['line-height']
		]);

		blocksy_output_responsive([
			'css' => $args['css'],
			'tablet_css' => $args['tablet_css'],
			'mobile_css' => $args['mobile_css'],
			'selector' => $args['selector'],
			'variableName' => blocksy_camel_case_prefix(
				'letterSpacing',
				$args['prefix']
			),
			'unit' => '',
			'value' => $args['font_value']['letter-spacing']
		]);
	}
}

if (! function_exists('blocksy_get_css_for_variation')) {
	function blocksy_get_css_for_variation($variation, $should_output_normals = true) {
		$weight_and_style = [
			'style' => '',
			'weight' => '',
		];

		if ($variation === 'Default') {
			return [
				'style' => 'CT_CSS_SKIP_RULE',
				'weight' => 'CT_CSS_SKIP_RULE'
			];
		}

		if (preg_match(
			"#(n|i)(\d+?)$#",
			$variation,
			$matches
		)) {
			if ('i' === $matches[1]) {
				$weight_and_style['style'] = 'italic';
			} else {
				$weight_and_style['style'] = 'normal';
			}

			$weight_and_style['weight'] = (int) $matches[2] . '00';
		}

		return $weight_and_style;
	}
}

if (! function_exists('blocksy_typography_default_values')) {
	function blocksy_typography_default_values($values = []) {
		return array_merge([
			'family' => 'Default',
			'variation' => 'Default',

			'size' => '17px',
			'line-height' => '1.65',
			'letter-spacing' => '0em',
			'text-transform' => 'none',
			'text-decoration' => 'none',

			'size' => 'CT_CSS_SKIP_RULE',
			'line-height' => 'CT_CSS_SKIP_RULE',
			'letter-spacing' => 'CT_CSS_SKIP_RULE',
			'text-transform' => 'CT_CSS_SKIP_RULE',
			'text-decoration' => 'CT_CSS_SKIP_RULE',
		], $values);
	}
}

add_action( 'wp_ajax_blocksy_get_fonts_list', function () {
	if (! current_user_can('edit_theme_options')) {
		wp_send_json_error();
	}

	$m = new Blocksy_Fonts_Manager();

	wp_send_json_success([
		'fonts' => $m->get_all_fonts()
	]);
});

