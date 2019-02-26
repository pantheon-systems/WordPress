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
 * @package   SkyVerge/WooCommerce/API/Request
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_API_JSON_Request' ) ) :

/**
 * Base JSON API request class.
 *
 * @since 4.3.0
 */
abstract class SV_WC_API_JSON_Request implements SV_WC_API_Request {


	/** @var string The request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string The request path */
	protected $path;

	/** @var array The request parameters, if any */
	protected $params = array();

	/** @var array the request data */
	protected $data = array();


	/**
	 * Get the request method.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_method()
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}


	/**
	 * Get the request path.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_path()
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 * Get the request parameters.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_params()
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}


	/**
	 * Get the request data.
	 *
	 * @since 4.5.0
	 * @return array
	 */
	protected function get_data() {
		return $this->data;
	}


	/** API Helper Methods ******************************************************/


	/**
	 * Get the string representation of this request.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::to_string()
	 * @return string
	 */
	public function to_string() {

		$data = $this->get_data();

		if ( empty( $data ) && ! in_array( strtoupper( $this->get_method() ), array( 'GET', 'HEAD' ) ) ) {
			$data = $this->get_params();
		}

		return ! empty( $data ) ? json_encode( $data ) : '';
	}


	/**
	 * Get the string representation of this request with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		return $this->to_string();
	}


}

endif; // class exists check
