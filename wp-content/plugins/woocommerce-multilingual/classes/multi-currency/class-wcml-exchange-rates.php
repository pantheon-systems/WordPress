<?php

/**
 * Class WCML_Exchange_Rates
 */
class WCML_Exchange_Rates {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var array */
	private $services = array();
	/** @var array */
	private $settings;
	/** @var  WP_Locale */
	private $wp_locale;

	const CRONJOB_EVENT = 'wcml_exchange_rates_update';
	const DIGITS_AFTER_DECIMAL_POINT = 6;

	/**
	 * WCML_Exchange_Rates constructor.
	 *
	 * @param $woocommerce_wpml
	 * @param WP_Locale
	 */
	public function __construct( $woocommerce_wpml, $wp_locale ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wp_locale = $wp_locale;
	}

	public function add_actions() {
		if ( is_admin() ) {
			add_action( 'wcml_saved_mc_options', array( $this, 'update_exchange_rate_options' ) ); //before init
		}
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		if ( $this->woocommerce_wpml->multi_currency->get_currencies() ) {
			if ( is_admin() ) {
				add_action( 'wp_ajax_wcml_update_exchange_rates', array( $this, 'update_exchange_rates_ajax' ) );
			}
			add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
			add_action( self::CRONJOB_EVENT, array( $this, 'update_exchange_rates' ) );
		}
	}

	public function initialize_settings() {
		if ( ! isset( $this->woocommerce_wpml->settings['multi_currency']['exchange_rates'] ) ) {
			$this->settings = array(
				'automatic'      => 0,
				'service'        => 'fixerio',
				'lifting_charge' => 0,
				'schedule'       => 'manual',
				'week_day'       => 1,
				'month_day'      => 1
			);
			$this->save_settings();
		} else {
			$this->settings =& $this->woocommerce_wpml->settings['multi_currency']['exchange_rates'];
		}
	}

	/**
	 * @return array
	 */
	public function get_services() {
		return $this->services;
	}

	/**
	 * @param string $service_id
	 * @param WCML_Exchange_Rate_Service $service
	 */
	public function add_service( $service_id, $service ) {
		$this->services[ $service_id ] = $service;
	}

	/**
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get_setting( $key ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
	}

	public function save_settings() {
		$this->woocommerce_wpml->settings['multi_currency']['exchange_rates'] = $this->settings;
		$this->woocommerce_wpml->update_settings();
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function save_setting( $key, $value ) {
		$this->settings[ $key ] = $value;
		$this->save_settings();
	}

	public function update_exchange_rates_ajax() {
		$response = array();
		if ( wp_create_nonce( 'update-exchange-rates' ) === $_POST['wcml_nonce'] ) {
			try {
				$rates                    = $this->update_exchange_rates();
				$response['success']      = 1;
				$response['last_updated'] = date_i18n( 'F j, Y g:i a', $this->settings['last_updated'] );
				$response['rates']        = $rates;
			} catch ( Exception $e ) {
				$response['success'] = 0;
				$response['error']   = $e->getMessage();
				$response['service'] = $this->settings['service'];
			}
		} else {
			$response['success'] = 0;
			$response['error']   = 'Invalid nonce';
		}
		wp_send_json( $response );
	}

	public function update_exchange_rates() {

		if ( isset( $this->services[ $this->settings['service'] ] ) ) {
			$service =& $this->services[ $this->settings['service'] ];

			$currencies           = $this->woocommerce_wpml->multi_currency->get_currency_codes();
			$default_currency     = get_option( 'woocommerce_currency' );
			$secondary_currencies = array_diff( $currencies, array( $default_currency ) );

			try {
				$rates = $service->get_rates( $default_currency, $secondary_currencies );
			} catch ( Exception $e ) {
				if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
					error_log( "Exchange rates update error (" . $this->settings['service'] . "): " . $e->getMessage() );
				}
				throw new Exception( $e->getMessage() );
			}

			$this->apply_lifting_charge( $rates );

			foreach ( $rates as $to => $rate ) {
				if ( $rate && is_numeric( $rate ) ) {
					$this->save_exchage_rate( $to, $rate );
				}
			}
		}

		$this->settings['last_updated'] = current_time( 'timestamp' );
		$this->save_settings();

		return $rates;
	}

	public function apply_lifting_charge( &$rates ) {
		foreach ( $rates as $k => $rate ) {
			$rates[ $k ] = round( $rate * ( 1 + $this->settings['lifting_charge'] / 100 ), self::DIGITS_AFTER_DECIMAL_POINT );
		}
	}

	/**
	 * @param string $currency
	 * @param string $rate
	 */
	private function save_exchage_rate( $currency, $rate ) {
		$this->woocommerce_wpml->settings['currency_options'][ $currency ]['previous_rate'] =
			$this->woocommerce_wpml->settings['currency_options'][ $currency ]['rate'];
		$this->woocommerce_wpml->settings['currency_options'][ $currency ]['rate']          = $rate;
		$this->woocommerce_wpml->update_settings();
	}

	/**
	 * @param $currency
	 *
	 * @return mixed
	 */
	public function get_currency_rate( $currency ) {
		return $this->woocommerce_wpml->settings['currency_options'][ $currency ]['rate'];
	}

