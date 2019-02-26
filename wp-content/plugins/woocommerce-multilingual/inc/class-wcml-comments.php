<?php

class WCML_Comments {

	const WCML_AVERAGE_RATING_KEY = '_wcml_average_rating';
	const WCML_REVIEW_COUNT_KEY = '_wcml_review_count';
	const WC_REVIEW_COUNT_KEY = '_wc_review_count';

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var Sitepress */
	private $sitepress;

	/**
	 * WCML_Comments constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function add_hooks() {

		add_action( 'comment_post', array( $this, 'add_comment_rating' ) );
		add_action( 'woocommerce_review_before_comment_meta', array( $this, 'add_comment_flag' ), 9 );

		add_filter( 'get_post_metadata', array( $this, 'filter_average_rating' ), 10, 4 );
		add_filter( 'comments_clauses', array( $this, 'comments_clauses' ), 10, 2 );
		add_filter( 'woocommerce_product_review_list_args', array( $this, 'comments_link' ) );
		add_filter( 'wpml_is_comment_query_filtered', array( $this, 'is_comment_query_filtered' ), 10, 2 );
	}

	/**
	 * Add comment rating
	 *
	 * @param int $comment_id
	 */
	public function add_comment_rating( $comment_id ) {

		if ( isset( $_POST['comment_post_ID'] ) ) {

			$product_id = sanitize_text_field( $_POST['comment_post_ID'] );

			if ( 'product' === get_post_type( $product_id ) ) {

				$this->recalculate_comment_rating( $product_id );
			}
		}
	}

	/**
	 * Calculate rating field for comments based on reviews in all languages.
	 *
	 * @param int $product_id
	 */
	public function recalculate_comment_rating( $product_id ){

		$trid                  = $this->sitepress->get_element_trid( $product_id, 'post_product' );
		$translations          = $this->sitepress->get_element_translations( $trid, 'post_product', false, true );
		$average_ratings_sum   = 0;
		$average_ratings_count = 0;
		$reviews_count         = 0;

		foreach ( $translations as $translation ) {
			$ratings      = get_post_meta( $translation->element_id, '_wc_rating_count', true );
			$review_count = get_post_meta( $translation->element_id, self::WC_REVIEW_COUNT_KEY, true );

			if ( $ratings ) {
				foreach ( $ratings as $rating => $count ) {
					$average_ratings_sum += $rating * $count;
					$average_ratings_count += $count;
				}
			}

			$reviews_count += $review_count;
		}

		if ( $average_ratings_sum ) {
			$average_rating = number_format( $average_ratings_sum / $average_ratings_count, 2, '.', '' );

			foreach ( $translations as $translation ) {
				update_post_meta( $translation->element_id, self::WCML_AVERAGE_RATING_KEY, $average_rating );
				update_post_meta( $translation->element_id, self::WCML_REVIEW_COUNT_KEY, $reviews_count );
			}
		}

	}

	/**
	 * Filter WC reviews meta
	 *
	 * @param null|array|string $value    The value get_metadata() should return a single metadata value, or an
	 *                                    array of values.
	 * @param int               $object_id  Post ID.
	 * @param string            $meta_key Meta key.
	 * @param bool
	 * @return array|null|string Filtered metadata value, array of values, or null.
	 */
	public function filter_average_rating( $value, $object_id, $meta_key, $single ) {

		$filtered_value = $value;

		if ( in_array( $meta_key, array( '_wc_average_rating', self::WC_REVIEW_COUNT_KEY ) ) && 'product' === get_post_type( $object_id ) ) {

			switch ( $meta_key ){
				case '_wc_average_rating':
					$filtered_value = get_post_meta( $object_id, self::WCML_AVERAGE_RATING_KEY, $single );
					break;
				case self::WC_REVIEW_COUNT_KEY:
					if ( $this->is_reviews_in_all_languages( $object_id ) ) {
						$filtered_value = get_post_meta( $object_id, self::WCML_REVIEW_COUNT_KEY, $single );
					}
					break;
			}
		}

		return !empty( $filtered_value ) ? $filtered_value : $value;
	}

