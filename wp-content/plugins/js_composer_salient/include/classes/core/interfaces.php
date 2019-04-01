<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.3
 * Interface for editors
 */
interface Vc_Editor_Interface {
	/**
	 * @since 4.3
	 * @return mixed
	 */
	public function renderEditor();
}

/**
 * @since 4.3
 * Default render interface
 */
interface Vc_Render {
	/**
	 * @since 4.3
	 * @return mixed
	 */
	public function render();
}

/**
 * @since 4.3
 * Interface for third-party plugins classes loader.
 */
interface Vc_Vendor_Interface {
	/**
	 * @since 4.3
	 * @return mixed
	 */
	public function load();
}
