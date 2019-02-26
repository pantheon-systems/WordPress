<?php

//Can't have two different versions of the plugin active at the same time. It would be incredibly buggy.
if (class_exists('WPMenuEditor')){
	trigger_error(
		'Another version of Admin Menu Editor is already active. Please deactivate it before activating this one.', 
		E_USER_ERROR
	);
}

$thisDirectory = dirname(__FILE__);
require $thisDirectory . '/shadow_plugin_framework.php';
require $thisDirectory . '/role-utils.php';
require $thisDirectory . '/ame-utils.php';
require $thisDirectory . '/menu-item.php';
require $thisDirectory . '/menu.php';
require $thisDirectory . '/auto-versioning.php';
require $thisDirectory . '/../ajax-wrapper/AjaxWrapper.php';
require $thisDirectory . '/module.php';
require $thisDirectory . '/persistent-module.php';

class WPMenuEditor extends MenuEd_ShadowPluginFramework {
	const WPML_CONTEXT = 'admin-menu-editor menu texts';

	const VERBOSITY_LOW = 1;
	const VERBOSITY_NORMAL = 2;
	const VERBOSITY_VERBOSE = 5;

	const DIRECTLY_GRANTED_VIRTUAL_CAPS = 2;
	const ALL_VIRTUAL_CAPS = 3;

	/**
	 * @var string The heading tag to use for admin pages.
	 */
	public static $admin_heading_tag = 'h1';

	private $plugin_db_version = 140;

	/** @var array The default WordPress menu, before display-specific filtering. */
	protected $default_wp_menu;
	/** @var array The default WordPress submenu. */
	protected $default_wp_submenu;

	/**
	 * We also keep track of the final, ready-for-display version of the default WP menu
	 * and submenu. These values are captured *just* before the admin menu HTML is output
	 * by _wp_menu_output() in /wp-admin/menu-header.php, and are restored afterwards.
	 */
	private $old_wp_menu;
	private $old_wp_submenu;

	private $title_lookups = array();   //A list of page titles indexed by $item['file']. Used to
	                                    //fix the titles of moved plugin pages.
	private $reverse_item_lookup = array(); //Contains the final (merged & filtered) list of admin menu items,
                                            //indexed by URL.

	/**
	 * @var array List of per-URL capabilities, indexed by priority. Used while merging and
	 * building the final admin menu.
	 */
	private $page_access_lookup = array();

	/**
	 * @var array A log of menu access checks.
	 */
	private $security_log = array();

	/**
	 * @var array The current custom menu with defaults merged in.
	 */
	private $merged_custom_menu = null;

	/**
	 * @var array The custom menu in WP-compatible format (top-level).
	 */
	private $custom_wp_menu = null;

	/**
	 * @var array The custom menu in WP-compatible format (sub-menu).
	 */
	private $custom_wp_submenu = null;

	private $item_templates = array();  //A lookup list of default menu items, used as templates for the custom menu.
	private $relative_template_order = array();

	private $cached_custom_menu = null; //Cached, non-merged version of the custom menu. Used by load_custom_menu().
	private $loaded_menu_config_id = null;
	private $cached_virtual_caps = null;//List of virtual caps. Used by get_virtual_caps().

	private $cached_user_caps = array(); //A cache of the current user's capabilities. Used only in very specific places.
	private $user_cap_cache_enabled = false;

	//Our personal copy of the request vars, without any "magic quotes".
	private $post = array();
	private $get = array();
	private $originalPost = array();

	/**
	 * @var array A cache of user role names indexed by user ID. E.g. [123 => array("administrator", "foo")]
	 */
	private $cached_user_roles = array();

	/**
	 * @var array An index of URLs relative to /wp-admin/. Any menus that match the index will be ignored.
	 */
	private $menu_url_blacklist = array();

	/**
	 * @var array Menu editor page tabs.
	 */
	private $tabs = array();

	/**
	 * @var string The slug of the current settings tab, if any.
	 */
	private $current_tab = '';

	/**
	 * @var array List of capabilities that are used in the default admin menu. Used to detect meta capabilities.
	 */
	private $caps_used_in_menu = array();

	public $is_access_test = false;
	private $test_menu = null;
	/**
	 * @var ameAccessTestRunner|null
	 */
	private $access_test_runner = null;

	function init(){
		$this->sitewide_options = true;

		//Set some plugin-specific options
		if ( empty($this->option_name) ){
			$this->option_name = 'ws_menu_editor';
		}
		$this->defaults = array(
			'hide_advanced_settings' => true,
			'show_extra_icons' => false,
			'custom_menu' => null,
			'custom_network_menu' => null,
			'first_install_time' => null,
			'display_survey_notice' => true,
			'plugin_db_version' => 0,
			'security_logging_enabled' => false,

			'menu_config_scope' => ($this->is_super_plugin() || !is_multisite()) ? 'global' : 'site',

			//super_admin, specific_user, or a capability.
			'plugin_access' => $this->is_super_plugin() ? 'super_admin' : 'manage_options',
			//The ID of the user who is allowed to use this plugin. Only used when plugin_access == specific_user.
			'allowed_user_id' => null,
			//The user who can see this plugin on the "Plugins" page. By default all admins can see it.
			'plugins_page_allowed_user_id' => null,

			'show_deprecated_hide_button' => true, //Note: Un-deprecated as of 2015.10.01.
			'dashboard_hiding_confirmation_enabled' => true,

			//When to show submenu icons.
			'submenu_icons_enabled' => 'if_custom', //"never", "if_custom" or "always".

			//Enable/disable CSS workaround that helps override menu icons set by other plugins.
			'force_custom_dashicons' => true,

			//Menu editor UI colour scheme. "Classic" is the old blue/yellow scheme, and "wp-grey" is more WP-like.
			'ui_colour_scheme' => 'classic',

			//User logins that will show up in the actor list at the top of the editor.
			'visible_users' => array(),

			//Enable/disable the admin notice that tells the user where the plugin settings menu is.
			'show_plugin_menu_notice' => true,

			//Where to place menu items that are not part of the last saved menu configuration.
			//This usually applies to new items added by other plugins and, in Multisite, items that exist on
			//the current site but did not exist on the site where the user last edited the menu configuration.
			'unused_item_position' => 'relative', //"relative" or "bottom".

			//Permissions for menu items that are not part of the save menu configuration.
			//The default is to leave the permissions unchanged.
			'unused_item_permissions' => 'unchanged', //"unchanged" or "match_plugin_access".

			//Verbosity level of menu permission errors.
			'error_verbosity' => self::VERBOSITY_NORMAL,

			//Enable/disable menu configuration compression. Enabling it makes the DB row much smaller,
			//but adds decompression overhead to very admin page.
			'compress_custom_menu' => false,

			//Which modules are active or inactive. Format: ['module-id' => true/false].
			'is_active_module' => array(
				'highlight-new-menus' => false,
			),
		);
		$this->serialize_with_json = false; //(Don't) store the options in JSON format

		//WP 4.3+ uses H1 headings for admin pages. Older versions use H2 instead.
		self::$admin_heading_tag = version_compare($GLOBALS['wp_version'], '4.3', '<') ? 'h2' : 'h1';

		$this->settings_link = (is_network_admin() ? 'settings.php' : 'options-general.php') . '?page=menu_editor';
		
		$this->magic_hooks = true;
		//Run our hooks last (almost). Priority is less than PHP_INT_MAX mostly for defensive programming purposes.
		//Old PHP versions have known bugs related to large array keys, and WP might have undiscovered edge cases.
		$this->magic_hook_priority = PHP_INT_MAX - 10;

		/*
		 * Menu blacklist. Any menu items that *exactly* match one of the URLs on this list will be ignored.
		 * They won't show up in the editor or the admin menu, but they will remain accessible (caps permitting).
		 *
		 * This is a workaround for plugins that add a menu item and then remove it. Most plugins do this
		 * to create "Welcome" or "What's New" pages that are accessible but don't appear in the admin menu.
		 *
		 * We can't automatically detect menus like that. Here's why:
		 * 1) Most plugins remove them too late, e.g. in admin_head. By that point, output has already started.
		 *    We need the finalize the list of menu items and their permissions before that.
		 * 2) It's hard to automatically determine *why* a menu item was removed. We can't distinguish between
		 *    cosmetic changes like the hidden "welcome" items and people removing menus to deny access.
		 */
		$this->menu_url_blacklist = array(
			//WP RSS Aggregator 4.7.7
			'index.php?page=wprss-welcome' => true,
			//AffiliateWP 1.7.8
			'index.php?page=affwp-getting-started' => true,
			'index.php?page=affwp-what-is-new' => true,
			'index.php?page=affwp-credits' => true,
			//BuddyPress 2.3.4
			'index.php?page=bp-about' => true,
			'index.php?page=bp-credits' => true,
			//DW Question Answer 1.3.8.1
			'index.php?page=dwqa-about' => true,
			'index.php?page=dwqa-changelog' => true,
			'index.php?page=dwqa-credits' => true,
			//Ninja Forms 2.9.41
			'index.php?page=nf-about' => true,
			'index.php?page=nf-changelog' => true,
			'index.php?page=nf-getting-started' => true,
			'index.php?page=nf-credits' => true,
			//All in One SEO Pack 2.3.9.2
			'index.php?page=aioseop-about' => true,
			//WP Courseware 4.1.2
			//'wpcw' => true, //This is commented out due to a bug. The Courseware top level menu and its first submenu
			//both have the URL "wpcw", but the top level menu also has some visible, non-blacklisted items. AME would
			//still hide the entire menu because the template builder doesn't check if a menu has submenu items.
			'admin.php?page=wpcw-course-classroom'                 => true,
			'admin.php?page=wpcw-student'                          => true,
			'admin.php?page=WPCW_showPage_ConvertPage'             => true,
			'admin.php?page=WPCW_showPage_CourseOrdering'          => true,
			'admin.php?page=WPCW_showPage_GradeBook'               => true,
			'admin.php?page=WPCW_showPage_ModifyCourse'            => true,
			'admin.php?page=WPCW_showPage_ModifyModule'            => true,
			'admin.php?page=WPCW_showPage_ModifyQuestion'          => true,
			'admin.php?page=WPCW_showPage_ModifyQuiz'              => true,
			'admin.php?page=WPCW_showPage_UserCourseAccess'        => true,
			'admin.php?page=WPCW_showPage_UserProgess'             => true,
			'admin.php?page=WPCW_showPage_UserProgess_quizAnswers' => true,
		);
		
		//AJAXify screen options
		add_action('wp_ajax_ws_ame_save_screen_options', array($this,'ajax_save_screen_options'));

		//AJAXify hints and warnings
		add_action('wp_ajax_ws_ame_hide_hint', array($this, 'ajax_hide_hint'));
		add_action(
			'wp_ajax_ws_ame_disable_dashboard_hiding_confirmation',
			array($this, 'ajax_disable_dashboard_hiding_confirmation')
		);

		//Retrieve a list of pages via AJAX.
		add_action('wp_ajax_ws_ame_get_pages', array($this, 'ajax_get_pages'));
		//Get details about a specific page via AJAX.
		add_action('wp_ajax_ws_ame_get_page_details', array($this, 'ajax_get_page_details'));

		//Make sure we have access to the original, un-mangled request data.
		//This is necessary because WordPress will stupidly apply "magic quotes"
		//to the request vars even if this PHP misfeature is disabled.
		$this->capture_request_vars();

		add_action('admin_enqueue_scripts', array($this, 'enqueue_menu_fix_script'));

		//Enqueue miscellaneous helper scripts and styles.
		add_action('admin_enqueue_scripts', array($this, 'enqueue_helper_scripts'));
		add_action('admin_print_styles', array($this, 'enqueue_helper_styles'));

		//Make sure our scripts load before other plugins' scripts.
		add_action('admin_print_scripts', array($this, 'move_editor_scripts_to_top'));

		//User survey
		add_action('admin_notices', array($this, 'display_survey_notice'));

		//Tell first-time users where they can find the plugin settings page.
		add_action('all_admin_notices', array($this, 'display_plugin_menu_notice'));

		//Reset plugin access if the only allowed user gets deleted or their ID changes.
		add_action('wp_login', array($this, 'maybe_reset_plugin_access'), 10, 2);

		//Workaround for buggy plugins that unintentionally remove user roles.
		/** @see WPMenuEditor::get_user_roles */
		add_action('set_current_user', array($this, 'update_current_user_cache'), 1, 0); //Run before most plugins.
		add_action('updated_user_meta', array($this, 'clear_user_role_cache'), 10, 2);
		add_action('deleted_user_meta', array($this, 'clear_user_role_cache'), 10, 2);
		//There's also a "set_user_role" hook, but it's only called by WP_User::set_role and not WP_User::add_role.
		//It's also redundant - WP_User::set_role updates user meta, so the above hooks already cover it.

		//Multisite: Clear role and capability caches when switching to another site.
		add_action('switch_blog', array($this, 'clear_site_specific_caches'), 10, 0);

		//"Test Access" feature.
		if ( (defined('DOING_AJAX') && DOING_AJAX) || isset($this->get['ame-test-menu-access-as']) ) {
			require_once 'access-test-runner.php';
			$this->access_test_runner = new ameAccessTestRunner($this, $this->get);
		}

		//Additional links below the plugin description.
		add_filter('plugin_row_meta', array($this, 'add_plugin_row_meta_links'), 10, 2);

		//Utility actions. Modules can use them in their templates.
		add_action('admin_menu_editor-display_tabs', array($this, 'display_editor_tabs'));
		add_action('admin_menu_editor-display_header', array($this, 'display_settings_page_header'));
		add_action('admin_menu_editor-display_footer', array($this, 'display_settings_page_footer'));

	}
	
	function init_finish() {
		parent::init_finish();
		$should_save_options = false;

		//If we have no stored settings for this version of the plugin, try importing them
		//from other versions (i.e. the free or the Pro version).
		if ( !$this->load_options() ){
			$this->import_settings();
			$should_save_options = true;
		}

		//Track first install time.
        if ( !isset($this->options['first_install_time']) ) {
			$this->options['first_install_time'] = time();
			$should_save_options = true;
        }

		if ( $this->options['plugin_db_version'] < $this->plugin_db_version ) {
			/* Put any activation code here. */

			$this->options['plugin_db_version'] = $this->plugin_db_version;
			$should_save_options = true;
		}

		if ( $should_save_options ) {
			//Skip saving options if the plugin hasn't been fully activated yet.
			if ( $this->is_plugin_active($this->plugin_basename) ) {
				$this->save_options();
			} else {
				//Yes, this method can actually run before WP updates the list of active plugins. That means functions
				//like is_plugin_active_for_network() will return false. As as result, we can't determine whether
				//the plugin has been network-activated yet, so lets skip setting up the default config until
				//the next page load.
			}
		}

		//This is here and not in init() because it relies on $options being initialized.
		if ( $this->options['security_logging_enabled'] ) {
			add_action('admin_notices', array($this, 'display_security_log'));
		}

		if ( did_action('plugins_loaded') ) {
			$this->load_modules();
		} else {
			add_action('plugins_loaded', array($this, 'load_modules'), 11);
		}
	}

	public function load_modules() {
		//Modules
		foreach($this->get_active_modules() as $module) {
			/** @noinspection PhpIncludeInspection */
			include ($module['path']);
			if ( !empty($module['className']) ) {
				new $module['className']($this);
			}
		}

		//Set up the tabs for the menu editor page. Many tabs are provided by modules.
		$firstTabs = array('editor' => 'Admin Menu');
		if ( is_network_admin() ) {
			//TODO: This could be in extras.php
			$firstTabs = array('network-admin-menu' => 'Network Admin Menu');
		}
		$this->tabs = apply_filters('admin_menu_editor-tabs', $firstTabs);
		//The "Settings" tab is always last.
		$this->tabs['settings'] = 'Settings';
	}

  /**
   * Import settings from a different version of the plugin.
   * 
   * @return bool True if settings were imported successfully, False otherwise
   */
	function import_settings(){
		$possible_names = array('ws_menu_editor', 'ws_menu_editor_pro');
		foreach($possible_names as $option_name){
			if ( $this->load_options($option_name) ){
				return true;
			}
		}
		return false;
	}

