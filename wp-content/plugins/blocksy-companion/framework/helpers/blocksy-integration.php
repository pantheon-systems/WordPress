<?php

function blc_call_fn($args = [], ...$params) {
	$args = wp_parse_args(
		$args,
		[
			'fn' => null,

			// string | null | array
			'default' => ''
		]
	);

	if (! $args['fn']) {
		throw new Error('$fn must be specified!');
	}

	if (! function_exists($args['fn'])) {
		return $args['default'];
	}

	return call_user_func($args['fn'], ...$params);
}

/**
 * Recursively find a key's value in array
 *
 * @param string       $keys 'a/b/c' path.
 * @param array|object $array_or_object array to extract from.
 * @param null|mixed   $default_value defualt value.
 *
 * @return null|mixed
 */
if (! function_exists('blocksy_akg')) {
	function blocksy_akg($keys, $array_or_object, $default_value = null) {
		if (! is_array($keys)) {
			$keys = explode('/', (string) $keys);
		}

		$array_or_object = $array_or_object;
		$key_or_property = array_shift($keys);

		if (is_null($key_or_property)) {
			return $default_value;
		}

		$is_object = is_object($array_or_object);

		if ($is_object) {
			if (! property_exists($array_or_object, $key_or_property)) {
				return $default_value;
			}
		} else {
			if (! is_array($array_or_object) || ! array_key_exists($key_or_property, $array_or_object)) {
				return $default_value;
			}
		}

		if (isset($keys[0])) { // not used count() for performance reasons.
			if ($is_object) {
				return blocksy_akg($keys, $array_or_object->{$key_or_property}, $default_value);
			} else {
				return blocksy_akg($keys, $array_or_object[$key_or_property], $default_value);
			}
		} else {
			if ($is_object) {
				return $array_or_object->{$key_or_property};
			} else {
				return $array_or_object[ $key_or_property ];
			}
		}
	}
}

/**
 * Generate a random ID.
 */
if (! function_exists('blocksy_rand_md5')) {
	function blocksy_rand_md5() {
		return md5(time() . '-' . uniqid(wp_rand(), true) . '-' . wp_rand());
	}
}

/**
 * Transform key-value pairs into ordered arrays.
 *
 * @param array $choices key-value pairs.
 */
if (! function_exists('blocksy_ordered_keys')) {
	function blocksy_ordered_keys($choices, $args = []) {
		if (isset($choices[0])) {
			return $choices;
		}

		$args = wp_parse_args(
			$args,
			[
				'additional' => []
			]
		);

		$result = [];

		foreach ($choices as $key => $val) {
			$result[] = array_merge([
				'key' => $key,
				'value' => $val,
			], $args['additional']);
		}

		return $result;
	}
}

/**
 * Extract a key from an array with defaults.
 *
 * @param string       $keys 'a/b/c' path.
 * @param array|object $array_or_object array to extract from.
 * @param null|mixed   $default_value defualt value.
 */
if (! function_exists('blocksy_default_akg')) {
	function blocksy_default_akg($keys, $array_or_object, $default_value) {
		return blocksy_akg($keys, $array_or_object, $default_value);
	}
}

/**
 * Generate html tag
 *
 * @param string      $tag Tag name.
 * @param array       $attr Tag attributes.
 * @param bool|string $end Append closing tag. Also accepts body content.
 *
 * @return string The tag's html
 */
if (! function_exists('blocksy_html_tag')) {
	function blocksy_html_tag($tag, $attr = [], $end = false) {
		$html = '<' . $tag . ' ' . blocksy_attr_to_html($attr);

		if (true === $end) {
			// <script></script>
			$html .= '></' . $tag . '>';
		} elseif (false === $end) {
			// <br/>
			$html .= '/>';
		} else {
			// <div>content</div>
			$html .= '>' . $end . '</' . $tag . '>';
		}

		return $html;
	}
}

/**
 * Generate attributes string for html tag
 *
 * @param array $attr_array array('href' => '/', 'title' => 'Test').
 *
 * @return string 'href="/" title="Test"'
 */
if (! function_exists('blocksy_attr_to_html')) {
	function blocksy_attr_to_html(array $attr_array) {
		$html_attr = '';

		foreach ($attr_array as $attr_name => $attr_val) {
			if (false === $attr_val) {
				continue;
			}

			$html_attr .= $attr_name . '="' . $attr_val . '" ';
		}

		return $html_attr;
	}
}
