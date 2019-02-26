<?php

class Affiliate_WP_Visits_Graph extends Affiliate_WP_Graph {

	public $total     = 0;
	public $converted = 0;

	/**
	 * Get things started
	 *
	 * @since 1.1
	 */
	public function __construct( $_data = array() ) {

		// Generate unique ID
		$this->id = md5( rand() );

		// Setup default options;
		$this->options = array(
			'y_mode'          => null,
			'y_decimals'      => 0,
			'x_decimals'      => 0,
			'y_position'      => 'right',
			'time_format'     => '%d/%b',
			'ticksize_unit'   => 'day',
			'ticksize_num'    => 1,
			'multiple_y_axes' => false,
			'bgcolor'         => '#f9f9f9',
			'bordercolor'     => '#ccc',
			'color'           => '#bbb',
			'borderwidth'     => 2,
			'bars'            => false,
			'lines'           => true,
			'points'          => true,
			'affiliate_id'    => false,
			'show_controls'   => true,
			'query_args'      => array(),
		);
	}

	/**
	 * Retrieve referral data
	 *
	 * @since 1.1
	 */
	public function get_data() {

		$converted   = array();
		$unconverted = array();

		$dates = affwp_get_report_dates();

		$start = $dates['year'] . '-' . $dates['m_start'] . '-' . $dates['day'] . ' 00:00:00';
		$end   = $dates['year_end'] . '-' . $dates['m_end'] . '-' . $dates['day_end'] . ' 23:59:59';

		$date  = array(
			'start' => $start,
			'end'   => $end
		);

		$difference = ( strtotime( $date['end'] ) - strtotime( $date['start'] ) );

		$args = wp_parse_args( $this->get( 'query_args' ), array(
			'orderby'      => 'date',
			'order'        => 'ASC',
			'date'         => $date,
			'number'       => -1,
			'affiliate_id' => $this->get( 'affiliate_id' )
		) );

		$visits = affiliate_wp()->visits->get_visits( $args );

		$converted_data   = array();
		$unconverted_data = array();

		if( $visits ) {

			// Loop through each visit and find how many there are per day
			foreach( $visits as $visit ) {

				if ( in_array( $dates['range'], array( 'this_year', 'last_year' ), true )
					|| $difference >= YEAR_IN_SECONDS
				) {
					$date = date( 'Y-m', strtotime( $visit->date ) );
				} else {
					$date = date( 'Y-m-d', strtotime( $visit->date ) );
				}

				$this->total += 1;

				if( ! empty( $visit->referral_id ) ) {

					if( array_key_exists( $date, $converted_data ) ) {
						$converted_data[ $date ] += 1;
					} else {
						$converted_data[ $date ] = 1;
					}

					$this->converted += 1;

				} else {

					if( array_key_exists( $date, $unconverted_data ) ) {
						$unconverted_data[ $date ] += 1;
					} else {
						$unconverted_data[ $date ] = 1;
					}

				}

			}
		}

		$converted_visits = array();
		foreach( $converted_data as $date => $count ) {

			$converted_visits[] = array( strtotime( $date ) * 1000, $count );

		}

		$unconverted_visits = array();
		$unconverted_visits[] = array( strtotime( $start ) * 1000 );
		$unconverted_visits[] = array( strtotime( $end ) * 1000 );
		foreach( $unconverted_data as $date => $count ) {

			$unconverted_visits[] = array( strtotime( $date ) * 1000, $count );

		}

		$data = array(
			__( 'Converted Visits', 'affiliate-wp' )   => $converted_visits,
			__( 'Unconverted Visits', 'affiliate-wp' ) => $unconverted_visits
		);

		return $data;

	}

	/**
	 * Retrieve conversion rate for successful visits
	 *
	 * @since 1.1
	 */
	public function get_conversion_rate() {
		return $this->total > 0 ? round( ( $this->converted / $this->total ) * 100, 2 ) : 0;
	}

}