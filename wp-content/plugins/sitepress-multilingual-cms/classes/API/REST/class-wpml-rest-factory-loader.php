<?php

/**
 * @author OnTheGo Systems
 */
abstract class WPML_REST_Factory_Loader implements IWPML_REST_Action_Loader, IWPML_Deferred_Action_Loader {
	const REST_API_INIT_ACTION = 'rest_api_init';

	/**
	 * @return string
	 */
	public function get_load_action() {
		return self::REST_API_INIT_ACTION;
	}
}