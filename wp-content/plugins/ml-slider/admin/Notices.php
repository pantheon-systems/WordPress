<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('Updraft_Notices_1_0')) require_once(METASLIDER_PATH.'admin/lib/Updraft_Notices.php');

/**
 * Meta Slider notices
 */
class MetaSlider_Notices extends Updraft_Notices_1_0 {

	/**
	 * All Ads
	 *
	 * @var object $ads
	 */
    protected $ads;
    
	/**
	 * Notices content
	 *
	 * @var object $notices_content
	 */
	protected $notices_content;

    /**
     * Plugin details
     *
	 * @var object $plugin
	 */
	protected $plugin;


	/**
	 * Populates ad content and loads assets
	 *
	 * @param array $plugin Plugin details
	 */
	public function __construct($plugin) {
        $this->ads = $this->is_metasliderpro_installed() ? $this->pro_notices() : $this->lite_notices();
        
        // To avoid showing the user ads off the start, lets wait
        $this->notices_content = ($this->ad_delay_has_finished()) ? $this->ads : array();
        $this->plugin = $plugin;

        // If $notices_content is empty, we still want to offer seasonal ads
        if (empty($this->notices_content) && !$this->is_metasliderpro_installed()) {
            $this->notices_content = $this->valid_seasonal_notices();
        }
        
        add_action('admin_enqueue_scripts', array($this, 'add_notice_assets'));
        add_action('wp_ajax_notice_handler', array($this, 'ajax_notice_handler'));
        add_action('admin_notices', array($this, 'show_dashboard_notices'));
	}

	/**
	 * Handles assets for the notices
	 */
	public function add_notice_assets() {
        wp_enqueue_style('ml-slider-notices-css',  METASLIDER_ADMIN_URL . 'assets/css/notices.css', false, METASLIDER_VERSION);
        wp_localize_script('jquery', 'metaslider_notices', array(
            'handle_notices_nonce' => wp_create_nonce('metaslider_handle_notices_nonce')
        ));
	}

	/**
	 * Deprecated for MetaSlider for now
	 */
	public function notices_init() { return; }

