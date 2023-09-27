<?php

function blocksy_safe_antispambot($string_with_email) {
	$has_mail_to_prefix = strpos($string_with_email, 'mailto:') !== false;

	$result = antispambot(str_replace(
		'mailto:',
		'',
		$string_with_email
	));

	if ($has_mail_to_prefix) {
		$result = 'mailto:' . $result;
	}

	return $result;
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
