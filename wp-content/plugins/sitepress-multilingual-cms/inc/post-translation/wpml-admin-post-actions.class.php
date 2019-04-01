<?php

/**
 * Class WPML_Admin_Post_Actions
 *
 * @package    wpml-core
 * @subpackage post-translation
 */
class WPML_Admin_Post_Actions extends WPML_Post_Translation {

	const DUPLICATE_MEDIA_META_KEY = '_wpml_media_duplicate';
	const DUPLICATE_FEATURED_META_KEY = '_wpml_media_featured';
	const DUPLICATE_MEDIA_GLOBAL_KEY = 'duplicate_media';
	const DUPLICATE_FEATURED_GLOBAL_KEY = 'duplicate_media';

	private $http_referer;

	public function init() {
		parent::init ();
		if ( $this->is_setup_complete() ) {
			add_action ( 'delete_post', array( $this, 'delete_post_actions' ) );
			add_action ( 'wp_trash_post', array( $this, 'trashed_post_actions' ) );
			add_action ( 'untrashed_post', array( $this, 'untrashed_post_actions' ) );
		}
	}

	/**
	 * @param int    $post_id
	 * @param string $post_status
	 *
	 * @return null|int
	 */
	function get_save_post_trid( $post_id, $post_status ) {
		$trid = $this->get_element_trid( $post_id );

		if ( ! $this->is_inner_post_insertion() ) {
			$trid = $trid ? $trid : filter_var( isset( $_POST['icl_trid'] ) ? $_POST['icl_trid'] : '', FILTER_SANITIZE_NUMBER_INT );
			$trid = $trid ? $trid : filter_var( isset( $_GET['trid'] ) ? $_GET['trid'] : '', FILTER_SANITIZE_NUMBER_INT );
			$trid = $trid ? $trid : $this->get_trid_from_referer();
		}

		$trid = apply_filters( 'wpml_save_post_trid_value', $trid, $post_status );

		return $trid;
	}

	/**
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public function save_post_actions( $post_id, $post ) {
		global $sitepress;

		wp_defer_term_counting( true );
		$post = isset( $post ) ? $post : get_post( $post_id );
		// exceptions
		$http_referer = $this->get_http_referer();
		if ( ! $this->has_save_post_action( $post ) && ! $http_referer->is_rest_request_called_from_post_edit_page() ) {
			wp_defer_term_counting( false );

			return;
		}
		if ( WPML_WordPress_Actions::is_bulk_trash( $post_id ) ||
		     WPML_WordPress_Actions::is_bulk_untrash( $post_id ) ||
		     $this->has_invalid_language_details_on_heartbeat()
		) {

			return;
		}

		$default_language = $sitepress->get_default_language();
		$post_vars        = $this->get_post_vars( $post );

		if ( isset( $post_vars['action'] ) && $post_vars['action'] === 'post-quickpress-publish' ) {
			$language_code = $default_language;
		} else {
			if( isset( $post_vars['post_ID'] ) ){
				$post_id = $post_vars['post_ID'];
			}
			$language_code = $this->get_save_post_lang( $post_id, $sitepress );
		}

		if ( $this->is_inline_action( $post_vars ) && ! ( $language_code = $this->get_element_lang_code(
				$post_id
			) )
		) {
			return;
		}

		if ( isset( $post_vars['icl_translation_of'] ) && is_numeric( $post_vars['icl_translation_of'] ) ) {
			$translation_of_data_prepared = $this->wpdb->prepare(
				"SELECT trid, language_code
				 FROM {$this->wpdb->prefix}icl_translations
				 WHERE element_id=%d
					AND element_type=%s
				 LIMIT 1",
				$post_vars['icl_translation_of'],
				'post_' . $post->post_type
			);
			list( $trid, $source_language ) = $this->wpdb->get_row( $translation_of_data_prepared, 'ARRAY_N' );
		}

		if ( isset( $post_vars['icl_translation_of'] ) && $post_vars['icl_translation_of'] == 'none' ) {
			$trid            = null;
			$source_language = $language_code;
		} else {
			$trid = isset( $trid ) && $trid ? $trid : $this->get_save_post_trid( $post_id, $post->post_status );
			// after getting the right trid set the source language from it by referring to the root translation
			// of this trid, in case no proper source language has been set yet
			$source_language = isset( $source_language )
				? $source_language : $this->get_save_post_source_lang( $trid, $language_code, $default_language );
		}
		if ( isset( $post_vars['icl_tn_note'] ) ) {
			update_post_meta( $post_id, '_icl_translator_note', $post_vars['icl_tn_note'] );
		}
		$save_filter_action_state = new WPML_WP_Filter_State( 'save_post' );
		$this->after_save_post( $trid, $post_vars, $language_code, $source_language );
		$this->save_media_options( $post_id, $source_language );
		$save_filter_action_state->restore();
	}

	/**
	 * @param int         $post_id
	 * @param string|null $source_language
	 */
	private function save_media_options( $post_id, $source_language  ) {

		if ( $this->has_post_media_options_metabox() ) {
			$original_post_id = isset( $_POST['icl_translation_of'] )
				? filter_var( $_POST['icl_translation_of'], FILTER_SANITIZE_NUMBER_INT ) : $post_id;
			$duplicate_media = isset( $_POST['wpml_duplicate_media'] )
				? filter_var( $_POST['wpml_duplicate_media'], FILTER_SANITIZE_NUMBER_INT ) : false;
			$duplicate_featured = isset( $_POST['wpml_duplicate_featured'] )
				? filter_var( $_POST['wpml_duplicate_featured'], FILTER_SANITIZE_NUMBER_INT ) : false;

			update_post_meta( $original_post_id, self::DUPLICATE_MEDIA_META_KEY, (int) $duplicate_media );
			update_post_meta( $original_post_id, self::DUPLICATE_FEATURED_META_KEY, (int) $duplicate_featured );
		} else {
			$this->sync_media_options_with_original_or_global_settings( $post_id, $source_language );
		}
	}

