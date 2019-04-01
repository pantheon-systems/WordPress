<?php

class WPML_Slug_Translation implements IWPML_Action {

	/** @var array $post_link_cache */
	private $post_link_cache = array();

	/** @var  SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Slug_Translation_Records_Factory $slug_records_factory */
	private $slug_records_factory;

	/** @var WPML_ST_Term_Link_Filter $term_link_filter */
	private $term_link_filter;

	/** @var WPML_ST_Slug_Translation_Strings_Sync $slug_strings_sync */
	private $slug_strings_sync;

	/** @var WPML_Get_LS_Languages_Status $ls_languages_status */
	private $ls_languages_status;

	/** @var WPML_ST_Slug_Translation_Settings $slug_translation_settings */
	private $slug_translation_settings;

	private $ignore_post_type_link = false;

	/** @var array $translated_slugs */
	private $translated_slugs = array();

	public function __construct(
		SitePress $sitepress,
		WPML_Slug_Translation_Records_Factory $slug_records_factory,
		WPML_Get_LS_Languages_Status $ls_language_status,
		WPML_ST_Term_Link_Filter $term_link_filter,
		WPML_ST_Slug_Translation_Settings $slug_translation_settings
	) {
		$this->sitepress                 = $sitepress;
		$this->slug_records_factory      = $slug_records_factory;
		$this->ls_languages_status       = $ls_language_status;
		$this->term_link_filter          = $term_link_filter;
		$this->slug_translation_settings = $slug_translation_settings;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'init' ), WPML_Slug_Translation_Factory::INIT_PRIORITY );
	}

	public function init() {
		$this->migrate_global_enabled_setting();

		if ( $this->slug_translation_settings->is_enabled() ) {
			add_filter( 'option_rewrite_rules', array( $this, 'rewrite_rules_filter' ), 1, 1 ); // high priority
			add_filter( 'post_type_link', array( $this, 'post_type_link_filter' ), apply_filters( 'wpml_post_type_link_priority', 1 ), 4 );
			add_filter( 'pre_term_link', array( $this->term_link_filter, 'replace_slug_in_termlink' ), 1, 2 ); // high priority
			add_filter( 'edit_post', array( $this, 'clear_post_link_cache' ), 1, 2 );
			add_filter( 'query_vars', array( $this, 'add_cpt_names' ), 1, 2 );
			add_filter( 'pre_get_posts', array( $this, 'filter_pre_get_posts' ), - 1000, 2 );
		}

		if ( is_admin() ) {
			add_action( 'icl_ajx_custom_call', array( $this, 'gui_save_options' ), 10, 2 );
			add_action( 'wp_loaded', array( $this, 'maybe_migrate_string_name' ), 10, 0 );
		}
	}

	/**
	 * @deprecated since 2.8.0, use the class `WPML_Post_Slug_Translation_Records` instead.
	 *
	 * @param string $type
	 *
	 * @return null|string
	 */
	public static function get_slug_by_type( $type ) {
		$slug_records_factory = new WPML_Slug_Translation_Records_Factory();
		$slug_records         = $slug_records_factory->create( WPML_Slug_Translation_Factory::POST );

		return $slug_records->get_original( $type );
	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public static function rewrite_rules_filter( $value ) {
		if ( empty( $value ) ) {
			return $value;
		} else {
			$rewrite_rule_filter_factory = new WPML_Rewrite_Rule_Filter_Factory();

			return $rewrite_rule_filter_factory->create()->rewrite_rules_filter( $value );
		}
	}

	/**
	 * This method is only for CPT
	 *
	 * @deprecated use `WPML_ST_Slug::filter_value` directly of the filter hook `wpml_get_translated_slug`
	 *
	 * @param string      $slug_value
	 * @param string      $post_type
	 * @param string|bool $language
	 *
	 * @return string
	 */
	public function get_translated_slug( $slug_value, $post_type, $language = false ) {
		if ( $post_type ) {
			$language = $language ? $language : $this->sitepress->get_current_language();
			$slug     = $this->slug_records_factory->create( WPML_Slug_Translation_Factory::POST )
			                                       ->get_slug( $post_type );

			return $slug->filter_value( $slug_value, $language );
		}

		return $slug_value;
	}

	/**
	 * @param string  $post_link
	 * @param WP_Post $post
	 * @param bool    $leavename
	 * @param bool    $sample
	 *
	 * @return mixed|string|WP_Error
	 */
	public function post_type_link_filter( $post_link, $post, $leavename, $sample ) {

		if ( $this->ignore_post_type_link ) {
			return $post_link;
		}

		if ( ! $this->sitepress->is_translated_post_type( $post->post_type )
		     || ! ( $ld = $this->sitepress->get_element_language_details( $post->ID, 'post_' . $post->post_type ) )
		) {
			return $post_link;
		}

		$cache_key = $leavename . '#' . $sample;
		$cache_key .= $this->ls_languages_status->is_getting_ls_languages() ? 'yes' : 'no';
		if ( isset( $this->post_link_cache[ $post->ID ][ $cache_key ] ) ) {
			$post_link = $this->post_link_cache[ $post->ID ][ $cache_key ];
		} else {
			$slug_settings = $this->sitepress->get_setting( 'posts_slug_translation' );
			$slug_settings = ! empty( $slug_settings['types'][ $post->post_type ] ) ? $slug_settings['types'][ $post->post_type ] : null;
			if ( (bool) $slug_settings === true ) {

				$post_type_obj = get_post_type_object( $post->post_type );
				$slug_this     = isset( $post_type_obj->rewrite['slug'] ) ? trim( $post_type_obj->rewrite['slug'], '/' ) : false;
				$slug_real     = $this->get_translated_slug( $slug_this, $post->post_type, $ld->language_code );

				if ( empty( $slug_real ) || empty( $slug_this ) || $slug_this == $slug_real ) {
					return $post_link;
				}

				global $wp_rewrite;

				if ( isset( $wp_rewrite->extra_permastructs[ $post->post_type ] ) ) {
					$struct_original = $wp_rewrite->extra_permastructs[ $post->post_type ]['struct'];

					$lslash                                                       = false !== strpos( $struct_original, '/' . $slug_this ) ? '/' : '';
					$wp_rewrite->extra_permastructs[ $post->post_type ]['struct'] = preg_replace( '@' . $lslash . $slug_this . '/@',
						$lslash . $slug_real . '/',
						$struct_original );
					$this->ignore_post_type_link                                  = true;
					$post_link                                                    = get_post_permalink( $post->ID, $leavename, $sample );
					$this->ignore_post_type_link                                  = false;
					$wp_rewrite->extra_permastructs[ $post->post_type ]['struct'] = $struct_original;
				} else {
					$post_link = str_replace( $slug_this . '=', $slug_real . '=', $post_link );
				}
			}
			$this->post_link_cache[ $post->ID ][ $cache_key ] = $post_link;
		}

		return $post_link;
	}

	/**
	 * @param int $post_ID
	 * @param $post
	 */
	public function clear_post_link_cache( $post_ID, $post ) {
		unset( $this->post_link_cache[ $post_ID ] );
	}

	/**
	 * @return array
	 */
	private function get_all_post_slug_translations() {
		$slug_translations              = array();
		$post_slug_translation_settings = $this->sitepress->get_setting( 'posts_slug_translation' );

		if ( isset( $post_slug_translation_settings['types'] ) ) {
			$types     = $post_slug_translation_settings['types'];
			$cache_key = 'WPML_Slug_Translation::get_all_slug_translations' . md5( json_encode( $types ) );

			$slug_translations = wp_cache_get( $cache_key );

			if ( ! is_array( $slug_translations ) ) {
				$slug_translations = array();
				$types_to_fetch    = array();

				foreach ( $types as $type => $state ) {
					if ( $state ) {
						$types_to_fetch[] = str_replace( '%', '%%', $type );
					}
				}

				if ( $types_to_fetch ) {
					$data = $this->slug_records_factory
						->create( WPML_Slug_Translation_Factory::POST )
						->get_all_slug_translations( $types_to_fetch );

					foreach ( $data as $row ) {
						foreach ( $types_to_fetch as $type ) {
							if ( preg_match( '#\s' . $type . '$#', $row->name ) === 1 ) {
								$slug_translations[ $row->value ] = $type;
							}
						}
					}
				}

				wp_cache_set( $cache_key, $slug_translations );
			}
		}

		return $slug_translations;
	}

	/**
	 * Adds all translated custom post type slugs as valid query variables in addition to their original values
	 *
	 * @param array $qvars
	 *
	 * @return array
	 */
	public function add_cpt_names( $qvars ) {
		$all_slugs_translations = array_keys( $this->get_all_post_slug_translations() );
		$qvars                  = array_merge( $qvars, $all_slugs_translations );

		return $qvars;
	}

	/**
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function filter_pre_get_posts( $query ) {
		/** Do not alter the query if it has already resolved the post ID */
		if ( ! empty( $query->query_vars['p'] ) ) {
			return $query;
		}

		$all_slugs_translations = $this->get_all_post_slug_translations();

		foreach ( $query->query as $slug => $post_name ) {
			if ( isset( $all_slugs_translations[ $slug ] ) ) {
				$new_slug = isset( $all_slugs_translations[ $slug ] ) ? $all_slugs_translations[ $slug ] : $slug;
				unset( $query->query[ $slug ] );
				$query->query[ $new_slug ] = $post_name;
				$query->query['name']      = $post_name;
				$query->query['post_type'] = $new_slug;
				unset( $query->query_vars[ $slug ] );
				$query->query_vars[ $new_slug ] = $post_name;
				$query->query_vars['name']      = $post_name;
				$query->query_vars['post_type'] = $new_slug;

			}
		}

		return $query;
	}

	/**
	 * @param string $action
	 */
	public static function gui_save_options( $action ) {
		switch ( $action ) {
			case 'icl_slug_translation':
				global $sitepress;
				$is_enabled = intval( ! empty( $_POST['icl_slug_translation_on'] ) );
				$settings   = new WPML_ST_Post_Slug_Translation_Settings( $sitepress );
				$settings->set_enabled( $is_enabled );
				echo '1|' . $is_enabled;
				break;
		}
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	public static function sanitize( $slug ) {
		// we need to preserve the %
		$slug = str_replace( '%', '%45', $slug );
		$slug = sanitize_title_with_dashes( $slug );
		$slug = str_replace( '%45', '%', $slug );

		return $slug;
	}

	/**
	 * @deprecated since 2.8.0, use the class `WPML_Post_Slug_Translation_Records` instead.
	 */
	public static function register_string_for_slug( $post_type, $slug ) {
		return icl_register_string( 'WordPress', 'URL slug: ' . $post_type, $slug );
	}

	public function maybe_migrate_string_name() {
		global $wpdb;

		$slug_settings = $this->sitepress->get_setting( 'posts_slug_translation' );

		if ( ! isset( $slug_settings['string_name_migrated'] ) ) {

			$queryable_post_types = get_post_types( array( 'publicly_queryable' => true ) );

			foreach ( $queryable_post_types as $type ) {
				$post_type_obj = get_post_type_object( $type );
				$slug          = trim( $post_type_obj->rewrite['slug'], '/' );

				if ( $slug ) {
					// First check if we should migrate from the old format URL slug: slug
					$string_id = $wpdb->get_var( $wpdb->prepare( "SELECT id
											FROM {$wpdb->prefix}icl_strings
											WHERE name = %s AND value = %s",
						'URL slug: ' . $slug,
						$slug
					) );
					if ( $string_id ) {
						// migrate it to URL slug: post_type

						$st_update['name'] = 'URL slug: ' . $type;
						$wpdb->update( $wpdb->prefix . 'icl_strings', $st_update, array( 'id' => $string_id ) );
					}
				}
			}

			$slug_settings['string_name_migrated'] = true;
			$this->sitepress->set_setting( 'posts_slug_translation', $slug_settings, true );
		}
	}

	/**
	 * Move global on/off setting to its own option WPML_ST_Slug_Translation_Settings::KEY_ENABLED_GLOBALLY
	 */
	private function migrate_global_enabled_setting() {
		$enabled = get_option( WPML_ST_Slug_Translation_Settings::KEY_ENABLED_GLOBALLY );

		if ( false === $enabled ) {
			$old_setting = $this->sitepress->get_setting( WPML_ST_Post_Slug_Translation_Settings::KEY_IN_SITEPRESS_SETTINGS );

			if ( array_key_exists( 'on', $old_setting ) ) {
				$enabled = (int) $old_setting['on'];
			} else {
				$enabled = 0;
			}

			update_option( WPML_ST_Slug_Translation_Settings::KEY_ENABLED_GLOBALLY, $enabled );
		}
	}
}
