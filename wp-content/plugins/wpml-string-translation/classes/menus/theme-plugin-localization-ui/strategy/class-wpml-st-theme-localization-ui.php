<?php

class WPML_ST_Theme_Localization_UI implements IWPML_Theme_Plugin_Localization_UI_Strategy {

	/** @var WPML_ST_Theme_Localization_UI */
	private $utils;

	/** @var string */
	private $template_path;

	/** @var WPML_Localization */
	private $localization;

	/**
	 * WPML_ST_Theme_Localization_UI constructor.
	 *
	 * @param WPML_Localization $localization
	 * @param WPML_ST_Theme_Localization_Utils $utils
	 * @param string $template_path
	 */
	public function __construct(
		WPML_Localization $localization,
		WPML_ST_Theme_Localization_Utils $utils,
		$template_path ) {

		$this->localization = $localization;
		$this->utils = $utils;
		$this->template_path = $template_path;
	}

	/** @return array */
	public function get_model() {

		$model = array(
			'section_label'      => __( 'Strings in the themes', 'wpml-string-translation' ),
			'scan_button_label'  => __( 'Scan selected themes for strings', 'wpml-string-translation' ),
			'completed_title'    => __( 'Completely translated strings', 'wpml-string-translation' ),
			'needs_update_title' => __( 'Strings in need of translation', 'wpml-string-translation' ),
			'component'          => __( 'Theme', 'wpml-string-translation' ),
			'domain'             => __( 'Textdomain', 'wpml-string-translation' ),
			'all_text'           => __( 'All', 'wpml-string-translation' ),
			'active_text'        => __( 'Active', 'wpml-string-translation' ),
			'inactive_text'      => __( 'Inactive', 'wpml-string-translation' ),
			'type'               => 'theme',
			'components'         => $this->get_components(),
			'stats_id'           => 'wpml_theme_scan_stats',
			'scan_button_id'     => 'wpml_theme_localization_scan',
			'section_class'      => 'wpml_theme_localization',
			'nonces'             => array(
				'scan_folder' => array(
					'action' => WPML_ST_Theme_Plugin_Scan_Dir_Ajax_Factory::AJAX_ACTION,
					'nonce'  => wp_create_nonce( WPML_ST_Theme_Plugin_Scan_Dir_Ajax_Factory::AJAX_ACTION ),
				),
				'scan_files'  => array(
					'action' => WPML_ST_Theme_Plugin_Scan_Files_Ajax_Factory::AJAX_ACTION,
					'nonce'  => wp_create_nonce( WPML_ST_Theme_Plugin_Scan_Files_Ajax_Factory::AJAX_ACTION ),
				),
				'update_hash' => array(
					'action' => WPML_ST_Update_File_Hash_Ajax_Factory::AJAX_ACTION,
					'nonce'  => wp_create_nonce( WPML_ST_Update_File_Hash_Ajax_Factory::AJAX_ACTION ),
				),
			),
			'status_count'       => array(
				'active'   => 1,
				'inactive' => count( $this->utils->get_theme_data() ) - 1,
			),
		);

		return $model;
	}

	/** @return array */
	private function get_components() {
		$components = array();
		$theme_localization_status = $this->localization->get_localization_stats( 'theme' );

		$status_counters = array(
			'complete'   => 0,
			'incomplete' => 0,
		);

		foreach ( $this->utils->get_theme_data() as $theme_folder => $theme_data ) {
			$domains = array_key_exists( $theme_folder, $theme_localization_status ) ? $theme_localization_status[ $theme_folder ] : false;

			$components[ $theme_folder ] = array(
				'id'             => md5( $theme_data['path'] ),
				'component_name' => $theme_data['name'],
				'active'         => wp_get_theme()->get( 'Name' ) === $theme_data['name'],
			);

			$components[ $theme_folder ]['domains'] = array();

			if ( $domains ) {
				foreach ( $domains as $domain => $stats ) {
					$components[ $theme_folder ]['domains'][ $domain ] = $this->get_component( $domain, $stats );
				}
			} else {
				if ( ! array_key_exists( 'TextDomain', $theme_data ) ) {
					$theme_data['TextDomain'] = __( 'No TextDomain', 'wpml-string-translation' );
				}
				$components[ $theme_folder ]['domains'][ $theme_data['TextDomain'] ] = $this->get_component( $theme_data['TextDomain'], $status_counters );
			}
		}

		return $components;
	}

	/**
	 * @param string $domain
	 * @param array $stats
	 *
	 * @return array
	 */
	private function get_component( $domain, array $stats ) {
		$base_st_url = admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php' );
		return array(
			'translated'   => $stats['complete'],
			'needs_update' => $stats['incomplete'],
			'needs_update_link' => add_query_arg( array( 'context' => $domain, 'status' => ICL_STRING_TRANSLATION_NOT_TRANSLATED ), $base_st_url ),
			'translated_link' => add_query_arg( array( 'context' => $domain, 'status' => ICL_STRING_TRANSLATION_COMPLETE ), $base_st_url ),
			'domain_link' => add_query_arg( array( 'context' => $domain), $base_st_url ),
			'title_needs_translation' => sprintf( __( 'Translate strings in %s', 'wpml-string-translation' ), $domain),
			'title_all_strings' => sprintf( __( 'All strings in %s', 'wpml-string-translation' ), $domain),
		);
	}

	/** @return string */
	public function get_template() {
		return 'theme-plugin-localization-ui.twig';
	}
}