<?php

if (!defined('ABSPATH')) die('No direct access allowed');
/**
 * Class for guided tour
 */
Class MetaSlider_Tour {

	/**
	 * The plugin object
	 *
	 * @var array
	 */
	protected $plugin;

	/**
	 * Sets up the notices, security and loads assets for the admin page
	 *
	 * @param array  $plugin Plugin details
	 * @param string $page   Tour page
	 */
	public function __construct($plugin, $page) {
		$this->plugin = $plugin;
		add_action('load-' . $page, array($this, 'load_tour'));
		add_action('wp_ajax_set_tour_status', array($this, 'handle_ajax'));
		add_action('wp_ajax_reset_tour_status', array($this, 'handle_ajax'));
	}

	/**
	 * Loads in tour assets
	 */
	public function load_tour() {
		wp_enqueue_script('metaslider-tether-js', METASLIDER_ADMIN_URL . 'assets/tether/tether.min.js', METASLIDER_VERSION, true);
		wp_enqueue_script('metaslider-shepherd-js', METASLIDER_ADMIN_URL . 'assets/tether-shepherd/shepherd.min.js', array('metaslider-tether-js'), METASLIDER_VERSION, true);
		wp_enqueue_style('metaslider-shepherd-css', METASLIDER_ADMIN_URL . 'assets/tether-shepherd/shepherd-theme-arrows.css', false, METASLIDER_VERSION);

		wp_register_script('metaslider-tour-js', METASLIDER_ADMIN_URL . 'assets/js/tour.js', array('metaslider-tether-js'), METASLIDER_VERSION, true);
		wp_localize_script('metaslider-tour-js', 'metaslider_tour', array(
			'no_slideshows' => array(
				'show' => ! (bool) count($this->plugin->all_meta_sliders()),
				'title' => __("Welcome", "ml-slider"),
				'message' => __("Thanks for using the MetaSlider WordPress plugin. It looks like you don’t have any slideshows yet! To get started, click above to add your first one.", "ml-slider")
			),
			'main_tour' => array(
				'show' => (bool) count($this->plugin->all_meta_sliders()) && ! (bool) get_option('metaslider_tour_cancelled_on'),
				'nonce' => wp_create_nonce('metaslider_tour_nonce'),
				// 'has_slides' => ms
				'learn_more_language' => __('Learn More', 'ml-slider'),
				'next_language' => __('Next', 'ml-slider'),
				'skip_language' => __('Skip this step', 'ml-slider'),
				'upgrade_link' => metaslider_get_upgrade_link(),
				'is_pro' => metaslider_pro_is_active(),
				'step1' => array(
					'title' => __("Add A Slide", "ml-slider"),
					'message' => __("Congratulations! Now that you've created a slideshow, click here to add a slide.", "ml-slider")
				),
				'step2a' => array(
					'title' => __("Select Slide Type", "ml-slider"),
					'message' => metaslider_pro_is_active() ? 'Thanks for activating the Add-on Pack! Premium users can choose from any of these slide types' : __("Premium users that have the Add-on Pack activated can access additional slide types!", "ml-slider")
				),
				'step2b' => array(
					'title' => __("Select Media", "ml-slider"),
					'message' => __("After you have selected your media from the left, click below to continue.", "ml-slider")
				),
				'step3' => array(
					'title' => __("Preview Slideshow", "ml-slider"),
					'message' => __("Now that you have some slides set, you can preview your slideshow by clicking here.", "ml-slider")
				),
				'step4' => array(
					'title' => __("Adjust Settings", "ml-slider"),
					'message' => __("If you need to adjust the settings for this slideshow, you may do so here.", "ml-slider")
				),
				'step5' => array(
					'title' => __("Shortcode Usage", "ml-slider"),
					'message' => __("You are all set! If you want to add the slideshow to a post, you may use this shortcode.", "ml-slider"),
					'button' => __('Finish')
				),
				'final_ad' => array(
					'title' => __("Congratulations!", "ml-slider"),
					'message' => __("You've completed the tour and are ready to add great slideshows to your website. Don't forget, if you want your slideshows to really stand out, pick up the add-on pack today. ", "ml-slider"),
					'button' => __('Finish')
				)
			)
		));
		wp_enqueue_script('metaslider-tour-js');
	}

	/**
	 * Removes the tour status so the tour can be seen again
	 *
	 * @return bool|WP_Error The Boolean should be true 
	 */
	public function reset_tour_status() {

		// If the option isn't set, the tour hasn't been cancelled
		if (!get_option('metaslider_tour_cancelled_on')) {
			return 'The tour is still active. Everything should be ok.';
		}

		$result = delete_option('metaslider_tour_cancelled_on');
		return $result ? 'The tour status was successfully reset' : new WP_Error('update_failed', 'The attempt to update the tour option failed.', array('status' => 409));
	}

	/**
	 * Updates the stored value for which step the tour ended on
     *
	 * @param object $request - the http $_REQUEST obj
	 * @return bool|WP_Error The Boolean should be true 
	 */
	public function set_tour_status($request) {
		$result = update_option('metaslider_tour_cancelled_on', $request['current_step']);
		return $result ? 'The tour status was successfully updated' : new WP_Error('update_failed', 'The attempt to update the tour option failed.', array('status' => 409));
	}

	/**
     * Handles AJAX calls
     *
     * @return String - (JSON) Sends a success response unless an error is encountered
     */
	public function handle_ajax() {
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_tour_nonce')) {
			return wp_send_json_error(array(
					'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
			), 401);
		}

		$method = str_replace('ajax_', '', $_POST['action']);
		if (!method_exists($this, $method)) {
			return wp_send_json_error(array(
					'message' => __('This method does not exist. Please refresh the page and try again.', 'ml-slider')
			), 401);
		}

		// Call the dynamic method
		$result = $this->{$method}($_REQUEST);

		if (is_wp_error($result)) {
			return wp_send_json_error(array(
					'message' => $result->get_error_message()
			), 409);
		}

		return wp_send_json_success(array(
			'message' => $result,
		), 200);
	}
}
