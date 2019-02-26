<?php

use DeliciousBrains\WP_Offload_Media\Pro\Integration_Manager;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Advanced_Custom_Fields;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Divi;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Easy_Digital_Downloads;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Enable_Media_Replace;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Meta_Slider;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Woocommerce;
use DeliciousBrains\WP_Offload_Media\Pro\Integrations\Wpml;
use DeliciousBrains\WP_Offload_Media\Pro\Sidebar_Presenter;
use DeliciousBrains\WP_Offload_Media\Pro\Tools\Copy_Buckets;
use DeliciousBrains\WP_Offload_Media\Pro\Tools\Download_And_Remover;
use DeliciousBrains\WP_Offload_Media\Pro\Tools\Downloader;
use DeliciousBrains\WP_Offload_Media\Pro\Tools\Remove_Local_Files;
use DeliciousBrains\WP_Offload_Media\Pro\Tools\Uploader;
use DeliciousBrains\WP_Offload_Media\Pro\Upgrades\Disable_Compatibility_Plugins;
use DeliciousBrains\WP_Offload_Media\Providers\Provider;

class Amazon_S3_And_CloudFront_Pro extends Amazon_S3_And_CloudFront {

	/**
	 * @var array
	 */
	protected $messages;

	/**
	 * @var AS3CF_Pro_Licences_Updates
	 */
	protected $licence;

	/**
	 * @var Integration_Manager
	 */
	protected $integrations;

	/**
	 * @var Sidebar_Presenter
	 */
	protected $sidebar;

	/**
	 * @var array
	 */
	private $_is_pro_plugin_setup;

	/**
	 * @var array
	 */
	private $_user_can_use_media_actions;

	/**
	 * @param string $plugin_file_path
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_file_path ) {
		$this->integrations = Integration_Manager::get_instance();
		$this->sidebar      = Sidebar_Presenter::get_instance( $this );

		parent::__construct( $plugin_file_path, $this->plugin_slug );
	}

	/**
	 * Plugin initialization
	 *
	 * @param string $plugin_file_path
	 */
	public function init( $plugin_file_path ) {
		parent::init( $plugin_file_path );

		// Licence and updates handler
		if ( is_admin() ) {
			$this->licence = new AS3CF_Pro_Licences_Updates( $this );
		}

		// add our custom CSS classes to <body>
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		// load assets
		add_action( 'as3cf_plugin_load', array( $this, 'load_assets' ) );

		// Only enable the plugin if compatible,
		// so we don't disable the license and updates functionality when disabled
		if ( self::is_compatible() ) {
			$this->enable_plugin();
			$this->enable_integrations();
		}
	}

