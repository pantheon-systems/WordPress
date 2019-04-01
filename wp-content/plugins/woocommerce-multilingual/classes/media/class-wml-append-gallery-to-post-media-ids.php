<?php

class WCML_Append_Gallery_To_Post_Media_Ids implements IWPML_Action {

	public function add_hooks() {
		add_filter( 'wpml_ids_of_media_used_in_post', array( $this, 'add_product_gallery_images' ), 10, 2 );
	}

	/**
	 * @param array $media_ids
	 *
	 * @return mixed
	 */
	public function add_product_gallery_images( $media_ids, $post_id ) {
		$product_gallery = get_post_meta( $post_id, '_product_image_gallery', true );
		if ( $product_gallery ) {
			$ids = array_map(
				'intval',
				array_map(
					'trim',
					explode( ',', $product_gallery )
				)
			);

			$media_ids = array_values( array_unique( array_merge( $media_ids, $ids ) ) );
		}

		return array_filter( $media_ids );
	}
}
