<?php

if (!class_exists('WP_Maintenance_Mode')) {

	class WP_Maintenance_Mode {

		const VERSION = '2.2.1';

		protected $plugin_slug = 'wp-maintenance-mode';
		protected $plugin_settings;
		protected $plugin_basename;
		protected static $instance = null;

		private function __construct() {
			$this->plugin_settings = get_option('wpmm_settings');
			$this->plugin_basename = plugin_basename(WPMM_PATH . $this->plugin_slug . '.php');

			// Load plugin text domain
			add_action('init', array($this, 'load_plugin_textdomain'));

			// Add shortcodes
			add_action('init', array('WP_Maintenance_Mode_Shortcodes', 'init'));

			// Activate plugin when new blog is added
			add_action('wpmu_new_blog', array($this, 'activate_new_site'));

			// Check update
			add_action('admin_init', array($this, 'check_update'));

			if (!empty($this->plugin_settings['general']['status']) && $this->plugin_settings['general']['status'] == 1) {
				// INIT
				add_action((is_admin() ? 'init' : 'template_redirect'), array($this, 'init'));

				// Add ajax methods
				add_action('wp_ajax_nopriv_wpmm_add_subscriber', array($this, 'add_subscriber'));
				add_action('wp_ajax_wpmm_add_subscriber', array($this, 'add_subscriber'));
				add_action('wp_ajax_nopriv_wpmm_send_contact', array($this, 'send_contact'));
				add_action('wp_ajax_wpmm_send_contact', array($this, 'send_contact'));

				// Redirect
				add_action('init', array($this, 'redirect'), 9);

				// Google Analytics tracking script
				add_action('wpmm_head', array($this, 'google_analytics_code'));
			}
		}

		public static function get_instance() {
			if (null == self::$instance) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Return plugin slug
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}

		/**
		 * Return plugin settings
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function get_plugin_settings() {
			return $this->plugin_settings;
		}

		/**
		 * Return plugin default settings
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function default_settings() {
			return array(
				'general' => array(
					'status' => 0,
					'status_date' => '',
					'bypass_bots' => 0,
					'backend_role' => array(),
					'frontend_role' => array(),
					'meta_robots' => 0,
					'redirection' => '',
					'exclude' => array(
						0 => 'feed',
						1 => 'wp-login',
						2 => 'login'
					),
					'notice' => 1,
					'admin_link' => 0
				),
				'design' => array(
					'title' => __('Maintenance mode', $this->plugin_slug),
					'heading' => __('Maintenance mode', $this->plugin_slug),
					'heading_color' => '',
					'text' => __('<p>Sorry for the inconvenience.<br />Our website is currently undergoing scheduled maintenance.<br />Thank you for your understanding.</p>', $this->plugin_slug),
					'text_color' => '',
					'bg_type' => 'color',
					'bg_color' => '',
					'bg_custom' => '',
					'bg_predefined' => 'bg1.jpg',
					'custom_css' => array()
				),
				'modules' => array(
					'countdown_status' => 0,
					'countdown_start' => date('Y-m-d H:i:s'),
					'countdown_details' => array(
						'days' => 0,
						'hours' => 1,
						'minutes' => 0
					),
					'countdown_color' => '',
					'subscribe_status' => 0,
					'subscribe_text' => __('Notify me when it\'s ready', $this->plugin_slug),
					'subscribe_text_color' => '',
					'social_status' => 0,
					'social_target' => 1,
					'social_github' => '',
					'social_dribbble' => '',
					'social_twitter' => '',
					'social_facebook' => '',
					'social_instagram' => '',
					'social_pinterest' => '',
					'social_google+' => '',
					'social_linkedin' => '',
					'contact_status' => 0,
					'contact_email' => get_option('admin_email') ? get_option('admin_email') : '',
					'contact_effects' => 'move_top|move_bottom',
					'ga_status' => 0,
					'ga_code' => '',
					'custom_css' => array()
				),
				'bot' => array(
					'status' => 0,
					'name' => 'Admin',
					'avatar' => '',
					'messages' => array(
						'01' => __("Hey! My name is {bot_name}, I'm the owner of this website and I'd like to be your assistant here.", $this->plugin_slug),
						'02' => __("I have just a few questions.", $this->plugin_slug),
						'03' => __("What is your name?", $this->plugin_slug),
						'04' => __("Nice to meet you here, {visitor_name}!"),
						'05' => __("How you can see, our website will be lauched very soon.", $this->plugin_slug),
						'06' => __("I know, you are very excited to see it, but we need a few days to finish it.", $this->plugin_slug),
						'07' => __("Would you like to be first to see it?", $this->plugin_slug),
						'08_1' => __("Cool! Please leave your email here and I will send you a message when it's ready.", $this->plugin_slug),
						'08_2' => __("Sad to hear that, {visitor_name} :( See you next time…", $this->plugin_slug),
						'09' => __("Got it! Thank you and see you soon here!", $this->plugin_slug),
						'10' => __("Have a great day!", $this->plugin_slug)
					),
					'responses' => array(
						'01' => __("Type your name here…", $this->plugin_slug),
						'02_1' => __("Tell me more", $this->plugin_slug),
						'02_2' => __("Boring", $this->plugin_slug),
						'03' => __("Type your email here…", $this->plugin_slug)
					),
					'custom_css' => array()
				),
				'gdpr' => array(
					'status' => 0,
					'policy_page_label' => __('Privacy Policy', $this->plugin_slug),
					'policy_page_link' => '',
					'contact_form_tail' => __('This form collects your name and email so that we can reach you back. Check out our <a href="#">Privacy Policy</a> page to fully understand how we protect and manage your submitted data.', $this->plugin_slug),
					'subscribe_form_tail' => __('This form collects your email so that we can add you to our newsletter list. Check out our <a href="#">Privacy Policy</a> page to fully understand how we protect and manage your submitted data.', $this->plugin_slug),
				),
			);
		}

		/**
		 * What to do when the plugin is activated
		 *
		 * @since 2.0.0
		 * @param boolean $network_wide
		 */
		public static function activate($network_wide) {
			// because we need translated items when activate :)
			load_plugin_textdomain(self::get_instance()->plugin_slug, FALSE, WPMM_LANGUAGES_PATH);

			// do the job
			if (function_exists('is_multisite') && is_multisite()) {
				if ($network_wide) {
					// Get all blog ids
					$blog_ids = self::get_blog_ids();
					foreach ($blog_ids as $blog_id) {
						switch_to_blog($blog_id);
						self::single_activate($network_wide);
						restore_current_blog();
					}
				} else {
					self::single_activate();
				}
			} else {
				self::single_activate();
			}

			// delete old options
			delete_option('wp-maintenance-mode');
			delete_option('wp-maintenance-mode-msqld');
		}

		/**
		 * Check plugin version for updating process
		 *
		 * @since 2.0.3
		 */
		public function check_update() {
			$version = get_option('wpmm_version', '0');

			if (!version_compare($version, WP_Maintenance_Mode::VERSION, '=')) {
				self::activate(is_multisite() && is_plugin_active_for_network($this->plugin_basename) ? true : false);
			}
		}

		/**
		 * What to do when the plugin is deactivated
		 *
		 * @since 2.0.0
		 * @param boolean $network_wide
		 */
		public static function deactivate($network_wide) {
			if (function_exists('is_multisite') && is_multisite()) {
				if ($network_wide) {
					// Get all blog ids
					$blog_ids = self::get_blog_ids();
					foreach ($blog_ids as $blog_id) {
						switch_to_blog($blog_id);
						self::single_deactivate();
						restore_current_blog();
					}
				} else {
					self::single_deactivate();
				}
			} else {
				self::single_deactivate();
			}
		}

		/**
		 * What to do when a new site is activated (multisite env)
		 *
		 * @since 2.0.0
		 * @param int $blog_id.
		 */
		public function activate_new_site($blog_id) {
			if (1 !== did_action('wpmu_new_blog')) {
				return;
			}

			switch_to_blog($blog_id);
			self::single_activate();
			restore_current_blog();
		}

		/**
		 * What to do on single activate
		 *
		 * @since 2.0.0
		 * @global object $wpdb
		 * @param boolean $network_wide
		 */
		public static function single_activate($network_wide = false) {
			global $wpdb;

			// create wpmm_subscribers table
			$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpmm_subscribers (
                    `id_subscriber` bigint(20) NOT NULL AUTO_INCREMENT,
                    `email` varchar(50) NOT NULL,
                    `insert_date` datetime NOT NULL,
                    PRIMARY KEY (`id_subscriber`)
                  ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			// get all options for different versions of the plugin
			$v2_options = get_option('wpmm_settings');
			$old_options = (is_multisite() && $network_wide) ? get_site_option('wp-maintenance-mode') : get_option('wp-maintenance-mode');
			$default_options = self::get_instance()->default_settings();

			/**
			 * Update from v1.8 to v2.x
			 *
			 * -  set notice if the plugin was installed before & set default settings
			 */
			if (!empty($old_options) && empty($v2_options)) {
				add_option('wpmm_notice', array(
					'class' => 'updated notice',
					'msg' => sprintf(__('WP Maintenance Mode plugin was relaunched and you MUST revise <a href="%s">settings</a>.', self::get_instance()->plugin_slug), admin_url('options-general.php?page=' . self::get_instance()->plugin_slug))
				));

				// import old options
				if (isset($old_options['active'])) {
					$default_options['general']['status'] = $old_options['active'];
				}
				if (isset($old_options['bypass'])) {
					$default_options['general']['bypass_bots'] = $old_options['bypass'];
				}

				if (!empty($old_options['role'][0])) {
					$default_options['general']['backend_role'] = $old_options['role'][0] == 'administrator' ? array() : $old_options['role'];
				}

				if (!empty($old_options['role_frontend'][0])) {
					$default_options['general']['frontend_role'] = $old_options['role_frontend'][0] == 'administrator' ? array() : $old_options['role_frontend'];
				}

				if (isset($old_options['index'])) {
					$default_options['general']['meta_robots'] = $old_options['index'];
				}

				if (!empty($old_options['rewrite'])) {
					$default_options['general']['redirection'] = $old_options['rewrite'];
				}

				if (!empty($old_options['exclude'][0])) {
					$default_options['general']['exclude'] = array_unique(array_merge($default_options['general']['exclude'], $old_options['exclude']));
				}

				if (isset($old_options['notice'])) {
					$default_options['general']['notice'] = $old_options['notice'];
				}

				if (isset($old_options['admin_link'])) {
					$default_options['general']['admin_link'] = $old_options['admin_link'];
				}

				if (!empty($old_options['title'])) {
					$default_options['design']['title'] = $old_options['title'];
				}

				if (!empty($old_options['heading'])) {
					$default_options['design']['heading'] = $old_options['heading'];
				}

				if (!empty($old_options['text'])) {
					$default_options['design']['text'] = $old_options['text'];
				}

				if (isset($old_options['radio'])) {
					$default_options['modules']['countdown_status'] = $old_options['radio'];
				}

				if (!empty($old_options['date'])) {
					$default_options['modules']['countdown_start'] = $old_options['date'];
				}

				if (isset($old_options['time']) && isset($old_options['unit'])) {
					switch ($old_options['unit']) {
						case 0: // seconds
							$default_options['modules']['countdown_details'] = array(
								'days' => 0,
								'hours' => 0,
								'minutes' => floor($old_options['time'] / 60)
							);
							break;
						case 1: // minutes
							$default_options['modules']['countdown_details'] = array(
								'days' => 0,
								'hours' => 0,
								'minutes' => $old_options['time']
							);
							break;
						case 2: // hours
							$default_options['modules']['countdown_details'] = array(
								'days' => 0,
								'hours' => $old_options['time'],
								'minutes' => 0
							);
						case 3: // days
							$default_options['modules']['countdown_details'] = array(
								'days' => $old_options['time'],
								'hours' => 0,
								'minutes' => 0
							);
							break;
						case 4: // weeks
							$default_options['modules']['countdown_details'] = array(
								'days' => $old_options['time'] * 7,
								'hours' => 0,
								'minutes' => 0
							);
							break;
						case 5: // months
							$default_options['modules']['countdown_details'] = array(
								'days' => $old_options['time'] * 30,
								'hours' => 0,
								'minutes' => 0
							);
							break;
						case 6: // years
							$default_options['modules']['countdown_details'] = array(
								'days' => $old_options['time'] * 365,
								'hours' => 0,
								'minutes' => 0
							);
							break;
						default:
							break;
					}
				}
			}
			
			/**
			 * Set options on first activation
			 */
			if (empty($v2_options)) {
				$v2_options = $default_options;
				
				// set options
				add_option('wpmm_settings', $v2_options);
			}

			/**
			 * Update from <= v2.0.6 to v2.0.7
			 */
			if (!empty($v2_options['modules']['ga_code'])) {
				$v2_options['modules']['ga_code'] = wpmm_sanitize_ga_code($v2_options['modules']['ga_code']);

				// update options
				update_option('wpmm_settings', $v2_options);
			}

			/**
			 * Update from <= v2.09 to v^2.1.2
			 */
			if (empty($v2_options['bot'])) {
				$v2_options['bot'] = $default_options['bot'];
				
				// update options
				update_option('wpmm_settings', $v2_options);
			}

			/**
			 * Update from =< v2.1.2 to 2.1.5
			 */
			if (empty($v2_options['gdpr'])) {
				$v2_options['gdpr'] = $default_options['gdpr'];
				
				// update options
				update_option('wpmm_settings', $v2_options);
			}

			// set current version
			update_option('wpmm_version', WP_Maintenance_Mode::VERSION);
		}

		/**
		 * What to do on single deactivate
		 *
		 * @since 2.0.0
		 */
		public static function single_deactivate() {
			// nothing
		}

		/**
		 * Get all blog ids of blogs in the current network
		 *
		 * @since 2.0.0
		 * @return array / false
		 */
		private static function get_blog_ids() {
			global $wpdb;

			return $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE archived = %d AND spam = %d AND deleted = %d", array(0, 0, 0)));
		}

		/**
		 * Load languages files
		 *
		 * @since 2.0.0
		 */
		public function load_plugin_textdomain() {
			$domain = $this->plugin_slug;
			$locale = apply_filters('plugin_locale', get_locale(), $domain);

			load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
			load_plugin_textdomain($domain, FALSE, WPMM_LANGUAGES_PATH);
		}

		/**
		 * Initialize when plugin is activated
		 *
		 * @since 2.0.0
		 */
		public function init() {
			/**
			 * CHECKS
			 */
			if (
					(!$this->check_user_role()) &&
					!strstr($_SERVER['PHP_SELF'], 'wp-cron.php') &&
					!strstr($_SERVER['PHP_SELF'], 'wp-login.php') &&
					// wp-admin/ is available to everyone only if the user is not loggedin, otherwise.. check_user_role decides
					!(strstr($_SERVER['PHP_SELF'], 'wp-admin/') && !is_user_logged_in()) &&
//                    !strstr($_SERVER['PHP_SELF'], 'wp-admin/') &&
					!strstr($_SERVER['PHP_SELF'], 'wp-admin/admin-ajax.php') &&
					!strstr($_SERVER['PHP_SELF'], 'async-upload.php') &&
					!(strstr($_SERVER['PHP_SELF'], 'upgrade.php') && $this->check_user_role()) &&
					!strstr($_SERVER['PHP_SELF'], '/plugins/') &&
					!strstr($_SERVER['PHP_SELF'], '/xmlrpc.php') &&
					!$this->check_exclude() &&
					!$this->check_search_bots() &&
					!(defined('WP_CLI') && WP_CLI)
			) {
				// HEADER STUFF
				$protocol = !empty($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.1', 'HTTP/1.0')) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
				$charset = get_bloginfo('charset') ? get_bloginfo('charset') : 'UTF-8';
				$status_code = (int) apply_filters('wp_maintenance_mode_status_code', 503); // this hook will be removed in the next versions
				$status_code = (int) apply_filters('wpmm_status_code', 503);
				$backtime_seconds = $this->calculate_backtime();
				$backtime = (int) apply_filters('wpmm_backtime', $backtime_seconds);

				// META STUFF
				$title = !empty($this->plugin_settings['design']['title']) ? $this->plugin_settings['design']['title'] : get_bloginfo('name') . ' - ' . __('Maintenance Mode', $this->plugin_slug);
				$title = apply_filters('wm_title', $title); // this hook will be removed in the next versions
				$title = apply_filters('wpmm_meta_title', $title);

				$robots = $this->plugin_settings['general']['meta_robots'] == 1 ? 'noindex, nofollow' : 'index, follow';
				$robots = apply_filters('wpmm_meta_robots', $robots);

				$author = apply_filters('wm_meta_author', get_bloginfo('name')); // this hook will be removed in the next versions
				$author = apply_filters('wpmm_meta_author', get_bloginfo('name'));

				$description = get_bloginfo('name') . ' - ' . get_bloginfo('description');
				$description = apply_filters('wm_meta_description', $description); // this hook will be removed in the next versions
				$description = apply_filters('wpmm_meta_description', $description);

				$keywords = __('Maintenance Mode', $this->plugin_slug);
				$keywords = apply_filters('wm_meta_keywords', $keywords); // this hook will be removed in the next versions
				$keywords = apply_filters('wpmm_meta_keywords', $keywords);

				// CSS STUFF
				$body_classes = !empty($this->plugin_settings['design']['bg_type']) && $this->plugin_settings['design']['bg_type'] != 'color' ? 'background' : '';
				$custom_css_design = !empty($this->plugin_settings['design']['custom_css']) && is_array($this->plugin_settings['design']['custom_css']) ? $this->plugin_settings['design']['custom_css'] : array();
				$custom_css_modules = !empty($this->plugin_settings['modules']['custom_css']) && is_array($this->plugin_settings['modules']['custom_css']) ? $this->plugin_settings['modules']['custom_css'] : array();
				$custom_css_bot = !empty($this->plugin_settings['bot']['custom_css']) && is_array($this->plugin_settings['bot']['custom_css']) ? $this->plugin_settings['bot']['custom_css'] : array();
				$custom_css = array_merge($custom_css_design, $custom_css_modules, $custom_css_bot);

				// CONTENT
				$heading = !empty($this->plugin_settings['design']['heading']) ? $this->plugin_settings['design']['heading'] : '';
				$heading = apply_filters('wm_heading', $heading); // this hook will be removed in the next versions
				$heading = apply_filters('wpmm_heading', $heading);

				$text = !empty($this->plugin_settings['design']['text']) ? $this->plugin_settings['design']['text'] : '';
				$text = apply_filters('wpmm_text', do_shortcode($text));

				// COUNTDOWN
				$countdown_start = !empty($this->plugin_settings['modules']['countdown_start']) ? $this->plugin_settings['modules']['countdown_start'] : $this->plugin_settings['general']['status_date'];
				$countdown_end = strtotime($countdown_start . ' +' . $backtime_seconds . ' seconds');

				// JS FILES
				$wp_scripts = wp_scripts();

				$scripts = array(
					'jquery' => !empty($wp_scripts->registered['jquery-core']) ? site_url($wp_scripts->registered['jquery-core']->src) : '//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery' . WPMM_ASSETS_SUFFIX . '.js',
					'frontend' => WPMM_JS_URL . 'scripts' . WPMM_ASSETS_SUFFIX . '.js'
				);
				if (!empty($this->plugin_settings['modules']['countdown_status']) && $this->plugin_settings['modules']['countdown_status'] == 1) {
					$scripts['countdown-dependency'] = WPMM_JS_URL . 'jquery.plugin' . WPMM_ASSETS_SUFFIX . '.js';
					$scripts['countdown'] = WPMM_JS_URL . 'jquery.countdown' . WPMM_ASSETS_SUFFIX . '.js';
				}
				if ((!empty($this->plugin_settings['modules']['contact_status']) && $this->plugin_settings['modules']['contact_status'] == 1) || (!empty($this->plugin_settings['modules']['subscribe_status']) && $this->plugin_settings['modules']['subscribe_status'] == 1) || (!empty($this->plugin_settings['bot']['status']) && $this->plugin_settings['bot']['status'] == 1)) {
					$scripts['validate'] = WPMM_JS_URL . 'jquery.validate' . WPMM_ASSETS_SUFFIX . '.js';
				}
				if (!empty($this->plugin_settings['bot']['status']) && $this->plugin_settings['bot']['status'] == 1) {
					$scripts['bot'] = WPMM_JS_URL . 'bot' . WPMM_ASSETS_SUFFIX . '.js';
					add_action('wpmm_before_scripts', array($this, 'add_bot_extras'));
				}
				$scripts = apply_filters('wpmm_scripts', $scripts);

				// CSS FILES
				$styles = array(
					'frontend' => WPMM_CSS_URL . 'style' . WPMM_ASSETS_SUFFIX . '.css'
				);
				if (!empty($this->plugin_settings['bot']['status']) && $this->plugin_settings['bot']['status'] == 1) {
					$styles['bot'] = WPMM_CSS_URL . 'style.bot' . WPMM_ASSETS_SUFFIX . '.css';
					$body_classes .= ' bot';
				}
				$styles = apply_filters('wpmm_styles', $styles);

				nocache_headers();
				ob_start();
				header("Content-type: text/html; charset=$charset");
				header("$protocol $status_code Service Unavailable", TRUE, $status_code);
				header("Retry-After: $backtime");

				// load maintenance mode template
				if (file_exists(get_stylesheet_directory() . '/wp-maintenance-mode.php')) { // check child theme folder
					include_once(get_stylesheet_directory() . '/wp-maintenance-mode.php');
				} else if (file_exists(get_template_directory() . "/wp-maintenance-mode.php")) { // check theme folder
					include_once(get_template_directory() . '/wp-maintenance-mode.php');
				} else if (file_exists(WP_CONTENT_DIR . '/wp-maintenance-mode.php')) { // check `wp-content` folder
					include_once(WP_CONTENT_DIR . '/wp-maintenance-mode.php');
				} else { // load from plugin `views` folder
					include_once(WPMM_VIEWS_PATH . 'maintenance.php');
				}
				ob_flush();

				exit();
			}
		}

		/**
		 * Extra variables for the bot functionality. Added to the DOM via hooks.
		 * It has to be called before scripts are loaded so the variables are available globally.
		 * 
		 * @todo Maybe we can find a better home for this method
		 * @since 2.1.1
		 * @return string Script tag with all the fixed text strings for the bot.
		 */
		public function add_bot_extras() {
			$upload_dir = wp_upload_dir();
			$bot_vars = array(
				'validationName' => __('Please type in your name.', $this->plugin_slug),
				'validationEmail' => __('Please type in a valid email address.', $this->plugin_slug),
				'uploadsBaseUrl' => trailingslashit($upload_dir['baseurl']),
				'typeName' => __('Type your name here…', $this->plugin_slug),
				'typeEmail' => __('Type your email here…', $this->plugin_slug),
				'send' => __('Send', $this->plugin_slug)
			);
			echo "<script type='text/javascript'>" .
			"var botVars = " . json_encode($bot_vars) .
			"</script>";
		}

		/**
		 * Check if the current user has access to backend / frontend based on his role compared with role from settings (refactor @ 2.0.4)
		 *
		 * @since 2.0.0
		 * @return boolean
		 */
		public function check_user_role() {
			// check super admin (when multisite is activated) / check admin (when multisite is not activated)
			if (is_super_admin()) {
				return true;
			}

			$user = wp_get_current_user();
			$user_roles = !empty($user->roles) && is_array($user->roles) ? $user->roles : array();
			$allowed_roles = is_admin() ? (array) $this->plugin_settings['general']['backend_role'] : (array) $this->plugin_settings['general']['frontend_role'];

			// add `administrator` role when multisite is activated and the admin of a blog is trying to access his blog
			if (is_multisite()) {
				array_push($allowed_roles, 'administrator');
			}

			$is_allowed = (bool) array_intersect($user_roles, $allowed_roles);

			return $is_allowed;
		}

		/**
		 * Calculate backtime based on countdown remaining time if it is activated
		 *
		 * @since 2.0.0
		 * @return int
		 */
		public function calculate_backtime() {
			$backtime = 3600;

			if (!empty($this->plugin_settings['modules']['countdown_status']) && $this->plugin_settings['modules']['countdown_status'] == 1) {
				$backtime = ($this->plugin_settings['modules']['countdown_details']['days'] * DAY_IN_SECONDS) + ($this->plugin_settings['modules']['countdown_details']['hours'] * HOUR_IN_SECONDS) + ($this->plugin_settings['modules']['countdown_details']['minutes'] * MINUTE_IN_SECONDS);
			}

			return $backtime;
		}

		/**
		 * Check if the visitor is a bot (using useragent)
		 *
		 * @since 2.0.0
		 * @return boolean
		 */
		public function check_search_bots() {
			$is_search_bots = false;

			if (!empty($this->plugin_settings['general']['bypass_bots']) && $this->plugin_settings['general']['bypass_bots'] == 1) {
				$bots = apply_filters('wpmm_search_bots', array(
					'Abacho' => 'AbachoBOT',
					'Accoona' => 'Acoon',
					'AcoiRobot' => 'AcoiRobot',
					'Adidxbot' => 'adidxbot',
					'AltaVista robot' => 'Altavista',
					'Altavista robot' => 'Scooter',
					'ASPSeek' => 'ASPSeek',
					'Atomz' => 'Atomz',
					'Bing' => 'bingbot',
					'BingPreview' => 'BingPreview',
					'CrocCrawler' => 'CrocCrawler',
					'Dumbot' => 'Dumbot',
					'eStyle Bot' => 'eStyle',
					'FAST-WebCrawler' => 'FAST-WebCrawler',
					'GeonaBot' => 'GeonaBot',
					'Gigabot' => 'Gigabot',
					'Google' => 'Googlebot',
					'ID-Search Bot' => 'IDBot',
					'Lycos spider' => 'Lycos',
					'MSN' => 'msnbot',
					'MSRBOT' => 'MSRBOT',
					'Rambler' => 'Rambler',
					'Scrubby robot' => 'Scrubby',
					'Yahoo' => 'Yahoo'
				));

				$is_search_bots = (bool) preg_match('~(' . implode('|', array_values($bots)) . ')~i', $_SERVER['HTTP_USER_AGENT']);
			}

			return $is_search_bots;
		}

		/**
		 * Check if slug / ip address exists in exclude list
		 *
		 * @since 2.0.0
		 * @return boolean
		 */
		public function check_exclude() {
			$is_excluded = false;
			$excluded_list = array();

			if (!empty($this->plugin_settings['general']['exclude']) && is_array($this->plugin_settings['general']['exclude'])) {
				$excluded_list = $this->plugin_settings['general']['exclude'];

				foreach ($excluded_list as $item) {
					if (empty($item)) { // just to be sure :-)
						continue;
					}

					if ((!empty($_SERVER['REMOTE_ADDR']) && strstr($_SERVER['REMOTE_ADDR'], $item)) || (!empty($_SERVER['REQUEST_URI']) && strstr($_SERVER['REQUEST_URI'], $item))) {
						$is_excluded = true;
						break;
					}
				}
			}

			$is_excluded = apply_filters('wpmm_is_excluded', $is_excluded, $excluded_list);

			return $is_excluded;
		}

		/**
		 * Redirect if "Redirection" option is used and users don't have access to WordPress dashboard
		 *
		 * @since 2.0.0
		 * @return null
		 */
		public function redirect() {
			// we do not redirect if there's nothing saved in "redirect" input
			if (empty($this->plugin_settings['general']['redirection'])) {
				return NULL;
			}

			// we do not redirect ajax calls
			if ((defined('DOING_AJAX') && DOING_AJAX)) {
				return NULL;
			}

			// we do not redirect visitors or logged-in users that are not using /wp-admin/
			if (!is_user_logged_in() || !is_admin()) {
				return NULL;
			}

			// we do not redirect users that have access to backend
			if ($this->check_user_role()) {
				return NULL;
			}

			$redirect_to = stripslashes($this->plugin_settings['general']['redirection']);
			wp_redirect($redirect_to);
			exit;
		}

		/**
		 * Google Analytics code
		 *
		 * @since 2.0.7
		 */
		public function google_analytics_code() {
			// check if module is activated and code exists
			if (
					empty($this->plugin_settings['modules']['ga_status']) ||
					$this->plugin_settings['modules']['ga_status'] != 1 ||
					empty($this->plugin_settings['modules']['ga_code'])
			) {
				return false;
			}

			// sanitize code
			$ga_code = wpmm_sanitize_ga_code($this->plugin_settings['modules']['ga_code']);
			if (empty($ga_code)) {
				return false;
			}

			// show google analytics javascript snippet
			include_once(WPMM_VIEWS_PATH . 'google-analytics.php');
		}

		/**
		 * Save subscriber into database (refactor @ 2.0.4)
		 *
		 * @since 2.0.0
		 * @global object $wpdb
		 * @throws Exception
		 */
		public function add_subscriber() {
			global $wpdb;

			try {
				$_POST = array_map('trim', $_POST);

				// checks
				if (empty($_POST['email']) || !is_email($_POST['email'])) {
					throw new Exception(__('Please enter a valid email address.', $this->plugin_slug));
				}

				// save
				$exists = $wpdb->get_row($wpdb->prepare("SELECT id_subscriber FROM {$wpdb->prefix}wpmm_subscribers WHERE email = %s", sanitize_text_field($_POST['email'])), ARRAY_A);
				if (empty($exists)) {
					$wpdb->insert($wpdb->prefix . 'wpmm_subscribers', array(
						'email' => sanitize_text_field($_POST['email']),
						'insert_date' => date('Y-m-d H:i:s')
							), array('%s', '%s'));
				}

				wp_send_json_success(__('You successfully subscribed. Thanks!', $this->plugin_slug));
			} catch (Exception $ex) {
				wp_send_json_error($ex->getMessage());
			}
		}

		/**
		 * Send email via contact form (refactor @ 2.0.4)
		 *
		 * @since 2.0.0
		 * @throws Exception
		 */
		public function send_contact() {
			try {
				$_POST = array_map('trim', $_POST);

				// checks
				if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['content'])) {
					throw new Exception(__('All fields required.', $this->plugin_slug));
				}

				if (!is_email($_POST['email'])) {
					throw new Exception(__('Please enter a valid email address.', $this->plugin_slug));
				}

				// if you add new fields to the contact form... you will definitely need to validate their values
				do_action('wpmm_contact_validation', $_POST);

				// vars
				$send_to = !empty($this->plugin_settings['modules']['contact_email']) ? stripslashes($this->plugin_settings['modules']['contact_email']) : get_option('admin_email');
				$subject = apply_filters('wpmm_contact_subject', __('Message via contact', $this->plugin_slug));
				$headers = apply_filters('wpmm_contact_headers', array('Reply-To: ' . sanitize_text_field($_POST['email'])));
				$template_path = apply_filters('wpmm_contact_template', WPMM_VIEWS_PATH . 'contact.php');
				$from_name = sanitize_text_field($_POST['name']);

				ob_start();
				include_once($template_path);
				$message = ob_get_clean();

				// filters
				add_filter('wp_mail_content_type', 'wpmm_change_mail_content_type', 10, 1);
				add_filter('wp_mail_from_name', function() use ($from_name) {
							return $from_name;
						});

				// send email
				@wp_mail($send_to, $subject, $message, $headers);
				wp_send_json_success(__('Your email was sent to the website administrator. Thanks!', $this->plugin_slug));
			} catch (Exception $ex) {
				wp_send_json_error($ex->getMessage());
			}
		}

	}

}
