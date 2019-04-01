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

if ( ! class_exists( 'SV_WC_API_XML_Request' ) ) :

/**
 * Base XML API request class.
 *
 * @since 4.3.0
 */
abstract class SV_WC_API_XML_Request implements SV_WC_API_Request {

	/** @var string the request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string the request path */
	protected $path = '';

	/** @var array the request parameters */
	protected $params = array();

	/** @var array request data */
	protected $request_data;

	/** @var string root element for XML */
	protected $root_element;

	/** @var \XMLWriter $xml object */
	protected $xml;

	/** @var string complete request XML */
	protected $request_xml;


	/**
	 * Get the method for this request.
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::get_method()
	 * @return null|string
	 */
	public function get_method() {
		return $this->method;
	}


	/**
	 * Get the path for this request.
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
	 * @since 4.5.0
	 * @return array
	 */
	public function get_params() {
		return $this->params;
	}


	/**
	 * Convert the request data into XML.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function to_xml() {

		if ( ! empty( $this->request_xml ) ) {
			return $this->request_xml;
		}

		$this->xml = new XMLWriter();

		// Create XML document in memory
		$this->xml->openMemory();

		// Set XML version & encoding
		$this->xml->startDocument( '1.0', 'UTF-8' );

		$request_data = $this->get_request_data();

		SV_WC_Helper::array_to_xml( $this->xml, $this->get_root_element(), $request_data[ $this->get_root_element() ] );

		$this->xml->endDocument();

		return $this->request_xml = $this->xml->outputMemory();
	}


	/**
	 * Return the request data to be converted to XML
	 *
	 * @since 4.3.0
	 * @return array
	 */
	public function get_request_data() {

		return $this->request_data;
	}


	/**
	 * Get the string representation of this request
	 *
	 * @since 4.3.0
	 * @see SV_WC_API_Request::to_string()
	 * @return string
	 */
	public function to_string() {

		return $this->to_xml();
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

		return $this->prettify_xml( $this->to_string() );
	}


	/**
	 * Helper method for making XML pretty, suitable for logging or rendering
	 *
	 * @since 4.3.0
	 * @param string $xml_string ugly XML string
	 * @return string
	 */
	public function prettify_xml( $xml_string ) {

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $xml_string ) ) {
			$dom->formatOutput = true;
			$xml_string = $dom->saveXML();
		}

		return $xml_string;
	}


	/**
	 * Concrete classes must implement this method to return the root element
	 * for the XML document
	 *
	 * @since 4.3.0
	 * @return string
	 */
	abstract protected function get_root_element();


}

endif; // class exists check
