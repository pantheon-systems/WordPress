<?php

/**
 * Class WPML_TF_Frontend_Display_Requirements
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Frontend_Display_Requirements {

	/** @var WPML_Queried_Object $queried_object */
	private $queried_object;

	/** @var WPML_TF_Settings $settings */
	private $settings;

	/**
	 * WPML_TF_Frontend_Display_Requirements constructor.
	 *
	 * @param WPML_Queried_Object $queried_object
	 * @param WPML_TF_Settings    $settings
	 */
	public function __construct( WPML_Queried_Object $queried_object, WPML_TF_Settings $settings ) {
		$this->queried_object = $queried_object;
		$this->settings       = $settings;
	}

	/**
	 * @return bool
	 */
	public function verify() {
		return $this->is_enabled_on_frontend()
		       && $this->is_translation()
		       && $this->is_allowed_language()
		       && $this->is_not_expired();
	}

	/**
	 * @return bool
	 */
	private function is_enabled_on_frontend() {
		return $this->settings->is_enabled()
		       && $this->settings->get_button_mode() !== WPML_TF_Settings::BUTTON_MODE_DISABLED;
	}

	/**
	 * @return bool
	 */
	private function is_translation() {
		return (bool) $this->queried_object->get_source_language_code();
	}

	/**
	 * @return bool
	 */
	private function is_allowed_language() {
		return is_array( $this->settings->get_languages_to() )
		       && in_array( $this->queried_object->get_language_code(), $this->settings->get_languages_to(), true );
	}

	/**
	 * @return bool
	 */
	private function is_not_expired() {
		$is_expired = false;

		if ( $this->settings->get_display_mode() === WPML_TF_Settings::DISPLAY_CUSTOM
		     && $this->queried_object->is_post()
		) {
			$post = get_post( $this->queried_object->get_id() );
			$now  = strtotime( 'now' );

			if ( $this->settings->get_expiration_mode() === WPML_TF_Settings::EXPIRATION_ON_PUBLISH_ONLY ) {
				$post_date = strtotime( $post->post_date );
			} else {
				$post_date = max( strtotime( $post->post_date ), strtotime( $post->post_modified ) );
			}

			$post_age_in_days = ( $now - $post_date ) / DAY_IN_SECONDS;

			if ( $post_age_in_days > $this->settings->get_expiration_delay_in_days() ) {
				$is_expired = true;
			}
		}

		return ! $is_expired;
	}
}
