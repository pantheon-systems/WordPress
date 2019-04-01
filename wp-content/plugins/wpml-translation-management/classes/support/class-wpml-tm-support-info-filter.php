<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Support_Info_Filter {
	/** @var WPML_TM_Support_Info */
	private $support_info;

	function __construct( WPML_TM_Support_Info $support_info ) {
		$this->support_info = $support_info;
	}

	/**
	 * @param array $blocks
	 *
	 * @return array
	 */
	public function filter_blocks( array $blocks ) {

		$is_simplexml_extension_loaded      = $this->support_info->is_simplexml_extension_loaded();
		$blocks['php']['data']['simplexml'] = array(
			'label'      => __( 'SimpleXML extension', 'wpml-translation-management' ),
			'value'      => $is_simplexml_extension_loaded ? __( 'Loaded', 'wpml-translation-management' ) : __( 'Not loaded', 'wpml-translation-management' ),
			'url'        => 'http://php.net/manual/book.simplexml.php',
			'messages'   => array(
				__( 'SimpleXML extension is required for using XLIFF files in WPML Translation Management.', 'wpml-translation-management' ) => 'https://wpml.org/home/minimum-requirements/',
			),
			'is_warning' => ! $is_simplexml_extension_loaded,
		);

		return $blocks;
	}
}
