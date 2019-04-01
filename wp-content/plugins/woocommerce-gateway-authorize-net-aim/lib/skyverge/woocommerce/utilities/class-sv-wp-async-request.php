<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Utilities
 * @author    SkyVerge / Delicious Brains
 * @copyright Copyright (c) 2015-2016 Delicious Brains Inc.
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WP_Async_Request' ) ) :

/**
 * SkyVerge Wordpress Async Request class
 *
 * Based on the incredible work by deliciousbrains - most of the code is from
 * here: https://github.com/A5hleyRich/wp-background-processing
 *
 * Forked & namespaced to prevent dependency conflicts and to facilitate
 * further customizations.
 *
 * Use SV_WP_Async_Request::set_data() to set request data, instead of ::data().
 *
 * @since 4.4.0
 */
abstract class SV_WP_Async_Request {


	/** @var string request prefix */
	protected $prefix = 'wp';

	/** @var string request action name */
	protected $action = 'async_request';

	/** @var string request identifier */
	protected $identifier;

	/** @var array request data */
	protected $data = array();


	/**
	 * Initiate a new async request
	 *
	 * @since 4.4.0
	 */
	public function __construct() {
		$this->identifier = $this->prefix . '_' . $this->action;

		add_action( 'wp_ajax_' . $this->identifier,        array( $this, 'maybe_handle' ) );
		add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
	}


	/**
	 * Set data used during the async request
	 *
	 * @since 4.4.0
	 * @param array $data
	 * @return \SV_WP_Async_Request
	 */
	public function set_data( $data ) {
		$this->data = $data;

		return $this;
	}


	/**
	 * Dispatch the async request
	 *
	 * @since 4.4.0
	 * @return array|WP_Error
	 */
	public function dispatch() {

		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_request_args();

		return wp_safe_remote_get( esc_url_raw( $url ), $args );
	}


	/**
	 * Get query args
	 *
	 * @since 4.4.0
	 * @return array
	 */
	protected function get_query_args() {

		if ( property_exists( $this, 'query_args' ) ) {
			return $this->query_args;
		}

		return array(
			'action' => $this->identifier,
			'nonce'  => wp_create_nonce( $this->identifier ),
		);
	}


	/**
	 * Get query URL
	 *
	 * @since 4.4.0
	 * @return string
	 */
	protected function get_query_url() {

		if ( property_exists( $this, 'query_url' ) ) {
			return $this->query_url;
		}

		return admin_url( 'admin-ajax.php' );
	}


	/**
	 * Get request args
	 *
	 * In 4.6.3 renamed from get_post_args to get_request_args
	 *
	 * @since 4.4.0
	 * @return array
	 */
	protected function get_request_args() {

		if ( property_exists( $this, 'request_args' ) ) {
			return $this->request_args;
		}

		return array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'body'      => $this->data,
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);
	}


	/**
	 * Maybe handle
	 *
	 * Check for correct nonce and pass to handler.
	 * @since 4.4.0
	 */
	public function maybe_handle() {
		check_ajax_referer( $this->identifier, 'nonce' );

		$this->handle();

		wp_die();
	}


	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 *
	 * @since 4.4.0
	 */
	abstract protected function handle();


}

endif;
