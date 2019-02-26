<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );

/**
 * Add element for VC editors with a list of mapped shortcodes for gri item constructor.
 *
 * @since 4.4
 */
class Vc_Add_Element_Box_Grid_Item extends Vc_Add_Element_Box {
	/**
	 * Get shortcodes from param type vc_grid_item
	 * @return array|bool
	 */

	public function shortcodes() {
		return WpbMap_Grid_Item::getSortedGitemUserShortCodes();
	}

	/**
	 * Get categories list from mapping data.
	 * @since 4.5
	 *
	 * @return array
	 */
	public function getCategories() {
		return WpbMap_Grid_Item::getGitemUserCategories();
	}

	public function getPartState() {
		return vc_user_access()->part( 'grid_builder' )->getState();
	}
}
