<?php

class WPML_End_User_Info_Plugin_List implements WPML_End_User_Info {
	/** @var  WPML_End_User_Info_Plugin_Data[] */
	private $plugins;

	/**
	 * @param WPML_End_User_Info_Plugin_Data[] $plugins
	 */
	public function __construct( array $plugins ) {
		$this->plugins = $plugins;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		$result = array();
		foreach ( $this->plugins as $plugin ) {
			$result[] = $plugin->to_array();
		}

		return $result;
	}
}
