<?php

class OTGS_Installer_Log_Factory {

	/**
	 * @return OTGS_Installer_Log
	 */
	public function create() {
		return new OTGS_Installer_Log();
	}
}