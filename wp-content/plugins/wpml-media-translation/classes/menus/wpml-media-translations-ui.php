<?php

/**
 * Class WPML_Media_Translations_UI
 */
class WPML_Media_Translations_UI extends WPML_Templates_Factory {
	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var WP_Locale
	 */
	private $wp_locale;
	/**
	 * @var WPML_Query_Filter
	 */
	private $wpml_query_filter;
	/**
	 * @var WPML_Admin_Pagination
	 */
	private $pagination;
	/**
	 * @var array
	 */
	private $query_args = array();

	/**
	 * WPML_Media_Translations_UI constructor.
	 *
	 * @param SitePress $sitepress
	 * @param wpdb $wpdb
	 * @param WP_Locale $wp_locale
	 * @param WPML_Query_Filter $wpml_query_filter
	 * @param WPML_Admin_Pagination $pagination
	 */
	public function __construct(
		SitePress $sitepress,
		wpdb $wpdb,
		WP_Locale $wp_locale,
		WPML_Query_Filter $wpml_query_filter,
		WPML_Admin_Pagination $pagination
	) {
		parent::__construct();
		$this->sitepress         = $sitepress;
		$this->wpdb              = $wpdb;
		$this->wp_locale         = $wp_locale;
		$this->wpml_query_filter = $wpml_query_filter;
		$this->pagination        = $pagination;
	}

	/**
	 * @return array
	 */
	public function get_model() {

		$this->set_query_args();

		$languages = $this->get_languages();

		$model = array(
			'strings'         => array(
				'heading'                  => __( 'Media Translation', 'wpml-media' ),
				'filter_by_date'           => __( 'Filter by date', 'wpml-media' ),
				'all_dates'                => __( 'All dates', 'wpml-media' ),
				'in'                       => __( 'in', 'wpml_media' ),
				'to'                       => __( 'to', 'wpml_media' ),
				'filter_by_status'         => __( 'Filter by translation status', 'wpml-media' ),
				'status_all'               => __( 'All translation statuses', 'wpml-media' ),
				'status_not'               => __( 'Media file not translated', 'wpml-media' ),
				'status_translated'        => __( 'Translated media uploaded', 'wpml-media' ),
				'status_in_progress'       => __( 'Translation in progress', 'wpml-media' ),
				'status_needs_translation' => __( 'Needs media file translation', 'wpml-media' ),
				'filter_by_language'       => __( 'Filter by language', 'wpml-media' ),
				'any_language'             => __( 'Any language', 'wpml-media' ),
				'search_media'             => __( 'Search Media:', 'wpml-media' ),
				'search_placeholder'       => __( 'Title, caption or description', 'wpml-media' ),
				'search_button_label'      => __( 'Filter', 'wpml-media' ),
				'original_language'        => __( 'Original language', 'wpml-media' ),
				'no_attachments'           => __( 'No attachments found', 'wpml-media' ),
				'add_translation'          => __( 'Add media file translation', 'wpml-media' ),
				'edit_translation'         => __( 'Edit translation', 'wpml-media' ),
				'original'                 => __( 'Original:', 'wpml-media' ),
				'translation'              => __( 'Translation:', 'wpml-media' ),
				'file'                     => __( 'File', 'wpml-media' ),
				'name'                     => __( 'Name', 'wpml-media' ),
				'caption'                  => __( 'Caption', 'wpml-media' ),
				'alt_text'                 => __( 'Alt text', 'wpml-media' ),
				'description'              => __( 'Description', 'wpml-media' ),
				'copy_from_original'       => __( 'Copy from original', 'wpml-media' ),
				'upload_translated_media'  => __( 'Upload translated media file', 'wpml-media' ),
				'use_different_file'       => __( 'Use a different file', 'wpml-media' ),
				'revert_to_original'       => __( 'Revert to original', 'wpml-media' ),
				'restore_original_media'   => __( 'Restore original media file', 'wpml-media' ),
				'statuses'                 => self::get_translation_status_labels(),
				'texts_change_notice'      => __( 'Any changes you make to the text here will not affect any previous publications of this media on your website. This edited version will only appear if you select it from the library to be embedded.', 'wpml-media' )
			),
			'months'          => $this->get_months(),
			'selected_month'  => isset( $this->query_args['m'] ) ? (int) $this->query_args['m'] : 0,
			'selected_status' => isset( $this->query_args['status'] ) ? $this->query_args['status'] : '',
			'from_language'   => isset( $this->query_args['slang'] ) ? $this->query_args['slang'] : '',
			'to_language'     => isset( $this->query_args['tlang'] ) ? $this->query_args['tlang'] : '',
			'statuses'        => array(
				'not_translated'    => WPML_Media_Translation_Status::NOT_TRANSLATED,
				'in_progress'       => WPML_Media_Translation_Status::IN_PROGRESS,
				'translated'        => WPML_Media_Translation_Status::TRANSLATED,
				'needs_translation' => WPML_Media_Translation_Status::NEEDS_MEDIA_TRANSLATION
			),
			'search'          => isset( $this->query_args['s'] ) ? $this->query_args['s'] : '',
			'languages'       => $languages,
			'attachments'     => $this->get_attachments( $languages ),
			'nonce'           => wp_nonce_field( 'media-translation', 'wpnonce', false, false ),
			'pagination'      => $this->get_pagination(),
			'target_language' => $this->should_filter_by_target_language() ? $this->query_args['tlang'] : '',

			'batch_translation' => $this->get_batch_translation(),

			'show_text_change_notice' => ! get_user_meta( get_current_user_id(), WPML_Media_Editor_Notices::TEXT_EDIT_NOTICE_DISMISSED, true )
		);

		return $model;
	}