  /**
   * Create a configuration page and load the custom menu
   *
   * @return void
   */
	function hook_admin_menu(){
		global $menu, $submenu;

		//Compatibility fix for Shopp 1.2.9. This plugin has an "admin_menu" hook (Flow::menu) that adds another
		//"admin_menu" hook (AdminFlow::taxonomies) when it runs. Basically, it indirectly modifies the global
		//$wp_filters['admin_menu'] array while WordPress is iterating it (nasty!). Due to how PHP arrays are
		//implemented and how do_action() works, this second hook is the very last one to run, even after hooks
		//with a lower priority.
		//The only way we can see the changes made by the second hook is to do the same thing.
		static $firstRunSkipped = false;
		if ( !$firstRunSkipped && class_exists('Flow') ) {
			add_action(current_filter(), array($this, 'hook_admin_menu'), $this->magic_hook_priority + 1);
			$firstRunSkipped = true;
			return;
		}

		//Menu reset (for emergencies). Executed by accessing http://example.com/wp-admin/?reset_admin_menu=1
		$reset_requested = isset($this->get['reset_admin_menu']) && $this->get['reset_admin_menu'];
		if ( $reset_requested && $this->current_user_can_edit_menu() ){
			$this->set_custom_menu(null);
		}
		
		//The menu editor is only visible to users with the manage_options privilege.
		//Or, if the plugin is installed in mu-plugins, only to the site administrator(s). 
		if ( $this->current_user_can_edit_menu() ){
			$this->log_security_note('Current user can edit the admin menu.');

			//Determine the current menu editor page tab.
			reset($this->tabs);
			$this->current_tab = isset($this->get['sub_section']) ? strval($this->get['sub_section']) : key($this->tabs);
			$tab_title = '';
			if ($this->current_tab !== 'editor' && isset($this->tabs[$this->current_tab])) {
				$tab_title = ' - ' . $this->tabs[$this->current_tab];
			}

			$parent_slug = is_network_admin() ? 'settings.php' : 'options-general.php';

			$page = add_submenu_page(
				$parent_slug,
				apply_filters('admin_menu_editor-self_page_title', 'Menu Editor') . $tab_title,
				apply_filters('admin_menu_editor-self_menu_title', 'Menu Editor'), 
				apply_filters('admin_menu_editor-capability', 'manage_options'),
				'menu_editor', 
				array($this, 'page_menu_editor')
			);
			//Output our JS & CSS on that page only
			add_action("admin_print_scripts-$page", array($this, 'enqueue_scripts'), 1);
			add_action("admin_print_styles-$page", array($this, 'enqueue_styles'));

			//Make sure Lodash doesn't conflict with the copy of Underscore that's bundled with WordPress.
			add_filter('script_loader_tag', array($this, 'lodash_noconflict'), 10, 2); //Filter exists since WP 4.1.

			//Compatibility fix for All In One Event Calendar; see the callback for details.
			add_action("admin_print_scripts-$page", array($this, 'dequeue_ai1ec_scripts'));

			//Compatibility fix for Participants Database.
			add_action("admin_print_scripts-$page", array($this, 'dequeue_pd_scripts'));

			//Experimental compatibility fix for Ultimate TinyMCE
			add_action("admin_print_scripts-$page", array($this, 'remove_ultimate_tinymce_qtags'));

			//Make a placeholder for our screen options (hacky)
			$screen_hook_name = $page;
			if ( is_network_admin() ) {
				$screen_hook_name .= '-network';
			}
			if ( $this->current_tab === 'editor' ) {
				add_meta_box("ws-ame-screen-options", "[AME placeholder]", '__return_false', $screen_hook_name);
			}
		}

		//Compatibility fix for the WooCommerce order count bubble. Must be run before storing or processing $submenu.
		$this->apply_woocommerce_order_count_fix();

		//Store the "original" menus for later use in the editor
		$this->default_wp_menu = $menu;
		$this->default_wp_submenu = $submenu;

		//Compatibility fix for bbPress.
		$this->apply_bbpress_compat_fix();
		//Compatibility fix for WooCommerce (woo).
		$this->apply_woocommerce_compat_fix();
		//Compatibility fix for WordPress Mu Domain Mapping.
		$this->apply_wpmu_domain_mapping_fix();
		//Compatibility fix for Divi Training.
		$this->apply_divi_training_fix();
		//As of WP 3.5, the "Links" menu is hidden by default.
		if ( !current_user_can('manage_links') ) {
			$this->remove_link_manager_menus();
		}

		//Generate item templates from the default menu.
		$templateBuilder = new ameMenuTemplateBuilder();
		$this->item_templates = $templateBuilder->build(
			$this->default_wp_menu,
			$this->default_wp_submenu,
			$this->menu_url_blacklist
		);

		//Store the default order for later. It will be used when (re)inserting unused items into the menu.
		$this->relative_template_order = $templateBuilder->getRelativeTemplateOrder();

		//Add extra templates that are not part of the normal menu.
		$this->item_templates = $this->add_special_templates($this->item_templates);
		//TODO: It would be nice to add the "Delete Site" item on multisite when on the main site.

		//Is there a custom menu to use?
		$custom_menu = $this->load_custom_menu();
		if ( $custom_menu !== null ){
			//Merge in data from the default menu
			$custom_menu['tree'] = $this->menu_merge($custom_menu['tree']);

			//Save the merged menu for later - the editor page will need it
			$this->merged_custom_menu = $custom_menu;

			//Convert our custom menu to the $menu + $submenu structure used by WP.
			//Note: This method sets up multiple internal fields and may cause side-effects.
			$this->user_cap_cache_enabled = true;
			$this->build_custom_wp_menu($this->merged_custom_menu['tree']);
			$this->user_cap_cache_enabled = false;

			if ( $this->is_access_test ) {
				$this->access_test_runner['wasCustomMenuApplied'] = true;
				$this->access_test_runner->setCurrentMenuItem($this->get_current_menu_item());
			}

			if ( !$this->user_can_access_current_page() ) {
				$this->log_security_note('DENY access.');
				if ( $this->is_access_test ) {
					$this->access_test_runner['userCanAccessCurrentPage'] = false;
				}

				$message = 'You do not have sufficient permissions to access this admin page.';

				if ( ($this->options['error_verbosity'] >= self::VERBOSITY_NORMAL) ) {
					$current_item = $this->get_current_menu_item();
					if ( isset($current_item, $current_item['access_decision_reason']) ) {
						$message .= sprintf(
							'<p>Reason: %s</p>',
							htmlentities($current_item['access_decision_reason'])
						);
					}
				}

				if ($this->options['security_logging_enabled']
					|| ($this->options['error_verbosity'] >= self::VERBOSITY_VERBOSE)
				) {
					$message .= '<p><strong>Admin Menu Editor security log</strong></p>';
					$message .= $this->get_formatted_security_log();
				}
				do_action('admin_page_access_denied');
				wp_die($message);
			} else {
				$this->log_security_note('ALLOW access.');
				if ( $this->is_access_test ) {
					$this->access_test_runner['userCanAccessCurrentPage'] =
						($this->access_test_runner['currentMenuItem'] !== null);
				}
			}

			//Replace the admin menu just before it is displayed and restore it afterwards.
			//The fact that replace_wp_menu() is attached to the 'parent_file' hook is incidental;
			//there just wasn't any other, more suitable hook available.
			add_filter('parent_file', array($this, 'replace_wp_menu'));
			add_action('adminmenu', array($this, 'restore_wp_menu'));

			//A compatibility hack for Ozh's Admin Drop Down Menu. Make sure it also sees the modified menu.
			$ozh_adminmenu_priority = has_action('in_admin_header', 'wp_ozh_adminmenu');
			if ( $ozh_adminmenu_priority !== false ) {
				add_action('in_admin_header', array($this, 'replace_wp_menu'), $ozh_adminmenu_priority - 1);
				add_action('in_admin_header', array($this, 'restore_wp_menu'), $ozh_adminmenu_priority + 1);
			}
		} else {
			do_action('admin_menu_editor-menu_replacement_skipped');
		}
	}

	/**
	 * Replace the current WP menu with our custom one.
	 *
	 * @param string $parent_file Ignored. Required because this method is a hook for the 'parent_file' filter.
	 * @return string Returns the $parent_file argument.
	 */
	public function replace_wp_menu($parent_file = '') {
		global $menu, $submenu;

		$this->old_wp_menu = $menu;
		$this->old_wp_submenu = $submenu;

		$menu = $this->custom_wp_menu;
		$submenu = $this->custom_wp_submenu;

		$this->user_cap_cache_enabled = true;
		list($menu, $submenu) = $this->filter_menu($menu, $submenu);
		$this->user_cap_cache_enabled = false;

		do_action('admin_menu_editor-menu_replaced');
		return $parent_file;
	}

	/**
	 * Restore the default WordPress menu that was replaced using replace_wp_menu().
	 *
	 * @return void
	 */
	public function restore_wp_menu() {
		global $menu, $submenu;
		$menu = $this->old_wp_menu;
		$submenu = $this->old_wp_submenu;
	}

	/**
	 * Filter a menu so that it can be handed to _wp_menu_output(). This method basically
	 * emulates the filtering that WordPress does in /wp-admin/includes/menu.php, with a few
	 * additions of our own.
	 *
	 * - Removes inaccessible items and superfluous separators.
	 *
	 * - Sets accessible items to a capability that the user is guaranteed to have to prevent
	 *   _wp_menu_output() from choking on plugin-specific capabilities like "cap1,cap2+not:cap3".
	 *
	 * - Adds position-dependent CSS classes.
	 *
	 * @param array $menu
	 * @param array $submenu
	 * @return array An array with two items - the filtered menu and submenu.
	 */
	private function filter_menu($menu, $submenu) {
		global $_wp_menu_nopriv; //Caution: Modifying this array could lead to unexpected consequences.

		//Remove sub-menus which the user shouldn't be able to access,
		//and ensure the rest are visible.
		foreach ($submenu as $parent => $items) {
			foreach ($items as $index => $data) {
				if ( ! $this->current_user_can($data[1]) ) {
					unset($submenu[$parent][$index]);
					$_wp_submenu_nopriv[$parent][$data[2]] = true;
				} else {
					//The menu might be set to some kind of special capability that is only valid
					//within this plugin and not WP in general. Ensure WP doesn't choke on it.
					//(This is safe - we'll double-check the caps when the user tries to access a page.)
					$submenu[$parent][$index][1] = 'exist'; //All users have the 'exist' cap.
				}
			}

			if ( empty($submenu[$parent]) ) {
				unset($submenu[$parent]);
			}
		}

		//Remove consecutive submenu separators. This can happen if there are separators around a menu item
		//that is not accessible to the current user.
		foreach ($submenu as $parent => $items) {
			$found_separator = false;
			foreach ($items as $index => $item) {
				//Separator have a dummy #anchor as a URL. See wsMenuEditorExtras::create_submenu_separator().
				if (strpos($item[2], '#submenu-separator-') === 0) {
					if ( $found_separator ) {
						unset($submenu[$parent][$index]);
					}
					$found_separator = true;
				} else {
					$found_separator = false;
				}
			}
		}

		//Remove menus that have no accessible sub-menus and require privileges that the user does not have.
		//Ensure the rest are visible. Run re-parent loop again.
		foreach ( $menu as $id => $data ) {
			if ( ! $this->current_user_can($data[1]) ) {
				$_wp_menu_nopriv[$data[2]] = true;
			} else {
				$menu[$id][1] = 'exist';
			}

			//If there is only one submenu and it is has same destination as the parent,
			//remove the submenu.
			if ( ! empty( $submenu[$data[2]] ) && 1 == count ( $submenu[$data[2]] ) ) {
				$subs = $submenu[$data[2]];
				$first_sub = array_shift($subs);
				if ( $data[2] == $first_sub[2] ) {
					unset( $submenu[$data[2]] );
				}
			}

			//If submenu is empty...
			if ( empty($submenu[$data[2]]) ) {
				// And user doesn't have privs, remove menu.
				if ( isset( $_wp_menu_nopriv[$data[2]] ) ) {
					unset($menu[$id]);
				}
			}
		}
		unset($id, $data, $subs, $first_sub);

		//Remove any duplicated separators
		$separator_found = false;
		foreach ( $menu as $id => $data ) {
			if ( 0 == strcmp('wp-menu-separator', $data[4] ) ) {
                if ($separator_found) {
                    unset($menu[$id]);
                }
                $separator_found = true;
            } else {
				$separator_found = false;
			}
		}
		unset($id, $data);

		//Remove the last menu item if it is a separator.
		$last_menu_key = array_keys( $menu );
		$last_menu_key = array_pop( $last_menu_key );
		if (!empty($menu) && 'wp-menu-separator' == $menu[$last_menu_key][4]) {
			unset($menu[$last_menu_key]);
		}
		unset( $last_menu_key );

		//Add display-specific classes like "menu-top-first" and others.
		$menu = add_menu_classes($menu);

		return array($menu, $submenu);
	}

	public function register_base_dependencies() {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;

		$this->register_jquery_plugins();

		//Lodash library
		wp_register_auto_versioned_script('ame-lodash', plugins_url('js/lodash.min.js', $this->plugin_file));

		//Knockout
		wp_register_auto_versioned_script('knockout', plugins_url('js/knockout.js', $this->plugin_file));

		//Actor manager.
		wp_register_auto_versioned_script(
			'ame-actor-manager',
			plugins_url('js/actor-manager.js', $this->plugin_file),
			array('ame-lodash')
		);

		$roles = array();

		$wp_roles = ameRoleUtils::get_roles();
		foreach($wp_roles->roles as $role_id => $role) {
			$role['capabilities'] = $this->castValuesToBool($role['capabilities']);
			$roles[$role_id] = $role;
		}

		//Known users.
		$users = array();
		$current_user = wp_get_current_user();
		$logins_to_include = apply_filters('admin_menu_editor-users_to_load', array());

		//Always include the current user.
		$logins_to_include[] = $current_user->get('user_login');
		$logins_to_include = array_unique($logins_to_include);

		//Load user details.
		foreach($logins_to_include as $login) {
			$user = get_user_by('login', $login);
			if ( !empty($user) ) {
				$users[$login] = $this->user_to_property_map($user);
			}
		}

		//Compatibility workaround: Get the real roles of the current user even if other plugins corrupt the list.
		$users[$current_user->get('user_login')]['roles'] = array_values($this->get_user_roles($current_user));

		$suspected_meta_caps = $this->detect_meta_caps($roles, $users);

		//The current user has all of the meta caps. That's how we know they're meta caps and not just regular
		//capabilities that simply haven't been granted to anyone.
		$users[$current_user->get('user_login')]['meta_capabilities'] = $suspected_meta_caps;

		//TODO: Include currentUserLogin
		$actor_data = array(
			'roles' => $roles,
			'users' => $users,
			'isMultisite' => is_multisite(),
			'capPower' => $this->load_cap_power(),
			'suspectedMetaCaps' => $suspected_meta_caps,
		);
		wp_localize_script('ame-actor-manager', 'wsAmeActorData', $actor_data);

		//Modules
		wp_register_auto_versioned_script(
			'ame-access-editor',
			plugins_url('modules/access-editor/access-editor.js', $this->plugin_file),
			array('jquery', 'ame-lodash')
		);

		//Let extras register their scripts.
		do_action('admin_menu_editor-register_scripts');
	}

	/**
	 * @access private
	 */
	public function register_jquery_plugins() {
		//jQuery JSON plugin
		wp_register_auto_versioned_script('jquery-json', plugins_url('js/jquery.json.js', $this->plugin_file), array('jquery'));
		//jQuery sort plugin
		wp_register_auto_versioned_script('jquery-sort', plugins_url('js/jquery.sort.js', $this->plugin_file), array('jquery'));
		//qTip2 - jQuery tooltip plugin
		wp_register_auto_versioned_script('jquery-qtip', plugins_url('js/jquery.qtip.min.js', $this->plugin_file), array('jquery'));
		//jQuery Form plugin. This is a more recent version than the one included with WP.
		wp_register_auto_versioned_script('ame-jquery-form', plugins_url('js/jquery.form.js', $this->plugin_file), array('jquery'));
		//jQuery cookie plugin
		wp_register_auto_versioned_script('jquery-cookie', plugins_url('js/jquery.biscuit.js', $this->plugin_file), array('jquery'));
	}

	/**
	 * Detect meta capabilities.
	 * This only works if the current user is an admin. In Multisite, they must be a Super Admin.
	 *
	 * @param array $roles
	 * @param array $users
	 * @return array [capability => true]
	 */
	private function detect_meta_caps($roles, $users) {
		if ( !$this->current_user_can_edit_menu() || !is_super_admin() ) {
			return array();
		}

		//Any capability that's assigned to a role probably isn't a meta capability.
		$allRealCaps = ameRoleUtils::get_all_capabilities();
		//Similarly, capabilities that are directly assigned to users are probably real.
		foreach($users as $user) {
			$allRealCaps = array_merge($allRealCaps, $user['capabilities']);
		}
		//Role IDs can also be used as capabilities.
		foreach($roles as $roleId => $role) {
			$allRealCaps[$roleId] = true;
		}

		//Collect all of the required capabilities from the admin menu.
		$menu = $this->get_default_menu();
		ameMenu::for_each($menu['tree'], array($this, 'collect_menu_cap'));

		//Any capability that's part of the admin menu but not assigned to any role or user
		//is probably a meta capability.
		$suspectedMetaCaps = array_diff_key($this->caps_used_in_menu, $allRealCaps);

		//The current user is an admin and should have access to everything. If they don't have a cap,
		//that's probably a non-meta cap that isn't enabled for *anyone*.
		$suspectedMetaCaps = array_filter(array_keys($suspectedMetaCaps), 'current_user_can');

		return array_fill_keys($suspectedMetaCaps, true);
	}

	/**
	 * @access private
	 * @param array $item
	 */
	public function collect_menu_cap($item) {
		if ( isset($item['defaults'], $item['defaults']['access_level']) ) {
			$this->caps_used_in_menu[$item['defaults']['access_level']] = true;
		}
	}

	/** @noinspection PhpUnusedPrivateMethodInspection */
	/**
	 * Unfinished feature: Detect which roles have which meta capabilities.
	 *
	 * Create a temp. user for each role, test which meta caps they have, then cache the results in a site option.
	 * Put this part in an AJAX request to avoid a massive slowdown (takes several seconds even on a fast PC).
	 *
	 * @param array $suspected_meta_caps
	 * @param string[] $roleIds
	 * @return array
	 */
	private function analyse_role_meta_caps($suspected_meta_caps, $roleIds) {
		//$start = microtime(true);
		$results = array();
		$real_current_user = wp_get_current_user();

		foreach($roleIds as $role_id) {
			$id = wp_insert_user(array(
				'role' => $role_id,
				'user_login' => wp_slash('ametemp_' . wp_generate_password(14)),
				'user_pass' => wp_generate_password(20),
				'display_name' => 'Temporary user created by AME',
			));
			$user = new WP_User($id);

			//Some plugins only check the current user and ignore the user ID passed to the "user_has_cap" filter.
			//To account for cases like that, we need to also change the current user.
			wp_set_current_user($user->ID);

			$results[$role_id] = array();
			foreach($suspected_meta_caps as $meta_cap => $ignored) {
				$results[$role_id][$meta_cap] = $user->has_cap($meta_cap);
			}

			wp_delete_user($id);
		}

		//Restore the original user.
		wp_set_current_user($real_current_user->ID);

		/*$elapsed = microtime(true) - $start;
		printf('Meta cap analysis: %.2f ms<br>', $elapsed * 1000);*/
		return $results;
	}

	/**
	  * Add the JS required by the editor to the page header
	  *
	  * @return void
	  */
	function enqueue_scripts() {
		//Optimization: Remove wp-emoji.js from the plugin page. wpEmoji makes DOM manipulation slow because
		//it tracks *all* DOM changes using MutationObserver.
		remove_action('admin_print_scripts', 'print_emoji_detection_script');

		//Workaround: Suppress a buggy "lets add a 'defer' attribute to all <script> tags" filter.
		//It's been going around the web and breaking AME installations by producing invalid HTML.
		remove_filter('clean_url', 'defer_parsing_of_js', 11);

		$this->register_base_dependencies();

		//Move admin notices (e.g. "Settings saved") below editor tabs.
		//This is a separate script because it has to run after common.js which is loaded in the page footer.
		wp_enqueue_auto_versioned_script(
			'ame-editor-tab-fix',
			plugins_url('js/editor-tab-fix.js', $this->plugin_file),
			array('jquery', 'common'),
			true
		);

		//Editor's scripts
		$editor_dependencies = array(
			'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-ui-tabs',
			'ame-jquery-form', 'jquery-ui-droppable', 'jquery-qtip',
			'jquery-sort', 'jquery-json', 'jquery-cookie',
			'wp-color-picker', 'ame-lodash', 'ame-access-editor', 'ame-actor-manager',
			'ame-actor-selector',
		);
		wp_register_auto_versioned_script(
			'menu-editor',
			plugins_url('js/menu-editor.js', $this->plugin_file),
			apply_filters('admin_menu_editor-editor_script_dependencies', $editor_dependencies)
		);

		do_action('admin_menu_editor-enqueue_scripts-' . $this->current_tab);

		//Actors (roles and users) are used in the permissions UI, so we need to pass them along.
		//TODO: This is redundant. Consider using the actor manager or selector instead.
		$actors = array();

		$wp_roles = ameRoleUtils::get_roles();
		foreach($wp_roles->roles as $role_id => $role) {
			$actors['role:' . $role_id] = $role['name'];
		}

		if ( is_multisite() && is_super_admin() ) {
			$actors['special:super_admin'] = 'Super Admin';
		}

		$current_user = wp_get_current_user();
		$actors['user:' . $current_user->get('user_login')] = sprintf(
			'Current user (%s)',
			$current_user->get('user_login')
		);

		//Add only certain scripts to the settings sub-section.
		if ( $this->is_settings_page() ) {
			wp_enqueue_script('jquery-qtip');
			return;
		}

		//Add all scripts to our editor page, but not the settings sub-section
		//that shares the same page slug. Some of the scripts would crash otherwise.
		if ( !$this->is_editor_page() ) {
			return;
		}

		wp_enqueue_script('menu-editor');

		//We use WordPress media uploader to let the user upload custom menu icons (WP 3.5+).
		if ( function_exists('wp_enqueue_media') ) {
			wp_enqueue_media();
		}

		//Remove the default jQuery Form plugin to prevent conflicts with our custom version.
		wp_dequeue_script('jquery-form');

		//The editor will need access to some of the plugin data and WP data.
		$script_data = array(
			'imagesUrl' => plugins_url('images', $this->plugin_file),
			'adminAjaxUrl' => admin_url('admin-ajax.php'),
			'hideAdvancedSettings' => (boolean)$this->options['hide_advanced_settings'],
			'showExtraIcons' => true, //No longer used.
			'submenuIconsEnabled' => $this->options['submenu_icons_enabled'],

			'hideAdvancedSettingsNonce' => wp_create_nonce('ws_ame_save_screen_options'),
			'dashiconsAvailable' => wp_style_is('dashicons', 'registered'),
			'captionShowAdvanced' => 'Show advanced options',
			'captionHideAdvanced' => 'Hide advanced options',
			'wsMenuEditorPro' => $this->is_pro_version(), //Will be overwritten if extras are loaded
			'menuFormatName' => ameMenu::format_name,
			'menuFormatVersion' => ameMenu::format_version,

			'blankMenuItem' => ameMenuItem::blank_menu(),
			'itemTemplates' => $this->item_templates,
			'customItemTemplate' => array(
				'name' => '< Custom URL >',
				'defaults' => ameMenuItem::custom_item_defaults(),
			),

			'unclickableTemplateId' => ameMenuItem::unclickableTemplateId,
			'unclickableTemplateClass' => ameMenuItem::unclickableTemplateClass,

			'embeddedPageTemplateId' => ameMenuItem::embeddedPageTemplateId,

			'actors' => $actors,
			'currentUserLogin' => $current_user->get('user_login'),
			'selectedActor' => isset($this->get['selected_actor']) ? strval($this->get['selected_actor']) : null,

			'postTypes' => $this->get_post_type_details(),
			'taxonomies' => $this->get_taxonomy_details(),

			'showHints' => $this->get_hint_visibility(),
			'dashboardHidingConfirmationEnabled' => $this->options['dashboard_hiding_confirmation_enabled'],
			'disableDashboardConfirmationNonce' => wp_create_nonce('ws_ame_disable_dashboard_hiding_confirmation'),

			'getPagesNonce' => wp_create_nonce('ws_ame_get_pages'),
			'getPageDetailsNonce' => wp_create_nonce('ws_ame_get_page_details'),

			'selectedMenu'    => isset($this->get['selected_menu_url'])  ? strval($this->get['selected_menu_url']) : null,
			'selectedSubmenu' => isset($this->get['selected_submenu_url']) ? strval($this->get['selected_submenu_url']) : null,
			'expandSelectedMenu'    => isset($this->get['expand_menu']) && ($this->get['expand_menu'] === '1'),
			'expandSelectedSubmenu' => isset($this->get['expand_submenu']) && ($this->get['expand_submenu'] === '1'),

			'isDemoMode' => defined('IS_DEMO_MODE'),
			'isMasterMode' => defined('IS_MASTER_MODE'),
		);
		$script_data = apply_filters('admin_menu_editor-script_data', $script_data);
		wp_localize_script('menu-editor', 'wsEditorData', $script_data);
	}

