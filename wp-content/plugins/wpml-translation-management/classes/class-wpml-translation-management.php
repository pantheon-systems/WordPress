<?php
/**
 * Class WPML_Translation_Management
 */
class WPML_Translation_Management {

	const PAGE_SLUG_MANAGEMENT = '/menu/main.php';
	const PAGE_SLUG_SETTINGS   = '/menu/settings';
	const PAGE_SLUG_QUEUE      = '/menu/translations-queue.php';

	var $load_priority = 200;

	/** @var  SitePress $sitepress */
	protected $sitepress;

	/** @var  WPML_TM_Loader $tm_loader */
	private $tm_loader;

	/** @var  TranslationManagement $tm_instance */
	private $tm_instance;

	/** @var  WPML_Translations_Queue $tm_queue */
	private $tm_queue;

	/** @var WPML_TM_Menus_Management $wpml_tm_menus_management */
	private $wpml_tm_menus_management;

	/** @var WPML_Ajax_Route $ajax_route */
	private $ajax_route;

	/**
	 * @var WPML_TP_Translator
	 */
	private $wpml_tp_translator;

	/** @var  WPML_UI_Screen_Options_Pagination $dashboard_screen_options */
	private $dashboard_screen_options;
	/**
	 * WPML_Translation_Management constructor.
	 *
	 * @param SitePress $sitepress
	 * @param WPML_TM_Loader $tm_loader
	 * @param TranslationManagement $tm_instance
	 * @param WPML_TP_Translator $wpml_tp_translator
	 */
	function __construct( $sitepress, $tm_loader, $tm_instance, WPML_TP_Translator $wpml_tp_translator = null ) {
		$this->sitepress = $sitepress;

		$this->tm_loader     = $tm_loader;
		$this->tm_instance   = $tm_instance;

		$this->wpml_tp_translator = $wpml_tp_translator;
	}

	public function init() {
		global $wpdb;

		$template_service_loader        = new WPML_Twig_Template_Loader( array( WPML_TM_PATH . '/templates/tm-menus/' ) );
		$manager_records                = new WPML_Translation_Manager_Records( $wpdb, new WPML_WP_User_Query_Factory() );
		$translator_records             = new WPML_Translator_Records( $wpdb, new WPML_WP_User_Query_Factory() );
		$this->wpml_tm_menus_management = new WPML_TM_Menus_Management( $template_service_loader->get_template(), $manager_records, $translator_records );

	  $mcs_factory = new WPML_TM_Scripts_Factory();
	  $mcs_factory->init_hooks();

	  if ( null === $this->wpml_tp_translator ) {
			$this->wpml_tp_translator = new WPML_TP_Translator();
		}
		$this->ajax_route    = new WPML_Ajax_Route( new WPML_TM_Ajax_Factory( $wpdb, $this->sitepress, $_POST ) );

	}

