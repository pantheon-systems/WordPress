<?php

class WPML_Element_Sync_Settings {

	/** @var array $settings */
	private $settings;

	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public function is_sync( $type ) {
		return isset( $this->settings[ $type ] ) &&
		       (
			       $this->settings[ $type ] == WPML_CONTENT_TYPE_TRANSLATE ||
			       $this->settings[ $type ] == WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED
		       );
	}

}