	public static function get_translation_status_labels() {
		return array(
			WPML_Media_Translation_Status::NOT_TRANSLATED          => __( 'Not translated', 'wpml-media' ),
			WPML_Media_Translation_Status::IN_PROGRESS             => __( 'In progress', 'wpml-media' ),
			WPML_Media_Translation_Status::TRANSLATED              => __( 'Translated', 'wpml-media' ),
			WPML_Media_Translation_Status::NEEDS_MEDIA_TRANSLATION => __( 'Needs translation', 'wpml-media' )
		);
	}

	private function set_query_args() {
		$arg_keys = array( 'm', 'status', 'slang', 'tlang', 's', 'paged' );
		foreach ( $arg_keys as $key ) {
			if ( isset ( $_GET[ $key ] ) ) {
				$this->query_args[ $key ] = sanitize_text_field( $_GET[ $key ] );
			}
		}
	}

	/**
	 * @return array
	 */
	private function get_months() {
		$months = array();

		$month_results = $this->wpdb->get_results( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM {$this->wpdb->posts}
			WHERE post_type = 'attachment'
			ORDER BY post_date DESC
		" );

		foreach ( $month_results as $month ) {
			$months[] = array(
				'id'    => $month->year . zeroise( $month->month, 2 ),
				'label' => sprintf( __( '%1$s %2$d' ), $this->wp_locale->get_month( $month->month ), $month->year )
			);
		}

		return $months;
	}

	/**
	 * @return array
	 */
	private function get_languages() {
		$languages = array();

		$active_languages = $this->sitepress->get_active_languages();
		foreach ( $active_languages as $language ) {
			$languages[ $language['code'] ] = array(
				'name' => $language['display_name'],
				'flag' => $this->sitepress->get_flag_url( $language['code'] )
			);
		}

		return $languages;
	}

	private function get_items_per_page() {
		return get_option( 'wpml_media_translation_dashboard_items_per_page', 20 );
	}

	/**
	 * @param array $languages
	 *
	 * @return array
	 */
	private function get_attachments( $languages ) {
		$attachments = array();

		$args = array(
			'post_type'      => 'attachment',
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'posts_per_page' => $this->get_items_per_page()
		);
		$args = array_merge( $args, $this->query_args );

		if ( $this->should_filter_by_status() ) {
			$args['meta_query'] = array(
				'key'   => '_media_translation_status',
				'value' => $this->query_args['status']
			);
		}

		$this->add_query_filters();

		$attachment_posts = get_posts( $args );

		$this->remove_query_filters();

		foreach ( $attachment_posts as $attachment ) {
			$attachment_id       = $attachment->ID;
			$post_element        = new WPML_Post_Element( $attachment_id, $this->sitepress );
			$media_file_original = get_post_meta( $attachment_id, '_wp_attached_file', true );
			$translations        = array();

			$is_image = (int) ( 0 === strpos( $attachment->post_mime_type, 'image/' ) );

			foreach ( $languages as $code => $language ) {
				$translation = $post_element->get_translation( $code );

				if ( $translation ) {
					$translation_post      = $translation->get_wp_object();
					$media_file_translated = get_post_meta( $translation->get_id(), '_wp_attached_file', true );

					$translations[ $code ] = array(
						'id'                  => $translation->get_id(),
						'file_name'           => basename( $media_file_translated ),
						'title'               => $translation_post->post_title,
						'caption'             => $translation_post->post_excerpt,
						'description'         => $translation_post->post_content,
						'alt'                 => get_post_meta( $translation->get_id(), '_wp_attachment_image_alt', true ),
						'thumb'               => $this->get_attachment_thumb( $translation->get_id(), $is_image ),
						'media_is_translated' => $media_file_translated && $media_file_translated !== $media_file_original,
						'status'              => $this->get_translation_status( $attachment_id, $translation )
					);

				} else {
					$translations[ $code ] = array(
						'status' => WPML_Media_Translation_Status::NOT_TRANSLATED
					);
				}

			}

			$attachments[] = array(
				'post'         => $attachment,
				'mime_type'    => $is_image ? 'image/*' : $attachment->post_mime_type,
				'is_image'     => $is_image,
				'file_name'    => basename( $media_file_original ),
				'alt'          => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
				'meta'         => get_post_meta( $attachment_id, '_wp_attachment_metadata', true ),
				'language'     => $post_element->get_language_code(),
				'thumb'        => $this->get_attachment_thumb( $attachment_id, $is_image ),
				'translations' => $translations
			);

		}

		return $attachments;
	}

	/**
	 * @param int $attachment_id
	 * @param bool $is_image
	 *
	 * @return array
	 */
	private function get_attachment_thumb( $attachment_id, $is_image = true ) {
		$image = wp_get_attachment_image_src( $attachment_id, 'thumbnail', true );

		$thumb = array(
			'src'    => $image ? $image[0] : '',
			'width'  => $is_image ? 60 : $image[1],
			'height' => $is_image ? 60 : $image[2],
		);

		return $thumb;
	}

	public function set_total_items_in_pagination( $query ) {

		$query_count = preg_replace(
			"/^SELECT /", "SELECT SQL_CALC_FOUND_ROWS ", $query );
		$this->wpdb->get_results( $query_count );

		$this->pagination->set_total_items( $this->wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		return $query;
	}

	private function get_pagination() {

		$this->pagination->set_items_per_page( $this->get_items_per_page() );
		$current_page = isset( $this->query_args['paged'] ) ? $this->query_args['paged'] : 1;
		$this->pagination->set_current_page( $current_page );

		$model = array(
			'strings'     => array(
				'list_navigation' => __( 'Navigation', 'wpml-media' ),
				'of'              => __( 'of', 'wpml-media' )
			),
			'pagination'  => array(
				'get_first_page_url' => $this->pagination->get_first_page_url(),
				'first_page'         => __( 'First page', 'wpml-media' ),

				'get_previous_page_url' => $this->pagination->get_previous_page_url(),
				'previous_page'         => __( 'Previous page', 'wpml-media' ),

				'get_current_page' => $this->pagination->get_current_page(),
				'get_total_pages'  => $this->pagination->get_total_pages(),

				'get_next_page_url' => $this->pagination->get_next_page_url(),
				'next_page'         => __( 'Next page', 'wpml-media' ),

				'get_last_page_url' => $this->pagination->get_last_page_url(),
				'last_page'         => __( 'Last page', 'wpml-media' )
			),
			'total_items' => sprintf(
				_n( '%s item', '%s items', $this->pagination->get_total_items() ),
				$this->pagination->get_total_items()
			)
		);

		return $model;
	}

	private function get_batch_translation() {
		$model = array(
			'strings' => array(
				'close'               => __( 'Close', 'wpml-media' ),
				'was_replaced'        => __( 'The media that you uploaded was replaced in these translated posts:', 'wpml-media' ),
				'other_posts'         => __( 'The same media might be used in other posts too. Do you want to scan and replace it?', 'wpml-media' ),
				'without_usage'       => __( 'The media that you uploaded will be used in future post translations. The same media might be used in already existing posts. Do you want to scan and replace it now?', 'wpml-media' ),
				'scan_for_this_media' => __( 'Scan for content that has specifically this media (faster)', 'wpml-media' ),
				'scan_for_all_media'  => __( 'Scan for content that has any of the media files that I translated (takes more time)', 'wpml-media' ),
				'button_label'        => _x( 'Scan & Replace', 'Button label (verb)', 'wpml-media' )
			)
		);

		return $model;
	}

	private function add_query_filters() {
		remove_filter( 'posts_join', array( $this->wpml_query_filter, 'posts_join_filter' ), 10 );
		remove_filter( 'posts_where', array( $this->wpml_query_filter, 'posts_where_filter' ), 10 );

		add_filter( 'posts_join', array( $this, 'filter_request_clause_join' ), PHP_INT_MAX );
		add_filter( 'posts_where', array( $this, 'filter_request_clause_where' ), PHP_INT_MAX );
		add_filter( 'posts_request', array( $this, 'set_total_items_in_pagination' ), - PHP_INT_MAX );
	}

	private function remove_query_filters() {
		add_filter( 'posts_join', array( $this->wpml_query_filter, 'posts_join_filter' ), 10, 2 );
		add_filter( 'posts_where', array( $this->wpml_query_filter, 'posts_where_filter' ), 10, 2 );

		remove_filter( 'posts_join', array( $this, 'filter_request_clause_join' ), PHP_INT_MAX );
		remove_filter( 'posts_where', array( $this, 'filter_request_clause_where' ), PHP_INT_MAX );
		remove_filter( 'posts_request', array( $this, 'set_total_items_in_pagination' ), - PHP_INT_MAX );
	}

	public function filter_request_clause_join( $join ) {

		$join .= " LEFT JOIN {$this->wpdb->prefix}icl_translations icl_translations_source 
				   ON icl_translations_source.element_id = {$this->wpdb->posts}.ID ";

		if ( $this->should_filter_by_status() ) {
			if ( $this->should_filter_by_target_language() ) {
				$join .= $this->wpdb->prepare( " LEFT JOIN {$this->wpdb->postmeta} post_meta 
					   ON icl_translations_source.element_id = post_meta.post_id  
					    AND post_meta.meta_key = %s				              
				        ", WPML_Media_Translation_Status::STATUS_PREFIX . $this->query_args['tlang'] );
			} else {
				$active_language = $this->sitepress->get_active_languages();
				$default_lanuage = $this->sitepress->get_default_language();

				foreach ( $active_language as $language ) {
					if ( $language['code'] !== $default_lanuage ) {
						$sanitized_code = str_replace( '-', '_', $language['code'] );

						$join .= $this->wpdb->prepare( " LEFT JOIN {$this->wpdb->postmeta} post_meta_{$sanitized_code} 
					   ON {$this->wpdb->posts}.ID = post_meta_{$sanitized_code}.post_id  
					    AND post_meta_{$sanitized_code}.meta_key=%s", WPML_Media_Translation_Status::STATUS_PREFIX . $language['code'] );
					}
				}
			}
		} else {
			if ( $this->should_filter_by_target_language() ) {
				$join .= " JOIN {$this->wpdb->prefix}icl_translations icl_translations_target 
					   ON icl_translations_target.trid = icl_translations_source.trid 
					   AND icl_translations_target.language_code='{$this->query_args['tlang']}'";
			}
		}

		return $join;
	}

	public function filter_request_clause_where( $where ) {

		$where .= " AND icl_translations_source.element_type='post_attachment' 
					AND icl_translations_source.source_language_code IS NULL ";

		if ( ! empty ( $this->query_args['slang'] ) && $this->query_args['slang'] !== 'all' ) {
			$where .= $this->wpdb->prepare( ' AND icl_translations_source.language_code = %s ', $this->query_args['slang'] );
		}

		if ( $this->should_filter_by_status() ) {
			if ( $this->should_filter_by_target_language() ) {
				if ( $this->query_args['status'] === WPML_Media_Translation_Status::NOT_TRANSLATED ) {
					$where .= $this->wpdb->prepare(
						' AND ( 
								post_meta.meta_value IS NULL OR
								post_meta.meta_value = %s OR 
								post_meta.meta_value = %s OR
								post_meta.meta_value = %s
						)',
						WPML_Media_Translation_Status::NOT_TRANSLATED,
						WPML_Media_Translation_Status::IN_PROGRESS,
						WPML_Media_Translation_Status::NEEDS_MEDIA_TRANSLATION
					);
				} else {
					$where .= $this->wpdb->prepare( ' AND post_meta.meta_value = %s', $this->query_args['status'] );
				}
			} else {
				$active_language  = $this->sitepress->get_active_languages();
				$default_language = $this->sitepress->get_default_language();

				if ( $this->query_args['status'] === WPML_Media_Translation_Status::TRANSLATED ) {
					foreach ( $active_language as $language ) {
						$sanitized_code = str_replace( '-', '_', $language['code'] );
						if ( $language['code'] !== $default_language ) {
							$where .= $this->wpdb->prepare( "AND post_meta_{$sanitized_code}.meta_value = %s",
								WPML_Media_Translation_Status::TRANSLATED );
						}
					}
				} elseif ( $this->query_args['status'] === WPML_Media_Translation_Status::NOT_TRANSLATED ) {
					$where .= 'AND ( 0 ';
					foreach ( $active_language as $language ) {
						$sanitized_code = str_replace( '-', '_', $language['code'] );
						if ( $language['code'] !== $default_language ) {
							$where .= $this->wpdb->prepare( " 
								OR post_meta_{$sanitized_code}.meta_value = %s								
								OR post_meta_{$sanitized_code}.meta_value IS NULL",
								$this->query_args['status'] );
						}
					}
					$where .= ') ';
				} else {
					$where .= 'AND ( 0 ';
					foreach ( $active_language as $language ) {
						$sanitized_code = str_replace( '-', '_', $language['code'] );
						if ( $language['code'] !== $default_language ) {
							$where .= $this->wpdb->prepare( " 
								OR post_meta_{$sanitized_code}.meta_value = %s",
								$this->query_args['status'] );
						}
					}
					$where .= ') ';
				}
			}
		}

		return $where;
	}

	private function should_filter_by_status() {
		return isset ( $this->query_args['status'] ) && $this->query_args['status'] !== '';
	}

	private function should_filter_by_target_language() {
		return isset ( $this->query_args['tlang'] ) && $this->query_args['tlang'] !== '';
	}

	protected function init_template_base_dir() {
		$this->template_paths = array(
			WPML_MEDIA_PATH . '/templates/menus/',
			WPML_PLUGIN_PATH . '/templates/pagination/'
		);
	}

	/**
	 * @return string
	 */
	public function get_template() {
		return 'media-translation.twig';
	}

	/**
	 * @param $attachment_id
	 * @param $translation
	 *
	 * @return mixed|string
	 */
	private function get_translation_status( $attachment_id, $translation ) {
		$translation_status = get_post_meta(
			$attachment_id,
			WPML_Media_Translation_Status::STATUS_PREFIX . $translation->get_language_code(),
			true
		);
		if ( ! $translation_status ) {
			$translation_status = WPML_Media_Translation_Status::NOT_TRANSLATED;
		}

		return $translation_status;
	}


}
