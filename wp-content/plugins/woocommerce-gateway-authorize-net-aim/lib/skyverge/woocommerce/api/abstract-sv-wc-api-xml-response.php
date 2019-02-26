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

if ( ! class_exists( 'SV_WC_API_XML_Response' ) ) :

/**
 * Base XML API response class.
 *
 * @since 4.3.0
 */
abstract class SV_WC_API_XML_Response implements SV_WC_API_Response {


	/** @var string string representation of this response */
	protected $raw_response_xml;

	/** @var SimpleXMLElement XML object */
	protected $response_xml;

	/** @var array|mixed|object XML data after conversion into an usable object */
	protected $response_data;


	/**
	 * Build an XML object from the raw response.
	 *
	 * @since 4.3.0
	 * @param string $raw_response_xml The raw response XML
	 */
	public function __construct( $raw_response_xml ) {

		$this->raw_response_xml = $raw_response_xml;

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$this->response_xml = new SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );

		/**
		 * workaround to convert the horrible data structure that SimpleXMLElement returns
		 * and provide a nice array of stdClass objects. Note there is some fidelity lost
		 * in the conversion (such as attributes), but implementing classes can access
		 * the response_xml member directly to retrieve them as needed.
		 */
		$this->response_data = json_decode( json_encode( $this->response_xml ) );
	}


	/**
	 * Magic method for getting XML element data. Note the response data has
	 * already been casted into simple data types (string,int,array) and does not
	 * require further casting in order to use.
	 *
	 * @since 4.3.0
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( ! isset( $this->response_data->$key ) ) {
			return null;
		}

		// array cast & empty check prevents fataling on empty stdClass objects
		$array = (array) $this->response_data->$key;

		if ( empty( $array ) ) {
			return null;
		}

		return $this->response_data->$key;
	}


	/**
	 * Get the string representation of this response.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	public function to_string() {

		$response = $this->raw_response_xml;

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $response ) ) {
			$dom->formatOutput = true;
			$response = $dom->saveXML();
		}

		return $response;
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
