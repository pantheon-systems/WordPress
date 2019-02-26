<?php
/**
 * @author OnTheGo Systems
 */
interface IWPML_Deferred_Action_Loader extends IWPML_Action_Loader_Factory {

	/**
	 * @return string
	 */
	public function get_load_action();
}
