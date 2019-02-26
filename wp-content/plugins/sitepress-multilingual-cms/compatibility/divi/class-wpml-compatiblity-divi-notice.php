<?php

class WPML_Compatibility_Divi_Notice extends WPML_Notice {

	const ID = 'wpml-compatibility-divi-editor-warning';
	const GROUP = 'wpml-compatibility-divi';

	public function __construct() {
		parent::__construct( self::ID, $this->get_message(), self::GROUP );
		$this->set_dismissible( true );
		$this->set_css_class_types( 'warning' );
	}

	/**
	 * @return string
	 */
	private function get_message() {
		$msg = esc_html_x(
			'You are using DIVI theme, and you have chosen to use the standard editor for translating content.',
			'Use Translation Editor notice 1/3',
			'sitepress'
		);

		$msg .= ' ' . esc_html_x(
			'Some functionalities may not work properly. We encourage you to switch to use the Translation Editor.',
			'Use Translation Editor notice 2/3',
			'sitepress'
		);

		$msg .= ' ' . sprintf(
			esc_html_x(
				'You can find more information here: %s',
				'Use Translation Editor notice 2/3',
				'sitepress'
			),
			'<a href="https://wpml.org/errata/some-internal-taxonomies-will-be-missing-when-you-translate-divi-layouts/">Some internal taxonomies will be missing when you translate Divi layouts</a>'
		);

		return $msg;
	}
}
