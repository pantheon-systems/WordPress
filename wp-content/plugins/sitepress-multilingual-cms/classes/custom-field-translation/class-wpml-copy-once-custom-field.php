<?php

class WPML_Copy_Once_Custom_Field implements IWPML_Action{

	/** @var SitePress $sitepress */
	private $sitepress;
	/** @var WPDB $wpdb */
	private $wpdb;
	/** @var WPML_Post_Translation $wpml_post_translation */
	private $wpml_post_translation;

	public function __construct( SitePress $sitepress, WPDB $wpdb, WPML_Post_Translation $wpml_post_translation ) {
		$this->sitepress             = $sitepress;
		$this->wpdb                  = $wpdb;
		$this->wpml_post_translation = $wpml_post_translation;
	}

	public function add_hooks() {
		add_action( 'wpml_after_save_post', array( $this, 'copy' ), 10, 1 );
		add_action( 'wpml_pro_translation_completed', array( $this, 'copy' ), 10, 1 );
	}

	public function copy( $post_id ) {

		$custom_fields_to_copy = $this->sitepress->get_custom_fields_translation_settings( WPML_COPY_ONCE_CUSTOM_FIELD );

		foreach ( $custom_fields_to_copy as $meta_key ) {
			$sql    = "SELECT meta_value FROM {$this->wpdb->postmeta} WHERE post_id=%d AND meta_key=%s";
			$values = $this->wpdb->get_results( $this->wpdb->prepare( $sql, array( $post_id, $meta_key ) ), ARRAY_N );
			/**
			 * Custom fields values for given post obtained directly from database
			 *
			 * @since 4.1
			 *
			 * @param array $values Custom fields values as they are in the database
			 * @param array {
			 *      @type int $post_id ID of post associated with custom field
			 *      @type string $meta_key custom fields meta key
			 *      @type int $custom_fields_translation field translation option
			 *
			 * }
			 */
			$values = apply_filters( 'wpml_custom_field_values', $values, array('post_id' => $post_id, 'meta_key' => $meta_key, 'custom_fields_translation' => WPML_COPY_ONCE_CUSTOM_FIELD ) );
			if ( empty( $values ) ) {
				$source_element_id = $this->wpml_post_translation->get_original_element( $post_id );
				if ( $source_element_id ) {
					$this->sitepress->sync_custom_field( $source_element_id, $post_id, $meta_key );
				}
			}
		}
	}

}

