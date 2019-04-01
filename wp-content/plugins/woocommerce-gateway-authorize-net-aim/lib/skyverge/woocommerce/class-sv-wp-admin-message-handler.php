<?php
/**
 * WooCommerce Admin Message Handler
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
 * @package   SkyVerge/WordPress/WP-Admin-Message-Handler
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */


defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WP_Admin_Message_Handler' ) ) :

/**
 * # WordPress Admin Message Handler Class
 *
 * This class provides a reusable wordpress admin messaging facility for setting
 * and displaying messages and error messages across admin page requests without
 * resorting to passing the messages as query vars.
 *
 * ## Usage
 *
 * To use simple instantiate the class then set one or more messages:
 *
 * `
 * $admin_message_handler = new WP_Admin_Message_Handler( __FILE__ );
 * $admin_message_handler->add_message( 'Hello World!' );
 * `
 *
 * Then show the messages wherever you need, either with the built-in method
 * or by writing your own:
 *
 * `$admin_message_handler->show_messages();`
 *
 * @version 1.0.1
 */
class SV_WP_Admin_Message_Handler {

	/** transient message prefix */
	const MESSAGE_TRANSIENT_PREFIX = '_wp_admin_message_';

	/** the message id GET name */
	const MESSAGE_ID_GET_NAME = 'wpamhid';

	/** @var string unique message identifier, defaults to __FILE__ unless otherwise set */
	private $message_id;

	/** @var array array of messages */
	private $messages = array();

	/** @var array array of error messages */
	private $errors = array();


	/**
	 * Construct and initialize the admin message handler class
	 *
	 * @since 1.0.0
	 * @param string $message_id optional message id.  Best practice is to set
	 *        this to a unique identifier based on the client plugin, such as __FILE__
	 */
	public function __construct( $message_id = null ) {

		$this->message_id = $message_id;

		// load any available messages
		$this->load_messages();

		add_filter( 'wp_redirect', array( $this, 'redirect' ), 1, 2 );
	}


	/**
	 * Persist messages
	 *
	 * @since 1.0.0
	 * @return boolean true if any messages were set, false otherwise
	 */
	public function set_messages() {

		// any messages to persist?
		if ( $this->message_count() > 0 || $this->error_count() > 0 ) {

			set_transient(
				self::MESSAGE_TRANSIENT_PREFIX . $this->get_message_id(),
				array( 'errors' => $this->errors, 'messages' => $this->messages ),
				60 * 60
			);

			return true;
		}

		return false;
	}


	/**
	 * Loads messages
	 *
	 * @since 1.0.0
	 */
	public function load_messages() {

		if ( isset( $_GET[ self::MESSAGE_ID_GET_NAME ] ) && $this->get_message_id() == $_GET[ self::MESSAGE_ID_GET_NAME ] ) {

			$memo = get_transient( self::MESSAGE_TRANSIENT_PREFIX . $_GET[ self::MESSAGE_ID_GET_NAME ] );

			if ( isset( $memo['errors'] ) )   $this->errors   = $memo['errors'];
			if ( isset( $memo['messages'] ) ) $this->messages = $memo['messages'];

			$this->clear_messages( $_GET[ self::MESSAGE_ID_GET_NAME ] );
		}
	}


	/**
	 * Clear messages and errors
	 *
	 * @since 1.0.0
	 * @param string $id the messages identifier
	 */
	public function clear_messages( $id ) {
		delete_transient( self::MESSAGE_TRANSIENT_PREFIX . $id );
	}


	/**
	 * Add an error message.
	 *
	 * @since 1.0.0
	 * @param string $error error message
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}


	/**
	 * Add a message.
	 *
	 * @since 1.0.0
	 * @param string $message the message to add
	 */
	public function add_message( $message ) {
		$this->messages[] = $message;
	}


	/**
	 * Get error count.
	 *
	 * @since 1.0.0
	 * @return int error message count
	 */
	public function error_count() {
		return sizeof( $this->errors );
	}


	/**
	 * Get message count.
	 *
	 * @since 1.0.0
	 * @return int message count
	 */
	public function message_count() {
		return sizeof( $this->messages );
	}


	/**
	 * Get error messages
	 *
	 * @since 1.0.0
	 * @return array of error message strings
	 */
	public function get_errors() {
		return $this->errors;
	}


	/**
	 * Get an error message
	 *
	 * @since 1.0.0
	 * @param int $index the error index
	 * @return string the error message
	 */
	public function get_error( $index ) {
		return isset( $this->errors[ $index ] ) ? $this->errors[ $index ] : '';
	}


	/**
	 * Get messages
	 *
	 * @since 1.0.0
	 * @return array of message strings
	 */
	public function get_messages() {
		return $this->messages;
	}


	/**
	 * Get a message
	 *
	 * @since 1.0.0
	 * @param int $index the message index
	 * @return string the message
	 */
	public function get_message( $index ) {
		return isset( $this->messages[ $index ] ) ? $this->messages[ $index ] : '';
	}


	/**
	 * Render the errors and messages.
	 *
	 * @since 1.0.0
	 * @param array $params {
	 *     Optional parameters.
	 *
	 *     @type array $capabilities Any user capabilities to check if the user is allowed to view the messages,
	 *                               default: `manage_woocommerce`
	 * }
	 */
	public function show_messages( $params = array() ) {

		$params = wp_parse_args( $params, array(
			'capabilities' => array(
				'manage_woocommerce',
			),
		) );

		$check_user_capabilities = array();

		// check if user has at least one capability that allows to see messages
		foreach ( $params['capabilities'] as $capability ) {
			$check_user_capabilities[] = current_user_can( $capability );
		}

		// bail out if user has no minimum capabilities to see messages
		if ( ! in_array( true, $check_user_capabilities, true ) ) {
			return;
		}

		if ( $this->error_count() > 0 ) {
			echo '<div id="wp-admin-message-handler-error" class="error"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_errors() ) . '</strong></li></ul></div>';
		}

		if ( $this->message_count() > 0 ) {
			echo '<div id="wp-admin-message-handler-message"  class="updated"><ul><li><strong>' . implode( '</strong></li><li><strong>', $this->get_messages() ) . '</strong></li></ul></div>';
		}
	}


	/**
	 * Redirection hook which persists messages into session data.
	 *
	 * @since 1.0.0
	 * @param string $location the URL to redirect to
	 * @param int $status the http status
	 * @return string the URL to redirect to
	 */
	public function redirect( $location, $status ) {

		// add the admin message id param to the
		if ( $this->set_messages() ) {
			$location = add_query_arg( self::MESSAGE_ID_GET_NAME, $this->get_message_id(), $location );
		}

		return $location;
	}


	/**
	 * Generate a unique id to identify the messages
	 *
	 * @since 1.0.0
	 * @return string unique identifier
	 */
	protected function get_message_id() {

		if ( ! isset( $this->message_id ) ) $this->message_id = __FILE__;

		return wp_create_nonce( $this->message_id );

	}


}

endif; // class exists check
