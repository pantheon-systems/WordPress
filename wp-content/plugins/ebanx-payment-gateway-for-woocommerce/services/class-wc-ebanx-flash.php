<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_EBANX_Flash
 */
class WC_EBANX_Flash {
	/**
	 * The key we are using on wp_option
	 */
	const KEY = '_ebanx_wp_flash_messages';

	/**
	 * Enqueue every flash message to the admin_notices hook
	 *
	 * @return void
	 */
	public static function enqueue_admin_messages() {
		$flash_messages = self::get_messages();
		$notices        = new WC_EBANX_Notice();
		foreach ( $flash_messages as $flash_message ) {
			$notices
				->with_message( $flash_message['message'] )
				->with_type( $flash_message['type'] );
			if ( $flash_message['dismissible'] ) {
				$notices->dismissible();
			}
			$notices->enqueue();
		}
	}

	/**
	 * Adds a message to WP_Option
	 *
	 * @param  string  $message     The message to enqueue.
	 * @param  string  $type        The notice type.
	 * @param  boolean $dismissible If the notice will be dismissible.
	 * @return void
	 */
	public static function add_message( $message, $type = 'error', $dismissible = false ) {
		$flash_messages   = self::get_messages( false );
		$flash_messages[] = array(
			'message'     => $message,
			'type'        => $type,
			'dismissible' => $dismissible,
		);
		update_option( self::KEY, $flash_messages );
	}

	/**
	 * Returns all the unqueued flash messages in an array
	 *
	 * @param bool $clear
	 *
	 * @return array All the enqueued flash messages
	 */
	public static function get_messages( $clear = true ) {
		$flash_messages = maybe_unserialize( get_option( self::KEY, array() ) );
		if ( $clear ) {
			self::clear_messages();
		}
		return $flash_messages;
	}

	/**
	 * Clears WordPress notices.
	 */
	public static function clear_messages() {
		delete_option( self::KEY );
	}
}
