<?php

class WPML_ST_Slug_Translation_Strings_Sync implements IWPML_Action {

	/** @var WPML_Slug_Translation_Records_Factory $slug_records_factory */
	private $slug_records_factory;

	/** @var WPML_ST_Slug_Translation_Settings_Factory $slug_settings_factory */
	private $slug_settings_factory;

	public function __construct(
		WPML_Slug_Translation_Records_Factory $slug_records_factory,
		WPML_ST_Slug_Translation_Settings_Factory $slug_settings_factory
	) {
		$this->slug_records_factory  = $slug_records_factory;
		$this->slug_settings_factory = $slug_settings_factory;
	}

	public function add_hooks() {
		add_action( 'registered_taxonomy', array( $this, 'run_taxonomy_sync' ), 10, 3 );
		add_action( 'registered_post_type', array( $this, 'run_post_type_sync' ), 10, 2 );
	}

	/**
	 * @param string       $taxonomy
	 * @param string|array $object_type
	 * @param array        $taxonomy_array
	 */
	public function run_taxonomy_sync( $taxonomy, $object_type, $taxonomy_array ) {
		if ( isset( $taxonomy_array['rewrite']['slug'] ) && $taxonomy_array['rewrite']['slug'] ) {
			$this->sync_element_slug( $taxonomy_array['rewrite']['slug'], $taxonomy, WPML_Slug_Translation_Factory::TAX );
		}
	}

	/**
	 * @param string       $post_type
	 * @param WP_Post_Type $post_type_object
	 */
	public function run_post_type_sync( $post_type, $post_type_object ) {
		if ( isset( $post_type_object->rewrite['slug'] ) && $post_type_object->rewrite['slug'] ) {
			$this->sync_element_slug( trim( $post_type_object->rewrite['slug'], '/' ), $post_type, WPML_Slug_Translation_Factory::POST );
		}
	}

	/**
	 * @param string $rewrite_slug
	 * @param string $type
	 * @param string $element_type
	 */
	public function sync_element_slug( $rewrite_slug, $type, $element_type ) {
		$settings = $this->slug_settings_factory->create( $element_type );

		if ( ! $settings->is_translated( $type ) ) {
			return;
		}

		$records = $this->slug_records_factory->create( $element_type );
		$slug    = $records->get_slug( $type );

		if ( $slug->get_original_value() !== $rewrite_slug ) {
			$records->update_original_slug( $type, $rewrite_slug );
		}
	}
}