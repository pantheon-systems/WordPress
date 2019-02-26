<?php

class WPML_Post_Edit_Ajax {
	const AJAX_ACTION_SWITCH_POST_LANGUAGE = 'wpml_switch_post_language';

	/**
	 * For test purposes
	 *
	 * @var WPML_Custom_Field_Setting_Factory
	 */
	public static $post_custom_field_settings;

	/**
	 * Ajax handler for adding a term via Ajax.
	 */
	public static function wpml_save_term_action() {
		global $sitepress;

		if ( ! wpml_is_action_authenticated( 'wpml_save_term' ) ) {
			wp_send_json_error( 'Wrong Nonce' );
		}

		$lang        = filter_var( $_POST['term_language_code'], FILTER_SANITIZE_STRING );
		$taxonomy    = filter_var( $_POST['taxonomy'], FILTER_SANITIZE_STRING );
		$slug        = filter_var( $_POST['slug'], FILTER_SANITIZE_STRING );
		$name        = filter_var( $_POST['name'], FILTER_SANITIZE_STRING );
		$trid        = filter_var( $_POST['trid'], FILTER_SANITIZE_NUMBER_INT );
		$description = filter_var( $_POST['description'], FILTER_SANITIZE_STRING );
		$meta_data   = isset( $_POST['meta_data'] ) ? $_POST['meta_data'] : array();

		$new_term_object = self::save_term_ajax( $sitepress, $lang, $taxonomy, $slug, $name, $trid, $description, $meta_data );
		$sitepress->get_wp_api()->wp_send_json_success( $new_term_object );

	}

	public static function save_term_ajax( $sitepress, $lang, $taxonomy, $slug, $name, $trid, $description, $meta_data ) {
		$new_term_object = false;

		if ( $name !== "" && $taxonomy && $trid && $lang ) {

			$args = array(
				'taxonomy'  => $taxonomy,
				'lang_code' => $lang,
				'term'      => $name,
				'trid'      => $trid,
				'overwrite' => true
			);

			if ( $slug ) {
				$args[ 'slug' ] = $slug;
			}
			if ( $description ) {
				$args[ 'description' ] = $description;
			}

			$switch_lang = new WPML_Temporary_Switch_Language( $sitepress, $lang );
			$is_new_term = ! term_exists( $name, $taxonomy );
			$res = WPML_Terms_Translations::create_new_term( $args );
			$switch_lang->restore_lang();

			if ( $res && isset( $res[ 'term_taxonomy_id' ] ) ) {
				/* res holds the term taxonomy id, we return the whole term objects to the ajax call */
				$switch_lang = new WPML_Temporary_Switch_Language( $sitepress, $lang );
				$new_term_object                = get_term_by( 'term_taxonomy_id', (int) $res[ 'term_taxonomy_id' ], $taxonomy );
				$switch_lang->restore_lang();
				$lang_details                   = $sitepress->get_element_language_details( $new_term_object->term_taxonomy_id, 'tax_' . $new_term_object->taxonomy );
				$new_term_object->trid          = $lang_details->trid;
				$new_term_object->language_code = $lang_details->language_code;
				if ( self::add_term_metadata( $res, $meta_data, $is_new_term ) ) {
					$new_term_object->meta_data = get_term_meta( $res['term_id'] );
				}

				WPML_Terms_Translations::icl_save_term_translation_action( $taxonomy, $res );
			}
		}

		return $new_term_object;
	}

	/**
	 * Gets the content of a post, its excerpt as well as its title and returns it as an array
	 *
	 * @param string $content_type
	 * @param string $excerpt_type
	 * @param int    $trid
	 * @param string $lang
	 *
	 * @return array containing all the fields information
	 */
	public static function copy_from_original_fields( $content_type, $excerpt_type, $trid, $lang ) {
		global $wpdb;
		$post_id = $wpdb->get_var(
			$wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s",
			                $trid,
			                $lang ) );
		$post    = get_post( $post_id );

		$fields_to_copy = array( 'content' => 'post_content',
								 'title'   => 'post_title',
								 'excerpt' => 'post_excerpt' );

		$fields_contents = array();
		if ( ! empty( $post ) ) {
			foreach ( $fields_to_copy as $editor_key => $editor_field ) { //loops over the three fields to be inserted into the array
				if ( $editor_key === 'content' || $editor_key === 'excerpt' ) { //
					$editor_var = 'rich';
					if ( $editor_key === 'content' ) {
						$editor_var = $content_type; //these variables are supplied by a javascript call in scripts.js icl_copy_from_original(lang, trid)
					} elseif ( $editor_key === 'excerpt' ) {
						$editor_var = $excerpt_type;
					}
					
					if ( function_exists( 'format_for_editor' ) ) {
						// WordPress 4.3 uses format_for_editor
						$html_pre = $post->$editor_field;
						if($editor_var == 'rich') {
							$html_pre = convert_chars( $html_pre );
							$html_pre = wpautop( $html_pre );
						}
						$html_pre = format_for_editor( $html_pre, $editor_var );
					} else {
						// Backwards compatible for WordPress < 4.3
						if ( $editor_var === 'rich' ) {
							$html_pre = wp_richedit_pre( $post->$editor_field );
						} else {
							$html_pre = wp_htmledit_pre( $post->$editor_field );
						}
					}

					$fields_contents[$editor_key] = htmlspecialchars_decode( $html_pre );
				} elseif ( $editor_key === 'title' ) {
					$fields_contents[ $editor_key ] = strip_tags( $post->$editor_field );
				}
			}
			$fields_contents[ 'builtin_custom_fields' ] = apply_filters( 'wpml_copy_from_original_custom_fields',
			                                                    self::copy_from_original_custom_fields( $post ) );

			$fields_contents['external_custom_fields'] = self::copy_meta_values_from_original( $post );
		} else {
			$fields_contents[ 'error' ] = __( 'Post not found', 'sitepress' );
		}
		do_action( 'icl_copy_from_original', $post_id );