	/**
	 * Convert a WP_User instance to an associative array with the keys defined
	 * in the AmeUserPropertyMap interface in actor-manager.ts.
	 *
	 * @param WP_User $user
	 * @return array
	 */
	public function user_to_property_map($user) {
		return array(
			'user_login' => $user->get('user_login'),
			'id' => $user->ID,
			'roles' => !empty($user->roles) ? array_values((array)($user->roles)) : array(),
			'capabilities' => $this->castValuesToBool($user->caps),
			'meta_capabilities' => array(),
			'display_name' => $user->display_name,
			'is_super_admin' => is_multisite() && is_super_admin($user->ID),
		);
	}

	/**
	 * Move editor scripts closer to the top of the script queue.
	 *
	 * This reduces the chances that JavaScript bugs in other plugins will crash the menu editor.
	 * For example, if another plugin's script loads first and crashes in a $(document).ready()
	 * handler, the editor's $(document).ready() handler will never be run. This will make the UI
	 * unusable because the menu list will not render, etc. Loading our scripts first makes that
	 * less likely.
	 */
	public function move_editor_scripts_to_top() {
		global $wp_scripts;
		if ( !(isset($wp_scripts) && ($wp_scripts instanceof WP_Scripts)) ) {
			$wp_scripts = new WP_Scripts();
		}

		//Sanity check. If the wp_scripts implementation has changed significantly, don't touch it.
		if ( !isset($wp_scripts->queue) || (!is_array($wp_scripts->queue) || ($wp_scripts->queue instanceof Traversable)) ) {
			return;
		}

		//We want to load our scripts *after* WordPress core scripts in case we depend on some core feature.
		$common_key = array_search('common', $wp_scripts->queue);
		$admin_bar_key = array_search('admin-bar', $wp_scripts->queue);
		if ( ($common_key === false) && ($admin_bar_key === false) ) {
			return;
		}
		$last_core_key = max($admin_bar_key, $common_key);

		//Move only those scripts that are actually in the queue.
		$handles_to_move = array();
		foreach(array('menu-editor', 'ame-helper-script') as $handle) {
			$key = array_search($handle, $wp_scripts->queue);
			if ($key !== false) {
				$handles_to_move[] = $handle;
				unset($wp_scripts->queue[$key]); //Remove the script from its old position.
			}
		}

		//Insert the scripts after core script(s).
		array_splice($wp_scripts->queue, $last_core_key + 1, 0, $handles_to_move);
	}

	/**
	 * Revert the "_" variable to its original value and store Lodash in "wsAmeLodash" instead.
	 *
	 * @param string $tag
	 * @param string $script_handle
	 * @return string
	 */
	public function lodash_noconflict($tag, $script_handle) {
		if ($script_handle === 'ame-lodash') {
			$tag .= '<script type="text/javascript">wsAmeLodash = _.noConflict();</script>';
		}
		return $tag;
	}

	/**
	 * Compatibility workaround for All In One Event Calendar 1.8.3-premium.
	 *
	 * The event calendar plugin is known to crash Admin Menu Editor Pro 1.40. The exact cause
	 * of the crash is unknown, but we can prevent it by removing AIOEC scripts from the menu
	 * editor page.
	 *
	 * This should not affect the functionality of the event calendar plugin. The scripts
	 * in question don't seem to do anything on pages not related to the event calendar. AIOEC
	 * just loads them indiscriminately on all pages.
	 */
	public function dequeue_ai1ec_scripts() {
		wp_dequeue_script('ai1ec_requirejs');
		wp_dequeue_script('ai1ec_common_backend');
		wp_dequeue_script('ai1ec_add_new_event_require');
	}

	/**
	 * Compatibility workaround for Participants Database 1.4.5.2.
	 *
	 * Participants Database loads its settings JavaScript on every page in the "Settings" menu,
	 * not just its own. It doesn't bother to also load the script's dependencies, though, so
	 * the script crashes *and* it breaks the menu editor by way of collateral damage.
	 *
	 * Fix by forcibly removing the offending script from the queue.
	 */
	public function dequeue_pd_scripts() {
		if ( is_plugin_active('participants-database/participants-database.php') ) {
			wp_dequeue_script('settings_script');
		}
	}

	public function remove_ultimate_tinymce_qtags() {
		remove_action('admin_print_footer_scripts', 'jwl_ult_quicktags');
	}

	 /**
	  * Add the editor's CSS file to the page header
	  *
	  * @return void
	  */
	function enqueue_styles(){
		wp_enqueue_auto_versioned_style('jquery-qtip-syle', plugins_url('css/jquery.qtip.min.css', $this->plugin_file), array());

		wp_register_auto_versioned_style('menu-editor-base-style', plugins_url('css/menu-editor.css', $this->plugin_file));
		wp_register_auto_versioned_style(
			'menu-editor-colours-classic',
			plugins_url('css/style-classic.css', $this->plugin_file),
			array('menu-editor-base-style')
		);
		wp_register_auto_versioned_style(
			'menu-editor-colours-wp-grey',
			plugins_url('css/style-wp-grey.css', $this->plugin_file),
			array('menu-editor-base-style')
		);
		wp_register_auto_versioned_style(
			'menu-editor-colours-modern-one',
			plugins_url('css/style-modern-one.css', $this->plugin_file),
			array('menu-editor-base-style')
		);

		//WordPress introduced a new screen meta button style in WP 3.8.
		//We have two different stylesheets - one for 3.8+ and one for backwards compatibility.
		wp_register_auto_versioned_style('menu-editor-screen-meta', plugins_url('css/screen-meta.css', $this->plugin_file));
		wp_register_auto_versioned_style('menu-editor-screen-meta-old', plugins_url('css/screen-meta-old-wp.css', $this->plugin_file));

		if ( isset($GLOBALS['wp_version']) && version_compare($GLOBALS['wp_version'], '3.8-RC1', '<') ) {
			wp_enqueue_style('menu-editor-screen-meta-old');
		} else {
			wp_enqueue_style('menu-editor-screen-meta');
		}

		$scheme = $this->options['ui_colour_scheme'];
		wp_enqueue_style('menu-editor-colours-' . $scheme);
		wp_enqueue_style('wp-color-picker');

		do_action('admin_menu_editor-enqueue_styles-' . $this->current_tab);
	}

	/**
	 * Set and save a new custom menu for the current site.
	 *
	 * @param array|null $custom_menu
	 * @param string|null $config_id Supported values: 'network-admin', 'global' or 'site'
	 */
	function set_custom_menu($custom_menu, $config_id = null) {
		if ( $config_id === null ) {
			$config_id = $this->guess_menu_config_id();
		}

		$custom_menu = apply_filters('ame_pre_set_custom_menu', $custom_menu);

		$previous_custom_menu = $this->load_custom_menu($config_id);
		$this->update_wpml_strings($previous_custom_menu, $custom_menu);

		if ( !empty($custom_menu) ) {
			$custom_menu['prebuilt_virtual_caps'] = $this->build_virtual_capability_list($custom_menu);
		}

		if ( !empty($custom_menu) && $this->options['compress_custom_menu'] ) {
			$custom_menu = ameMenu::compress($custom_menu);
		}

		if ($config_id === 'site') {
			$site_specific_options = get_option($this->option_name);
			if ( !is_array($site_specific_options) ) {
				$site_specific_options = array();
			}
			$site_specific_options['custom_menu'] = $custom_menu;
			update_option($this->option_name, $site_specific_options);
		} else if ($config_id === 'global') {
			$this->options['custom_menu'] = $custom_menu;
			$this->save_options();

		} else if ($config_id === 'network-admin' ) {
			$this->options['custom_network_menu'] = $custom_menu;
			$this->save_options();
		} else {
			throw new LogicException(sprintf('Invalid menu configuration ID: "%s"', $config_id));
		}

		$this->loaded_menu_config_id = null;
		$this->cached_custom_menu = null;
		$this->cached_virtual_caps = null;
		$this->cached_user_caps = array();
	}

	/**
	 * Load the current custom menu for this site, if any.
	 *
	 * @param null $config_id
	 * @return array|null Either a menu in the internal format, or NULL if there is no custom menu available.
	 * @throws InvalidMenuException
	 */
	public function load_custom_menu($config_id = null) {
		if ( $config_id === null ) {
			$config_id = $this->guess_menu_config_id();
		}

		if ( ($this->cached_custom_menu !== null) && ($this->loaded_menu_config_id === $config_id) ) {
			return $this->cached_custom_menu;
		}

		$this->loaded_menu_config_id = $config_id;

		if ( $this->is_access_test ) {
			return $this->test_menu;
		}

		if ( $config_id === 'network-admin' ) {
			if ( empty($this->options['custom_network_menu']) ) {
				return null;
			}
			$this->cached_custom_menu = ameMenu::load_array($this->options['custom_network_menu']);
		} else if ( $config_id === 'site' ) {
			$site_specific_options = get_option($this->option_name, null);
			if ( is_array($site_specific_options) && isset($site_specific_options['custom_menu']) ) {
				$this->cached_custom_menu = ameMenu::load_array($site_specific_options['custom_menu']);
			}
		} else {
			if ( empty($this->options['custom_menu']) ) {
				return null;
			}
			$this->cached_custom_menu = ameMenu::load_array($this->options['custom_menu']);
		}

		return $this->cached_custom_menu;
	}

	private function guess_menu_config_id() {
		if ( is_network_admin() ) {
			return 'network-admin';
		} elseif ( $this->should_use_site_specific_menu() ) {
			return 'site';
		} else {
			return 'global';
		}
	}

	/**
	 * @return string|null
	 */
	public function get_loaded_menu_config_id() {
		return $this->loaded_menu_config_id;
	}

	/**
	 * Determine if we should use a site-specific admin menu configuration
	 * for the current site, or fall back to the global config.
	 *
	 * @return bool True = use the site-specific config (if any), false = use the global config.
	 */
	protected function should_use_site_specific_menu() {
		if ( !is_multisite() ) {
			//If this is a single-site WP installation then there's really
			//no difference between "site-specific" and "global".
			return false;
		}
		return ($this->options['menu_config_scope'] === 'site');
	}

	function save_options() {
		if ( $this->is_access_test ) {
			//Don't change live settings during an access test.
			return false;
		}
		return parent::save_options();
	}

	/**
	 * Determine if the current user may use the menu editor.
	 * 
	 * @return bool
	 */
	public function current_user_can_edit_menu(){
		$access = $this->options['plugin_access'];

		if ( $access === 'super_admin' ) {
			return is_super_admin();
		} else if ( $access === 'specific_user' ) {
			return get_current_user_id() == $this->options['allowed_user_id'];
		} else {
			$capability = apply_filters('admin_menu_editor-capability', $access);
			return current_user_can($capability);
		}
	}

	/**
	 * Reset plugin access if the only allowed user no longer exists.
	 *
	 * Some people use security plugins like iThemes Security to replace the default admin account
	 * with a new one or change the user ID. This can be a problem when AME is configured to allow
	 * only one user to edit the admin menu. Deleting that user ID makes the plugin inaccessible.
	 * As a workaround, allow any admin if the configured user is missing.
	 *
	 * @internal
	 * @param string $login
	 * @param WP_User $current_user
	 */
	public function maybe_reset_plugin_access(/** @noinspection PhpUnusedParameterInspection */ $login = null, $current_user = null) {
		if ( ($this->options['plugin_access'] !== 'specific_user') || !$current_user || !$current_user->exists() ) {
			return;
		}

		//For performance, only run this check when an admin logs in.
		//Note that current_user_can() and friends don't work at this point in the login flow.
		$current_user_is_admin = is_multisite()
			? is_super_admin($current_user->ID)
			: $current_user->has_cap('manage_options');

		if ( !$current_user_is_admin ) {
			return;
		}

		$allowed_user = get_user_by('id', $this->options['allowed_user_id']);
		if ( !$allowed_user || !$allowed_user->exists() ) {
			//The allowed user no longer exists. Allow any administrator to use the plugin.
			$this->options['plugin_access'] = 'manage_options';
			$this->save_options();
		}
	}
	
	/**
	 * Apply the custom page title, if any.
	 *
	 * This is a callback for the "admin_title" filter. It will change the browser window/tab
	 * title (i.e. <title>), but not the title displayed on the admin page itself.
	 * 
	 * @param string $admin_title The current admin title (full).
	 * @param string $title The current page title. 
	 * @return string New admin title.
	 */
	function hook_admin_title($admin_title, $title){
		$item = $this->get_current_menu_item();
		if ( $item === null ) {
			return $admin_title;
		}

		$custom_title = null;

		//Check if the we have a custom title for this page.
		$default_title = isset($item['defaults']['page_title']) ? $item['defaults']['page_title'] : '';
		if ( !empty($item['page_title']) && $item['page_title'] != $default_title ) {
			$custom_title = $item['page_title'];
		}

		//Alternatively, use the custom menu title if the default page title is empty (as is usually
		//the case with core menus) or matches the default menu title (which is typical for plugins).
		//This saves the user a little bit of time, and, presumably, they'd want the titles to match.
		$default_menu_title = isset($item['defaults']['menu_title']) ? $item['defaults']['menu_title'] : '';
		if (
			!isset($custom_title)
			&& !empty($item['menu_title'])
			&& ($item['menu_title'] !== $default_menu_title)
			&& (($default_menu_title === $default_title) || ($default_title === ''))
		) {
			$custom_title = strip_tags($item['menu_title']);
		}

		if ( isset($custom_title) ) {
			if ( empty($title) ) {
				$admin_title = $custom_title . $admin_title;
			} else {
				//Replace the first occurrence of the default title with the custom one.
				$title_pos = strpos($admin_title, $title);
				$admin_title = substr_replace($admin_title, $custom_title, $title_pos, strlen($title));
			}
		}

		return $admin_title;
	}

	/**
	 * Generate special menu templates and add them to the input template list.
	 *
	 * @param array $templates Template list.
	 * @return array Modified template list.
	 */
	private function add_special_templates($templates) {
		//Add a special template for unclickable menu items. These can be used as headers and such.
		$itemDefaults = ameMenuItem::custom_item_defaults();
		$unclickableDefaults = array_merge(
			$itemDefaults,
			array(
				'file' => '#' . ameMenuItem::unclickableTemplateClass,
				'url'  => '#' . ameMenuItem::unclickableTemplateClass,
				'css_class' => $itemDefaults['css_class'] . ' ' . ameMenuItem::unclickableTemplateClass,
				'menu_title' => 'Unclickable Menu',
			)
		);
		$templates[ameMenuItem::unclickableTemplateId] = array(
			'name' => '< None >',
			'used' => true,
			'defaults' => $unclickableDefaults,
		);

		if ( $this->is_pro_version() ) {
			$templates[ameMenuItem::embeddedPageTemplateId] = array(
				'name' => '< Embed WP page >',
				'used' => true,
				'defaults' => array_merge(
					$itemDefaults,
					array(
						'file' => '#automatically-generated',
						'url' => '#automatically-generated',
						'menu_title' => 'Embedded Page',
						'page_heading' => ameMenuItem::embeddedPagePlaceholderHeading,
					)
				)
			);

			//The Pro version has a [wp-logout-url] shortcode. Lets make it easier o use
			//by adding it to the "Target page" dropdown.
			$logoutDefaults = array_merge(
				ameMenuItem::basic_defaults(),
				array(
					'menu_title' => 'Logout',
					'file' => '[wp-logout-url]',
					'url'  => '[wp-logout-url]',
					'icon_url' => 'dashicons-migrate',
				)
			);
			$templates['>logout'] = array(
				'name' => 'Logout',
				'used' => true,
				'defaults' => $logoutDefaults,
			);
		}

		return $templates;
	}

