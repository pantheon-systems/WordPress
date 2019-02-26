<?php

require dirname( __FILE__ ) . '/wpml-taxonomy-translation-sync-display.class.php';
new WPML_Taxonomy_Translation_Sync_Display();

class WPML_Taxonomy_Translation_Table_Display {

	private static function get_strings_translation_array() {
		$st_plugin = '<a href="' . get_admin_url( null, 'plugins.php' ) . '" target="_blank" class="wpml-external-link">WPML String Translation</a>';

		$term_results_cap = defined( 'WPML_TAXONOMY_TRANSLATION_MAX_TERMS_RESULTS_SET' ) ?
			WPML_TAXONOMY_TRANSLATION_MAX_TERMS_RESULTS_SET :
			WPML_Taxonomy_Translation_Screen_Data::WPML_TAXONOMY_TRANSLATION_MAX_TERMS_RESULTS_SET;

		$labels = array(
			'Show'                            => __( 'Show', 'sitepress' ),
			'untranslated'                    => __( 'untranslated', 'sitepress' ),
			'all'                             => __( 'all', 'sitepress' ),
			'in'                              => __( 'in', 'sitepress' ),
			'to'                              => __( 'to', 'sitepress' ),
			'of'                              => __( 'of', 'sitepress' ),
			'taxonomy'                        => __( 'Taxonomy', 'sitepress' ),
			'anyLang'                         => __( 'any language', 'sitepress' ),
			'apply'                           => __( 'Refresh', 'sitepress' ),
			'synchronizeBtn'                  => __( 'Update Taxonomy Hierarchy', 'sitepress' ),
			'searchPlaceHolder'               => __( 'Search', 'sitepress' ),
			'selectParent'                    => __( 'select parent', 'sitepress' ),
			'taxToTranslate'                  => __( 'Select the taxonomy to translate: ', 'sitepress' ),
			'translate'                       => sprintf( __( '%1$s Translation', 'sitepress' ), '%taxonomy%' ),
			'Synchronize'                     => __( 'Hierarchy Synchronization', 'sitepress' ),
			'lowercaseTranslate'              => __( 'translate', 'sitepress' ),
			'copyToAllLanguages'              => __( 'Copy to all languages', 'sitepress' ),
			'copyToAllMessage'                => sprintf( __( 'Copy this term from original: %1$s to all other languages?' ), '%language%' ),
			'copyAllOverwrite'                => __( 'Overwrite existing translations', 'sitepress' ),
			'willBeRemoved'                   => __( 'Will be removed', 'sitepress' ),
			'willBeAdded'                     => __( 'Will be added', 'sitepress' ),
			'legend'                          => __( 'Legend:', 'sitepress' ),
			'refLang'                         => sprintf( __( 'Synchronize taxonomy hierarchy according to: %1$s language.', 'sitepress' ), '%language%' ),
			'targetLang'                      => __( 'Target Language', 'sitepress' ),
			'termPopupDialogTitle'            => __( 'Term translation', 'sitepress' ),
			'originalTermPopupDialogTitle'    => __( 'Original term', 'sitepress' ),
			'labelPopupDialogTitle'           => __( 'Label translation', 'sitepress' ),
			'copyFromOriginal'                => __( 'Copy from original', 'sitepress' ),
			'original'                        => __( 'Original:', 'sitepress' ),
			'translationTo'                   => __( 'Translation to:', 'sitepress' ),
			'Name'                            => __( 'Name', 'sitepress' ),
			'Slug'                            => __( 'Slug', 'sitepress' ),
			'Description'                     => __( 'Description', 'sitepress' ),
			'Ok'                              => __( 'OK', 'sitepress' ),
			'save'                            => __( 'Save', 'sitepress' ),
			'Singular'                        => __( 'Singular', 'sitepress' ),
			'Plural'                          => __( 'Plural', 'sitepress' ),
			'changeLanguage'                  => __( 'Change language', 'sitepress' ),
			'cancel'                          => __( 'Cancel', 'sitepress' ),
			'loading'                         => __( 'loading', 'sitepress' ),
			'Save'                            => __( 'Save', 'sitepress' ),
			'currentPage'                     => __( 'Current page', 'sitepress' ),
			'goToPreviousPage'                => __( 'Go to previous page', 'sitepress' ),
			'goToNextPage'                    => __( 'Go to the next page', 'sitepress' ),
			'goToFirstPage'                   => __( 'Go to the first page', 'sitepress' ),
			'goToLastPage'                    => __( 'Go to the last page', 'sitepress' ),
			'hieraSynced'                     => __( 'The taxonomy hierarchy is now synchronized.', 'sitepress' ),
			'hieraAlreadySynced'              => __( 'The taxonomy hierarchy is already synchronized.', 'sitepress' ),
			'noTermsFound'                    => sprintf( __( 'No %1$s found.', 'sitepress' ), '%taxonomy%' ),
			'items'                           => __( 'items', 'sitepress' ),
			'item'                            => __( 'item', 'sitepress' ),
			'summaryTerms'                    => sprintf( __( 'Translation of %1$s', 'sitepress' ), '%taxonomy%' ),
			'summaryLabels'                   => sprintf( __( 'Translations of taxonomy %1$s labels and slug', 'sitepress' ), '%taxonomy%' ),
			'activateStringTranslation'       => sprintf( __( 'To translate taxonomy labels and slug you need %s plugin.', 'sitepress' ), $st_plugin ),
			'preparingTermsData'              => __( 'Loading ...', 'sitepress' ),
			'firstColumnHeading'              => sprintf( __( '%1$s terms (in original language)', 'sitepress' ), '%taxonomy%' ),
			'resultsTruncated'                => sprintf( __( 'Because too many %1$s were found, only the first %2$s results are listed. You can refine the results using the Search field below.', 'sitepress' ), '%taxonomy%', '<strong>' . $term_results_cap . '</strong>' ),
			'wpml_save_term_nonce'            => wp_create_nonce( 'wpml_save_term_nonce' ),
			'wpml_tt_sync_hierarchy_nonce'    => wp_create_nonce( 'wpml_tt_sync_hierarchy_nonce' ),
			'wpml_generate_unique_slug_nonce' => wp_create_nonce( 'wpml_generate_unique_slug_nonce' ),
			'wpml_taxonomy_translation_nonce' => wp_create_nonce( 'wpml_taxonomy_translation_nonce' ),

			'addTranslation'   => __( 'Add translation', 'sitepress' ),
			'editTranslation'  => __( 'Edit translation', 'sitepress' ),
			'originalLanguage' => __( 'Original language', 'sitepress' ),
			'termMetaLabel'    => __( 'This term has additional meta fields:', 'sitepress' ),
		);

		return $labels;
	}

