<?php

/**
 * Log Subscription Renewal event data
 */
final class WC_EBANX_Subscription_Renewal_Logger extends WC_EBANX_Logger {
	/**
	 *
	 * @param array $log_data data to be logged.
	 */
	public static function persist( array $log_data = [] ) {
		parent::save( 'subscription_renewal', $log_data );
	}
}
