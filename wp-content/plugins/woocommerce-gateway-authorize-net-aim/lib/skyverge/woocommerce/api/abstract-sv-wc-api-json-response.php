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
 * @package   SkyVerge/WooCommerce/API/Response
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_API_JSON_Response' ) ) :


/**
 * Base JSON API response class.
 *
 * @since 4.3.0
 */
abstract class SV_WC_API_JSON_Response implements SV_WC_API_Response {


	/** @var string string representation of this response */
	protected $raw_response_json;

	/** @var mixed decoded response data */
	public $response_data;


	/**
	 * Build the data object from the raw JSON.
	 *
	 * @since 4.3.0
	 * @param string $raw_response_json The raw JSON
	 */
	public function __construct( $raw_response_json ) {

		$this->raw_response_json = $raw_response_json;

		$this->response_data = json_decode( $raw_response_json );
	}


	/**
	 * Magic accessor for response data attributes
	 *
	 * @since 4.3.0
	 * @param string $name The attribute name to get.
	 * @return mixed The attribute value
	 */
	public function __get( $name ) {

		// accessing the response_data object indirectly via attribute (useful when it's a class)
		return isset( $this->response_data->$name ) ? $this->response_data->$name : null;
	}


	/**
	 * Get the string representation of this response.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Response::to_string()
	 * @return string
	 */
	public function to_string() {

		return $this->raw_response_json;
	}


	/**
	 * Get the string representation of this response with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Response::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		return $this->to_string();
	}


}

endif; // class exists check
