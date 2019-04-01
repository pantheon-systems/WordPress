<?php

/**
 * Class WPML_TF_WP_Cron_Events
 *
 * @author OnTheGoSystems
 */
class WPML_TF_WP_Cron_Events implements IWPML_Action {

	const SYNCHRONIZE_RATINGS_EVENT = 'wpml_tf_synchronize_ratings_event';

	/** @var WPML_TF_Settings_Read $settings_read */
	private $settings_read;

	/** @var WPML_TF_Settings $settings */
	private $settings;

	/** @var WPML_TF_TP_Ratings_Synchronize_Factory $ratings_synchronize_factory */
	private $ratings_synchronize_factory;

	/**
	 * WPML_TF_WP_Cron_Events constructor.
	 *
	 * @param WPML_TF_Settings_Read                  $settings_read
	 * @param WPML_TF_TP_Ratings_Synchronize_Factory $ratings_synchronize_factory
	 */
	public function __construct(
		WPML_TF_Settings_Read $settings_read,
		WPML_TF_TP_Ratings_Synchronize_Factory $ratings_synchronize_factory
	) {
		$this->settings_read               = $settings_read;
		$this->ratings_synchronize_factory = $ratings_synchronize_factory;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'init_action' ) );
		add_action( self::SYNCHRONIZE_RATINGS_EVENT, array( $this, 'synchronize_ratings' ) );
	}

	public function init_action() {
		if ( $this->get_settings()->is_enabled() ) {
			$this->add_synchronize_ratings_event();
		} else {
			$this->remove_synchronize_ratings_event();
		}
	}

	private function add_synchronize_ratings_event() {
		if ( ! wp_next_scheduled( self::SYNCHRONIZE_RATINGS_EVENT ) ) {
			wp_schedule_event( time(), 'twicedaily', self::SYNCHRONIZE_RATINGS_EVENT );
		}
	}

	private function remove_synchronize_ratings_event() {
		$timestamp = wp_next_scheduled( self::SYNCHRONIZE_RATINGS_EVENT );
		wp_unschedule_event( $timestamp, self::SYNCHRONIZE_RATINGS_EVENT );
	}

	public function synchronize_ratings() {
		$ratings_synchronize = $this->ratings_synchronize_factory->create();
		$ratings_synchronize->run();
	}

	/** @return WPML_TF_Settings */
	private function get_settings() {
		if ( ! $this->settings ) {
			$this->settings = $this->settings_read->get( 'WPML_TF_Settings' );
		}

		return $this->settings;
	}
}
