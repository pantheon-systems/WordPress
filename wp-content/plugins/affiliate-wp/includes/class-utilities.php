<?php
use AffWP\Utils;

/**
 * Utilities class for AffiliateWP.
 *
 * @since 2.0
 *
 * @property-read string $date_format The current WordPress date format.
 * @property-read string $time_format The current WordPress time format.
 * @property-read int    $wp_offset   The calculated WordPress gmt_offset in seconds.
 */
class Affiliate_WP_Utilities {

	/**
	 * Batch process registry class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Batch_Process\Registry
	 */
	public $batch;

	/**
	 * Temporary data storage class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Data_Storage
	 */
	public $data;

	/**
	 * Upgrades class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \Affiliate_WP_Upgrades
	 */
	public $upgrades;

	/**
	 * Logger class instance.
	 *
	 * @access public
	 * @since  2.0.2
	 * @var    \Affiliate_WP_Logging
	 */
	public $logs;

	/**
	 * Signifies whether debug mode is enabled.
	 *
	 * @access protected
	 * @since  2.0.2
	 * @var    bool
	 */
	public $debug_enabled;

	/**
	 * Instantiates the utilities class.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {
		$this->wp_offset   = get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
		$this->date_format = get_option( 'date_format', 'M j, Y' );
		$this->time_format = get_option( 'time_format', 'g:i a' );

		$this->includes();
		$this->setup_objects();
	}

	/**
	 * Includes necessary utility files.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function includes() {
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-date.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-logging.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-upgrade-registry.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-process-registry.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-data-storage.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-upgrades.php';
	}

	/**
	 * Sets up utility objects.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function setup_objects() {
		// Set the debug flag.
		$this->debug_enabled = affiliate_wp()->settings->get( 'debug_mode', false );

		$this->logs     = new Affiliate_WP_Logging;
		$this->batch    = new Utils\Batch_Process\Registry;
		$this->upgrades = new Affiliate_WP_Upgrades( $this );
		$this->data     = new Utils\Data_Storage;

		// Initialize batch registry after loading the upgrades class.
		$this->batch->init();
	}

	/**
	 * Writes a debug log entry.
	 *
	 * @access public
	 * @since  2.0.2
	 *
	 * @param string $message Message to write to the debug log.
	 */
	public function log( $message = '' ) {
		if ( $this->debug_enabled ) {
			$this->logs->log( $message );
		}
	}

	/**
	 * Performs processes on request data depending on the given context.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array  $data    Request data.
	 * @param string $old_key Optional. Old key under which to process data. Default empty.
	 * @return array (Maybe) processed request data.
	 */
	public function process_request_data( $data, $old_key = '' ) {
		switch ( $old_key ) {
			case 'user_name':
			case '_affwp_affiliate_user_name':
			case 'affwp_pms_user_name':
				if ( ! empty( $data[ $old_key ] ) ) {
					$username = sanitize_text_field( $data[ $old_key ] );

					if ( $user = get_user_by( 'login', $username ) ) {
						$data['user_id'] = $user->ID;

						unset( $data[ $old_key ] );
					} else {
						$data['user_id'] = 0;
					}
				}
				break;

			default : break;
		}
		return $data;
	}

	/**
	 * Retrieves a date format string based on a given short-hand format.
	 *
	 * @since 2.1.9
	 *
	 * @param string $format Shorthand date format string. Accepts 'date', 'time', 'mysql', or
	 *                       'datetime'. If none of the accepted values, the original value will
	 *                       simply be returned. Default is the value of the `$date_format` property,
	 *                       derived from the core 'date_format' option.
	 * @return string date_format()-compatible date format string.
	 */
	public function get_date_format( $format ) {

		if ( empty( $format ) ) {
			$format = 'date';
		}

		if ( ! in_array( $format, array( 'date', 'time', 'datetime', 'mysql' ) ) ) {
			return $format;
		}

		switch( $format ) {
			case 'time':
				$format = $this->time_format;
				break;

			case 'datetime':
				$format = $this->date_format . ' ' . $this->time_format;
				break;

			case 'mysql':
				$format = 'Y-m-d H:i:s';
				break;

			case 'date':
			default:
				$format = $this->date_format;
				break;
		}

		return $format;
	}

	/**
	 * Retrieves a date instance for the WP timezone (and offset) based on the given date string.
	 *
	 * @since 2.1.9
	 *
	 * @param string $date_string Optional. Date string. Default 'now'.
	 * @param string $timezone    Optional. Timezone to generate the Carbon instance for.
	 *                            Default is the timezone set in WordPress settings.
	 * @return \AffWP\Utils\Date Date instance.
	 */
	public function date( $date_string = 'now' ) {

		$timezone = affwp_get_timezone();

		/*
		 * Create the DateTime object with the "local" WordPress timezone.
		 *
		 * Note that supplying the timezone during DateTime instantiation doesn't actually
		 * convert the UNIX timestamp, it just lays the groundwork for deriving the offset.
		 */
		$date = new Utils\Date( $date_string, new DateTimezone( $timezone ) );

		return $date;
	}

	/**
	 * Refreshes the wp_offset property for the benefit of PHPUnit.
	 *
	 * @since 2.1.9
	 */
	public function _refresh_wp_offset() {
		if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
			_doing_it_wrong( 'This method is only intended for use in phpunit tests', '2.1.9' );
		} else {
			$this->wp_offset = get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
		}

	}
}
