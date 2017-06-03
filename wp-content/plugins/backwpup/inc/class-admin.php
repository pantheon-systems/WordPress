<?php
/**
 *
 */
final class BackWPup_Admin {

	private static $instance = NULL;
	public $page_hooks = array();

	/**
	 *
	 * Set needed filters and actions and load all needed
	 *
	 * @return \BackWPup_Admin
	 */
	public function __construct() {

		//Load text domain
		BackWPup::load_text_domain();

		//Add menu pages
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_jobs' ), 2 );
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_editjob' ), 3 );
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_logs' ), 4 );
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_backups' ), 5 );
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_settings' ), 6 );
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_about' ), 20 );

		//Add Menu
		if ( is_multisite() )
			add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
		else
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		//add Plugin links
		add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );
		//add more actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_css' ) );
		//Save Form posts general
		add_action( 'admin_post_backwpup', array( $this, 'save_post_form' ) );
		//Save Form posts wizard
		add_action( 'admin_post_backwpup_wizard', array( 'BackWPup_Pro_Page_Wizard', 'save_post_form' ) );
		//Admin Footer Text replacement
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 100 );
		add_filter( 'update_footer', array( $this, 'update_footer' ), 100 );
		//User Profile fields
		add_action( 'show_user_profile', array( $this, 'user_profile_fields' ) );
		add_action( 'edit_user_profile',  array( $this, 'user_profile_fields' ) );
		add_action( 'profile_update',  array( $this, 'save_profile_update' ) );

		new BackWPup_EasyCron();
	}

	/**
	 * @static
	 * @return \BackWPup
	 */
	public static function get_instance() {

		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __clone() {}

	/**
	 * Admin init function
	 */
	public function admin_init() {

		//only add action if ajax call
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && defined( 'WP_ADMIN' ) && WP_ADMIN ) {
			//ajax calls
			add_action( 'wp_ajax_backwpup_working', array( 'BackWPup_Page_Jobs', 'ajax_working' ) );
			add_action( 'wp_ajax_backwpup_cron_text', array( 'BackWPup_Page_Editjob', 'ajax_cron_text' ) );
			//ajax or view logs
			add_action( 'wp_ajax_backwpup_view_log', array( 'BackWPup_Page_Logs', 'ajax_view_log' ) );
			//ajax calls for job types
			if ( $jobtypes = BackWPup::get_job_types() ) {
				foreach ( $jobtypes as $id => $jobtypeclass ) {
					add_action( 'wp_ajax_backwpup_jobtype_' . strtolower( $id ), array( $jobtypeclass, 'edit_ajax' ) );
				}
			}
			//ajax calls for destinations
			if ( $dests = BackWPup::get_registered_destinations() ) {
				foreach ( $dests as $id => $dest ) {
					if ( ! empty( $dest[ 'class' ] ) )
						add_action( 'wp_ajax_backwpup_dest_' . strtolower( $id ), array( BackWPup::get_destination( $id ), 'edit_ajax' ) );
				}
			}
		}

		//display about page after Update
		if ( ! defined( 'DOING_AJAX' ) && ! get_site_option( 'backwpup_about_page', FALSE ) && ! isset( $_GET['activate-multi'] ) ) {
			update_site_option( 'backwpup_about_page', TRUE );
			wp_redirect( network_admin_url( 'admin.php' ) . '?page=backwpupabout' );
			exit();
		}
	}

	/**
	 * Admin init function
	 */
	public static function admin_css() {

		//register js and css for BackWPup
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_enqueue_style( 'backwpup', BackWPup::get_plugin_data( 'URL' ) . '/assets/css/backwpup.css', array(), time(), 'screen' );
		} else {
			wp_enqueue_style( 'backwpup', BackWPup::get_plugin_data( 'URL' ) . '/assets/css/backwpup.min.css', array(), BackWPup::get_plugin_data( 'Version' ), 'screen' );
		}
	}

	/**
	 *
	 * Add Links in Plugins Menu to BackWPup
	 *
	 * @param $links
	 * @param $file
	 * @return array
	 */
	public function plugin_links( $links, $file ) {

		if ( $file == plugin_basename( BackWPup::get_plugin_data( 'MainFile' ) ) ) {
			$links[ ] = '<a href="' . esc_attr__( 'http://docs.backwpup.com', 'backwpup' ) . '">' . __( 'Documentation', 'backwpup' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Add menu entries
	 */
	public function admin_menu() {

		add_menu_page( BackWPup::get_plugin_data( 'name' ), BackWPup::get_plugin_data( 'name' ), 'backwpup', 'backwpup', array( 'BackWPup_Page_BackWPup', 'page' ), 'div' );
		$this->page_hooks[ 'backwpup' ] = add_submenu_page( 'backwpup', __( 'BackWPup Dashboard', 'backwpup' ), __( 'Dashboard', 'backwpup' ), 'backwpup', 'backwpup', array( 'BackWPup_Page_BackWPup', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpup' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpup' ], array( 'BackWPup_Page_BackWPup', 'load' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpup' ], array( 'BackWPup_Page_BackWPup', 'admin_print_scripts' ) );

		//Add pages form plugins
		$this->page_hooks = apply_filters( 'backwpup_admin_pages' ,$this->page_hooks );

	}


	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_jobs( $page_hooks ) {

		$this->page_hooks[ 'backwpupjobs' ] = add_submenu_page( 'backwpup', __( 'Jobs', 'backwpup' ), __( 'Jobs', 'backwpup' ), 'backwpup_jobs', 'backwpupjobs', array( 'BackWPup_Page_Jobs', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupjobs' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupjobs' ], array( 'BackWPup_Page_Jobs', 'load' ) );
		add_action( 'admin_print_styles-' . $this->page_hooks[ 'backwpupjobs' ], array( 'BackWPup_Page_Jobs', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpupjobs' ], array( 'BackWPup_Page_Jobs', 'admin_print_scripts' ) );

		return $page_hooks;
	}

	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_editjob( $page_hooks ) {

		$this->page_hooks[ 'backwpupeditjob' ] = add_submenu_page( 'backwpup', __( 'Add new job', 'backwpup' ), __( 'Add new job', 'backwpup' ), 'backwpup_jobs_edit', 'backwpupeditjob', array( 'BackWPup_Page_Editjob', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupeditjob' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupeditjob' ], array( 'BackWPup_Page_Editjob', 'auth' ) );
		add_action( 'admin_print_styles-' . $this->page_hooks[ 'backwpupeditjob' ], array( 'BackWPup_Page_Editjob', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpupeditjob' ], array( 'BackWPup_Page_Editjob', 'admin_print_scripts' ) );

		return $page_hooks;
	}

	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_logs( $page_hooks ) {

		$this->page_hooks[ 'backwpuplogs' ] = add_submenu_page( 'backwpup', __( 'Logs', 'backwpup' ), __( 'Logs', 'backwpup' ), 'backwpup_logs', 'backwpuplogs', array( 'BackWPup_Page_Logs', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpuplogs' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpuplogs' ], array( 'BackWPup_Page_Logs', 'load' ) );
		add_action( 'admin_print_styles-' . $this->page_hooks[ 'backwpuplogs' ], array( 'BackWPup_Page_Logs', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpuplogs' ], array( 'BackWPup_Page_Logs', 'admin_print_scripts' ) );

		return $page_hooks;
	}

	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_backups( $page_hooks ) {

		$this->page_hooks[ 'backwpupbackups' ] = add_submenu_page( 'backwpup', __( 'Backups', 'backwpup' ), __( 'Backups', 'backwpup' ), 'backwpup_backups', 'backwpupbackups', array( 'BackWPup_Page_Backups', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupbackups' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupbackups' ], array( 'BackWPup_Page_Backups', 'load' ) );
		add_action( 'admin_print_styles-' . $this->page_hooks[ 'backwpupbackups' ], array( 'BackWPup_Page_Backups', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpupbackups' ], array( 'BackWPup_Page_Backups', 'admin_print_scripts' ) );

		return $page_hooks;
	}

	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_settings( $page_hooks ) {

		$this->page_hooks[ 'backwpupsettings' ] = add_submenu_page( 'backwpup', __( 'Settings', 'backwpup' ), __( 'Settings', 'backwpup' ), 'backwpup_settings', 'backwpupsettings', array( 'BackWPup_Page_Settings', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupsettings' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpupsettings' ], array( 'BackWPup_Page_Settings', 'admin_print_scripts' ) );

		return $page_hooks;
	}

	/**
	 * @param $page_hooks
	 * @return mixed
	 */
	public function admin_page_about( $page_hooks ) {

		$this->page_hooks[ 'backwpupabout' ] = add_submenu_page( 'backwpup', __( 'About', 'backwpup' ), __( 'About', 'backwpup' ), 'backwpup', 'backwpupabout', array( 'BackWPup_Page_About', 'page' ) );
		add_action( 'load-' . $this->page_hooks[ 'backwpupabout' ], array( 'BackWPup_Admin', 'init_general' ) );
		add_action( 'admin_print_styles-' . $this->page_hooks[ 'backwpupabout' ], array( 'BackWPup_Page_About', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page_hooks[ 'backwpupabout' ], array( 'BackWPup_Page_About', 'admin_print_scripts' ) );

		return $page_hooks;
	}


	/**
	 * Load for all BackWPup pages
	 */
	public static function init_general() {

		add_thickbox();

		//register js and css for BackWPup
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_register_script( 'backwpupgeneral', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/general.js', array( 'jquery' ), time(), false );
		} else {
			wp_register_script( 'backwpupgeneral', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/general.min.js', array( 'jquery' ), BackWPup::get_plugin_data( 'Version' ), false );
		}

		//add Help
		BackWPup_Help::help();
	}


	/**
	 * Called on save form. Only POST allowed.
	 */
	public function save_post_form() {

		//Allowed Pages
		if ( ! in_array( $_POST[ 'page' ], array ( 'backwpupeditjob', 'backwpupinformation', 'backwpupsettings' ), true ) )
			wp_die( __( 'Cheating, huh?', 'backwpup' ) );

		//nonce check
		check_admin_referer( $_POST[ 'page' ] . '_page' );

		if ( ! current_user_can( 'backwpup' ) )
			wp_die( __( 'Cheating, huh?', 'backwpup' ) );

		//build query for redirect
		if ( ! isset( $_POST[ 'anchor' ] ) )
			$_POST[ 'anchor' ] = NULL;
		$query_args=array();
		if ( isset( $_POST[ 'page' ] ) )
			$query_args[ 'page' ] = $_POST[ 'page' ];
		if ( isset( $_POST[ 'tab' ] ) )
			$query_args[ 'tab' ] = $_POST[ 'tab' ];
		if ( isset( $_POST[ 'tab' ] ) && isset( $_POST[ 'nexttab' ] ) && $_POST[ 'tab' ] != $_POST[ 'nexttab' ] )
			$query_args[ 'tab' ] = $_POST[ 'nexttab' ];

		$jobid = NULL;
		if ( isset( $_POST[ 'jobid' ] ) ) {
			$jobid = (int) $_POST[ 'jobid' ];
			$query_args[ 'jobid' ] = $jobid;
		}

		//Call method to save data
		if ( $_POST[ 'page' ] == 'backwpupeditjob' )
			BackWPup_Page_Editjob::save_post_form( $_POST[ 'tab' ], $jobid );
		elseif ( $_POST[ 'page' ] == 'backwpupsettings' ) {
			BackWPup_Page_Settings::save_post_form();
		}

		//Back to topic
		wp_safe_redirect( add_query_arg( $query_args, network_admin_url( 'admin.php' ) ) . $_POST[ 'anchor' ] );
		exit;
	}

	/**
	 * Add Message (across site loadings)
	 *
	 * @param $message string Message test
	 * @param $error bool ist it a error message
	 */
	public static function message( $message, $error = FALSE ) {

		if ( empty( $message ) ) {
			return;
		}

		$saved_message = self::get_messages();

		if ( $error )
			$saved_message[ 'error' ][] = $message;
		else
			$saved_message[ 'updated' ][] = $message;

		update_site_option( 'backwpup_messages', $saved_message );
	}

	/**
	 * Get all Message that not displayed
	 *
	 * @return array
	 */
	public static function get_messages() {

		return get_site_option( 'backwpup_messages', array() );
	}

	/**
	 * Display Messages
	 *
	 * @param bool $echo
	 * @return string
	 */
	public static function display_messages( $echo = TRUE ) {

		$message_updated= '';
		$message_error	= '';
		$saved_message 	= self::get_messages();
		$message_id 	= ' id="message"';

		if( empty( $saved_message ) )
			return '';

		if ( ! empty( $saved_message[ 'updated' ] ) ) {
			foreach( $saved_message[ 'updated' ] as $msg )
				$message_updated .= '<p>' .  $msg  . '</p>';
		}
		if ( ! empty( $saved_message[ 'error' ] ) ) {
			foreach( $saved_message[ 'error' ] as $msg )
				$message_error .= '<p>' .  $msg  . '</p>';
		}

		update_site_option( 'backwpup_messages', array() );

		if ( ! empty( $message_updated ) ) {
			$message_updated = '<div' . $message_id . ' class="updated">' . $message_updated . '</div>';
			$message_id = '';
		}
		if ( ! empty( $message_error ) ) {
			$message_error = '<div' . $message_id . ' class="error">' . $message_error . '</div>';
		}

		if ( $echo )
			echo $message_updated . $message_error;

		return $message_updated . $message_error;
	}

	/**
	 * Overrides WordPress text in Footer
	 *
	 * @param $admin_footer_text string
	 * @return string
	 */
	public function admin_footer_text( $admin_footer_text ) {

		$default_text = $admin_footer_text;

		if ( isset( $_REQUEST[ 'page' ] ) && strstr( $_REQUEST[ 'page' ], 'backwpup' ) ) {
			$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde_logo" title="Inpsyde GmbH">Inpsyde GmbH</a>';
			if ( ! class_exists( 'BackWPup_Pro', FALSE ) ) {
				$admin_footer_text .= sprintf( __( '<a class="backwpup-get-pro" href="%s">Get BackWPup Pro now.</a>', 'backwpup' ), __( 'http://backwpup.com', 'backwpup' ) );
			}

			return $admin_footer_text . $default_text;
		}

		return $admin_footer_text;
	}

	/**
	 * Overrides WordPress Version in Footer
	 *
	 * @param $update_footer_text string
	 * @return string
	 */
	public function update_footer( $update_footer_text ) {

		$default_text = $update_footer_text;

		if ( isset( $_REQUEST[ 'page' ] ) && strstr( $_REQUEST[ 'page' ], 'backwpup') ) {
			$update_footer_text  = '<span class="backwpup-update-footer"><a href="' . __( 'http://backwpup.com', 'backwpup' ) . '">' . BackWPup::get_plugin_data( 'Name' ) . '</a> '. sprintf( __( 'version %s' ,'backwpup'), BackWPup::get_plugin_data( 'Version' ) ) . '</span>';

			return $update_footer_text . $default_text;
		}

		return $update_footer_text;
	}


	/**
	 *  Add filed for selecting user role in user section
	 *
	 * @param $user WP_User
	 */
	public function user_profile_fields( $user ) {
		global $wp_roles;

		if ( ! is_super_admin() && ! current_user_can( 'backwpup_admin' ) ) {
			return;
		}

		//user is admin and has BackWPup rights
		if ( $user->has_cap( 'administrator' ) && $user->has_cap( 'backwpup_settings' ) ) {
			return;
		}

		//get backwpup roles
		$backwpup_roles = array();
		foreach ( $wp_roles->roles as $role => $role_value ) {
			if ( substr( $role, 0, 8 ) != 'backwpup' ) {
				continue;
			}
			$backwpup_roles[ $role ] = $role_value;
		}

		//only if user has other than backwpup role
		if ( ! empty( $user->roles[ 0 ] ) && in_array( $user->roles[ 0 ], array_keys( $backwpup_roles ), true ) ) {
			return;
		}

		?>
		    <h3><?php echo BackWPup::get_plugin_data( 'name' ); ?></h3>
		    <table class="form-table">
		        <tr>
		            <th>
		                <label for="backwpup_role"><?php _e( 'Add BackWPup Role', 'backwpup' ); ?></label>
		            </th>
		            <td>
		                <select name="backwpup_role" id="backwpup_role" style="display:inline-block; float:none;">
							<option value=""><?php _e( '&mdash; No additional role for BackWPup &mdash;', 'backwpup' ); ?></option>
							<?php
							foreach ( $backwpup_roles as $role => $role_value ) {
								echo '<option value="'.$role.'" '. selected( $user->has_cap( $role ), TRUE, FALSE ) .'>'. $role_value[ 'name' ] . '</option>';
							}
							?>
		                </select>
		            </td>
		        </tr>
		    </table>
		<?php
	}

	/**
	 * Save for user role adding
	 *
	 * @param $user_id int
	 */
	public function save_profile_update( $user_id ) {
		global $wp_roles;

		if ( ! is_super_admin() && ! current_user_can( 'backwpup_admin' ) ) {
			return;
		}

		if ( empty( $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST[ 'backwpup_role' ] ) ) {
			return;
		}

		$backwpup_role = esc_attr( $_POST[ 'backwpup_role' ] );

		//get BackWPup roles
		$backwpup_roles = array();
		foreach ( array_keys( $wp_roles->roles ) as $role ) {
			if ( ! strstr( $role, 'backwpup_' ) ) {
				continue;
			}
			$backwpup_roles[] = $role;
		}

		//get user for adding/removing role
		$user = new WP_User( $user_id );
		//a admin needs no extra role
		if ( $user->has_cap( 'administrator' ) && $user->has_cap( 'backwpup_settings' ) ) {
			$backwpup_role = '';
		}

		//remove BackWPup role from user if it not the actual
		foreach ( $user->roles as $role ) {
			if ( ! strstr( $role, 'backwpup_' ) ) {
				continue;
			}
			if ( $role !== $backwpup_role ) {
				$user->remove_role( $role );
			} else {
				$backwpup_role = '';
			}
		}

		//add new role to user if it not the actual
		if ( $backwpup_role && in_array( $backwpup_role, $backwpup_roles, true ) ) {
			$user->add_role( $backwpup_role );
		}

		return;
	}

}
