<?php
/**
 * Implementation of 'SessionHandlerInterface' that writes to the database.
 *
 * @package WPNPS
 */

namespace Pantheon_Sessions;

use Pantheon_Sessions\Session;

/**
 * Implementation of 'SessionHandlerInterface' that writes to the database.
 *
 * @package WPNPS
 */
class Session_Handler implements \SessionHandlerInterface {

	/**
	 * Closes the session.
	 *
	 * @param string $save_path    Path to where the session is to be stored.
	 * @param string $session_name Name of the session.
	 * @return boolean
	 */
	public function open( $save_path, $session_name ) {
		return true;
	}

	/**
	 * Writes a session to the database.
	 *
	 * @param string $session_id   Session id.
	 * @param string $session_data Session data.
	 * @return boolean
	 */
	public function write( $session_id, $session_data ) {
		$session = Session::get_by_sid( $session_id );

		if ( ! $session ) {
			$session = Session::create_for_sid( $session_id );
		}

		if ( ! $session ) {
			trigger_error( 'Could not write session to the database. Please check MySQL configuration.', E_USER_WARNING );
			return false;
		}

		$session->set_data( $session_data );

		return true;
	}

	/**
	 * Reads session data from the database.
	 *
	 * @param string $session_id Session id.
	 * @return string
	 */
	public function read( $session_id ) {
		// Handle the case of first time visitors and clients that don't store
		// cookies (eg. web crawlers).
		$insecure_session_name = substr( session_name(), 1 );
		if ( empty( $session_id )
			|| ( ! isset( $_COOKIE[ session_name() ] ) && ! isset( $_COOKIE[ $insecure_session_name ] ) ) ) {
			return '';
		}

		$session = Session::get_by_sid( $session_id );
		if ( $session ) {
			return $session->get_data();
		} else {
			return '';
		}
	}

	/**
	 * Destroys the session.
	 *
	 * @param string $session_id Session id.
	 */
	public function destroy( $session_id ) {
		$session = Session::get_by_sid( $session_id );
		if ( ! $session ) {
			return;
		}

		$session->destroy();

		return true;
	}

	/**
	 * Runs the garbage collection process.
	 *
	 * @param integer $maxlifetime Maximum lifetime in seconds.
	 */
	public function gc( $maxlifetime ) {
		global $wpdb;

		$wpdb = Session::restore_wpdb_if_null( $wpdb );

		// Be sure to adjust 'php_value session.gc_maxlifetime' to a large enough
		// value. For example, if you want user sessions to stay in your database
		// for three weeks before deleting them, you need to set gc_maxlifetime
		// to '1814400'. At that value, only after a user doesn't log in after
		// three weeks (1814400 seconds) will his/her session be removed.
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->pantheon_sessions WHERE `datetime` <= %s ", gmdate( 'Y-m-d H:i:s', time() - $maxlifetime ) ) );
		return true;
	}

	/**
	 * Closes the session.
	 *
	 * @return boolean
	 */
	public function close() {
		return true;
	}

}
