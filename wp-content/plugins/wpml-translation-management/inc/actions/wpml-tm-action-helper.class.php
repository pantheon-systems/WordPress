<?php

class WPML_TM_Action_Helper {

	public function get_tm_instance(){

		return wpml_load_core_tm();
	}

	public function create_translation_package( $post ) {
		$package_helper = new WPML_Element_Translation_Package();

		return $package_helper->create_translation_package( $post );
	}

	public function add_translation_job( $rid, $translator_id, $translation_package, $batch_options = array() ) {

		return $this->get_update_translation_action( $translation_package )
		            ->add_translation_job( $rid, $translator_id, $translation_package, $batch_options );
	}

	/**
	 * calculate post md5
	 *
	 * @param object|int $post
	 *
	 * @return string
	 * @todo full support for custom posts and custom taxonomies
	 */
	public function post_md5( $post ) {
		$post_key = '';

		//TODO: [WPML 3.2] Make it work with PackageTranslation: this is not the right way anymore
		if ( isset( $post->external_type ) && $post->external_type ) {
			foreach ( $post->string_data as $key => $value ) {
				$post_key .= $key . $value;
			}
		} else {
			if ( is_numeric( $post ) ) {
				$post = get_post( $post );
			}

			$post_tags            = $this->get_post_terms( $post, 'post_tag' );
			$post_categories      = $this->get_post_terms( $post, 'category' );
			$post_taxonomies      = $this->get_post_taxonomies( $post );
			$custom_fields_values = $this->get_post_custom_fields( $post );

			$content = $post->post_content;
			$content = apply_filters( 'wpml_pb_shortcode_content_for_translation', $content, $post->ID );

			$post_key = $post->post_title . ';' . $content . ';' . $post->post_excerpt . ';' . implode( ',', $post_tags ) . ';' . implode( ',', $post_categories ) . ';' . implode( ',', $custom_fields_values );

			if ( ! empty( $post_taxonomies ) ) {
				$post_key .= ';' . implode( ';', $post_taxonomies );
			}
			if ( wpml_get_setting_filter( false, 'translated_document_page_url' ) === 'translate' ) {
				$post_key .= $post->post_name . ';';
			}
		}

		$post_key = apply_filters( 'wpml_post_md5_key', $post_key, $post );

		return md5( $post_key );
	}

	private function get_post_terms( $post, $taxonomy, $sort = false ) {
		global $sitepress;

		$terms               = array();
		//we shouldn't adjust term by current language need get terms by post_id
		remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );

		$post_taxonomy_terms = wp_get_object_terms( $post->ID, $taxonomy );
		if ( ! is_wp_error( $post_taxonomy_terms ) ) {
			foreach ( $post_taxonomy_terms as $trm ) {
				$terms[] = $trm->name;
			}
		}

		if ( $terms ) {
			sort( $terms, SORT_STRING );
		}

		add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );

		return $terms;
	}

	private function get_post_taxonomies( $post ) {
		global $wpdb, $sitepress_settings;

		$post_taxonomies = array();

		// get custom taxonomies
		$taxonomies = $wpdb->get_col(
			$wpdb->prepare(
				"
				SELECT DISTINCT tx.taxonomy
				FROM {$wpdb->term_taxonomy} tx JOIN {$wpdb->term_relationships} tr ON tx.term_taxonomy_id = tr.term_taxonomy_id
				WHERE tr.object_id =%d ",
				$post->ID
			)
		);
		sort( $taxonomies, SORT_STRING );
		if ( isset( $sitepress_settings['taxonomies_sync_option'] ) ) {
			foreach ( $taxonomies as $t ) {
				if ( taxonomy_exists( $t ) && isset( $sitepress_settings['taxonomies_sync_option'][ $t ] ) && $sitepress_settings['taxonomies_sync_option'][ $t ] == 1 ) {
					$taxs = $this->get_post_terms( $post, $t );

					if ( $taxs ) {
						$post_taxonomies[] = '[' . $t . ']:' . implode( ',', $taxs );
					}
				}
			}
		}

		return $post_taxonomies;
	}

	private function get_post_custom_fields( $post ) {
		global $iclTranslationManagement;

		$custom_fields_values = array();

		if ( isset( $iclTranslationManagement->settings['custom_fields_translation'] ) && is_array( $iclTranslationManagement->settings['custom_fields_translation'] ) ) {
			foreach ( $iclTranslationManagement->settings['custom_fields_translation'] as $cf => $op ) {
				if ( in_array( (int) $op, array( WPML_TRANSLATE_CUSTOM_FIELD, WPML_COPY_ONCE_CUSTOM_FIELD ), true ) ) {
					$value = get_post_meta( $post->ID, $cf, true );
					if ( is_scalar( $value ) ) {
						$custom_fields_values[] = $value;
					} else {
						$custom_fields_values[] = wp_json_encode( $value );
					}
				}
			}
		}

		$custom_fields_values = apply_filters( 'wpml_custom_field_values_for_post_signature', $custom_fields_values, $post->ID );
		return $custom_fields_values;
	}

	private function get_update_translation_action( $translation_package ) {
		require_once WPML_TM_PATH . '/inc/translation-jobs/helpers/wpml-update-external-translation-data-action.class.php';
		require_once WPML_TM_PATH . '/inc/translation-jobs/helpers/wpml-update-post-translation-data-action.class.php';

		return array_key_exists( 'type', $translation_package ) && $translation_package['type'] === 'post'
			? new WPML_TM_Update_Post_Translation_Data_Action() : new WPML_TM_Update_External_Translation_Data_Action();
	}
}