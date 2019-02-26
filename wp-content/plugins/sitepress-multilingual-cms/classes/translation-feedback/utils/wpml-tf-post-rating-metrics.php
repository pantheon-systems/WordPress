<?php

/**
 * Class WPML_TF_Rating_Average
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Post_Rating_Metrics {

	const QUANTITY_KEY = 'wpml_tf_post_rating_quantity';
	const AVERAGE_KEY  = 'wpml_tf_post_rating_average';

	/** @var wpdb $wpdb */
	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param int $post_id
	 *
	 * @return string
	 */
	public function get_display( $post_id ) {
		$quantity = (int) get_post_meta( $post_id, self::QUANTITY_KEY, true );

		if ( 0 === $quantity ) {
			return '';
		}

		$average = get_post_meta( $post_id, self::AVERAGE_KEY, true );
		$title   = sprintf( __( 'Average rating - %s', 'sitepress' ), $average );
		$stars   = round( $average );
		$url     = admin_url( 'admin.php?page=' . WPML_TF_Backend_Hooks::PAGE_HOOK . '&post_id=' . $post_id );

		$output  = '<div class="wpml-tf-rating" title="' . esc_attr( $title ) . '"><a href="'. esc_url( $url ) . '">';

		for ( $i = 1; $i < 6; $i++ ) {
			$output .= '<span class="otgs-ico-star';

			if ( $i <= $stars ) {
				$output .= ' full-star';
			}

			$output .= '"></span>';
		}

		$output .= '</a> <span class="rating-quantity">(' . esc_html( $quantity ) . ')</span></div>';

		return $output;

	}

	/** @param int $post_id */
	public function refresh( $post_id ) {
		$document_id_key = WPML_TF_Data_Object_Storage::META_PREFIX . 'document_id';
		$rating_key      = WPML_TF_Data_Object_Storage::META_PREFIX . 'rating';

		$subquery = $this->wpdb->prepare(
			"SELECT post_id FROM {$this->wpdb->postmeta}
			 WHERE meta_key = %s AND meta_value = %d",
			$document_id_key,
			$post_id
		);

		$query = $this->wpdb->prepare(
			"SELECT meta_value FROM {$this->wpdb->postmeta}
			 WHERE meta_key = %s AND post_id IN( {$subquery} )",
			$rating_key
		);

		$ratings = $this->wpdb->get_results( $query );

		if ( $ratings ) {

			$total = 0;
			foreach ( $ratings as $rating ) {
				$total += (int) $rating->meta_value;
			}

			$quantity = count( $ratings );
			$average  = round( $total / $quantity, 1 );

			update_post_meta( $post_id, self::QUANTITY_KEY, $quantity );
			update_post_meta( $post_id, self::AVERAGE_KEY, $average );
		} else {
			delete_post_meta( $post_id, self::QUANTITY_KEY );
			delete_post_meta( $post_id, self::AVERAGE_KEY );
		}
	}
}