	/**
	 * Enable the complete plugin when compatible
	 */
	public function enable_plugin() {
		add_action( 'load-upload.php', array( $this, 'load_media_pro_assets' ), 11 );
		add_filter( 'as3cf_settings_tabs', array( $this, 'settings_tabs' ) );
		add_action( 'as3cf_after_settings', array( $this, 'settings_page' ) );

		// Pro customisations
		add_filter( 'as3cf_lost_files_notice', array( $this, 'lost_files_notice' ) );
		add_action( 'as3cf_load_attachment_assets', array( $this, 'load_attachment_js' ) );
		add_filter( 'as3cf_media_action_strings', array( $this, 'media_action_strings' ) );

		// Media row actions
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'enrich_attachment_model' ), 10, 2 );
		add_filter( 'bulk_actions-upload', array( $this, 'add_list_table_bulk_actions' ) );
		add_filter( 'media_row_actions', array( $this, 'add_media_row_actions' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'maybe_display_media_action_message' ) );
		add_action( 'admin_init', array( $this, 'process_media_actions' ) );

		// Ajax handlers
		add_action( 'wp_ajax_as3cfpro_process_media_action', array( $this, 'ajax_process_media_action' ) );
		add_action( 'wp_ajax_as3cfpro_update_acl', array( $this, 'ajax_update_acl' ) );

		// Settings link on the plugins page
		add_filter( 'plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );

		// Diagnostic info
		add_filter( 'as3cf_diagnostic_info', array( $this, 'diagnostic_info' ) );

		// Include compatibility code for other plugins
		$this->plugin_compat = new AS3CF_Pro_Plugin_Compatibility( $this );

		// Register tools
		$this->sidebar->register_tool( new Uploader( $this ) );
		$this->sidebar->register_tool( new Downloader( $this ) );
		$this->sidebar->register_tool( new Download_And_Remover( $this ) );
		$this->sidebar->register_tool( new Remove_Local_Files( $this ), 'background' );
		$this->sidebar->register_tool( new Copy_Buckets( $this ), 'background' );

		// Perform network upgrades
		new Disable_Compatibility_Plugins( $this, $this->plugin_version );
	}

	/**
	 * Enable integrations.
	 */
	protected function enable_integrations() {
		$integrations = apply_filters( 'as3cf_integrations', array(
			'acf'  => new Advanced_Custom_Fields( $this ),
			'divi' => new Divi( $this ),
			'edd'  => new Easy_Digital_Downloads( $this ),
			'emr'  => new Enable_Media_Replace( $this ),
			'msl'  => new Meta_Slider( $this ),
			'woo'  => new Woocommerce( $this ),
			'wpml' => new Wpml( $this ),
		) );

		foreach ( $integrations as $integration ) {
			$this->integrations->register_integration( $integration );
		}
	}

	/**
	 * Is this plugin compatible with its required plugin?
	 *
	 * @return bool
	 */
	public static function is_compatible() {
		global $as3cf_compat_check;

		return $as3cf_compat_check->is_compatible();
	}

	/**
	 * Load the scripts and styles required for the plugin
	 */
	public function load_assets() {
		$this->enqueue_style( 'as3cf-pro-styles', 'assets/css/pro/styles', array( 'as3cf-styles' ) );
		$this->enqueue_script( 'as3cf-pro-script', 'assets/js/pro/script', array( 'jquery', 'underscore' ) );

		wp_localize_script( 'as3cf-pro-script', 'as3cfpro', array(
			'settings' => apply_filters( 'as3cfpro_js_settings', array() ),
			'strings'  => apply_filters( 'as3cfpro_js_strings', array() ),
			'nonces'   => apply_filters( 'as3cfpro_js_nonces', array() ),
		) );

		do_action( 'as3cfpro_load_assets' );
	}

	/**
	 * Load the media Pro assets
	 */
	public function load_media_pro_assets() {
		if ( ! $this->is_plugin_setup() ) {
			return;
		}

		$this->enqueue_script( 'as3cf-pro-media-script', 'assets/js/pro/media', array(
			'jquery',
			'media-views',
			'media-grid',
			'wp-util',
		), false );

		$nonces = array(
			'get_attachment_provider_details' => wp_create_nonce( 'get-attachment-s3-details' ),
		);

		foreach ( $this->get_available_media_actions() as $action => $scopes ) {
			foreach ( $scopes as $scope ) {
				$nonces["{$scope}_{$action}"] = wp_create_nonce( "{$scope}-{$action}" );
			}
		}

		wp_localize_script( 'as3cf-pro-media-script', 'as3cfpro_media', array(
			'strings'  => $this->get_media_action_strings(),
			'actions'  => array(
				'bulk'     => $this->get_available_media_actions( 'bulk' ),
				'singular' => $this->get_available_media_actions( 'singular' ),
			),
			'nonces'   => $nonces,
			'settings' => array(
				'default_acl' => $this->get_provider()->get_default_acl(),
				'private_acl' => $this->get_provider()->get_private_acl(),
			),
		) );
	}

	/**
	 * Load the attachment JS only when editing an attachment.
	 */
	public function load_attachment_js() {
		$this->enqueue_script( 'as3cf-pro-attachment-script', 'assets/js/pro/attachment', array(
			'jquery',
			'wp-util',
		), false );

		$actions = $this->get_available_media_actions( 'singular' );
		$nonces  = array();

		foreach ( $actions as $action ) {
			$nonces["singular_{$action}"] = wp_create_nonce( "singular-{$action}" );
		}

		wp_localize_script( 'as3cf-pro-attachment-script', 'as3cfpro_media', array(
			'strings'  => array(
				'local_warning'    => $this->get_media_action_strings( 'local_warning' ),
				'updating_acl'     => $this->get_media_action_strings( 'updating_acl' ),
				'change_acl_error' => $this->get_media_action_strings( 'change_acl_error' ),
			),
			'actions'  => $actions,
			'nonces'   => $nonces,
			'settings' => array(
				'post_id'     => get_the_ID(),
				'default_acl' => $this->get_provider()->get_default_acl(),
				'private_acl' => $this->get_provider()->get_private_acl(),
			),
		) );
	}

	/**
	 * Add Pro media action strings.
	 *
	 * @param array $strings
	 *
	 * @return array
	 */
	public function media_action_strings( $strings ) {
		$strings['copy']               = __( 'Copy to Bucket', 'amazon-s3-and-cloudfront' );
		$strings['remove']             = __( 'Remove from Bucket', 'amazon-s3-and-cloudfront' );
		$strings['download']           = __( 'Copy to Server from Bucket', 'amazon-s3-and-cloudfront' );
		$strings['local_warning']      = __( 'This file does not exist locally so removing it from the bucket will result in broken links on your site. Are you sure you want to continue?', 'amazon-s3-and-cloudfront' );
		$strings['bulk_local_warning'] = __( 'Some files do not exist locally so removing them from the bucket will result in broken links on your site. Are you sure you want to continue?', 'amazon-s3-and-cloudfront' );
		$strings['change_to_private']  = __( 'Click to set as Private in the bucket', 'amazon-s3-and-cloudfront' );
		$strings['change_to_public']   = __( 'Click to set as Public in the bucket', 'amazon-s3-and-cloudfront' );
		$strings['updating_acl']       = __( 'Updating…', 'amazon-s3-and-cloudfront' );
		$strings['change_acl_error']   = __( 'There was an error changing the ACL. Make sure the IAM user has permission to change the ACL and try again.', 'amazon-s3-and-cloudfront' );

		return $strings;
	}

	/**
	 * Get ACL value string.
	 *
	 * @param array $acl
	 * @param int   $post_id
	 *
	 * @return string
	 */
	protected function get_acl_value_string( $acl, $post_id ) {
		if ( ! in_array( 'update_acl', $this->get_available_media_actions( 'singular' ) ) || ! $this->is_attachment_served_by_provider( $post_id, true ) ) {
			return parent::get_acl_value_string( $acl, $post_id );
		}

		return sprintf( '<a id="as3cfpro-toggle-acl" title="%s" data-currentACL="%s" href="#">%s</a>', $acl['title'], $acl['acl'], $acl['name'] );
	}

	/**
	 * Add custom classes to the HTML body tag
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	function admin_body_class( $classes ) {
		if ( ! $classes ) {
			$classes = array();
		} else {
			$classes = explode( ' ', $classes );
		}

		$classes[] = 'as3cf-pro';

		// Recommended way to target WP 3.8+
		// http://make.wordpress.org/ui/2013/11/19/targeting-the-new-dashboard-design-in-a-post-mp6-world/
		if ( version_compare( $GLOBALS['wp_version'], '3.8-alpha', '>' ) ) {
			if ( ! in_array( 'mp6', $classes ) ) {
				$classes[] = 'mp6';
			}
		}

		return implode( ' ', $classes );
	}

	/**
	 * Add bulk action explanation to lost files notice
	 *
	 * @param string $notice
	 *
	 * @return string
	 */
	function lost_files_notice( $notice ) {
		return $notice . ' ' . __( 'Alternatively, use the Media Library bulk action <strong>Copy to Server from Bucket</strong> to ensure the local files exist.', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Add the Pro tabs to the UI.
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function settings_tabs( $tabs ) {
		$new_tabs = array();

		foreach ( $tabs as $slug => $tab ) {
			$new_tabs[ $slug ] = $tab;

			if ( 'addons' === $slug ) {
				$new_tabs['licence'] = _x( 'License', 'Show the License tab', 'amazon-s3-and-cloudfront' );
			}
		}

		return $new_tabs;
	}

	/**
	 * Display the settings page content.
	 */
	public function settings_page() {
		$this->render_view( 'licence' );
	}

	/**
	 * Render a view template file specific to child class
	 * or use parent view as a fallback
	 *
	 * @param string $view View filename without the extension
	 * @param array  $args Arguments to pass to the view
	 */
	function render_view( $view, $args = array() ) {
		extract( $args );
		$view_file = $this->plugin_dir_path . '/view/pro/' . $view . '.php';
		if ( file_exists( $view_file ) ) {
			include $view_file;
		} else {
			include $this->plugin_dir_path . '/view/' . $view . '.php';
		}
	}

	/**
	 * Get all the blogs of the site (only one if single site)
	 * Returning    - table prefix
	 *              - last_attachment: flag to record if we have processed all attachments for the blog
	 *              - processed: record last post id process to be used as an offset in the next batch for the blog
	 *
	 * @return array
	 */
	function get_blogs_data() {
		global $wpdb;

		$blogs = array();

		$blogs[1] = array(
			'prefix' => $wpdb->prefix,
		);

		if ( is_multisite() ) {
			$blog_ids = $this->get_blog_ids();

			foreach ( $blog_ids as $blog_id ) {
				$blogs[ $blog_id ] = array(
					'prefix' => $wpdb->get_blog_prefix( $blog_id ),
				);
			}
		}

		return $blogs;
	}

	/**
	 * Get all attachments uploaded to S3
	 *
	 * @param string $prefix Table prefix for multisite support
	 * @param bool   $count
	 * @param bool   $limit
	 * @param int    $offset
	 *
	 * @return mixed
	 */
	function get_all_provider_attachments( $prefix, $count = false, $limit = false, $offset = 0 ) {
		global $wpdb;

		$sql = " FROM `{$prefix}postmeta`
		        WHERE `meta_key` = 'amazonS3_info'";

		if ( $count ) {
			$sql    = 'SELECT COUNT(*)' . $sql;
			$result = $wpdb->get_var( $sql );

			return ( ! is_null( $result ) ) ? $result : 0;
		}

		$sql = 'SELECT *' . $sql;

		if ( false !== $limit ) {
			$sql .= ' LIMIT %d OFFSET %d';
			$sql = $wpdb->prepare( $sql, $limit, $offset );
		}

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Handle S3 actions applied to attachments via the Backbone JS
	 * in the media grid and edit attachment modal
	 */
	function ajax_process_media_action() {
		if ( ! isset( $_POST['s3_action'] ) && ! isset( $_POST['ids'] ) ) {
			return;
		}

		$scope  = filter_input( INPUT_POST, 'scope' );
		$action = filter_input( INPUT_POST, 's3_action' );

		check_ajax_referer( "{$scope}-{$action}" );

		$ids = array_map( 'intval', $_POST['ids'] ); // input var okay

		// process the S3 action for the attachments
		$return = $this->maybe_do_provider_action( $action, $ids, true );

		$message_html = '';

		if ( $return ) {
			$message_html = $this->get_media_action_result_message( $action, $return['count'], $return['errors'] );
		}

		wp_send_json_success( $message_html );
	}

	/*
	 * Handle updating the ACL for an attachment
	 */
	function ajax_update_acl() {
		check_ajax_referer( 'singular-update_acl' );

		$id    = $this->filter_input( 'id', INPUT_POST, FILTER_VALIDATE_INT ); // input var ok
		$acl   = $this->filter_input( 'acl', INPUT_POST, FILTER_SANITIZE_STRING ); // input var ok
		$title = $this->get_media_action_strings( 'change_to_public' );

		if ( empty( $id ) || empty( $acl ) ) {
			wp_send_json_error();
		}

		if ( $this->get_provider()->get_private_acl() !== $acl ) {
			$acl   = $this->get_provider()->get_default_acl();
			$title = $this->get_media_action_strings( 'change_to_private' );
		}

		// Update in S3.
		$provider_object = $this->get_attachment_provider_info( $id );
		$update          = $this->set_attachment_acl_on_provider( $id, $provider_object, $acl );

		$data = array(
			'acl'         => $acl,
			'acl_display' => $this->get_acl_display_name( $acl ),
			'title'       => $title,
			'url'         => wp_get_attachment_url( $id ),
		);

		if ( is_wp_error( $update ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $data );
	}

	/**
	 * Calculate batch limit based on the amount of registered image sizes
	 *
	 * @param int         $max
	 * @param string|null $filter_handle
	 *
	 * @return float
	 */
	function get_batch_limit( $max, $filter_handle = null ) {
		if ( ! is_null( $filter_handle ) ) {
			$max = apply_filters( $filter_handle, $max );
		}

		$sizes = count( get_intermediate_image_sizes() );

		return floor( $max / $sizes );
	}

	/**
	 * Get the S3 attachment url, based on the provided URL settings.
	 *
	 * @param array $attachment
	 * @param array $args
	 *
	 * @return string
	 */
	function get_custom_attachment_url( $attachment, $args ) {
		$scheme  = $this->get_url_scheme( $args['force-https'] );
		$expires = null;

		// Force use of secured url when ACL has been set to private
		if ( isset( $attachment['acl'] ) && $this->get_provider()->get_private_acl() === $attachment['acl'] ) {
			$expires = self::DEFAULT_EXPIRES;
		}

		$domain = $this->get_provider()->get_url_domain( $attachment['bucket'], $attachment['region'], $expires, $args );

		return $scheme . '://' . $domain . '/' . $attachment['key'];
	}

	/**
	 * Enrich the attachment model attributes used in JS
	 *
	 * @param array      $response   Array of prepared attachment data.
	 * @param int|object $attachment Attachment ID or object.
	 *
	 * @return array
	 */
	function enrich_attachment_model( $response, $attachment ) {
		$file = get_attached_file( $attachment->ID, true );

		// flag if the attachment file doesn't exist locally
		// so we can ask for confirmation when removing from S3
		$response['bulk_local_warning'] = ! file_exists( $file );

		return $response;
	}

	/**
	 * Check we can do the media actions
	 *
	 * @return bool
	 */
	function verify_media_actions() {
		if ( ! $this->is_pro_plugin_setup( true ) ) {
			return false;
		}

		return $this->user_can_use_media_actions();
	}

	/**
	 * Get a list of available media actions which can be performed according to plugin and user capability requirements.
	 *
	 * @param string|null $scope
	 *
	 * @return array
	 */
	public function get_available_media_actions( $scope = null ) {
		$actions = array();

		if ( ! $this->is_plugin_setup( true ) || ! $this->user_can_use_media_actions() ) {
			return $actions;
		}

		if ( $this->is_pro_plugin_setup( true ) ) {
			$actions['copy']       = array( 'singular', 'bulk' );
			$actions['download']   = array( 'singular', 'bulk' );
			$actions['update_acl'] = array( 'singular' );
		}

		$actions['remove'] = array( 'singular', 'bulk' );

		if ( $scope ) {
			$in_scope = array_filter( $actions, function ( $scopes ) use ( $scope ) {
				return in_array( $scope, $scopes );
			} );

			return array_keys( $in_scope );
		}

		return $actions;
	}

	/**
	 * Check if the given user can use on-demand S3 media actions.
	 *
	 * @param null|int|WP_User $user User to check. Defaults to current user.
	 *
	 * @return bool
	 */
	public function user_can_use_media_actions( $user = null ) {
		$user = $user ? $user : wp_get_current_user();

		if ( is_object( $user ) ) {
			$user = $user->ID;
		}

		if ( ! is_null( $this->_user_can_use_media_actions ) && isset( $this->_user_can_use_media_actions[ $user ] ) ) {
			return $this->_user_can_use_media_actions[ $user ];
		}

		$this->_user_can_use_media_actions[ $user ] = false;

		if ( user_can( $user, 'use_as3cf_media_actions' ) ) {
			$this->_user_can_use_media_actions[ $user ] = true;
		} else {
			/**
			 * The default capability for using on-demand S3 media actions.
			 *
			 * @param string $capability Registered capability identifier
			 */
			$capability                                 = apply_filters( 'as3cfpro_media_actions_capability', 'manage_options' );
			$this->_user_can_use_media_actions[ $user ] = user_can( $user, $capability );
		}

		return $this->_user_can_use_media_actions[ $user ];
	}

	/**
	 * Add bulk media actions to a list table's bulk actions dropdown.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function add_list_table_bulk_actions( $actions ) {
		$strings = $this->get_media_action_strings();

		foreach ( $this->get_available_media_actions( 'bulk' ) as $action ) {
			$actions["bulk_as3cfpro_{$action}"] = $strings[ $action ];
		}

		return $actions;
	}

	/**
	 * Conditionally adds copy, remove and download S3 action links for an
	 * attachment on the Media library list view
	 *
	 * @param array       $actions
	 * @param WP_Post|int $post
	 *
	 * @return array
	 */
	function add_media_row_actions( $actions = array(), $post ) {
		$available_actions = $this->get_available_media_actions( 'singular' );

		if ( ! $available_actions ) {
			return $actions;
		}

		$post_id     = ( is_object( $post ) ) ? $post->ID : $post;
		$file        = get_attached_file( $post_id, true );
		$file_exists = file_exists( $file );

		// If offloaded to another provider can not do anything.
		if ( $this->get_attachment_provider_info( $post_id ) && ! $this->is_attachment_served_by_provider( $post_id, true ) ) {
			$actions['as3cfpro_wrong_provider'] = '<span title="' . __( 'Offloaded to a different provider than currently configured.', 'amazon-s3-and-cloudfront' ) . '">' . __( 'Wrong Provider', 'amazon-s3-and-cloudfront' ) . '</span>';

			return $actions;
		}

		// If not offloaded at all, or offloaded to current provider, can use copy.
		if ( in_array( 'copy', $available_actions ) && $file_exists && ( ! $this->get_attachment_provider_info( $post_id ) || $this->is_attachment_served_by_provider( $post_id, true ) ) ) {
			$this->add_media_row_action( $actions, $post_id, 'copy' );
		}

		// Actions beyond this point are for items on S3 only
		if ( ! $this->is_attachment_served_by_provider( $post_id, true ) ) {
			return $actions;
		}

		if ( in_array( 'remove', $available_actions ) ) {
			$this->add_media_row_action( $actions, $post_id, 'remove' );
		}

		if ( in_array( 'download', $available_actions ) && ! $file_exists ) {
			$this->add_media_row_action( $actions, $post_id, 'download' );
		}

		return $actions;
	}

	/**
	 * Add an action link to the media actions array
	 *
	 * @param array  $actions
	 * @param int    $post_id
	 * @param string $action
	 * @param string $text
	 * @param bool   $show_warning
	 */
	function add_media_row_action( &$actions, $post_id, $action, $text = '', $show_warning = false ) {
		$url   = $this->get_media_action_url( $action, $post_id );
		$text  = $text ?: $this->get_media_action_strings( $action );
		$class = $action;
		if ( $show_warning ) {
			$class .= ' local-warning';
		}

		$actions[ 'as3cfpro_' . $action ] = '<a href="' . $url . '" class="' . $class . '" title="' . esc_attr( $text ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * Generate the URL for performing S3 media actions
	 *
	 * @param string      $action
	 * @param int         $post_id
	 * @param null|string $sendback_path
	 *
	 * @return string
	 */
	function get_media_action_url( $action, $post_id, $sendback_path = null ) {
		$args = array(
			'action' => $action,
			'ids'    => $post_id,
		);

		if ( ! is_null( $sendback_path ) ) {
			$args['sendback'] = urlencode( admin_url( $sendback_path ) );
		}

		$url = add_query_arg( $args, admin_url( 'upload.php' ) );
		$url = wp_nonce_url( $url, 'as3cfpro-' . $action );

		return esc_url( $url );
	}

	/**
	 * Handler for single and bulk media actions
	 */
	function process_media_actions() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		global $pagenow;
		if ( 'upload.php' != $pagenow ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) ) { // input var okay
			return;
		}

		if ( ! empty( $_REQUEST['action2'] ) && '-1' != $_REQUEST['action2'] ) {
			// Handle bulk actions from the footer bulk action select
			$action = sanitize_key( $_REQUEST['action2'] ); // input var okay
		} else {
			$action = sanitize_key( $_REQUEST['action'] ); // input var okay
		}

		if ( false === strpos( $action, 'bulk_as3cfpro_' ) ) {
			$available_actions = $this->get_available_media_actions( 'singular' );
			$referrer          = 'as3cfpro-' . $action;
			$doing_bulk_action = false;
			if ( ! isset( $_GET['ids'] ) ) {
				return;
			}
			$ids = explode( ',', $_GET['ids'] ); // input var okay
		} else {
			$available_actions = $this->get_available_media_actions( 'bulk' );
			$action            = str_replace( 'bulk_as3cfpro_', '', $action );
			$referrer          = 'bulk-media';
			$doing_bulk_action = true;
			if ( ! isset( $_REQUEST['media'] ) ) {
				return;
			}
			$ids = $_REQUEST['media']; // input var okay
		}

		if ( ! in_array( $action, $available_actions ) ) {
			return;
		}

		$ids      = array_map( 'intval', $ids );
		$id_count = count( $ids );

		check_admin_referer( $referrer );

		$sendback = isset( $_GET['sendback'] ) ? $_GET['sendback'] : admin_url( 'upload.php' );

		$args = array(
			'as3cfpro-action' => $action,
		);

		$result = $this->maybe_do_provider_action( $action, $ids, $doing_bulk_action );

		if ( ! $result ) {
			unset( $args['as3cfpro-action'] );
			$result = array();
		}

		// If we're uploading a single file, add the id to the `$args` array.
		if ( 'copy' === $action && 1 === $id_count && ! empty( $result ) && 1 === ( $result['count'] + $result['errors'] ) ) {
			$args['as3cf_id'] = array_shift( $ids );
		}

		$args = array_merge( $args, $result );
		$url  = add_query_arg( $args, $sendback );

		wp_redirect( esc_url_raw( $url ) );
		$this->_exit();
	}

	/**
	 * Wrapper for S3 actions
	 *
	 * @param       $action              type of S3 action, copy, remove, download
	 * @param array $ids                 attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment
	 *
	 * @return bool|array on success array with success count and error count
	 * @throws Exception
	 */
	function maybe_do_provider_action( $action, $ids, $doing_bulk_action ) {
		switch ( $action ) {
			case 'copy':
				$result = $this->maybe_upload_attachments( $ids, $doing_bulk_action );
				break;
			case 'remove':
				$result = $this->maybe_delete_attachments_from_provider( $ids, $doing_bulk_action );
				break;
			case 'download':
				$result = $this->maybe_download_attachments_from_provider( $ids, $doing_bulk_action );
				break;
			default:
				// not one of our actions, remove
				$result = false;
				break;
		}

		return $result;
	}

	/**
	 * Display notices after processing media actions
	 */
	function maybe_display_media_action_message() {
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'upload.php', 'post.php' ) ) ) {
			return;
		}

		if ( isset( $_GET['as3cfpro-action'] ) && isset( $_GET['errors'] ) && isset( $_GET['count'] ) ) {
			$action = sanitize_key( $_GET['as3cfpro-action'] ); // input var okay

			$error_count = absint( $_GET['errors'] ); // input var okay
			$count       = absint( $_GET['count'] ); // input var okay

			$message_html = $this->get_media_action_result_message( $action, $count, $error_count );

			if ( false !== $message_html ) {
				echo $message_html;
			}
		}
	}

	/**
	 * Get the result message after an S3 action has been performed
	 *
	 * @param string $action      type of S3 action
	 * @param int    $count       count of successful processes
	 * @param int    $error_count count of errors
	 *
	 * @return bool|string
	 */
	function get_media_action_result_message( $action, $count = 0, $error_count = 0 ) {
		$class = 'updated';
		$type  = 'success';

		if ( 0 === $count && 0 === $error_count ) {
			// don't show any message if no attachments processed
			// i.e. they haven't met the checks for bulk actions
			return false;
		}

		if ( $error_count > 0 ) {
			$type = $class = 'error';

			// We have processed some successfully.
			if ( $count > 0 ) {
				$type = 'partial';
			}
		}

		$message = $this->get_message( $action, $type );

		// can't find a relevant message, abort
		if ( ! $message ) {
			return false;
		}

		$id = $this->filter_input( 'as3cf_id', INPUT_GET, FILTER_VALIDATE_INT );

		// If we're uploading a single item, add an edit link.
		if ( 1 === ( $count + $error_count ) && ! empty( $id ) ) {
			$url = esc_url( get_edit_post_link( $id ) );

			// Only add the link if we have a URL.
			if ( ! empty( $url ) ) {
				$text    = esc_html__( 'Edit attachment', 'amazon-s3-and-cloudfront' );
				$message .= sprintf( ' <a href="%1$s">%2$s</a>', $url, $text );
			}
		}

		$message = sprintf( '<div class="notice as3cf-notice %s is-dismissible"><p>%s</p></div>', $class, $message );

		return $message;
	}

	/**
	 * Retrieve all the media action related notice messages
	 *
	 * @return array
	 */
	function get_messages() {
		if ( is_null( $this->messages ) ) {
			$this->messages = array(
				'copy'     => array(
					'success' => __( 'Media successfully copied to bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media copied to bucket with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when copying the media to bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'remove'   => array(
					'success' => __( 'Media successfully removed from bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media removed from bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when removing the media from bucket.', 'amazon-s3-and-cloudfront' ),
				),
				'download' => array(
					'success' => __( 'Media successfully downloaded from bucket.', 'amazon-s3-and-cloudfront' ),
					'partial' => __( 'Media downloaded from bucket, with some errors.', 'amazon-s3-and-cloudfront' ),
					'error'   => __( 'There were errors when downloading the media from bucket.', 'amazon-s3-and-cloudfront' ),
				),
			);
		}

		return $this->messages;
	}

	/**
	 * Get a specific media action notice message
	 *
	 * @param string $action type of action, e.g. copy, remove, download
	 * @param string $type   if the action has resulted in success, error, partial (errors)
	 *
	 * @return string|bool
	 */
	function get_message( $action = 'copy', $type = 'success' ) {
		$messages = $this->get_messages();
		if ( isset( $messages[ $action ][ $type ] ) ) {
			return $messages[ $action ][ $type ];
		}

		return false;
	}

	/**
	 * Wrapper for uploading multiple attachments to S3
	 *
	 * @param array $post_ids            attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure the
	 *                                   file exists locally before uploading to S3
	 *
	 * @return array|WP_Error
	 * @throws Exception
	 */
	function maybe_upload_attachments( $post_ids, $doing_bulk_action = false ) {
		$error_count    = 0;
		$uploaded_count = 0;

		foreach ( $post_ids as $post_id ) {
			if ( $doing_bulk_action ) {
				// if bulk action check the file exists
				$file = get_attached_file( $post_id, true );
				// if the file doesn't exist locally we can't copy
				if ( ! file_exists( $file ) ) {
					continue;
				}
			}

			// Upload the attachment to S3
			$result = $this->upload_attachment( $post_id, null, null, $doing_bulk_action );

			if ( is_wp_error( $result ) ) {
				$error_count++;
				continue;
			}

			$uploaded_count++;
		}

		$result = array(
			'errors' => $error_count,
			'count'  => $uploaded_count,
		);

		return $result;
	}

	/**
	 * Wrapper for removing multiple attachments from S3
	 *
	 * @param array $post_ids            attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to S3 before trying to delete it
	 *
	 * @return array
	 * @throws Exception
	 */
	function maybe_delete_attachments_from_provider( $post_ids, $doing_bulk_action = false ) {
		$error_count   = 0;
		$deleted_count = 0;

		foreach ( $post_ids as $post_id ) {
			// if bulk action check has been uploaded to S3
			if ( $doing_bulk_action && ! $this->get_attachment_provider_info( $post_id ) ) {
				// Confirm that item already deleted.
				$deleted_count++;
				continue;
			}

			// Download any missing local files before removing from S3
			$downloaded = $this->download_attachment_from_provider( $post_id, $doing_bulk_action );

			if ( is_wp_error( $downloaded ) ) {
				$error_count++;
				continue;
			}

			// Delete attachment from S3
			$this->delete_attachment( $post_id, $doing_bulk_action );
			if ( $this->get_attachment_provider_info( $post_id ) ) {
				$error_count++;
				continue;
			}

			$deleted_count++;
		}

		$result = array(
			'errors' => $error_count,
			'count'  => $deleted_count,
		);

		return $result;
	}

	/**
	 * Wrapper for downloading multiple attachments from S3
	 *
	 * @param array $post_ids            attachment IDs
	 * @param bool  $doing_bulk_action   flag for multiple attachments, if true then we need to
	 *                                   perform a check for each attachment to make sure it has
	 *                                   been uploaded to S3 and does not exist locally before
	 *                                   trying to download it
	 *
	 * @return array
	 * @throws Exception
	 */
	function maybe_download_attachments_from_provider( $post_ids, $doing_bulk_action = false ) {
		$error_count    = 0;
		$download_count = 0;

		foreach ( $post_ids as $post_id ) {
			$file                = get_attached_file( $post_id, true );
			$file_exists_locally = false;

			if ( $doing_bulk_action ) {
				// if bulk action check has been uploaded to S3
				if ( ! $this->get_attachment_provider_info( $post_id ) ) {
					continue;
				}
				$file_exists_locally = file_exists( $file );
			}

			if ( ! $file_exists_locally ) {
				// Download the attachment from S3
				$this->download_attachment_from_provider( $post_id, $doing_bulk_action );
				if ( ! file_exists( $file ) ) {
					$error_count++;
					continue;
				}
			}

			$download_count++;
		}

		$result = array(
			'errors' => $error_count,
			'count'  => $download_count,
		);

		return $result;
	}

	/**
	 * Download missing attachment and associated files from S3 to local
	 *
	 * @param int  $post_id                   Attachment ID
	 * @param bool $force_new_provider_client If we are downloading in bulk, force new S3 client
	 *                                        to cope with possible different regions
	 * @param bool $skip_setup_check
	 *
	 * @return bool|WP_Error
	 * @throws Exception
	 */
	function download_attachment_from_provider( $post_id, $force_new_provider_client = false, $skip_setup_check = false ) {
		if ( ! $skip_setup_check && ! $this->is_plugin_setup( true ) ) {
			return false;
		}

		if ( ! ( $provider_object = $this->get_attachment_provider_info( $post_id ) ) ) {
			return false;
		}

		$prefix     = trailingslashit( dirname( $provider_object['key'] ) );
		$file_paths = AS3CF_Utils::get_attachment_file_paths( $post_id, false );
		$downloads  = array();

		foreach ( $file_paths as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				$file_name   = wp_basename( $file_path );
				$downloads[] = array(
					'Key'    => $prefix . $file_name,
					'SaveAs' => $file_path,
				);
			}
		}

		$errors = array();

		if ( ! empty( $downloads ) ) {
			// This test is "late" so that we don't raise the error if the local files exist anyway.
			if ( ! $this->is_attachment_served_by_provider( $post_id, true ) ) {
				$errors[] = sprintf( __( 'Attachment ID %s is offloaded to a different provider than currently configured', 'amazon-s3-and-cloudfront' ), $post_id );
			} else {
				$region = $this->get_provider_object_region( $provider_object );
				if ( is_wp_error( $region ) ) {
					$region = false;
				}

				$provider_client = $this->get_provider_client( $region, $force_new_provider_client );

				foreach ( $downloads as $download ) {
					// Save object to a file
					$download['Bucket'] = $provider_object['bucket'];

					$result = $this->download_object( $provider_client, $download );

					if ( is_wp_error( $result ) ) {
						$errors[] = $result->get_error_message();
					}
				}
			}
		}

		if ( ! empty( $errors ) ) {
			$error_msg = sprintf( __( 'There were %s errors downloading files for attachment ID %s from bucket', 'amazon-s3-and-cloudfront' ), count( $errors ), $post_id );
			AS3CF_Error::log( $error_msg, 'PRO' );

			return $this->_throw_error( 'download_attachment', $error_msg, $errors );
		}

		// Looks like we downloaded everything ok, do a little cleanup.
		$data = $this->maybe_cleanup_filesize_metadata( $post_id, wp_get_attachment_metadata( $post_id ) );

		return true;
	}

	/**
	 * Download an object from S3
	 *
	 * @param Provider $provider_client
	 * @param array    $object
	 *
	 * @return bool|WP_Error
	 */
	public function download_object( $provider_client, $object ) {
		// Make sure the local directory exists
		if ( ! is_dir( dirname( $object['SaveAs'] ) ) ) {
			wp_mkdir_p( dirname( $object['SaveAs'] ) );
		}

		try {
			$provider_client->get_object( $object );
		} catch ( Exception $e ) {
			$error_msg = 'Error downloading ' . $object['Key'] . ' from bucket: ' . $e->getMessage();
			AS3CF_Error::log( $error_msg, 'PRO' );
			// If S3 file doesn't exist, an empty local file will be created, clean it up
			@unlink( $object['SaveAs'] );

			return $this->_throw_error( 'download_object', $error_msg );
		}

		return true;
	}

	/**
	 * Get the plugin slug used as the identifier in the Plugin page table
	 *
	 * @return string
	 */
	public function get_plugin_row_slug() {
		return sanitize_title( $this->licence->plugin->name );
	}

	/**
	 * Checks whether the saved licence has expired or not.
	 * Interfaces to the $licence object instead of making it public.
	 *
	 * @param bool $skip_transient_check
	 * @param bool $skip_expired_check
	 *
	 * @return bool
	 */
	public function is_valid_licence( $skip_transient_check = false, $skip_expired_check = true ) {
		return $this->licence->is_valid_licence( $skip_transient_check, $skip_expired_check );
	}

	/**
	 * Check if the license is over the media limit.
	 *
	 * @return bool
	 */
	public function is_licence_over_media_limit() {
		return $this->licence->is_licence_over_media_limit();
	}

	/**
	 * Update the API with the total of attachments offloaded to S3 for the site
	 */
	public function update_media_library_total() {
		$this->licence->check_licence_media_limit( true );
	}

	/**
	 * Get the number of media items allowed to be uploaded for the license
	 *
	 * @return bool|int
	 */
	public function get_total_allowed_media_items_to_upload() {
		$cached_media_limit_check = get_site_transient( $this->licence->plugin->prefix . '_licence_media_check' );

		$media_limit_check = $this->licence->check_licence_media_limit( true );

		if ( ! isset( $media_limit_check['total'] ) || ! isset( $media_limit_check['limit'] ) ) {
			// Can't use latest API call

			if ( ! isset( $cached_media_limit_check['total'] ) || ! isset( $cached_media_limit_check['limit'] ) ) {
				// Cached data failed
				return false;
			}

			// Use cached data
			$media_limit_check = $cached_media_limit_check;
		}

		$total   = absint( $media_limit_check['total'] );
		$limit   = absint( $media_limit_check['limit'] );
		$allowed = $limit - $total;

		if ( 0 === $limit ) {
			// Unlimited uploads allowed
			return -1;
		}

		if ( $allowed < 0 ) {
			// Upload limit reached
			return 0;
		}

		return $allowed;
	}

	/**
	 * Render the license issue notice
	 *
	 * @param bool $dashboard
	 * @param bool $skip_transient
	 */
	public function render_licence_issue_notice( $dashboard = false, $skip_transient = false ) {
		$this->licence->licence_issue_notice( $dashboard, $skip_transient );
	}

	/**
	 * Get the addons for the plugin with license information
	 *
	 * @return array
	 */
	public function get_plugin_addons() {
		return $this->licence->addons;
	}

	/**
	 * Check to see if the plugin is setup
	 *
	 * @param bool $with_credentials Do S3 credentials need to be set up too? Defaults to false.
	 *
	 * @return bool
	 */
	public function is_pro_plugin_setup( $with_credentials = false ) {
		if ( ! is_null( $this->_is_pro_plugin_setup ) && isset( $this->_is_pro_plugin_setup[ $with_credentials ] ) ) {
			return $this->_is_pro_plugin_setup[ $with_credentials ];
		}

		if ( isset( $this->licence ) ) {
			if ( ! $this->is_valid_licence() ) {
				// Empty or invalid license.
				$this->_is_pro_plugin_setup[ $with_credentials ] = false;

				return $this->_is_pro_plugin_setup[ $with_credentials ];
			}

			if ( $this->is_licence_over_media_limit() ) {
				// License key over the media library total license limit
				$this->_is_pro_plugin_setup[ $with_credentials ] = false;

				return $this->_is_pro_plugin_setup[ $with_credentials ];
			}
		}

		$this->_is_pro_plugin_setup[ $with_credentials ] = $this->is_plugin_setup( $with_credentials );

		return $this->_is_pro_plugin_setup[ $with_credentials ];
	}

	/**
	 * Get the total media library items offloaded to S3 for the site
	 *
	 * @param bool $skip_transient Ignore transient total
	 *
	 * @return int
	 */
	function get_media_library_provider_total( $skip_transient = false ) {
		if ( $skip_transient || false === ( $library_total = get_site_transient( $this->licence->plugin->prefix . '_media_library_total' ) ) ) {
			$library_total  = 0;
			$table_prefixes = $this->get_all_blog_table_prefixes();

			foreach ( $table_prefixes as $blog_id => $table_prefix ) {
				$total         = $this->count_attachments( $table_prefix, true );
				$library_total += $total;
			}

			set_site_transient( $this->licence->plugin->prefix . '_media_library_total', $library_total, HOUR_IN_SECONDS );
		}

		return $library_total;
	}

	/**
	 * Pro specific diagnostic info
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	function diagnostic_info( $output = '' ) {
		$post_count = $this->get_diagnostic_post_count();
		$output     .= 'Posts Count: ';
		$output     .= number_format_i18n( $post_count );
		$output     .= "\r\n\r\n";

		$output      .= 'Pro Upgrade: ';
		$output      .= "\r\n";
		$output      .= 'License Status: ';
		$status      = $this->licence->is_licence_expired();
		$status_text = 'Valid';
		if ( isset( $status['errors'] ) ) {
			reset( $status['errors'] );
			$status_text = key( $status['errors'] );
		}
		$output .= ucwords( str_replace( '_', ' ', $status_text ) );
		$output .= "\r\n";
		$output .= 'License Constant: ';
		$output .= $this->licence->is_licence_constant() ? 'On' : 'Off';
		$output .= "\r\n\r\n";

		$output .= 'Host IP: ';
		$output .= gethostbyname( parse_url( admin_url(), PHP_URL_HOST ) );
		$output .= "\r\n";

		// Background processing jobs
		$output   .= 'Background Jobs: ';
		$job_keys = AS3CF_Pro_Utils::get_batch_job_keys();

		global $wpdb;
		$table        = $wpdb->options;
		$column       = 'option_name';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$value_column = 'meta_value';
		}

		foreach ( $job_keys as $key ) {
			$jobs = $wpdb->get_results( $wpdb->prepare( "
				SELECT * FROM {$table}
				WHERE {$column} LIKE %s
			", $key ) );

			if ( empty( $jobs ) ) {
				continue;
			}

			foreach ( $jobs as $job ) {
				$output .= $job->{$column};
				$output .= "\r\n";
				$output .= print_r( maybe_unserialize( $job->{$value_column} ), true );
				$output .= "\r\n";
			}
		}

		$output .= "\r\n\r\n";

		return $output;
	}

	/**
	 * Get the total of posts (in scope for find and replace) for the diagnostic log
	 *
	 * @return int
	 */
	protected function get_diagnostic_post_count() {
		if ( false === ( $post_count = get_site_transient( 'as3cf_post_count' ) ) ) {
			global $wpdb;

			$post_count     = 0;
			$table_prefixes = $this->get_all_blog_table_prefixes();

			foreach ( $table_prefixes as $blog_id => $table_prefix ) {
				$post_count += $wpdb->get_var( "SELECT COUNT(ID) FROM {$table_prefix}posts" );
			}

			set_site_transient( 'as3cf_post_count', $post_count, 2 * HOUR_IN_SECONDS );
		}

		return $post_count;
	}

	/**
	 * Callback to render tool errors.
	 *
	 * @param string $name
	 */
	protected function render_tool_errors_callback( $name ) {
		$tool = $this->sidebar->get_tool( $name );

		if ( ! $tool ) {
			return;
		}

		$this->render_view( 'tool-errors', array(
			'tool'   => $name,
			'errors' => $tool->get_errors(),
		) );
	}

}