  /**
   * Merge a custom menu with the current default WordPress menu. Adds/replaces defaults,
   * inserts new items and removes missing items.
   *
   * @uses self::$item_templates
   *
   * @param array $tree A menu in plugin's internal form
   * @return array Updated menu tree
   */
	function menu_merge($tree){
		//Iterate over all menus and submenus and look up default values
		//Also flag used and missing items.
		$orphans = array();

		//Build an index of menu positions so that we can quickly pick the right position for new/unused items.
		$positions_by_template = array();
		$following_separator_position = array();
		$previous_default_top_menu = null;

		foreach ($tree as &$topmenu){

			if ( !empty($topmenu['separator']) && isset($previous_default_top_menu) ) {
				$following_separator_position[$previous_default_top_menu] = ameMenuItem::get($topmenu, 'position', 0);
			}
			$previous_default_top_menu = null;

			if ( !ameMenuItem::get($topmenu, 'custom') ) {
				$template_id = ameMenuItem::template_id($topmenu);
				//Is this menu present in the default WP menu?
				if (isset($this->item_templates[$template_id])){
					//Yes, load defaults from that item
					$topmenu['defaults'] = $this->item_templates[$template_id]['defaults'];
					//Note that the original item was used
					$this->item_templates[$template_id]['used'] = true;
					//Add valid, non-custom items to the position index.
					$positions_by_template[$template_id] = ameMenuItem::get($topmenu, 'position', 0);
					$previous_default_top_menu = $template_id;
				} else {
					//Record the menu as missing, unless it's a menu separator
					if ( empty($topmenu['separator']) ){
						$topmenu['missing'] = true;

						$temp = ameMenuItem::apply_defaults($topmenu);
						$temp = $this->set_final_menu_capability($temp);
						$this->add_access_lookup($temp, 'menu', true);
                    }
					//Don't add missing menus to the index because they won't show up anyway.
				}
			}

			if (is_array($topmenu['items'])) {
				//Iterate over submenu items
				foreach ($topmenu['items'] as &$item){
					if ( !ameMenuItem::get($item, 'custom') ) {
						$template_id = ameMenuItem::template_id($item);

						//Is this item present in the default WP menu?
						if (isset($this->item_templates[$template_id])){
							//Yes, load defaults from that item
							$item['defaults'] = $this->item_templates[$template_id]['defaults'];
							$this->item_templates[$template_id]['used'] = true;
							//Add valid, non-custom items to the position index.
							$positions_by_template[$template_id] = ameMenuItem::get($item, 'position', 0);
							//We must move orphaned items elsewhere. Use the default location if possible.
							if ( isset($topmenu['missing']) && $topmenu['missing'] ) {
								$orphans[] = $item;
							}
						} else if ( empty($item['separator']) ) {
							//Record as missing, unless it's a menu separator
							$item['missing'] = true;

							$temp = ameMenuItem::apply_defaults($item);
							$temp = $this->set_final_menu_capability($temp);
							$this->add_access_lookup($temp, 'submenu', true);
                        }
					} else {
						//What if the parent of this custom item is missing?
						//Right now the custom item will just disappear.
					}
				}
			}
		}

		//If we don't unset these they will fuck up the next two loops where the same names are used.
		unset($topmenu);
		unset($item);

		//Now we have some items marked as missing, and some items in lookup arrays
		//that are not marked as used. Lets remove the missing items from the tree.
		$tree = ameMenu::remove_missing_items($tree);
		//TODO: What would happen if we kept missing items?

		//Lets merge in the unused items.
		$max_menu_position = !empty($positions_by_template) ? max($positions_by_template) : 100;
		$new_grant_access = $this->get_new_menu_grant_access();
		foreach ($this->item_templates as $template_id => $template){
			//Skip used menus and separators
			if ( !empty($template['used']) || !empty($template['defaults']['separator'])) {
				continue;
			}

			//Found an unused item. Build the tree entry.
			$entry = ameMenuItem::blank_menu();
			$entry['template_id'] = $template_id;
			$entry['defaults'] = $template['defaults'];
			$entry['unused'] = true; //Note that this item is unused

			$entry['grant_access'] = $new_grant_access;

			if ($this->options['unused_item_position'] === 'relative') {

				//Attempt to maintain relative menu order.
				$previous_item = $was_separated = null;
				if ( isset($this->relative_template_order[$template_id]) ) {
					$previous_item = $this->relative_template_order[$template_id]['previous_item'];
					$was_separated = $this->relative_template_order[$template_id]['was_previous_item_separated'];
				}

				if ( isset($previous_item, $positions_by_template[$previous_item]) ) {
					if ( $was_separated && isset($following_separator_position[$previous_item]) ) {
						//Desired order: previous item -> separator -> this item.
						$entry['position'] = $following_separator_position[$previous_item];
					} else {
						//Desired order: previous item -> this item.
						$entry['position'] = $positions_by_template[$previous_item];
						if ( isset($following_separator_position[$previous_item]) ) {
							//Now the separator is after this item, not the previous one.
							$following_separator_position[$template_id] = $following_separator_position[$previous_item];
							unset($following_separator_position[$previous_item]);
						}
					}
					$entry['position'] = strval(floatval($entry['position']) + 0.01);
				} else if ( $previous_item === '' ) {
					//Empty string = this was originally the first item.
					$entry['position'] = -1;
				} else {
					//Previous item is unknown or doesn't exist. Leave this item in its current, incorrect position.
				}

			} else {
				//Move unused entries to the bottom.
				$max_menu_position = $max_menu_position + 1;
				$entry['position'] = $max_menu_position;
			}
			$positions_by_template[$template_id] = ameMenuItem::get($entry, 'position', 0);

			//Add the new entry to the menu tree
			if ( isset($template['defaults']['parent']) ) {
				if ( isset($tree[$template['defaults']['parent']]) ) {
					//Okay, insert the item.
					$tree[$template['defaults']['parent']]['items'][] = $entry;
				} else {
					//This can happen if the original parent menu has been moved to a submenu.
					$tree[$template['defaults']['file']] = $entry;
				}
			} else {
				$tree[$template['defaults']['file']] = $entry;
			}
		}

		//Move orphaned items back to their original parents.
		foreach($orphans as $item) {
			$defaultParent = $item['defaults']['parent'];
			if ( isset($defaultParent) && isset($tree[$defaultParent]) ) {
				$tree[$defaultParent]['items'][] = $item;
			} else {
				//This can happen if the parent has been moved to a submenu.
				//Just put the orphan at the bottom of the menu.
				$tree[$item['defaults']['file']] = $item;
			}
		}

		//Resort the tree to ensure the found items are in the right spots
		$tree = ameMenu::sort_menu_tree($tree);

		//Order data is no longer necessary.
		$this->relative_template_order = null;

		return $tree;
	}

	/**
	 * Add a page and its required capability to the page access lookup.
	 *
	 * The lookup array is indexed by priority. Priorities (highest to lowest):
	 *      - Has custom permissions and a known template.
	 *      - Has custom permissions, template missing or can't be determined correctly.
	 *      - Default permissions.
	 *      - Everything else.
	 * Additionally, submenu items have slightly higher priority that top level menus.
	 * The desired end result is for menu items with custom permissions to override
	 * default menus.
	 *
	 * Note to self: If we were to keep items with an unknown template instead of throwing
	 * them away during the merge phase, we could simplify this considerably.
	 *
	 * @param array $item Menu item (with defaults already applied).
	 * @param string $item_type 'menu' or 'submenu'.
	 * @param bool $missing Whether the item template is missing or unknown.
	 */
	private function add_access_lookup($item, $item_type = 'menu', $missing = false) {
		if ( empty($item['url']) ) {
			return;
		}

		$has_custom_settings = !empty($item['grant_access']) || !empty($item['extra_capability']);
		$priority = 6;
		if ( $missing ) {
			if ( $has_custom_settings ) {
				$priority = 4;
			} else {
				return; //Don't even consider missing menus without custom access settings.
			}
		} else if ( $has_custom_settings ) {
			$priority = 2;
		}

		if ( $item_type == 'submenu' ) {
			$priority--;
		}

		//TODO: Include more details like menu title and template ID for debugging purposes (log output).
		$this->page_access_lookup[$item['url']][$priority] = $item['access_level'];
	}

	/**
	 * Get the access settings for menu items that are not part of the saved menu configuration.
	 *
	 * Typically, this applies to new menus that were added by recently activated plugins.
	 *
	 * @return array
	 */
	public function get_new_menu_grant_access() {
		if ( $this->options['unused_item_permissions'] === 'unchanged' ) {
			return array();
		}
		return apply_filters('admin_menu_editor-new_menu_grant_access', array());
	}

  /**
   * Generate WP-compatible $menu and $submenu arrays from a custom menu tree.
   * 
   * Side-effects: This function executes several filters that may modify global state.
   * Specifically, IFrame-handling callbacks in 'extras.php' will add add new hooks
   * and other menu-related structures.
   *
   * @uses WPMenuEditor::$custom_wp_menu Stores the generated top-level menu here.
   * @uses WPMenuEditor::$custom_wp_submenu Stores the generated sub-menu here.
   *
   * @uses WPMenuEditor::$title_lookups Generates a lookup list of page titles.
   * @uses WPMenuEditor::$reverse_item_lookup Generates a lookup list of url => menu item relationships.
   *
   * @param array $tree The new menu, in the internal tree format.
   * @return void
   */
	function build_custom_wp_menu($tree){
		$new_tree = array();
		$new_menu = array();
		$new_submenu = array();
		$this->title_lookups = array();
		
		//Prepare the top menu
		$first_nonseparator_found = false;
		foreach ($tree as $topmenu){

			//Skip leading menu separators. Fixes a superfluous separator showing up
			//in WP 3.0 (multisite mode) when there's a custom menu and the current user
			//can't access its first item ("Super Admin").
			if ( !empty($topmenu['separator']) && !$first_nonseparator_found ) {
				continue;
			}
			$first_nonseparator_found = true;

			$topmenu = $this->prepare_for_output($topmenu, 'menu');

			if ( empty($topmenu['separator']) ) {
				$this->title_lookups[$topmenu['file']] = !empty($topmenu['page_title']) ? $topmenu['page_title'] : $topmenu['menu_title'];
			}
				
			//Prepare the submenu of this menu
			$new_items = array();
			if( !empty($topmenu['items']) ){
				$items = $topmenu['items'];

				foreach ($items as $item) {
					$item = $this->prepare_for_output($item, 'submenu', $topmenu);
					$new_items[] = $item;

					//Make a note of the page's correct title so we can fix it later if necessary.
					$this->title_lookups[$item['file']] = !empty($item['page_title']) ? $item['page_title'] : $item['menu_title'];
				}

				//Sort by position
				usort($new_items, 'ameMenuItem::compare_position');
			}

			$topmenu['items'] = $new_items;
			$new_tree[] = $topmenu;
		}

		//Sort the menu by position
		uasort($new_tree, 'ameMenuItem::compare_position');

		//Use only the highest-priority capability for each URL.
		foreach($this->page_access_lookup as $url => $capabilities) {
			ksort($capabilities);
			$this->page_access_lookup[$url] = reset($capabilities);
		}

		if ( $this->is_access_test ) {
			$this->access_test_runner->onFinalTreeReady($new_tree);
		}

		//Convert the prepared tree to the internal WordPress format.
		foreach($new_tree as $topmenu) {
			$trueAccess = isset($this->page_access_lookup[$topmenu['url']]) ? $this->page_access_lookup[$topmenu['url']] : null;
			if ( ($trueAccess === 'do_not_allow') && ($topmenu['access_level'] !== $trueAccess) ) {
				$topmenu['access_level'] = $trueAccess;
				if ( isset($topmenu['access_check_log']) ) {
					$topmenu['access_check_log'][] = sprintf(
						'+ Override: There is a hidden menu item with the same URL (%1$s) but a higher priority.' .
						' Setting the capability to "%2$s".',
						$topmenu['url'],
						$trueAccess
					);
					$topmenu['access_check_log'][] = str_repeat('=', 79);
				}
			}

			if ( !isset($this->reverse_item_lookup[$topmenu['url']]) ) { //Prefer sub-menus.
				$this->reverse_item_lookup[$topmenu['url']] = $topmenu;
			}

			$has_submenu_icons = false;
			foreach($topmenu['items'] as $item) {
				$trueAccess = isset($this->page_access_lookup[$item['url']]) ? $this->page_access_lookup[$item['url']] : null;
				if ( ($trueAccess === 'do_not_allow') && ($item['access_level'] !== $trueAccess) ) {
					$item['access_level'] = $trueAccess;
					if ( isset($item['access_check_log']) ) {
						$item['access_check_log'][] = sprintf(
							'+ Override: There is a hidden menu item with the same URL (%1$s) but a higher priority.' .
							' Setting the capability to "%2$s".',
							$item['url'],
							$trueAccess
						);
						$item['access_check_log'][] = str_repeat('=', 79);
					}
				}

				$this->reverse_item_lookup[$item['url']] = $item;

				//Skip missing and hidden items
				if ( !empty($item['missing']) || !empty($item['hidden']) ) {
					continue;
				}

				//Keep track of which menus have items with icons. Ignore hidden items.
				$has_submenu_icons = $has_submenu_icons
					|| (!empty($item['has_submenu_icon']) && $item['access_level'] !== 'do_not_allow');

				$new_submenu[$topmenu['file']][] = $this->convert_to_wp_format($item);
			}

			//Skip missing and hidden menus.
			if ( !empty($topmenu['missing']) || !empty($topmenu['hidden']) ) {
				continue;
			}

			//The ame-has-submenu-icons class lets us change the appearance of all submenu items at once,
			//without having to add classes/styles to each item individually.
			if ( $has_submenu_icons && (strpos($topmenu['css_class'], 'ame-has-submenu-icons') === false) )  {
				$topmenu['css_class'] .= ' ame-has-submenu-icons';
			}

			$new_menu[] = $this->convert_to_wp_format($topmenu);
		}

		$this->custom_wp_menu = $new_menu;
		$this->custom_wp_submenu = $new_submenu;
	}

	/**
	 * Convert a menu item from the internal format used by this plugin to the format
	 * used by WP. The menu should be prepared using the prepare... function beforehand.
	 *
	 * @see self::prepare_for_output()
	 *
	 * @param array $item
	 * @return array
	 */
	private function convert_to_wp_format($item) {
		//Build the menu structure that WP expects
		$wp_item = array(
			$item['menu_title'],
			$item['access_level'],
			$item['file'],
			$item['page_title'],
			$item['css_class'],
			$item['hookname'], //ID
			isset($item['wp_icon_url']) ? $item['wp_icon_url'] : $item['icon_url'],
		);

		return $wp_item;
	}

	/**
	 * Prepare a menu item to be converted to the WordPress format and added to the current
	 * WordPress admin menu. This function applies menu defaults and templates, calls filters
	 * that allow other components to tweak the menu, decides on what capability/-ies to use,
	 * and so on.
	 *
	 * Caution: The filters called by this function may cause side-effects. Specifically, the Pro-only feature
	 * for displaying menu pages in a frame does this. See wsMenuEditorExtras::create_framed_menu().
	 * Therefore, it is not safe to call this function more than once for the same item.
	 *
	 * @param array $item Menu item in the internal format.
	 * @param string $item_type Either 'menu' or 'submenu'.
	 * @param array $parent Optional. The parent of this sub-menu item. Top level menus have no parent.
	 * @return array Menu item in the internal format.
	 */
	private function prepare_for_output($item, $item_type = 'menu', $parent = array()) {
		$parent_file = isset($parent['file']) ? $parent['file'] : null;

		// Special case : plugin pages that have been moved from a sub-menu to a different
		// menu or the top level. We'll need to adjust the file field to point to the correct URL.
		// This is required because WP identifies plugin pages using *both* the plugin file
		// and the parent file.
		if ( $item['template_id'] !== '' && !$item['separator'] ) {
			$template = $this->item_templates[$item['template_id']];
			if ( $template['defaults']['is_plugin_page'] ) {
				$default_parent = $template['defaults']['parent'];
				if ( $parent_file != $default_parent ){
					$item['file'] = $template['defaults']['url'];
				}
			}
		}

		//Give each unclickable item a unique URL.
		if ( $item['template_id'] === ameMenuItem::unclickableTemplateId ) {
			static $unclickableCounter = 0;
			$unclickableCounter++;
			$unclickableUrl = '#' . ameMenuItem::unclickableTemplateClass . '-' . $unclickableCounter;
			$item['file'] = $item['url'] = $unclickableUrl;

			//The item must have the special "unclickable" class even if the user overrides the class.
			$cssClass = ameMenuItem::get($item, 'css_class', '');
			if ( strpos($cssClass, ameMenuItem::unclickableTemplateClass) === false ) {
				$item['css_class'] = ameMenuItem::unclickableTemplateClass . ' ' . $cssClass;
			}
		}

		//Make the default submenu icon the same as the parent icon.
		if ( !empty($parent) && isset($item['defaults']) ) {
			$parent_icon = ameMenuItem::get($parent, 'icon_url', '');
			if ( !empty($parent_icon) ) {
				$item['defaults']['icon_url'] = $parent_icon;
			}
		}

		//Menus that have both a custom icon URL and a "menu-icon-*" class will get two overlapping icons.
		//Fix this by automatically removing the class. The user can set a custom class attr. to override.
		$hasCustomIconUrl = !ameMenuItem::is_default($item, 'icon_url');
		$hasIcon = !in_array(ameMenuItem::get($item, 'icon_url'), array('', 'none', 'div'));
		if (
			ameMenuItem::is_default($item, 'css_class')
			&& $hasCustomIconUrl
			&& $hasIcon //Skip "no icon" settings.
		) {
			$new_classes = preg_replace('@\bmenu-icon-[^\s]+\b@', '', $item['defaults']['css_class']);
			if ( $new_classes !== $item['defaults']['css_class'] ) {
				$item['css_class'] = $new_classes;
			}
		}

		if ( $hasCustomIconUrl && (strpos(ameMenuItem::get($item, 'icon_url'), 'dashicons-') === 0) ) {
			$item['css_class'] = ameMenuItem::get($item, 'css_class', '') . ' ame-has-custom-dashicon';
		}

		//WPML support: Translate only custom titles. See further below.
		$hasCustomMenuTitle = isset($item['menu_title']);

		//Apply defaults & filters
		$item = ameMenuItem::apply_defaults($item);
		$item = ameMenuItem::apply_filters($item, $item_type, $parent_file); //may cause side-effects

		//Store the hierarchical menu title for errors and debugging messages.
		$item['full_title'] = $item['menu_title'];
		if ( isset($parent, $parent['menu_title']) ) {
			$item['full_title'] = $parent['menu_title'] . ' → ' . $item['full_title'];
		}

		$item = $this->set_final_menu_capability($item, $parent);
		if ( !$this->should_store_security_log() ) {
			unset($item['access_check_log']); //Throw away the log to conserve memory.
		}
		$this->add_access_lookup($item, $item_type);

		//Menus without a custom icon image should have it set to "none" (or "div" in older WP versions).
		//See /wp-admin/menu-header.php for details on how this works.
		if ( $item['icon_url'] === '' ) {
			$item['icon_url'] = 'none';
		}

		//Submenus must not have the "menu-top" class(-es). In WP versions that support submenu CSS classes,
		//it can break menu display.
		if ( !empty($item['css_class']) && ($item_type === 'submenu') ) {
			$item['css_class'] = preg_replace('@\bmenu-top(?:-[\w\-]+)?\b@', '', $item['css_class']);
		} elseif ( ($item_type === 'menu') && (!$item['separator']) && (!preg_match('@\bmenu-top\b@', $item['css_class'])) ) {
			//Top-level menus should always have the "menu-top" class.
			$item['css_class'] = 'menu-top ' . $item['css_class'];
		}

		//Add a flag to menus that will be kept open.
		if ( !empty($item['is_always_open']) && ($item_type === 'menu') && (!$item['separator']) ) {
			$item['css_class'] .= ' ws-ame-has-always-open-submenu';
		}

		//Add submenu icons if necessary.
		if ( ($item_type === 'submenu') && $hasIcon ) {
			$item = apply_filters('admin_menu_editor-submenu_with_icon', $item, $hasCustomIconUrl);
		}

		//Used later to determine the current page based on URL.
		if ( empty($item['url']) ) {
			$original_parent = isset($item['defaults']['parent']) ? $item['defaults']['parent'] : $parent_file;
			$item['url'] = ameMenuItem::generate_url($item['file'], $original_parent);
		}

		//Convert relative URls to fully qualified ones. This prevents problems with WordPress
		//incorrectly converting "index.php?page=xyz" to, say, "tools.php?page=index.php?page=xyz"
		//if the menu item was moved from "Dashboard" to "Tools".
		$itemFile = ameMenuItem::remove_query_from($item['file']);
		$shouldMakeAbsolute =
			   (strpos($item['file'], '://') === false)
			&& (substr($item['file'], 0, 1) != '/')
			&& ($itemFile == 'index.php')
			&& (strpos($item['file'], '?') !== false);

		if ( $shouldMakeAbsolute ) {
			$item['file'] = admin_url($item['url']);
		}

		//WPML support: Use translated menu titles where available.
		if ( !$item['separator'] && $hasCustomMenuTitle && function_exists('icl_t') ) {
			$item['menu_title'] = icl_t(
				self::WPML_CONTEXT,
				$this->get_wpml_name_for($item, 'menu_title'),
				$item['menu_title']
			);
		}

		return $item;
	}

