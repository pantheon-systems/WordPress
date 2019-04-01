<?php
namespace AFFWP\Integrations\Opt_In;

use AffWP\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a opt-in platform registry class.
 *
 * @since 2.2
 *
 * @see \AffWP\Utils\Registry
 */
class Platform_Registry extends Utils\Registry {

	/**
	 * Initializes the platform registry.
	 *
	 * @access public
	 * @since  2.2
	 */
	public function init() {

		$this->register_core_platforms();

		/**
		 * Fires during instantiation of the opt-in platform registry.
		 *
		 * @since 2.2
		 *
		 * @param \AffWP\Utils\Registry $this Registry instance.
		 */
		do_action( 'affwp_opt_in_platforms_init', $this );
	}

	/**
	 * Registers core opt-in platforms.
	 *
	 * @access protected
	 * @since  2.2
	 */
	protected function register_core_platforms() {

		$this->register_platform( 'mailchimp', array(
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/opt-in-platforms/class-opt-in-platform-mailchimp.php',
			'class' => '\AFFWP\Integrations\Opt_In\MailChimp',
			'label' => 'MailChimp',
		) );

		$this->register_platform( 'activecampaign', array(
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/opt-in-platforms/class-opt-in-platform-activecampaign.php',
			'class' => '\AFFWP\Integrations\Opt_In\ActiveCampaign',
			'label' => 'ActiveCampaign',
		) );

		$this->register_platform( 'convertkit', array(
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/opt-in-platforms/class-opt-in-platform-convertkit.php',
			'class' => '\AFFWP\Integrations\Opt_In\ConvertKit',
			'label' => 'ConvertKit',
		) );

	}

	/**
	 * Registers a new opt-in platform.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $platform_id Unique opt-in platform ID.
	 * @param array  $args {
	 *     Arguments for registering a new opt-in platform.
	 *
	 *     @platform string $class The class for the opt-in platform.
	 *     @platform string $file The file for the opt-in platform.
	 *     @platform string $label The label for the opt-in platform.
	 * }
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 */
	public function register_platform( $platform_id, $args ) {
		$args = wp_parse_args( $args,  array_fill_keys( array( 'class', 'file', 'label' ), '' ) );

		if ( empty( $args['class'] ) ) {
			return new \WP_Error( 'invalid_class', __( 'A platform class must be specified.', 'affiliate-wp' ) );
		}

		if ( empty( $args['file'] ) ) {
			return new \WP_Error( 'invalid_file', __( 'A platform file must be specified.', 'affiliate-wp' ) );
		}

		if ( empty( $args['label'] ) ) {
			return new \WP_Error( 'invalid_label', __( 'A platform label must be specified.', 'affiliate-wp' ) );
		}

		return $this->add_item( $platform_id, $args );
	}

	/**
	 * Removes a opt-in platform from the registry by ID.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $platform_id opt-in platform ID.
	 */
	public function remove_platform( $platform_id ) {
		$this->remove_item( $platform_id );
	}

	/**
	 * Retrieves a platform and its associated attributes.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $platform_id platform ID.
	 * @return array|false Array of attributes for the platform if registered, otherwise false.
	 */
	public function get_platform( $platform_id ) {
		return $this->get( $platform_id );
	}

	/**
	 * Retrieves registered opt-in platforms.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @return array The list of registered opt-in platforms.
	 */
	public function get_platforms() {
		return $this->get_items();
	}

}