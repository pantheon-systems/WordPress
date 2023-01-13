<?php

/**
 * Init Admin Bar
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ocean_Admin_Bar {

	public function __construct() {
		if ( ! $this->has_access() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_action( 'admin_bar_menu', array( $this, 'register' ), 999 );
	}

	/**
	 * Check current user has access to see admin bar menu
	 */
	public function has_access() {
		$access = false;

		if (
			is_admin_bar_showing()
		) {
			$access = true;
		}

		return (bool) apply_filters( 'ocean_adminbarmenu_has_access', $access );
	}

	/**
	 * Add "Ocean" item to new-content admin bar menu item
	 */
	public function admin_bar( $wp_admin_bar ) {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		$args = array(
			'id'     => 'ocean',
			'title'  => esc_html__( 'OceanWP', 'ocean-extra' ),
			'href'   => admin_url( 'admin.php?page=oceanwp' ),
			'parent' => 'new-content',
		);

		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Register and render admin bar menu items
	 */
	public function register( WP_Admin_Bar $wp_admin_bar ) {
		$items = (array) apply_filters(
			'ocean_admin_adminbarmenu_items',
			array(
				'main_menu',
				'notification_menu',
				'panel_menu',
				'system_info_menu',
				'community_menu',
				'documentation_menu',
			),
			$wp_admin_bar
		);

		foreach ( $items as $item ) {

			$this->{$item}( $wp_admin_bar );

			do_action( "ocean_admin_adminbarmenu_init_{$item}_after", $wp_admin_bar );
		}
	}

	/**
	 * Render primary top-level admin bar menu item
	 */
	public function main_menu( WP_Admin_Bar $wp_admin_bar ) {
		$indicator     = '';
		$notifications = $this->has_notifications();

		if ( $notifications ) {
			$count     = $notifications < 10 ? $notifications : '!';
			$indicator = ' <div class="wp-core-ui wp-ui-notification ocean-menu-notification-counter">' . $count . '</div>';
		}

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'ocean-menu',
				'title' => 'OceanWP' . $indicator,
				'href'  => admin_url( 'admin.php?page=oceanwp' ),
			)
		);
	}

	public function notification_menu( WP_Admin_Bar $wp_admin_bar ) {
		if ( ! $this->has_notifications() ) {
			return;
		}

		$wp_admin_bar->add_menu(
			array(
				'parent' => 'ocean-menu',
				'id'     => 'ocean-notifications',
				'title'  => esc_html__( 'Notifications', 'ocean-extra' ) . ' <div class="wp-core-ui wp-ui-notification ocean-menu-notification-indicator"></div>',
				'href'   => admin_url( 'admin.php?page=oceanwp' ),
			)
		);
	}


	public function panel_menu( WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_menu(
			array(
				'parent' => 'ocean-menu',
				'id'     => 'ocean-customizer',
				'title'  => esc_html__( 'Panel', 'oceanwp' ),
				'href'   => admin_url( 'admin.php?page=oceanwp#customizer' ),
			)
		);
	}

	public function system_info_menu( WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_menu(
			array(
				'parent' => 'ocean-menu',
				'id'     => 'ocean-system-info',
				'title'  => esc_html__( 'System Info', 'oceanwp' ),
				'href'   => admin_url( 'admin.php?page=oceanwp#system-info' ),
			)
		);
	}

	public function community_menu( WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_menu(
			array(
				'parent' => 'ocean-menu',
				'id'     => 'ocean-community',
				'title'  => esc_html__( 'Community', 'oceanwp' ),
				'href'   => 'https://www.facebook.com/groups/oceanwptheme/',
				'meta'  => array( 'target' => '_blank' )
			)
		);
	}

	public function documentation_menu( WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_menu(
			array(
				'parent' => 'ocean-menu',
				'id'     => 'ocean-documentation',
				'title'  => esc_html__( 'Documentation', 'oceanwp' ),
				'href'   => 'https://docs.oceanwp.org/',
				'meta'  => array( 'target' => '_blank' )
			)
		);
	}

	/**
	 * Determine whether new notifications are available
	 */
	public function has_notifications() {
		return ocean_notifications()->get_count();
	}

	/**
	 * Enqueue styles
	 */
	public function enqueue_css() {
		wp_enqueue_style( 'ocean-admin-bar', plugins_url( '/assets/css/admin-bar.css', __FILE__ ), false, OCEANWP_THEME_VERSION );
	}
}

new Ocean_Admin_Bar();