	/**
	 * Figure out if the current user can access a menu item and what capability they would need.
	 *
	 * This method takes into account the default capability set by WordPress as well as any
	 * custom role and capability settings specified by the user. It will set "access_level"
	 * to the required capability, or set it to 'do_not_allow' if the current user can't access
	 * this menu.
	 *
	 * @param array $item Menu item (with defaults applied).
	 * @param array $parent_item Parent menu item, if any.
	 * @return array
	 */
	private function set_final_menu_capability($item, $parent_item = null) {
		$item['access_check_log'] = array(
			str_repeat('=', 79),
			'Figuring out what capability the user will need to access this item...'
		);
		$debug_title = ameMenuItem::get($item, 'full_title', ameMenuItem::get($item, 'menu_title', '[untitled menu]'));

		//The user can configure the plugin to automatically hide all submenu items if the parent menu is hidden.
		//This is the opposite of how WordPress usually handles submenu permissions, so it's optional.
		$is_parent_denied = !empty($parent_item) && ($parent_item['access_level'] === 'do_not_allow');
		if ( $is_parent_denied && !empty($parent_item['restrict_access_to_items']) ) {
			$item['access_check_log'][] = '-----';
			$item['access_check_log'][] = 'WARNING: The parent menu overrides submenu permissions.';
			$item['access_check_log'][] = sprintf(
				'The current user doesn\'t have access to the parent menu ("%s"). Because the "Hide all submenu items
				 when this item is hidden" option is enabled, this item will also be hidden. Setting capability to
				 "do_not_allow".',
				htmlentities($parent_item['menu_title'])
			);

			$item['access_check_log'][] = str_repeat('=', 79);
			if ( !empty($parent_item['access_check_log']) ) {
				$item['access_check_log'][] = 'For reference, here\'s the log for the parent menu:';
				$item['access_check_log'] = array_merge($item['access_check_log'], $parent_item['access_check_log']);
			}

			$item['access_level'] = 'do_not_allow';
			return $item;
		}

		//TODO: A direct call to apply_custom_access would be faster.
		$item = apply_filters('custom_admin_menu_capability', $item);

		$item['access_check_log'][] = '-----';

		//Check if the current user can access this menu.
		$user_has_access = true;
		$cap_to_use = '';

		$user_has_default_cap = null;
		$reason = isset($item['access_decision_reason']) ? $item['access_decision_reason'] : null;

		if ( !empty($item['access_level']) ) {
			$cap_to_use = $item['access_level'];

			if ( isset($item['user_has_access_level']) ) {
				//The "custom_admin_menu_capability" filter has already determined whether this user should
				//have the required capability, so checking it again would be redundant. This usually only
				//applies to the Pro version which uses that filter in extras.php.
				$user_has_cap = $item['user_has_access_level'];

				$item['access_check_log'][] = sprintf(
					'Skipping a "%1$s" capability check because we\'ve already determined that the current user %2$s access.',
					htmlentities($cap_to_use),
					$user_has_cap ? 'should have' : 'should not have'
				);
			} else {
				$user_has_cap = $this->current_user_can($cap_to_use);
				$item['access_check_log'][] = sprintf(
					'Required capability: %1$s. User %2$s this capability.',
					htmlentities($cap_to_use),
					$user_has_cap ? 'HAS' : 'DOES NOT have'
				);

				$user_has_default_cap = $user_has_cap;
				if ( is_null($reason) ) {
					$reason = sprintf(
						'The current user %1$s the "%2$s" capability that is required to access the "%3$s" menu item.',
						$user_has_cap ? 'has' : 'doesn\'t have',
						$cap_to_use,
						$debug_title
					);
				}
			}

			$user_has_access = $user_has_access && $user_has_cap;

		} else {
			$item['access_check_log'][] = '- No required capability set.';
		}

		if ( !empty($item['extra_capability']) ) {
			$had_access_before_extra_cap = $user_has_access;

			$user_has_cap = $this->current_user_can($item['extra_capability']);
			$user_has_access = $user_has_access && $user_has_cap;
			$cap_to_use = $item['extra_capability'];

			$item['access_check_log'][] = sprintf(
				'Extra capability: %1$s. User %2$s this capability.',
				htmlentities($cap_to_use),
				$user_has_cap ? 'HAS' : 'DOES NOT have'
			);

			//Provide a more detailed reason for situations where the extra cap disagrees.
			if ( !$user_has_access ) {
				if ( $had_access_before_extra_cap && !$user_has_cap ) {
					$reason = sprintf(
						'The current user doesn\'t have the extra capability "%1$s" that is required to access the "%2$s" menu item.',
						$item['extra_capability'],
						$debug_title
					);
				} else if ( $user_has_cap && !$user_has_default_cap && !is_null($user_has_default_cap) ) {
					//Note: Will this ever show up? If the user doesn't have the required cap,
					//WordPress won't even register the menu. AME won't be able to identify the menu for that user.
					$reason = sprintf(
						'The current user has the extra capability "%1$s". However, they don\'t ' .
						'have the "%2$s" capability that is also required to access "%3$s".',
						$item['extra_capability'],
						$item['access_level'],
						$debug_title
					);
				}
			}
		} else {
			$item['access_check_log'][] = 'No "extra capability" set.';
		}

		if ( !is_null($reason) ) {
			$item['access_decision_reason'] = $reason;
		}

		$capability = $user_has_access ? $cap_to_use : 'do_not_allow';
		$item['access_check_log'][] = 'Final capability setting: ' . $capability;
		$item['access_check_log'][] = str_repeat('=', 79);

		$item['access_level'] = $capability;
		return $item;
	}
	
  /**
   * Output the menu editor page
   *
   * @return void
   */
	function page_menu_editor(){
		if ( !$this->current_user_can_edit_menu() ){
			wp_die(sprintf(
				'You do not have sufficient permissions to use Admin Menu Editor. Required: <code>%s</code>.',
				htmlentities($this->options['plugin_access'])
			));
		}

		$action = isset($this->post['action']) ? $this->post['action'] : (isset($this->get['action']) ? $this->get['action'] : '');
		do_action('admin_menu_editor-header', $action, $this->post);

		if ( !empty($action) ) {
			$this->handle_form_submission($this->post, $action);
		}

		//By default, show the "Hide" button only if the user has already hidden something with it,
		//or if they're using the free version. Pro users should use role permissions instead, but can
		//explicitly enable the button if they want.
		if ( !isset($this->options['show_deprecated_hide_button']) ) {
			if ( $this->is_pro_version() ) {
				$this->options['show_deprecated_hide_button'] = ameMenu::has_hidden_items($this->merged_custom_menu);
				$this->save_options();
			} else {
				$this->options['show_deprecated_hide_button'] = true;
			}
		}

		if ( $this->current_tab === 'settings' ) {
			$this->display_plugin_settings_ui();
		} else if ( $this->current_tab == 'generate-menu-dashicons' ) {
			require dirname(__FILE__) . '/generate-menu-dashicons.php';
		} else if ( $this->current_tab === 'repair-database' ) {
			$this->repair_database();
		} else if ( $this->is_editor_page() ) {
			$this->display_editor_ui();
		} else {
			do_action('admin_menu_editor-section-' . $this->current_tab);
		}

		//Let the Pro version script output it's extra HTML & scripts.
		do_action('admin_menu_editor-footer');
		do_action('admin_menu_editor-footer-' . $this->current_tab, $action);
	}

	private function repair_database() {
		global $wpdb; /** @var wpdb $wpdb */

		if ( !is_multisite() ) {
			echo 'This is not Multisite. The "repair" function does not apply to your site.';
			return;
		}

		echo '<div class="wrap"><h1>Repairing database...</h1><p></p>';

		$options_to_repair = array(
			$this->option_name,
			'wsh_license_manager-admin-menu-editor-pro',
			'ws_abe_admin_bar_nodes',
			'ws_abe_admin_bar_settings',
		);

		echo "Repair {$wpdb->sitemeta}<br>";
		$wpdb->query('REPAIR TABLE ' . $wpdb->sitemeta);

		echo "Lock {$wpdb->sitemeta}<br>";
		$wpdb->query('LOCK TABLES ' . $wpdb->sitemeta);

		foreach($options_to_repair as $option) {
			if ( empty($option) ) {
				continue; //Sanity check.
			}

			echo "Fetch option {$option}<br>";
			$row = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM {$wpdb->sitemeta} WHERE meta_key = %s LIMIT 1",
				$option
			));

			if ( empty($row) || empty($row->site_id) ) {
				echo "Option doesn't exist, skipping it.<br>";
				continue;
			}

			echo "Delete all rows where meta_key = {$option}<br>";
			$wpdb->delete($wpdb->sitemeta, array('meta_key' => $option), '%s');

