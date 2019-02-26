<?php

class WPML_TP_Lock_Notice_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		$tp_lock_factory = new WPML_TP_Lock_Factory();
		$notices         = wpml_get_admin_notices();
		return new WPML_TP_Lock_Notice( $tp_lock_factory->create(), $notices );
	}
}
