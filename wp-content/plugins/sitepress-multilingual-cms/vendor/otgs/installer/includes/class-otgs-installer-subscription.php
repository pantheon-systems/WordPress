<?php

/**
 * @author OnTheGo Systems
 */
class OTGS_Installer_Subscription {
	const SUBSCRIPTION_STATUS_INACTIVE = 0;
	const SUBSCRIPTION_STATUS_ACTIVE = 1;
	const SUBSCRIPTION_STATUS_EXPIRED = 2;
	const SUBSCRIPTION_STATUS_INACTIVE_UPGRADED = 3;
	const SUBSCRIPTION_STATUS_ACTIVE_NO_EXPIRATION = 4;

	const SUBSCRIPTION_STATUS_TEXT_EXPIRED = 'expired';
	const SUBSCRIPTION_STATUS_TEXT_VALID = 'valid';
	const SUBSCRIPTION_STATUS_TEXT_MISSING = 'missing';

	private $status;
	private $expires;

	/**
	 * WPML_Installer_Subscription constructor.
	 *
	 * @param stdClass|null $data
	 */
	public function __construct( stdClass $data = null ) {
		if ( $data ) {
			if ( isset( $data->status ) ) {
				$this->status = (int) $data->status;
			}
			if ( isset( $data->expires ) ) {
				$this->expires = $data->expires;
			}
		}
	}

	public function get_subscription_status_text() {
		if ( $this->is_expired() ) {
			return self::SUBSCRIPTION_STATUS_TEXT_EXPIRED;
		}

		if ( $this->is_valid() ) {
			return self::SUBSCRIPTION_STATUS_TEXT_VALID;
		}

		return self::SUBSCRIPTION_STATUS_TEXT_MISSING;
	}

	/**
	 * @return bool
	 */
	private function is_expired() {
		return ! $this->is_lifetime()
		       && (
			       self::SUBSCRIPTION_STATUS_EXPIRED === $this->get_status()
			       || ( $this->get_expiration() && strtotime( $this->get_expiration() ) <= time() )
		       );
	}

	/**
	 * @return bool
	 */
	private function is_lifetime() {
		return $this->get_status() === self::SUBSCRIPTION_STATUS_ACTIVE_NO_EXPIRATION;
	}

	private function get_status() {
		return $this->status;
	}

	private function get_expiration() {
		return $this->expires;
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return ( $this->is_lifetime()
		         || ( $this->get_status() === self::SUBSCRIPTION_STATUS_ACTIVE && ! $this->is_expired() ) );
	}
}