			echo "Recreate the first copy of {$option}<br>";
			$wpdb->insert(
				$wpdb->sitemeta,
				array(
					'site_id' => $row->site_id,
					'meta_key' => $option,
					'meta_value' => $row->meta_value,
				),
				array('%d', '%s', '%s')
			);
		}

		echo "Unlock {$wpdb->sitemeta}<br>";
		$wpdb->query('UNLOCK TABLES');

		echo "Done.<br>";
		echo '<div>';
	}

	private function handle_form_submission($post, $action = '') {
		if ( $action == 'save_menu' ) {
			//Save the admin menu configuration.
			if ( isset($post['data']) ){
				check_admin_referer('menu-editor-form');

				//Try to decode a menu tree encoded as JSON
				$url = remove_query_arg(array('noheader'));
				try {
					$menu = ameMenu::load_json($post['data'], true);
				} catch (InvalidMenuException $ex) {
					$debugData = '';
					$debugData .= "Exception:\n"      . $ex->getMessage() . "\n\n";
					$debugData .= "Used POST data:\n" . print_r($this->post, true) . "\n\n";
					$debugData .= "Original POST:\n"  . print_r($this->originalPost, true) . "\n\n";
					$debugData .= "\$_POST global:\n" . print_r($_POST, true);

					$debugData = sprintf(
						"<textarea rows=\"30\" cols=\"100\">%s</textarea>",
						htmlentities($debugData)
					);

					wp_die(
						"Error: Failed to decode menu data!<br><br>\n"
						. "Please send this debugging information to the developer: <br>"
						. $debugData
					);

					return;
				}

				//Sanitize menu item properties.
				$menu['tree'] = ameMenu::sanitize($menu['tree']);

				//Discard capabilities that refer to unregistered post types or taxonomies.
				if ( !empty($menu['granted_capabilities']) ) {
					$capFilter = new ameGrantedCapabilityFilter();
					$menu['granted_capabilities'] = $capFilter->clean_up($menu['granted_capabilities']);
				}

				//Remember if the user has changed any menu icons to different Dashicons.
				$menu['has_modified_dashicons'] = ameModifiedIconDetector::detect($menu);

				//Which menu configuration are we changing?
				$config_id = isset($post['config_id']) ? $post['config_id'] : null;
				if ( !in_array($config_id, array('site', 'global', 'network-admin')) ) {
					$config_id = $this->get_loaded_menu_config_id();
				}

				//Save the custom menu
				$this->set_custom_menu($menu, $config_id);

				//Redirect back to the editor and display the success message.
				$query = array('message' => 1);

				//Also, automatically select the last selected actor and menu (convenience feature).
				$pass_through_params = array(
					'selected_actor', 'selected_menu_url', 'selected_submenu_url',
					'expand_menu', 'expand_submenu',
				);
				foreach($pass_through_params as $param) {
					if ( isset($post[$param]) && !empty($post[$param]) ) {
						$query[$param] = rawurlencode(strval($post[$param]));
					}
				}

				wp_redirect( add_query_arg($query, $url) );
				die();
			} else {
				$message = "Failed to save the menu. ";
				if ( isset($this->post['data_length']) && is_numeric($this->post['data_length']) ) {
					$message .= sprintf(
						'Expected to receive %d bytes of menu data in $_POST[\'data\'], but got nothing.',
						intval($this->post['data_length'])
					);
				}
				wp_die($message);
			}

		} else if ( $action == 'save_settings' ) {

			//Save overall plugin configuration (permissions, etc).
			check_admin_referer('save_settings');

			//Plugin access setting.
			$valid_access_settings = array('super_admin', 'manage_options');
			//On Multisite only Super Admins can choose the "Only the current user" option.
			if ( !is_multisite() || is_super_admin() ) {
				$valid_access_settings[] = 'specific_user';
			}
			if ( isset($this->post['plugin_access']) && in_array($this->post['plugin_access'], $valid_access_settings) ) {
				$this->options['plugin_access'] = $this->post['plugin_access'];

				if ( $this->options['plugin_access'] === 'specific_user' ) {
					$this->options['allowed_user_id'] = get_current_user_id();
				} else {
					$this->options['allowed_user_id'] = null;
				}
			}

			//Whether to hide the plugin on the "Plugins" admin page.
			if ( !is_multisite() || is_super_admin() ) {
				if ( !empty($this->post['hide_plugin_from_others']) ) {
					$this->options['plugins_page_allowed_user_id'] = get_current_user_id();
				} else {
					$this->options['plugins_page_allowed_user_id'] = null;
				}
			}

			//Configuration scope. The Super Admin is the only one who can change it since it affects all sites.
			if ( is_multisite() && is_super_admin() ) {
				$valid_scopes = array('global', 'site');
				if ( isset($this->post['menu_config_scope']) && in_array($this->post['menu_config_scope'], $valid_scopes) ) {
					$this->options['menu_config_scope'] = $this->post['menu_config_scope'];
				}
			}

			//Security logging.
			$this->options['security_logging_enabled'] = !empty($this->post['security_logging_enabled']);

			//Hide some menu options by default.
			$this->options['hide_advanced_settings'] = !empty($this->post['hide_advanced_settings']);

			//Enable the now-obsolete "Hide" button.
			if ( $this->is_pro_version() ) {
				$this->options['show_deprecated_hide_button'] = !empty($this->post['show_deprecated_hide_button']);
			}

			//Menu editor colour scheme.
			if ( !empty($this->post['ui_colour_scheme']) ) {
				$valid_colour_schemes = array('classic', 'wp-grey', 'modern-one');
				$scheme = strval($this->post['ui_colour_scheme']);
				if ( in_array($scheme, $valid_colour_schemes) ) {
					$this->options['ui_colour_scheme'] = $scheme;
				}
			}

			//Enable submenu icons.
			if ( !empty($this->post['submenu_icons_enabled']) ) {
				$submenu_icons_enabled = strval($this->post['submenu_icons_enabled']);
				$valid_icon_settings = array('never', 'if_custom', 'always');
				if ( in_array($submenu_icons_enabled, $valid_icon_settings, true) ) {
					$this->options['submenu_icons_enabled'] = $submenu_icons_enabled;
				}
			}

			//Work around icon CSS problems.
			$this->options['force_custom_dashicons'] = !empty($this->post['force_custom_dashicons']);

			//Where to put new or unused menu items.
			if ( !empty($this->post['unused_item_position']) ) {
				$unused_item_position = strval($this->post['unused_item_position']);
				$valid_position_settings = array('relative', 'bottom');
				if ( in_array($unused_item_position, $valid_position_settings, true) ) {
					$this->options['unused_item_position'] = $unused_item_position;
				}
			}

			//Permissions for unused menu items.
			if (
				isset($this->post['unused_item_permissions'])
				&& in_array($this->post['unused_item_permissions'], array('unchanged', 'match_plugin_access'), true)
			) {
				$this->options['unused_item_permissions'] = strval($this->post['unused_item_permissions']);
			}

			//How verbose "access denied" errors should be.
			if ( !empty($this->post['error_verbosity']) ) {
				$error_verbosity = intval($this->post['error_verbosity']);
				$valid_verbosity_levels = array(self::VERBOSITY_LOW, self::VERBOSITY_NORMAL, self::VERBOSITY_VERBOSE);
				if ( in_array($error_verbosity, $valid_verbosity_levels) ) {
					$this->options['error_verbosity'] = $error_verbosity;
				}
			}

			//Menu data compression.
			$this->options['compress_custom_menu'] = !empty($this->post['compress_custom_menu']);

			//Active modules.
			$activeModules = isset($this->post['active_modules']) ? (array)$this->post['active_modules'] : array();
			$activeModules = array_fill_keys(array_map('strval', $activeModules), true);
			$this->options['is_active_module'] = array_merge(
				array_map('__return_false', $this->get_available_modules()),
				$activeModules
			);

			$this->save_options();
			wp_redirect(add_query_arg('message', 1, $this->get_settings_page_url()));
		}
	}

	private function display_editor_ui() {
		//Prepare a bunch of parameters for the editor.
		$editor_data = array(
			'message' => isset($this->get['message']) ? intval($this->get['message']) : null,
			'images_url' => plugins_url('images', $this->plugin_file),
			'hide_advanced_settings' => $this->options['hide_advanced_settings'],
			'show_extra_icons' => $this->options['show_extra_icons'],
			'current_tab_url' => $this->get_plugin_page_url(array('sub_section' => $this->current_tab)),
			'settings_page_url' => $this->get_settings_page_url(),
			'show_deprecated_hide_button' => $this->options['show_deprecated_hide_button'],
			'dashicons_available' => wp_style_is('dashicons', 'done'),
			'menu_config_id' => $this->get_loaded_menu_config_id(),
		);

		//Build a tree struct. for the default menu
		$default_menu = $this->get_default_menu();

		//Is there a custom menu?
		if (!empty($this->merged_custom_menu)){
			$custom_menu = $this->merged_custom_menu;
		} else {
			//Start out with the default menu if there is no user-created one
			$custom_menu = $default_menu;
		}

		//The editor doesn't use the color CSS. Including it would just make the page bigger and waste bandwidth.
		unset($custom_menu['color_css']);
		unset($custom_menu['color_css_modified']);

		//Encode both menus as JSON
		$editor_data['default_menu_js'] = ameMenu::to_json($default_menu);
		$editor_data['custom_menu_js'] = ameMenu::to_json($custom_menu);

		//Create a list of all known capabilities and roles. Used for the drop-down list on the access field.
		$all_capabilities = ameRoleUtils::get_all_capabilities();
		//"level_X" capabilities are deprecated so we don't want people using them.
		//This would look better with array_filter() and an anonymous function as a callback.
		for($level = 0; $level <= 10; $level++){
			$cap = 'level_' . $level;
			if ( isset($all_capabilities[$cap]) ){
				unset($all_capabilities[$cap]);
			}
		}
		$all_capabilities = array_keys($all_capabilities);
		natcasesort($all_capabilities);

		//Multi-site installs also get the virtual "Super Admin" cap, but only the Super Admin sees it.
		if ( is_multisite() && !isset($all_capabilities['super_admin']) && is_super_admin() ){
			array_unshift($all_capabilities, 'super_admin');
		}
		$editor_data['all_capabilities'] = $all_capabilities;

		//Create a list of all roles, too.
		$all_roles = ameRoleUtils::get_role_names();
		asort($all_roles);
		$editor_data['all_roles'] = $all_roles;

		//Include hint visibility settings
		$editor_data['show_hints'] = $this->get_hint_visibility();

		require dirname(__FILE__) . '/editor-page.php';
	}

	/**
	 * Get the default admin menu configuration.
	 *
	 * @return array
	 */
	private function get_default_menu() {
		$default_tree = ameMenu::wp2tree($this->default_wp_menu, $this->default_wp_submenu, $this->menu_url_blacklist);
		$default_menu = ameMenu::load_array($default_tree);
		return $default_menu;
	}

	/**
	 * Get the admin menu configuration that was used during this page load.
	 *
	 * @return array
	 */
	public function get_active_admin_menu() {
		if ( !did_action('admin_menu') && !did_action('network_admin_menu') ) {
			throw new LogicException(__METHOD__ . ' was called too early. You must only call it after the admin menu is ready.');
		}

		if (!empty($this->merged_custom_menu)){
			return $this->merged_custom_menu;
		} else {
			return $this->get_default_menu();
		}
	}

	/**
	 * Display the header of the "Menu Editor" page.
	 * This includes the page heading and tab list.
	 */
	public function display_settings_page_header() {
		$wrap_classes = array('wrap');
		if ( $this->is_pro_version() ) {
			$wrap_classes[] = 'ame-is-pro-version';
		} else {
			$wrap_classes[] = 'ame-is-free-version';
		}

		echo '<div class="', implode(' ', $wrap_classes), '">';
		printf(
			'<%1$s id="ws_ame_editor_heading">%2$s</%1$s>',
			self::$admin_heading_tag,
			apply_filters('admin_menu_editor-self_page_title', 'Menu Editor')
		);

		do_action('admin_menu_editor-display_tabs');

		if ( isset($_GET['message']) && (intval($_GET['message']) === 1) ) {
			add_settings_error('ame-settings-page', 'settings_updated', __('Settings saved.'), 'updated');
		}
		settings_errors('ame-settings-page');
	}

	public function display_settings_page_footer() {
		echo '</div>'; //div.wrap
	}

	/**
	 * Display the tabs for the settings page.
	 */
	public function display_editor_tabs() {
		echo '<h2 class="nav-tab-wrapper">';
		foreach($this->tabs as $slug => $title) {
			printf(
				'<a href="%s" id="%s" class="nav-tab%s">%s</a>',
				esc_attr(add_query_arg('sub_section', $slug, self_admin_url($this->settings_link))),
				esc_attr('ws_ame_' . $slug . '_tab'),
				$slug === $this->current_tab ? ' nav-tab-active' : '',
				$title
			);
		}
		echo '</h2>';
		echo '<div class="clear"></div>';
	}

	/**
	 * Display the plugin settings page.
	 */
	private function display_plugin_settings_ui() {
		//These variables are used by settings-page.php.
		/** @noinspection PhpUnusedLocalVariableInspection */
		$settings = $this->options;
		/** @noinspection PhpUnusedLocalVariableInspection */
		$settings_page_url = $this->get_settings_page_url();
		/** @noinspection PhpUnusedLocalVariableInspection */
		$editor_page_url = admin_url($this->settings_link);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$db_option_name = $this->option_name;

		require dirname(__FILE__) . '/settings-page.php';
	}

	/**
	 * Get the fully qualified URL of the plugin page, i.e. "Settings -> Menu Editor [Pro]".
	 *
	 * @param array $extra_query_args List of query arguments to append to the URL. Format: [param => value].
	 * @return string
	 */
	public function get_plugin_page_url($extra_query_args = array()) {
		$url = self_admin_url($this->settings_link);
		if ( !empty($extra_query_args) ) {
			$url = add_query_arg($extra_query_args, $url);
		}
		return $url;
	}

	/**
	 * Get the fully qualified URL of the "Settings" sub-section of our plugin page.
	 *
	 * @return string
	 */
	private function get_settings_page_url() {
		return $this->get_plugin_page_url(array('sub_section' => 'settings'));
	}

	/**
	 * Check if the current page is the "Menu Editor" admin page.
	 *
	 * @return bool
	 */
	public function is_editor_page() {
		return $this->is_tab_open('editor') || $this->is_tab_open('network-admin-menu');
	}

	/**
	 * Check if the current page is the "Settings" sub-section of our admin page.
	 *
	 * @return bool
	 */
	protected function is_settings_page() {
		return $this->is_tab_open('settings');
	}

	/**
	 * Check if the specified AME settings tab is currently open.
	 *
	 * @param string $tab_slug
	 * @return bool
	 */
	public function is_tab_open($tab_slug) {
		return is_admin()
			&& ($this->current_tab === $tab_slug)
			&& isset($this->get['page']) && ($this->get['page'] == 'menu_editor');
	}

	/**
	 * Get the list of virtual capabilities.
	 *
	 * @uses self::$cached_virtual_caps to cache the generated list of caps.
	 *
	 * @param int|null $mode
	 * @return array A list of capability => [role1 => true, ... roleN => true] assignments.
	 */
	function get_virtual_caps($mode = null) {
		if ( $mode === null ) {
			$mode = self::ALL_VIRTUAL_CAPS;
		}

		if ( $this->cached_virtual_caps !== null ) {
			return $this->cached_virtual_caps[$mode];
		}

		$custom_menu = $this->load_custom_menu();
		if ( $custom_menu === null ){
			return array();
		}

		if ( isset($custom_menu['prebuilt_virtual_caps']) ) {
			$this->cached_virtual_caps = $custom_menu['prebuilt_virtual_caps'];
		} else {
			$this->cached_virtual_caps = $this->build_virtual_capability_list($custom_menu);
		}

		return $this->cached_virtual_caps[$mode];
	}

	/**
	 * Generate a list of "virtual" capabilities that should be granted to specific actors.
	 *
	 * This is based on grant_access settings for the custom menu and enables selected
	 * roles and users to access menu items that they ordinarily would not be able to.
	 *
	 * @uses self::get_virtual_caps_for() to actually generate the caps.
	 *
	 * @param array $custom_menu
	 * @return array
	 */
	private function build_virtual_capability_list($custom_menu) {
		//Include directly granted capabilities.
		$grantedCaps = array();
		if ( !empty($custom_menu['granted_capabilities']) ) {
			foreach ($custom_menu['granted_capabilities'] as $actor => $capabilities) {
				foreach ($capabilities as $capability => $allow) {
					$grantedCaps[$actor][$capability] = (bool)(is_array($allow) ? $allow[0] : $allow);
				}
			}
		}

		//Include caps that are required to access menu items (grant_access).
		$menuCaps = array();
		foreach($custom_menu['tree'] as $item) {
			$menuCaps = self::array_replace_recursive($menuCaps, $this->get_virtual_caps_for($item));
		}

		//grant_access settings on individual items have precedence.
		$allCaps = self::array_replace_recursive($grantedCaps, $menuCaps);

		return array(
			self::DIRECTLY_GRANTED_VIRTUAL_CAPS => $grantedCaps,
			self::ALL_VIRTUAL_CAPS => $allCaps,
		);
	}

	private function get_virtual_caps_for($item) {
		$caps = array();

		if ( $item['template_id'] !== '' ) {
			$required_cap = ameMenuItem::get($item, 'access_level');

			$required_cap = self::map_basic_meta_cap($required_cap);
			//Why not just call map_meta_cap? Because it needs a user ID and we may be working on a role.
			//Also, map_meta_cap is complex and filter-able, so it's hard to verify that it will work reliably
			//in a non-standard context.

			foreach ($item['grant_access'] as $grant => $has_access) {
				if ( $has_access ) {
					if ( !isset($caps[$grant]) ) {
						$caps[$grant] = array();
					}
					$caps[$grant][$required_cap] = true;
				}
			}
		}

		foreach($item['items'] as $sub_item) {
			$caps = self::array_replace_recursive($caps, $this->get_virtual_caps_for($sub_item));
		}

		return $caps;
	}

	private static function array_replace_recursive($array1, $array2) {
		if ( function_exists('array_replace_recursive') ) {
			return array_replace_recursive($array1, $array2);
		}
		foreach($array2 as $key => $value) {
			if ( is_array($value) && isset($array1[$key]) && is_array($array1[$key]) ) {
				$value = self::array_replace_recursive($array1[$key], $value);
			}
			$array1[$key] = $value;
		}
		return $array1;
	}

	private static function map_basic_meta_cap($capability) {
		if ( $capability === 'customize' ) {
			return 'edit_theme_options';
		} elseif ( $capability === 'delete_site' ) {
			return 'manage_options';
		}

		static $category_caps = array(
			'manage_post_tags'  => true,
			'edit_categories'   => true,
			'edit_post_tags'    => true,
			'delete_categories' => true,
			'delete_post_tags'  => true,
		);
		if ( isset($category_caps[$capability]) ) {
			return 'manage_categories';
		}

		if (($capability === 'assign_categories') || ($capability === 'assign_post_tags')) {
			return 'edit_posts';
		}

		return $capability;
	}

	/**
	 * Clear all internal caches that can vary depending on the current site.
	 *
	 * For example, the same user can have different roles on different sites,
	 * so we must clear the role cache when WordPress switches the active site.
	 */
	public function clear_site_specific_caches() {
		$this->cached_virtual_caps = null;
		$this->cached_user_caps = array();
		$this->cached_user_roles = array();

		if ($this->options['menu_config_scope'] === 'site') {
			$this->cached_custom_menu = null;
			$this->loaded_menu_config_id = null;
		}
	}

	/**
	 * Create a virtual 'super_admin' capability that only super admins have.
	 * This function accomplishes that by by filtering 'user_has_cap' calls.
	 * 
	 * @param array $allcaps All capabilities belonging to the current user, cap => true/false.
	 * @param array $required_caps The required capabilities.
	 * @param array $args The capability passed to current_user_can, the current user's ID, and other args.
	 * @return array Filtered version of $allcaps
	 */
	function hook_user_has_cap($allcaps, /** @noinspection PhpUnusedParameterInspection */ $required_caps, $args){
		//Be careful not to overwrite a super_admin cap added by other plugins 
		//For example, Advanced Access Manager also adds this capability. 
		if ( is_array($allcaps) && !isset($allcaps['super_admin']) ){
			$user_id = intval($args[1]);
			if ( $user_id != 0 ) {
				$allcaps['super_admin'] = is_multisite() && is_super_admin($user_id);
			}
		}
		return $allcaps;
	}

	/**
	 * AJAX callback for saving screen options (whether to show or to hide advanced menu options).
	 * 
	 * Handles the 'ws_ame_save_screen_options' action. The new option value 
	 * is read from $_POST['hide_advanced_settings'].
	 * 
	 * @return void
	 */
	function ajax_save_screen_options(){
		if (!$this->current_user_can_edit_menu() || !check_ajax_referer('ws_ame_save_screen_options', false, false)){
			die( $this->json_encode( array(
				'error' => "You're not allowed to do that!" 
			 )));
		}
		
		$this->options['hide_advanced_settings'] = !empty($this->post['hide_advanced_settings']);
		$this->options['show_extra_icons'] = !empty($this->post['show_extra_icons']);
		$this->save_options();
		die('1');
	}

	public function ajax_hide_hint() {
		if ( !isset($this->post['hint']) || !$this->current_user_can_edit_menu() ){
			die("You're not allowed to do that!");
		}

		$show_hints = $this->get_hint_visibility();
		$show_hints[strval($this->post['hint'])] = false;
		$this->set_hint_visibility($show_hints);

		die("OK");
	}

	private function get_hint_visibility() {
		$user = wp_get_current_user();
		$show_hints = get_user_meta($user->ID, 'ame_show_hints', true);
		if ( !is_array($show_hints) ) {
			$show_hints = array();
		}

        $defaults = array(
            'ws_sidebar_pro_ad' => true,
            'ws_whats_new_120' => false,
            'ws_hint_menu_permissions' => false,
        );

		return array_merge($defaults, $show_hints);
	}

	private function set_hint_visibility($show_hints) {
		$user = wp_get_current_user();
		update_user_meta($user->ID, 'ame_show_hints', $show_hints);
	}

	/**
	 * AJAX callback for permanently hiding the "are you sure you want to hide the Dashboard?" warning.
	 */
	public function ajax_disable_dashboard_hiding_confirmation() {
		if (!check_ajax_referer('ws_ame_disable_dashboard_hiding_confirmation', false, false) || !$this->current_user_can_edit_menu()){
			die("You don't have sufficient permissions to do that.");
		}
		$this->options['dashboard_hiding_confirmation_enabled'] = false;
		$this->save_options();
	}

	/**
	 * Retrieve a list of recently modified pages.
	 */
	public function ajax_get_pages() {
		if ( !check_ajax_referer('ws_ame_get_pages', false, false) ) {
			exit(json_encode(array('error' => 'Invalid nonce.')));
		} else if ( !$this->current_user_can_edit_menu() ) {
			exit(json_encode(array('error' => 'You don\'t have sufficient permissions to edit the admin menu.')));
		}

		$pages = get_pages(array(
			'sort_column' => 'post_modified',
			'sort_order' => 'DESC',
			'hierarchical' => false,
			'post_status' => array('publish', 'private'),
			'number' => 50, //Semi-arbitrary. We do need a limit - some users could have thousands of pages.
		));
		/** @var WP_Post[] $pages */
		$blog_id = get_current_blog_id();

		$results = array();
		foreach($pages as $page) {
			$results[] = array(
				'post_id' => $page->ID,
				'blog_id' => $blog_id,
				'post_title' => $page->post_title,
				'post_modified' => $page->post_modified
			);
		}

		exit(json_encode($results));
	}

	/**
	 * Get details about a specific page or post. CPTs also work.
	 */
	public function ajax_get_page_details() {
		if ( !check_ajax_referer('ws_ame_get_page_details', false, false) ) {
			exit(json_encode(array('error' => 'Invalid nonce.')));
		} else if ( !$this->current_user_can_edit_menu() ) {
			exit(json_encode(array('error' => 'You don\'t have sufficient permissions to edit the admin menu.')));
		}

		$post_id = intval($_GET['post_id']);
		$blog_id = intval($_GET['blog_id']);
		$should_switch = function_exists('get_current_blog_id') && ($blog_id !== get_current_blog_id());

		if ( $should_switch ) {
			switch_to_blog($blog_id);
		}

		$page = get_post($post_id);
		if ( !$page ) {
			exit(json_encode(array('error' => 'Not found')));
		}

		if ( $should_switch ) {
			restore_current_blog();
		}

		$response = array(
			'post_id' => $page->ID,
			'blog_id' => $blog_id,
			'post_title' => $page->post_title,
		);
		exit(json_encode($response));
	}

	/**
	 * Enqueue a script that fixes a bug where pages moved to a different menu
	 * would not be highlighted properly when the user visits them.
	 */
	public function enqueue_menu_fix_script() {
		wp_enqueue_auto_versioned_script(
			'ame-menu-fix',
			plugins_url('js/menu-highlight-fix.js', $this->plugin_file),
			array('jquery'),
			true
		);
	}

	/**
	 * Check if the current user can access the current admin menu page.
	 *
	 * @return bool
	 */
	private function user_can_access_current_page() {
		$current_item = $this->get_current_menu_item();
		if ( $current_item === null ) {
			$this->log_security_note('Could not determine the current menu item. We won\'t do any custom permission checks.');
			return true; //Let WordPress handle it.
		}

		$this->log_security_note(sprintf(
			'The current menu item is "%s", menu template ID: "%s"',
			htmlentities($current_item['menu_title']),
			htmlentities(ameMenuItem::get($current_item, 'template_id', 'N/A'))
		));
		if ( isset($current_item['access_check_log']) ) {
			$this->log_security_note($current_item['access_check_log']);
		}

		//Note: Per-role and per-user virtual caps will be applied by has_cap filters.
		$allow = $this->current_user_can($current_item['access_level']);
		$this->log_security_note(sprintf(
			'The current user %1$s the "%2$s" capability.',
			$allow ? 'has' : 'does not have',
			htmlentities($current_item['access_level'])
		));

		return $allow;
	}

	/**
	 * Check if the current user has the specified capability.
	 * If the Pro version installed, you can use special syntax to perform complex capability checks.
	 *
	 * @param string $capability
	 * @return bool
	 */
	private function current_user_can($capability) {
		//WP core uses a special "do_not_allow" capability in a dozen or so places to explicitly deny access.
		//Even multisite super admins do not have this cap. We can return early here.
		if ( $capability === 'do_not_allow' ) {
			return false;
		}

		//Everybody has the "exist" cap.
		if ( $capability === 'exist' ) {
			return true;
		}

		if ( $this->user_cap_cache_enabled && isset($this->cached_user_caps[$capability]) ) {
			return $this->cached_user_caps[$capability];
		}

		$user_can = apply_filters('admin_menu_editor-current_user_can', current_user_can($capability), $capability);
		$this->cached_user_caps[$capability] = $user_can;
		return $user_can;
	}

	/**
	 * Determine which menu item matches the currently open admin page.
	 *
	 * @uses self::$reverse_item_lookup
	 * @return array|null Menu item in the internal format, or NULL if no matching item can be found.
	 */
	private function get_current_menu_item() {
		if ( !is_admin() || empty($this->reverse_item_lookup)) {
			if ( !is_admin() ) {
				$this->log_security_note('This is not an admin page. is_admin() returns false.');
			} else if ( empty($this->reverse_item_lookup) ) {
				$this->log_security_note('Warning: reverse_item_lookup is empty!');
			}
			return null;
		}

		//The current menu item doesn't change during a request, so we can cache it
		//and avoid searching the entire menu every time.
		static $cached_item = null;
		if ( $cached_item !== null ) {
			return $cached_item;
		}

		//Find an item where *all* query params match the current ones, with as few extraneous params as possible,
		//preferring sub-menu items. This is intentionally more strict than what we do in menu-highlight-fix.js,
		//since this function is used to check menu access.
		//TODO: Use get_current_screen() to determine the current post type and taxonomy.

		$best_item = null;
		$best_extra_params = PHP_INT_MAX;

		$base_site_url = get_site_url();
		if ( preg_match('@(^\w+://[^/]+)@', $base_site_url, $matches) ) { //Extract scheme + hostname.
			$base_site_url = $matches[1];
		}

		//Calling admin_url() once and then manually appending each page's path is measurably faster than calling it
		//for each menu, but it means the "admin_url" filter is only called once. If there is a plugin that changes
		//the admin_url for some pages but not others, this could lead to bugs (no such plugins are known at this time).
		$base_admin_url = admin_url();
		$admin_url_is_filtered = has_filter('admin_url');

		$current_url = $base_site_url . remove_query_arg('___ame_dummy_param___');
		$this->log_security_note(sprintf('Current URL: "%s"', htmlentities($current_url)));

		$current_url = $this->parse_url($current_url);

		//Special case: if post_type is not specified for edit.php and post-new.php,
		//WordPress assumes it is "post". Here we make this explicit.
		if ( $this->endsWith($current_url['path'], '/wp-admin/edit.php') || $this->endsWith($current_url['path'], '/wp-admin/post-new.php') ) {
			if ( !isset($current_url['params']['post_type']) ) {
				$current_url['params']['post_type'] = 'post';
			}
		}

		//Hook-based submenu pages can be accessed via both "parent-page.php?page=foo" and "admin.php?page=foo".
		//WP has a private API function for determining the canonical parent page for the current request.
		if ( $this->endsWith($current_url['path'], '/admin.php') && is_callable('get_admin_page_parent') ) {
			$real_parent = get_admin_page_parent('admin.php');
			if ( !empty($real_parent) && ($real_parent !== 'admin.php') ) {
				$current_url['alt_path'] = str_replace('/admin.php', '/' . $real_parent, $current_url['path']);
			}
		}

		foreach($this->reverse_item_lookup as $url => $item) {
			$item_url = $url;
			//Convert to absolute URL. Caution: directory traversal (../, etc) is not handled.
			if (strpos($item_url, '://') === false) {
				if ( substr($item_url, 0, 1) == '/' ) {
					$item_url = $base_site_url . $item_url;
				} else {
					if ( $admin_url_is_filtered ) {
						$item_url = admin_url($item_url);
					} else {
						$item_url = $base_admin_url . ltrim( $item_url, '/' );
					}
				}
			}
			$item_url = $this->parse_url($item_url);

			//Must match scheme, host, port, user, pass and path or alt_path.
			$components = array('scheme', 'host', 'port', 'user', 'pass');
			$is_close_match = $this->urlPathsMatch($current_url['path'], $item_url['path']);
			if ( !$is_close_match && isset($current_url['alt_path']) ) {
				$is_close_match = $this->urlPathsMatch($current_url['alt_path'], $item_url['path']);
				//Technically, we should also compare current[path] vs item[alt_path],
				//but generating the alt_path for each menu item would be complicated.
			}
			foreach($components as $component) {
				$is_close_match = $is_close_match && ($current_url[$component] == $item_url[$component]);
				if ( !$is_close_match ) {
					break;
				}
			}

			//Same as above - default post type is "post".
			if ( $this->endsWith($item_url['path'], '/wp-admin/edit.php') || $this->endsWith($item_url['path'], '/wp-admin/post-new.php') ) {
				if ( !isset($item_url['params']['post_type']) ) {
					$item_url['params']['post_type'] = 'post';
				}
			}

			//Special case: In WP 4.0+ the URL of the "Customize" menu changes often due to a "return" query parameter
			//that contains the current page URL. To reliably recognize this item, we should ignore that parameter.
			if ( $this->endsWith($item_url['path'], 'customize.php') ) {
				unset($item_url['params']['return']);
			}

			//The current URL must match all query parameters of the item URL.
			$different_params = $this->arrayDiffAssocRecursive($item_url['params'], $current_url['params']);

			//The current URL must have as few extra parameters as possible.
			$extra_params = $this->arrayDiffAssocRecursive($current_url['params'], $item_url['params']);

			if ( $is_close_match && (count($different_params) == 0) && (count($extra_params) < $best_extra_params) ) {
				$best_item = $item;
				$best_extra_params = count($extra_params);
			}
		}

		//Special case for CPTs: When the "Add New" menu is disabled by CPT settings (show_ui, etc), and someone goes
		//to add a new item, WordPress highlights the "$CPT-Name" item as the current one. Lets do the same for
		//consistency. See also: /wp-admin/post-new.php, lines #20 to #40.
		if (
			($best_item === null)
			&& isset($current_url['params']['post_type'])
			&& (!empty($current_url['params']['post_type']))
			&& $this->endsWith($current_url['path'], '/wp-admin/post-new.php')
			&& isset($this->reverse_item_lookup['edit.php?post_type=' . $current_url['params']['post_type']])
		) {
			$best_item = $this->reverse_item_lookup['edit.php?post_type=' . $current_url['params']['post_type']];
		}

		$cached_item = $best_item;
		return $best_item;
	}

	/**
	 * Parse a URL and return its components.
	 *
	 * Returns an array that contains all of these components: 'scheme', 'host', 'port', 'user', 'pass',
	 * 'path', 'query', 'fragment' and 'params'. All entries are strings, except 'params' which is
	 * an associative array of query parameters and their values.
	 *
	 * @param string $url
	 * @return array
	 */
	private function parse_url($url) {
		$url_defaults = array_fill_keys(array('scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'), '');
		$url_defaults['port'] = '80';

		$parsed = @parse_url($url);
		if ( !is_array($parsed) ) {
			$parsed = array();
		}
		$parsed = array_merge($url_defaults, $parsed);

		$params = array();
		if (!empty($parsed['query'])) {
			wp_parse_str($parsed['query'], $params);
		};
		$parsed['params'] = $params;

		return $parsed;
	}

	/**
	 * Get the difference of two arrays.
	 *
	 * This methods works like array_diff_assoc(), except it also supports nested arrays by comparing them recursively.
	 *
	 * @param array $array1 The base array.
	 * @param array $array2 The array to compare to.
	 * @return array An associative array of values from $array1 that are not present in $array2.
	 */
	private function arrayDiffAssocRecursive($array1, $array2) {
		$difference = array();

		foreach($array1 as $key => $value) {
			if ( !array_key_exists($key, $array2) ) {
				$difference[$key] = $value;
				continue;
			}

			$otherValue = $array2[$key];
			if ( is_array($value) !== is_array($otherValue) ) {
				//If only one of the two values is an array then they can't be equal.
				$difference[$key] = $value;
			} elseif ( is_array($value) ) {
				//Compare array values recursively.
				$subDiff = $this->arrayDiffAssocRecursive($value, $otherValue);
				if( !empty($subDiff) ) {
					$difference[$key] = $subDiff;
				}

			//Like the original array_diff_assoc(), we compare the values as strings.
			} elseif ( (string)$value !== (string)$array2[$key] ) {
				$difference[$key] = $value;
			}
		}

		return $difference;
	}

	/**
	 * Check if two paths match. Intended for comparing WP admin URLs.
	 *
	 * @param string $path1
	 * @param string $path2
	 * @return bool
	 */
	private function urlPathsMatch($path1, $path2) {
		if ( $path1 == $path2 ) {
			return true;
		}

		// "/wp-admin/index.php" should match "/wp-admin/".
		if (
			($this->endsWith($path1, '/wp-admin/index.php') && $this->endsWith($path2, '/wp-admin/'))
			|| ($this->endsWith($path2, '/wp-admin/index.php') && $this->endsWith($path1, '/wp-admin/'))
		) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if the input $string ends with the specified $suffix.
	 *
	 * @param string $string
	 * @param string $suffix
	 * @return bool
	 */
	private function endsWith($string, $suffix) {
		$len = strlen($suffix);
		if ( $len == 0 ) {
			return true;
		}
		return substr($string, -$len) === $suffix;
	}

	public function castValuesToBool($capabilities) {
		if ( !is_array($capabilities) ) {
			if ( empty($capabilities) ) {
				$capabilities = array();
			} else {
				trigger_error("Unexpected capability array: " . print_r($capabilities, true), E_USER_WARNING);
				return array();
			}
		}
		foreach($capabilities as $capability => $value) {
			$capabilities[$capability] = (bool)$value;
		}
		return $capabilities;
	}

	public function display_survey_notice() {
		//Handle the survey notice
		$hide_param_name = 'ame_hide_survey_notice';
		if ( isset($this->get[$hide_param_name]) ) {
			$this->options['display_survey_notice'] = empty($this->get[$hide_param_name]);
			$this->save_options();
		}

		$display_notice = $this->options['display_survey_notice'] && $this->current_user_can_edit_menu();
		if ( isset($this->options['first_install_time']) ) {
			$minimum_usage_period = 7*24*3600;
			$display_notice = $display_notice && ((time() - $this->options['first_install_time']) > $minimum_usage_period);
		}

		//Only display the notice on the Menu Editor (Pro) page.
		$display_notice = $display_notice && isset($this->get['page']) && ($this->get['page'] == 'menu_editor');
		
		//Let the user override this completely (useful for client sites).
		if ( $display_notice && file_exists(dirname($this->plugin_file) . '/never-display-surveys.txt') ) {
			$display_notice = false;
			$this->options['display_survey_notice'] = false;
			$this->save_options();
		}

		if ( $display_notice ) {
			$free_survey_url = 'https://docs.google.com/spreadsheet/viewform?formkey=dERyeDk0OWhlbkxYcEY4QTNaMnlTQUE6MQ';
			$pro_survey_url =  'https://docs.google.com/spreadsheet/viewform?formkey=dHl4MnlHaVI3NE5JdVFDWG01SkRKTWc6MA';

			if ( $this->is_pro_version() ) {
				$survey_url = $pro_survey_url;
			} else {
				$survey_url = $free_survey_url;
			}

			$hide_url = add_query_arg($hide_param_name, 1);
			printf(
				'<div class="updated">
					<p><strong>Help improve Admin Menu Editor - take the user survey!</strong></p>
					<p><!--suppress HtmlUnknownTarget --><a href="%s" target="_blank" title="Opens in a new window">Take the survey</a></p>
					<p><!--suppress HtmlUnknownTarget --><a href="%s">Hide this notice</a></p>
				</div>',
				esc_attr($survey_url),
				esc_attr($hide_url)
			);
		}
	}

	/**
	 * Capture $_GET and $_POST in $this->get and $this->post.
	 * Slashes added by "magic quotes" will be stripped.
	 *
	 * @return void
	 */
	function capture_request_vars(){
		$this->post = $this->originalPost = $_POST;
		$this->get = $_GET;

		/** @noinspection PhpDeprecationInspection */
		if ( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ) {
			$this->post = stripslashes_deep($this->post);
			$this->get = stripslashes_deep($this->get);
		}
	}

	/**
	 * Get POST parameters for the current request.
	 *
	 * @return array
	 */
	public function get_post_params() {
		return $this->post;
	}

	/**
	 * Get query parameters for the current request.
	 *
	 * @return array
	 */
	public function get_query_params() {
		return $this->get;
	}

	public function enqueue_helper_scripts() {
		wp_enqueue_script(
			'ame-helper-script',
			plugins_url('js/admin-helpers.js', $this->plugin_file),
			array('jquery'),
			'20160407-2'
		);

		//The helper script needs to know the custom page heading (if any) to apply it.
		$currentItem = $this->get_current_menu_item();
		if ( $currentItem && !empty($currentItem['page_heading']) ) {
			wp_localize_script(
				'ame-helper-script',
				'wsAmeCurrentMenuItem',
				array(
					'customPageHeading' => $currentItem['page_heading'],
					'pageHeadingSelector' =>
						version_compare(self::get_wp_version(), '4.3', '<') ? '.wrap > h2:first' : '.wrap > h1:first',
				)
			);
		}
	}

	public function enqueue_helper_styles() {
		wp_enqueue_style(
			'ame-helper-style',
			plugins_url('css/admin.css', $this->plugin_file),
			array(),
			'20140630-3'
		);

		if ( $this->options['force_custom_dashicons'] ) {
			//Optimization: Only add the stylesheet if the menu actually has custom dashicons.
			$menu = $this->load_custom_menu();
			if ( $menu && !empty($menu['has_modified_dashicons']) ) {
				wp_enqueue_style(
					'ame-force-dashicons',
					plugins_url('css/force-dashicons.css', $this->plugin_file),
					array(),
					'20170607'
				);
			}
		}
	}

	/**
	 * Get one of the plugin configuration values.
	 *
	 * @param string $name Option name.
	 * @return mixed|null
	 */
	public function get_plugin_option($name) {
		if ( array_key_exists($name, $this->options) ) {
			return $this->options[$name];
		}
		return null;
	}

	/**
	 * Update a plugin configuration value. Saves immediately.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set_plugin_option($name, $value) {
		$this->options[$name] = $value;
		$this->save_options();
	}

	/**
	 * Log a security-related message.
	 *
	 * @param string|array $message The message to add tot he log, or an array of messages.
	 */
	private function log_security_note($message) {
		if ( !$this->should_store_security_log() ) {
			return;
		}
		if ( is_array($message) ) {
			$this->security_log = array_merge($this->security_log, $message);
		} else {
			$this->security_log[] = $message;
		}
	}

	private function should_store_security_log() {
		return (
			$this->options['security_logging_enabled']
			|| ($this->options['error_verbosity'] >= self::VERBOSITY_VERBOSE)
		);
	}

	/**
	 * Callback for "admin_notices".
	 */
	public function display_security_log() {
		?>
		<div class="updated">
			<h3>Admin Menu Editor security log</h3>
			<?php echo $this->get_formatted_security_log(); ?>
		</div>
		<?php
	}

	/**
	 * Get the security log in HTML format.
	 *
	 * @return string
	 */
	private function get_formatted_security_log() {
		$log = '<div style="font: 12px/17px Consolas, monospace; margin-bottom: 1em;">';
		$log .= implode("<br>\n", $this->security_log);
		$log .= '</div>';
		return $log;
	}

	public function get_security_log() {
		return $this->security_log;
	}

	/**
	 * WPML support: Update strings that need translation.
	 *
	 * @param array $old_menu The old custom menu, if any.
	 * @param array $custom_menu The new custom menu.
	 */
	private function update_wpml_strings($old_menu, $custom_menu) {
		if ( !function_exists('icl_register_string') ) {
			return;
		}

		$previous_strings = $this->get_wpml_strings($old_menu);
		$new_strings = $this->get_wpml_strings($custom_menu);

		//Delete strings that are no longer valid.
		if ( function_exists('icl_unregister_string') ) {
			$removed_strings = array_diff_key($previous_strings, $new_strings);
			foreach($removed_strings as $name => $value) {
				icl_unregister_string(self::WPML_CONTEXT, $name);
			}
		}

		//Register/update the new menu strings.
		foreach($new_strings as $name => $value) {
			icl_register_string(self::WPML_CONTEXT, $name, $value);
		}
	}

	/**
	 * Prepare WPML translation strings for all menu and page titles
	 * in the specified menu. Includes only custom titles.
	 *
	 * @param array $custom_menu
	 * @return array Associative array of strings that can be translated, indexed by unique name.
	 */
	private function get_wpml_strings($custom_menu) {
		if ( empty($custom_menu) ) {
			return array();
		}

		$strings = array();
		$translatable_fields = array('menu_title', 'page_title');
		foreach($custom_menu['tree'] as $top_menu) {
			if ( $top_menu['separator'] ) {
				continue;
			}

			foreach($translatable_fields as $field) {
				if ( isset($top_menu[$field]) ) {
					$name = $this->get_wpml_name_for($top_menu, $field);
					$strings[$name] = ameMenuItem::get($top_menu, $field);
				}
			}

			if ( isset($top_menu['items']) && !empty($top_menu['items']) ) {
				foreach($top_menu['items'] as $item) {
					if ( $item['separator'] ) {
						continue;
					}

					foreach($translatable_fields as $field) {
						if ( isset($item[$field]) ) {
							$name = $this->get_wpml_name_for($item, $field);
							$strings[$name] = ameMenuItem::get($item, $field);
						}
					}
				}
			}
		}

		return $strings;
	}

	/**
	 * Create a unique name for a specific field of a specific menu item.
	 * Intended for use with the icl_register_string() function.
	 *
	 * @param array $item Admin menu item in the internal format.
	 * @param string $field Field name.
	 * @return string
	 */
	private function get_wpml_name_for($item, $field = '') {
		$name = ameMenuItem::get($item, 'template_id');
		if ( empty($name) ) {
			$name = 'custom: ' . ameMenuItem::get($item, 'file');
		}
		if ( !empty($field) ) {
			$name = $name . '[' . $field. ']';
		}
		return $name;
	}

	/**
	 * Compatibility fix for bbPress 2.5.3.
	 *
	 * bbPress creates a bunch of "hidden" menu items in the admin_menu action only to remove them
	 * later in an admin_head hook. This results in apparently duplicated menus showing up when AME is
	 * active because AME processes the items before they get removed.
	 *
	 * This method works around the issue by explicitly removing those bbPress menus.
	 *
	 * @uses $this->default_wp_submenu
	 */
	private function apply_bbpress_compat_fix() {
		if ( !isset($this->default_wp_submenu, $this->default_wp_submenu['index.php']) ) {
			return;
		}

		//Note to self: This would be easier if we could rely on anonymous function support being available.
		//Then we could just array_filter() the submenu with a closure as the callback.
		$items_to_remove = array('bbp-about' => null, 'bbp-credits' => null);
		foreach($this->default_wp_submenu['index.php'] as $index => $menu) {
			if ( array_key_exists($menu[2], $items_to_remove) ) {
				$items_to_remove[$menu[2]] = $index;
			}
		}

		foreach($items_to_remove as $index) {
			if ( isset($index, $this->default_wp_submenu['index.php'][$index]) ) {
				unset($this->default_wp_submenu['index.php'][$index]);
			}
		}
	}

	/**
	 * Compatibility fix for WooCommerce 2.2.1+.
	 * Summary: When AME is active, an unusable WooCommerce -> WooCommerce menu item shows up. Here we remove it.
	 *
	 * WooCommerce creates a top level "WooCommerce" menu with no callback. By default, WordPress automatically adds
	 * a submenu item with the same name. However, since the item doesn't have a callback, it is unusable and clicking
	 * it just triggers a "Cannot load woocommerce" error. So WooCommerce removes this item in an admin_head hook to
	 * hide it. With AME active, the item shows up anyway, and users get confused by the error.
	 *
	 * Fix it by removing the problematic menu item.
	 *
	 * Caution: If the user hides all WooCommerce submenus but not the top level menu, the WooCommerce menu will still
	 * show up but be inaccessible. This may be slightly counter-intuitive, but seems reasonable.
	 */
	private function apply_woocommerce_compat_fix() {
		if ( !isset($this->default_wp_submenu, $this->default_wp_submenu['woocommerce']) ) {
			return;
		}

		$badSubmenuExists = isset($this->default_wp_submenu['woocommerce'][0])
			&& isset($this->default_wp_submenu['woocommerce'][0][2])
			&& ($this->default_wp_submenu['woocommerce'][0][2] === 'woocommerce');
		$anotherSubmenuExists = isset($this->default_wp_submenu['woocommerce'][1]);

		if ( $badSubmenuExists && $anotherSubmenuExists ) {
			$this->default_wp_submenu['woocommerce'][0] = $this->default_wp_submenu['woocommerce'][1];
			unset($this->default_wp_submenu['woocommerce'][1]);
		}
	}

	/**
	 * Compatibility fix for WooCommerce 2.6.8+.
	 *
	 * Summary: The "WooCommerce -> Orders" menu item includes an info bubble showing the number of new orders.
	 * When AME is active, this number doesn't show up. This workaround re-adds the info bubble.
	 *
	 * For some inexplicable reason, WooCommerce first creates the "Orders" menu item without the info bubble.
	 * Then it adds the number of new orders later by modifying the global $submenu array in a separate "admin_head"
	 * hook. However, by that time AME has already processed the admin menu, so it doesn't see the change.
	 *
	 * Workaround: Run the relevant WooCommerce callback during the "admin_menu" action (before processing the menu).
	 * The now-redundant"admin_head" hook is then removed.
	 */
	private function apply_woocommerce_order_count_fix() {
		global $wp_filter;
		if ( !class_exists('WC_Admin_Menus', false) || !isset($wp_filter['admin_head'][10]) || did_action('admin_head') ) {
			return;
		}

		//Find the WooCommerce callback that adds order count to the menu.
		//It's the menu_order_count method defined in /woocommerce/includes/admin/class-wc-admin-menus.php.
		foreach($wp_filter['admin_head'][10] as $key => $filter) {
			if (!isset($filter['function']) || !is_array($filter['function'])) {
				continue;
			}

			$callback = $filter['function'];
			if (
				(count($callback) === 2)
				&& ($callback[1] === 'menu_order_count')
				&& (get_class($callback[0]) === 'WC_Admin_Menus')
			) {
				//Run it now, not in admin_head.
				call_user_func($callback);
				remove_action('admin_head', $callback, 10);
				break;
			}
		}
	}

	/**
	 * Compatibility fix for WordPress Mu Domain Mapping 0.5.4.3.
	 *
	 * The aforementioned domain mapping plugin has a bug that makes the plugins_url() function
	 * return incorrect URLs for plugins installed in /mu-plugins. Fixed by removing the offending
	 * filter callback.
	 *
	 * Note that this won't break domain mapping. Domain Mapping adds two 'plugins_url' filters.
	 * The buggy one is completely redundant and can be removed with no ill effects.
	 */
	private function apply_wpmu_domain_mapping_fix() {
		$priority = has_filter('plugins_url', 'domain_mapping_plugins_uri');
		if ( ($priority !== false) && (has_filter('plugins_url', 'domain_mapping_post_content') !== false) ) {
			remove_filter('plugins_url', 'domain_mapping_plugins_uri', $priority);
		}
	}

	/**
	 * Compatibility fix for Divi Training 1.3.5.
	 *
	 * The Divi Training plugin adds a whole lot of "hidden" submenu items to the Dashboard menu
	 * and then removes them later. Lets get rid of them.
	 */
	private function apply_divi_training_fix() {
		if ( !class_exists('Wm_Divi_Training_Admin', false) ) {
			return;
		}
		if ( !isset($this->default_wp_submenu, $this->default_wp_submenu['index.php']) ) {
			return;
		}

		$items_to_remove = array();
		foreach($this->default_wp_submenu['index.php'] as $index => $menu) {
			//There's a lot of items, so we search for a common prefix instead of of including an explicit list.
			//
			if ( (strpos($menu[2], 'wm-divi-training-the-divi-') === 0) || ($menu[2] === 'wm-divi-training-updates')) {
				$items_to_remove[] = $index;
			}
		}
		foreach($items_to_remove as $index) {
			if ( isset($index, $this->default_wp_submenu['index.php'][$index]) ) {
				unset($this->default_wp_submenu['index.php'][$index]);
			}
		}
	}

	/**
	 * As of WP 3.5, the Links Manager is hidden by default. It's only visible if the user has existing links
	 * or they choose to enable it by installing the Links Manager plugin.
	 *
	 * However, the "Links" menu still exists. This can be confusing to users who will now see an apparently
	 * useless menu item that can't be enabled (since they don't have the Links Manager plugin) and can't be
	 * deleted either (since it's a default menu). To remedy that, hide the default "Links" menu.
	 */
	private function remove_link_manager_menus() {
		//Find the "Links" menu.
		$links_index = null;
		$links_slug = null;
		foreach($this->default_wp_menu as $index => $menu) {
			if ( ($menu[1] === 'manage_links') && isset($menu[5]) && ($menu[5] === 'menu-links') ) {
				$links_index = $index;
				$links_slug = $menu[2];
			}
		}

		//Remove the default "Links" submenus, but leave custom items created by other plugins.
		if ( isset($this->default_wp_submenu[$links_slug]) ) {
			$this->default_wp_submenu[$links_slug] = array_filter(
				$this->default_wp_submenu[$links_slug],
				array($this, 'filter_default_links_submenus')
			);
			if ( empty($this->default_wp_submenu[$links_slug]) ) {
				unset($this->default_wp_submenu[$links_slug]);
			}
		}

		//Remove the "Links" menu itself if it no longer has any children.
		if ( !isset($this->default_wp_submenu[$links_slug]) ) {
			unset($this->default_wp_menu[$links_index]);
		}
	}

	private function filter_default_links_submenus($item) {
		$default_items = array('link-manager.php', 'link-add.php', 'edit-tags.php?taxonomy=link_category');
		$is_default = isset($item[2]) && in_array($item[2], $default_items);
		return !$is_default;
	}

	/**
	 * Get a user's roles.
	 *
	 * "Why not just read the $user->roles array directly?", you may ask. Because some popular plugins have a really
	 * nasty bug where they inadvertently remove entries from that array. Specifically, they retrieve the first user
	 * role like this:
	 *
	 * $roleName = array_shift($currentUser->roles);
	 *
	 * What some plugin developers fail to realize is that, in addition to returning the first entry, array_shift()
	 * also *removes* it from the array. As a result, $user->roles is now missing one of the user's roles. This bug
	 * doesn't cause major problems only because most plugins check capabilities and don't care about roles as such.
	 * AME needs to know the roles because some menu permissions are set per role.
	 *
	 * Known buggy plugins:
	 * - W3 Total Cache 0.9.4.1
	 *
	 * The current workaround is to cache the role list before it can get corrupted by other plugins. This approach
	 * has its own risks (cache invalidation is hard), but it should be reasonably safe assuming that everyone uses
	 * only standard WP APIs to modify user roles (e.g. @see WP_User::add_role ).
	 *
	 * @param WP_User $user
	 * @return array
	 */
	public function get_user_roles($user) {
		if ( empty($user) ) {
			return array();
		}
		if ( !$user->exists() ) {
			//Note: In rare cases, WP_User::$roles can be false. For AME it's more convenient to have an empty list.
			return (!empty($user->roles) ? $user->roles : array());
		}

		if ( !isset($this->cached_user_roles[$user->ID]) ) {
			$this->cached_user_roles[$user->ID] = $this->extract_user_roles($user);
		}
		return $this->cached_user_roles[$user->ID];
	}

	/**
	 * The current user has changed; cache their roles.
	 */
	public function update_current_user_cache() {
		$user = wp_get_current_user();
		if ( empty($user) || !$user->exists() ) {
			return;
		}
		$this->cached_user_roles[$user->ID] = $this->extract_user_roles($user);
	}

	/**
	 * Get user roles by parsing their capabilities.
	 *
	 * This method is reliable because it determines user roles the same way that WordPress does. However, it's also
	 * relatively "slow" (~ 25 microseconds on my dev. system). Don't call it directly. Use get_user_roles() instead -
	 * it caches results.
	 *
	 * @see WP_User::get_role_caps
	 *
	 * @param WP_User $user
	 * @return array
	 */
	private function extract_user_roles($user) {
		if ( empty($user->caps) || !is_array($user->caps) ) {
			return (!empty($user->roles) ? $user->roles : array());
		}
		$wp_roles = ameRoleUtils::get_roles();
		return array_filter(array_keys($user->caps), array($wp_roles, 'is_role'));
	}

	/**
	 * User metadata was updated or deleted; invalidate the role cache.
	 *
	 * Not all metadata updates are related to role changes, but filtering them is non-trivial (meta keys change)
	 * and not really necessary for our purposes.
	 *
	 * @param int|array $unused_meta_id
	 * @param int $user_id
	 */
	public function clear_user_role_cache(/** @noinspection PhpUnusedParameterInspection */$unused_meta_id, $user_id) {
		if ( empty($user_id) || !is_numeric($user_id) ) {
			return;
		}
		unset($this->cached_user_roles[$user_id]);
	}

	/**
	 * Get registered public post types.
	 * @return array
	 */
	private function get_post_type_details() {
		$results = array();

		$post_types = get_post_types(array('public' => true, 'show_ui' => true), 'objects', 'or');
		$meta_caps = array('edit_post', 'read_post', 'delete_post');

		foreach($post_types as $id => $post_type) {
			$title = $id;
			if (isset($post_type->labels, $post_type->labels->name) && !empty($post_type->labels->name)) {
				$title = $post_type->labels->name;
			}

			$capabilities = array();
			foreach((array)$post_type->cap as $cap_type => $capability) {
				//Skip meta caps.
				if ($post_type->map_meta_cap && in_array($cap_type, $meta_caps)) {
					continue;
				}

				//Skip the "read" cap. It's redundant - most CPTs use it, and all roles have it by default.
				if (($cap_type === 'read') && ($capability === 'read')) {
					continue;
				}

				$capabilities[$cap_type] = $capability;
			}

			$results[$id] = array(
				'id' => $id,
				'title' => $title,
				'capabilities' => $capabilities,
			);
		}

		return $results;
	}

	/**
	 * Get registered taxonomies.
	 * @return array
	 */
	private function get_taxonomy_details() {
		$results = array();
		$taxonomies = get_taxonomies(array('public' => true, 'show_ui' => true), 'objects', 'or');

		foreach($taxonomies as $id => $taxonomy) {
			$title = $id;
			if (isset($taxonomy->labels, $taxonomy->labels->name) && !empty($taxonomy->labels->name)) {
				$title = $taxonomy->labels->name;
			}

			$capabilities = array();
			foreach((array)$taxonomy->cap as $cap_type => $capability) {
				//Skip the "read" cap. It's redundant - most CPTs use it, and all roles have it by default.
				if (($cap_type === 'read') && ($capability === 'read')) {
					continue;
				}
				$capabilities[$cap_type] = $capability;
			}

			$results[$id] = array(
				'id' => $id,
				'title' => $title,
				'capabilities' => $capabilities,
			);
		}

		return $results;
	}

	/**
	 * Tell new users how to access the plugin settings page.
	 */
	public function display_plugin_menu_notice() {
		//Display the notice only if it's enabled, the current user can access our settings page,
		//and there is no custom menu (if a custom menu already exists, chances are the user knows
		//where the settings page is).
		$showNotice = $this->options['show_plugin_menu_notice'] && ($this->load_custom_menu() === null);
		$showNotice = $showNotice && $this->current_user_can_edit_menu();
		if ( !$showNotice ) {
			return;
		}

		//Disable the notice when the user hides it or visits any of our admin pages.
		$hideNoticeParameter = 'ame-plugin-menu-notice';
		if ( !empty($_GET[$hideNoticeParameter]) || $this->is_editor_page() || $this->is_settings_page() ) {
			$this->options['show_plugin_menu_notice'] = false;
			$this->save_options();
			return;
		}

		$dismissUrl = add_query_arg($hideNoticeParameter, 'hide');
		$dismissUrl = remove_query_arg(array('message', 'activate'), $dismissUrl);

		if ( is_multisite() && is_network_admin() ) {
			$message = 'Tip: Go to any subsite to access Admin Menu Editor. It will not show up in the network admin.';
		} else {
			$message = 'Tip: Go to <a href="%1$s">Settings -&gt; %2$s</a> to start customizing the admin menu.';
		}
		printf(
			'<div class="updated" id="ame-plugin-menu-notice">
				<p>' . $message . '</p>
				<p><a href="%3$s" id="ame-hide-plugin-menu-notice">Hide this message</a></p>
			 </div>',
			esc_attr(admin_url($this->settings_link)),
			apply_filters('admin_menu_editor-self_menu_title', 'Menu Editor'),
			esc_attr($dismissUrl)
		);

	}

	public function is_pro_version() {
		return apply_filters('admin_menu_editor_is_pro', false);
	}

	/**
	 * Get the WordPress version number.
	 *
	 * Warning: Some plugins change the WordPress version number to hide the installed version from visitors.
	 * It's a security-by-obscurity technique. This means you can't rely on the number being correct.
	 *
	 * @return string Either the version number or an empty string.
	 */
	private static function get_wp_version() {
		if ( isset($GLOBALS['wp_version']) ) {
			return $GLOBALS['wp_version'];
		}
		return '';
	}

	/**
	 * @return array
	 */
	private function load_cap_power() {
		$cap_power = array();

		$power_filename = AME_ROOT_DIR . '/includes/capabilities/cap-power.csv';
		if ( is_file($power_filename) && is_readable($power_filename) ) {
			$csv = fopen($power_filename, 'r');
			$firstLineSkipped = false;

			while ($csv && !feof($csv)) {
				$line = fgetcsv($csv, 1000, ';');
				if ( !$firstLineSkipped ) {
					$firstLineSkipped = true;
					continue;
				}

				if ( is_array($line) && (count($line) >= 2) ) {
					$cap_power[strval($line[0])] = floatval(str_replace(',', '.', $line[1]));
				}
			}
			fclose($csv);

			arsort($cap_power);
		}

		return $cap_power;
	}

	public function add_plugin_row_meta_links($pluginMeta, $pluginFile) {
		$isRelevant = ($pluginFile == $this->plugin_basename);

		if ( $isRelevant && $this->current_user_can_edit_menu() ) {
			$documentationUrl = $this->is_pro_version()
				? 'https://adminmenueditor.com/documentation/'
				: 'https://adminmenueditor.com/free-version-docs/';
			$pluginMeta[] = sprintf(
				'<a href="%s">%s</a>',
				esc_attr($documentationUrl),
				'Documentation'
			);
		}

		return $pluginMeta;
	}

	private function get_active_modules() {
		$modules = $this->get_available_modules();

		$activeModules = array();
		foreach ($modules as $id => $module) {
			if ( $this->is_module_active($id, $module) ) {
				$activeModules[$id] = $module;
			}
		}

		return $activeModules;
	}

	public function get_available_modules() {
		$modules = array(
			'actor-selector' => array(
				'relativePath' => 'modules/actor-selector/actor-selector.php',
				'className' => 'ameActorSelector',
				'isAlwaysActive' => true,
			),
			'visible-users' => array(
				'relativePath' => 'extras/modules/visible-users/visible-users.php',
				'className' => 'ameVisibleUsers',
				'isAlwaysActive' => true,
			),
			'metaboxes' => array(
				'relativePath' => 'extras/modules/metaboxes/load.php',
				'className' => 'ameMetaBoxEditor',
				'requiredPhpVersion' => '5.3',
				'title' => 'Meta Boxes',
			),
			'dashboard-widget-editor' => array(
				'relativePath' => 'extras/modules/dashboard-widget-editor/load.php',
				'className' => 'ameWidgetEditor',
				'requiredPhpVersion' => '5.3',
				'title' => 'Dashboard Widgets',
			),
			'plugin-visibility' => array(
				'relativePath' => 'modules/plugin-visibility/plugin-visibility.php',
				'className' => 'amePluginVisibility',
				'title' => 'Plugins',
			),
			'super-users' => array(
				'relativePath' => 'extras/modules/super-users/super-users.php',
				'className' => 'ameSuperUsers',
				'title' => 'Hidden Users',
			),
			/*'admin-css' => array(
				'relativePath' => 'modules/admin-css/admin-css.php',
				'className' => 'ameAdminCss',
				'title' => 'Admin CSS',
			),*/
			/*'tweaks' => array(
				'relativePath' => 'modules/tweaks/tweaks.php',
				'className' => 'ameTweakManager',
				'title' => 'Tweaks',
			),*/
			'hide-admin-menu' => array(
				'relativePath' => 'extras/modules/hide-admin-menu/hide-admin-menu.php',
				'className' => 'ameAdminMenuHider',
				'title' => '"Show the admin menu" checkbox',
			),
			'hide-admin-bar' => array(
				'relativePath' => 'extras/modules/hide-admin-bar/hide-admin-bar.php',
				'className' => 'ameAdminBarHider',
				'title' => '"Show the Toolbar" checkbox',
			),
			'highlight-new-menus' => array(
				'relativePath' => 'modules/highlight-new-menus/highlight-new-menus.php',
				'className' => 'ameMenuHighlighterWrapper',
				'title' => 'Highlight new menu items',
				'requiredPhpVersion' => '5.3',
			),
		);

		foreach($modules as &$module) {
			if (!empty($module['relativePath'])) {
				$module['path'] = AME_ROOT_DIR . '/' . $module['relativePath'];
			}
		}
		unset($module);

		$modules = apply_filters('admin_menu_editor-available_modules', $modules);

		$modules = array_filter($modules, array($this, 'module_path_exists'));

		return $modules;
	}

	private function module_path_exists($module) {
		return !empty($module['path']) && file_exists($module['path']);
	}

	public function is_module_compatible($module) {
		if ( !empty($module['requiredPhpVersion']) ) {
			return version_compare(phpversion(), $module['requiredPhpVersion'], '>=');
		}
		return true;
	}

	public function is_module_active($id, $module) {
		if ( !$this->is_module_compatible($module) ) {
			return false;
		}
		if ( !empty($module['isAlwaysActive']) ) {
			return true;
		}
		if ( isset($this->options['is_active_module'][$id]) ) {
			return $this->options['is_active_module'][$id];
		}
		return true;
	}

} //class


