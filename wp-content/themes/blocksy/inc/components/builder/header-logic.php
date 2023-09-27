<?php

class Blocksy_Header_Builder {
	private $default_value = null;

	private $section_value = null;
	private $current_section = null;

	public function get_default_value() {
		if ($this->default_value) {
			return $this->default_value;
		}

		$this->default_value = apply_filters('blocksy:header:default_value', [
			'current_section' => 'type-1',
			'sections' => [
				$this->get_structure_for([
					'id' => 'type-1',
					'mode' => 'placements',
					'items' => [
						'desktop' => [
							'middle-row' => [
								'start' => ['logo'],
								'end' => ['menu', 'search']
							]
						],

						'mobile' => [
							'middle-row' => [
								'start' => ['logo'],
								'end' => ['trigger']
							],

							'offcanvas' => [
								'start' => [
									'mobile-menu',
								]
							]
						]
					]
				])
			]
		], $this);

		return $this->default_value;
	}

	public function get_current_section_id() {
		return $this->get_current_section()['id'];
	}

	public function get_current_section($initial_section_id = null) {
		// TODO: needs heavy refactoring

		if (! $this->current_section || $initial_section_id) {
			if (! $initial_section_id) {
				$section_id = $this->get_filtered_section_id();
			} else {
				$section_id = $initial_section_id;
			}

			if (! $initial_section_id) {
				$this->current_section = $this->get_section_value()['sections'][0];
			}

			foreach ($this->get_section_value()['sections'] as $single_section) {
				if ($single_section['id'] === $section_id) {
					if (! $initial_section_id) {
						$this->current_section = $single_section;
					} else {
						return $single_section;
					}

					break;
				}
			}

			if ($initial_section_id) {
				return $this->get_section_value()['sections'][0];
			}
		}

		return $this->current_section;
	}

