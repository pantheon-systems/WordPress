<?php

class WPML_Inactive_Content_Render extends WPML_Twig_Template_Loader {

	const TEMPLATE = 'inactive-content.twig';

	/** @var WPML_Inactive_Content $inactive_content */
	private $inactive_content;

	public function __construct( WPML_Inactive_Content $inactive_content, array $paths ) {
		$this->inactive_content = $inactive_content;
		parent::__construct( $paths );
	}

	public function render() {
		$model = array(
			'content' => $this->inactive_content,
			'strings' => array(
				'title'    => __( "There is content in the following languages but it won't be visible on the site because those languages are not activated.", 'sitepress' ),
				'language' => __( 'Language', 'sitepress' ),
				'total'    => __( 'Total', 'sitepress' ),
			),
		);

		return $this->get_template()->show( $model, self::TEMPLATE );
	}
}