class ameMenuTemplateBuilder {
	private $templates = array();

	private $parentNames = array();
	private $blacklist = array();

	private $templateOrder = array();
	private $previousItemId = '';
	private $wasPreviousItemSeparated = false;

	/**
	 * Populate a lookup array with default values (templates) from $menu and $submenu.
	 * Used later to merge a custom menu with the native WordPress menu structure.
	 *
	 * @param array $menu
	 * @param array $submenu
	 * @param array $blacklist
	 * @return array An array of menu templates and their default values.
	 */
	public function build($menu, $submenu, $blacklist = array()){
		$this->templates = array();
		$this->blacklist = $blacklist;

		//At this point, the menu might not be sorted yet, especially if other plugins have made changes to it.
		//We need to know the relative order of menus to insert new items in the right place.
		ksort($menu, SORT_NUMERIC);

		foreach($menu as $pos => $item){
			$this->addItem($item, $pos);
		}

		foreach($submenu as $parent => $items){
			//Skip sub-menus attached to non-existent parents. This should theoretically never happen,
			//but a buggy plugin can cause such a situation.
			if ( !isset($this->parentNames[$parent]) ) {
				continue;
			}

			ksort($items, SORT_NUMERIC);
			$this->previousItemId = '';
			$this->wasPreviousItemSeparated = false;

			foreach($items as $pos => $item) {
				$this->addItem($item, $pos, $parent);
			}
		}

		return $this->templates;
	}

