<?php

class WPML_API_Hook_Sync_Custom_Fields implements IWPML_Action {

	/** @var WPML_Sync_Custom_Fields $sync_custom_fields */
	private $sync_custom_fields;

	public function __construct( WPML_Sync_Custom_Fields $sync_custom_fields ) {
		$this->sync_custom_fields = $sync_custom_fields;
	}

	public function add_hooks() {
		add_action( 'wpml_sync_custom_field', array( $this, 'sync_custom_field' ), 10, 2 );
		add_action( 'wpml_sync_all_custom_fields', array( $this, 'sync_all_custom_fields' ), 10, 1 );
	}

	public function sync_custom_field( $post_id, $custom_field_name ) {
		$this->sync_custom_fields->sync_to_translations( $post_id, $custom_field_name );
	}

	public function sync_all_custom_fields( $post_id ) {
		$this->sync_custom_fields->sync_all_custom_fields( $post_id );
	}

}