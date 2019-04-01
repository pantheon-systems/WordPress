<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Admin {
	const settings_name_common = 'woocommerce-order-export-common';
	var $activation_notice_option = 'woocommerce-order-export-activation-notice-shown';
	var $step = 30;
	public static $formats = array( 'XLS', 'CSV', 'XML', 'JSON', 'TSV', 'PDF' );
	public static $export_types = array( 'EMAIL', 'FTP', 'HTTP', 'FOLDER', 'SFTP', 'ZAPIER' );
	public $url_plugin;
	public $path_plugin;
	var $methods_allowed_for_guests;

	public function __construct() {
		$this->url_plugin         = dirname( plugin_dir_url( __FILE__ ) ) . '/';
		$this->path_plugin        = dirname( plugin_dir_path( __FILE__ ) ) . '/';
		$this->path_views_default = dirname( plugin_dir_path( __FILE__ ) ) . "/view/";

		if ( is_admin() ) { // admin actions
			add_action( 'admin_menu', array( $this, 'add_menu' ) );

			// load scripts on our pages only
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-order-export' ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'thematic_enqueue_scripts' ) );
				add_filter( 'script_loader_src', array( $this, 'script_loader_src' ), 999, 2 );
			}
			add_action( 'wp_ajax_order_exporter', array( $this, 'ajax_gate' ) );

			//Add custom bulk export action in Woocomerce orders Table, modified for WP 4.7
			add_filter( 'bulk_actions-edit-shop_order', array( $this, 'export_orders_bulk_action' ) );
			add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'export_orders_bulk_action_process' ), 10,
				3 );
			add_action( 'admin_notices', array( $this, 'export_orders_bulk_action_notices' ) );
			//do once
			if ( ! get_option( $this->activation_notice_option ) ) {
				add_action( 'admin_notices', array( $this, 'display_plugin_activated_message' ) );
			}

			//extra links in >Plugins
			add_filter( 'plugin_action_links_' . WOE_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );

			// Add 'Export Status' orders page column header
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_status_column_header' ), 20 );

			// Add 'Export Status' orders page column content
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_status_column_content' ) );

			if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' ) {
				add_action( 'admin_print_styles', array( $this, 'add_order_status_column_style' ) );
			}
		}

		$this->settings = self::load_main_settings();

		//Pro active ?
		if ( self::is_full_version() ) {
			new WC_Order_Export_Zapier_Engine( $this->settings );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_filter( 'cron_schedules', array( 'WC_Order_Export_Cron', 'create_custom_schedules' ), 999, 1 );
			add_action( 'wc_export_cron_global', array( 'WC_Order_Export_Cron', 'wc_export_cron_global_f' ) );

			//for direct calls
			add_action( 'wp_ajax_order_exporter_run', array( $this, 'ajax_gate_guest' ) );
			add_action( 'wp_ajax_nopriv_order_exporter_run', array( $this, 'ajax_gate_guest' ) );
			$this->methods_allowed_for_guests = array( 'run_cron_jobs', 'run_one_job', 'run_one_scheduled_job' );

			// order actions
			add_action( 'woocommerce_order_status_changed', array( $this, 'wc_order_status_changed' ), 10, 3 );
			// activate CRON hook if it was removed
			add_action( 'wp_loaded', function () {
				WC_Order_Export_Cron::try_install_job();
			} );
		}

	}

	public function add_order_status_column_header( $columns ) {

		if ( ! $this->settings['show_export_status_column'] ) {
			return $columns;
		}

		$new_columns = array();
		foreach ( $columns as $column_name => $column_info ) {
			if ( 'order_actions' === $column_name OR 'wc_actions' === $column_name ) { // Woocommerce uses wc_actions since 3.3.0
				$label                            = __( 'Export Status', 'woo-order-export-lite' );
				$new_columns['woe_export_status'] = $label;
			}
			$new_columns[ $column_name ] = $column_info;
		}

		return $new_columns;
	}

	public function add_order_status_column_content( $column ) {
		global $post;

		if ( 'woe_export_status' === $column ) {
			$is_exported = false;

			if ( get_post_meta( $post->ID, 'woe_order_exported', true ) ) {
				$is_exported = true;
			}

			if ( $is_exported ) {
				echo '<span class="dashicons dashicons-yes" style="color: #2ea2cc"></span>';
			} else {
				echo '<span class="dashicons dashicons-minus"></span>';
			}
		}
	}

	function add_order_status_column_style() {
		$css = '.widefat .column-woe_export_status { width: 45px; text-align: center; }';
		wp_add_inline_style( 'woocommerce_admin_styles', $css );
	}

	public function install() {
		if ( self::is_full_version() ) {
			WC_Order_Export_Cron::try_install_job();
		}
	}

	public function display_plugin_activated_message() {
		?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Advanced Order Export For WooCommerce is available <a href="admin.php?page=wc-order-export">on this page</a>.',
					'woo-order-export-lite' ); ?></p>
        </div>
		<?php
		update_option( $this->activation_notice_option, true, false );
	}

	public function add_action_links( $links ) {
		$mylinks = array(
			'<a href="admin.php?page=wc-order-export">' . __( 'Settings', 'woo-order-export-lite' ) . '</a>',
			'<a href="https://algolplus.com/plugins/documentation-order-export-woocommerce/" target="_blank">' . __( 'Docs',
				'woo-order-export-lite' ) . '</a>',
			'<a href="https://algolplus.freshdesk.com" target="_blank">' . __( 'Support',
				'woo-order-export-lite' ) . '</a>',
		);

		return array_merge( $mylinks, $links );
	}

	public function deactivate() {
		wp_clear_scheduled_hook( "wc_export_cron_global" );
		delete_option( $this->activation_notice_option );

		if ( self::is_full_version() ) {
			//don't do it!  updater call this function!
			// WC_Order_Export_EDD::getInstance()->edd_woe_force_deactivate_license();
		}
	}

	public static function uninstall() {
		//delete_option( self::settings_name_common );
		//WC_Order_Export_Manage::remove_settings();
	}

	static function load_main_settings() {
		return array_merge(
			array(
				'default_tab'                      => 'export',
				'cron_tasks_active'                => '1',
				'show_export_status_column'        => '1',
				'show_export_actions_in_bulk'      => '1',
				'show_export_in_status_change_job' => '0',
				'autocomplete_products_max'        => '10',
				'ajax_orders_per_step'             => '30',
				'limit_button_test'                => '1',
				'cron_key'                         => '1234',
				'ipn_url'                          => '',
				'zapier_api_key'                   => '12345678',
				'zapier_file_timeout'              => 60,
			),
			get_option( self::settings_name_common, array() )
		);
	}

	static function save_main_settings() {
		// update main settings here!
		$settings = filter_input_array( INPUT_POST, array(
			'default_tab'                      => FILTER_SANITIZE_STRING,
			'cron_tasks_active'                => FILTER_VALIDATE_BOOLEAN,
			'show_export_status_column'        => FILTER_VALIDATE_BOOLEAN,
			'show_export_actions_in_bulk'      => FILTER_VALIDATE_BOOLEAN,
			'show_export_in_status_change_job' => FILTER_VALIDATE_BOOLEAN,
			'autocomplete_products_max'        => FILTER_VALIDATE_INT,
			'ajax_orders_per_step'             => FILTER_VALIDATE_INT,
			'limit_button_test'                => FILTER_SANITIZE_STRING,
			'cron_key'                         => FILTER_SANITIZE_STRING,
			'ipn_url'                          => FILTER_SANITIZE_STRING,
			'zapier_api_key'                   => FILTER_SANITIZE_STRING,
			'zapier_file_timeout'              => FILTER_SANITIZE_NUMBER_INT,
		) );
		update_option( self::settings_name_common, $settings, false );

		if ( isset( $settings['ipn_url'] ) ) {
			update_option( WOE_IPN_URL_OPTION_KEY, $settings['ipn_url'], false );
		}
	}


	function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woo-order-export-lite' );
		load_textdomain( 'woo-order-export-lite',
			WP_LANG_DIR . '/woocommerce-order-export/woocommerce-order-export-' . $locale . '.mo' );

		load_plugin_textdomain( 'woo-order-export-lite', false,
			plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/i18n/languages' );
	}

	public function add_menu() {
		if ( apply_filters( 'woe_current_user_can_export', true ) ) {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				add_submenu_page( 'woocommerce', __( 'Export Orders', 'woo-order-export-lite' ),
					__( 'Export Orders', 'woo-order-export-lite' ), 'view_woocommerce_reports', 'wc-order-export',
					array( $this, 'render_menu' ) );
			} else // add after Sales Report!
			{
				add_menu_page( __( 'Export Orders', 'woo-order-export-lite' ),
					__( 'Export Orders', 'woo-order-export-lite' ), 'view_woocommerce_reports', 'wc-order-export',
					array( $this, 'render_menu' ), null, '55.7' );
			}
		}
	}

	public function render_menu() {
		$this->render( 'main', array( 'WC_Order_Export' => $this, 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		$active_tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : $this->settings['default_tab'];
		if ( method_exists( $this, 'render_tab_' . $active_tab ) ) {
			$this->{'render_tab_' . $active_tab}();
		}
	}

	public function render_tab_export() {
		$this->render( 'tab/export', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

	public function render_tab_tools() {
		$this->render( 'tab/tools', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

	public function render_tab_settings() {
		$this->render( 'tab/settings',
			array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'settings' => $this->settings ) );
	}

	public function render_tab_license() {
		$this->render( 'tab/license', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

	public function render_tab_help() {
		$this->render( 'tab/help', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

	public function render_tab_order_actions() {
		$wc_oe     = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';

		if (in_array($wc_oe, array(
		    'copy_action',
		    'delete',
		    'change_status',
		    'change_statuses',
		)) && !check_admin_referer( 'woe_nonce', 'woe_nonce' )) {
		    return;
		}

		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$mode      = WC_Order_Export_Manage::EXPORT_ORDER_ACTION;
		$all_items = WC_Order_Export_Manage::get_export_settings_collection( $mode );
		$show      = array(
			'date_filter'         => $this->settings['show_export_in_status_change_job'],
			'export_button'       => $this->settings['show_export_in_status_change_job'],
			'export_button_plain' => $this->settings['show_export_in_status_change_job'],
			'preview_actions'     => false,
			'destinations'        => true,
			'schedule'            => false,
			'sort_orders'         => false,
			'order_filters'       => true,
			'product_filters'     => true,
			'customer_filters'    => true,
			'billing_filters'     => true,
			'shipping_filters'    => true,
		);
		switch ( $wc_oe ) {
			case 'add_action':
				end( $all_items );
				$next_id = key( $all_items ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'edit_action':
				if ( ! isset( $_REQUEST['action_id'] ) ) {
					break;
				}
				$item_id                                   = $_REQUEST['action_id'];
				WC_Order_Export_Manage::$edit_existing_job = true;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $item_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'copy_action':
				if ( ! isset( $_REQUEST['action_id'] ) ) {
					break;
				}
				$item_id = $_REQUEST['action_id'];
				$item_id = WC_Order_Export_Manage::clone_export_settings( $mode, $item_id );

				$url = add_query_arg( array(
				    'action_id' => $item_id,
				    'wc_oe'	=> 'edit_action',
				));

				$url = remove_query_arg(array('woe_nonce'), $url);

				wp_redirect( $url );

				return;
			case 'delete':
				if ( ! isset( $_REQUEST['action_id'] ) ) {
					break;
				}
				$item_id = $_REQUEST['action_id'];
				unset( $all_items[ $item_id ] );
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_items );

				$url = remove_query_arg( array( 'wc_oe', 'action_id', 'woe_nonce' ) );
				wp_redirect( $url );

				break;
			case 'change_status':
				if ( ! isset( $_REQUEST['action_id'] ) ) {
					break;
				}
				$item_id                         = $_REQUEST['action_id'];
				$all_items[ $item_id ]['active'] = $_REQUEST['status'];
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_items );
				$url = remove_query_arg( array( 'wc_oe', 'action_id', 'status', 'woe_nonce' ) );
				wp_redirect( $url );
				break;
			case 'change_statuses':
				if ( ! isset( $_REQUEST['chosen_order_actions'] ) AND ! isset( $_REQUEST['doaction'] ) AND - 1 == $_REQUEST['doaction'] ) {
					break;
				}
				$chosen_order_actions = explode( ',', $_REQUEST['chosen_order_actions'] );
				$doaction             = $_REQUEST['doaction'];

				foreach ( $chosen_order_actions as $order_action_id ) {
					if ( 'activate' == $doaction ) {
						$all_items[ $order_action_id ]['active'] = 1;
					} elseif ( 'deactivate' == $doaction ) {
						$all_items[ $order_action_id ]['active'] = 0;
					} elseif ( 'delete' == $doaction ) {
						unset( $all_items[ $order_action_id ] );
					}
				}
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_items );
				$url = remove_query_arg( array( 'wc_oe', 'chosen_order_actions', 'doaction', 'woe_nonce' ) );
				wp_redirect( $url );
				break;
		}
		$this->render( 'tab/order-actions',
			array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this, 'tab' => 'order_actions' ) );
	}

	public function render_tab_schedules() {
		$wc_oe    = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';

		if (in_array($wc_oe, array(
		    'copy_schedule',
		    'delete_schedule',
		    'change_status_schedule',
		    'change_status_schedules',
		)) && !check_admin_referer( 'woe_nonce', 'woe_nonce' )) {
		    return;
		}

		$ajaxurl  = admin_url( 'admin-ajax.php' );
		$mode     = WC_Order_Export_Manage::EXPORT_SCHEDULE;
		$all_jobs = WC_Order_Export_Manage::get_export_settings_collection( $mode );
		$show     = array(
			'date_filter'         => true,
			'export_button'       => true,
			'export_button_plain' => true,
			'destinations'        => true,
			'schedule'            => true,
		);
		switch ( $wc_oe ) {
			case 'add_schedule':
				end( $all_jobs );
				$next_id = key( $all_jobs ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'edit_schedule':
				if ( ! isset( $_REQUEST['schedule_id'] ) ) {
					break;
				}
				$schedule_id                               = $_REQUEST['schedule_id'];
				WC_Order_Export_Manage::$edit_existing_job = true;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $schedule_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'copy_schedule':
				if ( ! isset( $_REQUEST['schedule_id'] ) ) {
					break;
				}
				$schedule_id = $_REQUEST['schedule_id'];
				$schedule_id = WC_Order_Export_Manage::clone_export_settings( $mode, $schedule_id );

				$url = add_query_arg( array(
				    'schedule_id' => $schedule_id,
				    'wc_oe'	 => 'edit_schedule',
				));

				$url = remove_query_arg(array('woe_nonce'), $url);

				wp_redirect( $url );

				return;
			case 'delete_schedule':
				if ( ! isset( $_REQUEST['schedule_id'] ) ) {
					break;
				}
				$schedule_id = $_REQUEST['schedule_id'];
				unset( $all_jobs[ $schedule_id ] );
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_jobs );

				$url = remove_query_arg( array( 'wc_oe', 'schedule_id', 'woe_nonce' ) );
				wp_redirect( $url );

				break;
			case 'change_status_schedule':
				if ( ! isset( $_REQUEST['schedule_id'] ) ) {
					break;
				}
				$schedule_id                        = $_REQUEST['schedule_id'];
				$all_jobs[ $schedule_id ]['active'] = $_REQUEST['status'];
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_jobs );
				$url = remove_query_arg( array( 'wc_oe', 'schedule_id', 'status', 'woe_nonce' ) );
				wp_redirect( $url );
				break;
			case 'change_status_schedules':
				if ( ! isset( $_REQUEST['chosen_schedules'] ) AND ! isset( $_REQUEST['doaction'] ) AND - 1 == $_REQUEST['doaction'] ) {
					break;
				}
				$chosen_schedules = explode( ',', $_REQUEST['chosen_schedules'] );
				$doaction         = $_REQUEST['doaction'];

				foreach ( $chosen_schedules as $schedule_id ) {
					if ( 'activate' == $doaction ) {
						$all_jobs[ $schedule_id ]['active'] = 1;
					} elseif ( 'deactivate' == $doaction ) {
						$all_jobs[ $schedule_id ]['active'] = 0;
					} elseif ( 'delete' == $doaction ) {
						unset( $all_jobs[ $schedule_id ] );
					}
				}
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_jobs );
				$url = remove_query_arg( array( 'wc_oe', 'chosen_schedules', 'doaction', 'woe_nonce' ) );
				wp_redirect( $url );
				break;
		}
		$this->render( 'tab/schedules', array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this ) );
	}

	public function render_tab_profiles() {
		$wc_oe     = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';

		if (in_array($wc_oe, array(
		    'copy_profile',
		    'copy_profile_to_scheduled',
		    'copy_profile_to_actions',
		    'delete_profile',
		    'change_profile_statuses',
		)) && !check_admin_referer( 'woe_nonce', 'woe_nonce' )) {
		    return;
		}

		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$mode      = WC_Order_Export_Manage::EXPORT_PROFILE;
		$all_items = WC_Order_Export_Manage::get_export_settings_collection( $mode );
		$show      = array(
			'date_filter'         => true,
			'export_button'       => true,
			'export_button_plain' => true,
			'destinations'        => true,
			'schedule'            => false,
		);
		switch ( $wc_oe ) {
			case 'add_profile':
				end( $all_items );
				$next_id = key( $all_items ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'edit_profile':
				if ( ! isset( $_REQUEST['profile_id'] ) ) {
					break;
				}
				$profile_id                                = $_REQUEST['profile_id'];
				WC_Order_Export_Manage::$edit_existing_job = true;
				$this->render( 'settings-form', array(
					'mode'            => $mode,
					'id'              => $profile_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show,
				) );

				return;
			case 'copy_profile':
				if ( ! isset( $_REQUEST['profile_id'] ) ) {
					break;
				}

				$profile_id = $_REQUEST['profile_id'];
				$profile_id = WC_Order_Export_Manage::clone_export_settings( $mode, $profile_id );

				$url = add_query_arg( array(
				    'profile_id' => $profile_id,
				    'wc_oe'	 => 'edit_profile',
				));

				$url = remove_query_arg(array('woe_nonce'), $url);

				wp_redirect( $url );

				return;
			case 'copy_profile_to_scheduled':
				$profile_id  = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';
				$schedule_id = WC_Order_Export_Manage::advanced_clone_export_settings( $profile_id, $mode,
					WC_Order_Export_Manage::EXPORT_SCHEDULE );
				$url         = remove_query_arg( array('profile_id', 'woe_nonce') );
				$url         = add_query_arg( 'tab', 'schedules', $url );
				$url         = add_query_arg( 'wc_oe', 'edit_schedule', $url );
				$url         = add_query_arg( 'schedule_id', $schedule_id, $url );
				wp_redirect( $url );
				break;
			case 'copy_profile_to_actions':
				$profile_id  = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';
				$schedule_id = WC_Order_Export_Manage::advanced_clone_export_settings( $profile_id, $mode,
					WC_Order_Export_Manage::EXPORT_ORDER_ACTION );
				$url         = remove_query_arg( array('profile_id', 'woe_nonce') );
				$url         = add_query_arg( 'tab', 'order_actions', $url );
				$url         = add_query_arg( 'wc_oe', 'edit_action', $url );
				$url         = add_query_arg( 'action_id', $schedule_id, $url );
				wp_redirect( $url );
				break;
			case 'delete_profile':
				if ( ! isset( $_REQUEST['profile_id'] ) ) {
					break;
				}
				$profile_id = $_REQUEST['profile_id'];
				unset( $all_items[ $profile_id ] );
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_items );

				$url = remove_query_arg( array( 'wc_oe', 'profile_id', 'woe_nonce' ) );
				wp_redirect( $url );

				break;
			case 'change_profile_statuses':
				if ( ! isset( $_REQUEST['chosen_profiles'] ) AND ! isset( $_REQUEST['doaction'] ) AND - 1 == $_REQUEST['doaction'] ) {
					break;
				}
				$chosen_profiles = explode( ',', $_REQUEST['chosen_profiles'] );
				$doaction        = $_REQUEST['doaction'];

				foreach ( $chosen_profiles as $profile_id ) {
					if ( 'activate' == $doaction ) {
						$all_items[ $profile_id ]['use_as_bulk'] = 'on';
					} elseif ( 'deactivate' == $doaction ) {
						unset( $all_items[ $profile_id ]['use_as_bulk'] );
					} elseif ( 'delete' == $doaction ) {
						unset( $all_items[ $profile_id ] );
					}
				}
				WC_Order_Export_Manage::save_export_settings_collection( $mode, $all_items );
				$url = remove_query_arg( array( 'wc_oe', 'chosen_profiles', 'doaction', 'woe_nonce' ) );
				wp_redirect( $url );
				break;
		}

		//code to copy default settings as profile
		$profiles = WC_Order_Export_Manage::get_export_settings_collection( $mode );
		$free_job = WC_Order_Export_Manage::get_export_settings_collection( WC_Order_Export_Manage::EXPORT_NOW );
		if ( empty( $profiles ) AND ! empty( $free_job ) ) {
			$free_job['title'] = __( 'Copied from "Export now"', 'woo-order-export-lite' );
			$free_job['mode']  = $mode;
			$profiles[1]       = $free_job;
			update_option( WC_Order_Export_Manage::settings_name_profiles, $profiles, false );
		}

		$this->render( 'tab/profiles', array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this ) );
	}


	public function thematic_enqueue_scripts() {
		wp_enqueue_media();

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-touch-punch' );
		wp_enqueue_style( 'jquery-style',
			'//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
		$this->enqueue_select2_scripts();

		wp_enqueue_script( 'export', $this->url_plugin . 'assets/js/export.js', array(), WOE_VERSION );
		wp_enqueue_script( 'serializejson', $this->url_plugin . 'assets/js/jquery.serializejson.js', array( 'jquery' ),
			WOE_VERSION );
		wp_enqueue_style( 'export', $this->url_plugin . 'assets/css/export.css', array(), WOE_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array() );

		$_REQUEST['tab'] = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : $this->settings['default_tab'];
		if ( isset( $_REQUEST['wc_oe'] ) AND ( strpos( $_REQUEST['wc_oe'], 'add_' ) === 0 OR strpos( $_REQUEST['wc_oe'],
					'edit_' ) === 0 ) OR $_REQUEST['tab'] == 'export' ) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'settings-form', $this->url_plugin . 'assets/js/settings-form.js', array(),
				WOE_VERSION );

			$localize_settings_form = array(
				'add_fields_to_export'      => __( 'Add %s fields', 'woo-order-export-lite' ),
				'repeats'                   => array(
					'rows'            => __( 'rows', 'woo-order-export-lite' ),
					'columns'         => __( 'columns', 'woo-order-export-lite' ),
					'inside_one_cell' => __( 'one row', 'woo-order-export-lite' ),
				),
				'js_tpl_popup'              => array(
					'add'                      => __( 'Add', 'woo-order-export-lite' ),
					'as'                       => __( 'as', 'woo-order-export-lite' ),
					'split_values_by'          => __( 'Split values by', 'woo-order-export-lite' ),
					'fill_order_columns_label' => __( 'Fill order columns for', 'woo-order-export-lite' ),
					'for_all_rows_label'       => __( 'all rows', 'woo-order-export-lite' ),
					'for_first_row_only_label' => __( '1st row only', 'woo-order-export-lite' ),
					'grouping_by'              => array(
						'products' => __( 'Grouping by product', 'woo-order-export-lite' ),
						'coupons'  => __( 'Grouping by coupon', 'woo-order-export-lite' ),
					),
				),
				'index'                     => array(
					'product_pop_up_title' => __( 'Set up product fields', 'woo-order-export-lite' ),
					'coupon_pop_up_title'  => __( 'Set up coupon fields', 'woo-order-export-lite' ),
					'products'             => __( 'products', 'woo-order-export-lite' ),
					'coupons'              => __( 'coupons', 'woo-order-export-lite' ),
				),
				'remove_all_fields_confirm' => __( 'Remove all fields?', 'woo-order-export-lite' ),
				'reset_profile_confirm' => __( 'This action will reset filters, settings and fields to default state. Are you sure?', 'woo-order-export-lite' ),

			);
			wp_localize_script( 'settings-form', 'localize_settings_form', $localize_settings_form );


			$settings_form = array(
				'save_settings_url' => esc_url( add_query_arg(
					array(
						'page' => 'wc-order-export',
						'tab'  => $_REQUEST['tab'],
						'save' => 'y',
					),
					admin_url( 'admin.php' ) ) ),

				'EXPORT_NOW'          => WC_Order_Export_Manage::EXPORT_NOW,
				'EXPORT_PROFILE'      => WC_Order_Export_Manage::EXPORT_PROFILE,
				'EXPORT_SCHEDULE'     => WC_Order_Export_Manage::EXPORT_SCHEDULE,
				'EXPORT_ORDER_ACTION' => WC_Order_Export_Manage::EXPORT_ORDER_ACTION,

				'copy_to_profiles_url' => esc_url( add_query_arg(
					array(
						'page'  => 'wc-order-export',
						'tab'   => 'profiles',
						'wc_oe' => 'edit_profile',
					),
					admin_url( 'admin.php' ) ) ),

				'flat_formats'   => array_map('strtoupper', WC_Order_Export_Engine::get_plain_formats()),
				'object_formats' => array( 'XML', 'JSON' ),
				'xml_formats'    => array( 'XML' ),

				'day_names' => WC_Order_Export_Manage::get_days(),

			);

			wp_localize_script( 'settings-form', 'settings_form', $settings_form );

		};

		// Localize the script with new data
		$translation_array = array(
			'empty_column_name'           => __( 'empty column name', 'woo-order-export-lite' ),
			'empty_meta_key'              => __( 'empty meta key', 'woo-order-export-lite' ),
			'empty_meta_key_and_taxonomy' => __( 'select product field or item field or taxonomy',
				'woo-order-export-lite' ),
			'empty_value'                 => __( 'empty value', 'woo-order-export-lite' ),
			'empty_title'                 => __( 'title is empty', 'woo-order-export-lite' ),
			'wrong_date_range'            => __( 'Date From is greater than Date To', 'woo-order-export-lite' ),
			'no_fields'                   => __( 'Please, set up fields to export', 'woo-order-export-lite' ),
			'no_results'                  => __( 'Nothing to export. Please, adjust your filters',
				'woo-order-export-lite' ),
			'empty'                       => __( 'empty', 'woo-order-export-lite' ),
		);
		wp_localize_script( 'export', 'export_messages', $translation_array );

		$script_data = array(
			'locale'         => get_locale(),
			'select2_locale' => $this->get_select2_locale(),
		);

		wp_localize_script( 'export', 'script_data', $script_data );
	}

	private function get_select2_locale() {
		$locale          = get_locale();
		$select2_locales = array(
			'de_DE' => 'de',
			'de_CH' => 'de',
			'ru_RU' => 'ru',
			'pt_BR' => 'pt-BR',
			'pt_PT' => 'pt',
			'zh_CN' => 'zh-CN',
			'fr_FR' => 'fr',
			'es_ES' => 'es',
		);

		return isset( $select2_locales[ $locale ] ) ? $select2_locales[ $locale ] : 'en';
	}

	private function enqueue_select2_scripts() {
		wp_enqueue_script( 'select22', $this->url_plugin . 'assets/js/select2/select2.full.js',
			array( 'jquery' ), '4.0.3' );


		if ( $select2_locale = $this->get_select2_locale() ) {
			// enable by default
			if ( $select2_locale !== 'en' ) {
				wp_enqueue_script( "select22-i18n-{$select2_locale}",
					$this->url_plugin . "assets/js/select2/i18n/{$select2_locale}.js", array( 'jquery', 'select22' ) );
			}
		}

		wp_enqueue_style( 'select2-css', $this->url_plugin . 'assets/css/select2/select2.min.css',
			array(), WC_VERSION );
	}

	public function script_loader_src( $src, $handle ) {
		// don't load ANY select2.js / select2.min.js  and OUTDATED select2.full.js
		if ( ! preg_match( '/\/select2\.full\.js\?ver=[1-3]/', $src ) && ! preg_match( '/\/select2\.min\.js/',
				$src ) && ! preg_match( '/\/select2\.js/', $src )
		     && ! preg_match( '#jquery\.serialize-object\.#', $src )  /*this script breaks our json!*/
		) {
			return $src;
		}

		return "";
	}

	public function render( $view, $params = array(), $path_views = null ) {
		$params = apply_filters( 'woe_render_params', $params );
		$params = apply_filters( 'woe_render_params_' . $view, $params );

		extract( $params );
		if ( $path_views ) {
			include $path_views . "$view.php";
		} else {
			include $this->path_views_default . "$view.php";
		}
	}

	public function get_value( $arr, $name ) {
		$arr_name = explode( ']', $name );
		$arr_name = array_map( function ( $name ) {
			if ( substr( $name, 0, 1 ) == '[' ) {
				$name = substr( $name, 1 );
			}

			return trim( $name );
		}, $arr_name );
		$arr_name = array_filter( $arr_name );

		foreach ( $arr_name as $value ) {
			$arr = isset( $arr[ $value ] ) ? $arr[ $value ] : "";
		}

		return $arr;
	}

	//on status change
	public function wc_order_status_changed( $order_id, $old_status, $new_status ) {
		global $wp_filter;

		$all_items = get_option( WC_Order_Export_Manage::settings_name_actions, array() );
		if ( empty( $all_items ) ) {
			return;
		}
		$old_status = is_string( $old_status ) && strpos( $old_status, 'wc-' ) !== 0 ? "wc-{$old_status}" : $old_status;
		$new_status = is_string( $new_status ) && strpos( $new_status, 'wc-' ) !== 0 ? "wc-{$new_status}" : $new_status;

		$this->changed_order_id = $order_id;
		add_filter( 'woe_sql_get_order_ids_where', array( $this, "filter_by_changed_order" ), 10, 2 );

		$logger         = function_exists( "wc_get_logger" ) ? wc_get_logger() : false; //new logger in 3.0+
		$logger_context = array( 'source' => 'woo-order-export-lite' );

		foreach ( $all_items as $key => $item ) {
			$item = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_ORDER_ACTION, $key );
			if ( isset( $item['active'] ) && ! $item['active'] ) {
				continue;
			}
			// use empty for ANY status
			if ( ( empty( $item['from_status'] ) OR in_array( $old_status, $item['from_status'] ) )
			     AND
			     ( empty( $item['to_status'] ) OR in_array( $new_status, $item['to_status'] ) )
			) {
				$filters = $wp_filter;//remember hooks/filters
				do_action( 'woe_order_action_started', $order_id, $item );
				$result = WC_Order_Export_Engine::build_files_and_export( $item );
				$output = sprintf( __( 'Status change job #%s for order #%s. Result: %s', 'woo-order-export-lite' ),
					$key, $order_id, $result );
				// log if required
				if ( $logger AND ! empty( $item['log_results'] ) ) {
					$logger->info( $output, $logger_context );
				}

				do_action( 'woe_order_action_completed', $order_id, $item, $result );
				$wp_filter = $filters;//reset hooks/filters
			}
		}
		remove_filter( 'woe_sql_get_order_ids_where', array( $this, "filter_by_changed_order" ), 10 );
	}

	public function filter_by_changed_order( $where, $settings ) {
		$where[] = "orders.ID = " . $this->changed_order_id;

		return $where;
	}

	// AJAX part
	// calls ajax_action_XXXX
	public function ajax_gate() {
		if ( isset( $_REQUEST['method'] ) ) {
			$method = $_REQUEST['method'];
			if ( method_exists( 'WC_Order_Export_Ajax', $method ) ) {

                                if ($_POST && !check_admin_referer( 'woe_nonce', 'woe_nonce' )) {
                                    return;
                                }

				$_POST = stripslashes_deep( $_POST );
				// parse json to arrays?
				if ( ! empty( $_POST['json'] ) ) {
					$json = json_decode( $_POST['json'], true );
					if ( is_array( $json ) ) {
						// add $_POST['settings'],$_POST['orders'],$_POST['products'],$_POST['coupons']
						$_POST = $_POST + $json;
						unset( $_POST['json'] );
					}
				}
				$ajax = new WC_Order_Export_Ajax();
				$ajax->$method();
			}
		}
		die();
	}

	//TODO: debug!
	public function ajax_gate_guest() {
		if ( isset( $_REQUEST['method'] ) AND in_array( $_REQUEST['method'], $this->methods_allowed_for_guests ) ) {
			$method = $_REQUEST['method'];
			if ( method_exists( 'WC_Order_Export_Ajax', $method ) ) {
				$_POST = array_map( 'stripslashes_deep', $_POST );
				$ajax  = new WC_Order_Export_Ajax();
				$ajax->validate_url_key();
				$ajax->$method();
			}
		}
		die();
	}

	//Works since Wordpress 4.7
	function export_orders_bulk_action( $actions ) {
		$settings = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_NOW );
		WC_Order_Export_Manage::set_correct_file_ext( $settings );

		// default
		if ( ! empty( $settings['format'] ) ) {
			$actions['woe_export_selected_orders'] = sprintf( __( 'Export as %s', 'woo-order-export-lite' ),
				$settings['format'] );
		}

		// mark/unmark
		if ( $this->settings['show_export_actions_in_bulk'] ) {
			$actions['woe_mark_exported']   = __( 'Mark exported', 'woo-order-export-lite' );
			$actions['woe_unmark_exported'] = __( 'Unmark exported', 'woo-order-export-lite' );
		}

		$all_items = WC_Order_Export_Manage::get_export_settings_collection( WC_Order_Export_Manage::EXPORT_PROFILE );
		foreach ( $all_items as $job_id => $job ) {
			if ( isset( $job['use_as_bulk'] ) ) {
				$actions[ 'woe_export_selected_orders_profile_' . $job_id ] = sprintf( __( "Export as profile '%s'",
					'woo-order-export-lite' ), $job['title'] );
			}
		}

		return $actions;
	}

	function export_orders_bulk_action_process( $redirect_to, $action, $ids ) {
		switch ( $action ) {
			case 'woe_export_selected_orders':
				$redirect_to = add_query_arg( array( 'export_bulk_profile' => 'now', 'ids' => join( ',', $ids ) ),
					$redirect_to );
				break;
			case 'woe_mark_exported':
				foreach ( $ids as $post_id ) {
					update_post_meta( $post_id, 'woe_order_exported', 1 );
				}
				$redirect_to = add_query_arg( array(
					'woe_bulk_mark_exported'   => count( $ids ),
					'woe_bulk_unmark_exported' => false,
				), $redirect_to );
				break;
			case 'woe_unmark_exported':
				foreach ( $ids as $post_id ) {
					delete_post_meta( $post_id, 'woe_order_exported' );
				}
				$redirect_to = add_query_arg( array(
					'woe_bulk_mark_exported'   => false,
					'woe_bulk_unmark_exported' => count( $ids ),
				), $redirect_to );
				break;
			default:
				if ( preg_match( '/woe_export_selected_orders_profile_(\d+)/', $action, $matches ) ) {
					if ( isset( $matches[1] ) ) {
						$id          = $matches[1];
						$redirect_to = add_query_arg( array( 'export_bulk_profile' => $id, 'ids' => join( ',', $ids ) ),
							$redirect_to );
						break;
					}
				}

				//do nothing
				return $redirect_to;
		}

		wp_redirect( $redirect_to );
		exit();
	}

	function export_orders_bulk_action_notices() {

		global $post_type, $pagenow;

		if ( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset( $_REQUEST['export_bulk_profile'] ) ) {
			$url = admin_url( 'admin-ajax.php' ) . "?action=order_exporter&method=export_download_bulk_file&export_bulk_profile=" . $_REQUEST['export_bulk_profile'] . "&ids=" . $_REQUEST['ids'];
			wp_redirect( $url );
			exit();
			/* unused code
			//$message = sprintf( __( 'Orders exported. <a href="%s">Download report.</a>' ,'woo-order-export-lite'), $url );
			$message = __( 'Orders exported.','woo-order-export-lite');

			echo "<div class='updated'><p>{$message}</p></div><iframe width=0 height=0 style='display:none' src='$url'></iframe>";

			// must remove this arg from pagination url
			add_filter('removable_query_args', array($this, 'fix_table_links') );
			*/
		} else if ( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset( $_REQUEST['woe_bulk_mark_exported'] ) ) {
			$count = intval( $_REQUEST['woe_bulk_mark_exported'] );
			printf(
				'<div id="message" class="updated fade">' .
				_n( '%s order marked.', '%s orders marked.', $count, 'woo-order-export-lite' )
				. '</div>',
				$count
			);

		} else if ( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset( $_REQUEST['woe_bulk_unmark_exported'] ) ) {
			$count = intval( $_REQUEST['woe_bulk_unmark_exported'] );
			printf(
				'<div id="message" class="updated fade">' .
				_n( '%s order unmarked.', '%s orders unmarked.', $count, 'woo-order-export-lite' )
				. '</div>',
				$count
			);
		}
	}

	function fix_table_links( $args ) {
		$args[] = 'export_bulk_profile';
		$args[] = 'ids';

		return $args;
	}

	function must_run_ajax_methods() {
		// wait admin ajax!
		$script_name = ! empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'];
		if ( basename( $script_name ) != "admin-ajax.php" ) {
			return false;
		}

		// our method MUST BE called
		return isset( $_REQUEST['action'] ) AND ( $_REQUEST['action'] == "order_exporter" OR $_REQUEST['action'] == "order_exporter_run" );
	}

	public static function is_full_version() {
		return defined( 'WOE_STORE_URL' );
	}

	public static function user_can_add_custom_php() {
		return apply_filters( 'woe_user_can_add_custom_php', current_user_can( 'edit_themes' ) );
	}

}
