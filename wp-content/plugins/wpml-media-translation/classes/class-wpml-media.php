<?php

/**
 * Class WPML_Media
 */
class WPML_Media implements IWPML_Action {
	const SETUP_RUN     = 'setup_run';
	const SETUP_STARTED = 'setup_started';

	private static $settings;
	private static $settings_option_key = '_wpml_media';
	private static $default_settings = array(
		'version'                  => false,
		'media_files_localization' => array(
			'posts'         => true,
			'custom_fields' => true,
			'strings'       => true
		),
		'wpml_media_2_3_migration' => true,
		self::SETUP_RUN            => false
	);

	public $languages;
	public $parents;
	public $unattached;
	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var WPML_Media_Menus_Factory
	 */
	private $menus_factory;

	/**
	 * WPML_Media constructor.
	 *
	 * @param SitePress                $sitepress
	 * @param wpdb                     $wpdb
	 * @param WPML_Media_Menus_Factory $menus_factory
	 */
	public function __construct( SitePress $sitepress, wpdb $wpdb, WPML_Media_Menus_Factory $menus_factory ) {
		$this->sitepress     = $sitepress;
		$this->wpdb          = $wpdb;
		$this->menus_factory = $menus_factory;
	}

	public function add_hooks() {
		add_action( 'wpml_loaded', array( $this, 'loaded' ), 2 );
	}

	public static function has_settings() {
		return get_option( self::$settings_option_key );
	}

	public function loaded() {
		global $sitepress;
		if ( ! isset( $sitepress ) || ! $sitepress->get_setting( 'setup_complete' ) ) {
			return null;
		}

		$this->plugin_localization();

		if ( is_admin() ) {
			WPML_Media_Upgrade::run();
		}

		self::init_settings();

		global $sitepress_settings, $pagenow;

		$active_languages = $sitepress->get_active_languages();

		$this->languages = null;

		if ( $this->is_admin_or_xmlrpc() && ! $this->is_uploading_plugin_or_theme() ) {

			add_action( 'wpml_admin_menu_configure', array( $this, 'menu' ) );

			if ( 1 < count( $active_languages ) ) {

				if ( $pagenow == 'media-upload.php' ) {
					add_action( 'pre_get_posts', array( $this, 'filter_media_upload_items' ), 10, 1 );
				}

				if ( $pagenow == 'media.php' ) {
					add_action( 'admin_footer', array( $this, 'media_language_options' ) );
				}

				add_action( 'wp_ajax_wpml_media_scan_prepare', array( $this, 'batch_scan_prepare' ) );

				add_action( 'wp_ajax_find_posts', array( $this, 'find_posts_filter' ), 0 );
			}

		} else {
			if ( WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $sitepress_settings['language_negotiation_type'] ) {
				// Translate media url when in front-end and only when using custom domain
				add_filter( 'wp_get_attachment_url', array( $this, 'wp_get_attachment_url' ), 10, 2 );
			}
		}

		add_filter( 'WPML_filter_link', array( $this, 'filter_link' ), 10, 2 );
		add_filter( 'icl_ls_languages', array( $this, 'icl_ls_languages' ), 10, 1 );

		return null;
	}

	function is_admin_or_xmlrpc() {
		$is_admin  = is_admin();
		$is_xmlrpc = ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST );

		return $is_admin || $is_xmlrpc;
	}

	function is_uploading_plugin_or_theme() {
		global $action;

		return ( isset( $action ) && ( $action == 'upload-plugin' || $action == 'upload-theme' ) );
	}

	function plugin_localization() {
		load_plugin_textdomain( 'wpml-media', false, WPML_MEDIA_FOLDER . '/locale' );
	}

	/**
	 *    Needed by class init and by all static methods that use self::$settings
	 */
	public static function init_settings() {
		if ( ! self::$settings ) {
			self::$settings = get_option( self::$settings_option_key, array() );
		}

		self::$settings = array_merge( self::$default_settings, self::$settings );
	}

	public static function has_setup_run() {
		return self::get_setting( self::SETUP_RUN );
	}

	public static function set_setup_run( $value = 1 ) {
		return self::update_setting( self::SETUP_RUN, $value );
	}

	public static function has_setup_started() {
		return self::get_setting( self::SETUP_STARTED );
	}

	public static function set_setup_started( $value = 1 ) {
		return self::update_setting( self::SETUP_STARTED, $value );
	}

	public static function get_setting( $name, $default = false ) {
		self::init_settings();
		if ( ! isset( self::$settings[ $name ] ) || ! self::$settings[ $name ] ) {
			return $default;
		}

		return self::$settings[ $name ];
	}

	public static function update_setting( $name, $value ) {
		self::init_settings();
		self::$settings[ $name ] = $value;

		return update_option( self::$settings_option_key, self::$settings );
	}

	function batch_scan_prepare() {
		global $wpdb;

		$response = array();
		$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => 'wpml_media_processed' ) );

		$response['message'] = __( 'Started...', 'wpml-media' );

		echo wp_json_encode( $response );
		exit;
	}

	static function is_valid_post_type( $post_type ) {
		global $wp_post_types;

		$post_types = array_keys( (array) $wp_post_types );

		return in_array( $post_type, $post_types );
	}

	function find_posts_filter() {
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	function pre_get_posts( $query ) {
		$query->query['suppress_filters']      = 0;
		$query->query_vars['suppress_filters'] = 0;
	}

	function media_language_options() {
		global $sitepress;
		$att_id       = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
		$translations = $sitepress->get_element_translations( $att_id, 'post_attachment' );
		$current_lang = '';
		foreach ( $translations as $lang => $id ) {
			if ( $id == $att_id ) {
				$current_lang = $lang;
				unset( $translations[ $lang ] );
				break;
			}
		}

		$active_languages = icl_get_languages( 'orderby=id&order=asc&skip_missing=0' );
		$lang_links       = '';

		if ( $current_lang ) {

			$lang_links = '<strong>' . $active_languages[ $current_lang ]['native_name'] . '</strong>';

		}

		foreach ( $translations as $lang => $id ) {
			$lang_links .= ' | <a href="' . admin_url( 'media.php?attachment_id=' . $id . '&action=edit' ) . '">' . $active_languages[ $lang ]['native_name'] . '</a>';
		}


		echo '<div id="icl_lang_options" style="display:none">' . $lang_links . '</div>';
	}

	/**
	 * Synchronizes _wpml_media_* meta fields with all translations
	 *
	 * @param int          $meta_id
	 * @param int          $object_id
	 * @param string       $meta_key
	 * @param string|mixed $meta_value
	 */
	function updated_postmeta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( in_array( $meta_key, array( '_wpml_media_duplicate', '_wpml_media_featured' ) ) ) {
			global $sitepress;
			$el_type      = 'post_' . get_post_type( $object_id );
			$trid         = $sitepress->get_element_trid( $object_id, $el_type );
			$translations = $sitepress->get_element_translations( $trid, $el_type, true, true );
			foreach ( $translations as $translation ) {
				if ( $translation->element_id != $object_id ) {
					$t_meta_value = get_post_meta( $translation->element_id, $meta_key, true );
					if ( $t_meta_value != $meta_value ) {
						update_post_meta( $translation->element_id, $meta_key, $meta_value );
					}
				}
			}
		}
	}

	/**
	 *Add a filter to fix the links for attachments in the language switcher so
	 *they point to the corresponding pages in different languages.
	 */
	function filter_link( $url, $lang_info ) {
		return $url;
	}

	function wp_get_attachment_url( $url, $post_id ) {
		global $sitepress;

		return $sitepress->convert_url( $url );
	}

	function icl_ls_languages( $w_active_languages ) {
		static $doing_it = false;

		if ( is_attachment() && ! $doing_it ) {
			$doing_it = true;
			// Always include missing languages.
			$w_active_languages = icl_get_languages( 'skip_missing=0' );
			$doing_it           = false;
		}

		return $w_active_languages;
	}

	function get_post_metadata( $value, $object_id, $meta_key, $single ) {
		if ( $meta_key == '_thumbnail_id' ) {

			global $wpdb;

			$thumbnail_prepared = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s", array(
				$object_id,
				$meta_key
			) );
			$thumbnail          = $wpdb->get_var( $thumbnail_prepared );

			if ( $thumbnail == null ) {
				// see if it's available in the original language.

				$post_type_prepared = $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", array( $object_id ) );
				$post_type          = $wpdb->get_var( $post_type_prepared );
				$trid_prepared      = $wpdb->prepare( "SELECT trid, source_language_code FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type = %s", array(
					$object_id,
					'post_' . $post_type
				) );
				$trid               = $wpdb->get_row( $trid_prepared );
				if ( $trid ) {

					global $sitepress;

					$translations = $sitepress->get_element_translations( $trid->trid, 'post_' . $post_type );
					if ( isset( $translations[ $trid->source_language_code ] ) ) {
						$translation = $translations[ $trid->source_language_code ];
						// see if the original has a thumbnail.
						$thumbnail_prepared = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s", array(
							$translation->element_id,
							$meta_key
						) );
						$thumbnail          = $wpdb->get_var( $thumbnail_prepared );
						if ( $thumbnail ) {
							$value = $thumbnail;
						}
					}
				}
			} else {
				$value = $thumbnail;
			}

		}

		return $value;
	}

	/**
	 * @param string $menu_id
	 */
	public function menu( $menu_id ) {
		if ( 'WPML' !== $menu_id ) {
			return;
		}

		$menu_label         = __( 'Media Translation', 'wpml-media' );
		$menu               = array();
		$menu['order']      = 600;
		$menu['page_title'] = $menu_label;
		$menu['menu_title'] = $menu_label;
		$menu['capability'] = 'edit_others_posts';
		$menu['menu_slug']  = 'wpml-media';
		$menu['function']   = array( $this, 'menu_content' );

		do_action( 'wpml_admin_menu_register_item', $menu );
	}

	public function menu_content() {
		$menus = $this->menus_factory->create();
		$menus->display();
	}

	/**
	 * @param $ids
	 * @param $target_language
	 *
	 * @return array|string
	 */
	public function translate_attachment_ids( $ids, $target_language ) {
		global $sitepress;
		$return_string = false;
		if ( ! is_array( $ids ) ) {
			$attachment_ids = explode( ',', $ids );
			$return_string  = true;
		}

		$translated_ids = array();
		if ( ! empty( $attachment_ids ) ) {
			foreach ( $attachment_ids as $attachment_id ) {
				//Fallback to the original ID
				$translated_id = $attachment_id;

				//Find the ID translation
				$trid = $sitepress->get_element_trid( $attachment_id, 'post_attachment' );
				if ( $trid ) {
					$id_translations = $sitepress->get_element_translations( $trid, 'post_attachment', false, true );
					foreach ( $id_translations as $language_code => $id_translation ) {
						if ( $language_code == $target_language ) {
							$translated_id = $id_translation->element_id;
							break;
						}
					}
				}

				$translated_ids[] = $translated_id;
			}
		}

		if ( $return_string ) {
			return implode( ',', $translated_ids );
		}

		return $translated_ids;

	}

	/**
	 * Update query for media-upload.php page.
	 *
	 * @param object $query WP_Query
	 */
	public function filter_media_upload_items( $query ) {
		$current_lang = $this->sitepress->get_current_language();
		$ids          = icl_cache_get( '_media_upload_attachments' . $current_lang );

		if ( false === $ids ) {
			$tbl      = $this->wpdb->prefix . 'icl_translations';
			$db_query = "
				SELECT posts.ID
				FROM {$this->wpdb->posts} as posts, $tbl as icl_translations
				WHERE posts.post_type = 'attachment'
				AND icl_translations.element_id = posts.ID
				AND icl_translations.language_code = %s
				";

			$posts = $this->wpdb->get_results( $this->wpdb->prepare( $db_query, $current_lang ) );
			$ids   = array();
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post ) {
					$ids[] = absint( $post->ID );
				}
			}

			icl_cache_set( '_media_upload_attachments' . $current_lang, $ids );
		}

		$query->set( 'post__in', $ids );
	}

}