	public static function enqueue_taxonomy_table_js( $sitepress ) {

		WPML_Simple_Language_Selector::enqueue_scripts();

		$core_dependencies = array( 'jquery', 'jquery-ui-dialog', 'backbone', 'wpml-underscore-template-compiler' );
		wp_register_script(
			'templates-compiled',
		                    ICL_PLUGIN_URL . '/res/js/taxonomy-translation/templates-compiled.js',
		                    $core_dependencies, '1.2.4' );
		$core_dependencies[] = 'templates-compiled';
		wp_register_script( 'main-util', ICL_PLUGIN_URL . '/res/js/taxonomy-translation/util.js', $core_dependencies );

		wp_register_script( 'main-model', ICL_PLUGIN_URL . '/res/js/taxonomy-translation/main.js', $core_dependencies );
		$core_dependencies[] = 'main-model';

		$dependencies = $core_dependencies;
		wp_register_script(
			'term-rows-collection',
		                    ICL_PLUGIN_URL . '/res/js/taxonomy-translation/collections/term-rows.js',
			array_merge( $core_dependencies, array( 'term-row-model' ) )
		);
		$dependencies[] = 'term-rows-collection';
		wp_register_script(
			'term-model',
		                    ICL_PLUGIN_URL . '/res/js/taxonomy-translation/models/term.js',
		                    $core_dependencies );
		$dependencies[] = 'term-model';
		wp_register_script(
			'taxonomy-model',
		                    ICL_PLUGIN_URL . '/res/js/taxonomy-translation/models/taxonomy.js',
		                    $core_dependencies );
		$dependencies[] = 'taxonomy-model';
		wp_register_script(
			'term-row-model',
		                    ICL_PLUGIN_URL . '/res/js/taxonomy-translation/models/term-row.js',
		                    $core_dependencies );
		$dependencies[] = 'term-row-model';
		
		foreach ( array(
			          'filter-view',
			          'nav-view',
			          'table-view',
			          'taxonomy-view',
			          'term-popup-view',
			          'original-term-popup-view',
			          'label-popup-view',
			          'term-row-view',
			          'label-row-view',
			          'term-rows-view',
			          'term-view',
			          'term-original-view',
			          'copy-all-popup-view',
						) as $script ) {
			
			wp_register_script(
				$script,
				ICL_PLUGIN_URL . '/res/js/taxonomy-translation/views/' . $script . '.js',
				$core_dependencies,
				'1.2.4'
			);
			$dependencies[ ] = $script;
		}

		wp_localize_script( 'main-model', 'labels', self::get_strings_translation_array() );
		wp_localize_script( 'main-model', 'wpml_taxonomies', self::wpml_get_table_taxonomies( $sitepress ) );

		$need_enqueue   = $dependencies;
		$need_enqueue[] = 'main-model';
		$need_enqueue[] = 'main-util';
		$need_enqueue[] = 'templates';

		foreach ( $need_enqueue as $handle ) {
			wp_enqueue_script( $handle );
		}

		wp_register_script( 'taxonomy-hierarchy-sync-message', ICL_PLUGIN_URL . '/res/js/taxonomy-hierarchy-sync-message.js', array( 'jquery' ) );
		wp_enqueue_script( 'taxonomy-hierarchy-sync-message' );

	}

