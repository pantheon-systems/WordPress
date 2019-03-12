<?php

class OTGS_Installer_Instances {

	private $instances;

	/**
	 * @var OTGS_Installer_Instance[]
	 */
	private $instances_obj = array();

	public function __construct( $instances ) {
		$this->instances = $instances;
	}

	public function get() {
		if ( ! $this->instances_obj ) {
			foreach( $this->instances as $instance ) {
				$instance_obj = new OTGS_Installer_Instance();
				$instance_obj->set_bootfile( $instance['bootfile'] )
					->set_high_priority( isset( $instance['high_priority'] ) && $instance['high_priority'] )
					->set_version( $instance['version'] )
					->set_delegated( isset( $instance['delegated'] ) && $instance['delegated'] );

				$this->instances_obj[] = $instance_obj;

			}
		}

		return $this->instances_obj;
	}
}