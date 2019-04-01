<?php

require_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-helper.php';

/**
 * Abstract logger class, responsible to persist log on database
 */
abstract class WC_EBANX_Logger {
	/**
	 * Method responsible to save log on database
	 *
	 * @param string $event event name to be logged.
	 * @param array  $log_data data to be logged.
	 */
	final protected static function save( $event, array $log_data ) {
		WC_EBANX_Database::insert(
			'logs', array(
				'time'            => current_time( 'mysql' ),
				'integration_key' => WC_EBANX_Helper::get_integration_key(),
				'event'           => $event,
				'log'             => json_encode( $log_data ),
			)
		);
	}

	/**
	 * This method should be reimplemented by child classes
	 *
	 * This method is responsible for receive log data, manage them and send them to method save
	 *
	 * @param array $log_data data to be logged.
	 * @throws Exception In case sub classes do not reimplement an exception is thrown.
	 */
	public static function persist( array $log_data = [] ) {
		throw new Exception( 'Logger child classes must reimplemented the persist function. See class-wc-ebanx-logger.php' );
	}
}