	private function has_post_media_options_metabox() {
		return array_key_exists( WPML_Meta_Boxes_Post_Edit_HTML::FLAG_HAS_MEDIA_OPTIONS, $_POST );
	}

	/**
	 * @param int         $post_id
	 * @param string|null $source_language
	 */
	private function sync_media_options_with_original_or_global_settings( $post_id, $source_language ) {
		global $sitepress;

		$source_post_id = $sitepress->get_object_id( $post_id, get_post_type( $post_id ), false, $source_language );
		$is_translation = $source_post_id && $source_post_id !== $post_id;

		foreach (
			array(
				self::DUPLICATE_FEATURED_META_KEY => self::DUPLICATE_FEATURED_GLOBAL_KEY,
				self::DUPLICATE_MEDIA_META_KEY    => self::DUPLICATE_MEDIA_GLOBAL_KEY,
			) as $meta_key => $global_key
		) {

			$source_value = get_post_meta( $source_post_id, $meta_key, true );

			if ( '' === $source_value ) {
				// fallback to global setting
				$media_options = get_option( '_wpml_media', array() );

				if ( isset( $media_options['new_content_settings'][ $global_key ] ) ) {
					$source_value = (int) $media_options['new_content_settings'][ $global_key ];

					if ( $source_post_id ) {
						update_post_meta( $source_post_id, $meta_key, $source_value );
					}
				}
			}

			if ( '' !== $source_value && $is_translation ) {
				update_post_meta( $post_id, $meta_key, $source_value );
			}
		}

	}

	private function has_invalid_language_details_on_heartbeat() {
		if ( ! WPML_WordPress_Actions::is_heartbeat() ) {
			return false;
		}

		if ( isset( $_POST['data']['icl_post_language'], $_POST['data']['icl_trid'] ) ) {
			$_POST['icl_post_language'] = filter_var( $_POST['data']['icl_post_language'], FILTER_SANITIZE_STRING );
			$_POST['icl_trid'] = filter_var( $_POST['data']['icl_trid'], FILTER_SANITIZE_NUMBER_INT );
			return false;
		}

		return true;
	}

	/**
	 * @param integer   $post_id
	 * @param SitePress $sitepress
	 *
	 * @return null|string
	 */
	public function get_save_post_lang( $post_id, $sitepress ) {
		$language_code = null;
		if ( isset( $_POST['post_ID'] ) && (int) $_POST['post_ID'] === (int) $post_id ) {
			$language_code = filter_var(
				( isset( $_POST['icl_post_language'] ) ? $_POST['icl_post_language'] : '' ),
				FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}
		$language_code = $language_code
			? $language_code
			: filter_input(
				INPUT_GET,
				'lang',
				FILTER_SANITIZE_FULL_SPECIAL_CHARS
			);

		return $language_code ? $language_code : parent::get_save_post_lang( $post_id, $sitepress );
	}

	/**
	 * @param array $post_vars
	 * @return bool
	 */
	private function is_inline_action( $post_vars ) {

		return isset( $post_vars[ 'action' ] )
		       && $post_vars[ 'action' ] == 'inline-save'
		       || isset( $_GET[ 'bulk_edit' ] )
		       || isset( $_GET[ 'doing_wp_cron' ] )
		       || ( isset( $_GET[ 'action' ] )
		            && $_GET[ 'action' ] == 'untrash' );
	}

	/**
	 * @param int    $trid
	 * @param string $language_code
	 * @param string $default_language
	 *
	 * @uses \WPML_Backend_Request::get_source_language_from_referer to retrieve the source_language when saving via ajax
	 *
	 * @return null|string
	 */
	protected function get_save_post_source_lang( $trid, $language_code, $default_language ) {
		/** @var WPML_Backend_Request|WPML_Frontend_Request $wpml_request_handler */
		global $wpml_request_handler;

		$source_language = filter_input ( INPUT_GET, 'source_lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$source_language = $source_language ? $source_language : $wpml_request_handler->get_source_language_from_referer();
		$source_language = $source_language ? $source_language : SitePress::get_source_language_by_trid ( $trid );
		$source_language = $source_language === 'all' ? $default_language : $source_language;
		$source_language = $source_language !== $language_code ? $source_language : null;

		return $source_language;
	}

	public function get_trid_from_referer() {
		$http_referer = $this->get_http_referer();
		return $http_referer->get_trid();
	}

	private function get_http_referer() {
		if ( ! $this->http_referer ) {
			$factory = new WPML_URL_HTTP_Referer_Factory();
			$this->http_referer = $factory->create();
		}

		return $this->http_referer;
	}
}