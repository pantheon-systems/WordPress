<?php
/**
 * WooCommerce Admin
 *
 * @class    WC_Admin
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Admin class.
 */
class WC_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'preload_helper' ), 9 );
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_init', array( $this, 'preview_emails' ) );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		add_action( 'admin_init', array( $this, 'admin_redirects' ) );
		add_action( 'admin_footer', 'wc_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
		add_action( 'wp_ajax_setup_wizard_check_jetpack', array( $this, 'setup_wizard_check_jetpack' ) );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/wc-admin-functions.php';
		include_once dirname( __FILE__ ) . '/wc-meta-box-functions.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-post-types.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-taxonomies.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-menus.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-customize.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-notices.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-assets.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-api-keys.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-webhooks.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-pointers.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-importers.php';
		include_once dirname( __FILE__ ) . '/class-wc-admin-exporters.php';

		// Help Tabs
		if ( apply_filters( 'woocommerce_enable_admin_help_tab', true ) ) {
			include_once dirname( __FILE__ ) . '/class-wc-admin-help.php';
		}

		// Setup/welcome
		if ( ! empty( $_GET['page'] ) ) {
			switch ( $_GET['page'] ) {
				case 'wc-setup':
					include_once dirname( __FILE__ ) . '/class-wc-admin-setup-wizard.php';
					break;
			}
		}

		// Importers
		if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
			include_once dirname( __FILE__ ) . '/class-wc-admin-importers.php';
		}

		// Helper
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-options.php';
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-api.php';
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-updater.php';
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-plugin-info.php';
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-compat.php';
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper.php';
	}

	/**
	 * Preloads some functionality of the Helper to be loaded on the `plugins_loaded` hook
	 */
	public function preload_helper() {
		include_once dirname( __FILE__ ) . '/helper/class-wc-helper-file-headers.php';
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'dashboard':
			case 'dashboard-network':
				include 'class-wc-admin-dashboard.php';
				break;
			case 'options-permalink':
				include 'class-wc-admin-permalink-settings.php';
				break;
			case 'plugins':
				include 'plugin-updates/class-wc-plugins-screen-updates.php';
				break;
			case 'update-core':
				include 'plugin-updates/class-wc-updates-screen-updates.php';
				break;
			case 'users':
			case 'user':
			case 'profile':
			case 'user-edit':
				include 'class-wc-admin-profile.php';
				break;
		}
	}

	/**
	 * Handle redirects to setup/welcome page after install and updates.
	 *
	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
	 */
	public function admin_redirects() {
		// Nonced plugin install redirects (whitelisted)
		if ( ! empty( $_GET['wc-install-plugin-redirect'] ) ) {
			$plugin_slug = wc_clean( $_GET['wc-install-plugin-redirect'] );

			if ( current_user_can( 'install_plugins' ) && in_array( $plugin_slug, array( 'woocommerce-gateway-stripe' ) ) ) {
				$nonce = wp_create_nonce( 'install-plugin_' . $plugin_slug );
				$url   = self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug . '&_wpnonce=' . $nonce );
			} else {
				$url = admin_url( 'plugin-install.php?tab=search&type=term&s=' . $plugin_slug );
			}

			wp_safe_redirect( $url );
			exit;
		}

		// Setup wizard redirect
		if ( get_transient( '_wc_activation_redirect' ) ) {
			delete_transient( '_wc_activation_redirect' );

			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'wc-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( 'manage_woocommerce' ) || apply_filters( 'woocommerce_prevent_automatic_wizard_redirect', false ) ) {
				return;
			}

			// If the user needs to install, send them to the setup wizard
			if ( WC_Admin_Notices::has_notice( 'install' ) ) {
				wp_safe_redirect( admin_url( 'index.php?page=wc-setup' ) );
				exit;
			}
		}
	}

	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin.
	 */
	public function prevent_admin_access() {
		$prevent_access = false;

		if ( 'yes' === get_option( 'woocommerce_lock_down_admin', 'yes' ) && ! is_ajax() && basename( $_SERVER['SCRIPT_FILENAME'] ) !== 'admin-post.php' ) {
			$has_cap     = false;
			$access_caps = array( 'edit_posts', 'manage_woocommerce', 'view_admin_dashboard' );

			foreach ( $access_caps as $access_cap ) {
				if ( current_user_can( $access_cap ) ) {
					$has_cap = true;
					break;
				}
			}

			if ( ! $has_cap ) {
				$prevent_access = true;
			}
		}

		if ( apply_filters( 'woocommerce_prevent_admin_access', $prevent_access ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}

	/**
	 * Preview email template.
	 */
	public function preview_emails() {

		if ( isset( $_GET['preview_woocommerce_mail'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'preview-mail' ) ) {
				die( 'Security check' );
			}

			// load the mailer class
			$mailer = WC()->mailer();

			// get the preview email subject
			$email_heading = __( 'HTML email template', 'woocommerce' );

			// get the preview email content
			ob_start();
			include 'views/html-email-template-preview.php';
			$message = ob_get_clean();

			// create a new email
			$email = new WC_Email();

			// wrap the content with the email template and then add styles
			$message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $message ) ) );

			// print the preview email
			echo $message;
			exit;
		}
	}

	/**
	 * Change the admin footer text on WooCommerce admin pages.
	 *
	 * @since  2.3
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_screen_ids' ) ) {
			return $footer_text;
		}
		$current_screen = get_current_screen();
		$wc_pages       = wc_get_screen_ids();

		// Set only WC pages.
		$wc_pages = array_diff( $wc_pages, array( 'profile', 'user-edit' ) );

		// Check to make sure we're on a WooCommerce admin page.
		if ( isset( $current_screen->id ) && apply_filters( 'woocommerce_display_admin_footer_text', in_array( $current_screen->id, $wc_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'woocommerce_admin_footer_text_rated' ) ) {
				$footer_text = sprintf(
					/* translators: 1: WooCommerce 2:: five stars */
					__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'woocommerce' ),
					sprintf( '<strong>%s</strong>', esc_html__( 'WooCommerce', 'woocommerce' ) ),
					'<a href="https://wordpress.org/support/plugin/woocommerce/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woocommerce' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
				);
				wc_enqueue_js(
					"jQuery( 'a.wc-rating-link' ).click( function() {
						jQuery.post( '" . WC()->ajax_url() . "', { action: 'woocommerce_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});"
				);
			} else {
				$footer_text = __( 'Thank you for selling with WooCommerce.', 'woocommerce' );
			}
		}

		return $footer_text;
	}

	/**
	 * Check on a Jetpack install queued by the Setup Wizard.
	 *
	 * See: WC_Admin_Setup_Wizard::install_jetpack()
	 */
	public function setup_wizard_check_jetpack() {
		$jetpack_active = class_exists( 'Jetpack' );

		wp_send_json_success(
			array(
				'is_active' => $jetpack_active ? 'yes' : 'no',
			)
		);
	}
}

return new WC_Admin();
