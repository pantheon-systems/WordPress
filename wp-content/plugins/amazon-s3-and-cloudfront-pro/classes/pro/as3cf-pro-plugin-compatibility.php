<?php
/**
 * Pro Plugin Compatibility
 *
 * @package     amazon-s3-and-cloudfront-pro
 * @subpackage  Classes/Plugin-Compatibility
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.8.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AS3CF_Pro_Plugin_Compatibility Class
 *
 * This class handles compatibility code for third party plugins used in conjunction with AS3CF Pro
 *
 * @since 0.8.3
 */
class AS3CF_Pro_Plugin_Compatibility extends AS3CF_Plugin_Compatibility {

	/**
	 * @var
	 */
	protected $plugin_functions_abort_upload;

	/**
	 * @var AS3CF_Pro_Plugin_Installer
	 */
	protected $plugin_installer;

	/**
	 * @param Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	function __construct( $as3cf ) {
		parent::__construct( $as3cf );

		if ( is_admin() ) {
			$this->plugin_installer = new AS3CF_Pro_Plugin_Installer( 'addons', $this->as3cf->get_plugin_slug( true ), $this->as3cf->get_plugin_file_path() );
		}
	}

	/**
	 * Register the compatibility hooks
	 */
	function compatibility_init() {
		$this->set_plugin_functions_abort_upload();

		add_filter( 'as3cf_pre_update_attachment_metadata', array( $this, 'abort_update_attachment_metadata' ), 10, 3 );
	}

	/**
	 * Set the abort upload functions property.
	 */
	function set_plugin_functions_abort_upload() {
		$functions = array(
			'gambit_otf_regen_thumbs_media_downsize', // https://wordpress.org/plugins/otf-regenerate-thumbnails/
			'ewww_image_optimizer_resize_from_meta_data', // https://wordpress.org/plugins/ewww-image-optimizer/
		);

		/**
		 * Filter the array of functions which should lead to aborting our
		 * `wp_attachment_metadata_update` filter.
		 *
		 * @param array $functions Plugins functions which should lead to aborting
		 *                         our `wp_attachment_metadata_update` filter.
		 */
		$functions = apply_filters( 'wpos3_plugin_functions_to_abort_upload', $functions ); // Backwards compatibility

		/**
		 * Filter the array of functions which should lead to aborting our
		 * `wp_attachment_metadata_update` filter.
		 *
		 * @param array $functions Plugins functions which should lead to aborting
		 *                         our `wp_attachment_metadata_update` filter.
		 */
		$functions = apply_filters( 'as3cf_plugin_functions_to_abort_upload', $functions );

		// Unset any function that doesn't exist.
		foreach ( (array) $functions as $key => $function ) {
			if ( ! function_exists( $function ) ) {
				unset( $functions[ $key ] );
			}
		}

		$this->plugin_functions_abort_upload = $functions;
	}

	/**
	 * Abort our upload to S3 on wp_attachment_metadata_update from different plugins
	 * as as we have used the stream wrapper to do any uploading to S3.
	 *
	 * @param bool  $pre
	 * @param array $data
	 * @param int   $post_id
	 *
	 * @return bool
	 */
	function abort_update_attachment_metadata( $pre, $data, $post_id ) {
		if ( empty( $this->plugin_functions_abort_upload ) ) {
			return $pre;
		}

		$callers = debug_backtrace();
		foreach ( $callers as $caller ) {
			if ( isset( $caller['function'] ) && in_array( $caller['function'], $this->plugin_functions_abort_upload ) ) {
				if ( $this->as3cf->get_setting( 'remove-local-file' ) || ! file_exists( get_attached_file( $post_id, true ) ) ) {
					// abort the rest of the update_attachment_metadata hook
					// if the file doesn't exist on the server, as the stream wrapper
					// has taken care of the rest.
					return true;
				}
			}
		}

		return $pre;
	}
}