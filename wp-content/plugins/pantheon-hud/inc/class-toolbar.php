<?php

namespace Pantheon\HUD;

/**
 * Adds Pantheon details to the WordPress toolbar
 * Instantiation expects the user to be able to view Pantheon details
 */
class Toolbar {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	private function setup_actions() {
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_admin_bar_inline_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_bar_inline_styles' ) );
	}

	public function action_admin_bar_menu( $wp_admin_bar ) {
		$api = new API;
		$name = $api->get_site_name();
		$site_id = $api->get_site_id();
		$env = $this->get_environment();
		$title = '<img src="' . esc_url( plugins_url( 'assets/img/pantheon-fist-color.svg', PANTHEON_HUD_ROOT_FILE ) ) . '" width="32" height="32" />';
		$bits = array();
		if ( $name ) {
			$bits[] = $name;
		}
		$bits[] = $env;
		$title .= ' ' . esc_html( strtolower( implode( ':', $bits ) ) );
		$wp_admin_bar->add_node( array(
			'id'       => 'pantheon-hud',
			'href'     => false,
			'title'    => $title,
			) );

		$env_admins = '';
		# TODO: List envs from API to include Multidev.
		foreach( array( 'dev', 'test', 'live' ) as $e ) {
			$url = $api->get_primary_environment_url( $e );
			if ( $url ) {
				$env_admins .= '<a target="_blank" href="' . esc_url( rtrim( $url ) . '/wp-admin/' ) . '">' . esc_html( $e ) . '</a> | ';
			}
		}

		if ( ! empty( $env_admins ) ) {
			$wp_admin_bar->add_node( array(
				'id'     => 'pantheon-hud-wp-admin-links',
				'parent' => 'pantheon-hud',
				'href'   => false,
				'title'  => '<em>wp-admin links</em><br />' . rtrim( $env_admins, ' |' ),
				) );
		}

		$environment_details = $api->get_environment_details();
		if ( $environment_details ) {
			$details_html = array();
			if ( isset( $environment_details['web']['appserver_count'] ) ) {
				$pluralize = $environment_details['web']['appserver_count'] > 1 ? 's' : '';
				$web_detail = $environment_details['web']['appserver_count'] . ' app container' . $pluralize;
				if ( isset( $environment_details['web']['php_version'] ) ) {
					$web_detail .= ' running ' . $environment_details['web']['php_version'];
				}
				$details_html[] = $web_detail;
			}
			if ( isset( $environment_details['database']['dbserver_count'] ) ) {
				$pluralize = $environment_details['database']['dbserver_count'] > 1 ? 's' : '';
				$db_detail = $environment_details['database']['dbserver_count'] . ' db container' . $pluralize;
				if ( isset( $environment_details['database']['read_replication_enabled'] ) ) {
					$db_detail .= ' with ' . ( $environment_details['database']['read_replication_enabled'] ? 'replication enabled' : 'replication disabled' );
				}
				$details_html[] = $db_detail;
			}
			if ( ! empty( $details_html ) ) {
				$details_html = '<em>' . esc_html__( 'Environment Details', 'pantheon-hud' ) . '</em><br /> - ' . implode( '<br /> - ', $details_html );
				$wp_admin_bar->add_node( array(
					'id'     => 'pantheon-hud-environment-details',
					'parent' => 'pantheon-hud',
					'title'  => $details_html,
					) );
			}
		}

		if ( $name && $env ) {
			$wp_cli_stub = sprintf( 'terminus wp %s.%s', $name, $env );
			$wp_admin_bar->add_node( array(
				'id'     => 'pantheon-hud-wp-cli-stub',
				'parent' => 'pantheon-hud',
				'title'  => '<em>' . esc_html__( 'WP-CLI via Terminus', 'pantheon-hud' ) . '</em><br /><input value="' . esc_attr( $wp_cli_stub ) . '">',
				) );
		}

		if ( $site_id && $env ) {
			$dashboard_link = sprintf( 'https://dashboard.pantheon.io/sites/%s#%s/code', $site_id, $env );
			$wp_admin_bar->add_node( array(
				'id'     => 'pantheon-hud-dashboard-link',
				'parent' => 'pantheon-hud',
				'href'   => $dashboard_link,
				'title'  => esc_html__( 'Visit Pantheon Dashboard', 'pantheon-hud' ),
				'meta'   => array(
					'target' => '_blank',
					),
				) );
		}

	}

	/**
	 * Add admin bar inline styles.
	 */
	public function add_admin_bar_inline_styles() {
		ob_start();
		?>
		<style>
			#wpadminbar li#wp-admin-bar-pantheon-hud > .ab-item img {
				height:32px;
				width:32px;
				vertical-align:middle;
				margin-top:-4px;
			}
			#wpadminbar li#wp-admin-bar-pantheon-hud em {
				font-size: 11px;
				line-height: 13px;
				font-style: italic;
			}
			#wpadminbar li#wp-admin-bar-pantheon-hud br {
				line-height: 0;
			}
			#wpadminbar #wp-admin-bar-pantheon-hud-wp-admin-links a {
				display: inline;
				padding:0;
				height: auto;
			}
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-wp-admin-links .ab-item,
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-environment-details .ab-item,
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-wp-cli-stub .ab-item {
				height: auto;
			}
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-wp-cli-stub input {
				width: 100%;
				line-height: 15px;
				background-color: rgba( 255, 255, 255, 0.9 );
				border-width: 1px;
			}
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-dashboard-link {
				padding-left: 3px;
				padding-right: 3px;
			}
			#wpadminbar ul li#wp-admin-bar-pantheon-hud-dashboard-link a {
				border-top: 1px solid rgba(240,245,250,.4);
				padding-top: 3px;
				margin-top: 10px;
			}
		</style>
		<?php
		wp_add_inline_style( 'admin-bar', str_replace( array( '<style>', '</style>' ), '', ob_get_clean() ) );
	}

	private function get_environment() {
		return ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'local';
	}

}