	public static function wpml_get_table_taxonomies( SitePress $sitepress ) {
		$taxonomies = $sitepress->get_wp_api()->get_taxonomies( array(), 'objects' );

		$result = array( 'taxonomies' => array(), 'activeLanguages' => array(), 'allLanguages' => array() );
		$sitepress->set_admin_language();
		$active_langs = $sitepress->get_active_languages();
		$default_lang = $sitepress->get_default_language();

		$result['activeLanguages'][ $default_lang ] = array(
			'label' => esc_js( $active_langs[ $default_lang ]['display_name'] ),
			'flag'  => esc_url( $sitepress->get_flag_url( $default_lang ) ),
		);
		foreach ( $active_langs as $code => $lang ) {
			if ( $code !== $default_lang ) {
				$result['activeLanguages'][ $code ] = array(
					'label' => esc_js( $lang['display_name'] ),
					'flag'  => esc_url( $sitepress->get_flag_url( $code ) ),
				);
			}
		}
		
		$all_languages = $sitepress->get_languages();
		foreach ( $all_languages as $code => $lang ) {
			$result['allLanguages'][ $code ] = array(
				'label' => esc_js( $lang['display_name'] ),
				'flag'  => esc_url( $sitepress->get_flag_url( $code ) ),
			);
		}

		foreach ( $taxonomies as $key => $tax ) {
			if ( $sitepress->is_translated_taxonomy( $key ) ) {
				$result['taxonomies'][ $key ] = array(
					'label'         => $tax->label,
					'singularLabel' => $tax->labels->singular_name,
					'hierarchical'  => $tax->hierarchical,
					'name'          => $key
				);
			}
		}

		return $result;
	}

	public static function wpml_get_terms_and_labels_for_taxonomy_table() {
		global $sitepress;

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpml_taxonomy_translation_nonce' ) ) {
			wp_send_json_error( __( 'Wrong nonce', 'sitepress' ) );
			return;
		}

		$taxonomy = false;

		$request_post_taxonomy = filter_input( INPUT_POST,
			'taxonomy',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			FILTER_NULL_ON_FAILURE );
		if ( $request_post_taxonomy ) {
			$taxonomy = html_entity_decode( $request_post_taxonomy );
		}

		if ( $taxonomy ) {
			$terms_data     = new WPML_Taxonomy_Translation_Screen_Data( $sitepress, $taxonomy );
			$term_results   = $terms_data->terms();
			$labels         = apply_filters( 'wpml_label_translation_data', false, $taxonomy );
			$def_lang       = $sitepress->get_default_language();
			$bottom_content = apply_filters( 'wpml_taxonomy_translation_bottom', $html = '', $taxonomy, get_taxonomy( $taxonomy ) );
			wp_send_json( array(
				'terms'                => $term_results['terms'],
				'resultsTruncated'     => $term_results['truncated'],
				'taxLabelTranslations' => $labels,
				'defaultLanguage'      => $def_lang,
				'bottomContent'        => $bottom_content,
				'taxLangSelector'      => self::render_tax_language_selector( $labels, $taxonomy ),
			) );
		} else {
			wp_send_json_error();
		}
	}

	private static function render_tax_language_selector( $labels, $taxonomy ) {
		global $sitepress;

		if ( ! isset( $labels['st_default_lang'] ) ) {
			return null;
		}

		$args = array(
			'selected'           => $labels['st_default_lang'],
			'name'               => 'string_lang[' . $taxonomy . ']',
			'show_please_select' => false,
			'echo'               => false,
			'class'              => 'js-tax-lang-selector',
		);

		$lang_selector = new WPML_Simple_Language_Selector( $sitepress );
		return $lang_selector->render( $args );
	}
}
