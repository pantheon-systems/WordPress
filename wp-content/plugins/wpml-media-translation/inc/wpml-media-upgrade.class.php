<?php

class WPML_Media_Upgrade {
	private static $versions = array(
		'2.0',
		'2.0.1',
	);

	static function run() {
		global $wpdb;

		//Workaround, as for some reasons, get_option() doesn't work only in this case
		$wpml_media_settings_prepared = $wpdb->prepare( "select option_value from {$wpdb->prefix}options where option_name = %s", '_wpml_media' );
		$wpml_media_settings          = $wpdb->get_col( $wpml_media_settings_prepared );

		$needs_version_update = true;

		//Do not run upgrades if this is a new install (i.e.: plugin has no settings)
		if ( $wpml_media_settings || get_option( '_wpml_media_starting_help' ) ) {
			$current_version = WPML_Media::get_setting( 'version', null );

			if ( $current_version ) {
				$needs_version_update = version_compare( $current_version, WPML_MEDIA_VERSION, '<' );
				self::run_upgrades_before_2_3_0( $current_version );
			} elseif ( self::is_media_version_older_than_2_0() ) {
				$needs_version_update = true;
				self::run_upgrades_before_2_3_0( '1.6' );
			}
		}

		if ( $needs_version_update ) {
			WPML_Media::update_setting( 'version', WPML_MEDIA_VERSION );
		}

		// Blocking database migration
		self::upgrade_2_3_0();
	}

	/** @param int $current_version */
	private static function run_upgrades_before_2_3_0( $current_version ) {
		if ( version_compare( $current_version, '2.3.0', '<' ) ) {

			foreach ( self::$versions as $version ) {
				if ( version_compare( $version, WPML_MEDIA_VERSION, '<=' ) && version_compare( $version, $current_version, '>' ) ) {

					$upgrade_method = 'upgrade_' . str_replace( '.', '_', $version );
					if ( method_exists( __CLASS__, $upgrade_method ) ) {
						self::$upgrade_method();
					}
				}
			}

			update_option( 'wpml_media_upgraded_from_prior_2_3_0', 1 );
		}
	}

	/** @return bool */
	private static function is_media_version_older_than_2_0() {
		global $wpdb;
		return (bool) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = 'wpml_media_duplicate_of'" );
	}

	private static function upgrade_2_0() {
		global $wpdb;
		global $sitepress;

		//Check if the old options are set and in case move them to the new plugin settings, then delete the old ones
		$old_starting_help = get_option( '_wpml_media_starting_help' );
		if ( $old_starting_help ) {
			WPML_Media::update_setting( 'starting_help', $old_starting_help );
			delete_option( '_wpml_media_starting_help' );
		}

		//Create translated media

		$target_language         = $sitepress->get_default_language();
		$attachment_ids_prepared = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'attachment' );
		$attachment_ids          = $wpdb->get_col( $attachment_ids_prepared );

		//Let's first set the language of all images in default languages
		foreach ( $attachment_ids as $attachment_id ) {
			$wpml_media_lang         = get_post_meta( $attachment_id, 'wpml_media_lang', true );
			$wpml_media_duplicate_of = get_post_meta( $attachment_id, 'wpml_media_duplicate_of', true );

			if ( ! $wpml_media_duplicate_of && ( ! $wpml_media_lang || $wpml_media_lang == $target_language ) ) {
				$trid = $sitepress->get_element_trid( $attachment_id, 'post_attachment' );
				if ( $trid ) {
					//Since trid exists, get the language from there
					$target_language = $sitepress->get_language_for_element( $attachment_id, 'post_attachment' );
				}

				$sitepress->set_element_language_details( $attachment_id, 'post_attachment', $trid, $target_language );
			}
		}

		//Then all the translations
		foreach ( $attachment_ids as $attachment_id ) {
			$wpml_media_lang         = get_post_meta( $attachment_id, 'wpml_media_lang', true );
			$wpml_media_duplicate_of = get_post_meta( $attachment_id, 'wpml_media_duplicate_of', true );

			if ( $wpml_media_duplicate_of ) {
				$source_language = null;
				$trid            = $sitepress->get_element_trid( $wpml_media_duplicate_of, 'post_attachment' );
				$source_language = false;
				if ( $trid ) {
					//Get the source language of the attachment, just in case is from a language different than the default
					$source_language = $sitepress->get_language_for_element( $wpml_media_duplicate_of, 'post_attachment' );

					//Fix bug on 1.6, where duplicated images are set to the default language
					if ( $wpml_media_lang == $source_language ) {
						$wpml_media_lang = false;
						$attachment      = get_post( $attachment_id );
						if ( $attachment->post_parent ) {
							$parent_post          = get_post( $attachment->post_parent );
							$post_parent_language = $sitepress->get_language_for_element( $parent_post->ID, 'post_' . $parent_post->post_type );
							if ( $post_parent_language ) {
								$wpml_media_lang = $post_parent_language;
							}
						}

						if ( ! $wpml_media_lang ) {
							//Trash orphan image
							wp_delete_attachment( $attachment_id );
						}
					}
				}

				if ( $wpml_media_lang ) {
					$sitepress->set_element_language_details( $attachment_id, 'post_attachment', $trid, $wpml_media_lang, $target_language, $source_language );
				}
			}
		}


		//Remove old media translation meta
		//Remove both meta just in case
		$attachment_ids = $wpdb->get_col( $attachment_ids_prepared );
		foreach ( $attachment_ids as $attachment_id ) {
			delete_post_meta( $attachment_id, 'wpml_media_duplicate_of' );
			delete_post_meta( $attachment_id, 'wpml_media_lang' );
		}

	}

	private static function upgrade_2_0_1() {
		global $wpdb;
		global $sitepress;

		// Fixes attachments metadata among translations
		$sql          = "
				SELECT t.element_id, t.trid, t.language_code
				FROM {$wpdb->prefix}icl_translations t
				  LEFT JOIN {$wpdb->postmeta} pm
				  ON t.element_id = pm.post_id AND pm.meta_key=%s
				WHERE t.element_type = %s AND pm.meta_id IS NULL AND element_id IS NOT NULL
				";
		$sql_prepared = $wpdb->prepare( $sql, array( '_wp_attachment_metadata', 'post_attachment' ) );

		$original_attachments = $wpdb->get_results( $sql_prepared );

		foreach ( $original_attachments as $original_attachment ) {
			$attachment_metadata = get_post_meta( $original_attachment->element_id, '_wp_attachment_metadata', true );
			if ( ! $attachment_metadata ) {
				$attachment_translations = $sitepress->get_element_translations( $original_attachment->trid, 'post_attachment', true, true );
				// Get _wp_attachment_metadata first translation available
				foreach ( $attachment_translations as $attachment_translation ) {
					if ( $attachment_translation->language_code != $original_attachment->language_code ) {
						$attachment_metadata = get_post_meta( $attachment_translation->element_id, '_wp_attachment_metadata', true );
						// _wp_attachment_metadata found: save it in the original and go to the next attachment
						if ( $attachment_metadata ) {
							update_post_meta( $original_attachment->element_id, '_wp_attachment_metadata', $attachment_metadata );
							break;
						}
					}
				}
			}
		}

		return true;
	}

	private static function upgrade_2_3_0() {
		global $wpdb, $sitepress;

		if ( ! WPML_Media_2_3_0_Migration::migration_complete() ) {

			$migration = new WPML_Media_2_3_0_Migration( $wpdb, $sitepress );
			if ( $migration->is_required() ) {
				$migration->maybe_show_admin_notice();
				$migration->add_hooks();
			}
		}
	}

}