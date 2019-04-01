<?php

class WPML_TM_Default_Settings implements IWPML_Action {

	/** @var TranslationManagement */
	private $tm;

	public function __construct( TranslationManagement $tm ) {
		$this->tm = $tm;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'init_action' ), $this->tm->get_init_priority() );
	}

	public function init_action() {
		$this->maybe_update_notification( 'new-job', WPML_TM_Emails_Settings::NOTIFY_IMMEDIATELY );
		$this->maybe_update_notification( 'include_xliff', (int) apply_filters( 'wpml_setting', 0, 'include_xliff_in_notification' ) );

		if ( ! $this->has_notification( WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY ) ) {
			if ( $this->has_notification( 'completed' ) ) {
				$this->update_notification( WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY, $this->get_notification( 'completed') );
			} else {
				$this->update_notification( WPML_TM_Emails_Settings::COMPLETED_JOB_FREQUENCY, WPML_TM_Emails_Settings::NOTIFY_WEEKLY );
			}
		}

		$this->maybe_update_notification( 'completed', WPML_TM_Emails_Settings::NOTIFY_IMMEDIATELY );
		$this->maybe_update_notification( 'resigned', WPML_TM_Emails_Settings::NOTIFY_IMMEDIATELY );
		$this->maybe_update_notification( 'overdue', WPML_TM_Emails_Settings::NOTIFY_DAILY );
		$this->maybe_update_notification( 'overdue_offset', 7 );
		$this->maybe_update_notification( 'dashboard', true );
		$this->maybe_update_notification( 'purge-old', 7 );
	}

	/**
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return bool
	 */
	private function get_notification( $key, $default = null ) {
		return isset( $this->tm->settings['notification'][ $key ] )
			? $this->tm->settings['notification'][ $key ]
			: $default;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function has_notification( $key ) {
		return isset( $this->tm->settings['notification'][ $key ] );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	private function maybe_update_notification( $key, $value ) {
		if ( ! $this->has_notification( $key ) ) {
			$this->update_notification( $key, $value );
		}
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	private function update_notification( $key, $value ) {
		$this->tm->settings['notification'][ $key ] = $value;
	}

}