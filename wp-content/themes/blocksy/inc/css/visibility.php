<?php
/**
 * Visibility helpers
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

/**
 * Generate visibility classes
 *
 * @param string $data Devices state.
 */
if (! function_exists('blocksy_visibility_classes')) {
	function blocksy_visibility_classes($data) {
		$classes = [];

		if (isset($data['mobile']) && !$data['mobile']) {
			$classes[] = 'ct-hidden-sm';
		}

		if (isset($data['tablet']) && !$data['tablet']) {
			$classes[] = 'ct-hidden-md';
		}

		if (isset($data['desktop']) && !$data['desktop']) {
			$classes[] = 'ct-hidden-lg';
		}

		return implode(' ', $classes);
	}
}

if (! function_exists('blocksy_some_device')) {
	function blocksy_some_device($data, $value = null) {
		$data = blocksy_expand_responsive_value($data);

		if ($value) {
			return (
				isset($data['mobile']) && $data['mobile'] === $value
				||
				isset($data['tablet']) && $data['tablet'] === $value
				||
				isset($data['desktop']) && $data['desktop'] === $value
			);
		}

		return (
			isset($data['mobile']) && $data['mobile']
			||
			isset($data['tablet']) && $data['tablet']
			||
			isset($data['desktop']) && $data['desktop']
		);
	}
}

if (! function_exists('blocksy_output_responsive_switch')) {
	function blocksy_output_responsive_switch($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'css' => null,
				'tablet_css' => null,
				'mobile_css' => null,
				'value' => null,
				'selector' => '',

				'on' => 'block',
				'off' => 'none',

				'variable' => 'visibility',

				// all_enabled | all_disabled
				'skip_when' => 'all_enabled'
			]
		);

		blocksy_assert_args(
			$args,
			['css', 'tablet_css', 'mobile_css', 'selector', 'value']
		);

		$all_enabled = (
			isset($args['value']['mobile']) && $args['value']['mobile']
			&&
			isset($args['value']['tablet']) && $args['value']['tablet']
			&&
			isset($args['value']['desktop']) && $args['value']['desktop']
		);

		$all_disabled = (
			isset($args['value']['mobile']) && !$args['value']['mobile']
			&&
			isset($args['value']['tablet']) && !$args['value']['tablet']
			&&
			isset($args['value']['desktop']) && !$args['value']['desktop']
		);

		if ($all_enabled && $args['skip_when'] === 'all_enabled') {
			return;
		}

		if ($all_disabled && $args['skip_when'] === 'all_disabled') {
			return;
		}

		blocksy_output_css_vars([
			'css' => $args['css'],
			'tablet_css' => $args['tablet_css'],
			'mobile_css' => $args['mobile_css'],

			'selector' => $args['selector'],
			'responsive' => true,
			'variableName' => $args['variable'],

			'value' => [
				'desktop' => (
					isset($args['value']['desktop'])
					&&
					!$args['value']['desktop']
				) ? $args['off'] : $args['on'],

				'tablet' => (
					isset($args['value']['tablet'])
					&&
					!$args['value']['tablet']
				) ? $args['off'] : $args['on'],

				'mobile' => (
					isset($args['value']['mobile'])
					&&
					!$args['value']['mobile']
				) ? $args['off'] : $args['on'],
			]
		]);
	}
}

