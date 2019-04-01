<?php

class WPML_Beaver_Builder_Cleanup_Hooks implements IWPML_Action {

	public function add_hooks() {
		add_action( 'wpml_delete_unused_package_strings', array( $this, 'delete_block_layout_string' ) );
	}

	/**
	 * @param array $package_data
	 */
	public function delete_block_layout_string( $package_data ) {
		$packages = apply_filters( 'wpml_st_get_post_string_packages', array(), $package_data['post_id'] );

		/** @var WPML_Package[] $packages */
		foreach ( $packages as $package ) {
			if ( WPML_Gutenberg_Integration::PACKAGE_ID === $package->kind ) {
				$strings = $package->get_package_strings();

				$is_unique_string = count( $strings ) < 2;

				foreach ( $strings as $string ) {
					if ( 'fl-builder/layout' === $string->title ) {

						if ( $is_unique_string ) {
							do_action( 'wpml_delete_package', $package->name, $package->kind );
						} else {
							do_action( 'wpml_st_delete_all_string_data', $string->id );
						}
					}
				}
			}
		}
	}
}
