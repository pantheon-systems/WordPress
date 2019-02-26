<?php

class WPML_TM_Filters {
	/** @var array */
	private $string_lang_codes;

	/** @var wpdb */
	private $wpdb;

	/** @var SitePress */
	private $sitepress;

	/**
	 * WPML_TM_Filters constructor.
	 *
	 * @param wpdb $wpdb
	 * @param SitePress $sitepress
	 */
	public function __construct( wpdb $wpdb, SitePress $sitepress ) {
		$this->wpdb      = $wpdb;
		$this->sitepress = $sitepress;
	}

	/**
	 * Filters the active languages to include all languages in which strings exist.
	 *
	 * @param WPML_Language_Collection $source_langs
	 *
	 * @return array[]
	 */
	public function filter_tm_source_langs( WPML_Language_Collection $source_langs ) {
		foreach ( $this->get_string_lang_codes() as $lang_code ) {
			$source_langs->add( $lang_code );
		}

		return $source_langs;
	}

	private function get_string_lang_codes() {
		if ( null === $this->string_lang_codes ) {
			$this->string_lang_codes = $this->wpdb->get_col( "SELECT DISTINCT(s.language) FROM {$this->wpdb->prefix}icl_strings s" );
		}

		return $this->string_lang_codes;
	}

	/**
	 * This filters the check whether or not a job is assigned to a specific translator for local string jobs.
	 * It is to be used after assigning a job, as it will update the assignment for local string jobs itself.
	 *
	 * @param bool       $assigned_correctly
	 * @param string|int $string_translation_id
	 * @param int        $translator_id
	 * @param string|int $service
	 *
	 * @return bool
	 */
	public function job_assigned_to_filter( $assigned_correctly, $string_translation_id, $translator_id, $service ) {
		if ( ( ! $service || $service === 'local' ) && strpos( $string_translation_id, 'string|' ) !== false ) {
			$string_translation_id = preg_replace( '/[^0-9]/', '', $string_translation_id );
			$this->wpdb->update(
				$this->wpdb->prefix . 'icl_string_translations',
				array( 'translator_id' => $translator_id ),
				array( 'id' => $string_translation_id )
			);
			$assigned_correctly = true;
		}

		return $assigned_correctly;
	}
}