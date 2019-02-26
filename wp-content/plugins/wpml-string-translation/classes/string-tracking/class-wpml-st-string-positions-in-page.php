<?php

class WPML_ST_String_Positions_In_Page extends WPML_ST_String_Positions {

	const KIND = ICL_STRING_TRANSLATION_STRING_TRACKING_TYPE_PAGE;
	const TEMPLATE = 'positions-in-page.twig';

	/** @var WPML_ST_String_Factory $string_factory */
	private $string_factory;

	public function __construct(
		WPML_ST_String_Factory $string_factory,
		WPML_ST_DB_Mappers_String_Positions $string_position_mapper,
		IWPML_Template_Service $template_service
	) {
		$this->string_factory = $string_factory;
		parent::__construct( $string_position_mapper, $template_service );
	}

	protected function get_model( $string_id ) {
		return array(
			'pages' => $this->get_pages( $string_id ),
		);
	}

	protected function get_template_name() {
		return self::TEMPLATE;
	}

	private function get_pages( $string_id ) {
		$pages   = array();

		$string  = $this->string_factory->find_by_id( $string_id );
		$value   = $string->get_value();
		$context = $string->get_context();

		$urls = $this->get_mapper()->get_positions_by_string_and_kind( $string_id, self::KIND );

		foreach ( $urls as $url ) {
			$pages[] = array(
				'iframe_url' => add_query_arg(
					array(
						'icl_string_track_value'   => $value,
						'icl_string_track_context' => $context,
					),
					$url
				),
				'url' => $url,
			);
		}

		return $pages;
	}
}