	/**
	 * Add a menu item as a template.
	 *
	 * @param array $wpItem
	 * @param int $position
	 * @param string|null $parent
	 */
	private function addItem($wpItem, $position, $parent = null) {
		$item = ameMenuItem::fromWpItem($wpItem, $position, $parent);

		//Skip separators.
		if ( $item['separator'] ) {
			$this->wasPreviousItemSeparated = true;
			return;
		}

		//Skip blacklisted menus.
		//BUG: We shouldn't skip top level menus that have non-blacklisted submenu items.
		if ( isset($item['url'], $this->blacklist[$item['url']]) ) {
			return;
		}

		$name = $this->sanitizeMenuTitle($item['menu_title']);
		if ( $parent === null ) {
			$this->parentNames[$item['file']] = $name;
		} else {
			$name = $this->parentNames[$parent] . ' -> ' . $name;
		}

		$templateId = ameMenuItem::template_id($item);
		unset($item['template_id']);

		$this->templates[$templateId] = array(
			'name'     => $name,
			'used'     => false,
			'defaults' => $item,
		);

		//Remember the relative order of menu items. It's a bit like a linked list.
		$this->templateOrder[$templateId] = array(
			'previous_item' => $this->previousItemId,
			'was_previous_item_separated' => $this->wasPreviousItemSeparated,
		);
		$this->previousItemId = $templateId;
		$this->wasPreviousItemSeparated = false;
	}

	/**
	 * Sanitize a menu title for display.
	 * Removes HTML tags and update notification bubbles. Truncates long titles.
	 *
	 * @param string $title
	 * @return string
	 */
	private function sanitizeMenuTitle($title) {
		$title = strip_tags( preg_replace('@<span[^>]*>.*</span>@i', '', $title) );

		//Compact whitespace.
		$title = rtrim(preg_replace('@[\s\t\r\n]+@', ' ', $title));

		$maxLength = 50;
		if ( strlen($title) > $maxLength ) {
			$title = rtrim(substr($title, 0, $maxLength)) . '...';
		}

		return $title;
	}

	public function getRelativeTemplateOrder() {
		return $this->templateOrder;
	}
}