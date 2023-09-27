<?php

if (! function_exists('blocksy_values_are_similar')) {
	function blocksy_values_are_similar($actual, $expected) {
		if (!is_array($expected) || !is_array($actual)) return $actual === $expected;

		foreach ($expected as $key => $value) {
			if (! blocksy_values_are_similar($actual[$key], $expected[$key])) return false;
		}

		foreach ($actual as $key => $value) {
			if (! blocksy_values_are_similar($actual[$key], $expected[$key])) return false;
		}

		return true;
	}
}

if (! function_exists('blocksy_get_patterns_svgs_list')) {
function blocksy_get_patterns_svgs_list() {
	return array(
		'type-1' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY'%3E%3Cpath d='M0 38.59l2.83-2.83 1.41 1.41L1.41 40H0v-1.41zM0 1.4l2.83 2.83 1.41-1.41L1.41 0H0v1.41zM38.59 40l-2.83-2.83 1.41-1.41L40 38.59V40h-1.41zM40 1.41l-2.83 2.83-1.41-1.41L38.59 0H40v1.41zM20 18.6l2.83-2.83 1.41 1.41L21.41 20l2.83 2.83-1.41 1.41L20 21.41l-2.83 2.83-1.41-1.41L18.59 20l-2.83-2.83 1.41-1.41L20 18.59z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E",

		'type-2' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='72' viewBox='0 0 36 72'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY'%3E%3Cpath d='M2 6h12L8 18 2 6zm18 36h12l-6 12-6-12z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E",

		'type-3' => "data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-4' => "data:image/svg+xml,%3Csvg width='52' height='26' viewBox='0 0 52 26' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY'%3E%3Cpath d='M10 10c0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6h2c0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4 3.314 0 6 2.686 6 6 0 2.21 1.79 4 4 4v2c-3.314 0-6-2.686-6-6 0-2.21-1.79-4-4-4-3.314 0-6-2.686-6-6zm25.464-1.95l8.486 8.486-1.414 1.414-8.486-8.486 1.414-1.414z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E",

		'type-5' => "data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E",

		'type-6' => "data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10zm10 8c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm40 40c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E",

		'type-7' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4' viewBox='0 0 4 4'%3E%3Cpath fill='%23COLOR' fill-opacity='OPACITY' d='M1 3h1v1H1V3zm2-2h1v1H3V1z'%3E%3C/path%3E%3C/svg%3E",

		'type-8' => "data:image/svg+xml,%3Csvg width='6' height='6' viewBox='0 0 6 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'%3E%3Cpath d='M5 0h1L0 6V5zM6 5v1H5z'/%3E%3C/g%3E%3C/svg%3E",

		'type-9' => "data:image/svg+xml,%3Csvg width='12' height='16' viewBox='0 0 12 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4 .99C4 .445 4.444 0 5 0c.552 0 1 .45 1 .99v4.02C6 5.555 5.556 6 5 6c-.552 0-1-.45-1-.99V.99zm6 8c0-.546.444-.99 1-.99.552 0 1 .45 1 .99v4.02c0 .546-.444.99-1 .99-.552 0-1-.45-1-.99V8.99z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-10' => "data:image/svg+xml,%3Csvg width='40' height='1' viewBox='0 0 40 1' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h20v1H0z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-11' => "data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E",

		'type-12' => "data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M15 0C6.716 0 0 6.716 0 15c8.284 0 15-6.716 15-15zM0 15c0 8.284 6.716 15 15 15 0-8.284-6.716-15-15-15zm30 0c0-8.284-6.716-15-15-15 0 8.284 6.716 15 15 15zm0 0c0 8.284-6.716 15-15 15 0-8.284 6.716-15 15-15z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-13' => "data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-14' => "data:image/svg+xml,%3Csvg width='40' height='12' viewBox='0 0 40 12' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 6.172L6.172 0h5.656L0 11.828V6.172zm40 5.656L28.172 0h5.656L40 6.172v5.656zM6.172 12l12-12h3.656l12 12h-5.656L20 3.828 11.828 12H6.172zm12 0L20 10.172 21.828 12h-3.656z' fill='%23COLOR' fill-opacity='OPACITY' fill-rule='evenodd'/%3E%3C/svg%3E",

		'type-15' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 56 28' width='56' height='28'%3E%3Cpath fill='%23COLOR' fill-opacity='OPACITY' d='M56 26v2h-7.75c2.3-1.27 4.94-2 7.75-2zm-26 2a2 2 0 1 0-4 0h-4.09A25.98 25.98 0 0 0 0 16v-2c.67 0 1.34.02 2 .07V14a2 2 0 0 0-2-2v-2a4 4 0 0 1 3.98 3.6 28.09 28.09 0 0 1 2.8-3.86A8 8 0 0 0 0 6V4a9.99 9.99 0 0 1 8.17 4.23c.94-.95 1.96-1.83 3.03-2.63A13.98 13.98 0 0 0 0 0h7.75c2 1.1 3.73 2.63 5.1 4.45 1.12-.72 2.3-1.37 3.53-1.93A20.1 20.1 0 0 0 14.28 0h2.7c.45.56.88 1.14 1.29 1.74 1.3-.48 2.63-.87 4-1.15-.11-.2-.23-.4-.36-.59H26v.07a28.4 28.4 0 0 1 4 0V0h4.09l-.37.59c1.38.28 2.72.67 4.01 1.15.4-.6.84-1.18 1.3-1.74h2.69a20.1 20.1 0 0 0-2.1 2.52c1.23.56 2.41 1.2 3.54 1.93A16.08 16.08 0 0 1 48.25 0H56c-4.58 0-8.65 2.2-11.2 5.6 1.07.8 2.09 1.68 3.03 2.63A9.99 9.99 0 0 1 56 4v2a8 8 0 0 0-6.77 3.74c1.03 1.2 1.97 2.5 2.79 3.86A4 4 0 0 1 56 10v2a2 2 0 0 0-2 2.07 28.4 28.4 0 0 1 2-.07v2c-9.2 0-17.3 4.78-21.91 12H30zM7.75 28H0v-2c2.81 0 5.46.73 7.75 2zM56 20v2c-5.6 0-10.65 2.3-14.28 6h-2.7c4.04-4.89 10.15-8 16.98-8zm-39.03 8h-2.69C10.65 24.3 5.6 22 0 22v-2c6.83 0 12.94 3.11 16.97 8zm15.01-.4a28.09 28.09 0 0 1 2.8-3.86 8 8 0 0 0-13.55 0c1.03 1.2 1.97 2.5 2.79 3.86a4 4 0 0 1 7.96 0zm14.29-11.86c1.3-.48 2.63-.87 4-1.15a25.99 25.99 0 0 0-44.55 0c1.38.28 2.72.67 4.01 1.15a21.98 21.98 0 0 1 36.54 0zm-5.43 2.71c1.13-.72 2.3-1.37 3.54-1.93a19.98 19.98 0 0 0-32.76 0c1.23.56 2.41 1.2 3.54 1.93a15.98 15.98 0 0 1 25.68 0zm-4.67 3.78c.94-.95 1.96-1.83 3.03-2.63a13.98 13.98 0 0 0-22.4 0c1.07.8 2.09 1.68 3.03 2.63a9.99 9.99 0 0 1 16.34 0z'%3E%3C/path%3E%3C/svg%3E",

		'type-16' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23COLOR' fill-opacity='OPACITY' viewBox='0 0 56 100' width='56' height='100'%3E%3Cpath d='M28-1.2L-1,15.4v34v1.2v34l29,16.6l29-16.6v-34v-1.2v-34L28-1.2z M2,50l26-14.8L54,50L28,64.8L2,50z M1,16.6L27,1.7v31.7 L1,48.3V16.6z M1,51.7l26,14.9v31.7L1,83.4V51.7z M55,83.4L29,98.3V66.6l26-14.9V83.4z M29,33.4V1.7l26,14.9v31.7L29,33.4z'/%3E%3C/svg%3E",

		'type-17' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23COLOR' fill-opacity='OPACITY' viewBox='0 0 8 8' width='8' height='8'%3E%3Cpath d='M4.7,4l3.6,3.6L7.6,8.4L4,4.7L0.4,8.4l-0.7-0.7L3.3,4l-3.6-3.6l0.7-0.7L4,3.3l3.6-3.6l0.7,0.7L4.7,4z'/%3E%3C/svg%3E",

		'type-18' => "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='88' height='24' viewBox='0 0 88 24'%3E%3Cg fill-rule='evenodd'%3E%3Cg id='autumn' fill='%23COLOR' fill-opacity='OPACITY'%3E%3Cpath d='M10 0l30 15 2 1V2.18A10 10 0 0 0 41.76 0H39.7a8 8 0 0 1 .3 2.18v10.58L14.47 0H10zm31.76 24a10 10 0 0 0-5.29-6.76L4 1 2 0v13.82a10 10 0 0 0 5.53 8.94L10 24h4.47l-6.05-3.02A8 8 0 0 1 4 13.82V3.24l31.58 15.78A8 8 0 0 1 39.7 24h2.06zM78 24l2.47-1.24A10 10 0 0 0 86 13.82V0l-2 1-32.47 16.24A10 10 0 0 0 46.24 24h2.06a8 8 0 0 1 4.12-4.98L84 3.24v10.58a8 8 0 0 1-4.42 7.16L73.53 24H78zm0-24L48 15l-2 1V2.18A10 10 0 0 1 46.24 0h2.06a8 8 0 0 0-.3 2.18v10.58L73.53 0H78z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E",
	);
}
}

