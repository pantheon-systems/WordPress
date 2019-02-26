<?php

/**
 * Class WPML_Google_Sitemap_Generator
 *
 * # Compatibility class for Google XML Sitemaps (https://wordpress.org/plugins/google-sitemap-generator/)
 *
 * ## Why is this needed?
 *
 * Google XML Sitemaps displays all the translations together. When we use a different domain per language we want to have separate sitemaps for each domain.
 *
 * ## How does this work?
 *
 * WPML fetches a list of post ids in other languages to pass them to the database query via 'sm_b_exclude' option.
 *
 * This class is loaded and instantiated by `plugins-integration.php` only if the `GoogleSitemapGeneratorLoader` class exists.
 */
class WPML_Google_Sitemap_Generator {

	private $wpdb;
	private $sitepress;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb, SitePress $sitepress ) {
		$this->wpdb = $wpdb;
		$this->sitepress = $sitepress;
	}

	public function init_hooks() {
		if ( ! is_admin() && $this->is_per_domain() ) {
			add_filter( 'option_sm_options', array( $this, 'exclude_other_languages' ) );
		}
	}

	/**
	 * @return bool
	 */
	public function is_per_domain() {
		return WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $this->sitepress->get_setting( 'language_negotiation_type', false );
	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public function exclude_other_languages( $value ) {
		$current_language = apply_filters( 'wpml_current_language', false );
		$ids_query        = "SELECT element_id FROM {$this->wpdb->prefix}icl_translations WHERE element_type LIKE 'post_%%' AND language_code <> %s";
		$ids_prepared     = $this->wpdb->prepare( $ids_query, $current_language );
		$ids              = $this->wpdb->get_col( $ids_prepared );
		$ids              = array_map( 'intval', $ids );

		if ( ! is_array( $value ) ) {
			$value = array();
		}

		if ( ! array_key_exists( 'sm_b_exclude', $value ) || ! is_array( $value['sm_b_exclude'] ) ) {
			$value['sm_b_exclude'] = array();
		}

		$value['sm_b_exclude'] = array_merge( $value['sm_b_exclude'], $ids );

		return $value;
	}
}
