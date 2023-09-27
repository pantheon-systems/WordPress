<?php

function blocksy_assemble_selector($selector) {
	if (is_string($selector)) {
		return $selector;
	}

	if (! is_array($selector)) {
		throw new Error('$selector should be either string or array.');
	}

	return implode(' ', $selector);
}

function blocksy_mutate_selector($args = []) {
	$args = wp_parse_args(
		$args,
		[
			'selector' => null,
			// prefix | suffix | between | replace-last | el-prefix | el-suffix
			'operation' => 'between',
			'to_add' => ''
		]
	);

	if (! is_array($args['selector'])) {
		throw new Error('Only $selector as array can be mutated.');
	}

	if ($args['operation'] === 'between') {
		return array_merge(
			array_slice($args['selector'], 0, 1),
			[
				$args['to_add']
			],
			array_slice($args['selector'], 1)
		);
	}

	if ($args['operation'] === 'el-prefix' && count($args['selector']) > 1) {
		$args['selector'][1] = $args['to_add'] . $args['selector'][1];
		return $args['selector'];
	}

	if ($args['operation'] === 'el-suffix' && count($args['selector']) > 1) {
		$args['selector'][1] .= $args['to_add'];
		return $args['selector'];
	}

	if ($args['operation'] === 'container-suffix') {
		$args['selector'][0] .= $args['to_add'];
		return $args['selector'];
	}

	if ($args['operation'] === 'suffix') {
		$args['selector'][] = $args['to_add'];
		return $args['selector'];
	}

	if (
		$args['operation'] === 'replace-last'
		&&
		count($args['selector']) === 2
	) {
		$args['selector'][1] = $args['to_add'];

		return $args['selector'];
	}

	if ($args['operation'] === 'prefix') {
		array_unshift($args['selector'], $args['to_add']);
		return $args['selector'];
	}

	return $args['selector'];
}

function blocksy_get_source_for($prefix) {
	return [
		'strategy' => 'customizer',
		'prefix' => $prefix
	];
}

if (! function_exists('blocksy_prefix_selector')) {
	function blocksy_prefix_custom_selector($selector, $prefix_custom = '') {
		if (empty($prefix)) {
			return $selector;
		}

		$selector_for_prefix = 'body:not([data-prefix-custom*="' . $prefix . '"]) ';

		if (empty($selector)) {
			return trim($selector_for_prefix);
		}

		return $selector_for_prefix . $selector;
	}
}

if (! function_exists('blocksy_prefix_selector')) {
	function blocksy_prefix_selector($selector, $prefix = '') {
		if (empty($prefix)) {
			return $selector;
		}

		$selector_for_prefix = '[data-prefix="' . $prefix . '"] ';

		if (
			strpos($prefix, 'block') !== false
			&&
			$prefix !== 'ct_content_block_single'
			&&
			count(explode(':', $prefix)) > 1
		) {
			$selector_for_prefix = '[data-block*="' . explode(':', $prefix)[1] . '"]';
		}

		if (empty($selector)) {
			return trim($selector_for_prefix);
		}

		return $selector_for_prefix . $selector;
	}
}

if (! function_exists('blocksy_camel_case_prefix')) {
	function blocksy_camel_case_prefix($value, $prefix = '') {
		if (empty($prefix)) {
			return $value;
		}

		return $prefix . ucfirst($value);
	}
}
