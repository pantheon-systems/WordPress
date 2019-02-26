<?php
namespace AffWP\Utils;

/**
 * Implements date formatting helpers for AffiliateWP.
 *
 * The final keyword will be in use until we (likely) integrate
 * the Carbon library in 2.2.
 *
 * @since 2.1.9
 * @final
 */
final class Date extends \DateTime {

	/**
	 * Sets up the date.
	 *
	 * @since 2.1.9
	 */
	public function __construct( $time = 'now', \DateTimeZone $timezone = null ) {
		if ( null === $timezone ) {
			$timezone = new \DateTimeZone( affwp_get_timezone() );
		}

		parent::__construct( $time, $timezone );

		// Apply the WP offset based on the WP timezone that was set.
		$offset   = $this->getOffset();
		$interval = \DateInterval::createFromDateString( "{$offset} seconds" );
		$this->add( $interval );
	}

	/**
	 * Formats a given date string according to WP date and time formats and timezone.
	 *
	 * @since 2.1.9
	 *
	 * @param string|true $format Optional. How to format the date string.  Accepts 'date',
	 *                            'time', 'datetime', 'mysql', 'timestamp', 'wp_timestamp',
	 *                            'object', or any valid date_format() string. If true, 'datetime'
	 *                            will be used. Default 'datetime'.
	 * @return string|int|\DateTime Formatted date string, timestamp if `$type` is timestamp,
	 *                              or a DateTime object if `$type` is 'object'.
	 */
	public function format( $format ) {

		if ( empty( $format ) || true === $format ) {
			$format = 'datetime';
		}

		switch( $format ) {
			case 'date':
			case 'time':
			case 'datetime':
			case 'mysql':
				$formatted = parent::format( affwp_get_date_format( $format ) );
				break;

			case 'object':
				$formatted = $this;
				break;

			case 'timestamp':
				$formatted = $this->getTimestamp();
				break;

			case 'wp_timestamp':
				/*
				 * Note: Even if the timezone has been changed, getTimestamp() will still
				 * return the original timestamp because DateTime doesn't directly allow
				 * conversion of the timestamp in terms of offset; it's immutable.
				 */
				$formatted = $this->getWPTimestamp();
				break;

			default:
				$formatted = parent::format( $format );
				break;
		}

		return $formatted;
	}

	/**
	 * Retrieves the date timestamp with the WordPress offset applied.
	 *
	 * @since 2.1.9
	 *
	 * @return int WordPress "local" timestamp.
	 */
	public function getWPTimestamp() {
		return $this->getTimestamp() + affiliate_wp()->utils->wp_offset;
	}

}