if (! function_exists('blocksy_get_svg_pattern')) {
	function blocksy_get_svg_pattern($name, $color = '#ccc') {
		$opacity = 1;

		$colorPalette = blocksy_get_colors(
			get_theme_mod('colorPalette'),
			[
				'color1' => ['color' => '#2872fa'],
				'color2' => ['color' => '#1559ed'],
				'color3' => ['color' => '#3A4F66'],
				'color4' => ['color' => '#192a3d'],
				'color5' => ['color' => '#e1e8ed'],
				'color6' => ['color' => '#f2f5f7'],
				'color7' => ['color' => '#FAFBFC'],
				'color8' => ['color' => '#ffffff'],
			]
		);

		if (strpos($color, 'paletteColor1') !== false) {
			$color = $colorPalette['color1'];
		}

		if (strpos($color, 'paletteColor2') !== false) {
			$color = $colorPalette['color2'];
		}

		if (strpos($color, 'paletteColor3') !== false) {
			$color = $colorPalette['color3'];
		}

		if (strpos($color, 'paletteColor4') !== false) {
			$color = $colorPalette['color4'];
		}

		if (strpos($color, 'paletteColor5') !== false) {
			$color = $colorPalette['color5'];
		}

		if (strpos($color, 'paletteColor6') !== false) {
			$color = $colorPalette['color6'];
		}

		if (strpos($color, 'paletteColor7') !== false) {
			$color = $colorPalette['color7'];
		}

		if (strpos($color, 'paletteColor8') !== false) {
			$color = $colorPalette['color8'];
		}

		if (strpos($color, 'rgb') !== false) {
			$rgb_array = explode(',', str_replace(
				'rgb(',
				'',
				str_replace(
					')', '',
					str_replace(
						'rgba(',
						'',
						str_replace(' ', '', $color)
					)
				)
			));

			$color = sprintf("#%02x%02x%02x", $rgb_array[0], $rgb_array[1], $rgb_array[2]);

			if (count($rgb_array) > 3) {
				$opacity = $rgb_array[3];
			}
		}

		$color = str_replace('#', '', $color);
		$patterns = blocksy_get_patterns_svgs_list();

		return str_replace(
			'OPACITY',
			$opacity,
			str_replace(
				'COLOR',
				$color,
				$patterns[$name]
			)
		);
	}
}

