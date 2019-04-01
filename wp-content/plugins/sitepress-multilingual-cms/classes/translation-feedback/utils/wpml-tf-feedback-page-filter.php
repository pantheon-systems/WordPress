<?php

/**
 * Class WPML_TF_Feedback_Page_Filter
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Page_Filter {

	/** @var  SitePress $sitepress */
	private $sitepress;

	/** @var WPML_TF_Feedback_Query $feedback_query */
	private $feedback_query;

	/** @var  array $statuses */
	private $statuses = array();

	/** @var  array $languages */
	private $languages = array();

	/** @var  array $url_args */
	private $url_args;

	/** @var  string $current_url */
	private $current_url;

	/**
	 * WPML_TF_Feedback_Page_Filter constructor.
	 *
	 * @param SitePress              $sitepress
	 * @param WPML_TF_Feedback_Query $feedback_query
	 */
	public function __construct( SitePress $sitepress, WPML_TF_Feedback_Query $feedback_query ) {
		$this->sitepress      = $sitepress;
		$this->feedback_query = $feedback_query;
	}

	/**
	 * @return array
	 */
	public static function get_filter_keys() {
		return array(
			'status',
			'language',
			'post_id',
		);
	}

	/**
	 * Will not create filters inside the trash
	 * And will not include the "trash" status in the status row
	 */
	public function populate_counters_and_labels() {
		if ( $this->feedback_query->is_in_trash() ) {
			return;
		}

		foreach ( $this->feedback_query->get_unfiltered_collection() as $feedback ) {
			/** @var WPML_TF_Feedback $feedback */
			if ( 'trash' === $feedback->get_status() ) {
				continue;
			}

			if ( ! array_key_exists( $feedback->get_status(), $this->statuses ) ) {
				$this->statuses[ $feedback->get_status() ] = array(
					'count' => 1,
					'label' => $feedback->get_text_status(),
				);
			} else {
				$this->statuses[ $feedback->get_status() ]['count']++;
			}

			if ( ! array_key_exists( $feedback->get_language_to(), $this->languages ) ) {
				$lang_details = $this->sitepress->get_language_details( $feedback->get_language_to() );

				$this->languages[ $feedback->get_language_to() ] = array(
					'count' => 1,
					'label' => isset( $lang_details['display_name'] ) ? $lang_details['display_name'] : null,
				);
			} else {
				$this->languages[ $feedback->get_language_to() ]['count']++;
			}
		}

		ksort( $this->statuses );
		ksort( $this->languages );
	}

	/**
	 * @return array
	 */
	public function get_all_and_trash_data() {
		$main_data = array(
			'all' => array(
				'count'   => $this->feedback_query->get_total_items_count(),
				'label'   => __( 'All', 'sitepress' ),
				'url'     => $this->get_reset_filters_url(),
				'current' => $this->get_current_url() === $this->get_reset_filters_url(),
			),
		);

		if ( $this->feedback_query->get_total_trashed_items_count() ) {
			$main_data['trashed'] = array(
				'count'   => $this->feedback_query->get_total_trashed_items_count(),
				'label'   => __( 'Trash', 'sitepress' ),
				'url'     => $this->get_filter_url( 'status', 'trash' ),
				'current' => $this->get_current_url() === $this->get_filter_url( 'status', 'trash' ),
			);
		}

		if ( $this->is_current_filter( 'post_id' ) ) {
			$filters = $this->get_current_filters();
			$post    = get_post( $filters['post_id'] );

			if ( isset( $post->post_title ) ) {
				$main_data['post_id'] = array(
					'label'   => sprintf(
						__( 'For "%s"', 'sitepress' ),
						mb_strimwidth( $post->post_title, 0, 40, '...' )
					),
					'current' => true,
				);

			}
		}

		return $main_data;
	}

	/**
	 * @return array
	 */
	public function get_statuses_data() {
		foreach ( $this->statuses as $status => $data ) {
			$this->statuses[ $status ]['url'] = $this->get_filter_url( 'status', $status );
			$this->statuses[ $status ]['current'] = false;

			if ( $this->is_current_filter( 'status', $status ) ) {
				$this->statuses[ $status ]['current'] = true;
			}
		}

		return $this->statuses;
	}

	/**
	 * @return array
	 */
	public function get_languages_data() {
		foreach ( $this->languages as $language_code => $data ) {
			$this->languages[ $language_code ]['url'] = $this->get_filter_url( 'language', $language_code );
			$this->languages[ $language_code ]['current'] = false;

			if ( $this->is_current_filter( 'language', $language_code ) ) {
				$this->languages[ $language_code ]['current'] = true;
			}
		}

		return $this->languages;
	}

	/**
	 * @param string $filter_name
	 * @param string $filter_value
	 *
	 * @return string
	 */
	private function get_filter_url( $filter_name, $filter_value ) {
		return add_query_arg( $filter_name, $filter_value, $this->get_reset_filters_url() );
	}

	/**
	 * @param string $filter_key
	 * @param string $filter_value
	 *
	 * @return bool
	 */
	private function is_current_filter( $filter_key, $filter_value = null ) {
		$is_current_filter = false;
		$query_args        = $this->get_url_args();

		if ( array_key_exists( $filter_key, $query_args )
		     && ( ! $filter_value || $query_args[ $filter_key ] === $filter_value )
		) {
			$is_current_filter = true;
		}

		return $is_current_filter;
	}

	/**
	 * @return array
	 */
	public function get_current_filters() {
		$filters    = array();
		$query_args = $this->get_url_args();

		foreach ( self::get_filter_keys() as $filter_key ) {
			if ( array_key_exists( $filter_key, $query_args ) ) {
				$filters[ $filter_key ] = $query_args[ $filter_key ];
			}
		}

		return $filters;
	}

	/**
	 * @return array
	 */
	private function get_url_args() {
		if ( ! $this->url_args ) {
			$this->url_args = array();
			$url_query  = wpml_parse_url( $this->get_current_url(), PHP_URL_QUERY );
			parse_str( $url_query, $this->url_args );
		}

		return $this->url_args;
	}

	/**
	 * @return string
	 */
	private function get_current_url() {
		if ( ! $this->current_url ) {
			$this->current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$this->current_url = remove_query_arg( 'paged', $this->current_url );
		}

		return $this->current_url;
	}

	/**
	 * @return string
	 */
	private function get_reset_filters_url() {
		return remove_query_arg( self::get_filter_keys(), $this->get_current_url() );
	}
}