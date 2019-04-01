<?php

/**
 * Class WPML_TF_Data_Object_Storage
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Data_Object_Storage {

	const META_PREFIX = 'wpml_tf_';

	/** @var WPML_TF_Data_Object_Post_Convert  */
	private $post_convert;

	/**
	 * WPML_TF_Data_Object_Storage constructor.
	 *
	 * @param WPML_TF_Data_Object_Post_Convert $post_convert
	 */
	public function __construct( WPML_TF_Data_Object_Post_Convert $post_convert ) {
		$this->post_convert = $post_convert;
	}

	/**
	 * @param int $id
	 *
	 * @return IWPML_TF_Data_Object|null
	 */
	public function get( $id ) {
		$result    = null;
		$post_data = array();

		$post_data['post'] = get_post( $id );

		if ( $post_data['post'] ) {
			foreach ( $this->post_convert->get_meta_fields() as $meta_field ) {
				$post_data['metadata'][ $meta_field ] = get_post_meta( $id, self::META_PREFIX . $meta_field, true );
			}

			$result =  $this->post_convert->to_object( $post_data );
		}

		return $result;
	}

	/**
	 * @param IWPML_TF_Data_Object $data_object
	 *
	 * @return int|WP_Error
	 */
	public function persist( IWPML_TF_Data_Object $data_object ) {
		$post_data = $this->post_convert->to_post_data( $data_object );

		$updated_id = wp_insert_post( $post_data['post'] );

		if ( $updated_id && ! is_wp_error( $updated_id ) ) {
			foreach ( $post_data['metadata'] as $key => $value ) {
				update_post_meta( $updated_id, self::META_PREFIX . $key, $value );
			}
		}

		return $updated_id;
	}

	/**
	 * @param int  $id
	 * @param bool $force_delete
	 */
	public function delete( $id, $force_delete = false ) {
		if ( $force_delete ) {
			wp_delete_post( $id );
		} else {
			wp_trash_post( $id );
		}
	}

	/** @param int $id */
	public function untrash( $id ) {
		wp_untrash_post( $id );
	}

	/**
	 * @param IWPML_TF_Collection_Filter $collection_filter
	 *
	 * @return WPML_TF_Collection
	 */
	public function get_collection( IWPML_TF_Collection_Filter $collection_filter ) {
		$collection = $collection_filter->get_new_collection();
		$posts_args = $collection_filter->get_posts_args();

		if ( isset( $posts_args['meta_query']['relation'] ) && 'OR' === $posts_args['meta_query']['relation'] ) {
			$object_posts = $this->get_posts_from_split_queries( $posts_args );
		} else {
			$object_posts = get_posts( $posts_args );
		}

		foreach ( $object_posts as $object_post ) {
			$collection->add( $this->get( $object_post->ID ) );
		}

		return $collection;
	}

	/**
	 * For more than 2 meta queries with "OR" relation, the standard WP query has a very bad performance.
	 * It's much more efficient to make one query for each meta query.
	 *
	 * @param array $posts_args
	 *
	 * @return array
	 */
	private function get_posts_from_split_queries( array $posts_args ) {
		$object_posts     = array();
		$meta_query_parts = $posts_args['meta_query'];
		unset( $meta_query_parts['relation'] );

		foreach ( $meta_query_parts as $meta_query ) {
			$posts_args['meta_query'] = $meta_query;
			$object_posts = $object_posts + get_posts( $posts_args );
		}

		return $object_posts;
	}
}