<?php

class OTGS_Installer_Package_Product {

	private $id;
	private $name;
	private $description;
	private $price;
	private $subscription_type;
	private $subscription_type_text;
	private $subscription_info;
	private $subscription_type_equivalent;
	private $url;
	private $renewals;
	private $upgrades;
	private $plugins;
	private $downloads;

	public function __construct( array $params = array() ) {
		foreach ( get_object_vars( $this ) as $property => $value ) {
			if ( array_key_exists( $property, $params ) ) {
				$this->$property = $params[ $property ];
			}
		}
	}

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_price() {
		return $this->price;
	}

	public function get_subscription_type() {
		return $this->subscription_type;
	}

	public function get_subscription_type_text() {
		return $this->subscription_type_text;
	}

	public function get_subscription_info() {
		return $this->subscription_info;
	}

	public function get_subscription_type_equivalent() {
		return (int) $this->subscription_type_equivalent;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_renewals() {
		return $this->renewals;
	}

	public function get_upgrades() {
		return $this->upgrades;
	}

	public function get_plugins() {
		return $this->plugins;
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function is_plugin_registered( $slug ) {
		foreach ( $this->plugins as $plugin ) {
			if ( $slug === $plugin ) {
				return true;
			}
		}

		return false;
	}

	public function get_downloads() {
		return $this->downloads;
	}
}