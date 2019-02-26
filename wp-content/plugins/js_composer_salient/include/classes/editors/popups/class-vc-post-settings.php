<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Post settings like custom css for page are displayed here.
 *
 * @since   4.3
 */
class Vc_Post_Settings {
	/**
	 * @var Vc_Editor_Interface
	 */
	protected $editor;

	/**
	 * @param Vc_Editor_Interface $editor
	 */
	public function __construct( Vc_Editor_Interface $editor ) {
		$this->editor = $editor;
	}

	/**
	 * @return Vc_Editor_Interface
	 */
	public function editor() {
		return $this->editor;
	}

	/**
	 *
	 */
	public function renderUITemplate() {
		vc_include_template( 'editors/popups/vc_ui-panel-post-settings.tpl.php', array(
			'box' => $this,
		) );
	}
}
