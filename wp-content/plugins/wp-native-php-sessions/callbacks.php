<?php

/**
 * Session handler assigned by session_set_save_handler().
 *
 * This function is used to handle any initialization, such as file paths or
 * database connections, that is needed before accessing session data. The plugin
 * does not need to initialize anything in this function.
 *
 * This function should not be called directly.
 *
 * @return true
 */
function _pantheon_session_open() {
	// We use !empty() in the following check to ensure that blank session IDs are not valid.
	if ( ! empty( $_COOKIE[ session_name() ] ) || ( is_ssl() && ! empty( $_COOKIE[ substr(session_name(), 1) ] ) ) ) {
		// If a session cookie exists, initialize the session. Otherwise the
		// session is only started on demand in _pantheon_session_write(), making
		// anonymous users not use a session cookie unless something is stored in
		// $_SESSION. This allows HTTP proxies to cache anonymous pageviews.
		if ( get_current_user_id() || ! empty( $_SESSION ) ) {
			nocache_headers();
		}
	} else {
		// Set a session identifier for this request. This is necessary because
		// we lazily start sessions at the end of this request
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		$hasher = new PasswordHash( 8, false );
		session_id( md5( $hasher->get_random_bytes( 32 ) ) );
		if ( is_ssl() ) {
			$insecure_session_name = substr( session_name(), 1 );
			$insecure_session_id = md5( $hasher->get_random_bytes( 32 ) );
			//set custom expire time during cookie session creation
			$lifetime = (int) apply_filters( 'pantheon_session_expiration', 0 );
			setcookie( $insecure_session_name, $insecure_session_id, $_SERVER['REQUEST_TIME'] + $lifetime);
		}
	}
	return true;
}

/**
 * Reads an entire session from the database (internal use only).
 *
 * Also initializes the $user object for the user associated with the session.
 * This function is registered with session_set_save_handler() to support
 * database-backed sessions. It is called on every page load when PHP sets
 * up the $_SESSION superglobal.
 *
 * This function is an internal function and must not be called directly.
 * Doing so may result in logging out the current user, corrupting session data
 * or other unexpected behavior. Session data must always be accessed via the
 * $_SESSION superglobal.
 *
 * @param $sid
 *   The session ID of the session to retrieve.
 *
 * @return
 *   The user's session, or an empty string if no session exists.
 */
function _pantheon_session_read( $sid ) {

	// Handle the case of first time visitors and clients that don't store
	// cookies (eg. web crawlers).
	$insecure_session_name = substr( session_name(), 1 );
	if ( ! isset( $_COOKIE[ session_name() ] ) && ! isset( $_COOKIE[ $insecure_session_name ] ) ) {
		return '';
	}

	$session = \Pantheon_Sessions\Session::get_by_sid( $sid );
	if ( $session ) {
		return $session->get_data();
	} else {
		return '';
	}

}

/**
 * Writes an entire session to the database (internal use only).
 *
 * This function is registered with session_set_save_handler() to support
 * database-backed sessions.
 *
 * This function is an internal function and must not be called directly.
 * Doing so may result in corrupted session data or other unexpected behavior.
 * Session data must always be accessed via the $_SESSION superglobal.
 *
 * @param $sid The session ID of the session to write to.
 * @param $value Session data to write as a serialized string.
 * @return boolean
 */
function _pantheon_session_write( $sid, $value ) {

	$session = \Pantheon_Sessions\Session::get_by_sid( $sid );

	if ( ! $session ) {
		$session = \Pantheon_Sessions\Session::create_for_sid( $sid );
	}

	if ( ! $session ) {
		trigger_error( 'Could not write session to the database. Please check MySQL configuration.', E_WARNING );
		return false;
	}

	$session->set_data( $value );
	
	return true;
}

/**
 * Session handler assigned by session_set_save_handler().
 *
 * Cleans up a specific session.
 *
 * @param $sid Session ID.
 */
function _pantheon_session_destroy( $sid ) {

	$session = \Pantheon_Sessions\Session::get_by_sid( $sid );
	if ( ! $session ) {
		return;
	}

	$session->destroy();

}

/**
 * Session handler assigned by session_set_save_handler().
 *
 * This function is used to close the current session. Because the plugin stores
 * session data in the database immediately on write, this function does
 * not need to do anything.
 *
 * This function should not be called directly.
 *
 * @return true
 */
function _pantheon_session_close() {
	return true;
}

/**
 * Session handler assigned by session_set_save_handler().
 *
 * Cleans up stalled sessions.
 *
 * @param int $lifetime The value of session.gc_maxlifetime, passed by PHP. Sessions not updated for more than $lifetime seconds will be removed.
 * @return true
 */
function _pantheon_session_garbage_collection( $lifetime ) {
	global $wpdb;

	// Be sure to adjust 'php_value session.gc_maxlifetime' to a large enough
	// value. For example, if you want user sessions to stay in your database
	// for three weeks before deleting them, you need to set gc_maxlifetime
	// to '1814400'. At that value, only after a user doesn't log in after
	// three weeks (1814400 seconds) will his/her session be removed.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->pantheon_sessions WHERE `datetime` <= %s ", date( 'Y-m-d H:i:s', time() - $lifetime ) ) );
	return true;
}
