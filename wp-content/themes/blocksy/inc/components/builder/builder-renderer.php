<?php

abstract class Blocksy_Builder_Render {
	abstract public function get_section_type();

	private $current_section_id = null;

	public function __construct($args = []) {
		$args = wp_parse_args($args, [
			'current_section_id' => null
		]);

		$this->current_section_id = $args['current_section_id'];
	}

	public function get_section_value() {
		if ($this->get_section_type() === 'header') {
			return blocksy_manager()->header_builder->get_section_value();
		}

		return blocksy_manager()->footer_builder->get_section_value();
	}

	public function get_current_section_id() {
		if ($this->current_section_id) {
			return $this->current_section_id;
		}

		if ($this->get_section_type() === 'header') {
			return blocksy_manager()->header_builder->get_current_section_id();
		}

		return blocksy_manager()->footer_builder->get_current_section_id();
	}

	public function get_current_section() {
		if ($this->current_section_id) {
			foreach ($this->get_section_value()['sections'] as $current_section) {
				if ($current_section['id'] === $this->current_section_id) {
					return $current_section;
				}
			}

			return $this->get_section_value()['sections'][0];
		}

		if ($this->get_section_type() === 'header') {
			return blocksy_manager()->header_builder->get_current_section();
		}

		return blocksy_manager()->footer_builder->get_current_section();
	}

	public function get_original_id($id) {
		return explode('~', $id)[0];
	}

	public function get_root_selector($item = null) {
		$selector = '';

		if ($item) {
			$selector = '[data-id="' . $this->shorten_id($item['id']) . '"]';

			if ($item['id'] === 'socials') {
				$selector .= '.ct-' . $this->get_section_type() . '-socials';
			}

			if (in_array($item['id'], ['middle-row', 'top-row', 'bottom-row'])) {
				$selector = '[data-row*="' . str_replace('-row', '', $item['id']) . '"]';
			}

			if ($item['id'] === 'offcanvas') {
				$selector = '#offcanvas';
			}
		}

		$name = $this->get_section_type();

		$header_prefix =
			'[data-' . $this->get_section_type() . '*="' . $this->get_short_section_id() . '"]';

		if (
			$item
			&&
			in_array($item['id'], [
				'middle-row', 'top-row', 'bottom-row',
				'menu',
				'menu-secondary',
				'menu-tertiary',
				'logo',
				'language-switcher',
				'button',
				'text',
				'search-input',
				'contacts',
				'widget-area-1',
				'widget-area-2',
				'widget-area-3',
				'widget-area-4'
			])
		) {
			if ($this->get_section_type() === 'header') {
				$header_prefix .= ' .ct-header';
			}

			if ($this->get_section_type() === 'footer') {
				$header_prefix .= ' .ct-footer';
			}
		}

		if (empty($selector)) {
			return [$header_prefix];
		}

		return [$header_prefix, $selector];
	}

	public function get_column_selector($item) {
		$result = $this->get_original_id($item['id']);

		if (
			$this->get_original_id(
				$item['id']
			) !== $this->shorten_id($item['id'])
		) {
			$result .= ':' . $this->shorten_id($item['id']);
		}

		return '[data-column="' . $result . '"]';
	}

	public function get_short_section_id() {
		$id = $this->get_current_section_id();

		return substr(str_replace(
			'ct-custom-',
			'',
			$id
		), 0, 6);
	}

	public function shorten_id($id) {
		$ids = explode('~', $id);

		if (count($ids) === 1) {
			return $ids[0];
		}

		return substr($ids[1], 0, 6);
	}

	public function is_custom_id($id) {
		return count(explode('~', $id)) > 1;
	}

	public function get_item_config_for($id) {
		$id = $this->get_original_id($id);

		$registered_items = blocksy_manager()
			->builder
			->get_registered_items_by($this->get_section_type());

		foreach ($registered_items as $single_item) {
			if ($single_item['id'] === $id) {
				return $single_item;
			}
		}

		return [];
	}

	public function get_item_data_for($id) {
		$section = $this->get_section_value()['sections'][0];

		foreach ($this->get_section_value()['sections'] as $single_section) {
			if ($single_section['id'] === $this->get_current_section_id()) {
				$section = $single_section;
			}
		}

		foreach ($section['items'] as $single_item) {
			if (
				$single_item['id'] === $id
				&&
				isset($single_item['values'])
				&&
				is_array($single_item['values'])
			) {
				return $single_item['values'];
			}
		}

		return [];
	}

	public function get_customizer_location_for($id) {
		return $this->get_section_type() . ':builder_panel_' . $id;
	}
}

