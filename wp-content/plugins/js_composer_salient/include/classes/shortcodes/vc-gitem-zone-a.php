<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-gitem-zone.php' );

class WPBakeryShortCode_VC_Gitem_Zone_A extends WPBakeryShortCode_VC_Gitem_Zone {
	public $zone_name = 'a';

	protected function getFileName() {
		return 'vc_gitem_zone';
	}
}