if (! function_exists('blocksy_output_background_css'))  {
	function blocksy_output_background_css($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'value' => [],
				'css' => null,
				'tablet_css' => null,
				'mobile_css' => null,
				'selector' => null,

				'desktop_selector_prefix' => '',
				'tablet_selector_prefix' => '',
				'mobile_selector_prefix' => '',

				'important' => false,
				'responsive' => false,

				'conditional_var' => null,

				'forced_background_image' => false
			]
		);

		if (! $args['responsive']) {
			blocksy_output_single_background_css([
				'value' => $args['value'],
				'css' => $args['css'],
				'selector' => $args['selector'],
				'important' => $args['important'],
				'conditional_var' => $args['conditional_var'],
				'forced_background_image' => $args['forced_background_image']
			]);
			return;
		}

		blocksy_assert_args($args, ['tablet_css', 'mobile_css']);

		$responsive_value = blocksy_expand_responsive_value($args['value']);

		blocksy_output_single_background_css([
			'value' => $responsive_value['desktop'],
			'css' => $args['css'],
			'selector' => empty($args['desktop_selector_prefix']) ? $args['selector'] : (
				$args['desktop_selector_prefix'] . ' ' . $args['selector']
			),
			'important' => $args['important'],
			'conditional_var' => $args['conditional_var'],
			'forced_background_image' => $args['forced_background_image']
		]);

		if (! blocksy_values_are_similar(
			$responsive_value['tablet'],
			$responsive_value['desktop'])
		) {
			blocksy_output_single_background_css([
				'value' => $responsive_value['tablet'],
				'css' => $args['tablet_css'],
				'selector' => empty($args['tablet_selector_prefix']) ? $args['selector'] : (
					$args['tablet_selector_prefix'] . ' ' . $args['selector']
				),
				'important' => $args['important'],
				'conditional_var' => $args['conditional_var'],
				'forced_background_image' => $args['forced_background_image']
			]);
		}

		if (! blocksy_values_are_similar(
			$responsive_value['mobile'],
			$responsive_value['tablet'])
		) {
			blocksy_output_single_background_css([
				'value' => $responsive_value['mobile'],
				'css' => $args['mobile_css'],
				'selector' => empty($args['mobile_selector_prefix']) ? $args['selector'] : (
					$args['mobile_selector_prefix'] . ' ' . $args['selector']
				),
				'important' => $args['important'],
				'conditional_var' => $args['conditional_var'],
				'forced_background_image' => $args['forced_background_image']
			]);
		}
	}
}

