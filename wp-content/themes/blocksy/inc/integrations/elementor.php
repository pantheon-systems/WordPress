<?php

add_action('init', function () {
	if (
		get_option(
			'elementor_disable_color_schemes',
			'__DEFAULT__'
		) === '__DEFAULT__'
	) {
		update_option('elementor_disable_color_schemes', 'yes');
	}

	add_filter('elementor/schemes/enabled_schemes', function ($s) {
		// blocksy_print($s);
		// return ['color'];
		return $s;
	});

	if (
		get_option(
			'elementor_disable_typography_schemes',
			'__DEFAULT__'
		) === '__DEFAULT__'
	) {
		update_option('elementor_disable_typography_schemes', 'yes');
	}

	if (! get_option('elementor_viewport_lg')) {
		update_option('elementor_viewport_lg', 1000);
	}

	if (! get_option('elementor_viewport_md')) {
		update_option('elementor_viewport_md', 690);
	}

	add_filter(
		'rest_request_after_callbacks',
		function ($response, $handler, \WP_REST_Request $request) {
			$route = $request->get_route();
			$rest_id = substr($route, strrpos($route, '/') + 1);

			$palettes = [
				'blocksy_palette_1' => [
					'id' => 'blocksy_palette_1',
					'title' => __('Theme Color Palette 1', 'blocksy'),
					'value' => 'var(--paletteColor1)'
				],

				'blocksy_palette_2' => [
					'id' => 'blocksy_palette_2',
					'title' => __('Theme Color Palette 2', 'blocksy'),
					'value' => 'var(--paletteColor2)'
				],

				'blocksy_palette_3' => [
					'id' => 'blocksy_palette_3',
					'title' => __('Theme Color Palette 3', 'blocksy'),
					'value' => 'var(--paletteColor3)'
				],

				'blocksy_palette_4' => [
					'id' => 'blocksy_palette_4',
					'title' => __('Theme Color Palette 4', 'blocksy'),
					'value' => 'var(--paletteColor4)'
				],

				'blocksy_palette_5' => [
					'id' => 'blocksy_palette_5',
					'title' => __('Theme Color Palette 5', 'blocksy'),
					'value' => 'var(--paletteColor5)'
				],

				'blocksy_palette_6' => [
					'id' => 'blocksy_palette_6',
					'title' => __('Theme Color Palette 6', 'blocksy'),
					'value' => 'var(--paletteColor6)'
				],

				'blocksy_palette_7' => [
					'id' => 'blocksy_palette_7',
					'title' => __('Theme Color Palette 7', 'blocksy'),
					'value' => 'var(--paletteColor7)'
				],

				'blocksy_palette_8' => [
					'id' => 'blocksy_palette_8',
					'title' => __('Theme Color Palette 8', 'blocksy'),
					'value' => 'var(--paletteColor8)'
				]
			];

			if (isset($palettes[$rest_id])) {
				return new \WP_REST_Response($palettes[$rest_id]);
			}

			if (
				$route === '/elementor/v1/globals'
				&&
				method_exists($response, 'get_data')
			) {
				$data = $response->get_data();

				$colors = blocksy_get_colors(get_theme_mod('colorPalette'), [
					'color1' => [ 'color' => '#2872fa' ],
					'color2' => [ 'color' => '#1559ed' ],
					'color3' => [ 'color' => '#3A4F66' ],
					'color4' => [ 'color' => '#192a3d' ],
					'color5' => [ 'color' => '#e1e8ed' ],
					'color6' => [ 'color' => '#f2f5f7' ],
					'color7' => [ 'color' => '#FAFBFC' ],
					'color8' => [ 'color' => '#ffffff' ],
				]);

				$colors_for_palette = [
					'blocksy_palette_1' => 'color1',
					'blocksy_palette_2' => 'color2',
					'blocksy_palette_3' => 'color3',
					'blocksy_palette_4' => 'color4',
					'blocksy_palette_5' => 'color5',
					'blocksy_palette_6' => 'color6',
					'blocksy_palette_7' => 'color7',
					'blocksy_palette_8' => 'color8'
				];

				foreach ($palettes as $key => $value) {
					$value['value'] = $colors[
						$colors_for_palette[$key]
					];

					$data['colors'][$key] = $value;
				}

				$response->set_data($data);
			}

			return $response;
		},
		1000, 3
	);

	/*
	add_action('elementor/frontend/section/before_render', function ($element) {
		$settings = $element->get_settings_for_display();

		if (
			! $element->get_data('isInner')
			&&
			blocksy_akg('blocksy_stretch_section', $settings, '') !== 'stretched'
		) {
			$element->add_render_attribute('_wrapper', [
				'class' => 'ct-section-boxed'
			]);
		}
	});
	 */

	add_action(
		'elementor/element/section/section_layout/after_section_start',
		function ($element, $args) {
			$element->add_control('blocksy_stretch_section', [
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => esc_html__( 'Full Width Section', 'blocksy' ),
				'return_value' => 'stretched',
				'hide_in_inner' => true,
				'default' => '',
				'separator' => 'after',
				'prefix_class' => 'ct-section-',
			]);
		},
		10, 2
	);

	add_action(
		'elementor/element/section/section_layout/before_section_end',
		function ($element, $args) {
			$element->remove_control('stretch_section');
			// $element->add_control('fix_columns_alignment', [
			// 	'type' => \Elementor\Controls_Manager::SWITCHER,
			// 	'label' => esc_html__( 'Columns Alignment Fix', 'blocksy' ),
			// 	'return_value' => 'fix',
			// 	'default' => apply_filters(
			// 		'blocksy:integrations:elementor:fix_columns_alignment:default',
			// 		''
			// 	),
			// 	'separator' => 'before',
			// 	'prefix_class' => 'ct-columns-alignment-',
			// ]);
		},
		10, 2
	);

	add_action('elementor/editor/after_enqueue_styles', function () {
		if (! apply_filters(
			'blocksy:integrations:elementor:has-ui-styles',
			true
		)) {
			return;
		}

		$theme = blocksy_get_wp_parent_theme();

		wp_enqueue_style(
			'blocksy-elementor-styles',
			get_template_directory_uri() . '/static/bundle/elementor.min.css',
			[],
			$theme->get('Version')
		);
	});
});

add_action(
	'elementor/theme/register_locations',
	function ($elementor_theme_manager) {
		$elementor_theme_manager->register_all_core_location();
	}
);

