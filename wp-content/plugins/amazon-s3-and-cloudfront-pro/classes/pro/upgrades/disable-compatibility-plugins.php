<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Upgrades;

use Amazon_S3_And_CloudFront;
use DeliciousBrains\WP_Offload_Media\Upgrades\Network_Upgrade;

class Disable_Compatibility_Plugins extends Network_Upgrade {

	/**
	 * Network_Upgrade constructor.
	 *
	 * @param Amazon_S3_And_CloudFront $as3cf
	 * @param string                   $version
	 */
	public function __construct( $as3cf, $version ) {
		parent::__construct( $as3cf, $version );

		add_action( 'admin_init', array( $this, 'disable_obsolete_plugins' ) );
		add_action( 'as3cf_pre_settings_render', array( $this, 'show_obsolete_notice' ) );
	}

	/**
	 * Perform upgrade logic.
	 */
	protected function do_upgrade() {
		$this->remove_existing_notice();
	}

	/**
	 * Remove existing compatibility notice.
	 */
	protected function remove_existing_notice() {
		$notice_id = 'as3cf-compat-addons';

		if ( $this->as3cf->notices->find_notice_by_id( $notice_id ) ) {
			$this->as3cf->notices->undismiss_notice_for_all( $notice_id );
			$this->as3cf->notices->remove_notice_by_id( $notice_id );
		}

		delete_site_option( 'as3cf_compat_addons_to_install' );
	}

	/**
	 * Show deactivation notice.
	 */
	protected function show_deactivation_notice() {
		$active_plugins = $this->get_active_plugins();

		if ( empty( $active_plugins ) ) {
			return;
		}

		$id      = 'disable-compat-plugins';
		$plugins = $this->render_plugins( $active_plugins );
		$args    = array(
			'type'              => 'notice-info',
			'custom_id'         => $id,
			'only_show_to_user' => false,
			'flash'             => false,
			'auto_p'            => false,
		);

		if ( $this->as3cf->notices->find_notice_by_id( $id ) ) {
			$this->as3cf->notices->undismiss_notice_for_all( $id );
			$this->as3cf->notices->remove_notice_by_id( $id );
		}

		$this->as3cf->notices->add_notice( $this->render_notice( $plugins ), $args );
	}

	/**
	 * Render notice.
	 *
	 * @param string $plugins
	 *
	 * @return string
	 */
	protected function render_notice( $plugins ) {
		$title     = __( 'WP Offload Media Addons Deactivated', 'amazon-s3-and-cloudfront' );
		$message   = __( "We've deactivated the following WP Offload Media addons:", 'amazon-s3-and-cloudfront' );
		$more_info = __( 'Integrations are now included in the core WP Offload Media plugin. The addon plugins listed above can safely be removed.', 'amazon-s3-and-cloudfront' );

		return '<p><strong>' . $title . '</strong> &mdash; ' . $message . '</p>' . $plugins . '<p>' . $more_info . '</p>';
	}

	/**
	 * Render plugins list.
	 *
	 * @param array $plugins
	 * @param bool  $uninstall
	 *
	 * @return string
	 */
	protected function render_plugins( $plugins, $uninstall = false ) {
		$html = '<ul style="list-style-type: disc; padding: 0 0 0 30px; margin: 5px 0;">';

		foreach ( $plugins as $plugin => $name ) {
			$html .= '<li style="margin: 0;">' . $name;

			if ( $uninstall ) {
				$html .= ' (<a  href="' . wp_nonce_url( 'plugins.php?action=delete-selected&amp;checked[]=' . $plugin, 'bulk-plugins' ) . '">';
				$html .= __( 'Remove', 'Remove plugin', 'amazon-s3-and-cloudfront' );
				$html .= '</a>)';
			}

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Get compatibility plugins.
	 *
	 * @return array
	 */
	protected function get_plugins() {
		$plugins = array(
			'amazon-s3-and-cloudfront-acf-image-crop/amazon-s3-and-cloudfront-acf-image-crop.php'             => __( 'ACF Image Crop', 'amazon-s3-and-cloudfront' ),
			'amazon-s3-and-cloudfront-edd/amazon-s3-and-cloudfront-edd.php'                                   => __( 'Easy Digital Downloads', 'amazon-s3-and-cloudfront' ),
			'amazon-s3-and-cloudfront-enable-media-replace/amazon-s3-and-cloudfront-enable-media-replace.php' => __( 'Enable Media Replace', 'amazon-s3-and-cloudfront' ),
			'amazon-s3-and-cloudfront-meta-slider/amazon-s3-and-cloudfront-meta-slider.php'                   => __( 'Meta Slider', 'amazon-s3-and-cloudfront' ),
			'amazon-s3-and-cloudfront-woocommerce/amazon-s3-and-cloudfront-woocommerce.php'                   => __( 'WooCommerce', 'amazon-s3-and-cloudfront' ),
			'amazon-s3-and-cloudfront-wpml/amazon-s3-and-cloudfront-wpml.php'                                 => __( 'WPML', 'amazon-s3-and-cloudfront' ),
		);

		return $plugins;
	}

	/**
	 * Get active plugins.
	 *
	 * @return array
	 */
	protected function get_active_plugins() {
		static $active_plugins;

		if ( is_null( $active_plugins ) ) {
			$active_plugins = array();

			foreach ( $this->get_plugins() as $plugin => $name ) {
				if ( is_plugin_active( $plugin ) ) {
					$active_plugins[ $plugin ] = $name;
				}
			}
		}

		return $active_plugins;
	}

	/**
	 * Get installed plugins.
	 *
	 * @return array
	 */
	protected function get_installed_plugins() {
		$plugins           = get_plugins();
		$installed_plugins = array();

		foreach ( $this->get_plugins() as $plugin => $name ) {
			if ( array_key_exists( $plugin, $plugins ) ) {
				$installed_plugins[ $plugin ] = $name;
			}
		}

		return $installed_plugins;
	}

	/**
	 * Show obsolete notice.
	 */
	public function show_obsolete_notice() {
		$installed_plugins = $this->get_installed_plugins();

		if ( empty( $installed_plugins ) ) {
			return;
		}

		$plugins = $this->render_plugins( $installed_plugins, true );

		$this->as3cf->render_view( 'upgrades/obsolete-addons-notice', compact( 'plugins' ) );
	}

	/**
	 * Disable obsolete plugins.
	 */
	public function disable_obsolete_plugins() {
		$this->show_deactivation_notice();

		$plugins = $this->get_active_plugins();

		if ( ! empty( $plugins ) ) {
			deactivate_plugins( array_keys( $plugins ), true );
		}
	}
}