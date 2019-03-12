<?php

class WCML_Url_Translation {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var Sitepress */
	private $sitepress;
	/** @var wpdb */
	private $wpdb;

	public $default_product_base;
	public $default_product_category_base;
	public $default_product_category_gettext_base;
	public $default_product_tag_base;
	public $default_product_tag_gettext_base;
	public $wc_permalinks;

	/**
	 * WCML_Url_Translation constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 * @param wpdb $wpdb
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb )
	{
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;

		$this->default_product_base                  = 'product';
		$this->default_product_category_base         = 'product-category';
		$this->default_product_tag_base              = 'product-tag';
		$this->default_product_category_gettext_base = _x( 'product-category', 'slug', 'woocommerce' );
		$this->default_product_tag_gettext_base      = _x( 'product-tag', 'slug', 'woocommerce' );
	}

	function set_up() {

		$this->wc_permalinks = get_option( 'woocommerce_permalinks' );

		add_filter( 'pre_update_option_woocommerce_permalinks', array(
			$this,
			'register_product_and_taxonomy_bases'
		), 10, 2 );

		if ( ! is_admin() ) {
			add_filter( 'option_woocommerce_permalinks', array(
				$this,
				'use_untranslated_default_url_bases'
			), 1, 1 ); // avoid using the _x translations
			add_action( 'init', array(
				$this,
				'fix_post_object_rewrite_slug'
			), 6 ); // handle the particular case of the default product base: wpmlst-540
		}

		add_filter( 'pre_update_option_rewrite_rules', array(
			$this,
			'force_bases_in_strings_languages'
		), 1, 1 ); // high priority
		add_filter( 'option_rewrite_rules', array( $this, 'translate_bases_in_rewrite_rules' ), 0, 1 ); // high priority
		add_filter( 'term_link', array( $this, 'translate_taxonomy_base' ), 0, 3 ); // high priority

		add_action( 'wp_ajax_wcml_update_base_translation', array( $this, 'wcml_update_base_translation' ) );
		add_filter( 'redirect_canonical', array( $this, 'check_wc_tax_url_on_redirect' ), 10, 2 );
		add_filter( 'query_vars', array( $this, 'translate_query_var_for_product' ) );
		add_filter( 'wp_redirect', array( $this, 'encode_shop_slug' ), 10, 2 );
		add_action( 'switch_blog', array( $this, 'maybe_remove_query_vars_filter' ) );

		add_filter( 'post_type_link', array( $this, 'translate_product_post_type_link' ), 10, 2 );

		if ( empty( $this->woocommerce_wpml->settings['url_translation_set_up'] ) ) {

			$this->clean_up_product_and_taxonomy_bases();

			//set translate product by default
			$this->translate_product_base();

			$this->register_product_and_taxonomy_bases();

			$this->woocommerce_wpml->settings['url_translation_set_up'] = 1;
			$this->woocommerce_wpml->update_settings();
		}

	}

	function clean_up_product_and_taxonomy_bases() {

		$base = $this->get_woocommerce_product_base();

		//delete other old product bases
		$this->wpdb->query( "DELETE FROM {$this->wpdb->prefix}icl_strings WHERE context = 'WordPress' AND value != '" . trim( $base, '/' ) . "' AND name LIKE 'URL slug:%' " );

		//update name for current base

		$this->wpdb->update(
			$this->wpdb->prefix . 'icl_strings',
			array(
				'context' => 'WordPress',
				'name'    => 'URL slug: product'
			),
			array(
				'context' => 'WordPress',
				'name'    => sprintf( 'Url slug: %s', trim( $base, '/' ) )
			)
		);

		$woocommerce_permalinks = maybe_unserialize( get_option( 'woocommerce_permalinks' ) );

		foreach ( (array) $woocommerce_permalinks as $base_key => $base ) {

			$base_key = trim( $base_key, '/' );

			$taxonomy = false;

			switch ( $base_key ) {
				case 'category_base':
					$taxonomy = 'product_cat';
					break;
				case 'tag_base':
					$taxonomy = 'product_tag';
					break;
				case 'attribute_base':
					$taxonomy = 'attribute';
					break;
			}

			if ( $taxonomy ) {
				$this->wpdb->query( "DELETE FROM {$this->wpdb->prefix}icl_strings WHERE context LIKE '" . sprintf( 'URL %s slugs - ', $taxonomy ) . "%'" );
			}

		}

	}

	function fix_post_object_rewrite_slug() {
		global $wp_post_types, $wp_rewrite;

		if ( empty( $this->wc_permalinks['product_base'] ) ) {
			$wp_post_types['product']->rewrite['slug'] = 'product';
			if ( empty( $wp_rewrite->extra_permastructs['product']['struct'] ) ) {
				$wp_rewrite->extra_permastructs['product']['struct'] = '/product/%product%';
			}
		}
	}

	function url_strings_context() {
		return 'WordPress';
	}

	public function url_string_name( $type, $value = '' ) {

		$name = '';

		switch ( $type ) {
			case 'product':
				$name = sprintf( 'URL slug: %s', $type );
				break;
			case 'product_cat':
			case 'product_tag':
			case 'attribute':
				$name = sprintf( 'URL %s tax slug', $type );
				break;
			case 'attribute_slug':
				$name = sprintf( 'URL attribute slug: %s', $value );
				break;
		}

		return $name;
	}

	function translate_product_base() {

		if ( ! defined( 'WOOCOMMERCE_VERSION' ) || ( ! isset( $GLOBALS['ICL_Pro_Translation'] ) || is_null( $GLOBALS['ICL_Pro_Translation'] ) ) ) {
			return;
		}

		$slug = $this->get_woocommerce_product_base();

		if ( version_compare( WPML_ST_VERSION, '2.2.6', '>' ) ) {

			// Use new API for WPML ST > 2.2.6
			do_action( 'wpml_activate_slug_translation', 'product', $slug );

		} else {

			// force_product_slug_translation_on
			$iclsettings = $this->sitepress->get_settings();
			if ( empty( $iclsettings['posts_slug_translation']['on'] ) || empty( $iclsettings['posts_slug_translation']['types']['product'] ) ) {
				$iclsettings['posts_slug_translation']['on']               = 1;
				$iclsettings['posts_slug_translation']['types']['product'] = 1;
				$this->sitepress->save_settings( $iclsettings );
			}

			$string = icl_get_string_id( $slug, $this->url_strings_context(), $this->url_string_name( 'product' ) );
			if ( ! $string ) {
				do_action( 'wpml_register_single_string', $this->url_strings_context(), $this->url_string_name( 'product' ), trim( $slug, '/' ) );
			}

		}

	}

	function get_woocommerce_product_base() {

		if ( isset( $this->wc_permalinks['product_base'] ) && ! empty( $this->wc_permalinks['product_base'] ) ) {
			return trim( $this->wc_permalinks['product_base'], '/' );
		} elseif ( get_option( 'woocommerce_product_slug' ) != false ) {
			return trim( get_option( 'woocommerce_product_slug' ), '/' );
		} else {
			return $this->default_product_base; // the default WooCommerce value. Before permalinks options are saved
		}

	}

	function register_product_and_taxonomy_bases( $value = false, $old_value = false ) {

		if ( empty( $value ) ) {
			$permalink_options = $this->wc_permalinks;
		} else {
			$permalink_options = $value;
		}

		// products
		$product_base = ! empty( $permalink_options['product_base'] ) ? trim( $permalink_options['product_base'], '/' ) : $this->default_product_base;
		$name         = $this->url_string_name( 'product' );

		$string_language = $this->woocommerce_wpml->strings->get_string_language( $product_base, $this->url_strings_context(), $name );
		if ( is_null( $string_language ) ) {
			$string_language = '';
		}
		do_action( 'wpml_register_single_string', $this->url_strings_context(), $name, $product_base, false, $string_language );

		if ( isset( $_POST['product_base_language'] ) ) {
			$this->woocommerce_wpml->strings->set_string_language( $product_base, $this->url_strings_context(), $name, $_POST['product_base_language'] );
		}

		if ( $product_base == $this->default_product_base ) {
			$this->add_default_slug_translations( $product_base, $name );
		}

		// categories
		$category_base = ! empty( $permalink_options['category_base'] ) ? $permalink_options['category_base'] : $this->default_product_category_base;
		$name          = $this->url_string_name( 'product_cat' );

		$string_language = $this->woocommerce_wpml->strings->get_string_language( $category_base, $this->url_strings_context(), $name );
		if ( is_null( $string_language ) ) {
			$string_language = '';
		}
		do_action( 'wpml_register_single_string', $this->url_strings_context(), $name, $category_base, false, $string_language );

		if ( isset( $_POST['category_base_language'] ) ) {
			$this->woocommerce_wpml->strings->set_string_language( $category_base, $this->url_strings_context(), $name, $_POST['category_base_language'] );
		}

		if ( $category_base == $this->default_product_category_base ) {
			$this->add_default_slug_translations( $category_base, $name );
		}

		// tags
		$tag_base = ! empty( $permalink_options['tag_base'] ) ? $permalink_options['tag_base'] : $this->default_product_tag_base;
		$name     = $this->url_string_name( 'product_tag' );

		$string_language = $this->woocommerce_wpml->strings->get_string_language( $tag_base, $this->url_strings_context(), $name );
		if ( is_null( $string_language ) ) {
			$string_language = '';
		}
		do_action( 'wpml_register_single_string', $this->url_strings_context(), $name, $tag_base, false, $string_language );

		if ( isset( $_POST['tag_base_language'] ) ) {
			$this->woocommerce_wpml->strings->set_string_language( $tag_base, $this->url_strings_context(), $name, $_POST['tag_base_language'] );
		}

		if ( $tag_base == $this->default_product_tag_base ) {
			$this->add_default_slug_translations( $tag_base, $name );
		}


		if ( isset( $permalink_options['attribute_base'] ) && $permalink_options['attribute_base'] ) {
			$attr_base = trim( $permalink_options['attribute_base'], '/' );

			$string_language = $this->woocommerce_wpml->strings->get_string_language( $attr_base, $this->url_strings_context(), $name );
			if ( is_null( $string_language ) ) {
				$string_language = '';
			}
			do_action( 'wpml_register_single_string', $this->url_strings_context(), $this->url_string_name( 'attribute' ), $attr_base, false, $string_language );

			if ( isset( $_POST['attribute_base_language'] ) ) {
				$this->woocommerce_wpml->strings->set_string_language( $attr_base, $this->url_strings_context(), $this->url_string_name( 'attribute' ), $_POST['attribute_base_language'] );
			}
		}

		return $value;
	}

	/**
	 * @param string $permalinks
	 *
	 * @return mixed
	 */
	function use_untranslated_default_url_bases( $permalinks ) {

		// exception (index.php in WP permalink structure) #wcml-1939
		if ( preg_match( '#^/?index\.php/#', get_option( 'permalink_structure' ) ) ) {
			return $permalinks;
		}

		if ( empty( $permalinks['product_base'] ) ) {
			$permalinks['product_base'] = $this->default_product_base;
		}
		if ( empty( $permalinks['category_base'] ) ) {
			$permalinks['category_base'] = $this->default_product_category_base;
		}
		if ( empty( $permalinks['tag_base'] ) ) {
			$permalinks['tag_base'] = $this->default_product_tag_base;
		}

		return $permalinks;
	}

