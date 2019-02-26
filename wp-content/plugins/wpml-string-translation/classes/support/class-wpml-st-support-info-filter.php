<?php
/**
 * @author OnTheGo Systems
 */
class WPML_ST_Support_Info_Filter {
	/** @var WPML_ST_Support_Info */
	private $support_info;

	function __construct( WPML_ST_Support_Info $support_info ) {
		$this->support_info     = $support_info;
	}

	/**
	 * @param array $blocks
	 *
	 * @return array
	 */
	public function filter_blocks(array $blocks) {

		$is_mbstring_extension_loaded      = $this->support_info->is_mbstring_extension_loaded();
		$blocks['php']['data']['mbstring'] = array(
			'label'    => __( 'Multibyte String extension', 'wpml-string-translation' ),
			'value'    => $is_mbstring_extension_loaded ? __( 'Loaded', 'wpml-string-translation' ) : __( 'Not loaded', 'wpml-string-translation' ),
			'url'      => 'http://php.net/manual/book.mbstring.php',
			'messages' => array(
				__( 'Multibyte String extension is required for WPML String Translation.', 'wpml-string-translation' ) => 'https://wpml.org/home/minimum-requirements/',
			),
			'is_error' => ! $is_mbstring_extension_loaded,
		);

		return $blocks;
	}
}
