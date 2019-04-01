<?php

/**
 * Log PLugin Settings Change event data
 */
final class WC_EBANX_Plugin_Settings_Change_Logger extends WC_EBANX_Logger {
	/**
	 *
	 * @param array $log_data data to be logged.
	 */
	public static function persist( array $log_data = [] ) {
		parent::save(
			'plugin_settings_change',
			array_merge(
				WC_EBANX_Log::get_platform_info(),
				[ 'settings' => $log_data ]
			)
		);
	}
}
