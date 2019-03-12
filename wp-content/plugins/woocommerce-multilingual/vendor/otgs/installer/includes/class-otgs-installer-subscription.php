<?php

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
	private $site_key;
	private $site_url;
	private $type;
	private $registered_by;
	private $data;

	/**
	 * WPML_Installer_Subscription constructor.
	 *
	 * @param array|null $subscription
	 */
	public function __construct( $subscription = array() ) {
		if ( $subscription ) {

			if ( isset( $subscription['data'] ) ) {
				$this->data = $subscription['data'];
			}

			if ( isset( $subscription['data']->status ) ) {
				$this->status = (int) $subscription['data']->status;
			}

			if ( isset( $subscription['data']->expires ) ) {
				$this->expires = $subscription['data']->expires;
			}

			if ( isset( $subscription['key'] ) ) {
				$this->site_key = $subscription['key'];
			}

			if ( isset( $subscription['site_url'] ) ) {
				$this->site_url = $subscription['site_url'];
			}

			if ( isset( $subscription['registered_by'] ) ) {
				$this->registered_by = $subscription['registered_by'];
			}

			if ( isset( $subscription['data']->subscription_type ) ) {
				$this->type = $subscription['data']->subscription_type;
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

	public function get_site_key() {
		return $this->site_key;
	}

	public function get_site_url() {
		return $this->site_url;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_registered_by() {
		return $this->registered_by;
	}

	public function get_data() {
		return $this->data;
	}

	/**
	 * @return bool
	 */
	public function is_valid() {
		return ( $this->is_lifetime()
		         || ( $this->get_status() === self::SUBSCRIPTION_STATUS_ACTIVE && ! $this->is_expired() ) );
	}
}