	public function get_structure_for($args = []) {
		$args = wp_parse_args($args, [
			'id' => null,
			'name' => null,
			'mode' => 'placements',
			'items' => [],
			'settings' => []
		]);

		$args['items'] = wp_parse_args($args['items'], [
			'desktop' => [],
			'mobile' => []
		]);

		$args['items']['desktop'] = wp_parse_args($args['items']['desktop'], [
			'top-row' => [],
			'middle-row' => [],
			'bottom-row' => [],
			'offcanvas' => []
		]);

		$args['items']['mobile'] = wp_parse_args($args['items']['mobile'], [
			'top-row' => [],
			'middle-row' => [],
			'bottom-row' => [],
			'offcanvas' => [],
		]);

		$base = [
			'id' => $args['id'],
			'mode' => $args['mode'],
			'items' => [],
			'settings' => $args['settings']
		];

		if ($args['name']) {
			$base['name'] = $args['name'];
		}

		if ($args['mode'] === 'placements') {
			$base['desktop'] = [
				$this->get_bar_structure_for([
					'id' => 'top-row',
					'mode' => $args['mode'],
					'items' => $args['items']['desktop']['top-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'middle-row',
					'mode' => $args['mode'],
					'items' => $args['items']['desktop']['middle-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'bottom-row',
					'mode' => $args['mode'],
					'items' => $args['items']['desktop']['bottom-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'offcanvas',
					'mode' => $args['mode'],
					'has_secondary' => false,
					'items' => $args['items']['desktop']['offcanvas']
				]),
			];

			$base['mobile'] = [
				$this->get_bar_structure_for([
					'id' => 'top-row',
					'mode' => $args['mode'],
					'items' => $args['items']['mobile']['top-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'middle-row',
					'mode' => $args['mode'],
					'items' => $args['items']['mobile']['middle-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'bottom-row',
					'mode' => $args['mode'],
					'items' => $args['items']['mobile']['bottom-row']
				]),
				$this->get_bar_structure_for([
					'id' => 'offcanvas',
					'mode' => $args['mode'],
					'has_secondary' => false,
					'items' => $args['items']['mobile']['offcanvas']
				]),
			];
		}

		if ($args['mode'] === 'rows') {
			$base['desktop'] = [
				$this->get_bar_structure_for([
					'id' => 'top-row',
					'mode' => $args['mode']
				]),
				$this->get_bar_structure_for([
					'id' => 'middle-row',
					'mode' => $args['mode']
				]),
				$this->get_bar_structure_for([
					'id' => 'bottom-row',
					'mode' => $args['mode']
				]),
			];
		}

		return $base;
	}

	private function get_bar_structure_for($args = []) {
		$args = wp_parse_args($args, [
			'id' => null,
			'mode' => 'placements',
			'has_secondary' => true,
			'items' => []
		]);

		$args['items'] = wp_parse_args($args['items'], [
			'start' => [],
			'middle' => [],
			'end' => [],
			'start-middle' => [],
			'end-middle' => [],
		]);

		$placements = [
			['id' => 'start', 'items' => $args['items']['start']]
		];

		if ($args['has_secondary']) {
			$placements[] = ['id' => 'middle', 'items' => $args['items']['middle']];
			$placements[] = ['id' => 'end', 'items' => $args['items']['end']];

			$placements[] = ['id' => 'start-middle', 'items' => $args['items']['start-middle']];
			$placements[] = ['id' => 'end-middle', 'items' => $args['items']['end-middle']];
		}

		return array_merge([
			'id' => $args['id'],
		], (
			$args['mode'] === 'rows' ? [
				'row' => []
			] : ['placements' => $placements]
		));
	}

	public function enabled_on_this_page() {
		return blocksy_default_akg(
			'disable_header',
			blocksy_get_post_options(),
			'no'
		) === 'no';
	}

	public function render() {
		if (! $this->enabled_on_this_page()) {
			return '';
		}

		$renderer = new Blocksy_Header_Builder_Render();
		return $renderer->render();
	}

	public function get_section_value() {
		if (! $this->section_value || is_customize_preview()) {
			$this->section_value = get_theme_mod(
				'header_placements',
				$this->get_default_value()
			);
		}

		return $this->section_value;
	}

	public function translation_keys() {
		$render = new Blocksy_Header_Builder_Render();
		$sections = $this->get_section_value();

		$result = [];

		foreach ($sections['sections'] as $section) {
			foreach ($section['items'] as $item) {
				$nested_item = $render->get_item_config_for($item['id']);

				if (
					! isset($nested_item['config']['translation_keys'])
					||
					empty($nested_item['config']['translation_keys'])
				) {
					continue;
				}

				foreach ($nested_item['config']['translation_keys'] as $key) {
					if (! isset($item['values'][$key['key']])) {
						continue;
					}

					$key_prefix = 'header:' . $section['id'] . ':' . $item['id'] . ':' . $key['key'];

					if (isset($key['all_layers'])) {
						foreach ($item['values'][$key['key']] as $single_layer) {
							foreach ($key['all_layers'] as $layer_key) {
								if (! isset($single_layer[$layer_key])) {
									continue;
								}

								$result[] = array_merge($key, [
									'key' => $key_prefix . ':' . $single_layer['id'] . ':' . $layer_key,
									'value' => $single_layer[$layer_key]
								]);
							}
						}
					} else {
						$result[] = array_merge($key, [
							'key' => $key_prefix,
							'value' => $item['values'][$key['key']]
						]);
					}

				}
            }
        }

		return $result;
	}

	public function typography_keys() {
		$render = new Blocksy_Header_Builder_Render();
		$section = $render->get_current_section();

		$result = [];

		foreach ($section['items'] as $item) {
			$nested_item = $render->get_item_config_for($item['id']);

			if (
				! isset($nested_item['config']['typography_keys'])
				||
				empty($nested_item['config']['typography_keys'])
			) {
				continue;
			}

			$data = $render->get_item_data_for($item['id']);

			foreach ($nested_item['config']['typography_keys'] as $key) {
				$result[] = blocksy_akg($key, $data, []);
			}
		}

		return $result;
	}

	public function patch_value_for($processed_terms) {
		$current_value = get_theme_mod(
			'header_placements',
			$this->get_default_value()
		);

		foreach ($current_value['sections'] as $index => $header) {
			if (! isset($header['items'])) {
				continue;
			}

			foreach ($header['items'] as $item_index => $item) {
				if (! isset($item['values'])) {
					continue;
				}

				if (! isset($item['values']['menu'])) {
					continue;
				}

				if (! isset($processed_terms[$item['values']['menu']])) {
					continue;
				}

				$current_value['sections'][$index][
					'items'
				][$item_index]['values']['menu'] = $processed_terms[$item['values']['menu']];
			}
		}

		set_theme_mod('header_placements', $current_value);
	}

	public function get_filtered_section_id() {
		if (
			isset($this->get_section_value()['__forced_static_header__'])
			&&
			is_customize_preview()
		) {
			return $this->get_section_value()['__forced_static_header__'];
		}

		return apply_filters(
			'blocksy:header:current_section_id',
			'type-1',
			$this->get_section_value()
		);
	}
}
