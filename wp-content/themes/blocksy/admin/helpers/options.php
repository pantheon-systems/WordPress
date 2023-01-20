<?php

/**
 * Return the url to be used in image picker.
 *
 * @param string $path image name.
 */
if (! function_exists('blocksy_image_picker_url')) {
	function blocksy_image_picker_url($path) {
		return get_template_directory_uri() . '/static/images/' . $path;
	}
}

/**
 * Parse options.
 *
 * @param array $result The place to store result into.
 * @param array $options Proper options.
 * @param array $settings settings.
 * @param array $_recursion_data (private) for internal use.
 */
function blocksy_collect_options(
	&$result,
	&$options,
	$settings = [],
	$_recursion_data = []
) {
	static $default_settings = [
		/**
		 * If true:
		 * $result = array(
		 *   '(container|option):{id}' => array(
		 *      'id' => '{id}',
		 *      'level' => int, // from which nested level this option is
		 *      'group' => 'container|option',
		 *      'option' => array(...),
		 *   )
		 * )
		 *
		 * @type bool Wrap the result/collected options in arrays will useful info
		 *
		 * If false:
		 * $result = array(
		 *   '{id}' => array(...),
		 *   // Warning: There can be options and containers with the same id (array key will be replaced)
		 * )
		 */
		'info_wrapper' => false,

		/**
		 * For e.g. use 1 to collect only first level. 0 is for unlimited.
		 *
		 * @type int Nested options level limit.
		 */
		'limit_level' => 0,

		/**
		 * Empty array will skip all types
		 *
		 * @type false|array('option-type', ...)
		 */
		'limit_option_types' => false,
		'include_container_types' => true,
	];

	if (empty($options)) {
		return;
	}

	if (empty($_recursion_data)) {
		$settings = array_merge($default_settings, $settings);

		$_recursion_data = [
			'level' => 1,
		];
	}

	if (
		$settings['limit_level']
		&&
		$_recursion_data['level'] > $settings['limit_level']
	) {
		return;
	}

	foreach ($options as $option_id => &$option) {
		if (isset($option['options'])) { // this is a container.
			do {
				if ($settings['info_wrapper']) {
					if ($settings['include_container_types']) {
						$result['container:' . $option_id] = [
							'group' => 'container',
							'id' => $option_id,
							'option' => &$option,
							'level' => $_recursion_data['level'],
						];
					}
				} else {
					if ($settings['include_container_types']) {
						$result[$option_id] = &$option;
					}
				}
			} while (false);

			blocksy_collect_options(
				$result,
				$option['options'],
				$settings,
				array_merge(
					$_recursion_data,
					['level' => $_recursion_data['level'] + 1]
				)
			);
		} elseif (
			is_int($option_id)
			&&
			is_array($option)
			&&
			isset($options[0])
		) {
			blocksy_collect_options($result, $option, $settings, $_recursion_data);
		} elseif (isset($option['type'])) { // option.
			if (
				is_array($settings['limit_option_types'])
				&&
				! in_array($option['type'], $settings['limit_option_types'], true)
			) {
				continue;
			}

			if ($settings['info_wrapper']) {
				$result['option:' . $option_id] = [
					'group' => 'option',
					'id' => $option_id,
					'option' => &$option,
					'level' => $_recursion_data['level'],
				];
			} else {
				$result[$option_id] = &$option;
			}
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error(
				'Invalid option: ' . esc_html( $option_id ),
				E_USER_WARNING
			);
		}
	}
}

function blocksy_get_options($path, $pass_inside = [], $relative = true) {
	if ($relative) {
		$path = get_template_directory() . '/inc/options/' . $path . '.php';
	}

	if (! file_exists($path)) {
		return null;
	}

	return apply_filters('blocksy:options:retrieve', blocksy_akg(
		'options',
		blocksy_get_variables_from_file(
			$path,
			['options' => []],
			$pass_inside
		)
	), $path, $pass_inside);
}
