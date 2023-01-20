<?php

class Blocksy_Footer_Builder {
	private $default_value = null;

	private $section_value = null;
	private $current_section = null;

	public function get_default_value() {
		if ($this->default_value) {
			return $this->default_value;
		}

		$this->default_value = [
			'current_section' => 'type-1',
			'sections' => [
				$this->get_structure_for([
					'id' => 'type-1',
					'rows' => [
						'top-row' => [
							'columns' => [
								[],
								[]
							]
						],

						'middle-row' => [
							'columns' => [
								[],
								[],
								[]
							]
						],

						'bottom-row' => [
							'columns' => [
								['copyright']
							]
						],
					]
				]),

				$this->get_structure_for([
					'id' => 'type-2',
					'rows' => [
						'top-row' => [
							'columns' => [
								[],
								[]
							]
						],

						'middle-row' => [
							'columns' => [
								[],
								[],
								[],
								[]
							]
						],

						'bottom-row' => [
							'columns' => [
								['copyright']
							]
						],
					]
				])
			]
		];

		return $this->default_value;
	}

	public function enabled_on_this_page() {
		return blocksy_default_akg(
			'disable_footer',
			blocksy_get_post_options(),
			'no'
		) === 'no';
	}

	public function translation_keys() {
		$render = new Blocksy_Footer_Builder_Render();
		$sections = $render->get_section_value();

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

					$result[] = array_merge($key, [
						'key' => 'footer:' . $section['id'] . ':' . $item['id'] . ':' . $key['key'],
						'value' => $item['values'][$key['key']]
					]);
				}
			}
		}

		return $result;
	}

	public function typography_keys() {
		$render = new Blocksy_Footer_Builder_Render();
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

	public function render() {
		if (! $this->enabled_on_this_page()) {
			return '';
		}

		$render = new Blocksy_Footer_Builder_Render();
		return $render->render();
	}

	public function get_structure_for($args = []) {
		$args = wp_parse_args($args, [
			'id' => null,
			'mode' => 'columns',
			'rows' => []
		]);

		$args['rows'] = wp_parse_args($args['rows'], [
			'top-row' => [],
			'middle-row' => [],
			'bottom-row' => [],
		]);

		$base = [
			'id' => $args['id'],
			'mode' => $args['mode'],
			'rows' => [
				$this->get_bar_structure_for(array_merge([
					'id' => 'top-row',
					'mode' => $args['mode'],
				], $args['rows']['top-row'])),
				$this->get_bar_structure_for(array_merge([
					'id' => 'middle-row',
					'mode' => $args['mode']
				], $args['rows']['middle-row'])),
				$this->get_bar_structure_for(array_merge([
					'id' => 'bottom-row',
					'mode' => $args['mode']
				], $args['rows']['bottom-row'])),
			],
			'items' => [],
			'settings' => []
		];

		return $base;
	}

	private function get_bar_structure_for($args = []) {
		$args = wp_parse_args($args, [
			'id' => null,
			'mode' => 'columns',
			'columns' => [
				/**
				 * We always have one column available
				 */
				[],
				[],
				[]
			]
		]);

		return array_merge([
			'id' => $args['id'],
			'columns' => $args['columns']
		]);
	}

	public function get_section_value() {
		if (! $this->section_value || is_customize_preview()) {
			$this->section_value = get_theme_mod(
				'footer_placements',
				$this->get_default_value()
			);
		}

		return $this->section_value;
	}

	public function get_current_section_id() {
		return $this->get_current_section()['id'];
	}

	public function get_current_section() {
		if (! $this->current_section) {
			$this->current_section = $this->get_section_value()['sections'][0];

			foreach ($this->get_section_value()['sections'] as $single_section) {
				if ($single_section['id'] === $this->get_filtered_section_id()) {
					$this->current_section = $single_section;
					break;
				}
			}
		}

		return $this->current_section;
	}

	private function get_filtered_section_id() {
		if (
			isset($this->get_section_value()['__forced_static_footer__'])
			&&
			is_customize_preview()
		) {
			return $this->get_section_value()['__forced_static_footer__'];
		}

		return apply_filters(
			'blocksy:footer:current_section_id',
			'type-1',
			$this->get_section_value()
		);
	}

	public function patch_value_for($processed_terms) {
		$current_value = get_theme_mod(
			'footer_placements',
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

		set_theme_mod('footer_placements', $current_value);
	}
}

