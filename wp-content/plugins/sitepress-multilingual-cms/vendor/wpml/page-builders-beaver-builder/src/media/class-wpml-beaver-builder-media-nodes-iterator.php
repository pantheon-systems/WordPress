<?php

class WPML_Beaver_Builder_Media_Nodes_Iterator implements IWPML_PB_Media_Nodes_Iterator {

	/** @var WPML_Beaver_Builder_Media_Node_Provider $node_provider */
	private $node_provider;

	public function __construct( WPML_Beaver_Builder_Media_Node_Provider $node_provider ) {
		$this->node_provider = $node_provider;
	}

	/**
	 * @param array $data_array
	 * @param string $lang
	 * @param string $source_lang
	 *
	 * @return array
	 */
	public function translate( $data_array, $lang, $source_lang ) {
		foreach ( $data_array as &$data ) {
			if ( is_array( $data ) ) {
				$data = $this->translate( $data, $lang, $source_lang );
			} elseif ( is_object( $data )
			           && isset( $data->type ) && 'module' === $data->type
			           && isset( $data->settings->type )
			) {
				$data->settings = $this->translate_node( $data->settings, $lang, $source_lang );
			}
		}

		return $data_array;
	}

	/**
	 * @param stdClass $settings
	 * @param string $lang
	 * @param string $source_lang
	 *
	 * @return stdClass
	 */
	private function translate_node( $settings, $lang, $source_lang ) {
		$node = $this->node_provider->get( $settings->type );

		if ( $node ) {
			$settings = $node->translate( $settings, $lang, $source_lang );
		}

		return $settings;
	}
}
