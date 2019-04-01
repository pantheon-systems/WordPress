<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Code output
function crellySlider($alias) {
	echo getCrellySlider($alias);
}

function getCrellySlider($alias) {
	return CrellySliderFrontend::output($alias);
}

class CrellySliderFrontend {

	public static function setNotAdminJs() {
		add_action('wp_enqueue_scripts', 'CrellySliderFrontend::notAdminJs');
	}

	// Shortcode
	public static function shortcode($atts) {
		$a = shortcode_atts( array(
			'alias' => false,
		), $atts );

		if(! $a['alias']) {
			return __('You have to insert a valid alias in the shortcode', 'crelly-slider');
		}
		else {
			return CrellySliderFrontend::output($a['alias']);
		}
	}

	public static function addShortcode() {
		add_shortcode('crellyslider', array( __CLASS__, 'shortcode'));
	}

	public static function output($alias) {
		global $wpdb;

		// Check if the slider exists
		$slider = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'crellyslider_sliders WHERE alias = %s', esc_sql($alias)));
		if(! $slider) {
			return __('The slider hasn\'t been found', 'crelly-slider');
		}

		// Get the slider. Return if now() is not between from/to dates
		$slider = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_sliders WHERE NOW() BETWEEN fromDate AND toDate AND alias=%s', esc_sql($alias)));
		if(! $slider) {
			return '';
		}

		$slider_id = esc_sql($slider->id);
		$slides = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_slides WHERE draft = 0 AND slider_parent = %d ORDER BY position', $slider_id));

		$output = '';

		$output .= '<div style="display: none;" class="crellyslider-slider crellyslider-slider-' . esc_attr($slider->layout) . ' crellyslider-slider-' . esc_attr($alias) . '" id="crellyslider-' . esc_attr($slider_id) . '">' . "\n";
		$output .= '<ul>' . "\n";
		foreach($slides as $slide) {
			$background_type_image = $slide->background_type_image == 'undefined' || $slide->background_type_image == 'none' ? 'none;' : 'url(\'' . CrellySliderCommon::getURL($slide->background_type_image) . '\');';
			$output .= '<li' .  "\n" .
			'style="' . "\n" .
			'background-color: ' . esc_attr($slide->background_type_color) . ';' . "\n" .
			'background-image: ' . $background_type_image . "\n" .
			'background-position: ' . esc_attr($slide->background_propriety_position_x) . ' ' . esc_attr($slide->background_propriety_position_y) . ';' . "\n" .
			'background-repeat: ' . esc_attr($slide->background_repeat) . ';' . "\n" .
			'background-size: ' . esc_attr($slide->background_propriety_size) . ';' . "\n" .
			stripslashes($slide->custom_css) . "\n" .
			'"' . "\n" .

			'data-in="' . esc_attr($slide->data_in) . '"' . "\n" .
			'data-ease-in="' . esc_attr($slide->data_easeIn) . '"' . "\n" .
			'data-out="' . esc_attr($slide->data_out) . '"' . "\n" .
			'data-ease-out="' . esc_attr($slide->data_easeOut) . '"' . "\n" .
			'data-time="' . esc_attr($slide->data_time) . '"' . "\n" .
			'>' . "\n";

			if($slide->link != '') {
				if($slide->link_new_tab) {
					$output .= '<a class="cs-background-link" target="_blank" href="' . stripslashes($slide->link) . '"></a>';
				}
				else {
					$output .= '<a class="cs-background-link" href="' . stripslashes($slide->link) . '"></a>';
				}
			}

			$slide_parent = esc_sql($slide->position);
			$elements = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'crellyslider_elements WHERE slider_parent = %d AND slide_parent = %d', $slider_id, $slide_parent));

			foreach($elements as $element) {
				if($element->link != '') {
					$target = $element->link_new_tab == 1 ? 'target="_blank"' : '';

					$output .= '<a' . "\n" .
					'data-delay="' . esc_attr($element->data_delay) . '"' . "\n" .
					'data-ease-in="' . esc_attr($element->data_easeIn) . '"' . "\n" .
					'data-ease-out="' . esc_attr($element->data_easeOut) . '"' . "\n" .
					'data-in="' . esc_attr($element->data_in) . '"' . "\n" .
					'data-out="' . esc_attr($element->data_out) . '"' . "\n" .
					'data-ignore-ease-out="' . esc_attr($element->data_ignoreEaseOut) . '"' . "\n" .
					'data-top="' . esc_attr($element->data_top) . '"' . "\n" .
					'data-left="' . esc_attr($element->data_left) . '"' . "\n" .
					'data-time="' . esc_attr($element->data_time) . '"' . "\n" .
					'href="' . stripslashes($element->link) . '"' . "\n" .
					$target . "\n" .
					'style="' .
					'z-index: ' . $element->z_index . ';' . "\n" .
					'">' .  "\n";
				}

				switch($element->type) {
					case 'text':
						$output .= '<div' . "\n" .
						'class="' . esc_attr($element->custom_css_classes) . '"' . "\n" .
						'style="';
						if($element->link == '') {
							$output .= 'z-index: ' . esc_attr($element->z_index) . ';' . "\n";
						}
						$output .= stripslashes($element->custom_css) . "\n" .
						'"' .  "\n";
						if($element->link == '') {
							$output .= 'data-delay="' . esc_attr($element->data_delay) . '"' . "\n" .
							'data-ease-in="' . esc_attr($element->data_easeIn) . '"' . "\n" .
							'data-ease-out="' . esc_attr($element->data_easeOut) . '"' . "\n" .
							'data-in="' . esc_attr($element->data_in) . '"' . "\n" .
							'data-out="' . esc_attr($element->data_out) . '"' . "\n" .
							'data-ignore-ease-out="' . esc_attr($element->data_ignoreEaseOut) . '"' . "\n" .
							'data-top="' . esc_attr($element->data_top) . '"' . "\n" .
							'data-left="' . esc_attr($element->data_left) . '"' . "\n" .
							'data-time="' . esc_attr($element->data_time) . '"' . "\n";
						}
						$output .= '>' .
						stripslashes($element->inner_html) .
						'</div>' . "\n";
					break;

					case 'image':
						$output .= '<img' . "\n" .
						'class="' . esc_attr($element->custom_css_classes) . '"' . "\n" .
						'src="' . CrellySliderCommon::getURL($element->image_src) . '"' . "\n" .
						'alt="' . esc_attr($element->image_alt) . '"' . "\n" .
						'style="' . "\n";
						if($element->link == '') {
							$output .= 'z-index: ' . esc_attr($element->z_index) . ';' . "\n";
						}
						$output .= stripslashes($element->custom_css) . "\n" .
						'"' . "\n";
						if($element->link == '') {
							$output .= 'data-delay="' . esc_attr($element->data_delay) . '"' . "\n" .
							'data-ease-in="' . esc_attr($element->data_easeIn) . '"' . "\n" .
							'data-ease-out="' . esc_attr($element->data_easeOut) . '"' . "\n" .
							'data-in="' . esc_attr($element->data_in) . '"' . "\n" .
							'data-out="' . esc_attr($element->data_out) . '"' . "\n" .
							'data-ignore-ease-out="' . esc_attr($element->data_ignoreEaseOut) . '"' . "\n" .
							'data-top="' . esc_attr($element->data_top) . '"' . "\n" .
							'data-left="' . esc_attr($element->data_left) . '"' . "\n" .
							'data-time="' . esc_attr($element->data_time) . '"' . "\n";
						}
						$output .= '/>' . "\n";
					break;

					case 'youtube_video':
						$output .= '<iframe frameborder="0" type="text/html" width="560" height="315"' . "\n" .
						'class="cs-yt-iframe ' . esc_attr($element->custom_css_classes) . '"' . "\n" .
						'src="' . esc_url('https://www.youtube.com/embed/' . $element->video_id . '?enablejsapi=1') . '"' . "\n" .
						'data-autoplay="' . $element->video_autoplay . '"' . "\n" .
						'data-loop="' . $element->video_loop . '"' . "\n" .
						'style="' . "\n" .
						'z-index: ' . $element->z_index . ';' . "\n" .
						stripslashes($element->custom_css) . "\n" .
						'"' . "\n" .
						'data-delay="' . $element->data_delay . '"' . "\n" .
						'data-ease-in="' . $element->data_easeIn . '"' . "\n" .
						'data-ease-out="' . $element->data_easeOut . '"' . "\n" .
						'data-in="' . $element->data_in . '"' . "\n" .
						'data-out="' . $element->data_out . '"' . "\n" .
						'data-ignore-ease-out="' . $element->data_ignoreEaseOut . '"' . "\n" .
						'data-top="' . $element->data_top . '"' . "\n" .
						'data-left="' . $element->data_left . '"' . "\n" .
						'data-time="' . $element->data_time . '"' . "\n" .
						'></iframe>' . "\n";
					break;

					case 'vimeo_video':
						$output .= '<iframe frameborder="0" width="560" height="315"' . "\n" .
						'class="cs-vimeo-iframe ' . esc_attr($element->custom_css_classes) . '"' . "\n" .
						'src="' . esc_url('https://player.vimeo.com/video/' . $element->video_id . '?api=1') . '"' . "\n" .
						'data-autoplay="' . esc_attr($element->video_autoplay) . '"' . "\n" .
						'data-loop="' . esc_attr($element->video_loop) . '"' . "\n" .
						'style="' . "\n" .
						'z-index: ' . esc_attr($element->z_index) . ';' . "\n" .
						stripslashes($element->custom_css) . "\n" .
						'"' . "\n" .
						'data-delay="' . esc_attr($element->data_delay) . '"' . "\n" .
						'data-ease-in="' . esc_attr($element->data_easeIn) . '"' . "\n" .
						'data-ease-out="' . esc_attr($element->data_easeOut) . '"' . "\n" .
						'data-in="' . esc_attr($element->data_in) . '"' . "\n" .
						'data-out="' . esc_attr($element->data_out) . '"' . "\n" .
						'data-ignore-ease-out="' . esc_attr($element->data_ignoreEaseOut) . '"' . "\n" .
						'data-top="' . esc_attr($element->data_top) . '"' . "\n" .
						'data-left="' . esc_attr($element->data_left) . '"' . "\n" .
						'data-time="' . esc_attr($element->data_time) . '"' . "\n" .
						'></iframe>' . "\n";
					break;
				}

				if($element->link != '') {
					$output .= '</a>' . "\n";
				}
			}

			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		$output .= '</div>' . "\n";

		$output .= '<script type="text/javascript">' . "\n";
		$output .= '(function($) {' . "\n";
		$output .= '$(document).ready(function() {' . "\n";
		$output .= '$("#crellyslider-' . $slider_id  . '").crellySlider({' . "\n";
		$output .= 'layout: \'' . $slider->layout . '\',' . "\n";
		$output .= 'responsive: ' . $slider->responsive . ',' . "\n";
		$output .= 'startWidth: ' . $slider->startWidth . ',' . "\n";
		$output .= 'startHeight: ' . $slider->startHeight . ',' . "\n";
		$output .= 'automaticSlide: ' . $slider->automaticSlide . ',' . "\n";
		$output .= 'showControls: ' . $slider->showControls . ',' . "\n";
		$output .= 'showNavigation: ' . $slider->showNavigation . ',' . "\n";
		$output .= 'enableSwipe: ' . $slider->enableSwipe . ',' . "\n";
		$output .= 'showProgressBar: ' . $slider->showProgressBar . ',' . "\n";
		$output .= 'pauseOnHover: ' . $slider->pauseOnHover . ',' . "\n";
		if($slider->randomOrder != NULL) {
			$output .= 'randomOrder: ' . $slider->randomOrder . ',' . "\n";
		}
		if($slider->startFromSlide != NULL) {
			$output .= 'startFromSlide: ' . $slider->startFromSlide . ',' . "\n";
		}
		$output .= stripslashes($slider->callbacks) . "\n";
		$output .= '});' . "\n";
		$output .= '});' . "\n";
		$output .= '})(jQuery);' . "\n";
		$output .= '</script>' . "\n";

		return $output;
	}

}
