<?php
/**
 * Class: Utility Class
 *
 * @since 1.0.0
 * @package report-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Rep_Util_M
 *
 * @package report-wsal
 */
class WSAL_Rep_Util_M extends WSAL_Models_Meta {

	/**
	 * Returns Meta Table name.
	 *
	 * @return string
	 */
	public function GetTableName() {
		return $this->getConnector()->getAdapter( 'Meta' )->GetTable();
	}
}
