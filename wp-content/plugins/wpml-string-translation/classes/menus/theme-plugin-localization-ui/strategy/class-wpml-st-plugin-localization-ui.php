<?php

class WPML_ST_Plugin_Localization_UI implements IWPML_Theme_Plugin_Localization_UI_Strategy {

	/** @var WPML_ST_Plugin_Localization_Utils */
	private $utils;

	/** @var WPML_Localization */
	private $localization;

	/** @var string */
	private $base_st_url;

	/**
	 * WPML_ST_Plugin_Localization_UI constructor.
	 *
	 * @param WPML_Localization $localization
	 * @param WPML_ST_Plugin_Localization_Utils $utils
	 */
	public function __construct(
		WPML_Localization $localization,
		WPML_ST_Plugin_Localization_Utils $utils ) {

		$this->localization = $localization;
		$this->utils = $utils;
		$this->base_st_url = admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php' );
	}

	/**
	 * @return array
	 */
	public function get_model() {

		$model = array(
			'section_label'      => __( 'Strings in the plugins', 'wpml-string-translation' ),
			'scan_button_label'  => __( 'Scan selected plugins for strings', 'wpml-string-translation' ),
			'completed_title'    => __( 'Completely translated strings', 'wpml-string-translation' ),
			'needs_update_title' => __( 'Strings in need of translation', 'wpml-string-translation' ),
			'component'          => __( 'Plugin', 'wpml-string-translation' ),
			'domain'             => __( 'Textdomain', 'wpml-string-translation' ),
			'all_text'           => __( 'All', 'wpml-string-translation' ),
			'active_text'        => __( 'Active', 'wpml-string-translation' ),
			'inactive_text'      => __( 'Inactive', 'wpml-string-translation' ),
			'type'               => 'plugin',
			'components'         => $this->get_components( $this->utils->get_plugins(), $this->localization->get_localization_stats( 'plugin' ) ),
			'stats_id'           => 'wpml_plugin_scan_stats',
			'scan_button_id'     => 'wpml_plugin_localization_scan',
			'section_class'      => 'wpml_plugin_localization',
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
				'active'   => count( $this->utils->get_plugins_by_status( true ) ),
				'inactive' => count( $this->utils->get_plugins_by_status( false ) ),
			),
		);

		return $model;
	}

	/**
	 * @param array $plugins
	 * @param array $plugin_stats
	 *
	 * @return array
	 */
	private function get_components( $plugins, $plugin_stats ) {
		$components = array();
		$no_domain_stats = array(
			'complete'   => 0,
			'incomplete' => 0,
		);

		foreach ( $plugins as $plugin_file => $plugin_data ) {
			$domains = array_key_exists( $plugin_file, $plugin_stats ) ? $plugin_stats[ $plugin_file ] : false;

			$components[ $plugin_file ] = array(
				'id'             => md5( plugin_dir_path( WP_PLUGIN_URL . '/' . $plugin_file ) ),
				'file'           => basename( $plugin_file ),
				'component_name' => $plugin_data['Name'],
				'active'         => $this->utils->is_plugin_active( $plugin_file ),
			);

			$components[ $plugin_file ]['domains'] = array();

			if ( $domains ) {
				foreach ( $domains as $domain => $stats ) {
					$components[ $plugin_file ]['domains'][ $domain ] = $this->get_component( $domain, $stats );
				}
			} else {
				if ( ! array_key_exists( 'TextDomain', $plugin_data ) ) {
					$plugin_data['TextDomain'] = __( 'No TextDomain', 'wpml-string-translation' );
				}
				$components[ $plugin_file ]['domains'][ $plugin_data['TextDomain'] ] = $this->get_component( $plugin_data['TextDomain'], $no_domain_stats );
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
		return array(
			'translated'   => $stats['complete'],
			'needs_update' => $stats['incomplete'],
			'needs_update_link' => add_query_arg( array( 'context' => $domain, 'status' => ICL_STRING_TRANSLATION_NOT_TRANSLATED ), $this->base_st_url ),
			'translated_link' => add_query_arg( array( 'context' => $domain, 'status' => ICL_STRING_TRANSLATION_COMPLETE ), $this->base_st_url ),
			'domain_link' => add_query_arg( array( 'context' => $domain), $this->base_st_url ),
			'title_needs_translation' => sprintf( __( 'Translate strings in %s', 'wpml-string-translation' ), $domain),
			'title_all_strings' => sprintf( __( 'All strings in %s', 'wpml-string-translation' ), $domain),
		);
	}

	/** @return string */
	public function get_template() {
		return 'theme-plugin-localization-ui.twig';
	}
}