	/**
     * Returns notices that free/lite users should see. dismiss_time should match the key
     * hide_time is in weeks. Use a string to hide for 9999 weeks.
     *
	 * @return array returns an array of notices
	 */
	protected function lite_notices() {
		return array_merge(array(
			'updraftplus' => array(
				'title' => __('Always backup WordPress to avoid losing your site!', 'ml-slider'),
				'text' => __("UpdraftPlus is the world's #1 backup plugin from the makers of MetaSlider. Backup to the cloud, on a schedule and restore with 1 click!", 'ml-slider'),
				'image' => 'updraft_logo.png',
				'button_link' => 'https://wordpress.org/plugins/updraftplus/',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'updraftplus',
				'hide_time' => 12,
				'supported_positions' => array('header', 'dashboard'),
				'validity_function' => 'is_updraftplus_installed',
			),
			'keyy' => array(
				'title' => __('Keyy: Instant and secure logon with a wave of your phone', 'ml-slider'),
				'text' => __('No more forgotten passwords. Find out more about our revolutionary new WordPress plugin', 'ml-slider'),
				'image' => 'keyy_logo.png',
				'button_link' => 'https://getkeyy.com/?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'keyy',
				'dismiss_time' => 'keyy',
				'hide_time' => 12,
				'supported_positions' => array('header', 'dashboard'),
				'validity_function' => 'is_keyy_installed',
			),
			'updraftcentral' => array(
				'title' => __('Save Time and Money. Manage multiple WordPress sites from one location.', 'ml-slider'),
				'text' => __('UpdraftCentral is a highly efficient way to take backup, update and manage multiple WP sites from one location', 'ml-slider'),
				'image' => 'updraft_logo.png',
				'button_link' => 'https://updraftcentral.com?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'updraftcentral',
				'dismiss_time' => 'updraftcentral',
				'hide_time' => 12,
				'supported_positions' => array('header', 'dashboard'),
				'validity_function' => 'is_updraftcentral_installed',
			),
			'rate_plugin' => array(
				'title' => __('Like MetaSlider and have a minute to spare?', 'ml-slider'),
				'text' => __('Please help MetaSlider by giving a positive review at wordpress.org.', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => 'https://wordpress.org/support/plugin/ml-slider/reviews/?rate=5#new-post',
				'button_meta' => 'review',
				'dismiss_time' => 'rate_plugin',
				'hide_time' => 12,
				'supported_positions' => array('header', 'dashboard'),
			),
			'lite_survey' => array(
				'title' => __('Help us to get even better MetaSlider', 'ml-slider'),
				'text' => __('Let us know how you use MetaSlider by answering 4 simple questions. We will make MetaSlider to suit you better.', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => 'https://www.metaslider.com/survey?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'lets_start',
				'dismiss_time' => 'lite_survey',
				'hide_time' => 12,
				'supported_positions' => array('header'),
			),
			'pro_layers' => array(
				'title' => __('Spice up your site with animated layers and video slides', 'ml-slider'),
				'text' => __('With the MetaSlider Add-on pack you can give your slideshows a professional look!', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'pro_layers',
				'hide_time' => 12,
				'supported_positions' => array('header'),
			),
			'pro_features' => array(
				'title' => __('Increase your revenue and conversion with video slides and many more features', 'ml-slider'),
				'text' => __('Upgrade today to benefit from many more premium features. Find out more.', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'pro_features',
				'hide_time' => 12,
				'supported_positions' => array('header'),
			),
			'translation' => array(
				'title' => __('Can you translate? Want to improve MetaSlider for speakers of your language?', 'ml-slider'),
				'text' => __('Please go here for instructions - it is easy.', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => 'https://translate.wordpress.org/projects/wp-plugins/ml-slider',
				'button_meta' => 'lets_start',
				'dismiss_time' => 'translation',
				'hide_time' => 12,
				'supported_positions' => array('header'),
				'validity_function' => 'translation_needed',
			),
			'thankyou' => array(
				'title' => __('Thank you for installing MetaSlider', 'ml-slider'),
				'text' => __('Supercharge & secure your WordPress site with our other top plugins:', 'ml-slider'),
				'image' => 'metaslider_logo_large.png',
				'dismiss_time' => 'thankyou',
				'hide_time' => 24,
				'mega' => true,
				'supported_positions' => array('dashboard'),
			),
        ), $this->valid_seasonal_notices());
    }
    
	/**
	 * Premium user notices, if any. 
     *
	 * @return string
	 */
    protected function pro_notices() {
        return array(
			'pro_survey' => array(
				'title' => __("We’re making changes and need your help.", 'ml-slider'), 
				'text' => __('If you could spare a minute, we would like to ask you 4 easy questions about how you use MetaSlider. Your voice is important to us!', 'ml-slider'),
				'image' => 'metaslider_logo.png',
				'button_link' => 'https://www.metaslider.com/survey-pro',
                'button_meta' => 'lets_start',
                'dismiss_time' => 'pro_survey',
                'hide_time' => __('forever', 'ml-slider'),
				'supported_positions' => array('header'),
			),
        );
    }
    
	/**
	 * Seasonal Notices. Note that if dismissed, they will stay dismissed for 9999 weeks
     * Each year the key and dismiss time should be updated
     *
	 * @return string
	 */
    protected function seasonal_notices() {
        return array(
			'blackfriday2017' => array(
				'title' => __('Black Friday - 50% off the MetaSlider Add-on Pack until November 30th', 'ml-slider'),
				'text' => __('To benefit, use this discount code:', 'ml-slider').' ',
				'image' => 'seasonal/black_friday.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'blackfriday2017',
				'discount_code' => 'blackfriday2017sale',
				'valid_from' => '2017-11-20 00:00:00',
                'valid_to' => '2017-11-30 23:59:59',
                'hide_time' => __('until next year', 'ml-slider'),
				'supported_positions' => array('header', 'dashboard'),
			),
			'christmas2017' => array(
				'title' => __('Christmas sale - 50% off the MetaSlider Add-on Pack until December 25th', 'ml-slider'),
				'text' => __('To benefit, use this discount code:', 'ml-slider').' ',
				'image' => 'seasonal/christmas.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'christmas2017',
				'discount_code' => 'christmas2017sale',
				'valid_from' => '2017-12-01 00:00:00',
				'valid_to' => '2017-12-25 23:59:59',
                'hide_time' => __('until next year', 'ml-slider'),
				'supported_positions' => array('header', 'dashboard'),
			),
			'newyear2018' => array(
				'title' => __('Happy New Year - 50% off the MetaSlider Add-on Pack until January 1st', 'ml-slider'),
				'text' => __('To benefit, use this discount code:', 'ml-slider').' ',
				'image' => 'seasonal/new_year.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'newyear2018',
				'discount_code' => 'newyear2018sale',
				'valid_from' => '2017-12-26 00:00:00',
				'valid_to' => '2018-01-14 23:59:59',
                'hide_time' => __('until next year', 'ml-slider'),
				'supported_positions' => array('header', 'dashboard'),
			),
			'spring2018' => array(
				'title' => __('Spring sale - 50% off the MetaSlider Add-on Pack until April 31st', 'ml-slider'),
				'text' => __('To benefit, use this discount code:', 'ml-slider').' ',
				'image' => 'seasonal/spring.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'spring2018',
				'discount_code' => 'spring2018sale',
				'valid_from' => '2018-04-01 00:00:00',
				'valid_to' => '2018-04-30 23:59:59',
                'hide_time' => __('until next year', 'ml-slider'),
				'supported_positions' => array('header', 'dashboard'),
			),
			'summer2018' => array(
				'title' => __('Summer sale - 50% off the MetaSlider Add-on Pack until July 31st', 'ml-slider'),
				'text' => __('To benefit, use this discount code:', 'ml-slider').' ',
				'image' => 'seasonal/summer.png',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
				'button_meta' => 'ml-slider',
				'dismiss_time' => 'summer2018',
				'discount_code' => 'summer2018sale',
				'valid_from' => '2018-07-01 00:00:00',
				'valid_to' => '2018-07-31 23:59:59',
                'hide_time' => __('until next year', 'ml-slider'),
				'supported_positions' => array('header', 'dashboard'),
			)
		);
    }

	/**
	 * These appear inside a mega ad. 
     *
	 * @return string
	 */
    protected function mega_notice_parts() {
        return array(
			'ms_pro' => array(
				'title' => __('MetaSlider Add-on Pack:'), 
				'text' => __('Increase your conversion rate with video slides and many more options.', 'ml-slider'),
				'image' => '',
				'button_link' => apply_filters('metaslider_hoplink', 'https://www.metaslider.com/upgrade') . '?utm_source=metaslider-plugin-page&utm_medium=banner',
                'button_meta' => 'ml-slider',
			),
			// 'wpo_pro' => array(
			// 'title' => __('WP-Optimize Premium'), 
			// 'text' => __('offers unparalleled choice and flexibility, allowing you to select one or a combination of over a dozen optimization options.', 'ml-slider'),
			// 'image' => '',
			// 'button_link' => 'https://getwpo.com?utm_source=metaslider-plugin-page&utm_medium=banner',
            // 'button_meta' => 'ml-slider',
			// ),
			// 'udp_pro' => array(
			// 'title' => __('UpdraftPlus Premium'), 
			// 'text' => __('provides personal support, the ability to copy sites, more storage destinations, encrypted backups for security, multiple backup destinations, better reporting, no adverts and plenty more.', 'ml-slider'),
			// 'image' => '',
			// 'button_link' => 'https://updraftplus.com?utm_source=metaslider-plugin-page&utm_medium=banner',
            // 'button_meta' => 'ml-slider',
			// ),
			'udp' => array(
				'title' => __('UpdraftPlus'), 
				'text' => __('simplifies backups and restoration. It is the world\'s highest ranking and most popular scheduled backup plugin, with over a million currently-active installs.', 'ml-slider'),
				'image' => '',
				'button_link' => 'https://wordpress.org/plugins/updraftplus/',
                'button_meta' => 'updraftplus',
			),
			'wpo' => array(
				'title' => __('WP-Optimize:'), 
				'text' => __('auto-clean your WordPress database so that it runs at maximum efficiency.', 'ml-slider'),
				'image' => '',
				'button_link' => 'https://wordpress.org/plugins/wp-optimize/',
                'button_meta' => 'wp-optimize',
			),
			'keyy' => array(
				'title' => __('Keyy:'), 
				'text' => htmlspecialchars(__('Simple & secure login with a wave of your phone.', 'ml-slider')),
				'image' => '',
				'button_link' => 'https://getkeyy.com/?utm_source=metaslider-plugin-page&utm_medium=banner',
                'button_meta' => 'keyy',
			),
			'updraftcentral' => array(
				'title' => __('UpdraftCentral'), 
				'text' => __('is a highly efficient way to manage, update and backup multiple websites from one place.', 'ml-slider'),
				'image' => '',
				'button_link' => 'https://updraftcentral.com?utm_source=metaslider-plugin-page&utm_medium=banner',
                'button_meta' => 'updraftcentral',
			),
        );
    }

	/**
     * Check to disable ads on the Pro version. The parent function returns 
     * false if installed, so this is reversed and shouldn't be used for the validity function
     *
	 * @return bool 
	 */
	protected function is_metasliderpro_installed() {
		return !parent::is_plugin_installed('ml-slider-pro', false);
	}

	/**
	 * Check to see if UpdraftPlus is installed
     *
	 * @return bool 
	 */
	protected function is_updraftplus_installed() {
		return parent::is_plugin_installed('updraftplus', false);
    }

	/**
	 * Check to see if UpdraftPlus is installed
     *
	 * @return bool 
	 */
	protected function is_keyy_installed() {
		return parent::is_plugin_installed('keyy', false);
    }
    
	/**
	 * Check to see if UpdraftCentral is installed
     *
	 * @return bool 
	 */
	protected function is_updraftcentral_installed() {
		return parent::is_plugin_installed('updraftcentral', false);
	}

	/**
	 * Checks if the user agent isn't set as en_GB or en_US, and if the language file doesn't exist
     *
	 * @param  string $plugin_base_dir The plguin base directory
	 * @param  string $product_name    Product name
	 * @return bool
	 */
	protected function translation_needed($plugin_base_dir = null, $product_name = null) {
		return parent::translation_needed(METASLIDER_PATH, 'ml-slider');
	}
	
	/**
	 * This method checks to see if the ad has been dismissed
     *
	 * @param string $ad_identifier - identifier for the ad
	 * @return bool returns true when we dont want to show the ad
	 */
	protected function check_notice_dismissed($ad_identifier) {
		return (time() < get_option("ms_hide_{$ad_identifier}_ads_until"));
    }
	
	/**
	 * Returns all active seasonal ads
     *
	 * @return array
	 */
	protected function valid_seasonal_notices() {
        $valid = array();
        $time_now = time();
        // $time_now = strtotime('2017-11-20 00:00:01'); // Black Friday
        // $time_now = strtotime('2017-12-01 00:00:01'); // XMAS
        // $time_now = strtotime('2017-12-26 00:00:01'); // NY
        // $time_now = strtotime('2018-04-01 00:00:01'); // Spring
        // $time_now = strtotime('2018-07-01 00:00:01'); // Summer
        foreach($this->seasonal_notices() as $ad_identifier => $notice) {
            $valid_from = strtotime($notice['valid_from']);
            $valid_to = strtotime($notice['valid_to']);
            if ($valid_from < $time_now && $time_now <= $valid_to) {
                $valid[$ad_identifier] = $notice;
            }
        }
        return $valid;
    }

    /**
	 * The logic is handled elsewhere. This being true does not skip
     * the seasonal notices. Overrides parent function
     *
     * @param array $notice_data Notice data
	 * @return array
	 */
    protected function skip_seasonal_notices($notice_data) {
        return !$this->check_notice_dismissed($notice_data['dismiss_time']);
    }

	/**
	 * Checks whether this is an ad page - hard-coded
     *
	 * @return bool 
	 */
	protected function is_page_with_ads() {
        global $pagenow;
        $page = isset($_GET['page']) ? $_GET['page'] : '';

        // I'm thinking to limit the check to the actual settings page for now
        // This way, if the activaye the plugin but don't start using it until 
        // a few weeks after, it won't bother them with ads.
		// return ('index.php' === $pagenow) || ($page === 'metaslider');
		return ($page === 'metaslider');
    }

	/**
     * This method checks to see if the ad waiting period is over (2 weeks)
     * If not, it will set a two week time
     *
	 * @return bool returns true when we dont want to show the ad
	 */
	protected function ad_delay_has_finished() {

        if (metaslider_is_pro_installed()) {

            // If they are pro don't check anything but show the pro ad.
            return true;
        }

        // Disable this for now so that after a dismiss, ads hide for 24 hours
        // if (get_option("ms_ads_first_seen_on")) {
        // They have seen ads before which means the delay is over
        // return true;
        // }
        $delay = get_option("ms_hide_all_ads_until");

        // Only start the timer if they see a page that has ads
        if (!$this->is_page_with_ads() && !$delay) {
            return false;
        }
        if (!$delay) {

            // Set the delay for when to see an ad, 2 weeks; returns false
            return !update_option("ms_hide_all_ads_until", time() + 2*7*86400);
        } else if ((time() > $delay) && !get_option("ms_ads_first_seen_on")) {

            // Note the time they first saw ads
            update_option("ms_ads_first_seen_on", time());

            // Now that they can see ads, make sure the rate_plugin is shown first
            // TODO: Enable this next time
            // $notices = $this->lite_notices();
            // $this->ads = array('rate_plugin' => $notices['rate_plugin']);
            return true;
        } else if (time() < $delay) {

            // This means an ad was dismissed and there's a 24h delay
            return false;
        } else if (get_option("ms_ads_first_seen_on")) {

            // This means the delay has elapsed, the 24hr period expired
            // and there are still ads that haven't been dismissed
            return true;
        }
        // Default to not show an ad, in case there's some error
		return false;
    }
    
    /**
     * Method to handle dashboard notices
     */
    public function show_dashboard_notices() {
        $current_page = get_current_screen();
        if ('dashboard' === $current_page->base && metaslider_sees_notices($this->plugin)) {

            // Override the delay to show the thankyou notice on activation
            // if (!empty($_GET['ms_activated'])) {
	        // $lite_notices = $this->lite_notices();
	        // $this->notices_content['thankyou'] = $lite_notices['thankyou'];
            // }
            echo $this->do_notice(false, 'dashboard', true); 
        }
    }

	/**
	 * Selects the template and returns or displays the notice
     *
	 * @param array  $notice_information     - variable names/values to pass through to the template
	 * @param bool   $return_instead_of_echo - whether to 
	 * @param string $position               - where the notice is being displayed
	 * @return null|string - depending on the value of $return_instead_of_echo
	 */
	protected function render_specified_notice($notice_information, $return_instead_of_echo = false, $position = 'header') {
		$views = array(
			'header' => 'header-notice.php',
			'dashboard' => 'dashboard-notice.php',
        );
		$view = isset($views[$position]) ? $views[$position] : 'header-notice.php';
		return $this->include_template($view, $return_instead_of_echo, $notice_information);
	}

	/**
	 * Displays or returns the template
     *
	 * @param string $path                   file name of the template
	 * @param bool   $return_instead_of_echo Return the template instead of printing
	 * @param array  $args                   template arguments
	 * @return null|string
	 */
	public function include_template($path, $return_instead_of_echo = false, $args = array()) {
		if ($return_instead_of_echo) ob_start();

        extract($args);
        if (is_int($hide_time)) {
            $hide_time = $hide_time . ' ' . __('weeks', 'ml-slider');
        }
		include METASLIDER_PATH.'admin/views/notices/'.$path;

		if ($return_instead_of_echo) return ob_get_clean();
	}

	/**
	 * Builds a link based on the type of notice being requested
     *
	 * @param string $link - the URL to link to
	 * @param string $type - which notice is being displayed
	 * @return string - the resulting HTML
	 */
	public function get_button_link($link, $type) {
		$messages = array(
			'updraftplus' => __('Get UpdraftPlus', 'ml-slider'),
			'keyy' => __('Get Keyy', 'ml-slider'),
			'wp-optimize' => __('Optimize today', 'ml-slider'),
			'updraftcentral' => __('Get UpdraftCentral', 'ml-slider'),
			'lets_start' => __('Let\'s Start', 'ml-slider'),
			'review' => __('Review MetaSlider', 'ml-slider'),
			'ml-slider' => __('Find out more', 'ml-slider'),
			'signup' => __('Sign up', 'ml-slider'),
			'go_there' => __('Go there', 'ml-slider')
		);
		$message = isset($messages[$type]) ? $messages[$type] : __('Read more', 'ml-slider');
		$link = apply_filters('updraftplus_com_link', $link);

		return '<a class="updraft_notice_link" href="' . esc_url($link) . '">' . $message . '</a>';
	}

	/**
	 * Handles any notice related ajax calls
     *
	 * @return string - (JSON) Sends a success response unless an error is encountered
	 */
	public function ajax_notice_handler() {
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'metaslider_handle_notices_nonce')) {
			return wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
			), 401);
		}

		if (is_wp_error($ad_data = $this->ad_exists($_POST['ad_identifier']))) {
			return wp_send_json_error(array(
                'message' => __('This item does not exist. Please refresh the page and try again.', 'ml-slider')
			), 401);
		}
        
		$result = $this->dismiss_ad($ad_data['dismiss_time'], $ad_data['hide_time']);
        
		if (is_wp_error($result)) {
			return wp_send_json_error(array(
				'message' => $result->get_error_message()
			), 409);
		}
		
		return wp_send_json_success(array(
            'message' => __('The option was successfully updated', 'ml-slider'),
        ), 200);
    }
  
	/**
     * Returns the available ads that havent been dismissed by the user
     *
	 * @param string|array $location     the location for the ad
	 * @param boolean      $bypass_delay Bypass the ad delay
	 * @return array the identifier for the ad
	 */
	public function active_ads($location = 'header', $bypass_delay = false) {
        $dismissed_ads = array();

        $ads = ($bypass_delay) ? $this->ads : $this->notices_content;

        // Filter through all site options (cached)
        foreach(wp_load_alloptions() as $key => $value){
            if (strpos($key, 'ms_hide_') && strpos($key, '_ads_until')) {
                $key = str_replace(array('ms_hide_', '_ads_until'), '', $key);
                 $dismissed_ads[$key] = $value;
            }
        }
        
        // Filter out if the dismiss time has expired, then compare to the database
        $valid_ads = array();
        foreach ($ads as $ad_identifier => $values) {
            $is_valid = isset($values['validity_function']) ? call_user_func(array($this, $values['validity_function'])) : true;
            $not_dismissed = !$this->check_notice_dismissed($ad_identifier);
            $is_supported = in_array($location, $values['supported_positions']);
            if ($is_valid && $not_dismissed && $is_supported) {
                $valid_ads[$ad_identifier] = $values;
            }
        }

        return array_diff_key($valid_ads, $dismissed_ads);
    }
  
	/**
     * Returns all possible ads or the specified identifier
     *
     * @param string $ad_identifier Ad Identifier
	 * @return string|null the data of the ad
	 */
	public function get_ad($ad_identifier = null) {
        $all_notices = array_merge($this->pro_notices(), $this->lite_notices());
        return is_null($ad_identifier) ? $all_notices : $all_notices['ad_identifier'];
    } 
  
	/**
     * Checks if the ad identifier exists in any of the ads above
     *
     * @param string $ad_identifier Ad Identifier
	 * @return bool the data of the ad
	 */
	public function ad_exists($ad_identifier) {
        $all_notices = array_merge($this->pro_notices(), $this->lite_notices());
        return isset($all_notices[$ad_identifier]) ? $all_notices[$ad_identifier] : new WP_Error('bad_call', __('The requested data does not exist.', 'ml-slider'), array('status' => 401));
    } 

	/**
     * Updates the stored value for how long to hide the ads
     *
     * @param string     $ad_identifier Ad Identifier
     * @param int|string $weeks         time in weeks or a string to show
	 * @return bool|WP_Error whether the update was a success
	 */
	public function dismiss_ad($ad_identifier, $weeks) {

        // if the time isn't specified it will hide forever (9999 weeks)
        $weeks = is_int($weeks) ? $weeks : 9999;
        $result = update_option("ms_hide_{$ad_identifier}_ads_until", time() + $weeks*7*86400);
        
        // Hide all ads for 24 hours
        update_option("ms_hide_all_ads_until", time() + 1*86400);
        
		return $result ? $result : new WP_Error('update_failed', __('The attempt to update the option failed.', 'ml-slider'), array('status' => 409));
    }
}
