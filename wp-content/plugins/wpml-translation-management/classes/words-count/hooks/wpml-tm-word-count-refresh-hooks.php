<?php

class WPML_TM_Word_Count_Refresh_Hooks implements IWPML_Action {

	/** @var WPML_TM_Word_Count_Single_Process_Factory $single_process_factory */
	private $single_process_factory;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var WPML_ST_Package_Factory|null $st_package_factory */
	private $st_package_factory;

	/** @var array $packages_to_refresh */
	private $packages_to_refresh = array();

	public function __construct(
		WPML_TM_Word_Count_Single_Process_Factory $single_process_factory,
		WPML_Translation_Element_Factory $element_factory,
		WPML_ST_Package_Factory $st_package_factory = null
	) {
		$this->single_process_factory = $single_process_factory;
		$this->element_factory        = $element_factory;
		$this->st_package_factory     = $st_package_factory;
	}

	public function add_hooks() {
		add_action( 'save_post', array( $this, 'refresh_post_word_count' ) );

		if ( $this->st_package_factory ) {
			add_action( 'wpml_register_string', array( $this, 'register_string_action' ), 10, 3 );
			add_action( 'wpml_set_translated_strings', array( $this, 'set_translated_strings_action' ), 10, 2 );
			add_action( 'wpml_delete_unused_package_strings', array( $this, 'delete_unused_package_strings_action' ) );
		}
	}

	/** @param int $post_id */
	public function refresh_post_word_count( $post_id ) {
		$post_element = $this->element_factory->create( $post_id, 'post' );

		if ( ! $post_element->is_translatable() ) {
			return;
		}

		if ( $post_element->get_source_element() ) {
			$post_id = $post_element->get_source_element()->get_id();
		}

		$this->single_process_factory->create()->process( 'post', $post_id );
	}

	/**
	 * @param string $string_value
	 * @param string $string_name
	 * @param array  $package
	 */
	public function register_string_action( $string_value, $string_name, $package ) {
		$this->defer_package_refresh( $package );
	}

	/** @param array $package */
	public function delete_unused_package_strings_action( $package ) {
		$this->defer_package_refresh( $package );
	}

	/**
	 * @param array $translations
	 * @param array $package
	 */
	public function set_translated_strings_action( $translations, $package ) {
		$this->defer_package_refresh( $package );
	}

	/** @param array $package_array */
	private function defer_package_refresh( $package_array ) {
		$st_package = $this->st_package_factory->create( $package_array );

		if ( $st_package->ID && ! $st_package->post_id && ! in_array( $st_package->ID, $this->packages_to_refresh, true ) ) {
			$this->packages_to_refresh[] = $st_package->ID;

			if ( ! has_action( 'shutdown', array( $this, 'refresh_packages_word_count' ) ) ) {
				add_action( 'shutdown', array( $this, 'refresh_packages_word_count' ) );
			}
		}
	}

	public function refresh_packages_word_count() {
		foreach ( $this->packages_to_refresh as $package_to_refresh ) {
			$this->single_process_factory->create()->process( 'package', $package_to_refresh );
		}
	}
}
