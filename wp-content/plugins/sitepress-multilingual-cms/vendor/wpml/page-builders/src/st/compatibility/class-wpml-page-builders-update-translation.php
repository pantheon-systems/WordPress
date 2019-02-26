<?php

/**
 * Class WPML_Page_Builders_Update_Translation
 */
abstract class WPML_Page_Builders_Update_Translation extends WPML_Page_Builders_Update {

	const TRANSLATION_COMPLETE = 10;

	/**
	 * @var IWPML_Page_Builders_Translatable_Nodes
	 */
	protected $translatable_nodes;

	private $string_translations;
	private $lang;

	public function __construct(
		IWPML_Page_Builders_Translatable_Nodes $translatable_nodes,
		IWPML_Page_Builders_Data_Settings $data_settings
	) {
		$this->translatable_nodes = $translatable_nodes;
		parent::__construct( $data_settings );
	}

	/**
	 * @param int $translated_post_id
	 * @param $original_post
	 * @param $string_translations
	 * @param string $lang
	 */
	public function update( $translated_post_id, $original_post, $string_translations, $lang ) {
		$this->string_translations = $string_translations;
		$this->lang                = $lang;

		$converted_data = $this->get_converted_data( $original_post->ID );
		$this->update_strings_in_modules( $converted_data );
		$this->save( $translated_post_id, $original_post->ID, $converted_data );

	}

	/**
	 * @param WPML_PB_String $string
	 *
	 * @return WPML_PB_String
	 */
	protected function get_translation( WPML_PB_String $string ) {
		if ( array_key_exists( $string->get_name(), $this->string_translations ) &&
		     array_key_exists( $this->lang, $this->string_translations[ $string->get_name() ] ) ) {
			$translation = $this->string_translations[ $string->get_name() ][ $this->lang ];
			if ( (int) $translation['status'] === self::TRANSLATION_COMPLETE ) {
				$string->set_value( $translation['value'] );
			}
		}

		return $string;
	}

	abstract protected function update_strings_in_modules( array &$data_array );
	abstract protected function update_strings_in_node( $node_id, $settings );
}