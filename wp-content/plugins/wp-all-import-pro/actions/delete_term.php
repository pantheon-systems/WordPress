<?php 

function pmxi_delete_term($term, $tt_id, $taxonomy, $deleted_term, $object_ids) {
	if (is_numeric($term)){
		$post = new PMXI_Post_Record();
        $post->getBy(array(
            'post_id' => $term,
            'product_key' => 'taxonomy_term',
        ));
        $post->isEmpty() or $post->delete();
	}
}