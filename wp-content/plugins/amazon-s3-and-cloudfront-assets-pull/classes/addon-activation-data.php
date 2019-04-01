<?php

namespace DeliciousBrains\WP_Offload_Media_Assets_Pull;

class Addon_Activation_Data {

	/**
	 * @var string Addon slug/id.
	 */
	protected $addon;

	const KEY = 'as3cf_assets_addon_activation';

	/**
	 * Record activation of the Assets Pull addon.
	 */
	public static function assets_pull_activated() {
		$inst = new static( 'assets_pull' );
		$inst->activate();
	}

	/**
	 * Record deactivation of the Assets Pull addon.
	 */
	public static function assets_pull_deactivated() {
		$inst = new static( 'assets_pull' );
		$inst->deactivate();
	}

	/**
	 * Addon_Activation_Data constructor.
	 *
	 * @param string $addon
	 */
	public function __construct( $addon ) {
		$this->addon = $addon;
	}

	/**
	 * Get a saved value for the given key.
	 *
	 * @param $key
	 *
	 * @return null
	 */
	public function get( $key ) {
		$saved = self::load();

		return isset( $saved["{$this->addon}.$key"] ) ? $saved["{$this->addon}.$key"] : null;
	}

	/**
	 * Update saved data with new value(s).
	 *
	 * @param string|array $data  A string key for a single update, or array of key => values to update.
	 * @param mixed|null   $value A value to set if only updating a single key, or null if passing new data as an array.
	 */
	public function update( $data, $value = null ) {
		$saved = self::load();

		if ( ! is_array( $data ) ) {
			$data = array( $data => $value );
		}

		foreach ( $data as $key => $v ) {
			$saved["{$this->addon}.$key"] = $v;
		}

		self::save( $saved );
	}

	/**
	 * Record the activation time.
	 */
	public function activate() {
		$this->update( array(
			'activated_at'   => time(),
			'deactivated_at' => false,
		) );
	}

	/**
	 * Record the deactivation time.
	 */
	public function deactivate() {
		$this->update( array(
			'activated_at'   => false,
			'deactivated_at' => time(),
		) );
	}

	/**
	 * Check if the addon was activated within the last given number of seconds.
	 *
	 * @param $seconds
	 *
	 * @return bool
	 */
	public function activated_within( $seconds ) {
		$activation_age = time() - $this->get( 'activated_at' );

		return $activation_age <= abs( $seconds );
	}

	/**
	 * Check if the addon was deactivated within the last given number of seconds.
	 *
	 * @param $seconds
	 *
	 * @return bool
	 */
	public function deactivated_within( $seconds ) {
		$deactivation_age = time() - $this->get( 'deactivated_at' );

		return $deactivation_age <= abs( $seconds );
	}

	/**
	 * Get the saved data for all addons.
	 *
	 * @return array
	 */
	public static function load() {
		return get_site_option( self::KEY ) ?: array();
	}

	/**
	 * Update the saved data for all addons.
	 *
	 * @param array $value
	 */
	public static function save( $value ) {
		update_site_option( self::KEY, $value );
	}
}