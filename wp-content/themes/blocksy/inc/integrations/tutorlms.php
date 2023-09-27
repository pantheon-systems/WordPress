<?php

add_filter('llms_get_theme_default_sidebar', function ($id) {
	return 'sidebar-1';
});

if (! function_exists('blocksy_tutor_lms_content_close')) {
	function blocksy_tutor_lms_content_close() {
		if (! is_singular()) {
			echo '</article>';
			get_sidebar();
		}

		echo '</div>';
	}
}

if (! function_exists('blocksy_tutor_lms_hero_type_1')) {
	function blocksy_tutor_lms_hero_type_1($result) {
		return blocksy_output_hero_section([
			'type' => 'type-1',
			'elements' => str_replace(
				'tutor-course-details-header tutor-mb-44',
				'tutor-course-details-header entry-header',
				$result
			)
		]);
	}
}

if (! function_exists('blocksy_tutor_lms_course_content_open')) {
	function blocksy_tutor_lms_course_content_open() {
		$page_structure = blocksy_get_page_structure();

		$attr = [
			'class' => 'ct-container-full'
		];

		if ($page_structure === 'none' || blocksy_post_uses_vc()) {
			$attr['class'] = 'ct-container';

			if ($page_structure === 'narrow') {
				$attr['class'] = 'ct-container-narrow';
			}
		} else {
			$attr['data-content'] = $page_structure;
		}

		$attr = array_merge($attr, blocksy_sidebar_position_attr([
			'attr_id' => 'data-tutor-sidebar',
			'array' => true
		]));

		$attr = array_merge($attr, blocksy_get_v_spacing([
			'array' => true
		]));

		/**
		 * Note to code reviewers: This line doesn't need to be escaped.
		 * Function blocksy_output_hero_section() used here escapes the value properly.
		 */
		if (apply_filters('blocksy:single:has-default-hero', true)) {
			$resulting_hero = blocksy_output_hero_section([
				'type' => 'type-2'
			]);

			if (! empty($resulting_hero)) {
				echo $resulting_hero;

				add_filter('tutor_course/single/lead_info', function ($res) {
					return '';
				});

				add_filter('tutor_course/single/enrolled/lead_info', function ($res) {
					return '';
				});
			} else {
				add_filter(
					'tutor_course/single/lead_info',
					'blocksy_tutor_lms_hero_type_1'
				);

				add_filter(
					'tutor_course/single/enrolled/lead_info',
					'blocksy_tutor_lms_hero_type_1'
				);
			}
		}

		echo '<div ' . blocksy_attr_to_html($attr) . '>';
	}
}

if (! function_exists('blocksy_tutor_lms_content_open')) {
	function blocksy_tutor_lms_content_open() {
		$page_structure = blocksy_get_page_structure();

		$attr = [
			'class' => 'ct-container-full'
		];

		if ($page_structure === 'none' || blocksy_post_uses_vc()) {
			$attr['class'] = 'ct-container';

			if ($page_structure === 'narrow') {
				$attr['class'] = 'ct-container-narrow';
			}
		} else {
			$attr['data-content'] = $page_structure;
		}

		$attr = array_merge($attr, blocksy_sidebar_position_attr([
			'array' => true
		]));

		$attr = array_merge($attr, blocksy_get_v_spacing([
			'array' => true
		]));

		if (apply_filters('blocksy:single:has-default-hero', true)) {
			echo blocksy_output_hero_section([
				'type' => 'type-2'
			]);
		}

		echo '<div ' . blocksy_attr_to_html($attr) . '>';

		echo '<article>';
		echo blocksy_output_hero_section([
			'type' => 'type-1'
		]);
	}
}

add_action('tutor_dashboard/before/wrap', 'blocksy_tutor_lms_content_open');
add_action('tutor_dashboard/after/wrap', 'blocksy_tutor_lms_content_close');
add_action('tutor_course/archive/before/wrap', 'blocksy_tutor_lms_content_open');
add_action('tutor_course/archive/after/wrap', 'blocksy_tutor_lms_content_close');
add_action('tutor_student/before/wrap', 'blocksy_tutor_lms_content_open');
add_action('tutor_student/after/wrap', 'blocksy_tutor_lms_content_close');

add_action('tutor_course/single/before/wrap', 'blocksy_tutor_lms_course_content_open');
add_action('tutor_course/single/after/wrap', 'blocksy_tutor_lms_content_close');
add_action('tutor_course/single/enrolled/before/wrap', 'blocksy_tutor_lms_course_content_open');
add_action('tutor_course/single/enrolled/after/wrap', 'blocksy_tutor_lms_content_close');

add_action('wp_enqueue_scripts', function () {
	if (! function_exists('tutor_utils')) {
		return;
	}

	$is_script_debug = tutor_utils()->is_script_debug();
	$suffix = $is_script_debug ? '' : '.min';

	if (tutor_utils()->get_option('load_tutor_css')){
		wp_enqueue_style(
			'tutor-frontend',
			tutor()->url . "assets/css/tutor-front{$suffix}.css",
			array(),
			tutor()->version
		);
	}
}, 5);

