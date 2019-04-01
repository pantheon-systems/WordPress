<?php

class WPML_Page_Builders_Media_Shortcodes_Update_Factory implements IWPML_PB_Media_Update_Factory {

	/** @var WPML_PB_Config_Import_Shortcode WPML_PB_Config_Import_Shortcode */
	private $page_builder_config_import;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var WPML_Page_Builders_Media_Translate $media_translate */
	private $media_translate;

	public function __construct( WPML_PB_Config_Import_Shortcode $page_builder_config_import ) {
		$this->page_builder_config_import = $page_builder_config_import;
	}

	public function create() {
		$media_shortcodes = new WPML_Page_Builders_Media_Shortcodes(
			$this->get_media_translate(),
			$this->page_builder_config_import->get_media_settings()
		);

		return new WPML_Page_Builders_Media_Shortcodes_Update(
			$this->get_element_factory(),
			$media_shortcodes,
			new WPML_Page_Builders_Media_Usage( $this->get_media_translate(), new WPML_Media_Usage_Factory() )
		);
	}

	/** @return WPML_Translation_Element_Factory */
	private function get_element_factory() {
		global $sitepress;

		if ( ! $this->element_factory ) {
			$this->element_factory = new WPML_Translation_Element_Factory( $sitepress );
		}

		return $this->element_factory;
	}

	/** @return WPML_Page_Builders_Media_Translate */
	private function get_media_translate() {
		global $sitepress;

		if ( ! $this->media_translate ) {
			$this->media_translate = new WPML_Page_Builders_Media_Translate(
				$this->get_element_factory(),
				new WPML_Media_Image_Translate( $sitepress, new WPML_Media_Attachment_By_URL_Factory() )
			);
		}

		return $this->media_translate;
	}
}