if (! function_exists('blocksy_output_single_background_css')) {
	function blocksy_output_single_background_css($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'value' => [],
				'css' => null,
				'selector' => null,
				'important' => false,

				'conditional_var' => null,
				'forced_background_image' => false
			]
		);

		$args['value'] = blocksy_background_default_value($args['value']);

		$backgroundColor = blocksy_get_colors(
			blocksy_akg(
				'backgroundColor',
				$args['value'],
				['default' => ['color' => '#e5e7ea']]
			),
			['default' => ['color' => '#e5e7ea']]
		);

		$overlayColor = blocksy_get_colors(
			blocksy_akg(
				'overlayColor',
				$args['value'],
				['default' => ['color' => '#e5e7ea']]
			),
			['default' => ['color' => '#e5e7ea']]
		);

		$image_url = 'CT_CSS_SKIP_RULE';

		if ($backgroundColor['default'] !== 'CT_CSS_SKIP_RULE') {
			$image_url = 'none';
		}

		if ($args['value']['background_type'] === 'image') {
			$background_image = blocksy_akg('background_image', $args['value'], [
				'attachment_id' => null,
				'x' => 0.5,
				'y' => 0.5
			]);

			$attachment_url = wp_get_attachment_url($background_image['attachment_id']);

			if (! empty($attachment_url)) {
				if (! empty($attachment_url)) {
					$image_url = 'url(' . $attachment_url . ')';
				}

				$background_position = blocksy_maybe_append_important(
					(floatval($background_image['x']) * 100) . "% " . (
						floatval($background_image['y']) * 100
					) . '%',
					$args['important']
				);

				if (
					floatval($background_image['x']) !== 0
					||
					floatval($background_image['y']) !== 0
				) {
					$args['css']->put(
						$args['selector'],
						"background-position: {$background_position}"
					);
				}

				$background_size = blocksy_maybe_append_important(
					blocksy_akg('background_size', $args['value'], 'auto'),
					$args['important']
				);

				if ($background_size !== 'auto') {
					$args['css']->put(
						$args['selector'],
						"background-size: {$background_size}"
					);
				}

				$background_attachment = blocksy_maybe_append_important(blocksy_akg(
					'background_attachment',
					$args['value'],
					'scroll'
				), $args['important']);

				if ($background_attachment !== 'scroll') {
					$args['css']->put(
						$args['selector'],
						"background-attachment: {$background_attachment}"
					);
				}

				$background_repeat = blocksy_maybe_append_important(blocksy_akg(
					'background_repeat',
					$args['value'],
					'repeat'
				), $args['important']);

				if ($background_repeat !== 'repeat') {
					$args['css']->put(
						$args['selector'],
						"background-repeat: {$background_repeat}"
					);
				}
			}
		}

		if ($args['value']['background_type'] === 'pattern') {
			$patternColor = blocksy_get_colors(
				blocksy_akg(
					'patternColor',
					$args['value'],
					['default' => ['color' => '#e5e7ea']]
				),
				['default' => ['color' => '#e5e7ea']]
			);

			$image_url = 'url("' . blocksy_get_svg_pattern(
				blocksy_akg('background_pattern', $args['value'], 'type-1'),
				$patternColor['default']
			) . '")';
		}

		if ($args['value']['background_type'] === 'gradient') {
			$gradient = blocksy_akg('gradient', $args['value'], '');

			if (! empty($gradient)) {
				$image_url = $gradient;
			}
		}

		$backgroundColor['default'] = blocksy_maybe_append_important(
			$backgroundColor['default'],
			$args['important']
		);

		if ($args['conditional_var']) {
			$backgroundColor['default'] = 'var(' . $args[
				'conditional_var'
			] . ', ' . $backgroundColor['default'] . ')';
		}

		$args['css']->put(
			$args['selector'],
			"background-color: {$backgroundColor['default']}"
		);

		if (
			strpos($backgroundColor['default'], 'CT_CSS_SKIP_RULE') === false
			&&
			$args['forced_background_image']
			&&
			strpos($image_url, 'CT_CSS_SKIP_RULE') !== false
		) {
			$image_url = 'none';
		}

		if (
			$image_url !== 'none' && $image_url !== 'CT_CSS_SKIP_RULE'
			||
			$image_url === 'none' && $args['forced_background_image']
		) {
			$image_url = blocksy_maybe_append_important($image_url, $args['important']);

			if (
				$overlayColor['default'] !== 'CT_CSS_SKIP_RULE'
				&&
				$args['value']['background_type'] === 'image'
			) {
				$image_url = 'linear-gradient(' . $overlayColor['default'] . ', ' . $overlayColor['default'] . '), ' . $image_url;
			}

			if ($args['conditional_var']) {
				$image_url = 'var(' . $args['conditional_var'] . ', ' . $image_url . ')';
			}

			$args['css']->put(
				$args['selector'],
				"background-image: {$image_url}"
			);
		}
	}
}

if (! function_exists('blocksy_background_default_value')) {
	function blocksy_background_default_value($values = []) {
		if (isset($values[array_keys($values)[0]]['color'])) {
			$values = [
				'backgroundColor' => [
					'default' => $values[array_keys($values)[0]]
				]
			];
		}

		return array_merge([
			'background_type' => 'color',
			'background_pattern' => 'type-1',
			'background_image' => [
				'attachment_id' => null,
				'x' => 0,
				'y' => 0
			],

			'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',

			'background_repeat' => 'repeat',
			'background_size' => 'auto',
			'background_attachment' => 'scroll',

			'patternColor' => [
				'default' => [
					'color' => '#e5e7ea',
				]
			],

			'overlayColor' => [
				'default' => [
					'color' => 'CT_CSS_SKIP_RULE'
				]
			],

			'backgroundColor' => [
				'default' => [
					'color' => '#f8f9fb'
				]
			]
		], $values);
	}
}