		return $fields_contents;
	}

	/**
	 * Gets the content of a custom posts custom field , its excerpt as well as its title and returns it as an array
	 *
	 * @param  WP_post $post
	 *
	 * @return array
	 */
	public static function copy_from_original_custom_fields( $post ) {

		$elements                 = array();
		$elements [ 'post_type' ] = $post->post_type;
		$elements[ 'excerpt' ]    = array(
			'editor_name' => 'excerpt',
			'editor_type' => 'text',
			'value'       => $post->post_excerpt
		);

		return $elements;
	}

	/**
	 * @param WP_Post $post
	 * @return array
	 */
	private static function copy_meta_values_from_original ($post) {
		global $wpdb;

		if ( ! self::$post_custom_field_settings instanceof WPML_Custom_Field_Setting_Factory ) {
			$translation_management = wpml_load_core_tm();
			self::$post_custom_field_settings = new WPML_Custom_Field_Setting_Factory( $translation_management );
		}

		$post_custom_fields = self::$post_custom_field_settings->get_post_meta_keys();

		if ( ! is_array( $post_custom_fields ) || empty( $post_custom_fields ) ) {
			return array();
		}

		$sql = "SELECT meta_key as name, meta_value as value FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key IN ("
		       . wpml_prepare_in( $post_custom_fields ) . ')';

		return $wpdb->get_results( $wpdb->prepare( $sql, $post->ID ), ARRAY_A );
	}

	/**
	 * Ajax handler for switching the language of a post.
	 */
	public static function wpml_switch_post_language() {
		global $sitepress, $wpdb;

		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, self::AJAX_ACTION_SWITCH_POST_LANGUAGE ) ) {
			wp_send_json_error();
		}

		$to      = false;
		$post_id = false;

		if ( isset( $_POST[ 'wpml_to' ] ) ) {
			$to = $_POST[ 'wpml_to' ];
		}
		if ( isset( $_POST[ 'wpml_post_id' ] ) ) {
			$post_id = $_POST[ 'wpml_post_id' ];
		}

		$result = false;

		if ( $post_id && $to ) {

			$post_type      = get_post_type( $post_id );
			$wpml_post_type = 'post_' . $post_type;
			$trid           = $sitepress->get_element_trid( $post_id, $wpml_post_type );

			/* Check if a translation in that language already exists with a different post id.
			 * If so, then don't perform this action.
			 */

			$query_for_existing_translation = $wpdb->prepare( "	SELECT translation_id, element_id
																FROM {$wpdb->prefix}icl_translations
																WHERE element_type = %s
																	AND trid = %d
																	AND language_code = %s",
			                                                  $wpml_post_type, $trid, $to );
			$existing_translation           = $wpdb->get_row( $query_for_existing_translation );

			if ( $existing_translation && $existing_translation->element_id != $post_id ) {
				$result = false;
			} else {
				$sitepress->set_element_language_details( $post_id, $wpml_post_type, $trid, $to );
				// Synchronize the posts terms languages. Do not create automatic translations though.
				WPML_Terms_Translations::sync_post_terms_language( $post_id );
				require_once WPML_PLUGIN_PATH . '/inc/cache.php';
				icl_cache_clear( $post_type . 's_per_language', true );

				$result = $to;
			}
		}

		wp_send_json_success( $result );
	}

	public static function wpml_get_default_lang() {
		global $sitepress;
		wp_send_json_success( $sitepress->get_default_language() );
	}

	/**
	 * @param array $term
	 * @param array $meta_data
	 * @param bool  $is_new_term
	 *
	 * @return bool
	 */
	private static function add_term_metadata( $term, $meta_data, $is_new_term ) {
		global $sitepress;

		foreach ( $meta_data as $meta_key => $meta_value ) {
			delete_term_meta( $term['term_id'], $meta_key );
			$data = maybe_unserialize( stripslashes( $meta_value ) );
			if ( ! add_term_meta( $term['term_id'], $meta_key, $data ) ) {
				throw new RuntimeException( sprintf( 'Unable to add term meta form term: %d', $term['term_id'] ) );
			}
		}

		$sync_meta_action = new WPML_Sync_Term_Meta_Action( $sitepress, $term[ 'term_taxonomy_id' ], $is_new_term );
		$sync_meta_action->run();

		return true;
	}
}
