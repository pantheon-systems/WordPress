<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CrellySliderCommon {
	// Includes CSS and JavaScript
	public static function enqueues() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		
		if(CS_DEBUG) {
			wp_enqueue_script('jquery.crellyslider', CS_PLUGIN_URL . '/js/jquery.crellyslider.js', array(), CS_VERSION, false);
			wp_enqueue_style('crellyslider', CS_PLUGIN_URL . '/css/crellyslider.css', array(), CS_VERSION);
		}
		else {
			wp_enqueue_script('jquery.crellyslider.min', CS_PLUGIN_URL . '/js/jquery.crellyslider.min.js', array(), CS_VERSION, false);
			wp_enqueue_style('crellyslider.min', CS_PLUGIN_URL . '/css/crellyslider.min.css', array(), CS_VERSION);
		}
	}

	public static function setEnqueues() {
		add_action('wp_enqueue_scripts', 'CrellySliderCommon::enqueues');
		add_action('admin_enqueue_scripts', 'CrellySliderCommon::enqueues');
	}

	public static function loadPluginTextDomain() {
		add_action('plugins_loaded', 'CrellySliderCommon::textDomain');
	}

	// Returns true if a slider exists into the database. False if it doesn't
	public static function sliderExists($id) {
		global $wpdb;
		$slider = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'crellyslider_sliders WHERE id = %d', esc_sql($id)));
		if($slider != NULL) {
			return true;
		}
		return false;
	}

	// Returns the correct URL of an attachment
	public static function getURL($attachment_url) {
		// If the attachment ID is provided, get the URL
		if(is_numeric($attachment_url)) {
			return wp_get_attachment_url(intval($attachment_url));
		}

		// If a URL is provided, return the filtered URL
		if($attachment_url != 'none' && $attachment_url != 'undefined' && $attachment_url != '') {
			global $wpdb;
			$attachment_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s'", $attachment_url));

			if($attachment_id == NULL) {
				return $attachment_url;
			}

			$ret = wp_get_attachment_url($attachment_id);

			if($ret == false) {
				return $attachment_url;
			}

			return $ret;
		}

		// If something else is provided, do not touch it
		return $attachment_url;
	}
}
?>
