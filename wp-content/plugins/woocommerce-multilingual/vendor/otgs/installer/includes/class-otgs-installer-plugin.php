<?php

class OTGS_Installer_Plugin {

	private $name;
	private $slug;
	private $description;
	private $changelog;
	private $version;
	private $date;
	private $url;
	private $free_on_wporg;
	private $fallback_on_wporg;
	private $basename;
	private $external_repo;
	private $is_lite;
	private $repo;
	private $id;
	private $installed_version;
	private $channel;

	public function __construct( array $params = array() ) {
		foreach ( get_object_vars( $this ) as $property => $value ) {
			if ( array_key_exists( $property, $params ) ) {
				$this->$property = $params[ $property ];
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function get_changelog() {
		return $this->changelog;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function get_repo() {
		return $this->repo;
	}

	/**
	 * @return bool
	 */
	public function is_free_on_wporg() {
		return (bool) $this->free_on_wporg;
	}

	/**
	 * @return bool
	 */
	public function has_fallback_on_wporg() {
		return (bool) $this->fallback_on_wporg;
	}

	/**
	 * @return string
	 */
	public function get_basename() {
		return $this->basename;
	}

	/**
	 * @return string
	 */
	public function get_external_repo() {
		return $this->external_repo;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_installed_version() {
		return $this->installed_version;
	}

	/**
	 * @return string
	 */
	public function get_channel() {
		return $this->channel;
	}

	/**
	 * @return string
	 */
	public function is_lite() {
		return $this->is_lite;
	}
}