<?php

class OTGS_Installer_Subscription_Factory {

	public function create( $params = array() ) {
		return new OTGS_Installer_Subscription( $params );
	}
}