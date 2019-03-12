<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Administration (dashboard) functionality
 *
 * Class WC_Zapier_Admin
 */
class WC_Zapier_Admin {

	public function __construct() {

		new WC_Zapier_Admin_Pointers();
		new WC_Zapier_Admin_Feed_UI();
		new WC_Zapier_Admin_System_Status();
		new WC_Zapier_Privacy();

	}

}