	/**
	 * Filters comment queries to display in all languages if needed
	 *
	 * @param string[] $clauses
	 * @param WP_Comment_Query $obj
	 *
	 * @return string[]
	 */
	public function comments_clauses( $clauses, $obj ) {

		if ( $this->is_reviews_in_all_languages( $obj->query_vars['post_id'] ) ) {

			$ids              = $this->get_translations_ids_list( $obj->query_vars['post_id'] );

			$clauses['where'] = str_replace( 'comment_post_ID = ' . $obj->query_vars['post_id'], 'comment_post_ID IN (' . $ids . ')', $clauses['where'] );
		}

		return $clauses;
	}

	/**
	 * Get list of translated ids for product
	 *
	 * @param int $product_id
	 *
	 * @return string list of ids
	 */
	private function get_translations_ids_list( $product_id ){

		$trid         = $this->sitepress->get_element_trid( $product_id, 'post_product' );
		$translations = $this->sitepress->get_element_translations( $trid, 'post_product', false, true );

		$ids = array();
		foreach ( $translations as $translation ) {
			$ids[] = $translation->element_id;
		}

		return implode( ',', array_filter( $ids ) );

	}

	/**
	 * Display link to show rating in all/current language
	 *
	 * @param array $args
	 * @return array
	 */
	public function comments_link( $args ) {
		
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_language = $this->sitepress->get_current_language();

		if ( ! isset( $_GET['clang'] ) || $current_language === $_GET['clang'] ) {
			$comments_link = add_query_arg( array( 'clang' => 'all' ), $current_url );
			$reviews_count = $this->get_reviews_count( 'all' );
			$comments_link_text = sprintf( __( 'Show reviews in all languages  (%s)', 'woocommerce-multilingual'), $reviews_count);
		} elseif ( 'all' === $_GET['clang'] ) {
			$comments_link    = add_query_arg( array( 'clang' => $current_language ), $current_url );
			$language_details = $this->sitepress->get_language_details( $current_language );
			$reviews_count = $this->get_reviews_count( );
			$comments_link_text = sprintf( __( 'Show only reviews in %s (%s)', 'woocommerce-multilingual'), $language_details['display_name'], $reviews_count );
		}

		if( isset( $reviews_count ) && $reviews_count ){
			echo '<p><a id="lang-comments-link" href="' . $comments_link . '">' . $comments_link_text . '</a></p>';
		}

		return $args;
	}

	/**
	 * Checks if comments needs filtering by language.
	 *
	 * @param bool $filtered
	 * @param int $post_id
	 * @return bool
	 */
	public function is_comment_query_filtered( $filtered, $post_id ) {

		if ( $this->is_reviews_in_all_languages( $post_id ) ) {
			$filtered = false;
		}

		return $filtered;
	}

	/**
	 * Add flag to comment description
	 *
	 * @param WP_Comment $comment
	 */
	public function add_comment_flag( $comment ) {

		if ( $this->is_reviews_in_all_languages( $comment->comment_post_ID ) ) {
			$comment_language = $this->sitepress->get_language_for_element( $comment->comment_post_ID, 'post_product' );

			$html = '<div style="float: left; padding-right: 5px;">';
			$html .= '<img src="' . $this->sitepress->get_flag_url( $comment_language ) . '" width=18" height="12">';
			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * Checks if reviews in all languages should be displayed.
	 *
	 * @param int $product_id
	 * @return bool
	 */
	public function is_reviews_in_all_languages( $product_id ) {

		return isset( $_GET['clang'] ) && 'all' === $_GET['clang'] && 'product' === get_post_type( $product_id );
	}

	/**
	 * Return reviews count in language
	 *
	 * @param string $language
	 * @return int
	 */
	public function get_reviews_count( $language = false ) {

		remove_filter( 'get_post_metadata', array( $this, 'filter_average_rating' ), 10, 4 );

		if ( ! metadata_exists( 'post', get_the_ID(), self::WCML_REVIEW_COUNT_KEY ) ) {
			$this->recalculate_comment_rating( get_the_ID() );
		}

		if( 'all' === $language ){
			$reviews_count = get_post_meta( get_the_ID(), self::WCML_REVIEW_COUNT_KEY, true );
		}else{
			$reviews_count = get_post_meta( get_the_ID(), self::WC_REVIEW_COUNT_KEY, true );
		}

		add_filter( 'get_post_metadata', array( $this, 'filter_average_rating' ), 10, 4 );

		return $reviews_count;
	}

}