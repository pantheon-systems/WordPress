<?php
add_action( 'admin_init', 'duplicate_post_wpml_init' );

function duplicate_post_wpml_init() {
	if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
		add_action('dp_duplicate_page', 'duplicate_post_wpml_copy_translations', 10, 3);
		add_action('dp_duplicate_post', 'duplicate_post_wpml_copy_translations', 10, 3);
		add_action('shutdown', 'duplicate_wpml_string_packages', 11 );
	}
}

global $duplicated_posts;
$duplicated_posts = array();

function duplicate_post_wpml_copy_translations($post_id, $post, $status = '') {
	global $sitepress;
	global $duplicated_posts;
	
	remove_action('dp_duplicate_page', 'duplicate_post_wpml_copy_translations', 10);
	remove_action('dp_duplicate_post', 'duplicate_post_wpml_copy_translations', 10);
	
	$current_language = $sitepress->get_current_language();
	$trid = $sitepress->get_element_trid($post->ID);
	if (!empty($trid)) {
		$translations = $sitepress->get_element_translations($trid);
		$new_trid = $sitepress->get_element_trid($post_id);
		foreach ($translations as $code => $details) {
			if ($code != $current_language) {
				if ( $details->element_id ) {
					$translation = get_post( $details->element_id );
					if (!$translation) continue;
					$new_post_id = duplicate_post_create_duplicate( $translation, $status );
					$sitepress->set_element_language_details( $new_post_id, 'post_' . $translation->post_type, $new_trid, $code, $current_language );
				}
			}
		}
		$duplicated_posts[ $post->ID ] = $post_id;
	}
}

function duplicate_wpml_string_packages() {
	global $duplicated_posts;
	
	foreach ( $duplicated_posts as $original_post_id => $duplicate_post_id ) {
		
		$original_string_packages = apply_filters( 'wpml_st_get_post_string_packages', false, $original_post_id );
		$new_string_packages      = apply_filters( 'wpml_st_get_post_string_packages', false, $duplicate_post_id );
		if ( is_array( $original_string_packages ) ) {
			foreach ( $original_string_packages as $original_string_package ) {
				$translated_original_strings = $original_string_package->get_translated_strings( array() );
				
				foreach ( $new_string_packages as $new_string_package ) {
					$cache = new WPML_WP_Cache( 'WPML_Package' );
					$cache->flush_group_cache();
					$new_strings = $new_string_package->get_package_strings();
					foreach ( $new_strings as $new_string ) {
						
						if ( isset( $translated_original_strings[ $new_string->name ] ) ) {
							foreach ( $translated_original_strings[ $new_string->name ] as $language => $translated_string ) {
								
								do_action(
									'wpml_add_string_translation',
									$new_string->id,
									$language,
									$translated_string['value'],
									$translated_string['status']
									);
								
							}
						}
						
					}
					
				}
				
			}
		}
	}
}

?>