	function load() {
		global $pagenow;

		$this->tm_loader->tm_after_load();
		$wpml_wp_api                   = $this->sitepress->get_wp_api();
		if ( $wpml_wp_api->is_admin() ) {
			$this->tm_loader->load_xliff_frontend();
		}
		$this->plugin_localization();

		add_action('wp_ajax_basket_extra_fields_refresh', array($this, 'basket_extra_fields_refresh') );

		// Check if WPML is active. If not display warning message and not load Sticky links
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				add_action( 'admin_notices', array( $this, '_no_wpml_warning' ) );
			}
			return false;
		} elseif ( ! $this->sitepress->get_setting( 'setup_complete' ) ) {
			$this->maybe_show_wpml_not_installed_warning();

			return false;
		}
		if ( isset( $_GET['icl_action'] ) ) {
			if ( $_GET['icl_action'] === 'reminder_popup'
			     && isset( $_GET['_icl_nonce'] )
			     && wp_verify_nonce( $_GET['_icl_nonce'], 'reminder_popup_nonce' ) === 1
			) {
				add_action( 'init', array( 'TranslationProxy_Popup', 'display' ) );
			} elseif ( $_GET['icl_action'] === 'dismiss_help' ) {
				$this->sitepress->set_setting( 'dont_show_help_admin_notice', true, true );
			}
		}
		if ( isset( $_GET['wpml_tm_saved'] ) ) {
			add_action( 'admin_notices', array( $this, 'job_saved_message' ) );
		}
		if ( isset( $_GET['wpml_tm_cancel'] ) ) {
			add_action( 'admin_notices', array( $this, 'job_cancelled_message' ) );
		}
		$this->tm_loader->load_pro_translation( $wpml_wp_api );
		add_action( 'wp_ajax_wpml_check_batch_status', array($this, 'check_batch_status_ajax') );
		if ( $wpml_wp_api->is_admin() ) {
			add_action( 'init', array( $this, 'automatic_service_selection_action' ) );
			add_action( 'translation_service_authentication', array( $this, 'translation_service_authentication' ) );
			add_filter( 'translation_service_js_data', array( $this, 'translation_service_js_data' ) );
			add_filter( 'wpml_string_status_text',
			            array( 'WPML_Remote_String_Translation', 'string_status_text_filter' ),
			            10,
			            3 );
			add_action( 'trashed_post', array( $this, 'trashed_post_actions' ), 10, 2 );
			add_action( 'wp_ajax_icl_get_jobs_table', 'icl_get_jobs_table' );
			add_action( 'wp_ajax_icl_cancel_translation_jobs', 'icl_cancel_translation_jobs' );
			add_action( 'wp_ajax_icl_populate_translations_pickup_box', 'icl_populate_translations_pickup_box' );
			add_action( 'wp_ajax_icl_pickup_translations', 'icl_pickup_translations' );
			add_action( 'wp_ajax_icl_pickup_translations_complete', 'icl_pickup_translations_complete' );
			add_action( 'wp_ajax_get_translator_status', array( 'TranslationProxy_Translator', 'get_translator_status_ajax' ) );
			add_action( 'wp_ajax_wpml-flush-website-details-cache', array( 'TranslationProxy_Translator', 'flush_website_details_cache_action' ) );
			add_action( 'wpml_updated_translation_status', array( 'TranslationProxy_Batch', 'maybe_assign_generic_batch' ),  10, 2 );
			add_action( 'init', array($this, 'handle_notices_action' ) );
			do_action( 'wpml_tm_init' );
		if ( $pagenow !== 'customize.php' ) { // stop TM scripts from messing up theme customizer
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ), 11 );
			$this->add_custom_xml_config();
			}

			add_action( 'wpml_admin_menu_configure', array( $this, 'management_menu' ) );
			add_action( 'wpml_admin_menu_configure', array( $this, 'translators_menu' ) );
			add_action( 'wpml_admin_menu_configure', array( $this, 'settings_menu' ) );
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

			if ( $this->sitepress->get_wp_api()
			                     ->is_translation_queue_page() ) {

				$translation_queue_factory = new WPML_Translations_Queue_Factory();

				$this->tm_queue = $translation_queue_factory->create();
				$this->tm_queue->init_hooks();
			}

			if ( $this->sitepress->get_wp_api()->is_dashboard_tab() ) {
				$screen_options_factory = new WPML_UI_Screen_Options_Factory( $this->sitepress );
				$this->dashboard_screen_options = $screen_options_factory->create_pagination( 'tm_dashboard_per_page', ICL_TM_DOCS_PER_PAGE );
			}

			// Add a nice warning message if the user tries to edit a post manually and it's actually in the process of being translated
			if ( in_array( $pagenow, array( 'post-new.php', 'post.php', 'admin-ajax.php' ), true ) ) {
				$post_edit_notices_factory = new WPML_TM_Post_Edit_Notices_Factory();
				$post_edit_notices_factory->create()->add_hooks();
			}

			add_action( 'wp_ajax_dismiss_icl_side_by_site', array( $this, 'dismiss_icl_side_by_site' ) );
			add_action( 'wp_ajax_icl_tm_toggle_promo', array( $this, '_icl_tm_toggle_promo' ) );
			add_action ( 'wpml_support_page_after', array( $this, 'add_com_log_link' ) );
			add_action ( 'wpml_translation_basket_page_after', array( $this, 'add_com_log_link' ) );

			$this->translate_independently();
		}

	  if ( $wpml_wp_api->is_admin() || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			$page_builder_hooks = new WPML_TM_Page_Builders_Hooks( null, $this->sitepress );
			$page_builder_hooks->init_hooks();
		}

		$this->api_hooks();

		add_filter( 'wpml_config_white_list_pages', array( $this, 'filter_wpml_config_white_list_pages' ) );
		do_action( 'wpml_tm_loaded' );

		return true;
	}

	public function api_hooks() {
		add_action( 'wpml_save_custom_field_translation_option', array(
			$this,
			'wpml_save_custom_field_translation_option'
		), 10, 2 );
	}

	public function filter_wpml_config_white_list_pages( array $white_list_pages ) {
		$white_list_pages[] = WPML_TM_FOLDER . self::PAGE_SLUG_MANAGEMENT;
		$white_list_pages[] = WPML_TM_FOLDER . self::PAGE_SLUG_SETTINGS;

		return $white_list_pages;
	}

	public function maybe_show_wpml_not_installed_warning() {
		if ( ! ( isset( $_GET['page'] ) && 'sitepress-multilingual-cms/menu/languages.php' === $_GET['page'] ) ) {
			add_action( 'admin_notices', array( $this, '_wpml_not_installed_warning' ) );
		}
	}

	private function translate_independently() {
		global $wpdb;
		if (
			( isset( $_GET['sm'] ) && 'basket' === $_GET['sm'] )
			||
			( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && 'icl_disconnect_posts' === $_POST['action'] )
		) {
			$translation_basket      = new WPML_Translation_Basket( $wpdb );
			$translate_independently = new WPML_TM_Translate_Independently( $this->tm_instance, $translation_basket, $this->sitepress );
			$translate_independently->init();
		}
	}

	function trashed_post_actions( $post_id ) {
		//Removes trashed post from the basket
		TranslationProxy_Basket::delete_item_from_basket( $post_id );
	}

	function is_jobs_tab() {
		return $this->is_tm_page('jobs');
	}

	function is_translators_tab() {
		return $this->is_tm_page('translators');
	}

	function is_translation_services_tab() {
		return $this->is_tm_page('translation-services');
	}

	function admin_enqueue_scripts() {
		if ( ! defined( 'DOING_AJAX' ) ) {

			wp_register_script( 'wpml-tm-progressbar', WPML_TM_URL . '/res/js/wpml-progressbar.js', array(
					'jquery',
					'jquery-ui-progressbar',
					'backbone',
			), WPML_TM_VERSION );
			wp_register_script( 'wpml-tm-scripts', WPML_TM_URL . '/res/js/scripts.js', array(
					'jquery',
					'wpml-tm-progressbar',
					'sitepress-scripts',
			), WPML_TM_VERSION );
			wp_enqueue_script( 'wpml-tm-scripts' );

			wp_enqueue_style('wpml-tm-styles', WPML_TM_URL . '/res/css/style.css', array(), WPML_TM_VERSION);
			wp_enqueue_style('wpml-tm-queue', WPML_TM_URL . '/res/css/translations-queue.css', array(), WPML_TM_VERSION);

			if ( filter_input(INPUT_GET, 'page') === WPML_TM_FOLDER . '/menu/main.php' ) {
				if ( isset( $_GET[ 'sm' ] ) && ( $_GET[ 'sm' ] == 'translation-services' || $_GET[ 'sm' ] === 'translators' ) ) {

					wp_register_script( 'wpml-tm-translation-translators',
					                    WPML_TM_URL . '/dist/js/translators/app.js',
					                    array( 'jquery-ui-dialog' ),
					                    WPML_TM_VERSION );
					wp_register_script( 'wpml-tm-translation-managers',
						WPML_TM_URL . '/res/js/translation-managers.js',
						array( 'wpml-tm-scripts', 'underscore' ),
						WPML_TM_VERSION );

					wp_enqueue_script( 'wpml-select-2', ICL_PLUGIN_URL . '/lib/select2/select2.min.js', array( 'jquery' ), ICL_SITEPRESS_VERSION, true );

					wp_enqueue_script( 'wpml-tm-translation-roles-select2',
						WPML_TM_URL . '/res/js/translation-roles-select2.js',
						array(),
						WPML_TM_VERSION );

					wp_enqueue_script( 'wpml-tm-set-translation-roles',
						WPML_TM_URL . '/res/js/set-translation-role.js',
						array(),
						WPML_TM_VERSION );

					$active_service = TranslationProxy::get_current_service();
					$service_name = isset($active_service->name) ? $active_service->name : __('Translation Service', 'wpml-translation-management');
					if (isset($active_service->url)) {
						$service_site_url = "<a href='{$active_service->url}' target='_blank'>{$service_name}</a>";
					} else {
						$service_site_url = $service_name;
					}
					$tm_ts_data = array(
						'strings' => array(
							'done' => __( 'Done', 'wpml-translation-management' ),
							'header' => sprintf(__('%s requires additional data', 'wpml-translation-management'), $service_name),
							'tip' => sprintf(__("You can find this at %s site", 'wpml-translation-management'), $service_site_url)
						),
					);

					$tm_tt_data = array(
						'no_matches' => __( 'No matches', 'wpml-translation-management' ),
						'found'      => __( 'User found', 'wpml-translation-management' )
					);

					$tm_ts_data = apply_filters( 'translation_service_js_data', $tm_ts_data );

					$tm_tm_data = array(
						'restNonce' => wp_create_nonce( 'wp_rest' ),
					);

					wp_localize_script( 'wpml-tm-translation-translators', 'tm_tt_data', $tm_tt_data );
					wp_enqueue_script( 'wpml-tm-translation-translators' );
					wp_localize_script( 'wpml-tm-translation-managers', 'tm_tm_data', $tm_tm_data );
					wp_enqueue_script( 'wpml-tm-translation-managers' );
				}

				wp_enqueue_script( 'wpml-tm-translation-proxy',
				                   WPML_TM_URL . '/res/js/translation-proxy.js',
				                   array( 'wpml-tm-scripts', 'jquery-ui-dialog' ),
				                   WPML_TM_VERSION );
			}

			if ( WPML_TM_Page::is_settings() ) {
				WPML_Simple_Language_Selector::enqueue_scripts();
			}

			wp_enqueue_style ( 'wp-jquery-ui-dialog' );
			wp_enqueue_script( 'thickbox' );
			do_action('wpml_tm_scripts_enqueued');
		}
	}

	function admin_print_styles() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			wp_enqueue_style( 'wpml-tm-styles',
				WPML_TM_URL . '/res/css/style.css', array( 'jquery-ui-theme', 'jquery-ui-theme' ),
				WPML_TM_VERSION );
			wp_enqueue_style( 'wpml-tm-queue',
				WPML_TM_URL . '/res/css/translations-queue.css', array(),
				WPML_TM_VERSION );
			wp_enqueue_style( 'wpml-tm-editor-css',
				WPML_TM_URL . '/res/css/translation-editor/translation-editor.css',
				array(), WPML_TM_VERSION );

			//TODO Load only in translation editor && taxonomy transaltion
			wp_enqueue_style( 'wpml-dialog' );
			wp_enqueue_style( 'otgs-ui' );
		}
	}

	function translation_service_authentication() {
		$active_service = TranslationProxy::get_current_service();
		$custom_fields  = TranslationProxy::get_custom_fields( $active_service->id );

		$auth_content[] = '<div class="js-service-authentication">';
		$auth_content[] = '<ul>';
		if ( TranslationProxy::service_requires_authentication( $active_service ) ) {
			$auth_content[]     = '<input type="hidden" name="service_id" id="service_id" value="' . $active_service->id . '" />';
			$custom_fields_data = TranslationProxy::get_custom_fields_data();
			if ( ! $custom_fields_data ) {
				$authorization_message = sprintf( __( 'To send content to translation by %1$s, you need to have an account in %1$s and enter here your authentication details.', 'wpml-translation-management' ), $active_service->name );
				$js_action             = 'js-authenticate-service';
				$authorization_button  = __( 'Authenticate', 'wpml-translation-management' );
				$authorization_button_class = 'button-primary';
				$nonce_field           = wp_nonce_field( 'authenticate_service', 'authenticate_service_nonce', true, false );
			} else {
				$authorization_message = sprintf( __( '%s is authorized.', 'wpml-translation-management' ), $active_service->name ) . '&nbsp;';
				$js_action             = 'js-invalidate-service';
				$authorization_button  = __( 'De-authorize', 'wpml-translation-management' );
				$authorization_button_class = 'button-secondary';
				$nonce_field           = wp_nonce_field( 'invalidate_service', 'invalidate_service_nonce', true, false );
			}
			$auth_content[] = '<li>';
			$auth_content[] = '<p>';
			$auth_content[] = $authorization_message;
			$auth_content[] = '</p>';
			$auth_content[] = '</li>';
			$auth_content[] = '<li>';
			$auth_content[] = '<a href="#" class="' . $js_action . ' ' . $authorization_button_class . '" data-id="' . $active_service->id . '" data-custom-fields="' . esc_attr( wp_json_encode( $custom_fields ) ) . '">';
			$auth_content[] = $authorization_button;
			$auth_content[] = '</a>';
			$auth_content[] = $nonce_field;
		}
		if(!TranslationProxy::get_tp_default_suid()) {
			$auth_content[] = '<a href="#" class="js-deactivate-service button-secondary" data-id="' . $active_service->id . '" data-custom-fields="' . esc_attr( wp_json_encode( $custom_fields ) ) . '">';
			$auth_content[] = __( 'Deactivate', 'wpml-translation-management' );
			$auth_content[] = '</a>';
		}

		if ( isset( $active_service->doc_url ) && $active_service->doc_url ) {
			$auth_content[] = '<a href="' . $active_service->doc_url . '" target="_blank">' . __( 'Documentation', 'wpml-translation-management' ) . '</a>';
		}

		$auth_content[] = '</li>';
		$auth_content[] = '</ul>';
		$auth_content[] = '</div>';

		$auth_content_full = implode("\n", $auth_content);
		ICL_AdminNotifier::display_instant_message($auth_content_full);
	}

	function check_batch_status_ajax() {
		$batch_id    = filter_input( INPUT_POST, 'batch_id', FILTER_SANITIZE_NUMBER_INT );
		$valid_nonce = wp_verify_nonce( $_POST[ 'nonce' ], $_POST[ 'action' ] );
		if ( $valid_nonce && $batch_id > 0 ) {
			$project = TranslationProxy::get_current_project();
			$project->check_status( $batch_id );
			wp_send_json_success( array( 'error' => 0 ) );
		} else {
			wp_send_json_error( __( 'Invalid request', 'wpml-translation-management' ) );
		}
	}

	function translation_service_js_data($data) {
		$data['nonce']['translation_service_authentication'] = wp_create_nonce( 'translation_service_authentication' );
		$data['nonce']['translation_service_toggle'] = wp_create_nonce( 'translation_service_toggle' );
		return $data;
	}

	function _no_wpml_warning(){
        ?>
        <div class="message error wpml-admin-notice wpml-tm-inactive wpml-inactive"><p><?php printf(__('WPML Translation Management is enabled but not effective. It requires <a href="%s">WPML</a> in order to work.', 'wpml-translation-management'),
            'https://wpml.org/'); ?></p></div>
        <?php
    }

	function _wpml_not_installed_warning() {
		?>
		<div class="message error wpml-admin-notice wpml-tm-inactive wpml-not-configured">
			<p><?php printf( __( 'WPML Translation Management is enabled but not effective. Please finish the installation of WPML first.', 'wpml-translation-management' ) ); ?></p></div>
		<?php
	}

    function _old_wpml_warning(){
        ?>
        <div class="message error wpml-admin-notice wpml-tm-inactive wpml-outdated"><p><?php printf(__('WPML Translation Management is enabled but not effective. It is not compatible with  <a href="%s">WPML</a> versions prior 2.0.5.', 'wpml-translation-management'),
            'https://wpml.org/'); ?></p></div>
        <?php
    }

	function job_saved_message() {
		?>
			<div class="message updated wpml-admin-notice"><p><?php printf(__('Translation saved.', 'wpml-translation-management') ); ?></p></div>
		<?php
	}

	function job_cancelled_message() {
		?>
			<div class="message updated wpml-admin-notice"><p><?php printf(__('Translation cancelled.', 'wpml-translation-management') ); ?></p></div>
		<?php
	}

	/**
	 * @param string $menu_id
	 */
	public function management_menu( $menu_id ) {
		if ( 'WPML' !== $menu_id ) {
			return;
		}
		$menu_label = __( 'Translation Management', 'wpml-translation-management' );

		$is_translation_manager = current_user_can( WPML_Manage_Translations_Role::CAPABILITY );

		$menu               = array();
		$menu['order']      = 400;
		$menu['page_title'] = $menu_label;
		$menu['menu_title'] = $menu_label;
		$menu['capability'] = $is_translation_manager ? WPML_Manage_Translations_Role::CAPABILITY : 'wpml_manage_translation_management';
		$menu['menu_slug']  = WPML_TM_FOLDER . self::PAGE_SLUG_MANAGEMENT;
		$menu['function']   = array( $this, 'management_page' );

		do_action( 'wpml_admin_menu_register_item', $menu );
	}

	function management_page() {
		$this->wpml_tm_menus_management->display_main( $this->dashboard_screen_options );
	}

	/**
	 * Sets up the menu items for non-admin translators pointing at the TM
	 * and ST translators interfaces
	 *
	 * @param string $menu_id
	 */
	function translators_menu( $menu_id ) {
		if ( 'WPML' !== $menu_id ) {
			return;
		}
		$wp_api = $this->sitepress->get_wp_api();

		$is_translation_manager = $wp_api->current_user_can( WPML_Manage_Translations_Role::CAPABILITY );

		$can_manage_translation_management = $is_translation_manager ||
		                                     $wp_api->current_user_can( 'wpml_manage_translation_management' );
		$has_language_pairs                = (bool) $this->tm_instance->get_current_translator()->language_pairs
		                                     === true;

	  if ( $can_manage_translation_management || $has_language_pairs ) {
		  $menu               = array();
		  $menu['order']      = 400;
		  $menu['page_title'] = __( 'Translations', 'wpml-translation-management' );
		  $menu['menu_title'] = __( 'Translations', 'wpml-translation-management' );
		  $menu['menu_slug']  = WPML_TM_FOLDER . '/menu/translations-queue.php';
		  $menu['function']   = array( $this, 'translation_queue_page' );
		  $menu['icon_url']   = ICL_PLUGIN_URL . '/res/img/icon16.png';
		  $menu['capability'] = $is_translation_manager ? WPML_Manage_Translations_Role::CAPABILITY : 'wpml_manage_translation_management';

		  if ( $can_manage_translation_management ) {
			  do_action( 'wpml_admin_menu_register_item', $menu );
		  } elseif ( $has_language_pairs ) {
			  $menu['capability'] = WPML_Translator_Role::CAPABILITY;
			  add_menu_page( $menu['page_title'],
			                 $menu['menu_title'],
			                 $menu['capability'],
			                 $menu['menu_slug'],
			                 $menu['function'],
			                 $menu['icon_url'] );
		  }
	  }
  }

	/**
	 * Renders the TM queue
	 *
	 * @used-by \WPML_Translation_Management::menu
	 */
	function translation_queue_page() {
		if ( true !== apply_filters( 'wpml_tm_lock_ui', false ) && $this->is_the_main_request() ) {
			$this->tm_queue->display();
		}
	}

	/**
	 * @param string $menu_id
	 */
	public function settings_menu( $menu_id ) {
		if ( 'WPML' !== $menu_id ) {
			return;
		}
		$menu_label = __( 'Settings', 'wpml-translation-management' );

		$is_translation_manager = current_user_can( WPML_Manage_Translations_Role::CAPABILITY );

		$menu               = array();
		$menu['order']      = 9900; // see WPML_Main_Admin_Menu::MENU_ORDER_SETTINGS
		$menu['page_title'] = $menu_label;
		$menu['menu_title'] = $menu_label;
		$menu['capability'] = $is_translation_manager ? WPML_Manage_Translations_Role::CAPABILITY : 'wpml_manage_translation_management';
		$menu['menu_slug']  = WPML_TM_FOLDER . self::PAGE_SLUG_SETTINGS;
		$menu['function']   = array( $this, 'settings_page' );

		do_action( 'wpml_admin_menu_register_item', $menu );
	}

	public function settings_page() {
		$settings_page = new WPML_TM_Menus_Settings();
		$settings_page->init();
		$settings_page->display_main();
	}

	private function is_the_main_request() {
		return ! isset( $_SERVER['HTTP_ACCEPT'] ) || false !== strpos( $_SERVER['HTTP_ACCEPT'], 'text/html' );
	}

    function dismiss_icl_side_by_site(){
        global $iclTranslationManagement;
        $iclTranslationManagement->settings['doc_translation_method'] = ICL_TM_TMETHOD_MANUAL;
        $iclTranslationManagement->save_settings();
        exit;
    }

    function plugin_action_links($links, $file){
        $this_plugin = basename(WPML_TM_PATH) . '/plugin.php';
        if($file == $this_plugin) {
            $links[] = '<a href="admin.php?page='.basename(WPML_TM_PATH) . '/menu/main.php">' .
                __('Configure', 'wpml-translation-management') . '</a>';
        }
        return $links;
    }

    // Localization
    function plugin_localization(){
        load_plugin_textdomain( 'wpml-translation-management', false, WPML_TM_FOLDER . '/locale');
    }

	function _icl_tm_toggle_promo() {
		global $sitepress;
		$value = filter_input( INPUT_POST, 'value', FILTER_VALIDATE_INT );

		$iclsettings['dashboard']['hide_icl_promo'] = (int) $value;
		$sitepress->save_settings( $iclsettings );
		exit;
	}

	/**
	 * @return array
	 */
	public function get_active_services() {
		$cache_key = 'active_services';
		$cache_group = '';

		$found = false;
		$result = wp_cache_get($cache_key, $cache_group, false, $found);
		if($found) return $result;

		$active_services = array( 'local' => array() );
		$current_service = TranslationProxy::get_current_service();

		if ( !is_wp_error( $current_service ) ) {
			if ( $current_service ) {
				$active_services[ $current_service->name ] = $current_service;
			}

			wp_cache_set( $cache_key, $active_services, $cache_group );
		}
		return $active_services;
	}

	public function automatic_service_selection_action() {
		$this->automatic_service_selection();
	}

	/**
	 * Handles the display of notices in the TM translators tab
	 */
	public function handle_notices_action() {
		if ( $this->sitepress->get_wp_api()->is_back_end() && $this->sitepress->get_wp_api()->is_tm_page() ) {
			$lang_status = $this->wpml_tp_translator->get_icl_translator_status();
			if ( $lang_status ) {
				$this->sitepress->save_settings( $lang_status );
			}

			$this->service_authentication_notice();
		}
	}

	public function basket_extra_fields_refresh() {
		echo TranslationProxy_Basket::get_basket_extra_fields_inputs();
		die();
	}

	/**
	 * If user display Translation Dashboard or Translators
	 *
	 * @return boolean
	 */
	function automatic_service_selection_pages() {
		return is_admin() &&
					 isset($_GET['page']) &&
					 $_GET['page'] == WPML_TM_FOLDER . '/menu/main.php' &&
					 ( !isset($_GET['sm']) || $_GET['sm'] == 'translation-services' || $_GET['sm'] == 'dashboard' );
	}

	public function add_com_log_link( ) {
		WPML_TranslationProxy_Com_Log::add_com_log_link( );
	}

	public function service_activation_incomplete() {
		return $this->has_active_service() && ($this->service_requires_authentication() || $this->service_requires_translators());
	}

	private function has_active_service() {
		return TranslationProxy::get_current_service() !== false;
	}

	private function service_requires_translators() {
		$result                  = false;
		$service_has_translators = TranslationProxy::translator_selection_available();
		if ( $service_has_translators ) {
			$result = !$this->service_has_accepted_translators();
		}

		return $result;
	}

	private function service_requires_authentication() {
		$result = false;
		$service_has_translators = TranslationProxy::translator_selection_available();
		if ( !$service_has_translators ) {
			$has_custom_fields       = TranslationProxy::has_custom_fields();
			$custom_fields_data      = TranslationProxy::get_custom_fields_data();
			$result = $has_custom_fields && !$custom_fields_data;
		}

		return $result;
	}

	private function service_has_accepted_translators() {
		$result   = false;
		$icl_data = $this->wpml_tp_translator->get_icl_translator_status();
		if ( isset( $icl_data[ 'icl_lang_status' ] ) && is_array( $icl_data[ 'icl_lang_status' ] ) ) {
			foreach ( $icl_data[ 'icl_lang_status' ] as $translator ) {
				if ( isset( $translator[ 'contract_id' ] ) && $translator[ 'contract_id' ] != 0 ) {
					$result = true;
					break;
				}
			}
		}

		return $result;
	}

	private function service_authentication_notice() {
		$message_id = 'current_service_authentication_required';
		if ( current_user_can( WPML_Manage_Translations_Role::CAPABILITY ) && $this->service_activation_incomplete() ) {
			$current_service_name = TranslationProxy::get_current_service_name();

			if( ! $this->is_translation_services_tab() ) {
				$service_tab_name    = esc_html__( 'Translation Services Tab', 'wpml-translation-management' );
				$translator_tab_name = esc_html__( 'Translators Tab', 'wpml-translation-management' );

				$link_pattern    = '<strong><a href="%1$s">%2$s</a></strong>';
				$service_pattern = '<strong>%1$s</strong>';

				$services_base_url           = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php' );
				$translation_services_url    = add_query_arg( array( 'sm' => 'translation-services' ), $services_base_url );
				$translators_url             = add_query_arg( array( 'sm' => 'translators' ), $services_base_url );
				$translation_services_link   = sprintf( $link_pattern, $translation_services_url, $service_tab_name );
				$translators_link            = sprintf( $link_pattern, $translators_url, $translator_tab_name );
				$service_authentication_link = sprintf( $service_pattern,
					esc_html__( 'Authenticate', 'wpml-translation-management' ) );
				$service_deactivation_link   = sprintf( $service_pattern,
					esc_html__( 'Deactivate', 'wpml-translation-management' ) );


				if ( TranslationProxy::get_tp_default_suid() ) {
					$notification_message = __( 'You are using a translation service which requires authentication.', 'wpml-translation-management' );
					$notification_message .= '<ul>';
					$notification_message .= '<li>';
					$notification_message .= sprintf( __( 'Please go to %1$s and use the %2$s button.', 'wpml-translation-management' ), $translation_services_link, $service_authentication_link );
					$notification_message .= '</li>';
					$notification_message .= '</ul>';
				} else {

					$problem_detected = false;
					if ( $this->service_requires_authentication() ) {
						$notification_message = __( 'You have selected a translation service which requires authentication.', 'wpml-translation-management' );
					} elseif ( $this->service_requires_translators() ) {
						$notification_message      = __( 'You have selected a translation service which requires translators.', 'wpml-translation-management' );
						$service_authentication_link = '<strong>' . __( 'Add Translator', 'wpml-translation-management' ) . ' &raquo;</strong>';
					} else {
						$problem_detected       = true;
						$notification_message = __( 'There is a problem with your translation service.', 'wpml-translation-management' );
					}

					$notification_message .= '<ul>';
					$notification_message .= '<li>';

					if ( $this->service_requires_authentication() ) {
						$notification_message .= sprintf( __( 'If you wish to use %1$s, please go to %2$s and use the %3$s button.', 'wpml-translation-management' ), '<strong>'
																																																																										. $current_service_name
																																																																										. '</strong>', $translation_services_link, $service_authentication_link );
					} elseif ( $this->service_requires_translators() ) {
						$notification_message .= sprintf( __( 'If you wish to use %1$s, please go to %2$s and use the %3$s button.', 'wpml-translation-management' ), '<strong>'
																																																																										. $current_service_name
																																																																										. '</strong>', $translators_link, $service_authentication_link );
					} elseif ( $problem_detected ) {
						$notification_message .= sprintf( __( 'Please contact your administrator.', 'wpml-translation-management' ), $translation_services_link, $service_authentication_link );
					}

					$notification_message .= '</li>';

					$notification_message .= '<li>';
					$notification_message .= sprintf( __( 'If you wish to use only local translators, please go to %1$s and use the %2$s button.', 'wpml-translation-management' ), $translation_services_link, $service_deactivation_link );
					$notification_message .= '</li>';
					$notification_message .= '</ul>';
				}
			}
		}

		if(isset($notification_message)) {
			$args = array(
				'id'            => $message_id,
				'group'         => 'current_service_authentication',
				'msg'           => $notification_message,
				'type'          => 'error',
				'admin_notice'  => true,
				'hide'          => false,
				'limit_to_page' => array( WPML_TM_FOLDER . '/menu/main.php' ),
			);
			ICL_AdminNotifier::add_message( $args );
		} else {
			ICL_AdminNotifier::remove_message( $message_id );
		}

	}

	private function is_tm_page($tab = null) {
		$result = is_admin()
		       && isset( $_GET[ 'page' ] )
		       && $_GET[ 'page' ] == WPML_TM_FOLDER . '/menu/main.php';

		if($tab) {
			$result = $result && isset($_GET['sm']) && $_GET['sm'] == $tab;
		}

		return $result;
	}

	private function automatic_service_selection() {
		if ( defined( 'DOING_AJAX' ) || !$this->automatic_service_selection_pages() ) {
			return;
		}

		$done = wp_cache_get('done', 'automatic_service_selection');

		ICL_AdminNotifier::remove_message( 'automatic_service_selection' );
		$tp_default_suid = TranslationProxy::get_tp_default_suid();
		if ( ! $done && $tp_default_suid ) {
			$selected_service = TranslationProxy::get_current_service();

			if ( isset( $selected_service->suid ) && $selected_service->suid == $tp_default_suid ) {
				return;
			}

			try {
				$service_by_suid = TranslationProxy_Service::get_service_by_suid( $tp_default_suid );
			} catch ( Exception $ex ) {
				$service_by_suid = false;
			}
			if ( isset( $service_by_suid->id ) ) {
				$selected_service_id = isset( $selected_service->id ) ? $selected_service->id : false;
				if ( ! $selected_service_id || $selected_service_id != $service_by_suid->id ) {
					if ( $selected_service_id ) {
						TranslationProxy::deselect_active_service();
					}
					$result = TranslationProxy::select_service( $service_by_suid->id );
					if ( is_wp_error( $result ) ) {
						$error_data        = $result->get_error_data();
						$error_data_string = false;
						foreach ( $error_data as $key => $error_data_message ) {
							$error_data_string .= $result->get_error_message() . '<br/>';
							$error_data_string .= $key . ': <pre>' . print_r( $error_data_message, true ) . '</pre>';
							$error_data_string .= $result->get_error_message() . $error_data_string;
						}
					}
				}
			} else {
				$error_data_string = __( "WPML can't find the translation service. Please contact WPML Support or your translation service provider.", 'wpml-translation-management' );
			}
		}
		if (isset($error_data_string)) {
			$automatic_service_selection_args = array(
					'id'           => 'automatic_service_selection',
					'group'        => 'automatic_service_selection',
					'msg'          => $error_data_string,
					'type'         => 'error',
					'admin_notice' => true,
					'hide'         => false,
			);
			ICL_AdminNotifier::add_message( $automatic_service_selection_args );
		}

		wp_cache_set('done', true, 'automatic_service_selection');
	}

	/**
	 * @param $custom_field_name
	 * @param $translation_option
	 */
	public function wpml_save_custom_field_translation_option( $custom_field_name, $translation_option ) {
	    $available_options = array(
	    	WPML_IGNORE_CUSTOM_FIELD,
		    WPML_COPY_CUSTOM_FIELD,
		    WPML_COPY_ONCE_CUSTOM_FIELD,
		    WPML_TRANSLATE_CUSTOM_FIELD
	    );
		$translation_option = absint( $translation_option );
		if ( ! in_array( $translation_option, $available_options ) ) {
			$translation_option = WPML_IGNORE_CUSTOM_FIELD;
        }
		$custom_field_name = sanitize_text_field( $custom_field_name );
		$tm_settings = $this->sitepress->get_setting( 'translation-management', array() );
		$tm_settings['custom_fields_translation'][ $custom_field_name ] = $translation_option;
		$this->sitepress->set_setting( 'translation-management', $tm_settings, true );
    }

	private function add_custom_xml_config() {
	  if ( class_exists( 'WPML_Custom_XML' ) ) {
		  $factory = new WPML_TM_Custom_XML_Factory();
		  $hooks   = new WPML_TM_Custom_XML_UI_Hooks( $factory->create_ui(), $factory->create_resources( $this->sitepress->get_wp_api() ), $factory->create_ajax() );
		  $hooks->init();
	  }
	}
}
