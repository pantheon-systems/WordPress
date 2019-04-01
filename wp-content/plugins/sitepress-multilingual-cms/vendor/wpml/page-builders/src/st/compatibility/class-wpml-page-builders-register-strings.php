<?php

/**
 * Class WPML_Page_Builders_Register_Strings
 */
abstract class WPML_Page_Builders_Register_Strings {

	/**
	 * @var IWPML_Page_Builders_Translatable_Nodes
	 */
	private $translatable_nodes;

	/**
	 * @var IWPML_Page_Builders_Data_Settings
	 */
	protected $data_settings;

	/**
	 * @var WPML_PB_String_Registration
	 */
	private $string_registration;

	/**
	 * WPML_Page_Builders_Register_Strings constructor.
	 *
	 * @param IWPML_Page_Builders_Translatable_Nodes $translatable_nodes
	 * @param IWPML_Page_Builders_Data_Settings $data_settings
	 * @param WPML_PB_String_Registration $string_registration
	 */
	public function __construct(
		IWPML_Page_Builders_Translatable_Nodes $translatable_nodes,
		IWPML_Page_Builders_Data_Settings $data_settings,
		WPML_PB_String_Registration $string_registration ) {

		$this->data_settings = $data_settings;
		$this->translatable_nodes = $translatable_nodes;
		$this->string_registration = $string_registration;
	}

	/**
	 * @param WP_Post $post
	 * @param array $package
	 */
	public function register_strings( WP_Post $post, array $package ) {

		do_action( 'wpml_start_string_package_registration', $package );

		$data = get_post_meta( $post->ID, $this->data_settings->get_meta_field(), false );

		if ( $data ) {
			$this->register_strings_for_modules(
				$this->data_settings->convert_data_to_array( $data ),
				$package
			);
		}

		do_action( 'wpml_delete_unused_package_strings', $package );

	}

	/**
	 * @param string $node_id
	 * @param mixed $element
	 * @param array $package
	 */
	protected function register_strings_for_node( $node_id, $element, array $package ) {
		$strings = $this->translatable_nodes->get( $node_id, $element );
		foreach ( $strings as $string ) {
			$this->string_registration->register_string(
				$package['post_id'],
				$string->get_value(),
				$string->get_editor_type(),
				$string->get_title(),
				$string->get_name()
			);
		}
	}

	/**
	 * @param array $data_array
	 * @param array $package
	 */
	abstract protected function register_strings_for_modules( array $data_array, array $package );
}