	/**
	 * @param array $post_data
	 */
	public function update_exchange_rate_options( $post_data ) {

		if ( isset( $post_data['exchange-rates-automatic'] ) && $post_data['exchange-rates-automatic'] ) {

			$this->settings['automatic'] = (int) $post_data['exchange-rates-automatic'];

			if ( isset( $post_data['exchange-rates-service'] ) ) {

				// clear errors for replaced service
				if ( isset( $this->services[ $this->settings['service'] ] ) && $post_data['exchange-rates-service'] !== $this->settings['service'] ) {
					$this->services[ $this->settings['service'] ]->clear_last_error();
				}

				$this->settings['service'] = sanitize_text_field( $post_data['exchange-rates-service'] );

			}

			if ( isset( $post_data['services'] ) ) {

				foreach ( $post_data['services'] as $service_id => $service_data ) {
					if( isset( $service_data['api-key'] ) ){
						$this->services[ $service_id ]->save_setting( 'api-key', $service_data['api-key'] );
					}

				}

			}

			$this->settings['lifting_charge'] = is_numeric( $post_data['lifting_charge'] ) ? $post_data['lifting_charge'] : 0;

			if ( isset( $post_data['update-schedule'] ) ) {
				$this->settings['schedule'] = sanitize_text_field( $post_data['update-schedule'] );
			}

			if ( isset( $post_data['update-time'] ) ) {
				$this->settings['time'] = sanitize_text_field( $post_data['update-time'] );
			}

			if ( isset( $post_data['update-weekly-day'] ) ) {
				$this->settings['week_day'] = sanitize_text_field( $post_data['update-weekly-day'] );
			}

			if ( isset( $post_data['update-monthly-day'] ) ) {
				$this->settings['month_day'] = sanitize_text_field( $post_data['update-monthly-day'] );
			}

			if ( $this->settings['schedule'] === 'manual' ) {
				$this->delete_update_cronjob();
			} else {
				$this->enable_update_cronjob();
			}

		} else {
			$this->settings['automatic'] = 0;
			$this->delete_update_cronjob();
		}

		$this->save_settings();

	}

	public function enable_update_cronjob() {
		$schedule = wp_get_schedule( self::CRONJOB_EVENT );

		if ( $schedule !== $this->settings['schedule'] ) {
			$this->delete_update_cronjob();
		}

		if ( 'monthly' === $this->settings['schedule'] ) {
			$time_offset = $this->get_monthly_schedule_time_offset();
			$schedule    = 'wcml_' . $this->settings['schedule'] . '_on_' . $this->settings['month_day'];
		} elseif ( 'weekly' === $this->settings['schedule'] ) {
			$time_offset = $this->get_weekly_schedule_time_offset();
			$schedule    = 'wcml_' . $this->settings['schedule'] . '_on_' . $this->settings['week_day'];

		} else {
			$time_offset = time();
			$schedule    = $this->settings['schedule'];
		}

		if ( ! wp_next_scheduled( self::CRONJOB_EVENT ) ) {
			wp_schedule_event( $time_offset, $schedule, self::CRONJOB_EVENT );
		}

	}

	/**
	 * @return int
	 */
	private function get_monthly_schedule_time_offset(){
		$current_day           = date( 'j' );
		$days_in_current_month = cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) );

		if ( $this->settings['month_day'] >= $current_day && $this->settings['month_day'] <= $days_in_current_month ) {
			$days = $this->settings['month_day'] - $current_day;
		} else {
			$days = $days_in_current_month - $current_day + $this->settings['month_day'];
		}

		$time_offset = time() + $days * DAY_IN_SECONDS;

		return $time_offset;
	}

	/**
	 * @return int
	 */
	private function get_weekly_schedule_time_offset(){
		$current_day = date( 'w' );
		if ( $this->settings['week_day'] >= $current_day ) {
			$days = $this->settings['week_day'] - $current_day;
		} else {
			$days = 7 - $current_day + $this->settings['week_day'];
		}

		$time_offset = time() + $days * DAY_IN_SECONDS;

		return $time_offset;
	}

	public function delete_update_cronjob() {
		wp_clear_scheduled_hook( self::CRONJOB_EVENT );
	}

	/**
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function cron_schedules( $schedules ) {

		if ( 'monthly' === $this->settings['schedule'] ) {

			$month_day = $this->get_month_day_formatted();
			$current_month         = date( 'n' );
			$days_in_current_month = cal_days_in_month( CAL_GREGORIAN, $current_month, date( 'Y' ) );
			if ( $this->settings['month_day'] <= $days_in_current_month && $this->settings['month_day'] >= date( 'j' ) ) {
				$interval = DAY_IN_SECONDS * $days_in_current_month;
			} else {
				$month_number = 12 === (int)$current_month ? 1 : $current_month + 1;
				$year_number  = 12 === (int)$current_month? date( 'Y' ) + 1 : date( 'Y' );
				$interval     = DAY_IN_SECONDS * cal_days_in_month( CAL_GREGORIAN, $month_number, $year_number );
			}

			$schedules[ 'wcml_monthly_on_' . $this->settings['month_day'] ] = array(
				'interval' => $interval,
				'display'  => sprintf( __( 'Monthly on the %s', 'woocommerce-multilingual' ), $month_day ),
			);

		} elseif ( 'weekly' === $this->settings['schedule'] ) {

			$week_day  = $this->wp_locale->get_weekday( $this->settings['week_day'] );
			$schedules[ 'wcml_weekly_on_' . $this->settings['week_day'] ] = array(
				'interval' => WEEK_IN_SECONDS,
				'display'  => sprintf( __( 'Weekly on %s', 'woocommerce-multilingual' ), $week_day ),
			);

		}

		return $schedules;
	}

	private function get_month_day_formatted(){
		$month_day = $this->settings['month_day'];
		switch ( $month_day ) {
			case 1:
				$month_day .= 'st';
				break;
			case 2:
				$month_day .= 'nd';
				break;
			case 3:
				$month_day .= 'rd';
				break;
			default:
				$month_day .= 'th';
				break;
		}
		return $month_day;
	}

}