	function add_default_slug_translations( $slug, $name ) {

		$string_id       = icl_get_string_id( $slug, $this->url_strings_context(), $name );
		$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->url_strings_context(), $name );

		// will use a filter in the future wpmlst-529
		$string_object               = new WPML_ST_String( $string_id, $this->wpdb );
		$string_translation_statuses = $string_object->get_translation_statuses();

		foreach ( $string_translation_statuses as $s ) {
			$string_translations[ $s->language ] = $s->status;
		}

		$languages = $this->sitepress->get_active_languages();

		foreach ( $languages as $language => $language_info ) {

			if ( $language != $string_language ) {

				// check if there's an existing translation
				if ( ! isset( $string_translations[ $language ] ) ) {

					$slug_translation = $this->woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( $slug, $language, false );

					if ( $slug_translation ) {
						// add string translation
						icl_add_string_translation( $string_id, $language, $slug_translation, ICL_STRING_TRANSLATION_COMPLETE );
					}

				}

			}
		}

	}

	function force_bases_in_strings_languages( $value ) {

		if ( $value && $this->sitepress->get_current_language() != 'en' ) {

			remove_filter( 'gettext_with_context', array(
				$this->woocommerce_wpml->strings,
				'category_base_in_strings_language'
			), 99, 3 );
			$taxonomies = array(
				'product_cat' => array(
					'base'            => 'category_base',
					'base_translated' => apply_filters(
						'wpml_translate_single_string',
						'product-category',
						$this->url_strings_context(),
						$this->url_string_name( 'product_cat' )
					),
					'default'         => $this->default_product_category_base
				),
				'product_tag' => array(
					'base'            => 'tag_base',
					'base_translated' => apply_filters(
						'wpml_translate_single_string',
						'product-tag',
						$this->url_strings_context(),
						$this->url_string_name( 'product_tag' )
					),
					'default'         => $this->default_product_tag_base
				),
			);
			add_filter( 'gettext_with_context', array(
				$this->woocommerce_wpml->strings,
				'category_base_in_strings_language'
			), 99, 3 );
			foreach ( $taxonomies as $taxonomy => $taxonomy_details ) {

				if ( empty( $this->wc_permalinks[ $taxonomy_details['base'] ] ) && $value ) {

					$new_value = array();
					foreach ( $value as $k => $v ) {
						$k               = preg_replace( "#" . $taxonomy_details['base_translated'] . "/#", $taxonomy_details['default'] . '/', $k );
						$new_value[ $k ] = $v;
					}
					$value = $new_value;
					unset( $new_value );

				}

			}

		}

		return $value;

	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	function translate_bases_in_rewrite_rules( $value ) {

		if ( ! empty( $value ) ) {
			$value = $this->translate_wc_default_taxonomies_bases_in_rewrite_rules( $value );
			$value = $this->translate_attributes_bases_in_rewrite_rules( $value );
			$value = $this->translate_shop_page_base_in_rewrite_rules( $value );
		}

		return $value;
	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public function translate_wc_default_taxonomies_bases_in_rewrite_rules( $value ){
		$taxonomies = array( 'product_cat', 'product_tag' );

		foreach ( $taxonomies as $taxonomy ) {
			$slug_details = $this->get_translated_tax_slug( $taxonomy );

			$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug_details['slug'], $this->url_strings_context(), $this->url_string_name( $taxonomy ) );
			if ( $this->sitepress->get_current_language() == $string_language ) {
				continue;
			}

			if ( $slug_details ) {

				$slug_match             = addslashes( ltrim( $slug_details['slug'], '/' ) );
				$slug_translation_match = ltrim( $slug_details['translated_slug'], '/' );

				$buff_value = array();

				foreach ( (array) $value as $k => $v ) {

					if ( $slug_details['slug'] != $slug_details['translated_slug'] && preg_match( '#(^|^/)' . $slug_match . '/#', $k ) ) {
						$k = preg_replace( '#(^|^/)' . $slug_match . '/#', '$1'. $slug_translation_match . '/', $k );
					}

					$buff_value[ $k ] = $v;
				}
				$value = $buff_value;
				unset( $buff_value );
			}

		}

		return $value;
	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public function translate_attributes_bases_in_rewrite_rules( $value ) {

		// handle attributes
		$wc_taxonomies           = wc_get_attribute_taxonomies();
		$wc_taxonomies_wc_format = array();
		foreach ( $wc_taxonomies as $k => $v ) {
			$wc_taxonomies_wc_format[] = 'pa_' . $v->attribute_name;
		}

		foreach ( $wc_taxonomies_wc_format as $taxonomy ) {
			$taxonomy_obj = get_taxonomy( $taxonomy );

			if ( isset( $taxonomy_obj->rewrite['slug'] ) ) {
				$exp  = explode( '/', trim( $taxonomy_obj->rewrite['slug'], '/' ) );
				$slug = join( '/', array_slice( $exp, 0, count( $exp ) - 1 ) );

				$attribute_slug = preg_replace( "#^$slug/#", '', $taxonomy_obj->rewrite['slug'] );
			}

			if ( isset( $slug ) ) {
				$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->url_strings_context(), $this->url_string_name( 'attribute' ) );

				if ( $this->sitepress->get_current_language() != $string_language ) {

					$slug_translation = apply_filters( 'wpml_translate_single_string', $slug, $this->url_strings_context(), $this->url_string_name( 'attribute' ) );

					$attribute_slug_translation = apply_filters( 'wpml_translate_single_string', $attribute_slug, $this->url_strings_context(), $this->url_string_name( 'attribute_slug', $attribute_slug ) );

					if ( $slug && $slug_translation && $slug !== $slug_translation ) {

						$slug_match             = addslashes( ltrim( $slug, '/' ) );
						$slug_translation_match = ltrim( $slug_translation, '/' );

						$pattern             = '#^' . $slug_match . '/(.*)#';
						$replacement_pattern = '#^' . $slug_match . '/(' . $attribute_slug . ')/(.*)#';
						$replacement         = $slug_translation_match . '/' . $attribute_slug_translation . '/$2';

						$value = $this->replace_bases_in_rewrite_rules( $value, $pattern, $replacement_pattern, $replacement );

					} elseif ( $attribute_slug_translation && $attribute_slug !== $attribute_slug_translation ) {

						$slug_match  = addslashes( ltrim( $attribute_slug, '/' ) );
						$pattern     = "#(^|\/)" . $slug_match . "/(.*)#";
						$replacement = '$1'.$attribute_slug_translation . '/$2';

						$value = $this->replace_bases_in_rewrite_rules( $value, $pattern, $pattern, $replacement );

					}

				}
			}

		}

		return $value;

	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public function translate_shop_page_base_in_rewrite_rules( $value ){
		//filter shop page rewrite slug
		$current_shop_id = wc_get_page_id( 'shop' );
		$default_shop_id = apply_filters( 'translate_object_id', $current_shop_id, 'page', true, $this->sitepress->get_default_language() );

		if ( is_null( get_post( $current_shop_id ) ) || is_null( get_post( $default_shop_id ) ) ) {
			return $value;
		}

		$current_slug = get_post( $current_shop_id )->post_name;
		$default_slug = get_post( $default_shop_id )->post_name;

		if ( $current_slug != $default_slug ) {
			$buff_value = array();
			foreach ( (array) $value as $k => $v ) {

				if ( preg_match( '#^' . $default_slug . '/\?\$$#', $k ) ||
				     preg_match( '#^' . $default_slug . '/\(?feed#', $k ) ||
				     preg_match( '#^' . $default_slug . '/page#', $k )
				) {

					$k = preg_replace( '#^' . $default_slug . '/#', $current_slug . '/', $k );
				}

				$buff_value[ $k ] = $v;
			}

			$value = $buff_value;
			unset( $buff_value );
		}

		return $value;
	}

	/**
	 * @param array $value
	 * @param string $pattern
	 * @param string $replacement_pattern
	 * @param string $replacement
	 *
	 * @return array
	 */
	public function replace_bases_in_rewrite_rules( $value, $pattern, $replacement_pattern, $replacement ){

		$buff_value = array();
		foreach ( (array) $value as $k => $v ) {
			if ( preg_match( $pattern , $k ) ) {
				$k = preg_replace( $replacement_pattern, $replacement, $k );
			}
			$buff_value[ $k ] = $v;
		}

		$value = $buff_value;
		unset( $buff_value );

		return $value;
	}

	function translate_taxonomy_base( $termlink, $term, $taxonomy ) {
		global $wp_rewrite, $wpml_term_translations;
		static $no_recursion_flag;

		// handles product categories, product tags and attributes

		$wc_taxonomies = wc_get_attribute_taxonomies();
		foreach ( $wc_taxonomies as $k => $v ) {
			$wc_taxonomies_wc_format[] = 'pa_' . $v->attribute_name;
		}

		if ( ( $taxonomy == 'product_cat' || $taxonomy == 'product_tag' || ( ! empty( $wc_taxonomies_wc_format ) && in_array( $taxonomy, $wc_taxonomies_wc_format ) ) ) && ! $no_recursion_flag ) {

			$cache_key = 'termlink#' . $taxonomy . '#' . $term->term_id;
			if ( $link = wp_cache_get( $cache_key, 'terms' ) ) {
				$termlink = $link;
			} else {

				$no_recursion_flag = false;

				if ( ! is_null( $wpml_term_translations ) ) {
					$term_language = $term->term_id ? $wpml_term_translations->get_element_lang_code( $term->term_taxonomy_id ) : false;
				} else {
					$term_language = $term->term_id ? $this->sitepress->get_language_for_element( $term->term_taxonomy_id, 'tax_' . $taxonomy ) : false;
				}

				if ( $term_language ) {

					$slug_details = $this->get_translated_tax_slug( $taxonomy, $term_language );

					$base            = $slug_details['slug'];
					$base_translated = $slug_details['translated_slug'];

					if ( isset( $wp_rewrite->extra_permastructs[ $taxonomy ] ) ) {

						$buff = $wp_rewrite->extra_permastructs[ $taxonomy ]['struct'];

						if( $base_translated !== $base ){
							// translate the attribute base
							$wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] = preg_replace( '#^' . $base . '/(.*)#', $base_translated . '/$1', $wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] );
						}

						// translate the attribute slug
						$attribute_slug             = preg_replace( '#^' . $base . '/([^/]+)/.+$#', '$1', $wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] );
						$attribute_slug_default     = preg_replace( '#^pa_#', '', $taxonomy );
						$attribute_slug_translation = apply_filters(
							'wpml_translate_single_string',
							$attribute_slug,
							$this->url_strings_context(),
							$this->url_string_name( 'attribute_slug', $attribute_slug_default ),
							$term_language
						);

						if ( $attribute_slug_translation != $attribute_slug ) {

							if( $base ){
								$wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] = preg_replace(
									'#^' . $base_translated . '/([^/]+)/(.+)$#',
									$base_translated . '/' . $attribute_slug_translation . '/$2',
									$wp_rewrite->extra_permastructs[ $taxonomy ]['struct']
								);
							}else{
								$wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] = preg_replace(
									'#' . $attribute_slug_default . '/(.+)$#',
									$attribute_slug_translation . '/$1',
									$wp_rewrite->extra_permastructs[ $taxonomy ]['struct']
								);
							}
						}

						$no_recursion_flag = true;
						$termlink          = get_term_link( $term, $taxonomy );

						$wp_rewrite->extra_permastructs[ $taxonomy ]['struct'] = $buff;

					}

				}

				$no_recursion_flag = false;

				wp_cache_add( $cache_key, $termlink, 'terms', 0 );
			}

		}

		return $termlink;
	}

	function get_translated_tax_slug( $taxonomy, $language = false ) {

		switch ( $taxonomy ) {
			case 'product_tag':

				if ( ! empty( $this->wc_permalinks['tag_base'] ) ) {
					$slug = $gettext_slug = trim( $this->wc_permalinks['tag_base'], '/' );
				} else {
					$slug = 'product-tag';
				}

				$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->url_strings_context(), $this->url_string_name( $taxonomy ) );

				break;

			case 'product_cat':

				if ( ! empty( $this->wc_permalinks['category_base'] ) ) {
					$slug = $gettext_slug = trim( $this->wc_permalinks['category_base'], '/' );
				} else {
					$slug = 'product-category';
				}

				$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->url_strings_context(), $this->url_string_name( $taxonomy ) );

				break;

			default:
				$slug = trim( $this->wc_permalinks['attribute_base'], '/' );

				$string_language = $this->woocommerce_wpml->strings->get_string_language( $slug, $this->url_strings_context(), $this->url_string_name( 'attribute' ) );

				$taxonomy = 'attribute';

				break;
		}

		if ( ! $language ) {
			$language = $this->sitepress->get_current_language();
		}

		if ( $slug && $language != 'all' && $language != $string_language ) {

			$slug_translation = apply_filters( 'wpml_translate_single_string', $slug, $this->url_strings_context(), $this->url_string_name( $taxonomy ), $language, false );

			return array(
				'slug'            => $slug,
				'translated_slug' => $slug_translation
			);
		}

		return array(
			'slug'            => $slug,
			'translated_slug' => $slug
		);

	}

	function get_base_translation( $base, $language ) {

		// case of attribute slugs
		if ( strpos( $base, 'attribute_slug-' ) === 0 ) {
			$slug = preg_replace( '#^attribute_slug-#', '', $base );
			$base = 'attribute_slug';
		}

		switch ( $base ) {
			case 'product':
				$slug           = $this->get_woocommerce_product_base();
				$return['name'] = __( 'Product Base', 'woocommerce-multilingual' );
				break;

			case 'product_tag':
				$slug           = ! empty( $this->wc_permalinks['tag_base'] ) ? trim( $this->wc_permalinks['tag_base'], '/' ) : 'product-tag';
				$return['name'] = __( 'Product Tag Base', 'woocommerce-multilingual' );
				break;

			case 'product_cat':
				$slug           = ! empty( $this->wc_permalinks['category_base'] ) ? trim( $this->wc_permalinks['category_base'], '/' ) : 'product-category';
				$return['name'] = __( 'Product Category Base', 'woocommerce-multilingual' );
				break;

			case 'attribute':
				$slug           = trim( $this->wc_permalinks['attribute_base'], '/' );
				$return['name'] = __( 'Product Attribute Base', 'woocommerce-multilingual' );
				break;

			case 'attribute_slug':
				$return['name'] = __( 'Attribute Slug', 'woocommerce-multilingual' );
				$string_id      = icl_get_string_id( $slug, $this->url_strings_context(), $this->url_string_name( $base, $slug ) );
				break;

			default:
				$endpoints      = WC()->query->query_vars;
				$slug           = isset( $endpoints[ $base ] ) ? $endpoints[ $base ] : false;
				$return['name'] = sprintf( __( 'Endpoint: %s', 'woocommerce-multilingual' ), $base );
				$string_id      = icl_get_string_id( $slug, 'WooCommerce Endpoints', $base );
				break;
		}

		$return['original_value'] = $slug;
		if ( ! isset( $string_id ) ) {
			$string_id = icl_get_string_id( $slug, $this->url_strings_context(), $this->url_string_name( $base ) );
		}
		$base_translations = icl_get_string_translations_by_id( $string_id );

		$return['translated_base'] = '';
		if ( isset( $base_translations[ $language ] ) ) {
			if ( $base_translations[ $language ]['status'] == ICL_TM_COMPLETE ) {
				$return['translated_base'] = $base_translations[ $language ]['value'];
			} elseif ( $base_translations[ $language ]['status'] == ICL_TM_NEEDS_UPDATE ) {
				$return['translated_base'] = $base_translations[ $language ]['value'];
				$return['needs_update ']   = true;
			}
		}

		return $return;

	}

	function get_source_slug_language( $base ) {

		if ( $base == 'shop' ) {
			$source_language = $this->sitepress->get_language_for_element( get_option( 'woocommerce_shop_page_id' ), 'post_page' );
		} elseif ( in_array( $base, array( 'product', 'product_cat', 'product_tag', 'attribute' ) ) ) {
			$source_language = $this->woocommerce_wpml->strings->get_string_language( $base, $this->url_strings_context(), $this->url_string_name( $base ) );
		} else {
			$source_language = $this->woocommerce_wpml->strings->get_string_language( $base, 'WooCommerce Endpoints', $base );
		}

		return $source_language;
	}

	function wcml_update_base_translation() {

		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_update_base_translation' ) ) {
			die( 'Invalid nonce' );
		}

		$original_base       = $_POST['base'];
		$original_base_value = $_POST['base_value'];
		$base_translation    = $_POST['base_translation'];
		$language            = $_POST['language'];

		if ( $original_base == 'shop' ) {
			$original_shop_id   = get_option( 'woocommerce_shop_page_id' );
			$translated_shop_id = apply_filters( 'translate_object_id', $original_shop_id, 'page', false, $language );

			if ( ! is_null( $translated_shop_id ) ) {

				$trnsl_shop_obj = get_post( $translated_shop_id );
				$new_slug       = wp_unique_post_slug( sanitize_title( $_POST['base_translation'] ), $translated_shop_id, $trnsl_shop_obj->post_status, $trnsl_shop_obj->post_type, $trnsl_shop_obj->post_parent );
				$this->wpdb->update( $this->wpdb->posts, array( 'post_name' => $new_slug ), array( 'ID' => $translated_shop_id ) );

			}

		} else {
			if ( in_array( $original_base, array( 'product', 'product_cat', 'product_tag', 'attribute' ) ) ) {
				$string_id = icl_get_string_id( $original_base_value, $this->url_strings_context(), $this->url_string_name( $original_base ) );
			} elseif ( strpos( $original_base, 'attribute_slug-' ) === 0 ) {
				$slug = preg_replace( '#^attribute_slug-#', '', $original_base );
				do_action( 'wpml_register_single_string', $this->url_strings_context(), $this->url_string_name( 'attribute_slug', $slug ), $slug );
				$string_id = icl_get_string_id( $original_base_value, $this->url_strings_context(), $this->url_string_name( 'attribute_slug', $slug ) );
			} else {
				$string_id = icl_get_string_id( $original_base_value, 'WooCommerce Endpoints', $original_base );
				if ( ! $string_id && function_exists( 'icl_register_string' ) ) {
					$string_id = icl_register_string( 'WooCommerce Endpoints', $original_base, $original_base_value );
				}
				$this->woocommerce_wpml->endpoints->add_endpoints();
				$this->woocommerce_wpml->endpoints->flush_rules_for_endpoints_translations();
			}

			icl_add_string_translation( $string_id, $language, $base_translation, ICL_STRING_TRANSLATION_COMPLETE );

		}

		$edit_base = new WCML_Store_URLs_Edit_Base_UI( $original_base, $language, $this->woocommerce_wpml, $this->sitepress );
		$html      = $edit_base->get_view();

		echo json_encode( $html );
		die();


	}

	// return correct redirect URL for WC standard taxonomies when pretty permalink uses with lang as parameter in WPML
	function check_wc_tax_url_on_redirect( $redirect_url, $requested_url ) {
		global $wp_query;

		if ( is_tax() ) {
			$original = @parse_url( $requested_url );
			if ( isset( $original['query'] ) ) {
				parse_str( $original['query'], $query_args );
				if ( ( isset( $query_args['product_cat'] ) || isset( $query_args['product_tag'] ) ) && isset ( $query_args['lang'] ) ) {
					$obj     = $wp_query->get_queried_object();
					$tax_url = get_term_link( (int) $obj->term_id, $obj->taxonomy );

					return $tax_url;
				}
			}
		}

		return $redirect_url;
	}

	public function translate_query_var_for_product( $public_query_vars ) {

		$product_permalink = $this->woocommerce_wpml->strings->product_permalink_slug();
		$string_language   = $this->woocommerce_wpml->strings->get_string_language( $product_permalink, $this->url_strings_context(), $this->url_string_name( 'product' ) );

		if ( $this->sitepress->get_current_language() != $string_language ) {
			$translated_slug = $this->get_translated_product_base_by_lang( false, $product_permalink );

			if ( isset( $_GET[ $translated_slug ] ) ) {
				$buff = $_GET[ $translated_slug ];
				unset( $_GET[ $translated_slug ] );
				$_GET[ $product_permalink ] = $buff;
			}

		}

		return $public_query_vars;
	}

	public function maybe_remove_query_vars_filter() {
		if ( !is_plugin_active( basename( $this->sitepress->get_wp_api()->constant( 'WPML_ST_PATH' ) ) . '/plugin.php' ) ) {
			remove_filter( 'query_vars', array( $this, 'translate_query_var_for_product' ) );
		}
	}

	public function get_translated_product_base_by_lang( $language = false, $product_slug = false ) {

		if ( ! $language ) {
			$language = $this->sitepress->get_current_language();
		}

		if ( ! $product_slug ) {
			$product_slug = $this->woocommerce_wpml->strings->product_permalink_slug();
		}

		if ( version_compare( WPML_ST_VERSION, '2.3', '>=' ) ) {
			$translated_slug = apply_filters( 'wpml_get_translated_slug', $product_slug, 'product', $language );
		} elseif ( apply_filters( 'wpml_slug_translation_available', false ) ) {
			$translated_slug = apply_filters( 'wpml_get_translated_slug', 'product', $language );
		} else {
			$translated_slug = apply_filters( 'wpml_translate_single_string', $product_slug, $this->url_strings_context(), $this->url_string_name( 'product' ) );
		}

		return $translated_slug;
	}

	public function encode_shop_slug( $location, $status ) {
		if ( get_post_type( get_query_var( 'p' ) ) == 'product' ) {
			$language  = $this->sitepress->get_language_for_element( get_query_var( 'p' ), 'post_product' );
			$base_slug = $this->get_translated_product_base_by_lang( $language );

			$location = str_replace( $base_slug, urlencode( $base_slug ), $location );
		}

		return $location;
	}

	public function translate_product_post_type_link( $permalink, $post ) {

		// Abort if post is not a product or permalink don't have 'uncategorized' flag
		if ( 'product' !== $post->post_type || false === strpos( $permalink, '/uncategorized/' ) ) {
			return $permalink;
		}

		$permalinks = wc_get_permalink_structure();

		// Make sure the product permalink have %product_cat% flag.
		if ( preg_match( '`/(.+)(/%product_cat%)`', $permalinks['product_rewrite_slug'], $matches ) ) {
			$find             = 'uncategorized';
			$element_language = $this->sitepress->get_language_for_element( $post->ID, 'post_product' );
			$replace          = $this->woocommerce_wpml->strings->get_translation_from_woocommerce_mo_file( 'slug' . chr( 4 ) . $find, $element_language, false );

			if ( ! is_null( $replace ) ) {
				$permalink = str_replace( $find, $replace, $permalink );
			}
		}

		return $permalink;
	}

}