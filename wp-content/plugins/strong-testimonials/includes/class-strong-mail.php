<?php
/**
 * Mail class.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Strong_Mail' ) ) :

class Strong_Mail {

	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'process_mail_queue' ), 20 );
	}

	/**
	 * Process mail queue
	 *
	 * @since 2.8.0
	 */
	public function process_mail_queue() {
		$current_queue = get_transient( 'wpmtst_mail_queue' );
		if ( ! $current_queue )
			return;

		foreach ( $current_queue as $email ) {
			$this->send_mail( $email );
		}

		delete_transient( 'wpmtst_mail_queue' );
	}

	public function send_mail( $email ) {
		if ( defined( 'IS_LOCALHOST') && IS_LOCALHOST ) {
			error_log( print_r( $email, true ) );
		} else {
			wp_mail( $email['to'], $email['subject'], $email['message'], $email['headers'] );
		}
	}

	/**
	 * Enqueue mail.
	 *
	 * @since 2.8.0
	 * @param $email
	 */
	public function enqueue_mail( $email ) {
		$current_queue = get_transient( 'wpmtst_mail_queue' );
		if ( $current_queue ) {
			delete_transient( 'wpmtst_mail_queue' );
		} else {
			$current_queue = array();
		}

		$current_queue[] = $email;
		set_transient( 'wpmtst_mail_queue', $current_queue, DAY_IN_SECONDS );
	}

}

endif;
