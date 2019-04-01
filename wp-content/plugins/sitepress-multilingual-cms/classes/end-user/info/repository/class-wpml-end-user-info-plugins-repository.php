<?php

class WPML_End_User_Info_Plugins_Repository implements WPML_End_User_Info_Repository {

	const ONTHEGOSYSTEMS = 'OnTheGoSystems';

	/** @var  WPML_Active_Plugin_Provider */
	private $plugins_repository;

	/**
	 * @param WPML_Active_Plugin_Provider $plugins_repository
	 */
	public function __construct( WPML_Active_Plugin_Provider $plugins_repository ) {
		$this->plugins_repository = $plugins_repository;
	}

	/**
	 * @return WPML_End_User_Info_Plugin_List
	 * @throws InvalidArgumentException
	 */
	public function get_data() {
		$list = $this->plugins_repository->get_active_plugins();
		$list = array_filter( $list, array( $this, 'is_not_onthegosystems_plugin' ) );

		$result = array();

		foreach ( $list as $row ) {
			$result[] = new WPML_End_User_Info_Plugin_Data(
				$row['Name'],
				array_key_exists( 'Author', $row ) ? $row['Author'] : '',
				array_key_exists( 'PluginURI', $row ) ? $row['PluginURI'] : ''
			);
		}

		return new WPML_End_User_Info_Plugin_List( $result );
	}

	/**
	 * @return string
	 */
	public function get_data_id() {
		return 'plugins';
	}

	/**
	 * @param array $row
	 *
	 * @return bool
	 */
	private function is_not_onthegosystems_plugin( array $row ) {
		return array_key_exists( 'Author', $row ) && self::ONTHEGOSYSTEMS !== $row['Author'];
	}
}
