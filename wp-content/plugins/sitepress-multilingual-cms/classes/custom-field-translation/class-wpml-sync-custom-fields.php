<?php

class WPML_Sync_Custom_Fields {

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	/** @var array $fields_to_sync */
	private $fields_to_sync;

	public function __construct(
		wpdb $wpdb,
		WPML_Translation_Element_Factory $element_factory,
		array $fields_to_sync
	) {
		$this->wpdb            = $wpdb;
		$this->element_factory = $element_factory;
		$this->fields_to_sync  = $fields_to_sync;
	}

	/**
	 * @param int $post_id_from
	 * @param string $meta_key
	 */
	public function sync_to_translations( $post_id_from, $meta_key ) {
		if ( in_array( $meta_key, $this->fields_to_sync ) ) {
			$post_element = $this->element_factory->create( $post_id_from, 'post' );
			$translations = $post_element->get_translations();

			foreach ( $translations as $translation ) {
				$translation_id = $translation->get_element_id();
				if ( $translation_id != $post_id_from ) {
					$this->sync_custom_field( $post_id_from, $translation_id, $meta_key );
				}
			}
		}
	}

	/**
	 * @param int $post_id_from
	 */
	public function sync_all_custom_fields( $post_id_from ) {
		foreach ( $this->fields_to_sync as $meta_key ) {
			$this->sync_to_translations( $post_id_from, $meta_key );
		}
	}

	/**
	 * @param int $post_id_from
	 * @param int $post_id_to
	 * @param string $meta_key
	 */
	public function sync_custom_field( $post_id_from, $post_id_to, $meta_key ) {
		$sql         = "SELECT meta_value FROM {$this->wpdb->postmeta} WHERE post_id=%d AND meta_key=%s";
		$values_from = $this->wpdb->get_results( $this->wpdb->prepare( $sql, array(
			$post_id_from,
			$meta_key
		) ), ARRAY_N );
		$values_to   = $this->wpdb->get_results( $this->wpdb->prepare( $sql, array(
			$post_id_to,
			$meta_key
		) ), ARRAY_N );

		if ( ! empty( $values_from ) ) {
			$values_from = call_user_func_array( 'array_merge', $values_from );
		}

		if ( ! empty( $values_to ) ) {
			$values_to = call_user_func_array( 'array_merge', $values_to );
		}

		$removed = array_diff( $values_to, $values_from );
		foreach ( $removed as $v ) {
			delete_post_meta( $post_id_to, $meta_key, maybe_unserialize( $v ) );
		}

		$added = array_diff( $values_from, $values_to );
		foreach ( $added as $v ) {
			$v = maybe_unserialize( $v );
			$v = wp_slash( $v );
			add_post_meta( $post_id_to, $meta_key, $v );
		}

		do_action( 'wpml_after_copy_custom_field', $post_id_from, $post_id_to, $meta_key );
	}

}